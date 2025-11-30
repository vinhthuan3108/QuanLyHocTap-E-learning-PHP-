<?php
include("../layout.php");

$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'delete_exam':
        if (isset($_GET['exam_id'])) {
            $exam_id = intval($_GET['exam_id']); 
            $course_id = $_SESSION['course_id']; 

            $check_sql = "SELECT exam_id FROM exam WHERE exam_id = $exam_id AND course_id = $course_id";
            $check_query = mysqli_query($dbconnect, $check_sql);

            if (mysqli_num_rows($check_query) > 0) {

                $sql_delete = "DELETE FROM exam WHERE exam_id = $exam_id";
                
                if (mysqli_query($dbconnect, $sql_delete)) {
                    echo "<script>alert('Xóa bài kiểm tra thành công!'); window.location.href='exam.php';</script>";
                } else {
                    echo "<script>alert('Lỗi: " . mysqli_error($dbconnect) . "'); window.location.href='exam.php';</script>";
                }
            } else {
                echo "<script>alert('Bạn không có quyền xóa bài kiểm tra này!'); window.location.href='exam.php';</script>";
            }
        }
        break;

    default:
        header("Location: exam.php");
        break;
}
?>