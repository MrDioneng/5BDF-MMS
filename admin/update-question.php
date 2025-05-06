<?php
require_once '../db/dbcon.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = $_POST['item_id'];
    $exam_id = $_POST['exam_id'];
    $question = trim($_POST['question']);
    $option_a = trim($_POST['option_a']);
    $option_b = trim($_POST['option_b']);
    $option_c = trim($_POST['option_c']);
    $option_d = trim($_POST['option_d']);
    $correct_option = $_POST['correct_option'];

    // Prepare SQL update query
    $sql = "UPDATE exam_items SET 
                exam_id = ?, 
                question = ?, 
                option_a = ?, 
                option_b = ?, 
                option_c = ?, 
                option_d = ?, 
                correct_option = ? 
            WHERE item_id = ?";

    // Prepare statement
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssssi", $exam_id, $question, $option_a, $option_b, $option_c, $option_d, $correct_option, $item_id);

    // Execute the statement
    if ($stmt->execute()) {
        // Redirect with exam_id and success flag
        header("Location: exam-items.php?exam_id=" . $exam_id . "&updated=1");
        exit();
    } else {
        echo "Error updating question: " . $conn->error;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
