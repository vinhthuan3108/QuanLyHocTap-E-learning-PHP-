<?php
include("layout.php");
include_once("../config/connect.php");
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$role_id = $_GET['role_id'];
$role_name = $_GET['role_name'];

switch($role_id){
    case 1: $role_fullName = "Học Sinh"; break;
    case 2: $role_fullName = "Giáo Viên"; break;
    case 3: $role_fullName = "Quản Trị Viên"; break;
    default: $role_fullName = "Người dùng"; break;
}

if (isset($_GET['user_id'])){
    $user_id = $_GET['user_id'];
    $sql = "SELECT * FROM user us
            INNER JOIN user_role ur ON us.user_id = ur.user_id
            INNER JOIN user_account ua ON ua.user_id = us.user_id
            INNER JOIN role r ON r.role_id = ur.role_id
            WHERE us.user_id=$user_id";
    $result = mysqli_query($dbconnect, $sql);
    $row = mysqli_fetch_assoc($result);
} else {
    $row = null;
}

mysqli_close($dbconnect);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin cá nhân</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f5f6fa;
            font-family: 'Segoe UI', sans-serif;
        }

        .profile-card {
            background-color: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
        }

        .profile-image {
            border-radius: 50%;
            width: 150px;
            height: 150px;
            object-fit: cover;
            border: 4px solid #0d6efd;
        }

        .info-divider {
            border-top: 2px solid #dee2e6;
            margin: 10px 0;
        }

        .info-label {
            font-weight: 500;
            color: #495057;
        }

        .info-value {
            color: #212529;
        }

        .btn-profile {
            min-width: 140px;
        }

        .card-header {
            background-color: #0d6efd;
            color: #fff;
            font-weight: 600;
        }

        .icon-text i {
            margin-right: 6px;
            color: #0d6efd;
        }
    </style>
</head>

<body>
    <?php include "sidebar.php"; ?>

<div class="main p-4" id="mainContent">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 profile-card">
                <div class="row align-items-center">
                    <div class="col-md-3 text-center">
                        <img src="../assets/images/<?php echo $row['image'];?>" alt="Ảnh đại diện" class="profile-image mb-3">
                        <a href="account_edit.php?user_id=<?php echo $row['user_id']; ?>&role_id=<?php echo $role_id;?>&role_name=<?php echo $role_name;?>" class="btn btn-primary btn-profile mb-2">
                            <i class="bi bi-pencil-square"></i> Chỉnh sửa
                        </a>
                        <a href="<?php echo $role_name;?>.php" class="btn btn-secondary btn-profile">
                            <i class="bi bi-box-arrow-left"></i> Thoát
                        </a>
                    </div>
                    <div class="col-md-9">
                        <h2><?php echo $row['full_name']; ?></h2>
                        <h5 class="text-muted mb-3"><?php echo $role_fullName; ?></h5>

                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <div class="icon-text">
                                    <i class="bi bi-calendar3"></i>
                                    <span class="info-label">Ngày sinh:</span>
                                    <span class="info-value"><?php echo date('d/m/Y', strtotime($row['date_of_birth'])); ?></span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="icon-text">
                                    <i class="bi bi-gender-ambiguous"></i>
                                    <span class="info-label">Giới tính:</span>
                                    <span class="info-value"><?php echo ($row['gender'] == 'M' ? 'Nam' : 'Nữ'); ?></span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="icon-text">
                                    <i class="bi bi-envelope"></i>
                                    <span class="info-label">Email:</span>
                                    <span class="info-value"><?php echo $row['email']; ?></span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="icon-text">
                                    <i class="bi bi-telephone"></i>
                                    <span class="info-label">SĐT:</span>
                                    <span class="info-value"><?php echo $row['phone']; ?></span>
                                </div>
                            </div>
                            <div class="col-md-12 mb-2">
                                <div class="icon-text">
                                    <i class="bi bi-geo-alt"></i>
                                    <span class="info-label">Địa chỉ:</span>
                                    <span class="info-value"><?php echo $row['address']; ?></span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="icon-text">
                                    <i class="bi bi-credit-card"></i>
                                    <span class="info-label">CCCD/CMND:</span>
                                    <span class="info-value"><?php echo $row['citizen_id']; ?></span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="icon-text">
                                    <i class="bi bi-person-badge"></i>
                                    <span class="info-label">Tài khoản:</span>
                                    <span class="info-value"><?php echo $row['username']; ?></span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="icon-text">
                                    <i class="bi bi-lock-fill"></i>
                                    <span class="info-label">Mật khẩu:</span>
                                    <span class="info-value">********</span> <!-- Không hiển thị mật khẩu thực -->
                                </div>
                            </div>
                        </div> <!-- /row info -->
                    </div> <!-- /col info -->
                </div> <!-- /row profile -->
            </div> <!-- /profile-card -->
        </div> <!-- /row justify-content-center -->
    </div> <!-- /container -->
        <?php include("../footer.php"); ?>

</div> <!-- /mainContent -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
