<?php
include('layout.php');
include_once('config/connect.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Truy vấn để lấy tất cả các khóa học
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['timkiem'])) {
    $tukhoa = $_POST['tukhoa'];
    $keyword = strtolower(trim($tukhoa));
    $keyword = str_replace(' ', '', $keyword);
    $sql = "SELECT * FROM course 
            WHERE LOWER(REPLACE(REPLACE(REPLACE(REPLACE(course_name, ' ', ''), 'Đ', 'D'),'đ','d'), ' ', '')) LIKE '%$keyword%' 
            OR course_name LIKE '%$tukhoa%'";
    $result = mysqli_query($dbconnect, $sql);
} else {
    $sql = "SELECT * FROM course";
    $result = mysqli_query($dbconnect, $sql);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ - Tất cả khóa học</title>
    <style>
        .custom-card {
            width: 100%;
            height: 0;
            padding-top: 50%;
            position: relative;
        }

        .custom-card img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .price-tag {
            font-size: 1.25rem;
            font-weight: bold;
            color: #e74c3c;
        }
        
        .free-badge {
            background-color: #28a745;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.875rem;
        }
        .btn-group-custom {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        .btn-group-custom .btn {
            flex: 1;
            min-width: 120px;
        }
    </style>
</head>

<body>
    <header class="container mt-4">
        <div class="row">
            <div class="col-md-6">
                <h3>Tất cả khóa học</h3>
            </div>
            <div class="col-md-6">
                <form action="index.php" method="POST">
                    <div class="input-group">
                        <input type="search" class="form-control" placeholder="Tìm kiếm..." name="tukhoa">
                        <button class="btn btn-secondary rounded-end" type="submit" name="timkiem" value="find">Tìm kiếm</button>
                    </div>
                </form>
                <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['timkiem'])) { ?>
                    <div class="row mt-3">
                        <div class="col">
                            <?php
                            $tukhoa = $_POST['tukhoa'];
                            echo "<p>Tìm kiếm với từ khóa: '<strong>$tukhoa</strong>'</p>";
                            ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </header>

    <div class="container mt-5">
        <div class="row">
            <?php
            if (mysqli_num_rows($result) > 0) {
                mysqli_data_seek($result, 0);
                while ($row = mysqli_fetch_array($result)) {
                    $is_free = $row['price'] == 0;
            ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="custom-card">
                                <img src="<?php echo "../assets/file/course_background/" . $row['course_background'] ?>" class="card-img-top" alt="Course Image">
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo $row['course_name']; ?></h5>
                                <p class="card-text">
                                    Mã khóa học: <?php echo $row['course_code']; ?><br>
                                </p>
                                
                                <!-- Hiển thị giá -->
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <?php if ($is_free): ?>
                                            <span class="free-badge">Miễn phí</span>
                                        <?php else: ?>
                                            <span class="price-tag"><?php echo number_format($row['price'], 0, ',', '.'); ?> VNĐ</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="btn-group-custom">
                                        <!-- Luôn hiển thị nút Xem khóa học -->
                                        <a class="btn btn-outline-primary" href="course_preview.php?id=<?php echo $row['course_id']; ?>">
                                            Xem khóa học
                                        </a>
                                        
                                        <?php if (isset($_SESSION['user_id'])) { ?>
                                            <?php if ($is_free): ?>
                                                <a class="btn btn-success" href="course/index.php?id=<?php echo $row['course_id']; ?>">Tham gia ngay</a>
                                            <?php else: ?>
                                                <!-- Kiểm tra xem user đã mua khóa học chưa -->
                                                <?php
                                                $user_id = $_SESSION['user_id'];
                                                $check_payment_sql = "SELECT * FROM payments WHERE user_id = $user_id AND course_id = {$row['course_id']} AND payment_status = 'completed'";
                                                $payment_result = mysqli_query($dbconnect, $check_payment_sql);
                                                $has_paid = mysqli_num_rows($payment_result) > 0;
                                                
                                                if ($has_paid): ?>
                                                    <a class="btn btn-primary" href="course/index.php?id=<?php echo $row['course_id']; ?>">Truy cập</a>
                                                <?php else: ?>
                                                    <a class="btn btn-warning" href="payment.php?course_id=<?php echo $row['course_id']; ?>">Thanh toán</a>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        <?php } else { ?>
                                            <?php if ($is_free): ?>
                                                <a class="btn btn-success" href="login.php">Tham gia</a>
                                            <?php else: ?>
                                                <a class="btn btn-warning" href="login.php?redirect=payment&course_id=<?php echo $row['course_id']; ?>">Thanh toán</a>
                                            <?php endif; ?>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo "<div class='col-12'><p class='text-center'>Không tìm thấy khóa học nào.</p></div>";
            }
            ?>
        </div>
    </div>
    
    <?php include("footer.php"); ?>
</body>
</html>