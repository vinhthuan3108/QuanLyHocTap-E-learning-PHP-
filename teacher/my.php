<?php
include_once('layout.php');
include_once('../config/connect.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM user WHERE user_id = $user_id";
$result = mysqli_query($dbconnect, $sql);
$row = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trang cá nhân giáo viên</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f5f7fa;
        }

        .profile-header {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .profile-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 5px solid #fff;
            object-fit: cover;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }

        .info-card {
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            background: white;
        }

        .info-title {
            font-weight: 600;
            color: #0d6efd;
        }

        .profile-label {
            font-weight: 600;
        }

        .edit-btn {
            border-radius: 10px;
            font-weight: 500;
        }
    </style>
</head>

<body>

<div class="container mt-4">

    <!-- Header -->
    <div class="profile-header d-flex align-items-center gap-4">
        <img src="../assets/images/<?php echo $row['image']; ?>" class="profile-avatar">

        <div>
            <h2 class="mb-1"><?php echo $row['full_name']; ?></h2>
            <h5 class="text-secondary">Giáo viên Elearning</h5>

            <a href="edit_teacher_profile.php" class="btn btn-primary mt-2 edit-btn">
                ✏️ Thay đổi thông tin
            </a>
        </div>
    </div>

    <!-- Body -->
    <div class="row mt-4">

        <!-- Thông tin cá nhân -->
        <div class="col-md-6">
            <div class="info-card p-4">
                <h4 class="info-title">📌 Thông tin cá nhân</h4>
                <hr>

                <p><span class="profile-label">Ngày sinh:</span><br>
                    <?php echo date('d/m/Y', strtotime($row['date_of_birth'])); ?></p>

                <p><span class="profile-label">Giới tính:</span><br>
                    <?php echo ($row['gender'] == "M") ? "Nam" : "Nữ"; ?>
                </p>

                <p><span class="profile-label">Email:</span><br>
                    <?php echo $row['email']; ?></p>

                <p><span class="profile-label">Số điện thoại:</span><br>
                    <?php echo $row['phone']; ?></p>

                <p><span class="profile-label">CCCD/CMND:</span><br>
                    <?php echo $row['citizen_id']; ?></p>
            </div>
        </div>

        <!-- Danh sách khóa học -->
        <div class="col-md-6">
            <div class="info-card p-4">
                <h4 class="info-title">📘 Các khóa học đang giảng dạy</h4>
                <hr>

                <?php
                $sqlCourse = "SELECT * FROM course WHERE teacher_id = $user_id";
                $courseResult = mysqli_query($dbconnect, $sqlCourse);

                if (mysqli_num_rows($courseResult) > 0) {
                    while ($c = mysqli_fetch_assoc($courseResult)) {
                        echo "
                            <div class='mb-3 p-3 border rounded bg-light'>
                                <strong>{$c['course_code']} – {$c['course_name']}</strong>
                            </div>
                        ";
                    }
                } else {
                    echo "<p class='text-muted'>Không có khóa học nào.</p>";
                }
                ?>

            </div>
        </div>
    </div>

</div>

<?php include("../footer.php"); ?>

</body>
</html>
