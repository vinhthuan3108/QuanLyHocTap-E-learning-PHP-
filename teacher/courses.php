<?php
include('layout.php');
include_once('../config/connect.php');

if (session_status() == PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<p>Vui lòng đăng nhập để xem khóa học.</p>";
    exit;
}

$user_id = intval($_SESSION['user_id']);

// --- LỰA CHỌN SỐ KHÓA HỌC HIỂN THỊ ---
$per_page_options = [3,6,9];
$limit = isset($_GET['limit']) && in_array(intval($_GET['limit']), $per_page_options) ? intval($_GET['limit']) : 6;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start = ($page - 1) * $limit;

// --- TÌM KIẾM ---
$keyword_sql = "";
$tukhoa = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['timkiem'])) {
    $tukhoa = trim($_POST['tukhoa']);
    $keyword = strtolower(str_replace(' ', '', $tukhoa));
    // Escape để tránh SQL Injection
    $keyword_escaped = mysqli_real_escape_string($dbconnect, $keyword);
    $tukhoa_escaped = mysqli_real_escape_string($dbconnect, $tukhoa);
    $keyword_sql = " AND (LOWER(REPLACE(REPLACE(REPLACE(REPLACE(course_name, ' ', ''), 'Đ','D'),'đ','d'), ' ', '')) LIKE '%$keyword_escaped%'
                    OR course_name LIKE '%$tukhoa_escaped%')";
}

// --- Đếm tổng khóa học ---
$count_sql = "SELECT COUNT(*) as total FROM course WHERE teacher_id = $user_id $keyword_sql";
$res_count = mysqli_query($dbconnect, $count_sql);
$total_row = mysqli_fetch_assoc($res_count);
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);

// --- Lấy dữ liệu ---
$sql = "SELECT * FROM course WHERE teacher_id = $user_id $keyword_sql ORDER BY course_name ASC LIMIT $start, $limit";
$result = mysqli_query($dbconnect, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Khóa học của tôi</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<style>
.custom-card { width:100%; height:0; padding-top:50%; position:relative; }
.custom-card img { position:absolute; top:0; left:0; width:100%; height:100%; object-fit:cover; }
</style>
</head>
<body>
<div class="container mt-4">
    <div class="row align-items-center mb-4">
    <!-- Tiêu đề -->
    <div class="col-md-4 mb-2 mb-md-0">
        <h3 class="m-0">Khóa học của tôi</h3>
    </div>

    <!-- Form tìm kiếm -->
    <div class="col-md-5 mb-2 mb-md-0">
        <form action="courses.php" method="POST" class="d-flex gap-2 shadow-sm rounded p-2 bg-white">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="search" class="form-control border-start-0 rounded-end" placeholder="Tìm khóa học..." name="tukhoa" value="<?= htmlspecialchars($tukhoa) ?>">
            <button class="btn btn-outline-primary" type="submit" name="timkiem">Tìm</button>
        </form>
        <?php if ($tukhoa): ?>
            <small class="text-muted d-block mt-1">Kết quả tìm kiếm: '<strong><?= htmlspecialchars($tukhoa) ?></strong>'</small>
        <?php endif; ?>
    </div>

    <!-- Limit + Thêm khóa học -->
    <div class="col-md-3 d-flex justify-content-md-end align-items-center gap-2">
        <!-- Show Limit -->
        <div class="d-flex align-items-center gap-2">
        </div>
        <a href="create_course.php" class="btn btn-primary">Thêm khóa học mới</a>
    </div>
</div>

    <?php if ($total_records > 0): ?>
       <div class="row">
        <div class="col-md-8 mb-2 mb-md-3">
            <p>Showing <?php echo ($start+1); ?> to <?php echo min($start+$limit, $total_records); ?> of <?php echo $total_records; ?> courses</p>
        </div>
          <div class="col-md-4 d-flex align-items-center justify-content-md-end gap-2 mb-md-3">
        <div class="col-md-5 mb-2 mb-md-0"></div>
            <span>Hiển thị:</span>
            <form method="GET" class="d-inline-block">
                <select name="limit" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                    <?php foreach($per_page_options as $opt): ?>
                        <option value="<?php echo $opt; ?>" <?php if($limit==$opt) echo "selected"; ?>><?php echo $opt; ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
      </div>
        <div class="row">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="custom-card">
                            <img src="../assets/file/course_background/<?= $row['course_background'] ?>" class="card-img-top" alt="<?= $row['course_name'] ?>">
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?= $row['course_name'] ?></h5>
                            <p class="card-text">
                                Mã khóa học: <?= $row['course_code'] ?><br>
                                Trạng thái: <?= ($row['status']=="A")?"Đã duyệt":"Đang chờ duyệt" ?>
                            </p>
                            <a class="btn btn-primary" href="course/index.php?id=<?= $row['course_id'] ?>">Truy cập</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Phân trang -->
        <?php if ($total_pages > 1): ?>
            <nav>
                <ul class="pagination justify-content-center">
                    <?php
                    $max_pages = 5;
                    $start_page = max(1, $page-2);
                    $end_page = min($total_pages, $start_page+$max_pages-1);
                    if($start_page>1) echo '<li class="page-item"><a class="page-link" href="?page=1&limit='.$limit.'"><<</a></li>';
                    if($page>1) echo '<li class="page-item"><a class="page-link" href="?page='.($page-1).'&limit='.$limit.'"><</a></li>';
                    for($i=$start_page;$i<=$end_page;$i++):
                        ?><li class="page-item <?= $i==$page?'active':'' ?>"><a class="page-link" href="?page=<?= $i ?>&limit=<?= $limit ?>"><?= $i ?></a></li><?php
                    endfor;
                    if($page<$total_pages) echo '<li class="page-item"><a class="page-link" href="?page='.($page+1).'&limit='.$limit.'">></a></li>';
                    if($end_page<$total_pages) echo '<li class="page-item"><a class="page-link" href="?page='.$total_pages.'&limit='.$limit.'">>></a></li>';
                    ?>
                </ul>
            </nav>
        <?php endif; ?>
    <?php else: ?>
        <p>Không có khóa học nào.</p>
    <?php endif; ?>
</div>
<?php include("../footer.php"); ?>

<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->
</body>
</html>
