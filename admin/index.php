<?php
include_once('../config/connect.php');
include("layout.php");

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';
if ($user_id == '') {
    header("Location: login.php");
    exit();
}

// Tổng quan
$total_students = 0;
$total_courses = 0;
$total_teachers = 0;
$total_contents = 0;

$result = $dbconnect->query("SELECT COUNT(*) AS total FROM user_role WHERE role_id = 1");
if ($result) {
    $row = $result->fetch_assoc();
    $total_students = isset($row['total']) ? $row['total'] : 0;
}

$result = $dbconnect->query("SELECT COUNT(*) AS total FROM user_role WHERE role_id = 2");
if ($result) {
    $row = $result->fetch_assoc();
    $total_teachers = isset($row['total']) ? $row['total'] : 0;
}

$result = $dbconnect->query("SELECT COUNT(*) AS total FROM course");
if ($result) {
    $row = $result->fetch_assoc();
    $total_courses = isset($row['total']) ? $row['total'] : 0;
}

$result = $dbconnect->query("SELECT COUNT(*) AS total FROM course_contents");
if ($result) {
    $row = $result->fetch_assoc();
    $total_contents = isset($row['total']) ? $row['total'] : 0;
}

// 5 khóa học mới nhất
$latest_courses = $dbconnect->query("SELECT * FROM course ORDER BY course_id DESC LIMIT 5");

// 5 thanh toán gần đây
$latest_payments = $dbconnect->query("SELECT p.*, u.full_name, c.course_name
    FROM payments p
    LEFT JOIN user u ON u.user_id = p.user_id
    LEFT JOIN course c ON c.course_id = p.course_id
    ORDER BY p.payment_id DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>

.dashboard-card { border-radius: 10px; padding: 20px; color: #fff; transition: transform 0.2s; }
.dashboard-card:hover { transform: scale(1.05); }
.card-student { background-color: #0d6efd; }
.card-teacher { background-color: #198754; }
.card-course { background-color: #ffc107; color: #000; }
.card-content { background-color: #dc3545; }
.table thead { background-color: #343a40; color: #fff; }
.table-hover tbody tr:hover { background-color: #f1f1f1; }
.card-icon { font-size: 30px; margin-bottom: 10px; }

</style>
</head>
<body>

<?php include "sidebar.php";?>

<i class="fa-solid fa-bars toggle-btn" id="toggleBtn"></i>

<div class="main" id="mainContent">
    <h1 class="mb-4">Dashboard Admin</h1>

    <div class="row text-center mb-4">
        <div class="col-md-3">
            <div class="dashboard-card card-student">
                <i class="fa-solid fa-user-graduate card-icon"></i>
                <h5>Tổng học viên</h5>
                <p class="fs-4"><?php echo $total_students; ?></p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card card-teacher">
                <i class="fa-solid fa-chalkboard-teacher card-icon"></i>
                <h5>Tổng giáo viên</h5>
                <p class="fs-4"><?php echo $total_teachers; ?></p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card card-course">
                <i class="fa-solid fa-book card-icon"></i>
                <h5>Tổng khóa học</h5>
                <p class="fs-4"><?php echo $total_courses; ?></p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card card-content">
                <i class="fa-solid fa-file-lines card-icon"></i>
                <h5>Tổng bài học</h5>
                <p class="fs-4"><?php echo $total_contents; ?></p>
            </div>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-md-6">
            <h5>Khóa học mới nhất</h5>
            <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr><th>ID</th><th>Tên khóa học</th><th>Giáo viên</th></tr>
                </thead>
                <tbody>
                    <?php
                    if ($latest_courses) {
                        while ($course = $latest_courses->fetch_assoc()) {
                            $teacher_name = '';
                            $teacher_id = 0;
                            if (isset($course['teacher_id'])) $teacher_id = $course['teacher_id'];
                            $t_result = $dbconnect->query("SELECT full_name FROM user WHERE user_id = $teacher_id");
                            if ($t_result) {
                                $t_row = $t_result->fetch_assoc();
                                if (isset($t_row['full_name'])) $teacher_name = $t_row['full_name'];
                            }
                            echo "<tr><td>{$course['course_id']}</td><td>{$course['course_name']}</td><td>{$teacher_name}</td></tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
            </div>
        </div>
        <div class="col-md-6">
            <h5>Thanh toán gần đây</h5>
            <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr><th>ID</th><th>Người thanh toán</th><th>Khóa học</th><th>Số tiền</th></tr>
                </thead>
                <tbody>
                    <?php
                    if ($latest_payments) {
                        while ($payment = $latest_payments->fetch_assoc()) {
                            $payment_id = isset($payment['payment_id']) ? $payment['payment_id'] : '';
                            $full_name = isset($payment['full_name']) ? $payment['full_name'] : '';
                            $course_name = isset($payment['course_name']) ? $payment['course_name'] : '';
                            $amount = isset($payment['amount']) ? $payment['amount'] : '';
                            echo "<tr><td>$payment_id</td><td>$full_name</td><td>$course_name</td><td>$amount</td></tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>

    <div class="row mb-5">
    <div class="col-md-4">
        <h5>Tổng quan số lượng</h5>
        <canvas id="barChart"></canvas>
    </div>
    <div class="col-md-4">
        <h5>Tỷ lệ học viên / giáo viên</h5>
        <canvas id="doughnutChart"></canvas>
    </div>
    <div class="col-md-4">
        <h5>Khóa học đã duyệt / chờ duyệt</h5>
        <canvas id="pieChart"></canvas>
    </div>
        <?php include("../footer.php"); ?>

</div>

<script>
// Bar Chart - Tổng số lượng
const barCtx = document.getElementById('barChart').getContext('2d');
const barChart = new Chart(barCtx, {
    type: 'bar',
    data: {
        labels: ['Học viên', 'Giáo viên', 'Khóa học', 'Bài học'],
        datasets: [{
            label: 'Số lượng',
            data: [<?php echo $total_students; ?>, <?php echo $total_teachers; ?>, <?php echo $total_courses; ?>, <?php echo $total_contents; ?>],
            backgroundColor: ['#0d6efd','#198754','#ffc107','#dc3545'],
            borderRadius: 5
        }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});

// Doughnut Chart - Học viên / Giáo viên
const doughnutCtx = document.getElementById('doughnutChart').getContext('2d');
const doughnutChart = new Chart(doughnutCtx, {
    type: 'doughnut',
    data: {
        labels: ['Học viên', 'Giáo viên'],
        datasets: [{
            data: [<?php echo $total_students; ?>, <?php echo $total_teachers; ?>],
            backgroundColor: ['#0d6efd','#198754']
        }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});

// Pie Chart - Khóa học đã duyệt / chưa duyệt
<?php
$approved = $dbconnect->query("SELECT COUNT(*) AS total FROM course WHERE status='A'")->fetch_assoc()['total'];
$pending  = $dbconnect->query("SELECT COUNT(*) AS total FROM course WHERE status='N'")->fetch_assoc()['total'];
?>
const pieCtx = document.getElementById('pieChart').getContext('2d');
const pieChart = new Chart(pieCtx, {
    type: 'pie',
    data: {
        labels: ['Đã duyệt', 'Chưa duyệt'],
        datasets: [{
            data: [<?php echo $approved; ?>, <?php echo $pending; ?>],
            backgroundColor: ['#198754','#ffc107']
        }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});
</script>

</body>
</html>
