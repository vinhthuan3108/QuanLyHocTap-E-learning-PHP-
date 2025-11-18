<?php
include_once('../layout.php');
include_once('../../../config/connect.php');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$course_id = isset($_GET['course_id']) ? $_GET['course_id'] : $_SESSION['course_id'];
$topic_id = isset($_GET['topic_id']) ? intval($_GET['topic_id']) : 0;

// Lấy thông tin chủ đề
$sql_topic = "SELECT * FROM topics WHERE topic_id=?";
$stmt_topic = mysqli_prepare($dbconnect, $sql_topic);
mysqli_stmt_bind_param($stmt_topic, "i", $topic_id);
mysqli_stmt_execute($stmt_topic);
$result_topic = mysqli_stmt_get_result($stmt_topic);
$topic = mysqli_fetch_assoc($result_topic);
if (!$topic) die("Chủ đề không tồn tại.");

// Xử lý POST sửa chủ đề
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_topic'])) {
    $title_topic = $_POST['topicTitle'];
    $description = $_POST['topicDescription'];
    $sql_update = "UPDATE topics SET title_topic=?, description=? WHERE topic_id=?";
    $stmt_update = mysqli_prepare($dbconnect, $sql_update);
    mysqli_stmt_bind_param($stmt_update, "ssi", $title_topic, $description, $topic_id);
    mysqli_stmt_execute($stmt_update);
    header("Location: edit_content.php?topic_id=$topic_id");
    exit;
}

// Xử lý tìm kiếm
$search_keyword = "";
$where_search = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['timkiem'])) {
    $search_keyword = trim($_POST['tukhoa']);
    if (!empty($search_keyword)) {
        $search_keyword = "%".strtolower($search_keyword)."%";
        $where_search = " AND LOWER(title_content) LIKE ?";
    }
}

// Lấy nội dung
$sql_content = "SELECT ct.*, vc.video_id, vc.video_url, ec.embedded_id, ec.embed_code, fc.file_id, fc.file_name, tc.text_id, tc.text_content
FROM course_contents ct
LEFT JOIN video_contents vc ON ct.contents_id = vc.course_content_id AND ct.content_type='video'
LEFT JOIN embedded_contents ec ON ct.contents_id = ec.course_content_id AND ct.content_type='embed'
LEFT JOIN file_contents fc ON ct.contents_id = fc.course_content_id AND ct.content_type='file'
LEFT JOIN text_contents tc ON ct.contents_id = tc.course_content_id AND ct.content_type='text'
WHERE ct.topic_id=? $where_search
ORDER BY ct.contents_id ASC";

$stmt_content = mysqli_prepare($dbconnect, $sql_content);
if (!empty($search_keyword)) {
    mysqli_stmt_bind_param($stmt_content, "is", $topic_id, $search_keyword);
} else {
    mysqli_stmt_bind_param($stmt_content, "i", $topic_id);
}
mysqli_stmt_execute($stmt_content);
$result_content = mysqli_stmt_get_result($stmt_content);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sửa chủ đề và nội dung</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.0-rc.2/dist/quill.snow.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
.table td, .table th { vertical-align: middle; }
.mark { background-color: yellow; }
</style>
</head>
<body>
<div class="container mt-4">

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3><i class="bi bi-pencil-square me-2"></i>Sửa Chủ đề</h3>
    <a href="content.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left-circle"></i> Quay lại</a>
</div>

<!-- Form sửa chủ đề -->
<div class="card mb-4 shadow-sm">
    <div class="card-body">
        <form action="" method="post">
            <div class="mb-3">
                <label for="topicTitle" class="form-label">Tiêu đề chủ đề</label>
                <input type="text" class="form-control" id="topicTitle" name="topicTitle" value="<?php echo htmlspecialchars($topic['title_topic']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="topicDescription" class="form-label">Mô tả chủ đề</label>
                <div id="editor-container" class="border p-2"><?php echo $topic['description']; ?></div>
                <textarea name="topicDescription" id="topicDescription" style="display:none;" required></textarea>
            </div>
            <button type="submit" name="update_topic" class="btn btn-primary"><i class="bi bi-save me-1"></i> Cập nhật</button>
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteTopicModal"><i class="bi bi-trash me-1"></i> Xóa chủ đề</button>
        </form>
    </div>
</div>

<!-- Form tìm kiếm -->
<form method="post" class="mb-3 d-flex">
    <input type="text" name="tukhoa" class="form-control me-2" placeholder="Tìm kiếm nội dung..." value="<?php echo isset($_POST['tukhoa']) ? htmlspecialchars($_POST['tukhoa']) : ''; ?>">
    <button class="btn btn-dark" type="submit" name="timkiem">Tìm</button>
</form>

