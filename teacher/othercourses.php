<?php
include 'layout.php';
include_once '../config/connect.php';

if (session_status() == PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id'])) {
    echo "<p>Vui lòng đăng nhập để xem khóa học.</p>";
    exit;
}

$user_id = intval($_SESSION['user_id']);

// --- LỰA CHỌN SỐ KHÓA HỌC HIỂN THỊ ---
$per_page_options = array(3,6,9);
$limit = 6;
if (isset($_GET['limit']) && in_array(intval($_GET['limit']), $per_page_options)) {
    $limit = intval($_GET['limit']);
}

$page = 1;
if (isset($_GET['page'])) {
    $page = intval($_GET['page']);
}

$start = ($page - 1) * $limit;

// --- TÌM KIẾM ---
$keyword_sql = "";
$tukhoa = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['timkiem'])) {
    $tukhoa = trim($_POST['tukhoa']);
    $keyword = strtolower(str_replace(' ', '', $tukhoa));
    $keyword_escaped = mysqli_real_escape_string($dbconnect, $keyword);
    $tukhoa_escaped = mysqli_real_escape_string($dbconnect, $tukhoa);
    $keyword_sql = " AND (LOWER(REPLACE(REPLACE(REPLACE(REPLACE(c.course_name, ' ', ''), 'Đ','D'),'đ','d'), ' ', '')) LIKE '%$keyword_escaped%'
                    OR c.course_name LIKE '%$tukhoa_escaped%')";
}

// --- Đếm tổng khóa học ---
$count_sql = "SELECT COUNT(*) as total FROM course c
LEFT JOIN user u ON c.teacher_id = u.user_id
WHERE (u.user_id IS NULL OR u.user_id <> $user_id) $keyword_sql";
$res_count = mysqli_query($dbconnect, $count_sql);
$total_row = mysqli_fetch_assoc($res_count);
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);

// --- Lấy dữ liệu ---
$sql = "SELECT c.*, u.full_name as teacher_name FROM course c
LEFT JOIN user u ON c.teacher_id = u.user_id
WHERE (u.user_id IS NULL OR u.user_id <> $user_id) $keyword_sql
ORDER BY c.course_name ASC
LIMIT $start, $limit";
$result = mysqli_query($dbconnect, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Các khóa học khác</title>
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
            <h3 class="m-0">Các khóa học khác</h3>
        </div>

        <!-- Form tìm kiếm -->
        <div class="col-md-5 mb-2 mb-md-0">
            <form action="othercourses.php" method="POST" class="d-flex gap-2 shadow-sm rounded p-2 bg-white">
                <input type="search" class="form-control" placeholder="Tìm khóa học..." name="tukhoa" value="<?php echo htmlspecialchars($tukhoa); ?>">
                <button class="btn btn-outline-primary" type="submit" name="timkiem">Tìm</button>
            </form>
            <?php if ($tukhoa != ""): ?>
                <small class="text-muted d-block mt-1">Kết quả tìm kiếm: '<strong><?php echo htmlspecialchars($tukhoa); ?></strong>'</small>
            <?php endif; ?>
        </div>

        <!-- Show Limit -->
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
                            <img src="<?php echo "../assets/file/course_background/".$row['course_background']; ?>" class="card-img-top" alt="<?php echo $row['course_name']; ?>">
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $row['course_name']; ?></h5>
                            <p class="card-text">
                                Giáo viên: <?php echo $row['teacher_name'] != "" ? $row['teacher_name'] : "Chưa có"; ?><br>
                                Mã khóa học: <?php echo $row['course_code']; ?><br>
                                Trạng thái: <?php echo ($row['status']=="A")?"Đã duyệt":"Đang chờ duyệt"; ?>
                            </p>
                            <a class="btn btn-primary" href="details_course.php?course_id=<?php echo $row['course_id']; ?>">Chi tiết</a>
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
                        ?><li class="page-item <?php if($i==$page) echo 'active'; ?>"><a class="page-link" href="?page=<?php echo $i; ?>&limit=<?php echo $limit; ?>"><?php echo $i; ?></a></li><?php
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
</body>
</html>
