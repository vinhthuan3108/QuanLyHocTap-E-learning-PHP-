<?php
include("../layout.php"); // Kết nối DB
$exam_id = $_GET['exam_id'];

// 1. Lấy thông tin bài kiểm tra
$sql_exam = "SELECT * FROM exam WHERE exam_id = $exam_id";
$exam = mysqli_fetch_assoc(mysqli_query($dbconnect, $sql_exam));

// 2. Xử lý thêm câu hỏi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_question'])) {
    $q_text = mysqli_real_escape_string($dbconnect, $_POST['question_text']);
    $q_type = $_POST['question_type'];
    $q_points = $_POST['points'];
    
    // Thêm câu hỏi vào bảng question
    $sql_q = "INSERT INTO question (exam_id, question_type, question_text, points) VALUES ($exam_id, '$q_type', '$q_text', $q_points)";
    
    if(mysqli_query($dbconnect, $sql_q)){
        $question_id = mysqli_insert_id($dbconnect);
        
        // --- XỬ LÝ TRẮC NGHIỆM ---
        if(in_array($q_type, ['multiple_choice_single', 'multiple_choice_multiple']) && isset($_POST['answers'])){
            foreach($_POST['answers'] as $key => $ans_text){
                $is_correct = isset($_POST['correct_answer'][$key]) ? 1 : 0;
                $ans_text = mysqli_real_escape_string($dbconnect, $ans_text);
                // Lưu đáp án
                if(!empty($ans_text)) {
                    mysqli_query($dbconnect, "INSERT INTO answer (question_id, answer_text, is_correct) VALUES ($question_id, '$ans_text', $is_correct)");
                }
            }
        }
        
        // --- XỬ LÝ TỰ LUẬN NGẮN ---
        if($q_type == 'essay' && !empty($_POST['essay_correct_answer'])){
            $essay_ans = mysqli_real_escape_string($dbconnect, $_POST['essay_correct_answer']);
            mysqli_query($dbconnect, "INSERT INTO answer (question_id, answer_text, is_correct) VALUES ($question_id, '$essay_ans', 1)");
        }

        // --- THAY ĐỔI Ở ĐÂY: Thay alert bằng chuyển hướng ---
        // Load lại chính trang này để xóa dữ liệu form cũ và cập nhật danh sách câu hỏi mới
        header("Location: edit_exam_questions.php?exam_id=$exam_id");
        exit(); 
    }
}

// 3. Lấy danh sách câu hỏi hiện có
$sql_questions = "SELECT * FROM question WHERE exam_id = $exam_id ORDER BY order_num ASC, question_id ASC";
$result_questions = mysqli_query($dbconnect, $sql_questions);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Soạn câu hỏi - <?php echo $exam['title']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <header class="container mt-4 mb-3">
    <h3>Soạn đề: <?php echo $exam['title']; ?></h3>
    <ul class="nav nav-tabs mt-3">
        <li class="nav-item">
            <a class="nav-link" href="edit_exam_info.php?exam_id=<?php echo $exam_id; ?>">1. Thông tin chung</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active fw-bold" href="#">2. Soạn câu hỏi</a>
        </li>
    </ul>
</header>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Soạn đề: <?php echo $exam['title']; ?></h3>
        <a href="exam.php" class="btn btn-secondary">Hoàn tất</a>
    </div>

    <div class="row">
        <div class="col-md-5">
            <div class="card sticky-top" style="top: 20px; z-index: 1;">
                <div class="card-header bg-primary text-white">Thêm câu hỏi mới</div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="add_question" value="1">
                        
                        <div class="mb-3">
                            <label>Nội dung câu hỏi:</label>
                            <textarea name="question_text" class="form-control" rows="3" required></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label>Loại câu hỏi:</label>
                                <select name="question_type" id="q_type" class="form-select" onchange="toggleAnswers()">
                                    <option value="multiple_choice_single">Trắc nghiệm</option>
                                    <option value="essay">Tự luận ngắn</option>
                                </select>
                            </div>
                            <div class="col-6 mb-3">
                                <label>Điểm:</label>
                                <input type="number" name="points" class="form-control" value="1" step="0.25">
                            </div>
                        </div>

                        <div id="multiple_choice_area">
                            <label>Các đáp án (Tích chọn đáp án đúng):</label>
                            <div id="answer_list">
                                <?php for($i=0; $i<4; $i++): ?>
                                <div class="input-group mb-2">
                                    <div class="input-group-text">
                                        <input class="form-check-input mt-0" type="radio" name="correct_answer[<?php echo $i; ?>]" value="1">
                                    </div>
                                    <input type="text" name="answers[<?php echo $i; ?>]" class="form-control" placeholder="Đáp án <?php echo $i+1; ?>">
                                </div>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <div id="essay_area" style="display: none;">
                            <div class="mb-3">
                                <input type="text" name="essay_correct_answer" id="essay_input" class="form-control" placeholder="Ví dụ: Hà Nội">
                                <small class="text-muted">Nhập không phân biệt hoa thường.</small>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100 mt-3">Lưu câu hỏi</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <h4>Danh sách câu hỏi</h4>
            <?php 
            $count = 1;
            while($q = mysqli_fetch_assoc($result_questions)): 
            ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Câu <?php echo $count++; ?> (<?php echo $q['points']; ?> điểm)
                            <span class="badge bg-secondary float-end"><?php echo ($q['question_type']=='essay') ? 'Điền từ' : 'Trắc nghiệm'; ?></span>
                        </h5>
                        <p class="card-text"><?php echo nl2br($q['question_text']); ?></p>
                        
                        <?php 
                            $qid = $q['question_id'];
                            $ans_sql = "SELECT * FROM answer WHERE question_id = $qid";
                            $ans_res = mysqli_query($dbconnect, $ans_sql);
                        ?>
                        
                        <?php if($q['question_type'] == 'essay'): 
                             $essay_ans = mysqli_fetch_assoc($ans_res);
                        ?>
                            <div class="alert alert-info py-2">
                                <strong>Đáp án đúng:</strong> <?php echo isset($essay_ans['answer_text']) ? $essay_ans['answer_text'] : '(Chưa có)'; ?>
                            </div>

                        <?php else: ?>
                            <ul class="list-group list-group-flush">
                                <?php while($ans = mysqli_fetch_assoc($ans_res)): ?>
                                    <li class="list-group-item <?php echo ($ans['is_correct']) ? 'list-group-item-success' : ''; ?>">
                                        <?php echo $ans['answer_text']; ?>
                                        <?php if($ans['is_correct']) echo '<i class="bi bi-check-circle-fill float-end"></i>'; ?>
                                    </li>
                                <?php endwhile; ?>
                            </ul>
                        <?php endif; ?>
                        
                        <div class="mt-2 text-end">
                            <a href="#" class="btn btn-sm btn-outline-danger">Xóa</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<script>
    function toggleAnswers() {
        var type = document.getElementById('q_type').value;
        var mc_area = document.getElementById('multiple_choice_area');
        var essay_area = document.getElementById('essay_area');
        var essay_input = document.getElementById('essay_input');
        
        var mc_inputs = mc_area.querySelectorAll('input[type="text"]');

        if (type === 'essay') {
            mc_area.style.display = 'none';
            essay_area.style.display = 'block';
            mc_inputs.forEach(input => input.required = false);
            essay_input.required = true;
        } else {
            mc_area.style.display = 'block';
            essay_area.style.display = 'none';
            mc_inputs.forEach(input => input.required = true);
            essay_input.required = false;
        }
    }
</script>
</body>
</html>