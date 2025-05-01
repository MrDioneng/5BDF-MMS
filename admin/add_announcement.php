<?php
session_start();
require_once '../db/dbcon.php';
date_default_timezone_set('Asia/Manila');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add_announcement') {
    $what  = $_POST['what'];
    $where = $_POST['where'];
    $when  = $_POST['when'];
    $dateCreated = date('Y-m-d H:i:s');

    $insert_sql = "
      INSERT INTO announcements
        (`date_created`, `what`, `where`, `when`)
      VALUES
        (?,      ?,      ?,      ?)
    ";

    if ($stmt = mysqli_prepare($conn, $insert_sql)) {
        mysqli_stmt_bind_param($stmt, 'ssss', $dateCreated, $what, $where, $when);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        if ($success) {
            echo <<<JS
            <script>
              window.onload = function() {
                alert('Announcement posted successfully.');
                window.location.href = 'announcements.php';
              };
            </script>
            JS;
            exit;
        } else {
            echo <<<JS
            <script>
              window.onload = function() {
                alert('Failed to post announcement. Please try again.');
                window.location.href = 'announcements.php';
              };
            </script>
            JS;
            exit;
        }
    } else {
        echo <<<JS
        <script>
          window.onload = function() {
            alert('Database error. Please contact the administrator.');
            window.location.href = 'announcements.php';
          };
        </script>
        JS;
        exit;
    }
}

// If not a POST-add_announcement, just redirect back:
header('Location: announcements.php');
exit;
