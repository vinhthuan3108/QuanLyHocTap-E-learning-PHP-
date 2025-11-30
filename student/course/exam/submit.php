<?php
include("../layout.php");

// 1. Kiểm tra Session và Đăng nhập
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra user_id (thay vì member_id như cũ)
if (!isset($_SESSION['user_id'])) {
    die("Lỗi: Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại!");
}

// Lấy thông tin từ Session
$student_id_from_session = $_SESSION['user_id']; // Đây là user_id (ví dụ: 1)
$course_id = $_SESSION['course_id'];             // Ví dụ: 9

// --- BƯỚC QUAN TRỌNG: Lấy member_id ---
// Vì bảng exam_submission yêu cầu member_id (liên kết khóa học), ta phải tìm nó
$sql_get_member = "SELECT member_id FROM course_member 
                   WHERE student_id = '$student_id_from_session' 
                   AND course_id = '$course_id' LIMIT 1";

$query_member = mysqli_query($dbconnect, $sql_get_member);

if (!$query_member || mysqli_num_rows($query_member) == 0) {
    // Trường hợp user đã đăng nhập nhưng chưa được add vào lớp này
    die("Lỗi: Bạn không phải là thành viên của lớp học này (Không tìm thấy Member ID).");
}

$row_member = mysqli_fetch_assoc($query_member);
$member_id = $row_member['member_id']; // Đây mới là ID dùng để lưu vào bảng điểm
// --------------------------------------

// 2. Kiểm tra dữ liệu POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['exam_id'])) {
    header("Location: index.php");
    exit();
}

$exam_id = intval($_POST['exam_id']);
$answers = isset($_POST['answers']) ? $_POST['answers'] : []; 

// Lấy thông tin bài thi
$sql_exam_info = "SELECT * FROM exam WHERE exam_id = $exam_id";
$exam_query = mysqli_query($dbconnect, $sql_exam_info);
$exam_info = mysqli_fetch_assoc($exam_query);
$column_id = $exam_info['column_id'];

// 3. Tạo lượt nộp bài 
$submit_time = date('Y-m-d H:i:s');

// Câu lệnh SQL bây giờ đã có biến $member_id chính xác
$sql_create_sub = "INSERT INTO exam_submission (exam_id, member_id, start_time, submit_time, total_score) 
                   VALUES ($exam_id, $member_id, '$submit_time', '$submit_time', 0)";

if (!mysqli_query($dbconnect, $sql_create_sub)) {
    die("Lỗi SQL (Tạo bài nộp): " . mysqli_error($dbconnect)); 
}
$submission_id = mysqli_insert_id($dbconnect);

// 3. Bắt đầu chấm điểm từng câu
$total_score_earned = 0;

// Lấy danh sách toàn bộ câu hỏi và đáp án đúng của đề thi này để đối chiếu
$sql_questions = "SELECT * FROM question WHERE exam_id = $exam_id";
$res_questions = mysqli_query($dbconnect, $sql_questions);

