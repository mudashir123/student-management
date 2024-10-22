<?php
include 'connection.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, ucwords($_POST['name']));
    $subject = mysqli_real_escape_string($conn, ucwords($_POST['subject']));
    $marks = intval($_POST['marks']);

    // Check if the student with the same name and subject already exists
    $query = "SELECT id, marks FROM students WHERE name = '$name' AND subject = '$subject'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        // Student exists, update the marks by adding new marks
        $row = mysqli_fetch_assoc($result);
        $new_marks = $row['marks'] + $marks;
        $update_query = "UPDATE students SET marks = $new_marks WHERE id = {$row['id']}";
        if (mysqli_query($conn, $update_query)) {
            echo json_encode(['success' => true, 'message' => 'Marks updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update marks']);
        }
    } else {
        // Student doesn't exist, insert new record
        $insert_query = "INSERT INTO students (name, subject, marks) VALUES ('$name', '$subject', $marks)";
        if (mysqli_query($conn, $insert_query)) {
            echo json_encode(['success' => true, 'message' => 'Student added successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add student']);
        }
    }
}

mysqli_close($conn);
?>