<!-- Table nội dung -->
<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Tiêu đề nội dung</th>
                    <th>Loại</th>
                    <th class="d-flex justify-content-between align-items-center">
                        Hành động
                        <a href="add_content_in_heading.php?topic_id=<?php echo $topic_id; ?>&edit=1" class="btn btn-sm btn-success ms-2">
                            <i class="fa-solid fa-plus me-1"></i> Thêm
                        </a>
                    </th>
                </tr>
            </thead>
            <tbody>
            <?php
            if (mysqli_num_rows($result_content) == 0):
            ?>
                <tr>
                    <td colspan="3" class="text-center text-muted py-4">
                        <i class="fa-solid fa-circle-exclamation me-2"></i>Chưa có nội dung nào
                    </td>
                </tr>
            <?php
            else:
                while ($row = mysqli_fetch_assoc($result_content)):

                    $icon = '';
                    $edit_link = '#';
                    $view_link = '#';

                    switch ($row['content_type']) {
                        case 'video':
                            $icon = "<i class='fa-solid fa-film text-danger me-1'></i>";
                            $edit_link = "edit_content_in_heading.php?content_id={$row['contents_id']}&topic_id={$row['topic_id']}&nav_id=1";
                            $view_link = "view_content_file_vd.php?content_id={$row['video_id']}";
                            break;
                        case 'embed':
                            $icon = "<i class='fa-solid fa-play text-warning me-1'></i>";
                            $edit_link = "edit_content_in_heading.php?content_id={$row['contents_id']}&topic_id={$row['topic_id']}&nav_id=2";
                            $view_link = "view_content_video.php?content_id={$row['embedded_id']}";
                            break;
                        case 'text':
                            $icon = "<i class='fa-solid fa-file-pen text-primary me-1'></i>";
                            $edit_link = "edit_content_in_heading.php?content_id={$row['contents_id']}&topic_id={$row['topic_id']}&nav_id=4";
                            $view_link = "view_content_text.php?content_id={$row['text_id']}";
                            break;
                        case 'file':
                            $icon = "<i class='fa-solid fa-file-invoice text-success me-1'></i>";
                            $edit_link = "edit_content_in_heading.php?content_id={$row['contents_id']}&topic_id={$row['topic_id']}&nav_id=3";
                            $view_link = "view_content_file.php?content_id={$row['file_id']}";
                            break;
                        default:
                            $icon = "<i class='fa-solid fa-question-circle me-1'></i>";
                            break;
                    }

                    // Highlight từ khóa tìm kiếm
                    $title_display = htmlspecialchars($row['title_content']);
                    if (!empty($_POST['tukhoa'])) {
                        $keyword = htmlspecialchars($_POST['tukhoa']);
                        $title_display = preg_replace("/(" . preg_quote($keyword, '/') . ")/i", '<mark>$1</mark>', $title_display);
                    }
            ?>
                <tr>
                    <td><?php echo $icon . $title_display; ?></td>
                    <td><?php echo htmlspecialchars($row['content_type']); ?></td>
                    <td>
                        <a class="btn btn-sm btn-info me-1" href="<?php echo $edit_link; ?>"><i class="bi bi-pencil-square"></i> Sửa</a>
                        <a class="btn btn-sm btn-secondary me-1" href="<?php echo $view_link; ?>"><i class="bi bi-eye"></i> Xem</a>
                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteContentModal" data-contentid="<?php echo $row['contents_id']; ?>"><i class="bi bi-trash"></i> Xóa</button>
                    </td>
                </tr>
            <?php
                endwhile;
            endif;
            ?>
            </tbody>

        </table>
    </div>
</div>


<!-- Modal xóa nội dung -->
<div class="modal fade" id="deleteContentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Xác nhận xóa nội dung</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">Bạn có chắc chắn muốn xóa nội dung này?</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
        <form action="../process.php" method="post">
            <input type="hidden" name="content_id" id="deleteContentId">
            <input type="hidden" name="topic_id" value="<?php echo $topic_id; ?>">
            <button type="submit" class="btn btn-danger" name="delete_content">Xác nhận</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal xóa chủ đề -->
<div class="modal fade" id="deleteTopicModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Xác nhận xóa chủ đề</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">Bạn có chắc chắn muốn xóa chủ đề này?</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
        <form action="../process.php" method="post">
            <input type="hidden" name="topic_id" value="<?php echo $topic_id; ?>">
            <button type="submit" class="btn btn-danger" name="delete_topic">Xác nhận</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/quill@2.0.0-rc.2/dist/quill.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const quill = new Quill('#editor-container', {
    theme:'snow',
    modules:{ toolbar:[['bold','italic'],['link','image'],[{list:'ordered'},{list:'bullet'}]] }
});
quill.on('text-change', () => document.getElementById('topicDescription').value = quill.root.innerHTML);
document.getElementById('topicDescription').value = quill.root.innerHTML;

// Xử lý nút xóa nội dung
const deleteModal = document.getElementById('deleteContentModal');
deleteModal.addEventListener('show.bs.modal', e => {
    document.getElementById('deleteContentId').value = e.relatedTarget.getAttribute('data-contentid');
});
</script>

<?php include("../../../footer.php"); ?>
</body>
</html>
