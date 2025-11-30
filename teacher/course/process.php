<?php
include_once('../../config/connect.php');
include_once('../../config/connect.php');
// require '../../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


// use PHPMailer\PHPMailer\SMTP;
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['delete_course'])) {
    // Lấy course_id từ SESSION
    $course_id = $_SESSION['course_id'];

    // Thực hiện thao tác xóa khóa học từ cơ sở dữ liệu
    $sql_delete_course = "DELETE FROM course WHERE course_id = $course_id";
    $result_delete_course = mysqli_query($dbconnect, $sql_delete_course);

    if ($result_delete_course) {

        mysqli_close($dbconnect);
        header("location: ../courses.php");
    } else {
        // Xóa thất bại, xử lý lỗi hoặc thông báo cho người dùng
        echo "Không thể xóa khóa học. Lỗi: " . mysqli_error($dbconnect);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["change_cover"])) {
    $course_id =  $_SESSION['course_id'];
    if ($_FILES['new_cover']['size'] > 0) {
        $file_name = $_FILES['new_cover']['name'];
        $file_size = $_FILES['new_cover']['size'];
        $file_tmp = $_FILES['new_cover']['tmp_name'];
        $file_type = $_FILES['new_cover']['type'];

        $upload_dir = "../../assets/file/course_background/";
        $target_file = $upload_dir . basename($file_name);

        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');
        if (!in_array($imageFileType, $allowed_extensions)) {
            echo "Chỉ chấp nhận tệp ảnh có định dạng JPG, JPEG, PNG, hoặc GIF.";
            exit();
        }
        $max_file_size = 20 * 1024 * 1024;
        if ($file_size > $max_file_size) {
            echo "Kích thước tệp ảnh quá lớn. Vui lòng chọn một tệp ảnh nhỏ hơn.";
            exit();
        }

        if (move_uploaded_file($file_tmp, $target_file)) {
            $sql_update_cover = "UPDATE course SET course_background = '$file_name' WHERE course_id = $course_id";
            mysqli_query($dbconnect, $sql_update_cover);
            echo "Cập nhật ảnh bìa thành công.";
        } else {
            echo "Đã xảy ra lỗi khi tải lên tệp ảnh.";
        }
        mysqli_close($dbconnect);
        header('location: course_details/edit_course.php');
    }
}

