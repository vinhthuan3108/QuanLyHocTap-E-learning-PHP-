<?php
include('layout.php');
include_once('../config/connect.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Lấy ID khóa học từ URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Không tìm thấy khóa học");
}

$course_id = intval($_GET['id']);

// Lấy thông tin khóa học
$course_sql = "SELECT c.*, u.full_name as teacher_name 
               FROM course c 
               LEFT JOIN user u ON c.teacher_id = u.user_id 
               WHERE c.course_id = $course_id";
$course_result = mysqli_query($dbconnect, $course_sql);
$course = mysqli_fetch_assoc($course_result);

if (!$course) {
    die("Khóa học không tồn tại");
}

// Kiểm tra xem user đã đăng nhập và đã tham gia khóa học chưa
$has_joined = false;
$is_free = $course['price'] == 0;

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    // Kiểm tra xem user có phải là student không
    $check_student_sql = "SELECT ur.role_id 
                         FROM user_role ur 
                         WHERE ur.user_id = $user_id AND ur.role_id = 1";
    $student_result = mysqli_query($dbconnect, $check_student_sql);
    $is_student = mysqli_num_rows($student_result) > 0;
    
    if ($is_student) {
        // Kiểm tra trong bảng course_member
        $check_member_sql = "SELECT * FROM course_member 
                            WHERE student_id = $user_id AND course_id = $course_id";
        $member_result = mysqli_query($dbconnect, $check_member_sql);
        $has_joined = mysqli_num_rows($member_result) > 0;
    } else {
        // Nếu là giáo viên hoặc admin, có thể xem tất cả khóa học
        $has_joined = true;
    }
}

// Lấy lịch học
$schedule_sql = "SELECT * FROM course_schedule WHERE course_id = $course_id";
$schedule_result = mysqli_query($dbconnect, $schedule_sql);

// Lấy danh sách chủ đề và nội dung
$topics_sql = "SELECT ct.*, 
               (SELECT COUNT(*) FROM course_contents cc WHERE cc.topic_id = ct.topic_id) as content_count
               FROM topics ct 
               WHERE ct.course_id = $course_id 
               ORDER BY ct.created_at";
$topics_result = mysqli_query($dbconnect, $topics_sql);

