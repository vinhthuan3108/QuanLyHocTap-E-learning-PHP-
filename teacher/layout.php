<?php
include_once('../config/connect.php');

session_start();

if (isset($_SESSION['full_name'])) {
    $username_now = $_SESSION['full_name'];
} else {
    $username_now = "User not logged in";
}

$teacher_id = $_SESSION['user_id'];
$sql_profile = "SELECT image FROM user WHERE user_id = $teacher_id";
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
            background-color: white;
        }

        /* Menu item styling */
        .navbar-nav .nav-link {
            padding: 8px 14px;
            border-radius: 6px;
            color: #000 !important;
            transition: background-color 0.2s ease;
        }

        .navbar-nav .nav-link:hover {
            background-color: rgba(91, 87, 87, 0.1);
        }

        .navbar-brand {
            color: #000 !important;
        }

        .dropdown-menu a.dropdown-item:hover {
            background-color: rgba(91, 87, 87, 0.1);
        }
    </style>
</head>

<body class="with-navbar">

    <nav class="navbar navbar-expand-sm bg-white border-bottom fixed-top">
        <div class="container-fluid">

            <a class="navbar-brand" href="#">TNT</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">

                <ul class="navbar-nav ms-auto">

                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Trang chủ</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="courses.php">Khóa học của tôi</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="othercourses.php">Các khóa học khác</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="schedule.php">Lịch giảng dạy</a>
                    </li>

                    <!-- User dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" id="navbarDropdown"
                           role="button" data-bs-toggle="dropdown" aria-expanded="false">

                            <?php echo $username_now; ?>

                            <img src="../assets/images/course1.jpg"
                                 class="rounded-circle" width="30" height="30">
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="my.php">Trang cá nhân</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../logout.php">Đăng xuất</a></li>
                        </ul>

                    </li>

                </ul>

            </div>

        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
