<?php
session_start();
include_once('layout.php');
include_once('./config/connect.php');

$error = '';
$success = '';

$email = isset($_GET['email']) ? $_GET['email'] : '';
if (empty($email)) {
    die('Email không hợp lệ!');
}

if (!isset($_SESSION['pending_username']) || !isset($_SESSION['pending_password'])) {
    die('Phiên đăng ký hết hạn. Vui lòng đăng ký lại.');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input_code = $_POST['verification_code'];

    $sql = "SELECT user_id, verification_code FROM user WHERE email = ? AND email_verified_at IS NULL";
    $stmt = mysqli_prepare($dbconnect, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    if ($row && $row['verification_code'] == $input_code) {
        $update_sql = "UPDATE user SET email_verified_at = NOW(), verification_code = NULL WHERE email = ?";
        $update_stmt = mysqli_prepare($dbconnect, $update_sql);
        mysqli_stmt_bind_param($update_stmt, "s", $email);
        mysqli_stmt_execute($update_stmt);

        $user_id = $row['user_id'];
        $username = $_SESSION['pending_username'];
        $password = $_SESSION['pending_password'];  

       $account_sql = "INSERT INTO user_account (username, password, user_id) VALUES (?, ?, ?)";
$account_stmt = mysqli_prepare($dbconnect, $account_sql);
mysqli_stmt_bind_param($account_stmt, "ssi", $username, $password, $user_id);

        if (!mysqli_stmt_execute($account_stmt)) {
            $error = "Lỗi tạo tài khoản: " . mysqli_error($dbconnect);
        }

    
        $role_sql = "INSERT INTO user_role (user_id, role_id) VALUES (?, 1)";  
        $role_stmt = mysqli_prepare($dbconnect, $role_sql);
        mysqli_stmt_bind_param($role_stmt, "i", $user_id);
        if (!mysqli_stmt_execute($role_stmt)) {
            $error = "Lỗi gán vai trò: " . mysqli_error($dbconnect);
        }

    
        unset($_SESSION['pending_username']);
        unset($_SESSION['pending_password']);

        if (empty($error)) {
            $success = "Xác thực thành công! Bạn có thể đăng nhập với username: $username";
            echo "<script>setTimeout(function(){ window.location.href='login.php'; }, 3000);</script>";
        }
    } else {
        $error = 'Mã xác thực không đúng!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Xác thực Email</title>
</head>
<body>
    <div class="container mt-5">
        <h3>Xác thực Email</h3>
        <p>Nhập mã xác thực gửi đến <?php echo htmlspecialchars($email); ?></p>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="post" class="needs-validation" novalidate>
            <div class="mb-3">
                <label for="verification_code" class="form-label">Mã xác thực (6 chữ số)</label>
                <input type="text" class="form-control" id="verification_code" name="verification_code" required pattern="\d{6}" maxlength="6">
                <div class="invalid-feedback">Mã phải là 6 chữ số.</div>
            </div>
            <button type="submit" class="btn btn-primary">Xác thực</button>
            <a href="register.php" class="btn btn-secondary">Quay lại đăng ký</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function() {
            'use strict';
            var form = document.querySelector('form');
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        })();
    </script>
    <?php include("footer.php"); ?>
</body>
</html>
