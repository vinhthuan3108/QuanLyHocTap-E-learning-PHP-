<?php

$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $envLines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($envLines as $line) {
        if (strpos(trim($line), '#') === 0) continue; // Bỏ qua comment
        
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        
        // Đặt biến môi trường
        $_ENV[$key] = $value;
        putenv("$key=$value");
    }
}

//cau hinh ket noi
$servername = $_ENV['DB_HOST']; 
$username   = $_ENV['DB_USER'];
$password   = $_ENV['DB_PASS'];
$dbname     = $_ENV['DB_NAME'];

$dbconnect = mysqli_connect($servername, $username, $password, $dbname);
if ($dbconnect) {
    mysqli_query($dbconnect, "SET NAMES 'utf8'");
} else {
    echo "Kết nối thất bại";
}
