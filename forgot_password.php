<?php
session_start();
include_once('config/connect.php');
include_once('layout.php');
require 'src/PHPMailer.php';
require 'src/SMTP.php';
require 'src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    $sql = "SELECT user_id, full_name FROM user WHERE email = ?";
    $stmt = mysqli_prepare($dbconnect, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $user_id = $row['user_id'];
        $full_name = $row['full_name'];

        $otp = rand(100000, 999999);
        $expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));  // FIX: Tăng lên 10 phút

        $sql_token = "UPDATE user_account SET reset_token=?, reset_token_expiry=? WHERE user_id=?";
        $stmt_token = mysqli_prepare($dbconnect, $sql_token);
        mysqli_stmt_bind_param($stmt_token, "ssi", $otp, $expiry, $user_id);
        mysqli_stmt_execute($stmt_token);

        $mail = new PHPMailer(true);
        try {
            $mail->CharSet = 'UTF-8';
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = "xuannam1234zz@gmail.com";
            $mail->Password = 'qsyi hdos gdou twnh';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('xuannam1234zz@gmail.com', 'LMS');
            $mail->addAddress($email, $full_name);
            $mail->isHTML(true);

            $mail->Subject = 'Mã OTP đặt lại mật khẩu';
            $mail->Body = "
                Xin chào $full_name,<br><br>
                Mã OTP đặt lại mật khẩu của bạn là: <b>$otp</b><br>
                OTP sẽ hết hạn sau 10 phút.<br><br>  <!-- FIX: Cập nhật thời gian -->
                Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.
            ";

            $mail->send();

            $_SESSION['reset_user_id'] = $user_id;
            $_SESSION['reset_email'] = $email;

            header("Location: verify_otp.php?email=" . urlencode($email));
            exit;

        } catch (Exception $e) {
            $message = "Gửi email thất bại: {$mail->ErrorInfo}";
        }

    } else {
        $message = "Email không tồn tại trong hệ thống";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quên mật khẩu</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: "Segoe UI", sans-serif;
        }

        .reset-card {
            width: 420px;
            background: #fff;
            padding: 35px 30px;
            box-shadow: 0 12px 35px rgba(0,0,0,0.12);
        }

        h3 {
            font-weight: 700;
            color: #0d47a1;
            margin-bottom: 20px;
        }

        .form-control {
            
            padding: 12px;
            font-size: 1rem;
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
           
            font-size: 1.1rem;
            font-weight: 600;
            background: linear-gradient(135deg, #1976d2, #0d47a1);
            border: none;
            color: white;
            margin-top: 5px;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #1565c0, #0b3c91);
        }

        .back-login {
            text-decoration: none;
            font-size: 0.95rem;
            color: #0d47a1;
        }

        .back-login:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

<div class="reset-card">

    <h3 class="text-center">Quên mật khẩu</h3>

    <?php if ($message): ?>
        <div class="alert alert-danger text-center"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="post">

        <label class="form-label fw-semibold">Nhập email của bạn</label>
        <input type="email" name="email" class="form-control shadow-sm"  required>

        <button type="submit" class="btn-submit">Gửi mã OTP</button>

        <div class="text-center mt-3">
            <a href="login.php" class="back-login">
                <i class="fa-solid fa-arrow-left-long"></i> Quay lại đăng nhập
            </a>
        </div>

    </form>

</div>

</body>
</html>
