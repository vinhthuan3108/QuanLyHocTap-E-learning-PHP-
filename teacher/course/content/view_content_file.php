<?php
include("../layout.php");
include_once('../../../config/connect.php');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$file_id = $_GET['content_id'];
$sql = "SELECT * FROM file_contents fc
INNER JOIN course_contents ct ON ct.contents_id = fc.course_content_id
INNER JOIN topics tp ON ct.topic_id = tp.topic_id
INNER JOIN course c ON tp.course_id = c.course_id
INNER JOIN user us ON us.user_id = c.teacher_id
WHERE fc.file_id = $file_id";
$result = mysqli_query($dbconnect, $sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <title>Tài liệu học tập</title>
    <style>
        .file-preview {
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 20px;
            background-color: #f8f9fa;
            min-height: 500px;
        }
        .file-icon {
            font-size: 4rem;
            color: #6c757d;
            margin-bottom: 20px;
        }
        .file-info {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .preview-container {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
        }
        .pdf-preview {
            width: 100%;
            height: 700px;
            border: none;
        }
        .image-preview {
            max-width: 100%;
            max-height: 600px;
            display: block;
            margin: 0 auto;
        }
        .text-preview {
            white-space: pre-wrap;
            font-family: monospace;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            max-height: 600px;
            overflow-y: auto;
        }
        .action-buttons {
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <?php while ($row_file = mysqli_fetch_assoc($result)) : 
        $file_path = '../../../assets/study_files/' . $row_file['file_name'];
        $file_extension = strtolower(pathinfo($row_file['file_name'], PATHINFO_EXTENSION));
        $file_icon = getFileIcon($file_extension);
        $can_preview = canPreviewFile($file_extension);
    ?>
    <header class="container mt-4">
        <div class="row">
            <h3>
                <a href="content.php" class="text-decoration-none">
                    <i class="bi bi-arrow-left-circle"></i>
                </a>
                <?php echo htmlspecialchars($row_file['title_content']); ?>
            </h3>
        </div>
    </header>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-6">
                <p>
                    <b>Tạo bởi: </b> 
                    <img src="../../../assets/images/<?php echo htmlspecialchars($row_file['image']); ?>" 
                         alt="Avatar" 
                         class="img-fluid rounded-circle" 
                         style="width: 30px; height: 30px; object-fit: cover;">
                    <span><?php echo htmlspecialchars($row_file['full_name']); ?></span>
                </p>
            </div>
            <div class="col-md-6">
                <p class="float-end">
                    <b>Ngày tạo: </b><?php echo date('d/m/Y', strtotime($row_file['created_at'])); ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Phần thông tin tài liệu - ĐÃ CHUYỂN LÊN TRÊN -->
    <div class="container mt-4">
        <div class="file-info">
            <div class="row">
                <div class="col-md-8">
                    <h5>Thông tin tài liệu</h5>
                    <hr>
                    <p><strong>Tiêu đề:</strong> <?php echo htmlspecialchars($row_file['title_content']); ?></p>
                    <p><strong>Loại file:</strong> <?php echo strtoupper($file_extension); ?></p>
                    <p><strong>Kích thước:</strong> <?php echo formatFileSize($row_file['file_size']); ?></p>
                    <p><strong>Mô tả:</strong></p>
                    <p><?php echo nl2br(htmlspecialchars($row_file['description_content'])); ?></p>
                </div>
                <div class="col-md-4 text-center">
                    <div class="file-icon">
                        <i class="bi <?php echo $file_icon; ?>"></i>
                    </div>
                    <h5><?php echo htmlspecialchars($row_file['file_name']); ?></h5>
                    <p class="text-muted"><?php echo formatFileSize($row_file['file_size']); ?></p>
                    
                    <div class="action-buttons">
                        <a href="../../../assets/study_files/<?php echo htmlspecialchars($row_file['file_name']); ?>" 
                           class="btn btn-primary me-2" 
                           download>
                            <i class="bi bi-download"></i> Tải xuống
                        </a>
                        <?php if ($can_preview): ?>
                        <button class="btn btn-outline-primary" onclick="togglePreview()">
                            <i class="bi bi-eye"></i> Xem trước
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Phần xem trước - CHIẾM TOÀN BỘ CHIỀU RỘNG -->
        <div class="row">
            <div class="col-12">
                <?php if ($can_preview): ?>
                <div id="previewArea" class="preview-container" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Xem trước tài liệu</h5>
                        <button class="btn btn-sm btn-outline-secondary" onclick="togglePreview()">
                            <i class="bi bi-x"></i> Đóng xem trước
                        </button>
                    </div>
                    <hr>
                    <?php echo renderFilePreview($file_path, $file_extension); ?>
                </div>
                <?php else: ?>
                <div class="alert alert-warning text-center">
                    <i class="bi bi-exclamation-triangle"></i>
                    Không thể xem trước loại file này. Vui lòng tải xuống để xem.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endwhile; ?>

    <script>
        function togglePreview() {
            const previewArea = document.getElementById('previewArea');
            if (previewArea.style.display === 'none') {
                previewArea.style.display = 'block';
                // Cuộn đến khu vực xem trước
                previewArea.scrollIntoView({ behavior: 'smooth' });
            } else {
                previewArea.style.display = 'none';
            }
        }

        // Tự động mở xem trước nếu file là PDF hoặc hình ảnh
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('autopreview') === '1') {
                togglePreview();
            }
        });
    </script>

    <?php 
    // Hàm kiểm tra xem file có thể xem trước không
    function canPreviewFile($extension) {
        $previewable = ['pdf', 'txt', 'jpg', 'jpeg', 'png', 'gif', 'bmp', 'html', 'htm', 'csv'];
        return in_array($extension, $previewable);
    }

    // Hàm hiển thị xem trước file
    function renderFilePreview($file_path, $extension) {
        if (!file_exists($file_path)) {
            return '<div class="alert alert-danger">File không tồn tại</div>';
        }

        switch ($extension) {
            case 'pdf':
                return '<iframe src="' . $file_path . '" class="pdf-preview" title="PDF Preview"></iframe>';
            
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
            case 'bmp':
                return '<img src="' . $file_path . '" class="image-preview" alt="Image Preview">';
            
            case 'txt':
                $content = file_get_contents($file_path);
                // Giới hạn hiển thị 10000 ký tự để tránh quá tải
                if (strlen($content) > 10000) {
                    $content = substr($content, 0, 10000) . "\n\n...[Nội dung bị cắt bớt]";
                }
                return '<div class="text-preview">' . htmlspecialchars($content) . '</div>';
            
            case 'html':
            case 'htm':
                $content = file_get_contents($file_path);
                // Giới hạn cho file HTML
                if (strlen($content) > 50000) {
                    return '<div class="alert alert-info">File HTML quá lớn để xem trước. Vui lòng tải xuống.</div>';
                }
                return '<div class="border p-3 bg-light"><pre>' . htmlspecialchars($content) . '</pre></div>';
            
            case 'csv':
                return renderCSVPreview($file_path);
            
            default:
                return '<div class="alert alert-info">Chức năng xem trước cho loại file này đang được phát triển.</div>';
        }
    }

    // Hàm hiển thị xem trước CSV
    function renderCSVPreview($file_path) {
        $handle = fopen($file_path, 'r');
        $html = '<div class="table-responsive"><table class="table table-bordered table-sm">';
        $row_count = 0;
        
        while (($data = fgetcsv($handle)) !== FALSE && $row_count < 50) { // Giới hạn 50 dòng
            $html .= '<tr>';
            foreach ($data as $cell) {
                if ($row_count === 0) {
                    $html .= '<th>' . htmlspecialchars($cell) . '</th>';
                } else {
                    $html .= '<td>' . htmlspecialchars($cell) . '</td>';
                }
            }
            $html .= '</tr>';
            $row_count++;
        }
        
        fclose($handle);
        $html .= '</table></div>';
        
        if ($row_count >= 50) {
            $html .= '<div class="text-muted">...[Chỉ hiển thị 50 dòng đầu tiên]</div>';
        }
        
        return $html;
    }

    // Hàm lấy icon tương ứng với loại file
    function getFileIcon($extension) {
        $extension = strtolower($extension);
        switch ($extension) {
            case 'pdf':
                return 'bi-file-earmark-pdf';
            case 'doc':
            case 'docx':
                return 'bi-file-earmark-word';
            case 'xls':
            case 'xlsx':
                return 'bi-file-earmark-excel';
            case 'ppt':
            case 'pptx':
                return 'bi-file-earmark-ppt';
            case 'zip':
            case 'rar':
            case '7z':
                return 'bi-file-earmark-zip';
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
            case 'bmp':
                return 'bi-file-earmark-image';
            case 'txt':
                return 'bi-file-earmark-text';
            case 'csv':
                return 'bi-file-earmark-spreadsheet';
            default:
                return 'bi-file-earmark';
        }
    }
    
    // Hàm định dạng kích thước file
    function formatFileSize($size_kb) {
        if ($size_kb < 1024) {
            return number_format($size_kb, 2) . ' KB';
        } elseif ($size_kb < 1048576) {
            return number_format($size_kb / 1024, 2) . ' MB';
        } else {
            return number_format($size_kb / 1048576, 2) . ' GB';
        }
    }
    ?>

    <?php include("../../../footer.php"); ?>
</body>

</html>