<?php
require_once '../db/dbcon.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $exam_id = $_POST['exam_id'];
    $question = $_POST['question'];
    $a = $_POST['option_a'];
    $b = $_POST['option_b'];
    $c = $_POST['option_c'];
    $d = $_POST['option_d'];
    $correct = $_POST['correct_answer'];

    $query = "INSERT INTO exam_items (exam_id, question, option_a, option_b, option_c, option_d, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("issssss", $exam_id, $question, $a, $b, $c, $d, $correct);

    if ($stmt->execute()) {
        header("Location: exam-items.php?exam_id=" . $exam_id);
    } else {
        echo "Error: " . $stmt->error;
    }
}

?>