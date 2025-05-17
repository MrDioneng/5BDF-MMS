<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

require_once '../db/dbcon.php';

$exam_id = $_GET['exam_id'] ?? null;
$exam = null;

if ($exam_id) {
    $sql = "SELECT * FROM exams WHERE exam_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $exam_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $exam = $result->fetch_assoc();
    } else {
        header("Location: manage-exams.php?status=exam_not_found");
        exit();
    }
} else {
    header("Location: manage-exams.php?status=missing_exam_id");
    exit();
}

// Handle delete item request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_item'])) {
    $item_id = $_POST['delete_item'];

    $delete_sql = "DELETE FROM exam_items WHERE item_id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $item_id);

    if ($stmt->execute()) {
        header("Location: exam-items.php?exam_id=" . urlencode($exam_id) . "&status=item_deleted");
        exit();
    } else {
        echo "Error deleting item: " . $stmt->error;
        exit();
    }
}

// Handle update exam request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_exam'])) {
    $exam_id = $_POST['exam_id'] ?? null;
    $exam_title = $_POST['exam_title'] ?? '';
    $exam_department = $_POST['exam_department'] ?? '';
    $exam_duration = $_POST['exam_duration'] ?? '';
    $exam_description = $_POST['exam_description'] ?? '';

    if ($exam_id) {
        $query = "UPDATE exams SET exam_title = ?, department = ?, duration = ?, description = ? WHERE exam_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssi", $exam_title, $exam_department, $exam_duration, $exam_description, $exam_id);

        if ($stmt->execute()) {
            header("Location: manage-exams.php?status=success");
            exit();
        } else {
            echo "Error updating exam: " . $stmt->error;
            exit();
        }
    } else {
        header("Location: manage-exams.php?status=missing_id");
        exit();
    }
}

// Fetch exam items
$items_query = "SELECT * FROM exam_items WHERE exam_id = ?";
$stmt = $conn->prepare($items_query);
$stmt->bind_param("i", $exam_id);
$stmt->execute();
$items_result = $stmt->get_result();
$questions = $items_result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
  <title>Manage Exam</title>
