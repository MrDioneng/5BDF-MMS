<?php
  session_start();
  if (!isset($_SESSION['user_id'])) {
      header("Location: ../index.php");
      exit();
  }

  require_once '../db/dbcon.php';

  if (isset($_GET['id'])) {
      $exam_id = intval($_GET['id']);

      // Prepare and execute the delete query
      $stmt = $conn->prepare("DELETE FROM exams WHERE exam_id = ?");
      $stmt->bind_param("i", $exam_id);

      if ($stmt->execute()) {
          header("Location: manage-exams.php?deleted=success");
          exit();
      } else {
          echo "Error deleting exam: " . $stmt->error;
      }

      $stmt->close();
  } else {
      echo "Invalid request.";
  }
?>
