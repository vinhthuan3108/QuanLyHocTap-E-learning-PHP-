<?php
session_start();
include_once('config/connect.php');
include_once('layout.php');

$error = '';

$email = isset($_GET['email']) ? $_GET['email'] : '';

if (empty($email)) {
    header('Location: forgot_password.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input_otp = $_POST['otp'];

    if (empty($input_otp)) {
        $error = "Vui lòng nhập mã OTP.";
    } else {
       
        $sql_user = "SELECT ua.user_id, ua.reset_token, ua.reset_token_expiry FROM user_account ua
                     INNER JOIN user u ON ua.user_id = u.user_id WHERE u.email = ?";
        $stmt_user = mysqli_prepare($dbconnect, $sql_user);
        mysqli_stmt_bind_param($stmt_user, "s", $email);
        mysqli_stmt_execute($stmt_user);
        $result_user = mysqli_stmt_get_result($stmt_user);
        $row_user = mysqli_fetch_assoc($result_user);

        if ($row_user) {
            $stored_otp = $row_user['reset_token'];
            $expiry = $row_user['reset_token_expiry'];

            if (strtotime($expiry) > time() && $input_otp == $stored_otp) {
               
                $sql_clear = "UPDATE user_account SET reset_token = NULL, reset_token_expiry = NULL WHERE user_id = ?";
                $stmt_clear = mysqli_prepare($dbconnect, $sql_clear);
                mysqli_stmt_bind_param($stmt_clear, "i", $row_user['user_id']);
                mysqli_stmt_execute($stmt_clear);

               
                $session_user_id = isset($_SESSION['reset_user_id']) ? $_SESSION['reset_user_id'] : 0;
                if ($session_user_id == $row_user['user_id']) {
                  
                    header("Location: reset_password.php?user_id=" . $row_user['user_id']);
                    exit;
                } else {
                    $error = "Lỗi xác thực. Vui lòng thử lại.";
                }
            } else {
                $error = "Mã OTP không đúng hoặc đã hết hạn.";
            }
        } else {
            $error = "Không tìm thấy email.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xác thực OTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f3f6fc; 
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', sans-serif;
        }

        .otp-card {
            background: #ffffff;
            width: 420px;
            padding: 30px 35px;
           
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        }

        h3 {
            font-weight: 700;
            color: #1a4d91;
        }

        .otp-label {
            font-weight: 600;
        }

        .form-control {
            padding: 12px;
            font-size: 1.1rem;
            border-radius: 10px;
        }

        .btn-primary {
            background: #1a4d91;
            border: none;
            padding: 12px 20px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 10px;
        }

        .btn-primary:hover {
            background: #163f75;
        }

        .btn-secondary {
            border-radius: 10px;
        }

       
    </style>
</head>
<body>

    <div class="otp-card">
        
        <h3 class="text-center mb-3">Xác thực OTP</h3>

        <p class="text-muted text-center">
            Mã OTP đã gửi đến <strong><?php echo htmlspecialchars($email); ?></strong>.  
            <br>OTP có hiệu lực trong <strong>10 phút</strong>.
        </p>

        <?php if (!empty($error)) : ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label class="form-label otp-label">Nhập mã OTP</label>
                <input type="text" 
                       class="form-control text-center fs-4" 
                       name="otp" 
                       required>
            </div>

            <button type="submit" class="btn btn-primary w-100 mb-2">Xác nhận</button>

            <a href="forgot_password.php" class="btn btn-secondary w-100">
                Gửi lại OTP
            </a>
        </form>
    </div>

</body>
</html>
