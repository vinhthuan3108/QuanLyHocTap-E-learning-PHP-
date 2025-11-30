<?php
include("layout.php");
include_once("../config/connect.php");
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["sbm"])) {
    $_SESSION['course_name'] = $_POST['course_name'];
    $_SESSION['course_code'] = $_POST['course_code'];
    $_SESSION['course_description'] = $_POST['course_description'];
    $_SESSION['start_date'] = $_POST['start_date'];
    $_SESSION['end_date'] = $_POST['end_date'];
    $_SESSION['course_image'] = $_FILES['course_image']['name'];

    $image = $_SESSION['course_image'];
    $image_tmp = $_FILES['course_image']['tmp_name'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    if (strtotime($end_date) < strtotime($start_date)) {
        echo "<script>alert('Ngày kết thúc phải sau ngày bắt đầu'); history.back();</script>";
        exit;
    }

    if (move_uploaded_file($image_tmp, '../assets/file/course_background/' . $image)) {
        // Upload thành công
    } else {
        echo 'Lỗi khi upload: ' . error_get_last()['message'];
        exit;
    }
    header("location: choose_teacher.php?role=add");
}
mysqli_close($dbconnect);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tạo khóa học mới</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background-color: #f8f9fa; }
    .card { border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
    .form-label { font-weight: 500; }
    .preview-img { max-height: 200px; object-fit: cover; border-radius: 8px; margin-top: 10px; }
</style>
</head>

<body>
    <?php include "sidebar.php"; ?>

    <div class="main " id="mainContent">

<div class="container mt-3">
    <div class="d-flex align-items-center mb-4">
        <h3><a href="courses.php" class="text-decoration-none"><i class="bi bi-arrow-left-circle me-2"></i></a> Tạo khóa học mới</h3>
    </div>

    <div class="card p-4">
        <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate id="courseForm">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="course_name" class="form-label">Tên khóa học</label>
                    <input type="text" class="form-control" id="course_name" name="course_name" maxlength="225" required
                        value="<?php echo isset($_SESSION['course_name']) ? $_SESSION['course_name'] : "" ?>">
                    <div class="invalid-feedback">Tên khóa học không được để trống (tối đa 225 ký tự).</div>
                </div>
                <div class="col-md-6">
                    <label for="course_code" class="form-label">Mã khóa học</label>
                    <input type="text" class="form-control" id="course_code" name="course_code" maxlength="6" required
                        value="<?php echo isset($_SESSION['course_code']) ? $_SESSION['course_code'] : "" ?>">
                    <div class="invalid-feedback">Mã khóa học không được trống (tối đa 6 ký tự).</div>
                </div>
                <div class="col-md-12">
                    <label for="course_description" class="form-label">Mô tả khóa học</label>
                    <textarea class="form-control" id="course_description" name="course_description" rows="4" required><?php echo isset($_SESSION['course_description']) ? $_SESSION['course_description'] : "" ?></textarea>
                    <div class="invalid-feedback">Mô tả không được để trống.</div>
                </div>
                <div class="col-md-6">
    <label for="start_date" class="form-label">Ngày bắt đầu</label>
    <input type="date" class="form-control" id="start_date" name="start_date" required
        value="<?php echo isset($_SESSION['start_date']) ? $_SESSION['start_date'] : "" ?>">
    <div class="invalid-feedback">Chọn ngày bắt đầu.</div>
</div>
<div class="col-md-6">
    <label for="end_date" class="form-label">Ngày kết thúc</label>
    <input type="date" class="form-control" id="end_date" name="end_date" required
        value="<?php echo isset($_SESSION['end_date']) ? $_SESSION['end_date'] : "" ?>">
    <div class="invalid-feedback" id="endDateFeedback">Chọn ngày kết thúc (phải sau ngày bắt đầu).</div>
</div>

                <div class="col-md-12">
                    <label for="course_image" class="form-label">Ảnh bìa khóa học</label>
                    <input type="file" class="form-control" id="course_image" name="course_image" accept="image/*" required>
                    <img id="preview" class="preview-img" src="#" alt="Preview" style="display:none;">
                    <div class="invalid-feedback">Chọn ảnh bìa (tối đa 20MB).</div>
                </div>
            </div>
            <div class="d-flex justify-content-end mt-4">
                <a href="courses.php" class="btn btn-secondary me-2">Hủy</a>
                <button type="submit" class="btn btn-primary" name="sbm">Tạo khóa học</button>
            </div>
        </form>
    </div>
</div>
    <?php include("../footer.php"); ?>

</div>
<script>
    // Bootstrap validation
    (function() {
        'use strict'
        var form = document.getElementById('courseForm');
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    })();

    // Preview image trước khi upload
    const courseImage = document.getElementById('course_image');
    const preview = document.getElementById('preview');
    courseImage.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';
        } else {
            preview.style.display = 'none';
        }
    });
    // Bootstrap validation
(function() {
    'use strict';
    var form = document.getElementById('courseForm');

    form.addEventListener('submit', function(event) {
        var startDate = document.getElementById('start_date').value;
        var endDate = document.getElementById('end_date').value;
        var valid = true;

        // Kiểm tra ngày bắt đầu <= ngày kết thúc
        if (startDate && endDate && new Date(startDate) > new Date(endDate)) {
            valid = false;
            document.getElementById('end_date').classList.add('is-invalid');
        } else {
            document.getElementById('end_date').classList.remove('is-invalid');
        }

        if (!form.checkValidity() || !valid) {
            event.preventDefault();
            event.stopPropagation();
        }

        form.classList.add('was-validated');
    }, false);
})();


</script>

</body>
</html>
