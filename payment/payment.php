<?php
session_start();
include_once('../config/connect.php');
require_once '../vendor/autoload.php';

use PayOS\PayOS;

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
// Kiểm tra biến môi trường
if (!isset($_ENV['PAYOS_CLIENT_ID']) || !isset($_ENV['PAYOS_API_KEY']) || !isset($_ENV['PAYOS_CHECKSUM_KEY'])) {
    die("Lỗi cấu hình: Thiếu thông tin PayOS trong file .env");
}
// Kiểm tra course_id
if (!isset($_GET['course_id'])) {
    header("Location: index.php");
    exit();
}

$course_id = intval($_GET['course_id']);
$user_id = $_SESSION['user_id'];

// Lấy thông tin khóa học
$sql = "SELECT * FROM course WHERE course_id = ?";
$stmt = $dbconnect->prepare($sql);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();
$course = $result->fetch_assoc();

if (!$course) {
    die("Khóa học không tồn tại");
}

// Kiểm tra đã mua khóa học chưa
$check_sql = "SELECT * FROM course_member WHERE student_id = ? AND course_id = ?";
$check_stmt = $dbconnect->prepare($check_sql);
$check_stmt->bind_param("ii", $user_id, $course_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    header("Location: course/index.php?id=" . $course_id);
    exit();
}

// Xử lý thanh toán khi nhấn nút
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pay_now'])) {
    try {
        // Khởi tạo PayOS
        $payOS = new PayOS(
            $_ENV['PAYOS_CLIENT_ID'],
            $_ENV['PAYOS_API_KEY'], 
            $_ENV['PAYOS_CHECKSUM_KEY']
        );

        // Tạo order code unique
        $orderCode = rand(100000, 999999);
        
        // Tạo payment record
        $payment_sql = "INSERT INTO payments (user_id, course_id, amount, order_code, description) 
                       VALUES (?, ?, ?, ?, ?)";
        $payment_stmt = $dbconnect->prepare($payment_sql);
        $payment_stmt->bind_param("iidis", $user_id, $course_id, $course['price'], $orderCode, $course['course_name']);
        $payment_stmt->execute();
        $payment_id = $payment_stmt->insert_id;

        // Tạo payment link với PayOS
        // Tự động detect base URL từ server
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST']; // Sẽ bao gồm cả cổng nếu có
        $base_url = $protocol . '://' . $host;

        $orderData = [
            'orderCode' => $orderCode,
            'amount' => intval($course['price']),
            'description' => $course['course_name'],
            'returnUrl' => $base_url . '/payment-success.php',
            'cancelUrl' => $base_url . '/payment-cancel.php'
        ];

        $paymentLink = $payOS->createPaymentLink($orderData);

        // Cập nhật checkout_url vào database
        $update_sql = "UPDATE payments SET checkout_url = ? WHERE payment_id = ?";
        $update_stmt = $dbconnect->prepare($update_sql);
        $update_stmt->bind_param("si", $paymentLink['checkoutUrl'], $payment_id);
        $update_stmt->execute();

        // Redirect đến trang thanh toán PayOS
        header("Location: " . $paymentLink['checkoutUrl']);
        exit();

    } catch (Exception $e) {
        $error = "Lỗi thanh toán: " . $e->getMessage();
        error_log("PayOS Error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán khóa học</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Thanh toán khóa học</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <!-- Thông tin khóa học -->
                        <div class="mb-4">
                            <h5>Thông tin khóa học</h5>
                            <div class="border p-3 rounded">
                                <h6><?php echo htmlspecialchars($course['course_name']); ?></h6>
                                <p class="mb-1">Mã khóa học: <?php echo htmlspecialchars($course['course_code']); ?></p>
                                <p class="mb-1">Mô tả: <?php echo htmlspecialchars($course['course_description']); ?></p>
                                <h4 class="text-primary mt-2">
                                    <?php echo number_format($course['price'], 0, ',', '.'); ?> VNĐ
                                </h4>
                            </div>
                        </div>

                        <!-- Phương thức thanh toán -->
                        <div class="mb-4">
                            <h5>Phương thức thanh toán</h5>
                            <div class="border p-3 rounded">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="payos" checked>
                                    <label class="form-check-label" for="payos">
                                        <strong>PayOS</strong> - Thanh toán qua QR Code, Ví điện tử, Thẻ ngân hàng
                                    </label>
                                </div>
                                <small class="text-muted">
                                    Bạn sẽ được chuyển đến cổng thanh toán PayOS để hoàn tất giao dịch
                                </small>
                            </div>
                        </div>

                        <!-- Nút thanh toán -->
                        <form method="POST">
                            <button type="submit" name="pay_now" class="btn btn-success w-100 btn-lg">
                                Thanh toán ngay - <?php echo number_format($course['price'], 0, ',', '.'); ?> VNĐ
                            </button>
                        </form>

                        <div class="mt-3 text-center">
                            <a href="index.php" class="btn btn-outline-secondary">Quay lại</a>
                        </div>
                    </div>
                </div>

                <!-- Thông tin bảo mật -->
                <div class="mt-3 text-center">
                    <small class="text-muted">
                        <i class="bi bi-shield-check"></i> Giao dịch được bảo mật bởi PayOS
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>