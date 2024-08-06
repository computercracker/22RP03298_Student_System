<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require 'db.php';

// Validate and sanitize the student ID
$student_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if ($student_id && filter_var($student_id, FILTER_VALIDATE_INT)) {
    // Prepare the DELETE statement
    $stmt = $conn->prepare("DELETE FROM student WHERE id = ?");
    $stmt->bind_param("i", $student_id);

    // Execute the statement and check the result
    if ($stmt->execute()) {
        header('Location: view_students.php');
        exit();
    } else {
        echo 'Failed to delete student';
    }

    // Close the statement
    $stmt->close();
} else {
    echo 'Invalid student ID';
}

// Close the database connection
$conn->close();
?>
