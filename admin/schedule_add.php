<?php
include("layout.php");
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm thời khóa biểu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include "sidebar.php"; ?>

<div class="main p-4" id="mainContent">
    <header class="container mt-5">
        <h3>Thêm thời khóa biểu</h3>
        <p>Thêm thông tin thời khóa biểu dưới đây</p>
    </header>

    <div class="container mt-5">
        <?php
            if (isset($_SESSION['error_schedule'])): ?>
                <div id="alertError" class="alert alert-danger mt-3">
                    <?php
                        echo $_SESSION['error_schedule'];
                        unset($_SESSION['error_schedule']);
                    ?>
                </div>
            <?php endif; ?>


        <form id="scheduleForm" action="process.php" method="post" class="needs-validation" novalidate>
            <div id="additionalTimes" class="mb-3 row">
                <!-- Dynamic rows -->
            </div>

            <div class="mb-3 row">
                <div class="col-sm-12">
                    <button type="button" class="btn btn-primary" onclick="addTimeRow()">Thêm thời gian</button>
                    <button type="submit" class="btn btn-success" name="add_schedule">Lưu thời khóa biểu</button>
                    <a href="success_create_course.php" class="btn btn-secondary">Không thêm ngay bây giờ</a>
                </div>
            </div>
        </form>
    </div>
        <?php include("../footer.php"); ?>

</div>

<script>
(function () {
    'use strict';
    var forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
})();

function addTimeRow() {
    var container = document.getElementById('additionalTimes');
    var newRow = document.createElement('div');
    newRow.className = 'mb-3 row';

    newRow.innerHTML = `
        <label class="col-sm-2 col-form-label">Ngày trong tuần</label>
        <div class="col-sm-2">
            <select class="form-select" name="dayOfWeek[]" required>
                <option value="" disabled selected>Chọn ngày</option>
                <option value="monday">Thứ hai</option>
                <option value="tuesday">Thứ ba</option>
                <option value="wednesday">Thứ tư</option>
                <option value="thursday">Thứ năm</option>
                <option value="friday">Thứ sáu</option>
                <option value="saturday">Thứ bảy</option>
                <option value="sunday">Chủ nhật</option>
            </select>
        </div>
        <label class="col-sm-1 col-form-label">Bắt đầu</label>
        <div class="col-sm-2">
            <input type="time" class="form-control" name="startTime[]" required>
        </div>
        <label class="col-sm-1 col-form-label">Kết thúc</label>
        <div class="col-sm-2">
            <input type="time" class="form-control" name="endTime[]" required>
        </div>
        <div class="col-sm-2">
            <button type="button" class="btn btn-danger" onclick="removeTimeRow(this)">Xóa</button>
        </div>
    `;
    container.appendChild(newRow);
}

function removeTimeRow(button) {
    var row = button.closest('.row');
    row.remove();
}
</script>
<script>
    // Tự ẩn alert sau 3 giây
    var alertEl = document.getElementById('alertError');
    if (alertEl) {
        setTimeout(function() {
            alertEl.style.display = 'none';
        }, 3000); // 3000ms = 3 giây
    }
</script>

</body>
</html>
