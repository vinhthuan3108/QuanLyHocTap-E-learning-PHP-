<?php
include("../layout.php");
$course_id = $_SESSION['course_id'];

if (!isset($_GET['exam_id'])) {
    header("Location: exam.php");
    exit();
}
$exam_id = intval($_GET['exam_id']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = mysqli_real_escape_string($dbconnect, $_POST['title']);
    $description = mysqli_real_escape_string($dbconnect, $_POST['description']);
    $column_id = $_POST['column_id'];
    $open_time = $_POST['open_time'];
    $close_time = $_POST['close_time'];
    $time_limit = $_POST['time_limit'];
    $max_score = $_POST['max_score'];
    
    $sql_update = "UPDATE exam SET 
                   title = '$title',
                   description = '$description',
                   column_id = $column_id,
                   open_time = '$open_time',
                   close_time = '$close_time',
                   time_limit = $time_limit,
                   max_score = $max_score
                   WHERE exam_id = $exam_id AND course_id = $course_id";
    
    if (mysqli_query($dbconnect, $sql_update)) {
        $success_msg = "Cập nhật thông tin thành công!";
    } else {
        $error_msg = "Lỗi: " . mysqli_error($dbconnect);
    }
}

$sql_exam = "SELECT * FROM exam WHERE exam_id = $exam_id AND course_id = $course_id";
$result_exam = mysqli_query($dbconnect, $sql_exam);
if (mysqli_num_rows($result_exam) == 0) {
    die("Không tìm thấy bài kiểm tra hoặc bạn không có quyền sửa.");
}
$exam = mysqli_fetch_assoc($result_exam);


$sql_grade_columns = "SELECT * FROM grade_column WHERE course_id = $course_id";
$result_grade_columns = mysqli_query($dbconnect, $sql_grade_columns);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chỉnh sửa bài thi: <?php echo $exam['title']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header class="container mt-4 mb-3">
        <h3>Sửa bài kiểm tra: <?php echo $exam['title']; ?></h3>
        <ul class="nav nav-tabs mt-3">
            <li class="nav-item">
                <a class="nav-link active fw-bold" href="#">1. Thông tin chung</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="edit_exam_questions.php?exam_id=<?php echo $exam_id; ?>">2. Soạn câu hỏi</a>
            </li>
        </ul>
    </header>

    <div class="container">
        <?php if(isset($success_msg)) echo "<div class='alert alert-success'>$success_msg</div>"; ?>
        <?php if(isset($error_msg)) echo "<div class='alert alert-danger'>$error_msg</div>"; ?>

        <form method="POST" action="">
            <div class="row">
                <div class="col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-primary">Thông tin cơ bản</h5>
                            
                            <div class="mb-3">
                                <label class="form-label">Tiêu đề bài kiểm tra</label>
                                <input type="text" class="form-control" name="title" value="<?php echo $exam['title']; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Mô tả / Hướng dẫn</label>
                                <textarea class="form-control" name="description" rows="3"><?php echo $exam['description']; ?></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Thuộc cột điểm</label>
                                    <select class="form-select" name="column_id" required>
                                        <?php while ($col = mysqli_fetch_array($result_grade_columns)): ?>
                                            <option value="<?php echo $col['column_id']; ?>" 
                                                <?php if($col['column_id'] == $exam['column_id']) echo 'selected'; ?>>
                                                <?php echo $col['grade_column_name'] . ' (' . $col['proportion'] . '%)'; ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Điểm tối đa (Thang điểm)</label>
                                    <input type="number" class="form-control" name="max_score" step="0.01" value="<?php echo $exam['max_score']; ?>" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-primary">Thời gian & Cài đặt</h5>
                            
                            <div class="mb-3">
                                <label class="form-label">Thời gian mở</label>
                                <input type="datetime-local" class="form-control" name="open_time" 
                                       value="<?php echo date('Y-m-d\TH:i', strtotime($exam['open_time'])); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Thời gian đóng</label>
                                <input type="datetime-local" class="form-control" name="close_time" 
                                       value="<?php echo date('Y-m-d\TH:i', strtotime($exam['close_time'])); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Thời lượng làm bài (phút)</label>
                                <input type="number" class="form-control" name="time_limit" value="<?php echo $exam['time_limit']; ?>" placeholder="Để trống nếu không giới hạn">
                            </div>

                            <hr>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                                <a href="exam.php" class="btn btn-secondary">Quay lại danh sách</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <?php include("../../../footer.php"); ?>
</body>
</html>