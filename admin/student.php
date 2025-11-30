<?php
include("layout.php");
include_once('../config/connect.php');
if (session_status() == PHP_SESSION_NONE) { session_start(); }
// Chọn số dòng hiển thị
$limit_options = [5,10,15];
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
if(!in_array($limit, $limit_options)) $limit = 10;

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Tìm kiếm
$tukhoa = isset($_GET['tukhoa']) ? trim($_GET['tukhoa']) : '';
$keyword_sql = '';
if($tukhoa != ''){
    $keyword_sql = " AND (LOWER(REPLACE(us.full_name,' ','')) LIKE '%".strtolower(str_replace(' ','',$tukhoa))."%'
                     OR LOWER(ua.username) LIKE '%".strtolower($tukhoa)."%')";
}

// Tổng số bản ghi
$total_result = $dbconnect->query("SELECT COUNT(*) AS total
    FROM user us
    INNER JOIN user_role ur ON us.user_id=ur.user_id
    INNER JOIN user_account ua ON us.user_id=ua.user_id
    WHERE ur.role_id=1 $keyword_sql");
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = max(1, ceil($total_records / $limit));

// Lấy dữ liệu học sinh
$sql_student = "SELECT us.*, ua.username, ua.password FROM user us
    INNER JOIN user_role ur ON us.user_id=ur.user_id
    INNER JOIN user_account ua ON us.user_id=ua.user_id
    WHERE ur.role_id=1 $keyword_sql
    ORDER BY us.user_id DESC
    LIMIT $limit OFFSET $offset";
$result_student = $dbconnect->query($sql_student);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Danh sách học sinh</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<style>
.table-hover tbody tr:hover { background-color: #f1f1f1; }
.btn i { margin-right: 4px; }
.pagination li a { color: #0d6efd; }
</style>
</head>
<body>
<?php include "sidebar.php"; ?>

<div class="main p-4" id="mainContent">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <h3 class="mb-2">Danh sách Học sinh</h3>
        <a class="btn btn-primary mb-2" href="account_add.php?role_id=1&role_name=student">
            <i class="fa-solid fa-plus"></i> Tạo mới
        </a>
    </div>

    <!-- Tìm kiếm + Số dòng hiển thị -->
    <form class="row g-2 align-items-center mb-4" action="student.php" method="GET">
        <div class="col-auto">
            <select name="limit" class="form-select" onchange="this.form.submit()">
                <?php foreach($limit_options as $opt): ?>
                    <option value="<?= $opt ?>" <?= $limit==$opt?'selected':'' ?>><?= $opt ?> dòng</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-auto flex-grow-1">
            <input class="form-control" type="search" placeholder="Tìm kiếm tên hoặc username" name="tukhoa" value="<?= htmlspecialchars($tukhoa) ?>">
        </div>
        <div class="col-auto">
            <button class="btn btn-outline-primary" type="submit">
                <i class="fa-solid fa-magnifying-glass"></i> Tìm
            </button>
        </div>
    </form>



    <?php if($total_records == 0): ?>
        <div class="alert alert-warning text-center">
            Không có dữ liệu <?= $tukhoa ? "tìm kiếm với từ khóa '<strong>".htmlspecialchars($tukhoa)."</strong>'" : "" ?>.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>STT</th>
                        <th>Họ và tên</th>
                        <th>Ngày sinh</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = $offset + 1;
                    while($row = $result_student->fetch_assoc()): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= htmlspecialchars($row['full_name']) ?></td>
                            <td><?= date('d/m/Y', strtotime($row['date_of_birth'])) ?></td>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td>
                                <a class="btn btn-info btn-sm mb-1" href="account_view.php?user_id=<?= $row['user_id'] ?>&role_id=1&role_name=student"><i class="fa-solid fa-eye"></i> Xem</a>
                                <a class="btn btn-warning btn-sm mb-1" href="account_edit.php?user_id=<?= $row['user_id'] ?>&role_id=1&role_name=student"><i class="fa-solid fa-pen"></i> Sửa</a>
                                <button type="button" class="btn btn-danger btn-sm mb-1" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?= $row['user_id'] ?>"><i class="fa-solid fa-trash"></i> Xóa</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Phân trang -->
        <?php
        $max_links = 5; // số trang hiển thị tối đa
        $start = max(1, $page - 2); // bắt đầu 2 trang trước trang hiện tại
        $end = min($total_pages, $start + $max_links - 1); // kết thúc
        $start = max(1, $end - $max_links + 1); // điều chỉnh lại nếu gần cuối
        ?>

        <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <li class="page-item <?= $page<=1?'disabled':'' ?>">
                <a class="page-link" href="?page=<?= $page-1 ?>&limit=<?= $limit ?>">&laquo; Trước</a>
            </li>

            <?php if($start > 1): ?>
                <li class="page-item"><span class="page-link">1</span></li>
                <?php if($start > 2): ?>
                    <li class="page-item disabled"><span class="page-link">…</span></li>
                <?php endif; ?>
            <?php endif; ?>

            <?php for($p=$start; $p<=$end; $p++): ?>
                <li class="page-item <?= ($p==$page)?'active':'' ?>">
                    <a class="page-link" href="?page=<?= $p ?>&limit=<?= $limit ?>"><?= $p ?></a>
                </li>
            <?php endfor; ?>

            <?php if($end < $total_pages): ?>
                <?php if($end < $total_pages - 1): ?>
                    <li class="page-item disabled"><span class="page-link">…</span></li>
                <?php endif; ?>
                <li class="page-item"><a class="page-link" href="?page=<?= $total_pages ?>&limit=<?= $limit ?>"><?= $total_pages ?></a></li>
            <?php endif; ?>

            <li class="page-item <?= $page>=$total_pages?'disabled':'' ?>">
                <a class="page-link" href="?page=<?= $page+1 ?>&limit=<?= $limit ?>">Tiếp &raquo;</a>
            </li>
        </ul>
        </nav>

    <?php endif; ?>
        <?php include("../footer.php"); ?>

</div>

<!-- Modal Xóa -->
<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Xác nhận xóa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        Bạn có chắc chắn muốn xóa tài khoản này?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
        <form id="deleteForm" method="post" action="process.php">
            <input type="hidden" name="user_id" id="deleteUserId">
            <button type="submit" class="btn btn-danger" name="delete_student">Xóa</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
const deleteModal = document.getElementById('deleteModal');
deleteModal.addEventListener('show.bs.modal', event => {
    const button = event.relatedTarget;
    const userId = button.getAttribute('data-id');
    document.getElementById('deleteUserId').value = userId;
});
</script>
</body>
</html>
