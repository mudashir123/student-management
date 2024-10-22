<?php
include('connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];

    // Delete the student record
    $sql = "DELETE FROM students WHERE id = $id";
    mysqli_query($conn, $sql);
}

mysqli_close($conn);
?>
