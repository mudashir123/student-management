<?php
include('connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $subject = $_POST['subject'];
    $marks = $_POST['marks'];

    // Check if the student with the same name and subject already exists
    $query = "SELECT id, marks FROM students WHERE name = '$name' AND subject = '$subject'";
    $result = mysqli_query($conn, $query);

    // Update the student record
    $sql = "UPDATE students SET name = '$name', subject = '$subject', marks = '$marks' WHERE id = $id";
    mysqli_query($conn, $sql);
}

mysqli_close($conn);
?>
