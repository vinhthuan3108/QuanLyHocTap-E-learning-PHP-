<?php
ob_start();
session_start();
require '../src/Exception.php';
require '../src/PHPMailer.php';
require '../src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

include_once('../layout.php');

include_once('../config/connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fullName = $_POST['fullName'];
    $username = $_POST['username'];  
    $password = $_POST['password'];  
    $idCard = $_POST['idCard'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $phoneNumber = $_POST['phoneNumber'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $portrait = $_FILES['portrait']['name'];
    $image_tmp = $_FILES['portrait']['tmp_name'];

    $check_email_sql = "SELECT * FROM user WHERE email = ?";
$stmt_email = mysqli_prepare($dbconnect, $check_email_sql);
mysqli_stmt_bind_param($stmt_email, "s", $email);
mysqli_stmt_execute($stmt_email);
$result_email = mysqli_stmt_get_result($stmt_email);

if(mysqli_num_rows($result_email) > 0){
    die("Email này đã được đăng ký cho một tài khoản khác!");
}


    $check_sql = "SELECT * FROM user_account WHERE username = '$username'";
    $check_result = mysqli_query($dbconnect, $check_sql);
    if (mysqli_num_rows($check_result) > 0) {
        die("Username đã tồn tại!");
    }
    
    
    if(!move_uploaded_file($image_tmp, '../assets/images/'.$portrait)) {
        die("Upload ảnh thất bại");
    }

    $options = [
        'cost' => 12,  
    ];
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT, $options);  


    $_SESSION['pending_username'] = $username;
    $_SESSION['pending_password'] = $hashedPassword;   

    $verification_code = rand(100000, 999999);

    $sql = "INSERT INTO user (full_name,citizen_id,date_of_birth,gender,phone,email,address,image,verification_code)
            VALUES ('$fullName','$idCard','$dob','$gender','$phoneNumber','$email','$address','$portrait','$verification_code')";

    if(!mysqli_query($dbconnect, $sql)) {
        die("Lỗi: ".mysqli_error($dbconnect));
    }

    $app_password = 'qsyi hdos gdou twnh';   
    $mail = new PHPMailer(true);

    try {
        $mail->CharSet = 'UTF-8';
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = "xuannam1234zz@gmail.com";
        $mail->Password = $app_password;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        
        $mail->Port = 587;

        $mail->setFrom('xuannam1234zz@gmail.com', 'Xuân Nam');
        $mail->addAddress($email, $fullName);

        $mail->isHTML(true);
        $mail->Subject = 'Mã xác thực đăng ký';
        $mail->Body = "Mã xác thực của bạn là: <b>$verification_code</b>";

        $mail->send();

       
        header("Location: verify_email.php?email=" . urlencode($email));
        exit();

    } catch (Exception $e) {
        echo "Gửi email thất bại: {$mail->ErrorInfo}";
    }
}
ob_end_flush(); 
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <title>Tạo tài khoản mới</title>
</head>

<body>
    <div class="container mt-5">
        <h3>Tạo tài khoản mới</h3>

        <form id="accountForm" method="post" class="needs-validation" novalidate enctype="multipart/form-data">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="username" class="form-label">Tên đăng nhập</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                    <div class="invalid-feedback">Tên đăng nhập không được trống.</div>
                </div>
                <div class="col-md-6">
                    <label for="password" class="form-label">Mật khẩu</label>
                    <input type="password" class="form-control" id="password" name="password" required minlength="6">
                    <div class="invalid-feedback">Mật khẩu phải ít nhất 6 ký tự.</div>
                </div>
            </div>  
        
        <div class="row mb-3">
                <div class="col-md-6">
                    <label for="fullName" class="form-label">Họ và tên</label>
                    <input type="text" class="form-control" id="fullName" name="fullName" required>
                    <div class="invalid-feedback">
                        Họ và tên không được trống.
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="idCard" class="form-label">Mã số căn cước công dân</label>
                    <input type="text" class="form-control" id="idCard" name="idCard" required>
                    <div class="invalid-feedback">
                        Mã số căn cước công dân không được trống.
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="dob" class="form-label">Ngày sinh</label>
                    <input type="date" class="form-control" id="dob" name="dob" required>
                </div>
                <div class="col-md-4">
                    <label for="gender" class="form-label">Giới tính</label>
                    <select class="form-select" id="gender" name="gender" required>
                        <option value="" disabled selected>Chọn giới tính</option>
                        <option value="M">Nam</option>
                        <option value="F">Nữ</option>
                        <option value="O">Khác</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="phoneNumber" class="form-label">Số điện thoại</label>
                    <input type="tel" class="form-control" id="phoneNumber" name="phoneNumber" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="col-md-6">
                    <label for="address" class="form-label">Địa chỉ</label>
                    <input type="text" class="form-control" id="address" name="address" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="portrait" class="form-label">Ảnh chân dung</label>
                    <input type="file" class="form-control" id="portrait" name="portrait" required>
                    <div class="invalid-feedback">
                        Vui lòng chọn ảnh chân dung.
                    </div>
                </div>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary" name="sbm">Tạo tài khoản mới</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-rqI2waM7CtpVHmUnY9NXfQTKc3N8RBLtbl6TbY3b3NC6HjbF2wF81v11z5KnMK17" crossorigin="anonymous"></script>
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
    </script>
    <?php include("../footer.php"); ?>
</body>
</html>
