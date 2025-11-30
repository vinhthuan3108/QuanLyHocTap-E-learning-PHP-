<?php
include_once __DIR__ . '/../../config/connect.php';

session_start();

// Kiểm tra user đăng nhập
if (isset($_SESSION['full_name'])) {
    $username_now = $_SESSION['full_name'];
} else {
    $username_now = "User not logged in";
}

// 🔥 SỬA LỖI Ở ĐÂY:
// Luôn cập nhật session nếu URL có id (người dùng bấm khóa mới)
if (isset($_GET['id'])) {
    $_SESSION['course_id'] = $_GET['id'];
}

// Nếu không có id trong URL và cũng không có trong session → lỗi
if (!isset($_SESSION['course_id'])) {
    echo "Không tìm thấy ID khóa học.";
    exit();
}

$course_id = $_SESSION['course_id'];

// Lấy thông tin khóa học
$sql_layout = "SELECT * FROM course WHERE course_id = $course_id";
$result_layout = mysqli_query($dbconnect, $sql_layout);

if ($result_layout && mysqli_num_rows($result_layout) > 0) {
    $row_layout = mysqli_fetch_assoc($result_layout);
} else {
    echo "Không tìm thấy khóa học.";
    exit();
}

// Lấy avatar người dùng
$student_id = $_SESSION['user_id'];
$sql_profile = "SELECT image FROM user WHERE user_id = $student_id";
$result_profile = mysqli_query($dbconnect, $sql_profile);
$row_profile = mysqli_fetch_assoc($result_profile);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            padding-top: 70px;
        }

        .navbar-nav .nav-link {
            padding: 8px 14px;
            border-radius: 6px;
            transition: background-color 0.2s ease;
        }

        .navbar-nav .nav-link:hover {
            background-color: rgba(91, 87, 87, 0.1);
        }
    </style>
</head>

<body>

<nav class="navbar navbar-expand-lg bg-white border-bottom fixed-top">
    <div class="container-fluid">

        <!-- Tên khóa học -->
        <a class="navbar-brand text-dark" href="#">
            <?php echo $row_layout['course_code'] . " - " . $row_layout['course_name']; ?>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav" aria-controls="navbarNav"
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">

            <ul class="navbar-nav ms-auto">

                <li class="nav-item">
                    <a class="nav-link text-dark" href="/student/course/index.php">Trang chủ</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-dark" href="/student/course/post.php">Thông báo</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-dark" href="/student/course/content/content.php">Nội dung</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-dark" href="/student/course/exam.php">Bài tập và kiểm tra</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-dark" href="/student/course/grade.php">Điểm số</a>
                </li>

                <!-- User dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-dark" href="#" id="navbarDropdown"
                       role="button" data-bs-toggle="dropdown">
                        <?php echo $username_now; ?>
                        <img src="/assets/images/<?php echo $row_profile['image']; ?>" 
                             alt="Avatar" class="rounded-circle" width="30" height="30">
                    </a>

                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="/student/my.php">Trang cá nhân</a>
                        <a class="dropdown-item" href="/student/index.php">Trang chủ</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="../../account/logout.php">Đăng xuất</a>
                    </div>
                </li>

            </ul>
        </div>
    </div>
</nav>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
