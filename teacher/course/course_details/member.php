<?php
include_once('../layout.php');
include_once('../../../config/connect.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$result = null;
$tukhoa = "";
$course_id = $_SESSION['course_id'];

// ---------------------- XỬ LÝ TÌM KIẾM -------------------------
if (isset($_GET['tukhoa'])) {
    $tukhoa = trim($_GET['tukhoa']);
    $keyword = strtolower(str_replace(' ', '', $tukhoa));

    // Câu lệnh tìm kiếm
    $sql = "SELECT * FROM user us
            INNER JOIN course_member cm ON us.user_id = cm.student_id
            WHERE course_id = $course_id
            AND (
                LOWER(REPLACE(REPLACE(REPLACE(REPLACE(full_name,' ',''),'Đ','D'),'đ','d'),' ','')) LIKE '%$keyword%'
                OR full_name LIKE '%$tukhoa%'
            )";
} else {
    // Không tìm kiếm
    $sql = "SELECT * FROM user us
            INNER JOIN course_member cm ON us.user_id = cm.student_id
            WHERE course_id = $course_id";
}

// ---------------------- PHÂN TRANG -------------------------
$limit = 6;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) $page = 1;

$start = ($page - 1) * $limit;

$sql_count = str_replace("SELECT *", "SELECT COUNT(*) AS total", $sql);
$count_res = mysqli_query($dbconnect, $sql_count);
$count_row = mysqli_fetch_assoc($count_res);
$total_records = $count_row['total'];

$total_pages = ceil($total_records / $limit);

// Lấy dữ liệu theo phân trang
$sql .= " LIMIT $start, $limit";
$result = mysqli_query($dbconnect, $sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách thành viên</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f5f7fb;
        }

        .page-title {
            font-weight: 700;
            font-size: 26px;
        }

        .search-box input {
            border-radius: 10px 0 0 10px;
        }

        .search-box button {
            border-radius: 0 10px 10px 0;
        }

        .table thead th {
            background: #e9eef7;
            font-weight: 600;
        }

        .table tbody tr:hover {
            background: #f1f5ff;
        }

        .pagination .page-link {
            border-radius: 8px;
            margin: 0 3px;
        }
    </style>
</head>

<body>
    <div class="container mt-4">

        <!-- Title + Search -->
        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <h3 class="page-title">Danh sách thành viên khóa học</h3>
            </div>

            <div class="col-md-6">
                <form action="" method="GET" class="d-flex search-box">
                    <input type="text" name="tukhoa" value="<?php echo $tukhoa; ?>" class="form-control"
                        placeholder="Tìm tên sinh viên...">
                    <button class="btn btn-primary" type="submit">Tìm</button>
                </form>
                <?php
                if ($tukhoa != "") {
                    echo "<p class='mt-2 text-secondary'>Kết quả tìm kiếm cho: <strong>$tukhoa</strong></p>";
                }
                ?>
            </div>
        </div>

        <!-- Table Box -->
        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="25%">Họ và tên</th>
                            <th width="15%">Ngày sinh</th>
                            <th width="10%">Giới tính</th>
                            <th width="25%">Email</th>
                            <th width="20%" class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result && mysqli_num_rows($result) > 0) {
                            $stt = $start;
                            while ($row = mysqli_fetch_assoc($result)) {
                                $stt++;
                                $student_id = $row['student_id'];
                        ?>
                                <tr>
                                    <td><?php echo $stt; ?></td>
                                    <td><?php echo $row['full_name']; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($row['date_of_birth'])); ?></td>
                                    <td><?php echo ($row['gender'] == "M" ? "Nam" : "Nữ"); ?></td>
                                    <td><?php echo $row['email']; ?></td>

                                    <td class="text-center">
                                        <a href="my_student.php?user_id=<?php echo $student_id; ?>" class="btn btn-sm btn-primary">
                                            Xem thông tin
                                        </a>
                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                            data-bs-target="#deleteModal-<?php echo $student_id; ?>">
                                            Xóa
                                        </button>
                                    </td>
                                </tr>

                                <!-- Modal Xác nhận Xóa -->
                                <div class="modal fade" id="deleteModal-<?php echo $student_id; ?>" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Xác nhận</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                Bạn chắc chắn muốn xóa <strong><?php echo $row['full_name']; ?></strong> khỏi khóa học?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    Hủy
                                                </button>
                                                <form action="../process.php" method="POST">
                                                    <input type="hidden" name="delete_student_id" value="<?php echo $student_id; ?>">
                                                    <button type="submit" class="btn btn-danger" name="delete_member_course">
                                                        Xóa thành viên
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                        <?php
                            }
                        } else {
                            echo '<tr><td colspan="6" class="text-center text-muted">Không có dữ liệu</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- PHÂN TRANG -->
        <?php if ($total_pages > 1) { ?>
            <nav class="mt-3">
                <ul class="pagination justify-content-center">

                    <!-- Nút << -->
                    <?php if ($page > 1) { ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=1&tukhoa=<?php echo $tukhoa; ?>">&laquo;</a>
                        </li>
                    <?php } ?>

                    <!-- Nút < -->
                    <?php if ($page > 1) { ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo ($page - 1); ?>&tukhoa=<?php echo $tukhoa; ?>">&lsaquo;</a>
                        </li>
                    <?php } ?>

                    <!-- Số trang (giới hạn 5) -->
                    <?php
                    $start_page = max(1, $page - 2);
                    $end_page = min($total_pages, $page + 2);

                    if ($start_page > 1) {
                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }

                    for ($i = $start_page; $i <= $end_page; $i++) {
                        $active = ($i == $page) ? "active" : "";
                        echo '<li class="page-item ' . $active . '">
                                <a class="page-link" href="?page=' . $i . '&tukhoa=' . $tukhoa . '">' . $i . '</a>
                              </li>';
                    }

                    if ($end_page < $total_pages) {
                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }
                    ?>

                    <!-- Nút > -->
                    <?php if ($page < $total_pages) { ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo ($page + 1); ?>&tukhoa=<?php echo $tukhoa; ?>">&rsaquo;</a>
                        </li>
                    <?php } ?>

                    <!-- Nút >> -->
                    <?php if ($page < $total_pages) { ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $total_pages; ?>&tukhoa=<?php echo $tukhoa; ?>">&raquo;</a>
                        </li>
                    <?php } ?>
                </ul>
            </nav>
        <?php } ?>

    </div>
    <?php include("../../../footer.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