</head>
<body>
    <header class="d-flex flex-wrap align-items-center justify-content-between py-2 px-4 border-bottom">
    <div class="d-flex align-items-center gap-2">
      <img src="../img/5bdflogo.png" alt="5BDF Logo" height="50" width="50">
      <h4 class="m-0">Admin</h4>
    </div>
    <nav>
      <ul class="nav">
          <li class="nav-item">
          <a href="#" class="nav-link d-flex flex-column align-items-center px-2">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-house mb-1" viewBox="0 0 16 16">
              <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L2 8.207V13.5A1.5 1.5 0 0 0 3.5 15h9a1.5 1.5 0 0 0 1.5-1.5V8.207l.646.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293zM13 7.207V13.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V7.207l5-5z"/>
              </svg>
              Home
          </a>
          </li>
          <li class="nav-item">
          <a href="./admin.php" class="nav-link d-flex flex-column align-items-center px-2">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-people mb-1" viewBox="0 0 16 16">
              <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4"/>
              </svg>
              Users
          </a>
          </li>
          <li class="nav-item">
          <a href="./departments.php" class="nav-link d-flex flex-column align-items-center px-2">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-building mb-1" viewBox="0 0 16 16">
              <path d="M4 2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zM4 5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zM7.5 5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm2.5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zM4.5 8a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm2.5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5z"/>
              <path d="M2 1a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1zm11 0H3v14h3v-2.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 .5.5V15h3z"/>
              </svg>
              Departments
          </a>
          </li>
          <li class="nav-item">
          <a href="./memos.php" class="nav-link d-flex flex-column align-items-center px-2">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-journal mb-1" viewBox="0 0 16 16">
              <path d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2"/>
              <path d="M1 5v-.5a.5.5 0 0 1 1 0V5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0v.5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1z"/>
              </svg>
              Memos
          </a>
          </li>
          <li class="nav-item">
          <a href="./announcements.php" class="nav-link d-flex flex-column align-items-center px-2">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-megaphone mb-1" viewBox="0 0 16 16">
              <path d="M13 2.5a1.5 1.5 0 0 1 3 0v11a1.5 1.5 0 0 1-3 0v-.214c-2.162-1.241-4.49-1.843-6.912-2.083l.405 2.712A1 1 0 0 1 5.51 15.1h-.548a1 1 0 0 1-.916-.599l-1.85-3.49-.202-.003A2.014 2.014 0 0 1 0 9V7a2.02 2.02 0 0 1 1.992-2.013 75 75 0 0 0 2.483-.075c3.043-.154 6.148-.849 8.525-2.199zm1 0v11a.5.5 0 0 0 1 0v-11a.5.5 0 0 0-1 0m-1 1.35c-2.344 1.205-5.209 1.842-8 2.033v4.233q.27.015.537.036c2.568.189 5.093.744 7.463 1.993zm-9 6.215v-4.13a95 95 0 0 1-1.992.052A1.02 1.02 0 0 0 1 7v2c0 .55.448 1.002 1.006 1.009A61 61 0 0 1 4 10.065m-.657.975 1.609 3.037.01.024h.548l-.002-.014-.443-2.966a68 68 0 0 0-1.722-.082z"/>
              </svg>
              Announcements
          </a>
          </li>
          <li class="nav-item">
            <a href="./Exams.php" class="nav-link d-flex flex-column align-items-center px-2">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square mb-1" viewBox="0 0 16 16">
                <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293z"/>
                <path d="M13.752 4.396l-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
              </svg>
              Exams
            </a>
          </li>
      </ul>
    </nav>

    <div>
      <button class="btn btn-outline-danger ms-2" onclick="window.location.href='logout.php'">Logout</button>
    </div>
  </header>

  <main class="d-flex">
    <div class="flex-shrink-0 p-3 bg-dark text-white min-vh-100" style="width: 200px;">
      <a href="#" class="d-flex align-items-center pb-3 mb-3 text-white text-decoration-none border-bottom">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
          class="bi bi-pen" viewBox="0 0 16 16">
          <path
            d="m13.498.795.149-.149a1.207 1.207 0 1 1 1.707 1.708l-.149.148a1.5 1.5 0 0 1-.059 2.059L4.854 14.854a.5.5 0 0 1-.233.131l-4 1a.5.5 0 0 1-.606-.606l1-4a.5.5 0 0 1 .131-.232l9.642-9.642a.5.5 0 0 0-.642.056L6.854 4.854a.5.5 0 1 1-.708-.708L9.44.854A1.5 1.5 0 0 1 11.5.796a1.5 1.5 0 0 1 1.998-.001m-.644.766a.5.5 0 0 0-.707 0L1.95 11.756l-.764 3.057 3.057-.764L14.44 3.854a.5.5 0 0 0 0-.708z" />
        </svg>
        <span class="fs-5 fw-semibold ms-2">EXAM</span>
      </a>

      <ul class="list-unstyled ps-0">
        <!-- Menu Items -->
        <li class="mb-1">
          <a href="manage-exams.php" class="btn btn-toggle border-0 text-white d-flex justify-content-between align-items-center w-100">
            <span>Create Exam</span>
          </a>
        </li>

        <li class="mb-1">
          <a href="manage-exams.php" class="btn btn-toggle border-0 text-white d-flex justify-content-between align-items-center w-100">
            <span>Rank By Exam</span>
          </a>
        </li>

        <li class="mb-1">
          <a href="manage-exams.php" class="btn btn-toggle border-0 text-white d-flex justify-content-between align-items-center w-100">
            <span>Examinee Result</span>
          </a>
        </li>      
      </ul>
    </div>

