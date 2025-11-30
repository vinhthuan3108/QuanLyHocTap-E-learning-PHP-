<?php
include("layout.php");

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$student_id = $_SESSION['user_id'];

// Lấy danh sách khóa học của học sinh
$sql_course = "SELECT * FROM course co
INNER JOIN course_member cm ON co.course_id = cm.course_id
WHERE cm.student_id = $student_id";
$result_course = mysqli_query($dbconnect, $sql_course);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bảng điểm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .info-divider {
            border: 0;
            height: 1px;
            background-color: #dee2e6;
            margin: 10px 0;
        }

        .grade-table th,
        .grade-table td {
            text-align: center;
            vertical-align: middle;
        }

        .course-card {
            transition: 0.3s;
        }

        .course-card:hover {
            transform: scale(1.02);
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }

        .course-img {
            height: 150px;
            object-fit: cover;
            border-radius: 5px;
        }

        .avg-score {
            font-weight: bold;
            font-size: 1.2rem;
            color: #0d6efd;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h3 class="mb-4 text-center">Bảng điểm học sinh</h3>

        <?php
        if (mysqli_num_rows($result_course) == 0) {
            echo '<div class="alert alert-warning">Bạn chưa tham gia khóa học nào.</div>';
        } else {
            while ($row_course = mysqli_fetch_array($result_course)) {
                $course_id = $row_course['course_id'];

                // Lấy điểm của học sinh trong khóa học
                $sql_grade = "SELECT * FROM grade gr
                INNER JOIN grade_column gc ON gr.column_id = gc.column_id
                INNER JOIN course_member cm ON gr.member_id = cm.member_id
                WHERE cm.course_id = $course_id AND cm.student_id = $student_id";
                $result_grade = mysqli_query($dbconnect, $sql_grade);
        ?>
                <div class="card mb-5 course-card">
                    <div class="row g-0">
                        <div class="col-md-3">
                            <?php
                            $image_path = !empty($row_course['course_background']) ? "../../assets/file/course_background/" . $row_course['course_background'] : "../../assets/images/course_placeholder.jpg";
                            ?>
                            <img src="<?php echo $image_path; ?>" class="img-fluid course-img m-3" alt="Course Image">
                        </div>
                        <div class="col-md-9">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $row_course['course_name'] . " (" . $row_course['course_code'] . ")"; ?></h5>
                                <p class="card-text mb-3"><?php echo $row_course['course_description']; ?></p>
                                <?php
                                if (mysqli_num_rows($result_grade) == 0) {
                                    echo '<div class="alert alert-info">Chưa có điểm cho khóa học này.</div>';
                                } else {
                                ?>
                                    <table class="table table-striped table-hover table-bordered grade-table">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>STT</th>
                                                <th>Cột điểm</th>
                                                <th>Tỉ lệ tích lũy (%)</th>
                                                <th>Điểm</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $i = 0;
                                            $sum_score = 0;
                                            $sum_proportion = 0;

                                            while ($row_grade = mysqli_fetch_array($result_grade)) {
                                                $i++;
                                                $score_temp = $row_grade['score'];
                                                $proportion_temp = $row_grade['proportion'];
                                                $sum_score += $score_temp * $proportion_temp;
                                                $sum_proportion += $proportion_temp;
                                            ?>
                                                <tr>
                                                    <th scope="row"><?php echo $i; ?></th>
                                                    <td><?php echo $row_grade['grade_column_name']; ?></td>
                                                    <td><?php echo ($proportion_temp * 100); ?>%</td>
                                                    <td><?php echo $score_temp; ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="2"></td>
                                                <th>Điểm trung bình</th>
                                                <td class="avg-score">
                                                    <?php
                                                    if ($sum_proportion > 0) {
                                                        echo number_format($sum_score / $sum_proportion, 2);
                                                    } else {
                                                        echo "Chưa có điểm";
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
        <?php
            }
        }
        ?>
    </div>

    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->
    <?php include("../footer.php"); ?>
</body>

</html>
