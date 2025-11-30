<?php
include_once('../../../config/connect.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_grade_column"])) {

    $columnId = mysqli_real_escape_string($dbconnect, $_POST["editColumnId"]);
    $columnName = mysqli_real_escape_string($dbconnect, $_POST["editColumnName"]);
    $proportion = mysqli_real_escape_string($dbconnect, $_POST["editProportion"]);

    $sql_update_column = "UPDATE grade_column SET grade_column_name = ?, proportion = ? WHERE column_id = ?";
    $stmt = mysqli_prepare($dbconnect, $sql_update_column);

    mysqli_stmt_bind_param($stmt, "ssi", $columnName, $proportion, $columnId);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        header("Location: grade_column.php");
        exit();
    } else {

        echo "Error updating column: " . mysqli_stmt_error($stmt);
    }

    mysqli_stmt_close($stmt);
}

if (isset($_POST['submit_grade_member'])) {
    if (isset($_POST['member_id']) && isset($_POST['score'])) {
        $member_ids = array_map('intval', $_POST['member_id']);
        $scores = array_map('floatval', $_POST['score']);
        $column_id = $_SESSION['column_id'];

        for ($i = 0; $i < count($member_ids); $i++) {
            $member_id = $member_ids[$i];
            $score = $scores[$i];


            $update_query = "UPDATE grade SET score = $score WHERE member_id = $member_id AND column_id = $column_id";
            mysqli_query($dbconnect, $update_query);
        }

        header("Location: insert_grade_column.php");
        exit();
    } else {
        echo "Không có giá trị";
    }
}

if (isset($_POST['delete_grade_column'])) {
    if (isset($_POST['column_id'])) {
        $column_id = $_POST['column_id'];

        $delete_query = "DELETE FROM grade_column WHERE column_id = $column_id";

        mysqli_query($dbconnect, $delete_query);

        header("Location: grade_column.php");
        exit();
    } else {
        echo "Không có giá trị";
    }
}

if (isset($_POST['create_grade_column'])) {
    $column_name = $_POST['columnName'];
    $course_id = $_SESSION['course_id'];
    $proportion = $_POST['proportion'];

    $sql_create = "INSERT INTO grade_column (course_id, grade_column_name, proportion) VALUES ($course_id, '$column_name', $proportion)";
    mysqli_query($dbconnect, $sql_create);
    $last_column_id = $dbconnect->insert_id;

    $sql_member_create = "SELECT * FROM course_member WHERE course_id = $course_id";
    $result_member_create = mysqli_query($dbconnect, $sql_member_create);
    while ($row_member_create = mysqli_fetch_array($result_member_create)) {
        $member_id = $row_member_create['member_id'];
        $sql_add_score = "INSERT INTO grade (column_id, member_id) VALUES ($last_column_id, $member_id)";
        mysqli_query($dbconnect, $sql_add_score);
    }

    header("Location: grade_column.php");
    exit();
}