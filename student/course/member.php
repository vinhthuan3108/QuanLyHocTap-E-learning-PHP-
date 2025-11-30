<?php
include_once('layout.php');
include_once('../../config/connect.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Lấy course_id từ session
$course_id = isset($_SESSION['course_id']) ? $_SESSION['course_id'] : 0;

// Phân trang
$limit = 5; // số bản ghi/trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

$keyword = '';
$where_search = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['timkiem'])) {
    $keyword = trim($_POST['tukhoa']);
    $keyword_sql = strtolower(str_replace(' ', '', $keyword));
    $where_search = " AND (LOWER(REPLACE(REPLACE(REPLACE(REPLACE(full_name, ' ', ''), 'Đ', 'D'),'đ','d'), ' ', '')) LIKE '%$keyword_sql%' OR full_name LIKE '%$keyword%')";
}

// Lấy tổng số bản ghi để tính số trang
$count_sql = "SELECT COUNT(*) AS total FROM user us
    INNER JOIN course_member cm ON us.user_id = cm.student_id
    WHERE course_id = $course_id $where_search";
$count_result = mysqli_query($dbconnect, $count_sql);
$total_rows = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_rows / $limit);

// Lấy dữ liệu hiện tại
$sql = "SELECT * FROM user us
    INNER JOIN course_member cm ON us.user_id = cm.student_id
    WHERE course_id = $course_id $where_search
    LIMIT $start, $limit";
$result = mysqli_query($dbconnect, $sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Danh sách thành viên</title>
</head>

<body>
    <header class="container mt-4">
        <div class="row">
            <div class="col-md-6">
                <h3>Danh sách thành viên</h3>
            </div>
            <div class="col-md-6">
                <form class="d-flex" action="member.php" method="POST">
                    <div class="input-group">
                        <input type="search" class="form-control me-2" placeholder="Tìm kiếm" name="tukhoa" aria-label="Tìm kiếm" value="<?php echo htmlspecialchars($keyword); ?>">
                        <button class="btn btn-outline-primary" type="submit" name="timkiem" value="find">Tìm</button>
                    </div>
                </form>
                <?php if (!empty($keyword)) { ?>
                    <div class="row mt-3">
                        <div class="col">
                            <?php echo "<p>Tìm kiếm với từ khóa: '<strong>$keyword</strong>'</p>"; ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </header>

    <div class="container mt-4">
        <div class="row">
            <div class="">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>STT</th>
                            <th>Họ và tên</th>
                            <th>Ngày sinh</th>
                            <th>Giới tính</th>
                            <th>Email</th>
                            <th>Trang cá nhân</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result && mysqli_num_rows($result) > 0) {
                            $index = $start;
                            while ($row = mysqli_fetch_assoc($result)) {
                                $index++;
                                $student_id = $row['student_id'];
                        ?>
                                <tr>
                                    <td><?php echo $index; ?></td>
                                    <td><?php echo $row['full_name']; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($row['date_of_birth'])); ?></td>
                                    <td><?php echo ($row['gender'] == "M" ? "Nam" : "Nữ"); ?></td>
                                    <td><?php echo $row['email']; ?></td>
                                    <td><a class="btn btn-primary" href="my_student.php?user_id=<?php echo $student_id ?>">Xem chi tiết</a></td>
                                </tr>
                        <?php
                            }
                        } else {
                            echo '<tr><td colspan="6">Không có dữ liệu</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>

                <!-- Phân trang -->
                <?php if ($total_pages > 1) : ?>
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">

                            <!-- First page -->
                            <?php if ($page > 1) : ?>
                                <li class="page-item"><a class="page-link" href="?page=1<?php if($keyword) echo '&tukhoa='.urlencode($keyword); ?>"><<</a></li>
                            <?php endif; ?>

                            <!-- Previous -->
                            <?php if ($page > 1) : ?>
                                <li class="page-item"><a class="page-link" href="?page=<?php echo $page - 1; ?><?php if($keyword) echo '&tukhoa='.urlencode($keyword); ?>">&lt;</a></li>
                            <?php endif; ?>

                            <?php
                            // Giới hạn hiển thị tối đa 5 trang
                            $start_page = max(1, $page - 2);
                            $end_page = min($total_pages, $page + 2);

                            if ($start_page > 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';

                            for ($i = $start_page; $i <= $end_page; $i++) :
                            ?>
                                <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?><?php if($keyword) echo '&tukhoa='.urlencode($keyword); ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor;

                            if ($end_page < $total_pages) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                            ?>

                            <!-- Next -->
                            <?php if ($page < $total_pages) : ?>
                                <li class="page-item"><a class="page-link" href="?page=<?php echo $page + 1; ?><?php if($keyword) echo '&tukhoa='.urlencode($keyword); ?>">&gt;</a></li>
                            <?php endif; ?>

                            <!-- Last page -->
                            <?php if ($page < $total_pages) : ?>
                                <li class="page-item"><a class="page-link" href="?page=<?php echo $total_pages; ?><?php if($keyword) echo '&tukhoa='.urlencode($keyword); ?>">>></a></li>
                            <?php endif; ?>

                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Script Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
