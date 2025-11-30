<?php
include("../layout.php"); 

$course_id = $_SESSION['course_id'];
$user_id = $_SESSION['user_id']; 


$sql_member = "SELECT member_id FROM course_member WHERE student_id = '$user_id' AND course_id = '$course_id' LIMIT 1";
$res_member = mysqli_query($dbconnect, $sql_member);

if(!$res_member || mysqli_num_rows($res_member) == 0) {
    die("Lỗi: Không tìm thấy thông tin thành viên lớp học.");
}
$row_member = mysqli_fetch_assoc($res_member);
$member_id = $row_member['member_id'];
?>

<div class="container mt-4">
    <h3 class="mb-4">Danh sách bài kiểm tra</h3>
    
    <div class="row">
        <?php

        $sql = "SELECT e.*, gc.grade_column_name, 
                       es.submit_time, es.total_score 
                FROM exam e 
                JOIN grade_column gc ON e.column_id = gc.column_id
                LEFT JOIN exam_submission es ON e.exam_id = es.exam_id AND es.member_id = $member_id
                WHERE e.course_id = $course_id 
                ORDER BY e.open_time DESC";
                
        $result = mysqli_query($dbconnect, $sql);

        if(mysqli_num_rows($result) > 0){
            while($row = mysqli_fetch_assoc($result)){
                $exam_id = $row['exam_id'];
                $now = time();
                $open = strtotime($row['open_time']);
                $close = strtotime($row['close_time']);
                
                
                $is_submitted = !empty($row['submit_time']); 
                
                
                $btn_status = '';
                $btn_text = 'Vào thi ngay';
                $btn_link = "take_exam.php?exam_id=$exam_id";
                $card_class = 'border-primary';
                $badge_status = ''; 

                
                
                if ($is_submitted) {
                    
                    $btn_status = 'disabled'; 
                   
                    $score = floatval($row['total_score']); 
                    $btn_text = "Đã nộp bài - Điểm: " . $score;
                    $card_class = 'border-success'; 
                    $badge_status = '<span class="badge bg-success float-end ms-2">Đã hoàn thành</span>';
                    
                } elseif ($now < $open) {
                    $btn_status = 'disabled';
                    $btn_text = 'Chưa mở';
                    $card_class = 'border-secondary';
                    $badge_status = '<span class="badge bg-secondary float-end ms-2">Sắp diễn ra</span>';
                    
                } elseif ($now > $close) {
                    $btn_status = 'disabled';
                    $btn_text = 'Đã đóng';
                    $card_class = 'border-danger'; 
                    $badge_status = '<span class="badge bg-danger float-end ms-2">Đã kết thúc</span>';
                }
                

                echo "
                <div class='col-md-6 mb-4'>
                    <div class='card h-100 $card_class shadow-sm'>
                        <div class='card-header bg-transparent d-flex justify-content-between align-items-center'>
                            <div>
                                <span class='badge bg-info text-dark'>{$row['grade_column_name']}</span>
                                $badge_status
                            </div>
                            <small class='text-muted'><i class='bi bi-clock'></i> {$row['time_limit']} phút</small>
                        </div>
                        <div class='card-body'>
                            <h5 class='card-title text-truncate'>{$row['title']}</h5>
                            <p class='card-text text-muted small' style='min-height: 40px;'>".nl2br($row['description'])."</p>
                            
                            <div class='bg-light p-2 rounded mb-3 small'>
                                <div><strong>Mở:</strong> ".date('d/m/Y H:i', $open)."</div>
                                <div><strong>Đóng:</strong> ".date('d/m/Y H:i', $close)."</div>
                            </div>
                            
                            <div class='d-grid'>
                                <a href='$btn_link' class='btn ". ($is_submitted ? 'btn-success' : 'btn-primary') ." $btn_status'>
                                    $btn_text
                                </a>
                            </div>
                        </div>
                    </div>
                </div>";
            }
        } else {
            echo "<div class='col-12'><div class='alert alert-warning text-center'>Chưa có bài kiểm tra nào trong khóa học này.</div></div>";
        }
        ?>
    </div>
</div>

<?php include("../../../footer.php"); ?>