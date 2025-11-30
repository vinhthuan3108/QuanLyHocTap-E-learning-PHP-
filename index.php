<?php
// Lưu ý: Nếu file này nằm ở thư mục gốc thì include layout.php bình thường
// Nếu layout.php nằm trong thư mục includes thì sửa đường dẫn lại cho đúng
include('layout.php');
include_once('config/connect.php');

// Vẫn start session để header có thể hiển thị nút Login/Register nếu cần
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// --- XỬ LÝ TÌM KIẾM ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['timkiem'])) {
    $tukhoa = $_POST['tukhoa'];
    // Xử lý chuỗi tìm kiếm để tìm chính xác hơn (giữ nguyên logic của bạn)
    $keyword = strtolower(trim($tukhoa));
    $keyword = str_replace(' ', '', $keyword);
    $sql = "SELECT * FROM course 
            WHERE LOWER(REPLACE(REPLACE(REPLACE(REPLACE(course_name, ' ', ''), 'Đ', 'D'),'đ','d'), ' ', '')) LIKE '%$keyword%' 
            OR course_name LIKE '%$tukhoa%'";
    $result = mysqli_query($dbconnect, $sql);
} else {
    // Mặc định lấy tất cả khóa học
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
            padding-top: 56.25%; /* Tỉ lệ 16:9 chuẩn hơn 50% */
            position: relative;
            background-color: #f0f0f0; /* Màu nền chờ ảnh */
            overflow: hidden;
            border-radius: 4px;
        }

        .custom-card img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        /* Hiệu ứng zoom nhẹ khi di chuột */
        .card:hover .custom-card img {
            transform: scale(1.05);
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
            font-weight: bold;
        }

        .btn-group-custom {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .btn-group-custom .btn {
            flex: 1;
        }
    </style>
</head>

<body>
    <header class="container mt-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h3 class="fw-bold">Tất cả khóa học</h3>
            </div>
            <div class="col-md-6">
                <form action="index.php" method="POST">
                    <div class="input-group">
                        <input type="search" class="form-control" placeholder="Tìm kiếm khóa học..." name="tukhoa" value="<?php echo isset($_POST['tukhoa']) ? $_POST['tukhoa'] : ''; ?>">
                        <button class="btn btn-primary rounded-end" type="submit" name="timkiem" value="find">
                            <i class="bi bi-search"></i> Tìm kiếm
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </header>

    <div class="container mt-4">
        <div class="row">
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_array($result)) {
                    $is_free = $row['price'] == 0;
                    
                    // Xử lý đường dẫn ảnh:
                    // Nếu file index.php ở root, thì đường dẫn là "assets/..." chứ không phải "../assets/..."
                    // Tôi đã sửa lại thành "assets/...", bạn kiểm tra lại cấu trúc folder nhé
                    $img_path = "assets/file/course_background/" . $row['course_background'];
            ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="custom-card">
                                <img src="<?php echo $img_path; ?>" 
                                     alt="<?php echo $row['course_name']; ?>"
                                     onerror="this.src='https://via.placeholder.com/600x400?text=No+Image'">
                            </div>

                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title text-truncate" title="<?php echo $row['course_name']; ?>">
                                    <?php echo $row['course_name']; ?>
                                </h5>
                                <p class="card-text text-muted mb-2">
                                    <small>Mã: <?php echo $row['course_code']; ?></small>
                                </p>
                                
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <?php if ($is_free): ?>
                                            <span class="free-badge">Miễn phí</span>
                                        <?php else: ?>
                                            <span class="price-tag"><?php echo number_format($row['price'], 0, ',', '.'); ?> đ</span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="btn-group-custom">
                                        <a class="btn btn-outline-primary" href="course_preview.php?id=<?php echo $row['course_id']; ?>">
                                            Xem chi tiết
                                        </a>

                                        <?php if ($is_free): ?>
                                            <a class="btn btn-success" href="account/login.php?redirect=course&id=<?php echo $row['course_id']; ?>">
                                                Đăng ký học
                                            </a>
                                        <?php else: ?>
                                            <a class="btn btn-warning text-white" href="account/login.php?redirect=payment&course_id=<?php echo $row['course_id']; ?>">
                                                Mua ngay
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo "<div class='col-12 text-center py-5'>";
                echo "<h4 class='text-muted'>Không tìm thấy khóa học nào phù hợp.</h4>";
                echo "</div>";
            }
            ?>
        </div>
    </div>

    <?php include("footer.php"); ?>
</body>
</html>