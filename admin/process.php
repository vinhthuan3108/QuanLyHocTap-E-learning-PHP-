<?php
include_once "../config/connect.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["sbm_add"])) {
    $name = trim($_POST['full_name']);
    $image_name = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $image_size = $_FILES['image']['size'];
    $birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $citizen_id = trim($_POST['citizen_id']);
    $password = $_POST['password'];
    $username = trim($_POST['username']); // Lấy username từ form

    $this_id = $_GET['id'];
    $role_name = isset($_GET['role_name']) ? $_GET['role_name'] : '';

    // Kiểm tra username có tồn tại chưa
    $check_sql = "SELECT * FROM user_account WHERE username = '$username'";
    $check_result = mysqli_query($dbconnect, $check_sql);
    if (mysqli_num_rows($check_result) > 0) {
        $_SESSION['error'] = "Tên tài khoản '$username' đã tồn tại. Vui lòng chọn tên khác.";
        header("Location: add_account.php?role_id=$this_id&role_name=$role_name");
        exit;
    }

    // Kiểm tra file ảnh
    if(empty($image_name)){
        $_SESSION['error'] = "Vui lòng chọn ảnh chân dung.";
        header("Location: add_account.php?role_id=$this_id&role_name=$role_name");
        exit;
    }

    if($image_size > 5*1024*1024){
        $_SESSION['error'] = "Ảnh không được quá 5MB";
        header("Location: add_account.php?role_id=$this_id&role_name=$role_name");
        exit;
    }

    // Thêm timestamp để tránh trùng tên
    $ext = pathinfo($image_name, PATHINFO_EXTENSION);
    $image = $username . '_' . time() . '.' . $ext;
    $upload_dir = '../assets/images/';

    if(!move_uploaded_file($image_tmp, $upload_dir . $image)){
        $_SESSION['error'] = "Lỗi khi upload ảnh.";
        header("Location: add_account.php?role_id=$this_id&role_name=$role_name");
        exit;
    }

    // Thêm vào bảng user
    $sql = "INSERT INTO user (full_name, date_of_birth, gender, address, phone, email, citizen_id, image)
            VALUES ('$name','$birth','$gender','$address','$phone','$email','$citizen_id','$image')";
    $query = mysqli_query($dbconnect, $sql);
    if (!$query) {
        $_SESSION['error'] = "Lỗi khi chèn dữ liệu vào bảng user: " . mysqli_error($dbconnect);
        header("Location: add_account.php?role_id=$this_id&role_name=$role_name");
        exit;
    }

    $user_id = mysqli_insert_id($dbconnect);

    // Mã hóa mật khẩu và thêm vào user_account
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql_us = "INSERT INTO user_account(username, password, user_id)
               VALUES ('$username','$hashed_password','$user_id')";
    $query_us = mysqli_query($dbconnect, $sql_us);
    if (!$query_us) {
        $_SESSION['error'] = "Lỗi khi chèn dữ liệu vào bảng user_account: " . mysqli_error($dbconnect);
        header("Location: add_account.php?role_id=$this_id&role_name=$role_name");
        exit;
    }

    // Thêm role
    $role_id = intval($this_id);
    if(!in_array($role_id, [1,2,3])){
        $_SESSION['error'] = "Vai trò không tồn tại!";
        header("Location: add_account.php?role_id=$this_id&role_name=$role_name");
        exit;
    }
    $sql_role = "INSERT INTO user_role (user_id,role_id) VALUES ('$user_id','$role_id')";
    $query_role = mysqli_query($dbconnect, $sql_role);
    if (!$query_role) {
        $_SESSION['error'] = "Lỗi khi chèn dữ liệu vào bảng user_role: " . mysqli_error($dbconnect);
        header("Location: add_account.php?role_id=$this_id&role_name=$role_name");
        exit;
    }

    mysqli_close($dbconnect);

    // Redirect về trang tương ứng
    if ($role_id == 1) {
        header('location: student.php');
    } else if ($role_id == 2) {
        header('location: teacher.php');
    } else if ($role_id == 3) {
        header('location: admin.php');
    }
    exit;
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["sbm_edit"])) {
    $id = intval($_GET['id']);
    $role_name = isset($_GET['role_name']) ? $_GET['role_name'] : '';

    // Lấy thông tin hiện tại
    $sql_edit = "SELECT us.*, ua.username, ur.role_id
                 FROM user us
                 INNER JOIN user_account ua ON ua.user_id = us.user_id
                 INNER JOIN user_role ur ON ur.user_id = us.user_id
                 WHERE us.user_id=$id";
    $query_update = mysqli_query($dbconnect, $sql_edit);
    $row_update = mysqli_fetch_assoc($query_update);

    $name = trim($_POST['full_name']);
    $image_name = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $image_size = $_FILES['image']['size'];
    $birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $citizen_id = trim($_POST['citizen_id']);
    $username = trim($_POST['username']);
    $new_password = $_POST['new_password'];

    // Kiểm tra username trùng (trừ chính tài khoản hiện tại)
    if ($username != $row_update['username']) {
        $check_sql = "SELECT * FROM user_account WHERE username = '$username'";
        $check_result = mysqli_query($dbconnect, $check_sql);
        if (mysqli_num_rows($check_result) > 0) {
            $_SESSION['error'] = "Tên tài khoản '$username' đã tồn tại. Vui lòng chọn tên khác.";
            header("Location: edit_account.php?user_id=$id&role_id=" . $row_update['role_id'] . "&role_name=$role_name");
            exit;
        }
    }

    // Xử lý upload ảnh
    if (!empty($image_name)) {
        if($image_size > 5*1024*1024){
            $_SESSION['error'] = "Ảnh không được quá 5MB";
            header("Location: edit_account.php?user_id=$id&role_id=" . $row_update['role_id'] . "&role_name=$role_name");
            exit;
        }
        $ext = pathinfo($image_name, PATHINFO_EXTENSION);
        $image = $username . '_' . time() . '.' . $ext;
        $upload_dir = '../assets/images/';
        if (!move_uploaded_file($image_tmp, $upload_dir . $image)) {
            $_SESSION['error'] = "Lỗi khi upload ảnh.";
            header("Location: edit_account.php?user_id=$id&role_id=" . $row_update['role_id'] . "&role_name=$role_name");
            exit;
        }
    } else {
        $image = $row_update['image'];
    }

    // Cập nhật bảng user
    $sql_user = "UPDATE user
                 SET full_name='$name', date_of_birth='$birth', gender='$gender',
                     address='$address', phone='$phone', email='$email', citizen_id='$citizen_id', image='$image'
                 WHERE user_id='$id'";
    $query_user = mysqli_query($dbconnect, $sql_user);
    if (!$query_user) {
        $_SESSION['error'] = "Lỗi khi cập nhật bảng user: " . mysqli_error($dbconnect);
        header("Location: edit_account.php?user_id=$id&role_id=" . $row_update['role_id'] . "&role_name=$role_name");
        exit;
    }

    // Cập nhật bảng user_account
    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql_account = "UPDATE user_account SET username='$username', password='$hashed_password' WHERE user_id='$id'";
    } else {
        $sql_account = "UPDATE user_account SET username='$username' WHERE user_id='$id'";
    }
    $query_account = mysqli_query($dbconnect, $sql_account);
    if (!$query_account) {
        $_SESSION['error'] = "Lỗi khi cập nhật bảng user_account: " . mysqli_error($dbconnect);
        header("Location: edit_account.php?user_id=$id&role_id=" . $row_update['role_id'] . "&role_name=$role_name");
        exit;
    }

    mysqli_close($dbconnect);

    // Redirect về trang tương ứng theo role
    $role = $row_update['role_id'];
    if ($role == 1) {
        header('Location: student.php');
    } else if ($role == 2) {
        header('Location: teacher.php');
    } else if ($role == 3) {
        header('Location: admin.php');
    } else {
        echo "Vai trò không tồn tại!";
    }
    exit;
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_student"])) {
    $id = $_POST['user_id'];
    $sql = "DELETE FROM user where user_id = $id";
    $query = mysqli_query($dbconnect, $sql);
    mysqli_close($dbconnect);
    header('location: student.php');
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_teacher"])) {
    $id = $_POST['user_id'];
    $sql = "DELETE FROM user where user_id = $id";
    $query = mysqli_query($dbconnect, $sql);
    mysqli_close($dbconnect);
    header('location: teacher.php');
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_admin"])) {
    $id = $_POST['user_id'];
    $sql = "DELETE FROM user where user_id = $id";
    $query = mysqli_query($dbconnect, $sql);
    mysqli_close($dbconnect);
    header('location: admin.php');
}

