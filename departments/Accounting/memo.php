<?php
  session_start();
  require_once '../../db/dbcon.php';
  date_default_timezone_set('Asia/Manila');

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $description = mysqli_real_escape_string($conn, $_POST['description']);
      $to_department = mysqli_real_escape_string($conn, $_POST['to_department']);
      $from_department = $_SESSION['department'];
      $datetime_sent = date('Y-m-d H:i:s');
      $file_path = null;

      // Handle file upload
      if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
          $file_tmp = $_FILES['file']['tmp_name'];
          $file_name = basename($_FILES['file']['name']);
          $file_dir = "../../uploads/";
          $file_path = $file_dir . $file_name;

          if (!move_uploaded_file($file_tmp, $file_path)) {
              $file_path = null;
          }
      }

      $sql = "INSERT INTO memos (description, from_department, to_department, datetime_sent, file_path)
              VALUES ('$description', '$from_department', '$to_department', '$datetime_sent', '$file_path')";

      if (mysqli_query($conn, $sql)) {
          $_SESSION['memo_success'] = true; 
      }
  }

  header("Location: dashboard.php");
  exit;
  ?>