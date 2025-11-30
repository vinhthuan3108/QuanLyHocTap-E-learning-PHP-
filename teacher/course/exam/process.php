<?php
// process.php
include("../layout.php");


// Lấy hành động từ URL
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'delete_exam':
        // Xử lý xóa bài kiểm tra
        if (isset($_GET['exam_id'])) {
            $exam_id = intval($_GET['exam_id']); // Chuyển về số nguyên để bảo mật
            $course_id = $_SESSION['course_id']; // Dùng để verify xem giáo viên có quyền xóa bài trong lớp này không

            // Kiểm tra xem bài kiểm tra có thuộc khóa học hiện tại không (Tránh xóa bậy bài lớp khác)
            $check_sql = "SELECT exam_id FROM exam WHERE exam_id = $exam_id AND course_id = $course_id";
            $check_query = mysqli_query($dbconnect, $check_sql);

            if (mysqli_num_rows($check_query) > 0) {
                // Thực hiện xóa
                // Lưu ý: Do đã cài đặt ON DELETE CASCADE trong database (ở bước trước), 
                // nên khi xóa exam thì câu hỏi và bài làm sẽ tự động xóa theo.
                $sql_delete = "DELETE FROM exam WHERE exam_id = $exam_id";
                
                if (mysqli_query($dbconnect, $sql_delete)) {
                    // Xóa thành công
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
        // Nếu không có action thì quay về
        header("Location: exam.php");
        break;
}
?>