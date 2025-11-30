<?php
include_once('../layout.php');
include_once('../../../config/connect.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_GET['user_id'])) {
    echo "User not found";
    exit;
}

$user_id = $_GET['user_id'];
$sql = "SELECT * FROM user WHERE user_id = $user_id";
$result = mysqli_query($dbconnect, $sql);
$row = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ sơ học sinh</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f3f5f9;
        }

        .profile-header {
            background: white;
            border-radius: 15px;
            padding: 25px;
            display: flex;
            gap: 20px;
            align-items: center;
            box-shadow: 0 5px 18px rgba(0,0,0,0.08);
        }

        .profile-avatar {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .info-title {
            font-weight: 600;
            color: #0d6efd;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 5px 18px rgba(0,0,0,0.08);
        }

        .profile-label {
            font-weight: 600;
        }

        .course-box {
            background: #eef4ff;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 12px;
            border-left: 4px solid #0d6efd;
        }
    </style>
</head>

<body>

<div class="container mt-4">

    <!-- HEADER -->
    <div class="profile-header">
        <img src="../../../assets/images/course1.jpg" class="profile-avatar" alt="avatar">

        <div>
            <h2 class="mb-1"><?php echo $row['full_name']; ?></h2>
            <h5 class="text-secondary">Học sinh</h5>
        </div>
    </div>

    <!-- BODY -->
    <div class="row mt-4">

        <!-- Thông tin -->
        <div class="col-md-6">
            <div class="card p-4">
                <h4 class="info-title">📌 Thông tin cá nhân</h4>
                <hr>

                <p><span class="profile-label">Ngày sinh:</span><br>
                    <?php echo date('d/m/Y', strtotime($row['date_of_birth'])); ?></p>

                <p><span class="profile-label">Giới tính:</span><br>
                    <?php echo ($row['gender'] == "M") ? "Nam" : "Nữ"; ?></p>

                <p><span class="profile-label">Email:</span><br>
                    <?php echo $row['email']; ?></p>

                <p><span class="profile-label">Số điện thoại:</span><br>
                    <?php echo $row['phone']; ?></p>

                <p><span class="profile-label">CCCD/CMND:</span><br>
                    <?php echo $row['citizen_id']; ?></p>
            </div>
        </div>

        <!-- Khóa học -->
        <div class="col-md-6">
            <div class="card p-4">
                <h4 class="info-title">📘 Khóa học đang tham gia</h4>
                <hr>

                <?php
                $sql = "SELECT * FROM course co
                        INNER JOIN course_member cm ON co.course_id = cm.course_id
                        WHERE student_id = $user_id";
                $course_result = mysqli_query($dbconnect, $sql);

                if (mysqli_num_rows($course_result) > 0) {
                    while ($c = mysqli_fetch_assoc($course_result)) {
                        echo "
                            <div class='course-box'>
                                <strong>{$c['course_code']} – {$c['course_name']}</strong>
                            </div>
                        ";
                    }
                } else {
                    echo "<p class='text-muted'>Học sinh chưa tham gia khóa học nào.</p>";
                }
                ?>
            </div>
        </div>

    </div>
</div>

<?php include("../../../footer.php"); ?>

</body>
</html>
