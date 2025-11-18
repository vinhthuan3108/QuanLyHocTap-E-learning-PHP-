<?php
session_start();
include_once('config/connect.php');
include_once('layout.php');

$message = '';
$show_form = false;
$user_id = null;
$session_valid = false;  // Flag để giữ session valid cho POST

if (isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);
    
    // Check user tồn tại
    $sql_check = "SELECT user_id FROM user_account WHERE user_id = ?";
    $stmt_check = mysqli_prepare($dbconnect, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "i", $user_id);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);

    if (mysqli_num_rows($result_check) > 0) {
        // Check session
        $session_user_id = isset($_SESSION['reset_user_id']) ? $_SESSION['reset_user_id'] : 0;
        if ($session_user_id == $user_id) {
            $show_form = true;
            $session_valid = true;  // Set flag để giữ valid cho POST
        } else {
            $message = "Phiên xác thực không hợp lệ. Vui lòng bắt đầu lại từ quên mật khẩu.";
        }
    } else {
        $message = "User ID không tồn tại.";
    }
} else {
    $message = "Thiếu thông tin xác thực. Vui lòng kiểm tra OTP lại.";
}

// Handle POST nếu show_form (sử dụng flag để tránh unset sớm)
if ($_SERVER['REQUEST_METHOD'] == "POST" && $show_form) {
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $password_confirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';

    if ($password !== $password_confirm) {
        $message = "Mật khẩu xác nhận không khớp";
    } elseif (strlen($password) < 6) {
        $message = "Mật khẩu phải ít nhất 6 ký tự";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql_update = "UPDATE user_account SET password=?, reset_token=NULL, reset_token_expiry=NULL WHERE user_id=?";
        $stmt_update = mysqli_prepare($dbconnect, $sql_update);
        mysqli_stmt_bind_param($stmt_update, "si", $hashedPassword, $user_id);
        if (mysqli_stmt_execute($stmt_update)) {
            $message = "Đặt lại mật khẩu thành công! <a href='login.php'>Đăng nhập ngay</a>";
            $show_form = false;
            // Unset session chỉ sau khi success
            unset($_SESSION['reset_user_id']);
            unset($_SESSION['reset_email']);
        } else {
            $message = "Lỗi cập nhật mật khẩu: " . mysqli_error($dbconnect);
        }
    }
}

// Nếu không phải POST, hoặc POST fail, unset session nếu valid
if (!$show_form && $session_valid) {
    unset($_SESSION['reset_user_id']);
    unset($_SESSION['reset_email']);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt lại mật khẩu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <?php if (!$show_form): ?>
            <div class="alert alert-info"><?php echo $message ?: 'Quá trình reset đã hoàn tất hoặc lỗi hệ thống.'; ?></div>
            <a href="login.php" class="btn btn-primary">Về đăng nhập</a>
            <a href="forgot_password.php" class="btn btn-secondary ms-2">Quên mật khẩu lại</a>
        <?php else: ?>
            <h3>Đặt lại mật khẩu</h3>
            <?php if ($message) echo "<div class='alert alert-danger'>$message</div>"; ?>
            <form method="post">
                <div class="mb-3">
                    <label for="password" class="form-label">Mật khẩu mới</label>
                    <input type="password" class="form-control" name="password" required minlength="6">
                    <div class="form-text">Mật khẩu phải ít nhất 6 ký tự.</div>
                </div>
                <div class="mb-3">
                    <label for="password_confirm" class="form-label">Nhập lại mật khẩu</label>
                    <input type="password" class="form-control" name="password_confirm" required minlength="6">
                </div>
                <button type="submit" class="btn btn-success">Đặt lại mật khẩu</button>
                <a href="login.php" class="btn btn-secondary ms-2">Hủy</a>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>