while ($q = mysqli_fetch_assoc($res_questions)) {
    $qid = $q['question_id'];
    $points = $q['points'];
    $type = $q['question_type'];
    
    $user_selected_id = "NULL";
    $user_text_ans = ""; // Mặc định rỗng
    $is_correct = 0;
    $score_detail = 0;

    if (isset($answers[$qid])) {
        
        if ($type == 'essay') {
            // -- XỬ LÝ TỰ LUẬN NGẮN --
            // 1. Lấy dữ liệu user nhập
            $raw_user_ans = trim($answers[$qid]); 
            $user_text_ans = mysqli_real_escape_string($dbconnect, $raw_user_ans); // Để lưu vào DB an toàn
            
            // 2. Lấy đáp án đúng từ DB
            $sql_check = "SELECT answer_text FROM answer WHERE question_id = $qid LIMIT 1";
            $res_check = mysqli_query($dbconnect, $sql_check);
            
            if(mysqli_num_rows($res_check) > 0){
                $db_row = mysqli_fetch_assoc($res_check);
                $correct_text = $db_row['answer_text'];
                
                // 3. SO SÁNH (Logic quan trọng)
                // mb_strtolower: chuyển về chữ thường (hỗ trợ tiếng Việt có dấu)
                // trim: cắt khoảng trắng 2 đầu
                // Nếu muốn bỏ toàn bộ khoảng trắng ở giữa (vd: "Hà Nội" == "HàNội") thì dùng str_replace(' ', '', ...)
                
                // Cách 1: Chuẩn hóa lỏng (cắt 2 đầu, chữ thường) - Khuyên dùng
                $norm_user = mb_strtolower(trim($raw_user_ans), 'UTF-8');
                $norm_db   = mb_strtolower(trim($correct_text), 'UTF-8');
                
                if ($norm_user === $norm_db && $norm_user !== '') {
                    $is_correct = 1;
                    $score_detail = $points;
                }
            }
            
        } else {
            // -- XỬ LÝ TRẮC NGHIỆM (Code cũ) --
            $user_selected_id = intval($answers[$qid]);
            
            $sql_check = "SELECT * FROM answer WHERE question_id = $qid AND is_correct = 1 LIMIT 1";
            $res_check = mysqli_query($dbconnect, $sql_check);
            
            if (mysqli_num_rows($res_check) > 0) {
                $correct_ans = mysqli_fetch_assoc($res_check);
                if ($user_selected_id == $correct_ans['answer_id']) {
                    $is_correct = 1;
                    $score_detail = $points;
                }
            }
        }
    }

    // Cộng điểm tổng
    $total_score_earned += $score_detail;

    // Lưu vào DB (Lưu ý: user_text_ans cần bọc dấu nháy đơn nếu có dữ liệu)
    $val_text = ($user_text_ans !== "") ? "'$user_text_ans'" : "NULL";
    
    $sql_detail = "INSERT INTO exam_submission_detail 
                   (submission_id, question_id, selected_answer_id, text_answer, is_correct, points_earned)
                   VALUES ($submission_id, $qid, $user_selected_id, $val_text, $is_correct, $score_detail)";
    mysqli_query($dbconnect, $sql_detail);
}

// 4. Cập nhật điểm tổng của bài thi này vào bảng exam_submission
$sql_update_score = "UPDATE exam_submission SET total_score = $total_score_earned WHERE submission_id = $submission_id";
mysqli_query($dbconnect, $sql_update_score);


// ---------------------------------------------------------
// 5. TÍNH ĐIỂM TRUNG BÌNH CỘT (QUAN TRỌNG)
// Logic: Lấy tất cả bài thi thuộc cột điểm này (column_id) của user, tính AVG
// ---------------------------------------------------------

// Bước 5.1: Tính trung bình
$sql_avg = "SELECT AVG(es.total_score) as avg_score 
            FROM exam_submission es 
            JOIN exam e ON es.exam_id = e.exam_id 
            WHERE e.column_id = $column_id AND es.member_id = $member_id";
$res_avg = mysqli_query($dbconnect, $sql_avg);
$row_avg = mysqli_fetch_assoc($res_avg);
$final_grade = round($row_avg['avg_score'], 1); // Làm tròn 1 chữ số thập phân

// Bước 5.2: Cập nhật vào bảng grade
// Kiểm tra xem đã có dòng điểm nào trong bảng `grade` chưa
$sql_check_grade = "SELECT * FROM grade WHERE column_id = $column_id AND member_id = $member_id";
$res_check_grade = mysqli_query($dbconnect, $sql_check_grade);

if (mysqli_num_rows($res_check_grade) > 0) {
    // Nếu có rồi -> UPDATE
    $sql_grade_update = "UPDATE grade SET score = $final_grade WHERE column_id = $column_id AND member_id = $member_id";
    mysqli_query($dbconnect, $sql_grade_update);
} else {
    // Nếu chưa có -> INSERT
    $sql_grade_insert = "INSERT INTO grade (column_id, member_id, score) VALUES ($column_id, $member_id, $final_grade)";
    mysqli_query($dbconnect, $sql_grade_insert);
}

// ---------------------------------------------------------
// 6. Hoàn tất & Chuyển hướng
// ---------------------------------------------------------
echo "<script>
    alert('Nộp bài thành công! Điểm của bạn: $total_score_earned');
    window.location.href = 'index.php';
</script>";
?>