if (session_status() == PHP_SESSION_NONE) { session_start(); }

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_schedule"])) {
    $course_id = intval($_POST['course_id']);
    $dayOfWeeks = $_POST["dayOfWeek"];
    $startTimes = $_POST["startTime"];
    $endTimes = $_POST["endTime"];
    $scheduleIds = isset($_POST['schedule_id']) ? $_POST['schedule_id'] : array();

    if (empty($dayOfWeeks) || empty($startTimes) || empty($endTimes)) {
        $_SESSION['error_schedule'] = "Thời khóa biểu chưa đầy đủ hoặc còn trống";
        header("Location: schedule_edit.php?id=$course_id");
        exit();
    }

    // Lấy thông tin khóa học từ session
    $course_name = $_SESSION["course_name"];
    $course_code = $_SESSION["course_code"];
    $course_description = $_SESSION["course_description"];
    $start_date = $_SESSION["start_date"];
    $end_date = $_SESSION["end_date"];
    $teacher_id = $_SESSION['teacher_course'];
    $fileName = $_SESSION['course_image'];
    unset($_SESSION["course_name"], $_SESSION["course_code"], $_SESSION["course_description"], $_SESSION["start_date"], $_SESSION["end_date"], $_SESSION['teacher_course'], $_SESSION['course_image']);

    // Cập nhật khóa học
    if (empty($fileName)) {
        $sql_bg = mysqli_query($dbconnect,"SELECT course_background FROM course WHERE course_id=$course_id");
        $row_bg = mysqli_fetch_assoc($sql_bg);
        $fileName = $row_bg['course_background'];
    }
    $sql_update_course = "UPDATE course SET course_background='$fileName', course_name='$course_name', course_code='$course_code',
        course_description='$course_description', teacher_id='$teacher_id', start_date='$start_date', end_date='$end_date'
        WHERE course_id=$course_id";
    mysqli_query($dbconnect, $sql_update_course);

    $idsToKeep = array();
    for ($i=0; $i<count($dayOfWeeks); $i++) {
        // map ngày
        switch($dayOfWeeks[$i]) {
            case "monday": $dayVal=2; break;
            case "tuesday": $dayVal=3; break;
            case "wednesday": $dayVal=4; break;
            case "thursday": $dayVal=5; break;
            case "friday": $dayVal=6; break;
            case "saturday": $dayVal=7; break;
            case "sunday": $dayVal=1; break;
        }
        $start = $startTimes[$i];
        $end = $endTimes[$i];

        // Kiểm tra thời gian hợp lệ
        if ($start >= $end) {
            $_SESSION['error_schedule'] = "Thời gian bắt đầu phải nhỏ hơn thời gian kết thúc!";
            header("Location: schedule_edit.php?id=$course_id");
            exit();
        }

        if (isset($scheduleIds[$i]) && !empty($scheduleIds[$i])) {
            $sid = intval($scheduleIds[$i]);
            $idsToKeep[] = $sid;
            $sql_upd = "UPDATE course_schedule SET day_of_week='$dayVal', start_time='$start', end_time='$end' WHERE course_schedule_id=$sid";
            mysqli_query($dbconnect, $sql_upd);
        } else {
            $sql_ins = "INSERT INTO course_schedule (course_id, day_of_week, start_time, end_time) VALUES ($course_id,'$dayVal','$start','$end')";
            mysqli_query($dbconnect, $sql_ins);
            $idsToKeep[] = mysqli_insert_id($dbconnect);
        }
    }

    // Xóa các lịch không còn trong form
    if (!empty($idsToKeep)) {
        $idList = implode(",", $idsToKeep);
        mysqli_query($dbconnect,"DELETE FROM course_schedule WHERE course_id=$course_id AND course_schedule_id NOT IN ($idList)");
    }

    header("Location: success_course.php?course_id=$course_id&teacher_id=$teacher_id");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_schedule"])) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_POST["dayOfWeek"]) || empty($_POST["startTime"]) || empty($_POST["endTime"])) {
        $_SESSION['error_schedule'] = "Thời khóa biểu chưa đầy đủ hoặc còn trống";
        header("Location: schedule_add.php");
        exit();
    }

    // Lấy thông tin khóa học từ session
    $course_name = $_SESSION["course_name"];
    $course_code = $_SESSION["course_code"];
    $course_description = $_SESSION["course_description"];
    $start_date = $_SESSION["start_date"];
    $end_date = $_SESSION["end_date"];
    $teacher_id = $_SESSION['teacher_course'];
    $fileName = $_SESSION['course_image'];

    unset($_SESSION["course_name"], $_SESSION["course_code"], $_SESSION["course_description"], $_SESSION["start_date"], $_SESSION["end_date"], $_SESSION['teacher_course'], $_SESSION['course_image']);

    // Escape dữ liệu để tránh lỗi SQL
    $course_name = mysqli_real_escape_string($dbconnect, $course_name);
    $course_code = mysqli_real_escape_string($dbconnect, $course_code);
    $course_description = mysqli_real_escape_string($dbconnect, $course_description);
    $start_date = mysqli_real_escape_string($dbconnect, $start_date);
    $end_date = mysqli_real_escape_string($dbconnect, $end_date);
    $fileName = mysqli_real_escape_string($dbconnect, $fileName);

    // Tạo khóa học
    $sql_create_course = "INSERT INTO course (course_background, course_code, course_name, teacher_id, course_description, start_date, end_date, status)
                          VALUES ('$fileName', '$course_code', '$course_name', $teacher_id, '$course_description', '$start_date', '$end_date', 'N')";
    if (mysqli_query($dbconnect, $sql_create_course)) {
        $course_id = mysqli_insert_id($dbconnect);
    } else {
        die("Something went wrong. Error: " . mysqli_error($dbconnect));
    }

    // Thêm lịch học
    $dayOfWeeks = $_POST["dayOfWeek"];
    $startTimes = $_POST["startTime"];
    $endTimes = $_POST["endTime"];

    for ($i = 0; $i < count($dayOfWeeks); $i++) {
        switch ($dayOfWeeks[$i]) {
            case "monday": $day = 2; break;
            case "tuesday": $day = 3; break;
            case "wednesday": $day = 4; break;
            case "thursday": $day = 5; break;
            case "friday": $day = 6; break;
            case "saturday": $day = 7; break;
            case "sunday": $day = 1; break;
            default: $day = 0; break;
        }

        $startTime = mysqli_real_escape_string($dbconnect, $startTimes[$i]);
        $endTime = mysqli_real_escape_string($dbconnect, $endTimes[$i]);

        $sql_schedule = "INSERT INTO course_schedule (course_id, day_of_week, start_time, end_time)
                         VALUES ($course_id, '$day', '$startTime', '$endTime')";
        mysqli_query($dbconnect, $sql_schedule);
    }

    header("Location: success_course.php?course_id=$course_id&teacher_id=$teacher_id");
    exit();
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["approve_course"])) {
    $id = $_POST['course_id'];
    $sql = "UPDATE course SET status='A' where course_id = $id";
    $query = mysqli_query($dbconnect, $sql);
    mysqli_close($dbconnect);
    header('location:courses.php');
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_course"])) {
    $id = $_POST['course_id'];
    $sql = "DELETE FROM course where course_id = $id";
    $query = mysqli_query($dbconnect, $sql);
    mysqli_close($dbconnect);
    header('location:courses.php');
}
