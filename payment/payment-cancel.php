<?php
session_start();
include_once('../config/connect.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán bị hủy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border-warning">
                    <div class="card-header bg-warning text-dark text-center">
                        <h4 class="mb-0">⚠️ Thanh toán bị hủy</h4>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <i class="bi bi-x-circle-fill text-warning" style="font-size: 4rem;"></i>
                        </div>
                        
                        <h5 class="text-warning">Bạn đã hủy thanh toán</h5>
                        <p class="mb-4">Giao dịch thanh toán đã bị hủy. Bạn có thể thử lại bất cứ lúc nào.</p>
                        
                        <div class="d-grid gap-2">
                            <a href="index.php" class="btn btn-primary">Về trang chủ</a>
                            <a href="javascript:history.back()" class="btn btn-outline-secondary">Thử lại</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>