<?php
session_start();
include_once('config/connect.php');
include_once('layout.php');


if (!isset($cookie_name)) $cookie_name = 'auto_login';
if (!isset($cookie_time)) $cookie_time = 3600 * 24 * 30; 

$error = '';  
$login_error_message = ''; 

try {
    
    if (isset($_COOKIE[$cookie_name])) {
        $cookie_data = $_COOKIE[$cookie_name];
        parse_str($cookie_data, $cookie_values);

        if (isset($cookie_values['usr']) && isset($cookie_values['hash'])) {
            $t_username = $cookie_values['usr'];
            $t_hashedPassword = $cookie_values['hash'];  

            
            $sql = "SELECT ua.password, us.full_name, us.user_id FROM user_account ua
                    INNER JOIN user us ON ua.user_id = us.user_id
                    WHERE ua.username = ?";
            $stmt = mysqli_prepare($dbconnect, $sql);
            mysqli_stmt_bind_param($stmt, "s", $t_username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);

            if ($row && password_verify('', $row['password'])) {  
               
                
              
                if (isset($_SESSION['username'])) {
                
                    $sql_role = "SELECT r.role_name FROM user_account ua
                    INNER JOIN user_role ur ON ua.user_id = ur.user_id
                    INNER JOIN role r ON ur.role_id = r.role_id
                    WHERE ua.username = '{$_SESSION['username']}'";

                    $result_r = mysqli_query($dbconnect, $sql_role);
                    if ($result_r) {
                        if ($row_role = mysqli_fetch_assoc($result_r)) {
                            switch ($row_role['role_name']) {
                                case "student":
                                    header('location:student/index.php');
                                    exit;
                                case "teacher":
                                    header('location:teacher/index.php');
                                    exit;
                                case "admin":
                                    header('location:admin/index.php');
                                    exit;
                                default:
                                    exit;
                            }
                        }
                    }
                }
            }
        }
    }

   
    if (isset($_COOKIE['remember_credentials'])) {
        $remembered_credentials = $_COOKIE['remember_credentials'];
        parse_str($remembered_credentials, $credentials);
        $remembered_username = $credentials['usr'];
        $remembered_password = '';  
    } else {
        $remembered_username = '';
        $remembered_password = '';
    }

    if (isset($_POST['submit'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];  
        $remember = ((isset($_POST['remember']) != 0) ? 1 : "");

        if (empty($username) || empty($password)) {
            $login_error_message = "Thông tin chưa đầy đủ. Vui lòng nhập đầy đủ thông tin.";
        } else {
            
            $sql = "SELECT * FROM user_account WHERE username=?";  
            $stmt = mysqli_prepare($dbconnect, $sql);
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (!$result) {
                throw new Exception("Lỗi câu truy vấn: " . mysqli_error($dbconnect));
            }

            $row = mysqli_fetch_array($result);

            if ($row && password_verify($password, $row['password'])) { 
             
                $f_user = $row['username'];

               
                if ($remember == 1) {
                    setcookie('remember_credentials', 'usr=' . $f_user, time() + $cookie_time);
                } else {
                    setcookie('remember_credentials', '', time() - 3600);
                }

    
                $sql_user = "SELECT us.full_name, us.user_id FROM user us
                              INNER JOIN user_account ua ON us.user_id = ua.user_id
                              WHERE username = ?";

                $stmt_user = mysqli_prepare($dbconnect, $sql_user);
                mysqli_stmt_bind_param($stmt_user, "s", $username);
                mysqli_stmt_execute($stmt_user);
                $result_user = mysqli_stmt_get_result($stmt_user);

                $row_user = mysqli_fetch_assoc($result_user);

                $_SESSION['full_name'] = $row_user['full_name'];
                $_SESSION['username'] = $username;
                $_SESSION['user_id'] = $row_user['user_id'];

              
                $sql_role = "SELECT r.role_name FROM user_account ua
                                INNER JOIN user_role ur ON ua.user_id = ur.user_id
                                INNER JOIN role r ON ur.role_id = r.role_id
                                WHERE ua.username = ?";

                $stmt_role = mysqli_prepare($dbconnect, $sql_role);
                mysqli_stmt_bind_param($stmt_role, "s", $username);
                mysqli_stmt_execute($stmt_role);
                $result_role = mysqli_stmt_get_result($stmt_role);

                $row_role = mysqli_fetch_assoc($result_role);

                if ($row_role) {
                    $_SESSION['role_name'] = $row_role['role_name'];

                    switch ($row_role['role_name']) {
                        case "student":
                            header('location: student/index.php');
                            exit;
                        case "teacher":
                            header('location: teacher/index.php');
                            exit;
                        case "admin":
                            header('location: admin/index.php');
                            exit;
                        default:
                            echo "Vai trò không hợp lệ.";
                            exit;
                    }
                } else {
                    echo "Không tìm thấy thông tin vai trò cho người dùng.";
                    exit;
                }
            } else {
                $login_error_message = "Tên đăng nhập hoặc mật khẩu không chính xác";
            }
        }
    }
} catch (Exception $exp) {
    echo $exp->getMessage() . '<br>';
    echo 'File: ' . $exp->getFile() . '<br>';
    echo 'Line: ' . $exp->getLine() . '<br>';
}

$login_error_message = $login_error_message ?: $error;  
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng nhập</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: "Segoe UI", sans-serif;
        }

        .login-card {
            width: 380px;
            background: #ffffff;
            padding: 35px 30px;
          
            box-shadow: 0 12px 35px rgba(0,0,0,0.12);
        }

        h3 {
            font-weight: 700;
            color: #0d47a1;
            margin-bottom: 15px;
        }

        .form-control {
          
            padding: 12px 14px;
            font-size: 1rem;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            font-size: 1.1rem;
          
            font-weight: 600;
            background: linear-gradient(135deg, #1976d2, #0d47a1);
            border: none;
            color: white;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #1565c0, #0b3c91);
        }

        .password-toggle {
            position: absolute;
            right: 14px;
            color: #666;
        }

        .forgot-text a {
            color: #0d47a1;
            text-decoration: none;
            font-size: 0.95rem;
        }

        .forgot-text a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

<div class="login-card">

    <?php if (!empty($login_error_message)): ?>
        <div class="alert alert-danger text-center"><?php echo $login_error_message; ?></div>
    <?php endif; ?>

    <h3 class="text-center">Đăng nhập</h3>

    <form action="login.php" method="post">
        <div class="mb-3">
            <label class="form-label fw-semibold">Tên đăng nhập</label>
            <input type="text" 
                   class="form-control shadow-sm"
                   name="username" 
                   placeholder="Nhập tên đăng nhập"
                   value="<?php echo htmlspecialchars($remembered_username); ?>">
        </div>

        <div class="mb-3 position-relative">
            <label class="form-label fw-semibold">Mật khẩu</label>
            <input type="password" 
                   class="form-control shadow-sm"
                   id="password"
                   name="password" 
                   placeholder="Nhập mật khẩu">

            <span class="password-toggle" onclick="togglePasswordVisibility()">
                <i class="fa-regular fa-eye"></i>
            </span>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" 
                   class="form-check-input"
                   id="remember"
                   name="remember"
                   value="1"
                   <?php if (!empty($remembered_username)) echo 'checked'; ?>>
            <label class="form-check-label" for="remember">Ghi nhớ tài khoản</label>
        </div>

        <button type="submit" name="submit" class="btn-login">Đăng nhập</button>

        <div class="text-center mt-3 forgot-text">
            <a href="forgot_password.php">Quên mật khẩu?</a>
        </div>

    </form>
</div>

<script>
    function togglePasswordVisibility() {
        var pw = document.getElementById("password");
        var icon = document.querySelector(".password-toggle i");

        if (pw.type === "password") {
            pw.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            pw.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    }
</script>

</body>
</html>
