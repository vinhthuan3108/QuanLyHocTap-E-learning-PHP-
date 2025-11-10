<?php
//cau hinh ket noi
$servername = "localhost";
$username   = "root";
$password   = ""; // để trống nếu chưa đặt mật khẩu
$dbname     = "quanlysinhvientnt";

$dbconnect = mysqli_connect($servername, $username, $password, $dbname);
if ($dbconnect) {
    mysqli_query($dbconnect, "SET NAMES 'utf8'");
} else {
    echo "Kết nối thất bại";
}
