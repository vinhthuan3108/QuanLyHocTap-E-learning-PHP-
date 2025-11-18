<?php
session_start();
include_once('../config/connect.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['course_id'])) {
    $course_id = (int)$_GET['course_id'];
    $user_id = $_SESSION['user_id'];
    
    // Kiểm tra xem khóa học có tồn tại và miễn phí không
    $check_course_sql = "SELECT * FROM course WHERE course_id = $course_id AND price = 0";
    $course_result = mysqli_query($dbconnect, $check_course_sql);
    
    if (mysqli_num_rows($course_result) > 0) {
        // Kiểm tra xem user đã tham gia khóa học này chưa
        $check_member_sql = "SELECT * FROM course_member WHERE course_id = $course_id AND student_id = $user_id";
        $member_result = mysqli_query($dbconnect, $check_member_sql);
        
        if (mysqli_num_rows($member_result) == 0) {
            // Thêm user vào khóa học
            $insert_sql = "INSERT INTO course_member (course_id, student_id) VALUES ($course_id, $user_id)";
            if (mysqli_query($dbconnect, $insert_sql)) {
                // Thành công, chuyển hướng đến trang khóa học
                header("Location: course/index.php?id=$course_id");
                exit;
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra khi tham gia khóa học.";
                header("Location: index.php");
                exit;
            }
        } else {
            // User đã tham gia, chuyển hướng thẳng đến khóa học
            header("Location: course/index.php?id=$course_id");
            exit;
        }
    } else {
        $_SESSION['error'] = "Khóa học không tồn tại hoặc không phải khóa học miễn phí.";
        header("Location: index.php");
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}
?>