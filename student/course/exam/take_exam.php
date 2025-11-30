<?php

include("../layout.php");

if(!isset($_GET['exam_id'])) { header("Location: index.php"); exit(); }
$exam_id = intval($_GET['exam_id']);
$course_id = $_SESSION['course_id'];

$sql_exam = "SELECT * FROM exam WHERE exam_id = $exam_id AND course_id = $course_id";
$exam = mysqli_fetch_assoc(mysqli_query($dbconnect, $sql_exam));

$now = time();
if($now < strtotime($exam['open_time']) || $now > strtotime($exam['close_time'])) {
    die("<div class='container mt-5 alert alert-danger'>Bài thi hiện không khả dụng!</div>");
}

$sql_questions = "SELECT * FROM question WHERE exam_id = $exam_id ORDER BY order_num ASC, question_id ASC";
$res_questions = mysqli_query($dbconnect, $sql_questions);
?>

<div class="container mt-4 mb-5">
    <div class="row">
        <div class="col-md-9">
            <div class="card mb-3 border-primary">
                <div class="card-body">
                    <h2><?php echo $exam['title']; ?></h2>
                    <div class="alert alert-warning">
                        <i class="bi bi-clock"></i> Thời gian làm bài: <b><?php echo $exam['time_limit']; ?> phút</b>. 
                        Hệ thống sẽ tự động nộp bài khi hết giờ.
                    </div>
                </div>
            </div>

            <form action="submit.php" method="POST" id="examForm">
                <input type="hidden" name="exam_id" value="<?php echo $exam_id; ?>">
                
                <?php 
                $q_count = 1;
                while($q = mysqli_fetch_assoc($res_questions)): 
                ?>
                    <div class="card mb-4 shadow-sm" id="question_<?php echo $q['question_id']; ?>">
                        <div class="card-header">
                            <strong>Câu <?php echo $q_count; ?>:</strong> (<?php echo $q['points']; ?> điểm)
                        </div>
                        <div class="card-body">
                            <p class="card-text fs-5"><?php echo nl2br($q['question_text']); ?></p>
                            
                            <?php if($q['question_type'] == 'essay'): ?>
                                <textarea class="form-control" name="answers[<?php echo $q['question_id']; ?>]" rows="4" placeholder="Nhập câu trả lời của bạn..."></textarea>
                            
                            <?php else: // Trắc nghiệm 
                                $qid = $q['question_id'];

                                $sql_ans = "SELECT answer_id, answer_text FROM answer WHERE question_id = $qid";
                                $res_ans = mysqli_query($dbconnect, $sql_ans);
                                
                                while($ans = mysqli_fetch_assoc($res_ans)):
                            ?>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" 
                                           name="answers[<?php echo $q['question_id']; ?>]" 
                                           value="<?php echo $ans['answer_id']; ?>" 
                                           id="ans_<?php echo $ans['answer_id']; ?>">
                                    <label class="form-check-label" for="ans_<?php echo $ans['answer_id']; ?>">
                                        <?php echo $ans['answer_text']; ?>
                                    </label>
                                </div>
                            <?php endwhile; endif; ?>
                        </div>
                    </div>
                <?php $q_count++; endwhile; ?>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-success btn-lg px-5" onclick="return confirm('Bạn có chắc chắn muốn nộp bài?');">
                        NỘP BÀI
                    </button>
                </div>
            </form>
        </div>

        <div class="col-md-3">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-body text-center">
                    <h5>Thời gian còn lại</h5>
                    <h2 id="timer" class="text-danger fw-bold">--:--</h2>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    var timeLimit = <?php echo $exam['time_limit']; ?>; 
    var timeInSeconds = timeLimit * 60;
    
    var timerElement = document.getElementById('timer');
    
    var countdown = setInterval(function() {
        var minutes = Math.floor(timeInSeconds / 60);
        var seconds = timeInSeconds % 60;
        
        // Thêm số 0 đằng trước nếu < 10
        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;
        
        timerElement.innerHTML = minutes + ":" + seconds;
        
        if (timeInSeconds <= 0) {
            clearInterval(countdown);
            alert("Hết giờ làm bài!");
            document.getElementById('examForm').submit(); // Tự động nộp
        } else {
            timeInSeconds--;
        }
    }, 1000);
</script>

<?php include("../../../footer.php"); ?>