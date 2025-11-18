<?php
include_once('layout.php');
include_once('../../config/connect.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$course_id = $_SESSION['course_id'];
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['timkiem'])) {
    $tukhoa = $_POST['tukhoa'];

}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bài tập và Kiểm tra</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <div class="container mt-4"> 
        <header>
            <div class="row">
                <div class="col-md-6">
                    <h2>Bài tập và Kiểm tra</h2>
                </div>
                <div class="col-md-6">
                    <form class="d-flex" action="exam.php" method="POST">
                        <div class="input-group">
                            <input type="search" class="form-control" placeholder="Tìm kiếm" name="tukhoa" aria-label="Tìm kiếm">
                            <button class="btn btn-primary" type="submit" name="timkiem">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                    <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['timkiem'])) { ?>
                        <div class="row mt-3">
                            <div class="col">
                                <?php
                                $tukhoa = $_POST['tukhoa'];
                                echo "<p>Tìm kiếm với từ khóa: '<strong>$tukhoa</strong>'</p>"; ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </header>

        
        
    </div>
    
    </main> 
    

</body>
</html>