<div class="flex-grow-1 p-4">
  <div class="d-flex justify-content-center gap-4 flex-wrap">
    <?php if (!empty($exam)): ?>
      <div class="card" style="width: 45%;">
        <div class="card-body">
          <h5 class="card-title border-bottom pb-3">Edit Exam Information</h5>
          <form>
            <div class="mb-3">
              <input type="hidden" id="exam_id" name="exam_id" value="<?php echo htmlspecialchars($exam['exam_id']); ?>" required>

              <label for="exam_title" class="form-label">Exam Title</label>
              <input type="text" class="form-control" id="exam_title" name="exam_title" value="<?php echo htmlspecialchars($exam['exam_title']); ?>" disabled>
            </div>
            <div class="mb-3">
              <label for="exam_department" class="form-label">Department</label>
              <input type="text" class="form-control" id="exam_department" name="exam_department" value="<?php echo htmlspecialchars($exam['department']); ?>" disabled>
            </div>
            <div class="mb-3">
              <label for="exam_duration" class="form-label">Exam Duration</label>
              <input type="text" class="form-control" id="exam_duration" name="exam_duration" value="<?php echo htmlspecialchars($exam['duration']); ?>" disabled>
            </div>
            <div class="mb-3">
              <label for="exam_description" class="form-label">Exam Description</label>
              <input type="text" class="form-control" id="exam_description" name="exam_description" value="<?php echo htmlspecialchars($exam['description']); ?>" disabled>
            </div>

            <!-- Button to open the modal -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#updateExamModal<?php echo $exam['exam_id']; ?>">
              Update Exam
            </button>
          </form>
        </div>
      </div>

      <!-- Update Modal -->
      <div class="modal fade" id="updateExamModal<?php echo $exam['exam_id']; ?>" tabindex="-1" aria-labelledby="updateExamLabel<?php echo $exam['exam_id']; ?>" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <form action="update-exam.php" method="POST">
              <div class="modal-header">
                <h5 class="modal-title" id="updateExamLabel<?php echo $exam['exam_id']; ?>">Update Exam</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <input type="hidden" name="exam_id" value="<?php echo $exam['exam_id']; ?>">

                <div class="mb-3">
                  <label for="exam_title_<?php echo $exam['exam_id']; ?>" class="form-label">Exam Title</label>
                  <input type="text" class="form-control" id="exam_title_<?php echo $exam['exam_id']; ?>" name="exam_title" value="<?php echo htmlspecialchars($exam['exam_title']); ?>" required>
                </div>

                <div class="mb-3">
                  <label for="exam_department_<?php echo $exam['exam_id']; ?>" class="form-label">Department</label>
                  <input type="text" class="form-control" id="exam_department_<?php echo $exam['exam_id']; ?>" name="exam_department" value="<?php echo htmlspecialchars($exam['department']); ?>" required>
                </div>

                <div class="mb-3">
                  <label for="exam_duration_<?php echo $exam['exam_id']; ?>" class="form-label">Exam Duration</label>
                  <input type="text" class="form-control" id="exam_duration_<?php echo $exam['exam_id']; ?>" name="exam_duration" value="<?php echo htmlspecialchars($exam['duration']); ?>" required>
                </div>

                <div class="mb-3">
                  <label for="exam_description_<?php echo $exam['exam_id']; ?>" class="form-label">Exam Description</label>
                  <input type="text" class="form-control" id="exam_description_<?php echo $exam['exam_id']; ?>" name="exam_description" value="<?php echo htmlspecialchars($exam['description']); ?>" required>
                </div>
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-success">Save Changes</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <div class="card" style="width: 45%;">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h5 class="card-title mb-0">Exam Questions</h5>
          <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
            Add Question
          </button>
        </div>
        <p class="card-text">Number of Questions: <span id="questionCount"><?= count($questions) ?></span></p>
      </div>

      <div style="max-height: 350px; overflow-y: auto;">
        <?php if (count($questions) > 0): ?>
          <ol class="pe-2">
            <?php foreach ($questions as $q): ?>
              <li class="mb-3">
                <div class="d-flex justify-content-between align-items-start">
                  <strong><?= htmlspecialchars($q['question']) ?></strong>
                  <div>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#questionModal<?= $q['item_id'] ?>">Update</button>
                    <form method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this item?');">
                      <input type="hidden" name="delete_item" value="<?= $q['item_id'] ?>">
                      <input type="hidden" name="exam_id" value="<?= $exam_id ?>">
                      <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                  </div>
                </div>
                <ul class="mt-2">
                  <li>A. <?= htmlspecialchars($q['option_a']) ?></li>
                  <li>B. <?= htmlspecialchars($q['option_b']) ?></li>
                  <li>C. <?= htmlspecialchars($q['option_c']) ?></li>
                  <li>D. <?= htmlspecialchars($q['option_d']) ?></li>
                </ul>
                <p class="text-success"><em>Correct Answer: <?= strtoupper($q['correct_option']) ?></em></p>
                <hr>
              </li>

              <!-- Update Question Modal -->
              <div class="modal fade" id="questionModal<?= $q['item_id'] ?>" tabindex="-1" aria-labelledby="questionModalLabel<?= $q['item_id'] ?>" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content">
                    <form action="update-question.php" method="POST">
                      <div class="modal-header">
                        <h5 class="modal-title" id="questionModalLabel<?= $q['item_id'] ?>">Question Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>

                      <div class="modal-body">
                        <input type="hidden" name="item_id" value="<?= $q['item_id'] ?>">
                        <input type="hidden" name="exam_id" value="<?= $q['exam_id'] ?>">

                        <div class="mb-3">
                          <label for="questionText<?= $q['item_id'] ?>" class="form-label"><strong>Question</strong></label>
                          <input type="text" class="form-control" id="questionText<?= $q['item_id'] ?>" name="question" value="<?= htmlspecialchars($q['question']) ?>" required>
                        </div>

                        <div class="mb-3">
                          <label for="optionA<?= $q['item_id'] ?>" class="form-label"><strong>Option A</strong></label>
                          <input type="text" class="form-control" id="optionA<?= $q['item_id'] ?>" name="option_a" value="<?= htmlspecialchars($q['option_a']) ?>" required>
                        </div>

                        <div class="mb-3">
                          <label for="optionB<?= $q['item_id'] ?>" class="form-label"><strong>Option B</strong></label>
                          <input type="text" class="form-control" id="optionB<?= $q['item_id'] ?>" name="option_b" value="<?= htmlspecialchars($q['option_b']) ?>" required>
                        </div>

                        <div class="mb-3">
                          <label for="optionC<?= $q['item_id'] ?>" class="form-label"><strong>Option C</strong></label>
                          <input type="text" class="form-control" id="optionC<?= $q['item_id'] ?>" name="option_c" value="<?= htmlspecialchars($q['option_c']) ?>" required>
                        </div>

                        <div class="mb-3">
                          <label for="optionD<?= $q['item_id'] ?>" class="form-label"><strong>Option D</strong></label>
                          <input type="text" class="form-control" id="optionD<?= $q['item_id'] ?>" name="option_d" value="<?= htmlspecialchars($q['option_d']) ?>" required>
                        </div>

                        <hr>

                        <div class="mb-3">
                          <label for="correctOption<?= $q['item_id'] ?>" class="form-label"><strong>Correct Option</strong></label>
                          <select class="form-select" id="correctOption<?= $q['item_id'] ?>" name="correct_option" required>
                            <option value="A" <?= $q['correct_option'] === 'A' ? 'selected' : '' ?>>A</option>
                            <option value="B" <?= $q['correct_option'] === 'B' ? 'selected' : '' ?>>B</option>
                            <option value="C" <?= $q['correct_option'] === 'C' ? 'selected' : '' ?>>C</option>
                            <option value="D" <?= $q['correct_option'] === 'D' ? 'selected' : '' ?>>D</option>
                          </select>
                        </div>
                      </div>

                      <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </ol>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

    </div>
  </main>



  <!-- Add Question Modal -->
  <div class="modal fade" id="addQuestionModal" tabindex="-1" aria-labelledby="addQuestionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form action="add-question.php" method="POST">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addQuestionModalLabel">Add New Question</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">

            <div class="mb-3">
              <label for="question" class="form-label">Question</label>
              <textarea class="form-control" id="question" name="question" rows="3" required></textarea>
            </div>

            <div class="mb-3">
              <label for="option_a" class="form-label">Option A</label>
              <input type="text" class="form-control" id="option_a" name="option_a" required>
            </div>

            <div class="mb-3">
              <label for="option_b" class="form-label">Option B</label>
              <input type="text" class="form-control" id="option_b" name="option_b" required>
            </div>

            <div class="mb-3">
              <label for="option_c" class="form-label">Option C</label>
              <input type="text" class="form-control" id="option_c" name="option_c" required>
            </div>

            <div class="mb-3">
              <label for="option_d" class="form-label">Option D</label>
              <input type="text" class="form-control" id="option_d" name="option_d" required>
            </div>

            <div class="mb-3">
              <label for="correct_answer" class="form-label">Correct Answer</label>
              <select class="form-select" id="correct_answer" name="correct_answer" required>
                <option value="">Select Correct Option</option>
                <option value="A">Option A</option>
                <option value="B">Option B</option>
                <option value="C">Option C</option>
                <option value="D">Option D</option>
              </select>
            </div>

            <input type="hidden" name="exam_id" value="<?php echo $_GET['exam_id']; ?>">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Add Question</button>
          </div>
        </div>
      </form>
    </div>
  </div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-X..." crossorigin="anonymous"></script>

</body>
</html>