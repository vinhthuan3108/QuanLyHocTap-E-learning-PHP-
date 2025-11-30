<?php
include_once('layout.php');
include_once('../config/connect.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT * FROM user WHERE user_id = $user_id";
    $result = mysqli_query($dbconnect, $sql);
    $row = mysqli_fetch_assoc($result);
} else {
    $row = [
        'full_name' => 'User not logged in',
        'image' => 'default_avatar.png',
        'date_of_birth' => '',
        'gender' => '',
        'email' => '',
        'phone' => '',
        'citizen_id' => ''
    ];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Trang cá nhân - <?php echo $row['full_name']; ?></title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<style>
    body {
        background-color: #f2f4f8;
        font-family: 'Segoe UI', sans-serif;
    }

    .profile-header {
        text-align: center;
        padding: 30px 0 20px 0;
        background-color: #fff;
        border-radius: 12px;
        margin-bottom: 20px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .profile-avatar {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        margin-bottom: 15px;
    }

    .profile-name {
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .profile-role {
        font-size: 1rem;
        color: #6c757d;
        margin-bottom: 15px;
    }

    .btn-edit {
        font-size: 0.9rem;
        padding: 8px 20px;
    }

    .card {
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }

    .card h4 {
        font-weight: 600;
        margin-bottom: 15px;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 6px 0;
        border-bottom: 1px solid #e9ecef;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 500;
        color: #495057;
    }

    .info-value {
        color: #6c757d;
    }

    @media (max-width: 576px) {
        .profile-header { padding: 20px 10px; }
        .profile-name { font-size: 1.5rem; }
        .profile-avatar { width: 120px; height: 120px; }
    }
</style>
</head>
<body>
<?php include("sidebar.php"); ?>

<div class="main p-4" id="mainContent">

<div class="container mt-4">
    <!-- Header Avatar -->
    <div class="profile-header">
        <img src="../assets/images/<?php echo $row['image'];?>" alt="Avatar" class="profile-avatar">
        <div class="profile-name"><?php echo $row['full_name'];?></div>
        <div class="profile-role">Quản trị viên</div>
        <a href="account_edit.php?user_id=<?php echo $user_id;?>&role_id=3&role_name=admin" class="btn btn-primary btn-edit">
            <i class="bi bi-pencil-square"></i> Chỉnh sửa thông tin
        </a>
    </div>

    <!-- Thông tin cá nhân -->
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-3">
                <h4>Thông tin cá nhân</h4>
                <div class="info-row">
                    <div class="info-label">Ngày sinh:</div>
                    <div class="info-value"><?php echo !empty($row['date_of_birth']) ? date('d/m/Y', strtotime($row['date_of_birth'])) : 'Chưa cập nhật'; ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Giới tính:</div>
                    <div class="info-value"><?php echo ($row['gender'] == "M" ? "Nam" : ($row['gender'] == "F" ? "Nữ" : 'Chưa cập nhật')); ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Email:</div>
                    <div class="info-value"><?php echo !empty($row['email']) ? $row['email'] : 'Chưa cập nhật'; ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Số điện thoại:</div>
                    <div class="info-value"><?php echo !empty($row['phone']) ? $row['phone'] : 'Chưa cập nhật'; ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">CCCD/CMND:</div>
                    <div class="info-value"><?php echo !empty($row['citizen_id']) ? $row['citizen_id'] : 'Chưa cập nhật'; ?></div>
                </div>
            </div>
        </div>
    </div>
</div>
    <?php include("../footer.php"); ?>

</div>
</body>
</html>
