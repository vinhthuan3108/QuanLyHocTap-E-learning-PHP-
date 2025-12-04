<?php
include('layout.php');
include_once('../config/connect.php');

if (session_status() == PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['full_name'])) { echo "<p>Vui lòng đăng nhập để xem khóa học.</p>"; exit; }

$user_id = $_SESSION['user_id'];

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
    $keyword_sql = " AND (LOWER(REPLACE(REPLACE(REPLACE(REPLACE(c.course_name,' ',''),'Đ','D'),'đ','d'),' ','')) LIKE ? OR c.course_name LIKE ?)";
}

// --- TỔNG KHÓA HỌC ---
if ($keyword_sql) {
    $count_stmt = mysqli_prepare($dbconnect, "SELECT COUNT(*) as total FROM course c
        LEFT JOIN course_member cm ON c.course_id = cm.course_id AND cm.student_id = ?
        WHERE cm.member_id IS NULL $keyword_sql");
    $search_term = "%$keyword%";
    $search_term2 = "%$tukhoa%";
    mysqli_stmt_bind_param($count_stmt, "iss", $user_id, $search_term, $search_term2);
    mysqli_stmt_execute($count_stmt);
    $count_result = mysqli_stmt_get_result($count_stmt);
    $total_row = mysqli_fetch_assoc($count_result);
} else {
    $count_result = mysqli_query($dbconnect, "SELECT COUNT(*) as total FROM course c
        LEFT JOIN course_member cm ON c.course_id = cm.course_id AND cm.student_id = $user_id
        WHERE cm.member_id IS NULL");
    $total_row = mysqli_fetch_assoc($count_result);
}

$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);

// --- LẤY KHÓA HỌC ---
if ($keyword_sql) {
    $sql = "SELECT c.course_id, c.course_code, c.course_name, c.status, c.course_background
            FROM course c
            LEFT JOIN course_member cm ON c.course_id = cm.course_id AND cm.student_id = ?
            WHERE cm.member_id IS NULL $keyword_sql
            ORDER BY c.course_name ASC
            LIMIT ?, ?";
    $stmt = mysqli_prepare($dbconnect, $sql);
    mysqli_stmt_bind_param($stmt, "issii", $user_id, $search_term, $search_term2, $start, $limit);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $sql = "SELECT c.course_id, c.course_code, c.course_name, c.status, c.course_background
            FROM course c
            LEFT JOIN course_member cm ON c.course_id = cm.course_id AND cm.student_id = $user_id
            WHERE cm.member_id IS NULL
            ORDER BY c.course_name ASC
            LIMIT $start, $limit";
    $result = mysqli_query($dbconnect, $sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Các khóa học khác</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<style>
    .course-card {
        position: relative;
        overflow: hidden;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: transform .3s, box-shadow .3s;
        background-color: #fff;
    }
    .course-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.2);}
    .course-card img { width: 100%; height: 180px; object-fit: contain; border-top-left-radius:12px; border-top-right-radius:12px; }
    .course-card-body { padding: 15px; }
    .course-title { font-weight: 600; font-size: 1.1rem; margin-bottom: 5px; }
    .course-code { font-size: .9rem; color: #666; }
    .course-status { font-size: .75rem; color: #fff; background: #0d6efd; padding: 2px 6px; border-radius: 6px; margin-left:5px; }
    .pagination { margin-top: 20px; }
</style>
</head>
<body>
<div class="container mt-4">
    <h2>Các khóa học khác</h2>

    <!-- Form tìm kiếm + số hiển thị -->
    <form action="othercourses.php" method="GET" class="d-flex justify-content-end align-items-center gap-2 mb-4 flex-wrap">
        <select name="limit" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
            <?php foreach($per_page_options as $opt): ?>
                <option value="<?= $opt ?>" <?= $limit==$opt?'selected':'' ?>>Hiển thị <?= $opt ?></option>
            <?php endforeach; ?>
        </select>
        <input type="search" class="form-control form-control-sm" placeholder="Tìm kiếm khóa học..." name="tukhoa" value="<?= htmlspecialchars($tukhoa) ?>" style="max-width:250px;">
        <button class="btn btn-primary btn-sm" type="submit" name="timkiem">Tìm kiếm</button>
    </form>

    <?php if ($tukhoa): ?>
        <p class="text-muted mb-3">Kết quả tìm kiếm với từ khóa: "<strong><?= htmlspecialchars($tukhoa) ?></strong>"</p>
    <?php endif; ?>

    <div class="row">
        <?php if(mysqli_num_rows($result) > 0):
            while($row = mysqli_fetch_assoc($result)): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="course-card">
                        <img src="<?= "../assets/file/course_background/".$row['course_background'] ?>" alt="<?= $row['course_name'] ?>">
                        <div class="course-card-body">
                            <div class="course-title"><?= $row['course_name'] ?></div>
                            <div class="course-code">Mã: <?= $row['course_code'] ?>
                                <span class="course-status"><?= ($row['status']=='A')?'Đã duyệt':'Chờ duyệt' ?></span>
                            </div>
                            <a href="details_course.php?course_id=<?= $row['course_id'] ?>" class="btn btn-primary mt-2 w-100">Chi tiết</a>
                        </div>
                    </div>
                </div>
            <?php endwhile;
        else: ?>
            <p>Không có khóa học nào.</p>
        <?php endif; ?>
    </div>

    <!-- Phân trang -->
    <?php if ($total_pages>1): ?>
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
</div>
    <?php include("../footer.php"); ?>

</body>
</html>
