<?php
include_once('../config/connect.php');
require_once '../vendor/autoload.php';

use PayOS\PayOS;

$webhookBody = file_get_contents('php://input');
$data = json_decode($webhookBody, true);

if (!$data) {
    http_response_code(400);
    exit('Invalid webhook data');
}

try {

    $payOS = new PayOS(
        $_ENV['PAYOS_CLIENT_ID'],
        $_ENV['PAYOS_API_KEY'], 
        $_ENV['PAYOS_CHECKSUM_KEY']
    );
    

    $webhookSignature = isset($_SERVER['HTTP_X_PAYOS_SIGNATURE']) ? $_SERVER['HTTP_X_PAYOS_SIGNATURE'] : '';
    
    if (empty($webhookSignature)) {
        http_response_code(401);
        exit('Missing signature');
    }
    
    // Tạo signature để so sánh
    $computedSignature = hash_hmac('sha256', $webhookBody, $_ENV['PAYOS_CHECKSUM_KEY']);
    
    if (!hash_equals($webhookSignature, $computedSignature)) {
        http_response_code(401);
        exit('Invalid signature');
    }
    
    // Xử lý khi thanh toán thành công
    if ($data['code'] == '00' && $data['success'] == true) {
        $orderCode = $data['orderCode'];
        
        // Cập nhật trạng thái payment
        $update_sql = "UPDATE payments SET payment_status = 'completed', paid_at = NOW(), 
                      transaction_id = ? WHERE order_code = ?";
        $stmt = $dbconnect->prepare($update_sql);
        $transactionId = isset($data['transactionId']) ? $data['transactionId'] : (isset($data['id']) ? $data['id'] : null);
        $stmt->bind_param("si", $transactionId, $orderCode);
        
        if ($stmt->execute()) {
            // Lấy thông tin payment
            $payment_sql = "SELECT * FROM payments WHERE order_code = ?";
            $payment_stmt = $dbconnect->prepare($payment_sql);
            $payment_stmt->bind_param("i", $orderCode);
            $payment_stmt->execute();
            $payment = $payment_stmt->get_result()->fetch_assoc();
            
            if ($payment) {
                // Thêm user vào khóa học
                $check_sql = "SELECT * FROM course_member WHERE student_id = ? AND course_id = ?";
                $check_stmt = $dbconnect->prepare($check_sql);
                $check_stmt->bind_param("ii", $payment['user_id'], $payment['course_id']);
                $check_stmt->execute();
                
                if ($check_stmt->get_result()->num_rows == 0) {
                    $enroll_sql = "INSERT INTO course_member (course_id, student_id) VALUES (?, ?)";
                    $enroll_stmt = $dbconnect->prepare($enroll_sql);
                    $enroll_stmt->bind_param("ii", $payment['course_id'], $payment['user_id']);
                    
                    if ($enroll_stmt->execute()) {
                        // Ghi log thành công
                        error_log("Webhook: User {$payment['user_id']} enrolled in course {$payment['course_id']}");
                        
                        // TODO: Gửi email xác nhận ở đây
                    } else {
                        error_log("Webhook Error: Failed to enroll user - " . $enroll_stmt->error);
                    }
                } else {
                    error_log("Webhook: User {$payment['user_id']} already enrolled in course {$payment['course_id']}");
                }
            } else {
                error_log("Webhook Error: Payment not found for order code: " . $orderCode);
            }
        } else {
            error_log("Webhook Error: Failed to update payment - " . $stmt->error);
        }
    } else {
        $orderCode = $data['orderCode'];
        $status = $data['success'] ? 'pending' : 'failed';
        
        $update_sql = "UPDATE payments SET payment_status = ? WHERE order_code = ?";
        $stmt = $dbconnect->prepare($update_sql);
        $stmt->bind_param("si", $status, $orderCode);
        $stmt->execute();
        
        error_log("Webhook: Payment status updated to {$status} for order: " . $orderCode);
    }
    
    http_response_code(200);
    echo json_encode(['error' => 0, 'message' => 'Webhook processed successfully']);
    
} catch (Exception $e) {
    error_log("Webhook Error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode(['error' => 1, 'message' => 'Webhook processing failed']);
}