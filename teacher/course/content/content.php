<?php
// content_fixed.php
// Secureed and cleaned version of your content.php

include_once('../layout.php');
include_once('../../../config/connect.php');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// --- Validate course_id ---
if (isset($_GET['course_id'])) {
    $course_id = intval($_GET['course_id']);
    $_SESSION['course_id'] = $course_id;
} elseif (isset($_SESSION['course_id'])) {
    $course_id = intval($_SESSION['course_id']);
} else {
    // No course id: stop and show a friendly message
    echo "<div class=\"container mt-4\"><div class=\"alert alert-danger\">Không tìm thấy <strong>course_id</strong>. Vui lòng chọn khóa học.</div></div>";
    include("../../../footer.php");
    exit;
}

// --- Handle search safely using prepared statements ---
$search_mode = false;
$search_display = '';
if ($_SERVER["REQUEST_METHOD"] === 'POST' && isset($_POST['timkiem'])) {
    $tukhoa = isset($_POST['tukhoa']) ? trim($_POST['tukhoa']) : '';
    $search_display = $tukhoa;
    $keyword = strtolower(str_replace(' ', '', $tukhoa));
    if ($keyword !== '') {
        $search_mode = true;
    }
}

// Prepare topics query
if ($search_mode) {
    // We'll perform a normalized search on title_topic (remove spaces and normalize Đ/đ)
    $sql = "SELECT * FROM topics WHERE course_id = ? AND (
        LOWER(REPLACE(REPLACE(REPLACE(title_topic, 'Đ', 'D'), 'đ', 'd'), ' ', '')) LIKE ?
        OR title_topic LIKE ?
    ) ORDER BY topic_id ASC";
    $stmt = mysqli_prepare($dbconnect, $sql);
    $like1 = '%' . $keyword . '%';
    $like2 = '%' . $tukhoa . '%';
    mysqli_stmt_bind_param($stmt, 'iss', $course_id, $like1, $like2);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $sql = "SELECT * FROM topics WHERE course_id = ? ORDER BY topic_id ASC";
    $stmt = mysqli_prepare($dbconnect, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $course_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nội dung khóa học</title>
    <!-- Thêm Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
    .accordion-button:not(.collapsed) {
        background-color: #f8f9fa;
        color: #333;
    }
    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .list-group-item {
        border-radius: 0.5rem;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .list-group-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .content-icon {
        font-size: 1.2rem;
        margin-right: 8px;
        vertical-align: middle;
    }
    .content-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 0.7rem;
    }
    .card-title {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }
    .action-buttons {
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #eee;
    }
    </style>
</head>
<body>
<header class="container mt-4">
    <div class="row">
        <div class="col-md-6">
            <h3>Nội dung khóa học</h3>
        </div>
        <div class="col-md-4">
            <form class="d-flex" action="content.php" method="POST">
                <div class="input-group mb-3">
                    <input type="search" class="form-control" placeholder="Tìm kiếm ..." name="tukhoa" value="<?php echo htmlspecialchars($search_display); ?>">
                    <button class="btn btn-dark" type="submit" name="timkiem">Tìm</button>
                </div>
            </form>
            <?php if ($search_mode) : ?>
                <div class="row mt-3"><div class="col">
                    <p>Tìm kiếm với từ khóa: '<strong><?php echo htmlspecialchars($search_display); ?></strong>'</p>
                </div></div>
            <?php endif; ?>
        </div>
        <div class="col-md-2">
            <a class="btn btn-primary float-end" href="add_content_heading.php">
                <i class="bi bi-plus-circle me-1"></i>Tạo chủ đề mới
            </a>
        </div>
    </div>
</div>

<!-- Legend cho các loại nội dung -->
<div class="container mb-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body py-2">
                    <div class="d-flex flex-wrap gap-3">
                        <small><i class="bi bi-camera-video-fill text-danger content-icon"></i> Video</small>
                        <small><i class="bi bi-play-btn-fill text-warning content-icon"></i> Video nhúng</small>
                        <small><i class="bi bi-file-text-fill text-primary content-icon"></i> Văn bản</small>
                        <small><i class="bi bi-file-earmark-fill text-success content-icon"></i> Tài liệu</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container mt-3" style="max-height: 80vh; overflow-y: auto;">
    <?php if (!$result || mysqli_num_rows($result) === 0) : ?>
        <div class="alert alert-info text-center">
            <i class="bi bi-info-circle display-4 text-info"></i>
            <p class="mt-2">Không có chủ đề nào trong khóa học này.</p>
            <a href="add_content_heading.php" class="btn btn-primary mt-2">
                <i class="bi bi-plus-circle me-1"></i>Tạo chủ đề đầu tiên
            </a>
        </div>
    <?php endif; ?>

    <div class="accordion" id="topicsAccordion">
        <?php while ($row_topic = mysqli_fetch_assoc($result)) :
            $topic_id = intval($row_topic['topic_id']);
            $sql_content = "SELECT ct.*, vc.video_id, vc.video_url, ec.embedded_id, ec.embed_code, fc.file_id, fc.file_name, tc.text_id, tc.text_content
                FROM course_contents ct
                LEFT JOIN video_contents vc ON ct.contents_id = vc.course_content_id AND ct.content_type = 'video'
                LEFT JOIN embedded_contents ec ON ct.contents_id = ec.course_content_id AND ct.content_type = 'embed'
                LEFT JOIN file_contents fc ON ct.contents_id = fc.course_content_id AND ct.content_type = 'file'
                LEFT JOIN text_contents tc ON ct.contents_id = tc.course_content_id AND ct.content_type = 'text'
                WHERE ct.topic_id = ? ORDER BY ct.contents_id ASC";
            $stmt2 = mysqli_prepare($dbconnect, $sql_content);
            mysqli_stmt_bind_param($stmt2, 'i', $topic_id);
            mysqli_stmt_execute($stmt2);
            $result_content = mysqli_stmt_get_result($stmt2);
        ?>
        <div class="accordion-item mb-3 shadow-sm">
            <h2 class="accordion-header" id="heading<?php echo $topic_id; ?>">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $topic_id; ?>" aria-expanded="false" aria-controls="collapse<?php echo $topic_id; ?>">
                    <i class="bi bi-folder-fill text-warning me-2"></i>
                    <?php echo htmlspecialchars($row_topic['title_topic']); ?>
                </button>
            </h2>
            <div id="collapse<?php echo $topic_id; ?>" class="accordion-collapse collapse" aria-labelledby="heading<?php echo $topic_id; ?>" data-bs-parent="#topicsAccordion">
                <div class="accordion-body">
                    <?php if (!empty($row_topic['description'])): ?>
                        <div class="ql-editor topic-description mb-3">
                            <?= $row_topic['description']; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($result_content && mysqli_num_rows($result_content) > 0) : ?>
                        <div class="row g-3 mt-2">
                            <?php while ($row = mysqli_fetch_assoc($result_content)) : ?>
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="card h-100 shadow-sm position-relative">
                                        <div class="card-body d-flex flex-column">
                                            <!-- Badge hiển thị loại nội dung -->
                                            <span class="content-badge badge 
                                                <?php 
                                                switch ($row['content_type']) {
                                                    case 'video': echo 'bg-danger'; break;
                                                    case 'embed': echo 'bg-warning'; break;
                                                    case 'text': echo 'bg-primary'; break;
                                                    case 'file': echo 'bg-success'; break;
                                                    default: echo 'bg-secondary'; break;
                                                }
                                                ?>">
                                                <?php echo htmlspecialchars($row['content_type']); ?>
                                            </span>
                                            
                                            <h6 class="card-title">
                                                <?php
                                                $icon = "";
                                                switch ($row['content_type']) {
                                                    case 'video': 
                                                        $icon = "<i class='bi bi-camera-video-fill text-danger content-icon'></i>"; 
                                                        break;
                                                    case 'embed': 
                                                        $icon = "<i class='bi bi-play-btn-fill text-warning content-icon'></i>"; 
                                                        break;
                                                    case 'text': 
                                                        $icon = "<i class='bi bi-file-text-fill text-primary content-icon'></i>"; 
                                                        break;
                                                    case 'file': 
                                                        $icon = "<i class='bi bi-file-earmark-fill text-success content-icon'></i>"; 
                                                        break;
                                                    default: 
                                                        $icon = "<i class='bi bi-question-circle-fill content-icon'></i>"; 
                                                        break;
                                                }
                                                echo $icon . htmlspecialchars($row['title_content']);
                                                ?>
                                            </h6>
                                            
                                            <?php if (!empty($row['duration'])): ?>
                                                <small class="text-muted mb-2">
                                                    <i class="bi bi-clock me-1"></i>
                                                    Thời lượng: <?php echo htmlspecialchars($row['duration']); ?>
                                                </small>
                                            <?php endif; ?>

                                            <div class="mt-auto">
                                                <?php
                                                $url = "";
                                                $button_text = "Xem nội dung";
                                                switch ($row['content_type']) {
                                                    case 'embed': 
                                                        $url = !empty($row['embedded_id']) ? 'view_content_video.php?content_id=' . urlencode($row['embedded_id']) : ''; 
                                                        $button_text = "Xem video";
                                                        break;
                                                    case 'video': 
                                                        $url = !empty($row['video_id']) ? 'view_content_file_vd.php?content_id=' . urlencode($row['video_id']) : ''; 
                                                        $button_text = "Xem video";
                                                        break;
                                                    case 'text': 
                                                        $url = !empty($row['text_id']) ? 'view_content_text.php?content_id=' . urlencode($row['text_id']) : ''; 
                                                        $button_text = "Đọc tài liệu";
                                                        break;
                                                    case 'file': 
                                                        $url = !empty($row['file_id']) ? 'view_content_file.php?content_id=' . urlencode($row['file_id']) : ''; 
                                                        $button_text = "Tải xuống";
                                                        break;
                                                }
                                                if (!empty($url)) {
                                                    echo "<a class='btn btn-sm btn-outline-primary w-100 mt-2' href='$url'>";
                                                    echo "<i class='bi bi-eye me-1'></i>";
                                                    echo $button_text;
                                                    echo "</a>";
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-muted mt-2 text-center py-4">
                            <i class="bi bi-inbox display-4 text-muted"></i>
                            <p class="mt-2">Chưa có nội dung trong chủ đề này</p>
                        </div>
                    <?php endif; ?>

                    <!-- Các nút hành động cho giáo viên -->
                    <div class="action-buttons d-flex justify-content-end gap-2">
                        <a class="btn btn-sm btn-primary" href="add_content_in_heading.php?topic_id=<?php echo urlencode($row_topic['topic_id']); ?>">
                            <i class="bi bi-plus-circle me-1"></i>Thêm
                        </a>
                        <a class="btn btn-sm btn-warning" href="edit_content.php?topic_id=<?php echo urlencode($row_topic['topic_id']); ?>">
                            <i class="bi bi-pencil me-1"></i>Sửa
                        </a>
                        <button class="btn btn-sm btn-danger delete-topic-btn" data-topicid="<?php echo htmlspecialchars($row_topic['topic_id']); ?>" data-bs-toggle="modal" data-bs-target="#deleteTopicModal">
                            <i class="bi bi-trash me-1"></i>Xóa
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- Delete Topic Modal -->
<div class="modal fade" id="deleteTopicModal" tabindex="-1" aria-labelledby="deleteTopicModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteTopicModalLabel">Xác nhận xóa chủ đề</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa chủ đề này? Tất cả nội dung bên trong cũng sẽ bị xóa.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <form id="deleteTopicForm" method="post" action="../process.php">
                    <input type="hidden" name="topic_id" id="delete_topic_id" value="">
                    <input type="hidden" name="delete_topic" value="1">
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>Xóa
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include("../../../footer.php"); ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteTopicButtons = document.querySelectorAll('.delete-topic-btn');
    const deleteTopicIdInput = document.getElementById('delete_topic_id');

    deleteTopicButtons.forEach(button => {
        button.addEventListener('click', function() {
            const topicId = this.getAttribute('data-topicid');
            if (deleteTopicIdInput) deleteTopicIdInput.value = topicId;
        });
    });
});
</script>

</body>
</html>