// Hàm chuyển đổi thứ trong tuần
function getDayOfWeek($day) {
    $days = [
        '2' => 'Thứ 2',
        '3' => 'Thứ 3', 
        '4' => 'Thứ 4',
        '5' => 'Thứ 5',
        '6' => 'Thứ 6',
        '7' => 'Thứ 7',
        '8' => 'Chủ nhật'
    ];
    return isset($days[$day]) ? $days[$day] : 'Không xác định';
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $course['course_name']; ?> - Xem trước</title>
    <style>
        .course-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }
        .course-image-container {
            display: flex;
            justify-content: center;
            align-items: center;
            padding-right: 2rem;
        }
        .course-image {
            max-width: 280px;
            width: 100%;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .course-info {
            padding-left: 1rem;
        }
        .schedule-card {
            border-left: 4px solid #007bff;
        }
        .content-type-badge {
            font-size: 0.8rem;
            padding: 0.3rem 0.6rem;
        }
        .topic-card {
            transition: transform 0.2s;
        }
        .topic-card:hover {
            transform: translateY(-2px);
        }
        .login-prompt {
            background: #f8f9fa;
            border-left: 4px solid #28a745;
            padding: 1rem;
            margin: 1rem 0;
        }
        .price-section {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 0.5rem;
            padding: 1rem;
            margin: 1rem 0;
        }
        .price-tag {
            font-size: 2rem;
            font-weight: bold;
            color: #e74c3c;
        }
        .free-badge {
            background-color: #28a745;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            font-size: 1.25rem;
            font-weight: bold;
        }
        @media (max-width: 768px) {
            .course-image-container {
                padding-right: 0;
                margin-bottom: 1.5rem;
            }
            .course-info {
                padding-left: 0;
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <!-- Header khóa học -->
    <div class="course-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-4 course-image-container">
                    <img src="../assets/file/course_background/<?php echo $course['course_background']; ?>" 
                         alt="<?php echo $course['course_name']; ?>" 
                         class="course-image img-fluid">
                </div>
                <div class="col-md-8 course-info">
                    <h1 class="display-5"><?php echo $course['course_name']; ?></h1>
                    <p class="lead">Mã khóa học: <?php echo $course['course_code']; ?></p>
                    <p class="mb-1">Giảng viên: <?php echo $course['teacher_name']; ?></p>
                    <p class="mb-1">Thời gian: <?php echo date('d/m/Y', strtotime($course['start_date'])); ?> - <?php echo date('d/m/Y', strtotime($course['end_date'])); ?></p>
                    <p class="mb-0">Trạng thái: <span class="badge bg-success"><?php echo ($course['status'] == "A") ? "Đã duyệt" : "Đang chờ duyệt"; ?></span></p>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Phần giá và tham gia -->
        <!-- Phần giá và tham gia -->
<div class="price-section">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h4 class="mb-3">Chi phí khóa học</h4>
            <?php if ($is_free): ?>
                <span class="free-badge">KHÓA HỌC MIỄN PHÍ</span>
            <?php else: ?>
                <div class="price-tag"><?php echo number_format($course['price'], 0, ',', '.'); ?> VNĐ</div>
            <?php endif; ?>
        </div>
        <div class="col-md-6 text-end">
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if ($has_joined): ?>
                    <!-- Đã tham gia khóa học -->
                    <a href="/student/course/index.php?id=<?php echo $course_id; ?>" class="btn btn-success btn-lg">
                        Truy cập khóa học
                    </a>
                <?php else: ?>
                    <!-- Chưa tham gia khóa học -->
                    <?php if ($is_free): ?>
                        <!-- Khóa học miễn phí -->
                        <a href="join_free_course.php?course_id=<?php echo $course_id; ?>" class="btn btn-success btn-lg">
                            Tham gia ngay
                        </a>
                    <?php else: ?>
                        <!-- Khóa học có phí -->
                        <a href="/payment/payment.php?course_id=<?php echo $course_id; ?>" class="btn btn-warning btn-lg">
                            Thanh toán ngay
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            <?php else: ?>
                <!-- Chưa đăng nhập -->
                <?php if ($is_free): ?>
                    <a href="login.php?redirect=course_preview&course_id=<?php echo $course_id; ?>" class="btn btn-success btn-lg">Đăng nhập để tham gia</a>
                <?php else: ?>
                    <a href="login.php?redirect=course_preview&course_id=<?php echo $course_id; ?>" class="btn btn-warning btn-lg">Đăng nhập để đăng ký</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

        <div class="row">
            <!-- Thông tin chi tiết -->
            <div class="col-md-8">
                <!-- Mô tả khóa học -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Mô tả khóa học</h5>
                    </div>
                    <div class="card-body">
                        <p><?php echo nl2br(htmlspecialchars($course['course_description'])); ?></p>
                    </div>
                </div>

                <!-- Nội dung khóa học -->
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Nội dung khóa học</h5>
                    </div>
                    <div class="card-body">
                        <?php if (mysqli_num_rows($topics_result) > 0): ?>
                            <div class="accordion" id="courseAccordion">
                                <?php 
                                $topic_index = 0;
                                while ($topic = mysqli_fetch_assoc($topics_result)): 
                                    $topic_index++;
                                ?>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading<?php echo $topic_index; ?>">
                                            <button class="accordion-button collapsed" type="button" 
                                                    data-bs-toggle="collapse" 
                                                    data-bs-target="#collapse<?php echo $topic_index; ?>" 
                                                    aria-expanded="false" 
                                                    aria-controls="collapse<?php echo $topic_index; ?>">
                                                <?php echo $topic['title_topic']; ?>
                                                <span class="badge bg-secondary ms-2"><?php echo $topic['content_count']; ?> nội dung</span>
                                            </button>
                                        </h2>
                                        <div id="collapse<?php echo $topic_index; ?>" 
                                             class="accordion-collapse collapse" 
                                             aria-labelledby="heading<?php echo $topic_index; ?>" 
                                             data-bs-parent="#courseAccordion">
                                            <div class="accordion-body">
                                                <?php
                                                // Lấy nội dung chi tiết của chủ đề
                                                $contents_sql = "SELECT * FROM course_contents 
                                                               WHERE topic_id = {$topic['topic_id']} 
                                                               ORDER BY created_at";
                                                $contents_result = mysqli_query($dbconnect, $contents_sql);
                                                
                                                if (mysqli_num_rows($contents_result) > 0):
                                                    while ($content = mysqli_fetch_assoc($contents_result)):
                                                        $badge_color = '';
                                                        switch($content['content_type']) {
                                                            case 'video': $badge_color = 'bg-danger'; break;
                                                            case 'file': $badge_color = 'bg-primary'; break;
                                                            case 'embed': $badge_color = 'bg-warning'; break;
                                                            case 'text': $badge_color = 'bg-success'; break;
                                                            default: $badge_color = 'bg-secondary';
                                                        }
                                                ?>
                                                        <div class="d-flex justify-content-between align-items-center p-2 border-bottom">
                                                            <div>
                                                                <span class="badge <?php echo $badge_color; ?> content-type-badge me-2">
                                                                    <?php echo strtoupper($content['content_type']); ?>
                                                                </span>
                                                                <?php echo htmlspecialchars($content['title_content']); ?>
                                                            </div>
                                                            <?php if (!isset($_SESSION['user_id']) || !$has_joined): ?>
                                                                <span class="badge bg-light text-dark">
                                                                    <?php echo $is_free ? 'Đăng nhập để xem' : 'Đăng ký để xem'; ?>
                                                                </span>
                                                            <?php endif; ?>
                                                        </div>
                                                <?php 
                                                    endwhile;
                                                else:
                                                    echo "<p class='text-muted'>Chưa có nội dung trong chủ đề này.</p>";
                                                endif;
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">Khóa học chưa có nội dung.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Thông tin bổ sung -->
            <div class="col-md-4">
                <!-- Lịch học -->
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">Lịch học</h5>
                    </div>
                    <div class="card-body">
                        <?php if (mysqli_num_rows($schedule_result) > 0): ?>
                            <?php while ($schedule = mysqli_fetch_assoc($schedule_result)): ?>
                                <div class="schedule-card p-3 mb-2 bg-light rounded">
                                    <h6 class="mb-1"><?php echo getDayOfWeek($schedule['day_of_week']); ?></h6>
                                    <p class="mb-0">
                                        <?php echo date('H:i', strtotime($schedule['start_time'])); ?> - 
                                        <?php echo date('H:i', strtotime($schedule['end_time'])); ?>
                                    </p>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-muted">Chưa có lịch học.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Thống kê -->
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">Thông tin khóa học</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        // Thống kê số lượng nội dung theo loại
                        $stats_sql = "SELECT content_type, COUNT(*) as count 
                                     FROM course_contents cc
                                     INNER JOIN topics ct ON cc.topic_id = ct.topic_id
                                     WHERE ct.course_id = $course_id
                                     GROUP BY content_type";
                        $stats_result = mysqli_query($dbconnect, $stats_sql);
                        
                        $total_contents = 0;
                        $content_types = [];
                        while ($stat = mysqli_fetch_assoc($stats_result)) {
                            $content_types[$stat['content_type']] = $stat['count'];
                            $total_contents += $stat['count'];
                        }
                        ?>
                        
                        <p><strong>Tổng số nội dung:</strong> <?php echo $total_contents; ?></p>
                        <?php foreach($content_types as $type => $count): ?>
                            <p class="mb-1">
                                <strong><?php echo ucfirst($type); ?>:</strong> 
                                <?php echo $count; ?>
                            </p>
                        <?php endforeach; ?>
                        
                        <hr>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include("../footer.php"); ?>
    
    <script>
        // Thêm hiệu ứng cho accordion
        document.addEventListener('DOMContentLoaded', function() {
            const accordionItems = document.querySelectorAll('.accordion-button');
            accordionItems.forEach(item => {
                item.addEventListener('click', function() {
                    this.classList.toggle('collapsed');
                });
            });
        });
    </script>
</body>
</html>