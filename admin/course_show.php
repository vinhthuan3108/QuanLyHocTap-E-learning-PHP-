<?php
include_once('layout.php');
include_once('../config/connect.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$teacher_id = $_GET['teacher_id'];
$course_id = $_GET['id'];

// Lấy thông tin giáo viên
$sql_user = "SELECT * FROM user WHERE user_id = $teacher_id";
$result_user = mysqli_query($dbconnect, $sql_user);
$row_user = $result_user ? mysqli_fetch_assoc($result_user) : null;

// Lấy thông tin số lượng học viên
$sql_count_member = "SELECT COUNT(*) AS member_count FROM course_member WHERE course_id = $course_id";
$result_count_member = mysqli_query($dbconnect, $sql_count_member);
$row_count_member = $result_count_member ? mysqli_fetch_assoc($result_count_member) : ['member_count'=>0];

// Lấy thông tin khóa học
$sql_course = "SELECT * FROM course WHERE course_id = $course_id";
$result_course = mysqli_query($dbconnect, $sql_course);
if ($result_course) {
    $row_course = mysqli_fetch_assoc($result_course);
} else {
    echo "Lỗi lấy thông tin khóa học: " . mysqli_error($dbconnect);
    exit();
}

// Lấy lịch học
$sql_schedule = "SELECT * FROM course_schedule WHERE course_id = $course_id";
$result_schedule = mysqli_query($dbconnect, $sql_schedule);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Chi tiết khóa học</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
.card-header { background-color: #0d6efd; color: #fff; font-weight: 500; }
.info-divider { border-top: 2px solid #0d6efd; margin: 10px 0; }
</style>
</head>

<div>
        <?php include "sidebar.php"; ?>

    <div class="main " id="mainContent">
        <div class="container mt-4">

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3><a href="courses.php" class="text-decoration-none"><i class="bi bi-arrow-left-circle"></i> Quay lại</a></h3>
                <div>
                    <a class="btn btn-primary me-2" href="course_edit.php?id=<?php echo $course_id; ?>&teacher_id=<?php echo $teacher_id; ?>">Chỉnh sửa khóa học</a>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteCourseModal">Xóa khóa học</button>
                </div>
            </div>

            <!-- Thông tin khóa học -->
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header">Thông tin khóa học</div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $row_course['course_code'] . ' - ' . $row_course['course_name']; ?></h5>
                            <p class="card-text"><?php echo $row_course['course_description']; ?></p>
                            <hr class="info-divider">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><b>Giáo viên:</b> <?php echo $row_user ? $row_user['full_name'] : 'Không có thông tin'; ?></li>
                                <li class="list-group-item"><b>Số lượng học viên:</b> <?php echo $row_count_member['member_count']; ?></li>
                                <li class="list-group-item"><b>Ngày bắt đầu:</b> <?php echo date('d/m/Y', strtotime($row_course['start_date'])); ?></li>
                                <li class="list-group-item"><b>Ngày kết thúc:</b> <?php echo date('d/m/Y', strtotime($row_course['end_date'])); ?></li>
                                <li class="list-group-item"><b>Trạng thái:</b> <?php echo ($row_course['status']=='A') ? 'Đã duyệt' : 'Chưa duyệt'; ?></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Thời khóa biểu -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header">Thời khóa biểu</div>
                        <div class="card-body">
                            <?php if ($result_schedule && mysqli_num_rows($result_schedule) > 0): ?>
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Ngày trong tuần</th>
                                            <th>Thời gian</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($row_schedule = mysqli_fetch_assoc($result_schedule)): ?>
                                            <tr>
                                                <td>
                                                    <?php
                                                    echo ($row_schedule['day_of_week']=='C') ? 'Chủ Nhật' : 'Thứ ' . $row_schedule['day_of_week'];
                                                    ?>
                                                </td>
                                                <td><?php echo $row_schedule['start_time'] . ' - ' . $row_schedule['end_time']; ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p class="text-muted">Chưa có lịch học cho khóa học này.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            <?php include("../footer.php"); ?>

    </div>

<!-- Modal xóa khóa học -->
<div class="modal fade" id="deleteCourseModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa khóa học: <b><?php echo htmlspecialchars($row_course['course_name']); ?></b> ?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <!-- Form xóa theo POST -->
                <form action="process.php" method="post">
                    <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                    <button type="submit" class="btn btn-danger" name="delete_course">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>


</body>
</html>
