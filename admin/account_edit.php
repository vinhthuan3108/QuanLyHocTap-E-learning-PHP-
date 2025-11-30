<?php
include("layout.php");
include_once("../config/connect.php");
if (session_status() == PHP_SESSION_NONE) session_start();

$role_id = $_GET['role_id'];
$role_name = $_GET['role_name'];

switch($role_id){
    case 1: $role_fullName = "Học Sinh"; break;
    case 2: $role_fullName = "Giáo Viên"; break;
    case 3: $role_fullName = "Quản Trị Viên"; break;
    default: $role_fullName = "Người dùng"; break;
}

// Lấy thông tin user hiện tại
$id = $_GET['user_id'];
$sql_edit = "SELECT us.*, ua.username FROM user us
             INNER JOIN user_account ua ON us.user_id = ua.user_id
             WHERE us.user_id=$id";
$query_update = mysqli_query($dbconnect, $sql_edit);
$row_update = mysqli_fetch_assoc($query_update);

mysqli_close($dbconnect);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sửa tài khoản - <?php echo $role_fullName; ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<style>
body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
.form-card { background-color: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-top: 5px; }
.form-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
.form-header h2 { font-weight: 700; color: #343a40; }
.btn-upload { position: relative; overflow: hidden; }
.btn-upload input[type=file] { position: absolute; opacity: 0; right: 0; top: 0; cursor: pointer; }
.form-label { font-weight: 500; }
.btn-primary, .btn-secondary { min-width: 130px; }
</style>
</head>
<body>
<?php include("sidebar.php"); ?>

<div class="main" id="mainContent">
<div class="container">
    <div class="form-card mx-auto">
        <!-- Header -->
        <div class="form-header">
            <h2>Sửa thông tin (<?php echo $role_fullName; ?>)</h2>
            <div>
                <button class="btn btn-outline-primary btn-upload me-2">
                    <i class="bi bi-file-earmark-arrow-up"></i> Tải lên Excel
                    <input type="file" name="excel_file" accept=".xlsx,.xls">
                </button>
                <a href="<?php echo $role_name;?>.php" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle"></i> Thoát
                </a>
            </div>
        </div>

        <?php
        if(isset($_SESSION['error'])){
            echo '<div class="alert alert-danger">'.$_SESSION['error'].'</div>';
            unset($_SESSION['error']);
        }
        ?>

        <!-- Form -->
        <form action="process.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate id="accountForm">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="fullName" class="form-label">Họ và tên</label>
                    <input type="text" class="form-control" id="fullName" name="full_name" required placeholder="Nhập họ tên" value="<?php echo $row_update['full_name']; ?>">
                    <div class="invalid-feedback">Họ và tên không được trống.</div>
                </div>
                <div class="col-md-6">
                    <label for="username" class="form-label">Tên tài khoản</label>
                    <input type="text" class="form-control" id="username" name="username" required placeholder="Nhập tên tài khoản" value="<?php echo $row_update['username']; ?>">
                    <div class="invalid-feedback">Tên tài khoản không được trống.</div>
                    <div id="usernameFeedback" class="form-text"></div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="idCard" class="form-label">Mã số CCCD</label>
                    <input type="text" class="form-control" id="idCard" name="citizen_id" required placeholder="Nhập CCCD" value="<?php echo $row_update['citizen_id']; ?>">
                    <div class="invalid-feedback">Mã số căn cước không được trống.</div>
                </div>
                <div class="col-md-6">
                    <label for="newPassword" class="form-label">Mật khẩu mới (để trống nếu không đổi)</label>
                    <input type="password" class="form-control" id="newPassword" name="new_password" placeholder="Nhập mật khẩu mới">
                    <div class="form-text">Chỉ nhập nếu muốn thay đổi mật khẩu</div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="dob" class="form-label">Ngày sinh</label>
                    <input type="date" class="form-control" id="dob" name="date_of_birth" required value="<?php echo $row_update['date_of_birth'] ?: date('Y-m-d'); ?>">
                    <div class="invalid-feedback">Vui lòng chọn ngày sinh.</div>
                </div>
                <div class="col-md-4">
                    <label for="gender" class="form-label">Giới tính</label>
                    <select class="form-select" id="gender" name="gender" required>
                        <option value="" disabled>Chọn giới tính</option>
                        <option value="M" <?php echo ($row_update['gender'] == 'M') ? 'selected' : ''; ?>>Nam</option>
                        <option value="F" <?php echo ($row_update['gender'] == 'F') ? 'selected' : ''; ?>>Nữ</option>
                    </select>
                    <div class="invalid-feedback">Vui lòng chọn giới tính.</div>
                </div>
                <div class="col-md-4">
                    <label for="phoneNumber" class="form-label">Số điện thoại</label>
                    <input type="tel" class="form-control" id="phoneNumber" name="phone" required placeholder="Nhập SĐT" value="<?php echo $row_update['phone']; ?>">
                    <div class="invalid-feedback">Số điện thoại không được trống.</div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required placeholder="Nhập email" value="<?php echo $row_update['email']; ?>">
                    <div class="invalid-feedback" id="emailFeedback">Vui lòng nhập email hợp lệ.</div>
                </div>
                <div class="col-md-6">
                    <label for="address" class="form-label">Địa chỉ</label>
                    <input type="text" class="form-control" id="address" name="address" required placeholder="Nhập địa chỉ" value="<?php echo $row_update['address']; ?>">
                    <div class="invalid-feedback">Địa chỉ không được trống.</div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="portrait" class="form-label">Ảnh chân dung</label><br>
                    <img src="/assets/images/<?php echo $row_update['image']; ?>" width="60px" class="mb-2"><br>
                    <input type="file" class="form-control" id="portrait" name="image">
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary me-2" name="sbm_edit">
                    <i class="bi bi-check-circle"></i> Lưu
                </button>
                <a class="btn btn-secondary" href="<?php echo $role_name;?>.php">
                    <i class="bi bi-x-circle"></i> Thoát
                </a>
            </div>
        </form>
    </div>
        <?php include("../footer.php"); ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function() {
    'use strict';
    var form = document.getElementById('accountForm');
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    }, false);
})();

