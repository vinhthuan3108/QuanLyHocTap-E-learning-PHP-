<?php
include("layout.php");
include_once('../config/connect.php');
if (session_status() == PHP_SESSION_NONE) { session_start(); }

// Limit và phân trang
$limit_options = [3, 6,9, 12];
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 3;
if(!in_array($limit, $limit_options)) $limit = 3;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Tìm kiếm
$tukhoa = isset($_GET['tukhoa']) ? trim($_GET['tukhoa']) : '';
$keyword_sql = '';
if($tukhoa != '') {
    $keyword_sql = " AND (LOWER(REPLACE(c.course_name,' ','')) LIKE '%".strtolower(str_replace(' ','',$tukhoa))."%'
                     OR LOWER(c.course_name) LIKE '%".strtolower($tukhoa)."%')";
}

// Tổng số khóa học chưa duyệt
$total_result = $dbconnect->query("SELECT COUNT(*) AS total
    FROM course c
    INNER JOIN user us ON c.teacher_id = us.user_id
    WHERE c.status='N' $keyword_sql");
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = max(1, ceil($total_records / $limit));

// Lấy danh sách khóa học
$sql_course = "SELECT c.*, us.full_name as teacher_name
    FROM course c
    INNER JOIN user us ON c.teacher_id = us.user_id
    WHERE c.status='N' $keyword_sql
    ORDER BY c.course_id DESC
    LIMIT $limit OFFSET $offset";
$result_course = $dbconnect->query($sql_course);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Quản lý khóa học - Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
.card-img-top { height: 180px; object-fit: cover; }
.table-hover tbody tr:hover { background-color: #f1f1f1; }
</style>
</head>
<body>
<?php include "sidebar.php"; ?>

<div class="main p-4" id="mainContent">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <h3>Khóa học chưa duyệt</h3>
        <a class="btn btn-primary mb-2" href="course_add.php">Thêm khóa học mới</a>
    </div>

    <!-- Form tìm kiếm + limit -->
    <form class="row g-2 align-items-center mb-4" action="courses.php" method="GET">
        <div class="col-auto">
            <select name="limit" class="form-select" onchange="this.form.submit()">
                <?php foreach($limit_options as $opt): ?>
                    <option value="<?= $opt ?>" <?= $limit==$opt?'selected':'' ?>><?= $opt ?> dòng</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-auto flex-grow-1">
            <input type="search" class="form-control" placeholder="Tìm kiếm theo tên khóa học" name="tukhoa" value="<?= htmlspecialchars($tukhoa) ?>">
        </div>
        <div class="col-auto">
            <button class="btn btn-outline-primary" type="submit">Tìm</button>
        </div>
    </form>

    <?php if($total_records==0): ?>
        <div class="alert alert-warning text-center">
            Không tìm thấy khóa học <?= $tukhoa ? "với từ khóa '<strong>".htmlspecialchars($tukhoa)."</strong>'" : "" ?>.
        </div>
    <?php else: ?>
    <div class="row">
        <?php while($row = $result_course->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img src="../assets/file/course_background/<?= $row['course_background'] ?>" class="card-img-top" alt="<?= htmlspecialchars($row['course_name']) ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($row['course_name']) ?></h5>
                        <p class="card-text">
                            Giáo viên: <?= htmlspecialchars($row['teacher_name']) ?><br>
                            Mã khóa học: <?= $row['course_code'] ?><br>
                            Trạng thái: <?= ($row['status']=='A')?'Đã duyệt':'Đang chờ duyệt' ?>
                        </p>
                        <button type="button" class="btn btn-success btn-sm mb-1" data-bs-toggle="modal" data-bs-target="#approveModal" data-id="<?= $row['course_id'] ?>">Duyệt</button>
                        <button type="button" class="btn btn-danger btn-sm mb-1" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?= $row['course_id'] ?>">Không duyệt</button>
                        <a href="course_show.php?id=<?= $row['course_id'] ?>&teacher_id=<?= $row['teacher_id'] ?>" class="btn btn-info btn-sm mb-1">Chi tiết</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Phân trang -->
    <?php
    $max_links = 5;
    $start = max(1, $page-2);
    $end = min($total_pages, $start+$max_links-1);
    $start = max(1, $end-$max_links+1);
    ?>
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <li class="page-item <?= $page<=1?'disabled':'' ?>"><a class="page-link" href="?page=<?= $page-1 ?>&limit=<?= $limit ?>&tukhoa=<?= urlencode($tukhoa) ?>">&laquo; Trước</a></li>
            <?php for($p=$start;$p<=$end;$p++): ?>
                <li class="page-item <?= ($p==$page)?'active':'' ?>"><a class="page-link" href="?page=<?= $p ?>&limit=<?= $limit ?>&tukhoa=<?= urlencode($tukhoa) ?>"><?= $p ?></a></li>
            <?php endfor; ?>
            <li class="page-item <?= $page>=$total_pages?'disabled':'' ?>"><a class="page-link" href="?page=<?= $page+1 ?>&limit=<?= $limit ?>&tukhoa=<?= urlencode($tukhoa) ?>">Tiếp &raquo;</a></li>
        </ul>
    </nav>
    <?php endif; ?>
        <?php include("../footer.php"); ?>

</div>

<!-- Modal duyệt khóa học -->
<div class="modal fade" id="approveModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title">Xác nhận duyệt</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">Bạn có chắc chắn muốn duyệt khóa học này?</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
        <form id="approveForm" method="post" action="process.php">
            <input type="hidden" name="course_id" id="approveCourseId">
            <button type="submit" class="btn btn-success" name="approve_course">Duyệt</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal xóa khóa học -->
<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Xác nhận xóa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">Bạn có chắc chắn muốn xóa khóa học này?</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
        <form id="deleteForm" method="post" action="process.php">
            <input type="hidden" name="course_id" id="deleteCourseId">
            <button type="submit" class="btn btn-danger" name="delete_course">Xóa</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
const approveModal = document.getElementById('approveModal');
approveModal.addEventListener('show.bs.modal', event=>{
    const button = event.relatedTarget;
    const courseId = button.getAttribute('data-id');
    document.getElementById('approveCourseId').value = courseId;
});

const deleteModal = document.getElementById('deleteModal');
deleteModal.addEventListener('show.bs.modal', event=>{
    const button = event.relatedTarget;
    const courseId = button.getAttribute('data-id');
    document.getElementById('deleteCourseId').value = courseId;
});
</script>
</body>
</html>
