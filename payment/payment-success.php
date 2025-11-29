<?php
session_start();
include_once('../config/connect.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Lấy thông tin payment từ URL parameters (PayOS sẽ trả về)
$orderCode = isset($_GET['orderCode']) ? $_GET['orderCode'] : null;
$status = isset($_GET['status']) ? $_GET['status'] : null;

if ($orderCode && $status == 'PAID') {
    // Cập nhật trạng thái payment
    $update_sql = "UPDATE payments SET status = 'paid', paid_at = NOW() WHERE order_code = ?";
    $stmt = $dbconnect->prepare($update_sql);
    $stmt->bind_param("i", $orderCode);
    $stmt->execute();
    
    // Lấy thông tin payment
    $payment_sql = "SELECT * FROM payments WHERE order_code = ?";
    $payment_stmt = $dbconnect->prepare($payment_sql);
    $payment_stmt->bind_param("i", $orderCode);
    $payment_stmt->execute();
    $payment = $payment_stmt->get_result()->fetch_assoc();
    
    if ($payment) {
        // Thêm user vào khóa học
        $enroll_sql = "INSERT INTO course_member (course_id, student_id) VALUES (?, ?)";
        $enroll_stmt = $dbconnect->prepare($enroll_sql);
        $enroll_stmt->bind_param("ii", $payment['course_id'], $user_id);
        $enroll_stmt->execute();
        
        // Lấy thông tin khóa học
        $course_sql = "SELECT * FROM course WHERE course_id = ?";
        $course_stmt = $dbconnect->prepare($course_sql);
        $course_stmt->bind_param("i", $payment['course_id']);
        $course_stmt->execute();
        $course = $course_stmt->get_result()->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán thành công</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border-success">
                    <div class="card-header bg-success text-white text-center">
                        <h4 class="mb-0">✅ Thanh toán thành công!</h4>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                        </div>
                        
                        <?php if (isset($course)): ?>
                            <h5 class="text-success">Cảm ơn bạn đã mua khóa học!</h5>
                            <div class="border p-3 rounded mt-3">
                                <h6><?php echo htmlspecialchars($course['course_name']); ?></h6>
                                <p class="mb-1">Mã đơn hàng: <strong><?php echo $orderCode; ?></strong></p>
                                <p class="mb-1">Số tiền: <strong><?php echo number_format($payment['amount'], 0, ',', '.'); ?> VNĐ</strong></p>
                            </div>
                            
                            <div class="mt-4">
                                <a href="/student/course/index.php?id=<?php echo $course['course_id']; ?>" class="btn btn-primary btn-lg">
                                    Vào học ngay
                                </a>
                            </div>
                        <?php else: ?>
                            <p>Thanh toán của bạn đã được xử lý thành công.</p>
                            <a href="index.php" class="btn btn-primary">Về trang chủ</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>