if (isset($_POST['edit_course'])) {
    // Lấy dữ liệu từ form
    $course_name = mysqli_real_escape_string($dbconnect, $_POST['course_name']);
    $course_code = mysqli_real_escape_string($dbconnect, $_POST['course_code']);
    $course_description = mysqli_real_escape_string($dbconnect, $_POST['course_description']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $course_id = $_SESSION['course_id'];
    $price = $_POST['price'];
    // Cập nhật thông tin khóa học trong cơ sở dữ liệu
    $update_course_query = "UPDATE course SET
                            course_name = '$course_name',
                            course_code = '$course_code',
                            course_description = '$course_description',
                            start_date = '$start_date',
                            end_date = '$end_date',
                            price = '$price'
                            WHERE course_id = $course_id";

    mysqli_query($dbconnect, $update_course_query);
    mysqli_close($dbconnect);
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_schedule"])) {
    $course_id = $_SESSION['course_id'];
    $dayOfWeeks = $_POST["dayOfWeek"];
    $startTimes = $_POST["startTime"];
    $endTimes = $_POST["endTime"];

    if (!empty($dayOfWeeks) && !empty($startTimes) && !empty($endTimes)) {
        $scheduleIdsToKeep = array();
        for ($i = 0; $i < count($dayOfWeeks); $i++) {
            $dayOfWeek = $dayOfWeeks[$i];
            $startTime = $startTimes[$i];
            $endTime = $endTimes[$i];
            switch ($dayOfWeek) {
                case "monday":
                    $dayOfWeekValue = "2";
                    break;
                case "tuesday":
                    $dayOfWeekValue = "3";
                    break;
                case "wednesday":
                    $dayOfWeekValue = "4";
                    break;
                case "thursday":
                    $dayOfWeekValue = "5";
                    break;
                case "friday":
                    $dayOfWeekValue = "6";
                    break;
                case "saturday":
                    $dayOfWeekValue = "7";
                    break;
                case "sunday":
                    $dayOfWeekValue = "C";
                    break;
            }

            if (isset($_POST['schedule_id'][$i])) {
                $schedule_id = $_POST['schedule_id'][$i];
                $scheduleIdsToKeep[] = $schedule_id;
                // Nếu có, thực hiện cập nhật thông tin thời khóa biểu
                $sql_schedule = "UPDATE course_schedule SET day_of_week ='$dayOfWeekValue', start_time='$startTime', end_time= '$endTime' where course_schedule_id = $schedule_id";
                mysqli_query($dbconnect, $sql_schedule);
            } else {
                // Nếu không, thực hiện thêm mới thông tin thời khóa biểu
                $sql_schedule = "INSERT INTO course_schedule (course_id, day_of_week, start_time, end_time) VALUES ('$course_id', '$dayOfWeekValue', '$startTime', '$endTime')";
                mysqli_query($dbconnect, $sql_schedule);
                $scheduleIdsToKeep[] = mysqli_insert_id($dbconnect);
            }
        }
        // Xóa thời khóa biểu không có trong khóa học
        $scheduleisset = implode(',', $scheduleIdsToKeep);
        $sql_delete_schedule = "DELETE FROM course_schedule WHERE course_id ='$course_id' AND course_schedule_id NOT IN ($scheduleisset)";
        mysqli_query($dbconnect, $sql_delete_schedule);

        header("Location: index.php");
        exit();
    } else {
        echo "Không có dữ liệu được gửi từ form.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["delete_member_course"])) {
        if (isset($_POST["delete_student_id"])) {
            $student_id = $_POST["delete_student_id"];

            $course_id = $_SESSION['course_id'];

            echo $course_id;
            echo $student_id . " - m";

            $delete_query = "DELETE FROM course_member WHERE course_id = $course_id AND student_id = $student_id";
            $delete_result = mysqli_query($dbconnect, $delete_query);

            if ($delete_result) {
                mysqli_close($dbconnect);
                header("Location: course_details/member.php");
                exit();
            } else {
                die('Deletion failed: ' . mysqli_error($dbconnect));
            }
        } else {
            echo "Error: delete_student_id not set";
        }
    }
} else {
    echo "Invalid request method";
}




//mới sữa


ob_start();

if (isset($_POST['create_post'])) {
    $user_id = $_SESSION["user_id"];
    $course_id = $_SESSION['course_id'];
    $title = mysqli_real_escape_string($dbconnect, $_POST["postTitle"]);
    $content = mysqli_real_escape_string($dbconnect, $_POST["postContent"]);


    $sql = "INSERT INTO post (user_id, course_id, title, content, created_at)
            VALUES ($user_id, $course_id, '$title', '$content', DEFAULT)";
    mysqli_query($dbconnect, $sql);

    $post_id = mysqli_insert_id($dbconnect);


    $sql_student = "SELECT u.email, u.full_name
                    FROM course_member cm
                    JOIN user u ON cm.student_id = u.user_id
                    WHERE cm.course_id = $course_id";
    $result_student = mysqli_query($dbconnect, $sql_student);


    $teacher = mysqli_fetch_assoc(mysqli_query($dbconnect, "SELECT full_name, email FROM user WHERE user_id = $user_id"));
    $teacher_name = $teacher['full_name'];
    $teacher_email = $teacher['email'];


    $post_time_query = mysqli_query($dbconnect, "SELECT created_at FROM post WHERE post_id = $post_id");
    $post_time_row = mysqli_fetch_assoc($post_time_query);
    $post_time_formatted = date("H:i - d/m/Y", strtotime($post_time_row['created_at']));


   $app_password = 'qsyi hdos gdou twnh';
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $teacher_email;
        $mail->Password = $app_password;
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        $mail->setFrom($teacher_email, $teacher_name);

        while ($row = mysqli_fetch_assoc($result_student)) {
            $mail->addAddress($row['email'], $row['full_name']);
        }

        $mail->isHTML(true);
        $mail->Subject = "Thông báo mới từ giáo viên $teacher_name";
       $mail->Body = '
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông báo mới</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; background-color: #f8f9fa; padding: 20px; }
        .header { background: linear-gradient(135deg, #007bff, #0056b3); color: white; padding: 20px; border-radius: 10px 10px 0 0; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { background: white; padding: 25px; border-radius: 0 0 10px 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .post-meta { background: #e9ecef; padding: 10px; border-radius: 5px; margin-bottom: 20px; font-size: 14px; color: #6c757d; }
        .post-body { font-size: 16px; line-height: 1.7; margin-bottom: 20px; }
        .post-body img { max-width: 100%; height: auto; border-radius: 5px; }
        .post-body table { border-collapse: collapse; width: 100%; }
        .post-body table, .post-body th, .post-body td { border: 1px solid #ddd; padding: 8px; }
        .post-body th { background-color: #f8f9fa; }
        .footer { text-align: center; padding-top: 20px; border-top: 1px solid #dee2e6; color: #6c757d; font-size: 12px; }
        @media (max-width: 600px) { body { padding: 10px; } .content { padding: 15px; } .post-body { font-size: 14px; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>📢 Thông báo mới</h1>
    </div>
    <div class="content">
        <div class="post-meta">
            <strong>Bởi:</strong> ' . htmlspecialchars($teacher_name) . ' <br>
            <strong>Thời gian:</strong> ' . $post_time_formatted . '  <!-- Thời gian từ DB, chính xác hôm nay -->
        </div>
        <div class="post-body">
            ' . $content . '  <!-- Sử dụng $content trực tiếp để giữ HTML từ Quill, không htmlspecialchars -->
        </div>
        <p style="margin-top: 20px; font-style: italic; color: #6c757d;">
            Truy cập hệ thống Elearning để xem chi tiết và tương tác.
        </p>
    </div>
    <div class="footer">
        <p>— Hệ thống Elearning (Tự động gửi) —</p>
        <p>Nếu bạn có câu hỏi, vui lòng liên hệ giáo viên qua hệ thống.</p>
    </div>
</body>
</html>
';


        $mail->send();
    } catch (Exception $e) {
        // chỉ log vào file, không echo ra màn hình
        error_log("Email không gửi được. Lỗi: {$mail->ErrorInfo}");
    }

    mysqli_close($dbconnect);

    // xoá buffer trước khi redirect
    ob_end_clean();

    header("Location: post/post.php");
    exit();
}




if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['edit_post'])) {
        $post_id = $_GET['post_id'];
        $title = $_POST["postTitle"];
        $content = $_POST["postContent"];
        $sql = "UPDATE post SET title = '$title', content = '$content', created_at = DEFAULT WHERE post_id = $post_id";
        mysqli_query($dbconnect, $sql);
        mysqli_close($dbconnect);
        header("Location: post/post.php");
        exit();
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_post'])) {
    $post_id = $_GET['post_id'];
    $sql_delete = "DELETE FROM post WHERE post_id = $post_id";
    $result_delete = mysqli_query($dbconnect, $sql_delete);
    if ($result_delete) {
        mysqli_close($dbconnect);
        header("location: post/post.php");
    } else {
        echo "Không thể xóa. Lỗi: " . mysqli_error($dbconnect);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["create_practice"])) {
    $title_practice = $_POST["title_practice"];
    $open_date = $_POST["open_date"];
    $close_date = $_POST["close_date"];
    $content_practice = $_POST["content_practice"];
    $type_submit = $_POST["type_submit"];

    // Check if a file was uploaded
    if (isset($_FILES["upload_file"]) && $_FILES["upload_file"]["error"] == 0) {
        $file_name = $_FILES["upload_file"]["name"];
        $file_tmp = $_FILES["upload_file"]["tmp_name"];
        $file_destination = "../../assets/file/content/" . $file_name;

        if (move_uploaded_file($file_tmp, $file_destination)) {
            echo "File uploaded successfully.";
        } else {
            echo "Error uploading file.";
        }
    } else {
        echo "No file uploaded.";
        $file_destination = null; // Set to null if no file is uploaded
    }

    $sql = "INSERT INTO practice (course_id, open_time, close_time, description, file_content, type_question, text_content)
            VALUES (NULL, '$open_date', '$close_date', '$title_practice', '$file_destination', '$type_submit', '$content_practice')";

    if ($dbconnect->query($sql) === TRUE) {
        mysqli_close($dbconnect);
        header("Location: exam/exam.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $dbconnect->error;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["create_topic"])) {
    $course_id = $_SESSION['course_id'];
    $title_topic = $_POST['topicTitle'];
    $topicdescription = $_POST['topicdescription'];
    $sql = "INSERT INTO topics (title_topic, course_id, description, created_by, created_at)
    VALUES ('$title_topic','$course_id','$topicdescription',NULL,DEFAULT)";
    $resul = mysqli_query($dbconnect, $sql);
    mysqli_close($dbconnect);
    header("location: content/content.php");
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["create_text"])) {
        $topic_id = $_GET['topic_id'];
        $titlecontent = $_POST['titlecontent'];
        $title_description = $_POST['title_description'];
        $sql = "INSERT INTO course_contents(topic_id, title_content, content_type, description_content ,created_by, created_at)
        VALUES ('$topic_id','$titlecontent','text','$title_description',NULL,DEFAULT)";
        if (mysqli_query($dbconnect, $sql)) {
            $content_id = mysqli_insert_id($dbconnect);
            echo "Thêm record thành công";
        } else {
            echo "Lỗi: " . $sql . "<br>" . mysqli_error($dbconnect);
        }
        $text_content = $_POST['contentText'];
        $sql_content_text = "INSERT INTO text_contents(course_content_id, text_content)
        VALUES ('$content_id','$text_content')";
        mysqli_query($dbconnect, $sql_content_text);
        mysqli_close($dbconnect);
        header("location: content/content.php");

    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["create_file"])) {
        $topic_id = $_GET['topic_id'];
        $titlecontent = $_POST['titlecontent'];
        $title_description = $_POST['title_description'];
        $sql = "INSERT INTO course_contents(topic_id, title_content, content_type, description_content ,created_by, created_at)
        VALUES ('$topic_id','$titlecontent','file','$title_description',NULL,DEFAULT)";
        if (mysqli_query($dbconnect, $sql)) {
            $content_id = mysqli_insert_id($dbconnect);
            echo "Thêm record thành công";
        } else {
            echo "Lỗi: " . $sql . "<br>" . mysqli_error($dbconnect);
        }
        $file_name = $_FILES['contentFile']['name'];
        $file_size = $_FILES['contentFile']['size'] / 1024;
        $file_tmp = $_FILES['contentFile']['tmp_name'];
        $sql_file = "INSERT INTO file_contents(course_content_id, file_name, file_size)
        VALUES ('$content_id','$file_name','$file_size')";
        mysqli_query($dbconnect, $sql_file);
        if (move_uploaded_file($file_tmp, '../../assets/study_files/' . $file_name)) {
            echo 'Upload thành công';
            mysqli_close($dbconnect);
            header("location: content/content.php");
        } else {
            echo 'Lỗi khi upload: ' . error_get_last()['message'];
            exit;
        }
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["create_video"])) {
        $topic_id = $_GET['topic_id'];
        $titlecontent = $_POST['titlecontent'];
        $title_description = $_POST['title_description'];
        $sql = "INSERT INTO course_contents(topic_id, title_content, content_type, description_content ,created_by, created_at)
        VALUES ('$topic_id','$titlecontent','video','$title_description',NULL,DEFAULT)";
        if (mysqli_query($dbconnect, $sql)) {
            $content_id = mysqli_insert_id($dbconnect);
            echo "Thêm record thành công";
        }
        $videoName = $_FILES["contentVideo"]["name"];
        $videoTmpName = $_FILES["contentVideo"]["tmp_name"];
        $videosize = $_FILES["contentVideo"]["size"] / 1024;
        $uploadDirectory = "../../assets/videos/" . $videoName;
        move_uploaded_file($videoTmpName, $uploadDirectory);
        $query = "INSERT INTO video_contents(course_content_id, video_url, video_size) VALUES ('$content_id','$videoName', '$videosize')";
        mysqli_query($dbconnect, $query);
        mysqli_close($dbconnect);
        header("location: content/content.php");
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["create_embedded"])) {
        $topic_id = $_GET['topic_id'];
        $titlecontent = $_POST['titlecontent'];
        $title_description = $_POST['title_description'];
        $embed_code = $_POST["code_embedded"];
        $sql = "INSERT INTO course_contents(topic_id, title_content, content_type, description_content ,created_by, created_at)
        VALUES ('$topic_id','$titlecontent','embed','$title_description',NULL,DEFAULT)";
        if (mysqli_query($dbconnect, $sql)) {
            $content_id = mysqli_insert_id($dbconnect);
            echo "Thêm record thành công";
        }
        function extractVideoURL($iframeInput)
        {
            $pattern = '/src="([^"]+)"/';
            preg_match($pattern, $iframeInput, $matches);
            if (isset($matches[1])) {
                return $matches[1];
            } else {
                return false;
            }
        }
        $embed_code = extractVideoURL($embed_code);
        $query_video = "INSERT INTO embedded_contents(course_content_id,embed_code ) VALUES ('$content_id','$embed_code')";
        mysqli_query($dbconnect, $query_video);
        mysqli_close($dbconnect);
        header("location: content/content.php");
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["edit_content"])) {
    $content_id = $_GET['content_id'];
    $titlecontent = $_POST['titlecontent'];
    $title_description = $_POST['title_description'];
    $sql = "UPDATE course_contents SET title_content='$titlecontent',description_content='$title_description',created_at=DEFAULT WHERE contents_id = $content_id";
    mysqli_query($dbconnect, $sql);

    if (isset($_FILES["contentVideo"])) {
        $sql_edit = "SELECT * FROM video_contents where course_content_id=$content_id";
        $query_update = mysqLi_query($dbconnect, $sql_edit);
        $row_update = mysqli_fetch_assoc($query_update);
        $videoName = $_FILES["contentVideo"]["name"];
        $videoTmpName = $_FILES["contentVideo"]["tmp_name"];
        $videosize = $_FILES["contentVideo"]["size"] / 1024;
        $uploadDirectory = "../../assets/" . $videoName;
        move_uploaded_file($videoTmpName, $uploadDirectory);
        if ($videoName == NULL) {
            $videoName = $row_update['video_url'];
            $videosize = $row_update['video_size'];
        }
        $query = "UPDATE video_contents SET video_url = '$videoName', video_size = '$videosize' WHERE course_content_id=$content_id";
        mysqli_query($dbconnect, $query);
        mysqli_close($dbconnect);
        header("location: content/content.php");
    }
    if (isset($_POST["code_embedded"])) {
        $embed_code = $_POST["code_embedded"];
        function extractVideoURL($iframeInput)
        {
            $pattern = '/src="([^"]+)"/';
            preg_match($pattern, $iframeInput, $matches);
            if (isset($matches[1])) {
                return $matches[1];
            } else {
                return false;
            }
        }
        $embed_code = extractVideoURL($embed_code);
        $query_video = "UPDATE embedded_contents SET embed_code ='$embed_code' WHERE course_content_id = $content_id ";
        mysqli_query($dbconnect, $query_video);
        mysqli_close($dbconnect);
        header("location: content/content.php");
    }
    if (isset($_FILES['contentFile'])) {
        $sql_edit = "SELECT * FROM file_contents where course_content_id=$content_id";
        $query_update = mysqLi_query($dbconnect, $sql_edit);
        $row_update = mysqli_fetch_assoc($query_update);
        $file_name = $_FILES['contentFile']['name'];
        $file_size = $_FILES['contentFile']['size'] / 1024;
        $file_tmp = $_FILES['contentFile']['tmp_name'];
        move_uploaded_file($file_tmp, '../../assets/' . $file_name);
        if ($file_name == NULL) {
            $file_name = $row_update['file_name'];
            $file_size = $row_update['file_size'];
        }
        $sql_file = "UPDATE file_contents SET file_name= '$file_name', file_size ='$file_size' WHERE
        course_content_id = $content_id";
        mysqli_query($dbconnect, $sql_file);
        mysqli_close($dbconnect);
        header("location: content/content.php");
    }
    if (isset($_POST['contentText'])) {
        $text_content = $_POST['contentText'];
        $sql_content_text = "UPDATE text_contents SET text_content ='$text_content' WHERE course_content_id = '$content_id'";
        mysqli_query($dbconnect, $sql_content_text);
        mysqli_close($dbconnect);
        header("location: content/content.php");
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_topic"])) {
    $topic_id = isset($_POST['topic_id']) ? intval($_POST['topic_id']) : 0;
    if ($topic_id <= 0) {
        die("Lỗi: topic_id không hợp lệ");
    }
    $sql = "DELETE FROM topics where topic_id = $topic_id";
    $query = mysqli_query($dbconnect, $sql);
    mysqli_close($dbconnect);
    header('location: content/content.php');
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_content"])) {
    $content_id = $_POST['content_id'];
     $topic_id = isset($_POST['topic_id']) ? intval($_POST['topic_id']) : 0;
    $sql = "DELETE FROM course_contents where contents_id = $content_id";
    $query = mysqli_query($dbconnect, $sql);
    mysqli_close($dbconnect);
    header('location:content/edit_content.php?topic_id=' . $topic_id);
}
