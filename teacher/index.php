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
    $username_now = "User not logged in";
    $row = ['full_name' => 'Guest'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ giáo viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .course-card {
            height: 100%;
            display: flex;
            flex-direction: column;
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .course-card:hover {
            transform: translateY(-5px);
        }
        .course-card img {
            height: 180px;
            object-fit: cover;
        }
        .card-body {
            flex: 1;
        }
    </style>
</head>
<body>
<div class="container my-4">
    <header class="mb-5">
        <h3>Xin chào, <?php echo $row['full_name']; ?></h3>
    </header>

    <h5>Khóa học hôm nay</h5>
    <?php
    // Lấy ngày trong tuần: Chủ nhật = 1, Thứ hai = 2, … Thứ bảy = 7
    $phpDay = date("N"); // 1 = Thứ Hai, … 7 = Chủ nhật
    $dayOfWeekNumber = ($phpDay % 7) + 1; // Chuyển 7 → 1, 1→2,...6→7

    $sql = "SELECT co.*, cs.day_of_week, cs.start_time, cs.end_time
            FROM course co
            INNER JOIN course_schedule cs ON co.course_id = cs.course_id
            WHERE cs.day_of_week = $dayOfWeekNumber AND co.teacher_id = $user_id";
    $result_courses = mysqli_query($dbconnect, $sql);
    $num_courses = mysqli_num_rows($result_courses);

    if ($num_courses > 0): ?>
        <div class="row mt-3">
            <?php while ($course = mysqli_fetch_assoc($result_courses)): ?>
                <div class="col-md-4 mb-4">
                    <div class="card course-card">
                        <img src="<?php echo "../assets/file/course_background/" . $course['course_background']; ?>" class="card-img-top" alt="Course Image">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo $course['course_name']; ?></h5>
                            <p class="card-text mb-3">
                                Mã khóa học: <?php echo $course['course_code']; ?><br>
                                Thời gian: <?php echo $course['start_time'] . ' - ' . $course['end_time']; ?>
                            </p>
                            <a href="course/index.php?id=<?php echo $course['course_id']; ?>" class="btn btn-primary mt-auto">Truy cập</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p class="mt-3">Không có khóa học nào trong hôm nay.</p>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include("../footer.php"); ?>
</body>
</html>
