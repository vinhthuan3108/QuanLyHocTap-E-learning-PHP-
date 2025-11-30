<?php
include_once('layout.php');
include_once('../config/connect.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['full_name'])) {
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT * FROM user WHERE user_id = $user_id";
    $result = mysqli_query($dbconnect, $sql);
    $user = mysqli_fetch_assoc($result);
} else {
    $user = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang cá nhân</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f5f7fa;
        }
        .profile-header {
            background-color: #4e73df;
            color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .profile-header img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border: 4px solid white;
        }
        .profile-header h2 {
            margin-bottom: 0;
        }
        .card {
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            border: none;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .info-title {
            font-weight: 600;
            color: #4e73df;
        }
        .course-item {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .course-item:last-child {
            border-bottom: none;
        }
        .btn-edit {
            background-color: #1cc88a;
            border: none;
        }
        .btn-edit:hover {
            background-color: #17a673;
        }
    </style>
</head>
<body>
<div class="container mt-4">

    <?php if($user): ?>
    <!-- Profile Header -->
    <div class="profile-header d-flex align-items-center">
        <img src="../assets/images/<?php echo $user['image'] ?>" alt="Profile Image" class="rounded-circle mr-4">
        <div class="ml-4">
            <h2><?php echo $user['full_name']; ?></h2>
            <p class="mb-2"><i class="fa-solid fa-user-graduate"></i> Học sinh</p>
            <a href="edit_student_profile.php" class="btn btn-edit text-white"><i class="fa-solid fa-pen"></i> Chỉnh sửa thông tin</a>
        </div>
    </div>

    <div class="row">
        <!-- Thông tin cá nhân -->
        <div class="col-md-6">
            <div class="card p-3">
                <h4 class="mb-3">Thông tin cá nhân</h4>
                <p><span class="info-title"><i class="fa-solid fa-calendar-days"></i> Ngày sinh:</span> <?php echo date('d/m/Y', strtotime($user['date_of_birth'])); ?></p>
                <p><span class="info-title"><i class="fa-solid fa-venus-mars"></i> Giới tính:</span> <?php echo ($user['gender']=="M"?"Nam":"Nữ"); ?></p>
                <p><span class="info-title"><i class="fa-solid fa-envelope"></i> Email:</span> <?php echo $user['email']; ?></p>
                <p><span class="info-title"><i class="fa-solid fa-phone"></i> Số điện thoại:</span> <?php echo $user['phone']; ?></p>
                <p><span class="info-title"><i class="fa-solid fa-id-card"></i> CCCD/CMND:</span> <?php echo $user['citizen_id']; ?></p>
            </div>
        </div>

        <!-- Các khóa học -->
        <div class="col-md-6">
            <div class="card p-3">
                <h4 class="mb-3">Các khóa học đang tham gia</h4>
                <?php
                $sql_courses = "SELECT * FROM course co
                                INNER JOIN course_member cm ON co.course_id = cm.course_id
                                WHERE student_id = $user_id";
                $courses = mysqli_query($dbconnect, $sql_courses);
                if(mysqli_num_rows($courses) > 0):
                    while($course = mysqli_fetch_assoc($courses)):
                ?>
                    <div class="course-item">
                        <i class="fa-solid fa-book"></i> <?php echo $course['course_code'] . " - " . $course['course_name']; ?>
                    </div>
                <?php
                    endwhile;
                else:
                    echo "<p>Chưa tham gia khóa học nào</p>";
                endif;
                ?>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end mt-3">
        <a href="index.php?id=2" class="btn btn-secondary"><i class="fa-solid fa-right-from-bracket"></i> Thoát</a>
    </div>

    <?php else: ?>
        <div class="alert alert-warning">
            Bạn chưa đăng nhập.
        </div>
    <?php endif; ?>

</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
