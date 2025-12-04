<?php
include("layout.php");
include_once("../config/connect.php");

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$student_id = $_SESSION['user_id'];
$sql_edit = "SELECT * FROM user WHERE user_id = $student_id";
$query_update = mysqli_query($dbconnect, $sql_edit);
$row_update = mysqli_fetch_assoc($query_update);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa thông tin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f5f7fa;
        }
        .profile-edit-card {
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            padding: 30px;
            background-color: white;
        }
        .profile-img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid #4e73df;
        }
        .info-title {
            font-weight: 600;
            color: #4e73df;
        }
        .btn-save {
            background-color: #1cc88a;
            border: none;
        }
        .btn-save:hover {
            background-color: #17a673;
        }
        .btn-cancel {
            margin-left: 10px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="profile-edit-card row g-4">
        <!-- Hình đại diện -->
        <div class="col-md-4 text-center">
            <img src="../assets/images/<?php echo $row_update['image']; ?>" class="profile-img mb-3" alt="Avatar">
            <div class="mb-3">
                <label for="portrait" class="form-label"><i class="fa-solid fa-camera"></i> Thay ảnh</label>
                <input type="file" class="form-control" id="portrait" name="image" form="accountForm">
            </div>
        </div>

        <!-- Form thông tin -->
        <div class="col-md-8">
            <h3 class="mb-4 text-primary"><i class="fa-solid fa-user-pen"></i> Chỉnh sửa thông tin cá nhân</h3>
            <form action="process.php" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate id="accountForm">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="fullName" class="form-label info-title">Họ và tên</label>
                        <input type="text" class="form-control" id="fullName" name="full_name" required value="<?php echo $row_update['full_name']; ?>">
                        <div class="invalid-feedback">Họ và tên không được trống.</div>
                    </div>
                    <div class="col-md-6">
                        <label for="idCard" class="form-label info-title">CCCD/CMND</label>
                        <input type="text" class="form-control" id="idCard" name="citizen_id" required value="<?php echo $row_update['citizen_id']; ?>">
                        <div class="invalid-feedback">CCCD/CMND không được trống.</div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="dob" class="form-label info-title">Ngày sinh</label>
                        <input type="date" class="form-control" id="dob" name="date_of_birth" required value="<?php echo $row_update['date_of_birth']; ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="gender" class="form-label info-title">Giới tính</label>
                        <select class="form-select" id="gender" name="gender" required>
                            <option value="" disabled>Chọn giới tính</option>
                            <option value="M" <?php echo ($row_update['gender']=='M')?'selected':''; ?>>Nam</option>
                            <option value="F" <?php echo ($row_update['gender']=='F')?'selected':''; ?>>Nữ</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="phoneNumber" class="form-label info-title">Số điện thoại</label>
                        <input type="tel" class="form-control" id="phoneNumber" name="phone" required value="<?php echo $row_update['phone']; ?>">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="email" class="form-label info-title">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required value="<?php echo $row_update['email']; ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="address" class="form-label info-title">Địa chỉ</label>
                        <input type="text" class="form-control" id="address" name="address" required value="<?php echo $row_update['address']; ?>">
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-save" name="edit_student"><i class="fa-solid fa-floppy-disk"></i> Lưu</button>
                    <a href="my.php?user_id=<?php echo $student_id;?>" class="btn btn-secondary btn-cancel"><i class="fa-solid fa-xmark"></i> Thoát</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Bootstrap form validation
    (function() {
        'use strict'
        var form = document.getElementById('accountForm');
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    })();
</script>

<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->
</body>
</html>
