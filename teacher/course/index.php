<?php
include_once('layout.php');
include_once('../../config/connect.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$teacher_id = $row_layout['teacher_id'];
$sql_user = "SELECT * FROM user WHERE user_id = $teacher_id";
$result_user = mysqli_query($dbconnect, $sql_user);
$row_user = mysqli_fetch_assoc($result_user);

$_SESSION['user_id'] = $row_user['user_id'];

$sql_count_member = "SELECT COUNT(*) AS member_count FROM course_member WHERE course_id = $course_id";
$result_count_member = mysqli_query($dbconnect, $sql_count_member);
$row_count_member = mysqli_fetch_assoc($result_count_member);

$sql_post = "SELECT * FROM post WHERE course_id = $course_id AND user_id = $teacher_id ORDER BY created_at DESC LIMIT 5";
$result_post = mysqli_query($dbconnect, $sql_post);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang khóa học</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .info-divider {
            border-top: 1px solid #dee2e6;
            margin: 10px 0 15px 0;
        }

        .card-title {
            font-weight: bold;
        }

        .course-header {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 0.5rem;
        }

        .course-header h3 {
            margin-bottom: 10px;
        }

        .btn-space {
            margin-left: 5px;
        }

        .table-hover tbody tr:hover {
            background-color: #f1f3f5;
        }
    </style>
</head>

<body>
<div class="container my-4">
    <!-- Header khóa học -->
    <div class="row course-header align-items-center">
        <div class="col-md-8">
            <h3><?php echo $row_layout['course_code'] . " - " . $row_layout['course_name']; ?></h3>
            <p><?php echo $row_layout['course_description']; ?></p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="course_details/edit_course.php" class="btn btn-primary btn-sm">Thay đổi thuộc tính</a>
            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteCourseModal">Xóa khóa học</button>
        </div>
    </div>

    <!-- Modal Xóa khóa học -->
    <div class="modal fade" id="deleteCourseModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Xác nhận xóa khóa học</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Bạn có chắc chắn muốn xóa khóa học này?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <form action="process.php" method="post">
                        <button type="submit" class="btn btn-danger" name="delete_course">Xóa</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Thông tin khóa học & thời khóa biểu -->
    <div class="row mt-4">
        <div class="col-lg-6">
            <!-- Thông tin khóa học -->
            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Thông tin khóa học</h5>
                    <hr class="info-divider">
                    <table class="table table-borderless mb-0">
                        <tbody>
                            <tr>
                                <td>Giáo viên</td>
                                <td><?php echo $row_user['full_name']; ?></td>
                                <td>
                                    <a class="btn btn-outline-primary btn-sm" href="course_details/my_teacher.php">Chi tiết</a>
                                </td>
                            </tr>
                            <tr>
                                <td>Số lượng học viên</td>
                                <td><?php echo $row_count_member['member_count']; ?></td>
                                <td>
                                    <a class="btn btn-outline-primary btn-sm" href="course_details/member.php">Danh sách</a>
                                </td>
                            </tr>
                            <tr>
                                <td>Ngày bắt đầu</td>
                                <td><?php echo date('d/m/Y', strtotime($row_layout['start_date'])); ?></td>
                            </tr>
                            <tr>
                                <td>Ngày kết thúc</td>
                                <td><?php echo date('d/m/Y', strtotime($row_layout['end_date'])); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Thời khóa biểu -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Thời khóa biểu</h5>
                    <hr class="info-divider">
                    <table class="table table-bordered table-hover mb-3">
                        <thead class="table-light">
                            <tr>
                                <th>Ngày</th>
                                <th>Thời gian</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $sql_schedule = "SELECT * FROM course_schedule WHERE course_id = $course_id";
                        $result_schedule = mysqli_query($dbconnect, $sql_schedule);
                        while ($row_schedule = mysqli_fetch_array($result_schedule)) {
                            echo "<tr>
                                    <td>Thứ {$row_schedule['day_of_week']}</td>
                                    <td>{$row_schedule['start_time']} - {$row_schedule['end_time']}</td>
                                  </tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                    <a href="course_details/edit_schedule.php" class="btn btn-primary btn-sm">Cập nhật</a>
                </div>
            </div>
        </div>

        <!-- Bài đăng mới -->
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Bài đăng mới</h5>
                    <hr class="info-divider">
                    <?php if(mysqli_num_rows($result_post) == 0): ?>
                        <p>Chưa có bài đăng nào.</p>
                    <?php else: ?>
                        <ul class="list-group list-group-flush">
                            <?php while($row_post = mysqli_fetch_array($result_post)): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?php echo $row_post['title']; ?>
                                    <a href="post/view_post.php?post_id=<?php echo $row_post['post_id']; ?>" class="btn btn-sm btn-outline-primary">Xem</a>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->
<?php include("../../footer.php"); ?>
</body>
</html>
