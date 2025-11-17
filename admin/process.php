<?php
include_once "../config/connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["sbm_add"])) {
    $name = $_POST['full_name'];
    $image_name = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $citizen_id = $_POST['citizen_id'];
    $password = $_POST['password'];
    $username = $_POST['username']; // Lấy username từ form

    $this_id = $_GET['id'];

    // Kiểm tra username có tồn tại chưa
    $check_sql = "SELECT * FROM user_account WHERE username = '$username'";
    $check_result = mysqli_query($dbconnect, $check_sql);
    
    if (mysqli_num_rows($check_result) > 0) {
        // Username đã tồn tại, quay lại form với thông báo lỗi
        $_SESSION['error'] = "Tên tài khoản '$username' đã tồn tại. Vui lòng chọn tên khác.";
        header("Location: add_account.php?role_id=$this_id&role_name=" . $_GET['role_name']);
        exit;
    }

    // Xử lý upload ảnh
    $image = $username . '_' . $image_name;
    if (move_uploaded_file($image_tmp, '../assets/images/' . $image)) {
        echo 'Upload thành công';
    } else {
        echo 'Lỗi khi upload: ' . error_get_last()['message'];
    }

    // Thêm vào bảng user
    $sql = "INSERT INTO user (full_name, date_of_birth, gender, address, phone, email, citizen_id, image) 
            VALUES ('$name','$birth','$gender','$address','$phone','$email','$citizen_id','$image')";
    $query = mysqli_query($dbconnect, $sql);
    if (!$query) {
        echo "Lỗi khi chèn dữ liệu vào bảng user: " . mysqli_error($dbconnect);
    }

    $user_id = mysqli_insert_id($dbconnect);
    
    // Mã hóa mật khẩu và thêm vào user_account
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql_us = "INSERT INTO user_account(account_id, username, password, user_id) 
               VALUES ('$user_id','$username','$hashed_password','$user_id')";
    $query_us = mysqli_query($dbconnect, $sql_us);
    if (!$query_us) {
        echo "Lỗi khi chèn dữ liệu vào bảng user_account: " . mysqli_error($dbconnect);
    }

    // Thêm role
    if ($this_id == 1) {
        $sql_role = "INSERT INTO user_role (user_id,role_id) VALUES ('$user_id','1')";
    } else if ($this_id == 2) {
        $sql_role = "INSERT INTO user_role (user_id,role_id) VALUES ('$user_id','2')";
    } else if ($this_id == 3) {
        $sql_role = "INSERT INTO user_role (user_id,role_id) VALUES ('$user_id','3')";
    } else {
        echo "Lỗi role không tồn tại: " . mysqli_error($dbconnect);
    }
    
    $query_role = mysqli_query($dbconnect, $sql_role);
    if (!$query_role) {
        echo "Lỗi khi chèn dữ liệu vào bảng user_role: " . mysqli_error($dbconnect);
    }
    
    mysqli_close($dbconnect);
    
    // Redirect về trang tương ứng
    if ($this_id == 1) {
        header('location: student.php');
    } else if ($this_id == 2) {
        header('location: teacher.php');
    } else if ($this_id == 3) {
        header('location: admin.php');
    } else {
        echo "Vai trò không tồn tại!";
        exit;
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["sbm_edit"])) {
    $id = $_GET['id'];
    
    // Lấy thông tin hiện tại để so sánh
    $sql_edit = "SELECT * FROM user us
    INNER JOIN user_role ur ON us.user_id = ur.user_id
    INNER JOIN user_account ua ON ua.user_id = us.user_id
    where us.user_id=$id";
    $query_update = mysqLi_query($dbconnect, $sql_edit);
    $row_update = mysqli_fetch_assoc($query_update);
    
    $name = $_POST['full_name'];
    $image_name = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $citizen_id = $_POST['citizen_id'];
    $username = $_POST['username'];
    $new_password = $_POST['new_password'];

    // Kiểm tra username có bị trùng không (trừ username hiện tại)
    if ($username != $row_update['username']) {
        $check_sql = "SELECT * FROM user_account WHERE username = '$username' AND username != '" . $row_update['username'] . "'";
        $check_result = mysqli_query($dbconnect, $check_sql);
        
        if (mysqli_num_rows($check_result) > 0) {
            // Username đã tồn tại, quay lại form với thông báo lỗi
            $_SESSION['error'] = "Tên tài khoản '$username' đã tồn tại. Vui lòng chọn tên khác.";
            header("Location: edit_account.php?user_id=$id&role_id=" . $row_update['role_id'] . "&role_name=" . $_GET['role_name']);
            exit;
        }
    }

    // Xử lý ảnh
    if (!empty($image_name)) {
        $image = $username . '_' . $image_name;
        if (move_uploaded_file($image_tmp, '../assets/images/' . $image)) {
            echo 'Upload thành công';
        } else {
            echo 'Lỗi khi upload: ' . error_get_last()['message'];
        }
    } else {
        $image = $row_update['image'];
    }

    // Cập nhật thông tin user
    $sql = "UPDATE user SET full_name='$name', date_of_birth = '$birth',gender ='$gender',
    address = '$address',phone = '$phone',email='$email',citizen_id = '$citizen_id',image='$image' WHERE user_id='$id';";
    $query = mysqli_query($dbconnect, $sql);
    if (!$query) {
        echo "Lỗi khi sửa dữ liệu vào bảng user: " . mysqli_error($dbconnect);
    }

    // Cập nhật username và password (nếu có)
    if (!empty($new_password)) {
        // Mã hóa mật khẩu mới
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql_ua = "UPDATE user_account SET username='$username', password='$hashed_password' WHERE user_id='$id'";
    } else {
        $sql_ua = "UPDATE user_account SET username='$username' WHERE user_id='$id'";
    }
    
    $query_ua = mysqli_query($dbconnect, $sql_ua);
    if (!$query_ua) {
        echo "Lỗi khi cập nhật dữ liệu vào bảng user_account: " . mysqli_error($dbconnect);
    }

    mysqli_close($dbconnect);
    $role = $row_update['role_id'];
    if ($role == 1) {
        header('location: student.php');
    } else if ($role == 2) {
        header('location: teacher.php');
    } else if ($role == 3) {
        header('location: admin.php');
    } else {
        echo "Vai trò không tồn tại!";
        exit;
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_student"])) {
    $id = $_GET['user_id'];
    $sql = "DELETE FROM user where user_id = $id";
    $query = mysqli_query($dbconnect, $sql);
    mysqli_close($dbconnect);
    header('location: student.php');
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_teacher"])) {
    $id = $_GET['user_id'];
    $sql = "DELETE FROM user where user_id = $id";
    $query = mysqli_query($dbconnect, $sql);
    mysqli_close($dbconnect);
    header('location: teacher.php');
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_admin"])) {
    $id = $_GET['user_id'];
    $sql = "DELETE FROM user where user_id = $id";
    $query = mysqli_query($dbconnect, $sql);
    mysqli_close($dbconnect);
    header('location: admin.php');
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_schedule"])) {
    if (!isset($_POST["dayOfWeek"]) || !isset($_POST["startTime"]) || !isset($_POST["endTime"])) {
        echo '<div class="container mt-5">
                <div class="alert alert-danger" role="alert">
                    Thời khóa biểu chưa đầy đủ hoặc còn trống
                </div>
            </div>';
        include_once('schedule_edit.php');
    } else {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $course_id = $_POST['course_id'];
        $sql_course = "SELECT * FROM course WHERE course_id = $course_id";
        $result_edit = mysqli_query($dbconnect, $sql_course);
        $row_update = mysqli_fetch_array($result_edit);
        $course_name = $_SESSION["course_name"];
        $course_code = $_SESSION["course_code"];
        $course_description = $_SESSION["course_description"];
        $start_date = $_SESSION["start_date"];
        $end_date = $_SESSION["end_date"];
        $teacher_id = $_SESSION['teacher_course'];
        $fileName = $_SESSION['course_image'];

        unset($_SESSION["course_name"]);
        unset($_SESSION["course_code"]);
        unset($_SESSION["course_description"]);
        unset($_SESSION["start_date"]);
        unset($_SESSION["end_date"]);
        unset($_SESSION['teacher_course']);
        unset($_SESSION['course_image']);

        if (empty($fileName)) {
            $fileName = $row_update['course_background'];
        }

        $sql_create_course = "UPDATE course SET course_background ='$fileName',course_name='$course_name',course_code='$course_code',course_description='$course_description',teacher_id='$teacher_id',start_date='$start_date',end_date='$end_date' where course_id = $course_id";
        $result_course = mysqli_query($dbconnect, $sql_create_course);
        if (!$result_course) {
            die("Something went wrong. Error: " . mysqli_error($dbconnect));
        }

        $dayOfWeeks = $_POST["dayOfWeek"];
        $startTimes = $_POST["startTime"];
        $endTimes = $_POST["endTime"];

        // Kiểm tra xem có dữ liệu được gửi từ biểu mẫu không
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

            $scheduleisset = implode(',', $scheduleIdsToKeep);
            $sql_delete_schedule = "DELETE FROM course_schedule WHERE course_id ='$course_id' AND course_schedule_id NOT IN ($scheduleisset)";
            mysqli_query($dbconnect, $sql_delete_schedule);

            header("Location: success_course.php?course_id=$course_id&teacher_id=$teacher_id");
            exit();
        } else {
            echo "Không có dữ liệu được gửi từ form.";
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_schedule"])) {
    if (!isset($_POST["dayOfWeek"]) || !isset($_POST["startTime"]) || !isset($_POST["endTime"])) {
        echo '<div class="container mt-5">
                <div class="alert alert-danger" role="alert">
                    Thời khóa biểu chưa đầy đủ hoặc còn trống
                </div>
            </div>';
        include_once('schedule_add.php');
    } else {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $course_name = $_SESSION["course_name"];
        $course_code = $_SESSION["course_code"];
        $course_description = $_SESSION["course_description"];
        $start_date = $_SESSION["start_date"];
        $end_date = $_SESSION["end_date"];
        $teacher_id = $_SESSION['teacher_course'];
        $fileName = $_SESSION['course_image'];
        unset($_SESSION["course_name"]);
        unset($_SESSION["course_code"]);
        unset($_SESSION["course_description"]);
        unset($_SESSION["start_date"]);
        unset($_SESSION["end_date"]);
        unset($_SESSION['teacher_course']);
        unset($_SESSION['course_image']);
        $sql_create_course = "INSERT INTO course (course_background, course_code, course_name,teacher_id ,course_description, start_date, end_date, status)
    VALUES ('$fileName', '$course_code', '$course_name', '$teacher_id', '$course_description', '$start_date', '$end_date', 'N')";
        if (mysqli_query($dbconnect, $sql_create_course)) {
            $sql_course_id = "SELECT course_id FROM course WHERE course_code = '$course_code'";
            $result_course_id = mysqli_query($dbconnect, $sql_course_id);
            $row_course_id = mysqli_fetch_assoc($result_course_id);
            $_SESSION['course_id_add'] = $row_course_id['course_id'];
        } else {
            die("Something went wrong. Error: " . mysqli_error($dbconnect));
        }
        // Lấy thông tin từ form

        $dayOfWeeks = $_POST["dayOfWeek"];
        $startTimes = $_POST["startTime"];
        $endTimes = $_POST["endTime"];

        // Kiểm tra xem có dữ liệu được gửi hay không
        if (!empty($dayOfWeeks) && !empty($startTimes) && !empty($endTimes)) {
            // Lặp qua từng hàng và hiển thị thông tin
            for ($i = 0; $i < count($dayOfWeeks); $i++) {
                switch ($dayOfWeeks[$i]) {
                    case "monday":
                        $dayOfWeek = "2";
                        break;
                    case "tuesday":
                        $dayOfWeek = "3";
                        break;
                    case "wednesday":
                        $dayOfWeek = "4";
                        break;
                    case "thursday":
                        $dayOfWeek = "5";
                        break;
                    case "friday":
                        $dayOfWeek = "6";
                        break;
                    case "saturday":
                        $dayOfWeek = "7";
                        break;
                    case "sunday":
                        $dayOfWeek = "C";
                        break;
                }
                $startTime = $startTimes[$i];
                $endTime = $endTimes[$i];
                $course_id = $_SESSION['course_id_add'];
                unset($_SESSION['course_id_add']);
                $sql_schedule = "INSERT INTO course_schedule (course_id, day_of_week, start_time, end_time)
            VALUES ($course_id, '$dayOfWeek', '$startTime', '$endTime') ";
                mysqli_query($dbconnect, $sql_schedule);
            }
            header("Location: success_course.php?course_id=$course_id&teacher_id=$teacher_id");
            exit();
        } else {
            echo "Không có dữ liệu được gửi từ form.";
        }
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["approve_course"])) {
    $id = $_GET['course_id'];
    $sql = "UPDATE course SET status='A' where course_id = $id";
    $query = mysqli_query($dbconnect, $sql);
    mysqli_close($dbconnect);
    header('location:courses.php');
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_course"])) {
    $id = $_GET['course_id'];
    $sql = "DELETE FROM course where course_id = $id";
    $query = mysqli_query($dbconnect, $sql);
    mysqli_close($dbconnect);
    header('location:courses.php');
}
