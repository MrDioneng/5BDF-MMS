<?php
// update-exam.php

// Include your database connection
require_once '../db/dbcon.php';

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize inputs
    $exam_id = isset($_POST['exam_id']) ? intval($_POST['exam_id']) : 0;
    $exam_title = isset($_POST['exam_title']) ? trim($_POST['exam_title']) : '';
    $exam_department = isset($_POST['exam_department']) ? trim($_POST['exam_department']) : '';
    $exam_duration = isset($_POST['exam_duration']) ? trim($_POST['exam_duration']) : '';
    $exam_description = isset($_POST['exam_description']) ? trim($_POST['exam_description']) : '';

    // Basic validation
    if ($exam_id > 0 && $exam_title !== '' && $exam_department !== '' && $exam_duration !== '' && $exam_description !== '') {
        // Prepare the update statement with prepared statements for security
        $stmt = $conn->prepare("UPDATE exams SET exam_title = ?, department = ?, duration = ?, description = ? WHERE exam_id = ?");
        if ($stmt) {
            $stmt->bind_param("ssssi", $exam_title, $exam_department, $exam_duration, $exam_description, $exam_id);

            if ($stmt->execute()) {
                // Success - redirect back or show a success message
                header("Location: exam-items.php?exam_id=$exam_id");
                exit(); 
            } else {
                // Handle execution error
                echo "Error updating exam: " . htmlspecialchars($stmt->error);
            }

            $stmt->close();
        } else {
            echo "Failed to prepare the SQL statement.";
        }
    } else {
        echo "Please fill in all required fields.";
    }
} else {
    // If accessed without POST method, redirect or show an error
    header("Location: exam-items.php?exam_id=$exam_id");
    exit();
}
