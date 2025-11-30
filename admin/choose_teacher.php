<?php
include "layout.php";
include_once "../config/connect.php";
if (session_status() == PHP_SESSION_NONE) session_start();

$role = isset($_GET['role']) ? $_GET['role'] : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$teacher_id = isset($_GET['teacher_id']) ? intval($_GET['teacher_id']) : 0;

// Tìm kiếm giáo viên
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['timkiem'])) {
    $tukhoa = trim($_POST['tukhoa']);
    $keyword = strtolower(str_replace(' ', '', $tukhoa));
    $sql_course = "SELECT * FROM user us
        INNER JOIN user_role ur ON us.user_id = ur.user_id
        WHERE ur.role_id = 2 AND
        (LOWER(REPLACE(REPLACE(REPLACE(us.full_name, ' ', ''), 'Đ','D'), ' ', '')) LIKE '%$keyword%' OR us.full_name LIKE '%$tukhoa%')";
    $result = mysqli_query($dbconnect, $sql_course);
} else {
    $sql_course = "SELECT * FROM user us
        INNER JOIN user_role ur ON us.user_id = ur.user_id
        WHERE ur.role_id = 2";
    $result = mysqli_query($dbconnect, $sql_course);
}

// Chọn giáo viên
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["sbm"])) {
    $_SESSION['teacher_course'] = $_POST['sbm'];
    if ($role == 'add') {
        header("location: schedule_add.php");
    } else {
        header("location: schedule_edit.php?id=$id");
    }
    exit;
}

mysqli_close($dbconnect);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Chọn giáo viên phụ trách khóa học</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
    body { background-color: #f8f9fa; }
    .teacher-card {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    background-color: #fff;
    text-align: center;
    transition: transform 0.3s, box-shadow 0.3s;
    padding-bottom: 10px;
    display: flex;
    flex-direction: column;
    align-items: center; /* căn giữa tất cả nội dung */
}
.teacher-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 15px rgba(0,0,0,0.15);
}
.teacher-avatar {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 50%;
    margin-top: 15px;
    border: 2px solid #fff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
}
.card-body {
    display: flex;
    flex-direction: column;
    align-items: center; /* avatar, tên, email, nút đều chính giữa */
    padding: 10px;
}
.teacher-name { font-size: 1rem; font-weight: 500; margin-top: 8px; text-align: center; }
.teacher-info { font-size: 0.8rem; color: #666; margin-bottom: 5px; text-align: center; }
.choose-btn { width: 80%; font-size: 0.85rem; padding: 5px; }

</style>

</head>
<body>
<?php include "sidebar.php"; ?>

<div class="main p-4" id="mainContent">
<div class="container">
    <!-- Tiêu đề trang -->
    <div class="text-center mb-4">
        <h1 class="display-5 fw-bold">Chọn giáo viên phụ trách khóa học</h1>
        <p class="text-muted">Bạn có thể tìm kiếm hoặc chọn trực tiếp giáo viên phù hợp cho khóa học.</p>
    </div>

    <!-- Search bar & Quay lại -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="<?php echo ($role == 'add') ? "course_add.php" : "course_edit.php?id=$id&teacher_id=$teacher_id" ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left-circle"></i> Quay lại
        </a>
        <form class="form-inline d-flex search-bar" action="" method="POST">
            <input class="form-control mr-2 w-75" type="search" name="tukhoa" placeholder="Tìm kiếm giáo viên..." value="<?php echo isset($_POST['tukhoa']) ? $_POST['tukhoa'] : ''; ?>">
            <button class="btn btn-primary" type="submit" name="timkiem">Tìm</button>
        </form>
    </div>

    <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['timkiem'])): ?>
        <p>Kết quả tìm kiếm với từ khóa: "<strong><?php echo htmlspecialchars($_POST['tukhoa']); ?></strong>"</p>
    <?php endif; ?>

    <!-- Danh sách giáo viên -->
    <form method="post">
        <div class="row">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="col-12 col-sm-6 col-md-3 mb-4">
                    <div class="card teacher-card">
                        <img class="teacher-avatar" src="<?php echo "../assets/images/" . $row['image']; ?>" alt="<?php echo $row['full_name']; ?>">
                        <div class="card-body">
                            <div class="teacher-name"><?php echo $row['full_name']; ?></div>
                            <div class="teacher-info"><?php echo $row['email']; ?></div>
                            <button class="btn btn-success choose-btn" type="submit" name="sbm" value="<?php echo $row['user_id']; ?>">
                                Chọn
                            </button>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </form>
</div>
    <?php include("../footer.php"); ?>

</div>
</body>
</html>
