<?php
include("../layout.php");
$course_id = $_SESSION['course_id'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bài kiểm tra</title>
</head>

<body>
    <header class="container mt-4">
        <div class="row">
            <div class="col-md-6">
                <h3>Bài kiểm tra</h3>
            </div>
            <div class="col-md-6">
                <div class="input-group mb-3 float-end">
                    <input type="text" class="form-control" placeholder="Tìm kiếm ...">
                    <button class="btn btn-dark" type="button">Tìm</button>
                </div>
            </div>
        </div>
    </header>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <a class="btn btn-primary float-end" href="create_exam.php">+ Tạo bài kiểm tra</a>
            </div>
        </div>
    </div>
    <div class="container mt-4">
        <table class="table">
            <thead>
                <th>Tiêu đề</th>
                <th>Loại điểm</th>
                <th>Thời gian mở</th>
                <th>Thời gian đóng</th>
                <th>Thao tác</th>
            </thead>
            <tbody>
                <?php
                $sql_exam = "SELECT e.*, gc.grade_column_name 
                            FROM exam e 
                            JOIN grade_column gc ON e.column_id = gc.column_id 
                            WHERE e.course_id = $course_id 
                            ORDER BY e.created_at DESC"; 
                $result_exam = mysqli_query($dbconnect, $sql_exam);

                if ($result_exam && mysqli_num_rows($result_exam) > 0) {
                    while ($row = mysqli_fetch_array($result_exam)) {
                        $exam_id = $row['exam_id'];

                        $edit_link = "edit_exam_info.php?exam_id=" . $exam_id; 
                        
                        $delete_link = "process.php?action=delete_exam&exam_id=" . $exam_id;
                        
                        $view_link = "view_exam_results.php?exam_id=" . $exam_id; 

                        echo '<tr>
                            <td>' . htmlspecialchars($row['title']) . '</td>
                            <td>' . htmlspecialchars($row['grade_column_name']) . '</td>
                            <td>' . date('d/m/Y H:i', strtotime($row['open_time'])) . '</td>
                            <td>' . date('d/m/Y H:i', strtotime($row['close_time'])) . '</td>
                            <td>
                                <a class="me-2 btn btn-sm btn-warning text-white" href="' . $edit_link . '">Sửa đề</a>
                                <a class="btn btn-sm btn-danger" 
                                   href="' . $delete_link . '" 
                                   onclick="return confirm(\'Bạn có chắc chắn muốn xóa bài kiểm tra này? Toàn bộ câu hỏi và bài làm của sinh viên cũng sẽ bị xóa!\');">
                                   Xóa
                                </a>
                            </td>
                        </tr>';
                    }
                } else {
                    echo '<tr><td colspan="5" class="text-center">Chưa có bài kiểm tra nào</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php include("../../../footer.php"); ?>
</body>
</html>