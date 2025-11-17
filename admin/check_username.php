<?php
include_once "../config/connect.php";

if (isset($_GET['username'])) {
    $username = mysqli_real_escape_string($dbconnect, $_GET['username']);
    $current_username = isset($_GET['current']) ? mysqli_real_escape_string($dbconnect, $_GET['current']) : '';
    
    // Nếu username trùng với username hiện tại, thì vẫn cho phép
    if ($username === $current_username) {
        echo json_encode(['available' => true]);
        exit;
    }
    
    $sql = "SELECT * FROM user_account WHERE username = '$username'";
    $result = mysqli_query($dbconnect, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        echo json_encode(['available' => false]);
    } else {
        echo json_encode(['available' => true]);
    }
}

mysqli_close($dbconnect);
?>