// Kiểm tra email hợp lệ
document.getElementById('accountForm').addEventListener('submit', function(e){
    var email = document.getElementById('email');
    var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if(!emailPattern.test(email.value)){
        e.preventDefault();
        e.stopPropagation();
        document.getElementById('emailFeedback').innerHTML = 'Email không hợp lệ';
        email.classList.add('is-invalid');
        return;
    } else {
        email.classList.remove('is-invalid');
    }
});

// Kiểm tra username tồn tại
document.getElementById('username').addEventListener('blur', function() {
    var username = this.value;
    var currentUsername = '<?php echo $row_update['username']; ?>';
    if (username === currentUsername) {
        document.getElementById('usernameFeedback').innerHTML = 'Tên tài khoản hiện tại';
        document.getElementById('usernameFeedback').className = 'form-text text-success';
        return;
    }
    if (username.length < 3) {
        document.getElementById('usernameFeedback').innerHTML = 'Tên tài khoản phải có ít nhất 3 ký tự';
        document.getElementById('usernameFeedback').className = 'form-text text-danger';
        return;
    }
    fetch('check_username.php?username=' + encodeURIComponent(username) + '&current=' + encodeURIComponent(currentUsername))
        .then(response => response.json())
        .then(data => {
            const feedback = document.getElementById('usernameFeedback');
            if(data.available){
                feedback.innerHTML = 'Tên tài khoản có thể sử dụng';
                feedback.className = 'form-text text-success';
            } else {
                feedback.innerHTML = 'Tên tài khoản đã tồn tại, vui lòng chọn tên khác';
                feedback.className = 'form-text text-danger';
            }
        });
});

// Kiểm tra kích thước ảnh upload
document.getElementById('accountForm').addEventListener('submit', function(e) {
    var fileInput = document.getElementById('portrait');
    if(fileInput.files.length > 0){
        var fileSize = fileInput.files[0].size; // size tính bằng byte
        if(fileSize > 5 * 1024 * 1024){ // 5MB
            e.preventDefault();
            e.stopPropagation();
            fileInput.classList.add('is-invalid');
            alert('Ảnh phải nhỏ hơn hoặc bằng 5MB.');
        } else {
            fileInput.classList.remove('is-invalid');
        }
    }
});
</script>
</body>
</html>
