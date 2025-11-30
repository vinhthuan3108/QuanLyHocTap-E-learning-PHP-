<?php
include("../layout.php");
$course_id = $_SESSION['course_id'];

// Lấy danh sách cột điểm của khóa học
$sql_grade_columns = "SELECT * FROM grade_column WHERE course_id = $course_id";
$result_grade_columns = mysqli_query($dbconnect, $sql_grade_columns);

// Xử lý khi form được submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy dữ liệu từ form
    $title = mysqli_real_escape_string($dbconnect, $_POST['title']);
    $description = mysqli_real_escape_string($dbconnect, $_POST['description']);
    $column_id = $_POST['column_id'];
    $open_time = $_POST['open_time'];
    $close_time = $_POST['close_time'];
    $time_limit = $_POST['time_limit'];
    $max_score = $_POST['max_score'];
    
    // Thêm bài kiểm tra vào database
    $sql_insert_exam = "INSERT INTO exam (course_id, column_id, title, description, open_time, close_time, time_limit, max_score) 
                        VALUES ($course_id, $column_id, '$title', '$description', '$open_time', '$close_time', $time_limit, $max_score)";
    
    if (mysqli_query($dbconnect, $sql_insert_exam)) {
        $exam_id = mysqli_insert_id($dbconnect);
        $_SESSION['success_message'] = "Tạo bài kiểm tra thành công!";
        header("Location: edit_exam_questions.php?exam_id=$exam_id");
        exit();
    } else {
        $error_message = "Lỗi khi tạo bài kiểm tra: " . mysqli_error($dbconnect);
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo bài kiểm tra mới</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .question-card {
            border-left: 4px solid #0d6efd;
            margin-bottom: 1rem;
        }
        .answer-item {
            padding: 0.5rem;
            margin-bottom: 0.5rem;
            background-color: #f8f9fa;
            border-radius: 0.25rem;
        }
    </style>
</head>

<body>
    <header class="container mt-4">
        <div class="row">
            <div class="col-md-6">
                <h3>Tạo bài kiểm tra mới</h3>
            </div>
            <div class="col-md-6">
                <a class="btn btn-secondary float-end me-2" href="exam.php">Quay lại</a>
            </div>
        </div>
    </header>

    <div class="container mt-4">
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Thông tin bài kiểm tra</h5>
                            
                            <div class="mb-3">
                                <label for="title" class="form-label">Tiêu đề bài kiểm tra</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Mô tả</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="column_id" class="form-label">Loại điểm</label>
                                    <select class="form-select" id="column_id" name="column_id" required>
                                        <option value="">-- Chọn loại điểm --</option>
                                        <?php while ($column = mysqli_fetch_array($result_grade_columns)): ?>
                                            <option value="<?php echo $column['column_id']; ?>">
                                                <?php echo $column['grade_column_name'] . ' (' . $column['proportion'] . '%)'; ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="max_score" class="form-label">Điểm tối đa</label>
                                    <input type="number" class="form-control" id="max_score" name="max_score" step="0.01" min="0" value="10" required>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <label for="open_time" class="form-label">Thời gian mở</label>
                                    <input type="datetime-local" class="form-control" id="open_time" name="open_time" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="close_time" class="form-label">Thời gian đóng</label>
                                    <input type="datetime-local" class="form-control" id="close_time" name="close_time" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="time_limit" class="form-label">Thời lượng (phút)</label>
                                    <input type="number" class="form-control" id="time_limit" name="time_limit" min="1" placeholder="Ví dụ: 60">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Tùy chọn</h5>
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="show_results" name="show_results" checked>
                                    <label class="form-check-label" for="show_results">Hiển thị kết quả sau khi làm bài</label>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Tạo bài kiểm tra</button>
                                <a href="exam.php" class="btn btn-secondary">Hủy</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Đặt thời gian mở mặc định là hiện tại
        document.getElementById('open_time').value = new Date().toISOString().slice(0, 16);
        
        // Đặt thời gian đóng mặc định là 7 ngày sau
        const closeTime = new Date();
        closeTime.setDate(closeTime.getDate() + 7);
        document.getElementById('close_time').value = closeTime.toISOString().slice(0, 16);
    </script>
    
    <?php include("../../../footer.php"); ?>
</body>
</html>