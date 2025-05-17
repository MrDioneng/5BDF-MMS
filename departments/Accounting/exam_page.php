<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}
require_once '../../db/dbcon.php';
date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['department'])) {
    header("Location: ../../index.php");
    exit;
}

$department = $_SESSION['department'] ?? null;

if (!$department) {
    echo "Department not specified.";
    exit;
}

// Fetch exams for the department
$sql = "SELECT * FROM exams WHERE department = ? OR department = (
            SELECT department_id FROM departments WHERE department_name = ?
        )";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $department, $department);
$stmt->execute();
$result = $stmt->get_result();



?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
  <title>Exams</title>
</head>
<body>
  <!-- Top Navigation -->
  <div class="px-3 py-2 mb-5 text-bg-dark border-bottom">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-6 col-md-4">
          <span class="fs-3 text-white">
            <img src="../../img/5bdflogo.png" alt="5BDF Logo" style="width: 50px; height: auto;">
            <?= htmlspecialchars($_SESSION['department']) ?>
          </span>
        </div>
        <div class="col-6 col-md-4 d-flex justify-content-center">
          <ul class="nav col-12 col-lg-auto my-2 justify-content-center my-md-0 text-small">
            <li class="nav-item mx-3 text-center">
              <a href="memo.php" class="nav-link text-white">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-file-earmark-fill" viewBox="0 0 16 16">
                  <path d="M4 0h5.293A1 1 0 0 1 10 .293L13.707 4a1 1 0 0 1 .293.707V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2m5.5 1.5v2a1 1 0 0 0 1 1h2z"/>
                </svg>
                <div>Memo</div>
              </a>
            </li>
            <li class="nav-item mx-3 text-center">
              <a href="announcements.php" class="nav-link text-white">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-megaphone-fill" viewBox="0 0 16 16">
                  <path d="M13 2.5a1.5 1.5 0 0 1 3 0v11a1.5 1.5 0 0 1-3 0zm-1 .724c-2.067.95-4.539 1.481-7 1.656v6.237a25 25 0 0 1 1.088.085c2.053.204 4.038.668 5.912 1.56zm-8 7.841V4.934c-.68.027-1.399.043-2.008.053A2.02 2.02 0 0 0 0 7v2c0 1.106.896 1.996 1.994 2.009l.496.008a64 64 0 0 1 1.51.048m1.39 1.081q.428.032.85.078l.253 1.69a1 1 0 0 1-.983 1.187h-.548a1 1 0 0 1-.916-.599l-1.314-2.48a66 66 0 0 1 1.692.064q.491.026.966.06"/>
                </svg>
                <div>Announcements</div>
              </a>
            </li>
            <li class="nav-item mx-3 text-center">
              <a href="exam_page.php" class="nav-link text-white">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                  <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                  <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                </svg>
                <div>Exams</div>
              </a>
            </li>
          </ul>
        </div>
        <div class="col-12 col-md-4 d-flex justify-content-end">
          <form action="logout.php" method="POST">
            <button type="submit" class="btn btn-outline-danger ms-2">Logout</button>
          </form>
        </div>
      </div>
    </div>
  </div>  

  <div class="container mt-4">
    <h2>Exams for <?= htmlspecialchars($department) ?> Department</h2>
    <table class="table table-bordered mt-3">
      <thead>
        <tr>
          <th>Exam Title</th>
          <th>Description</th>
          <th>Duration</th>
          <th>Score</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result->num_rows > 0): ?>
          <?php while($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['exam_title']) ?></td>
              <td><?= htmlspecialchars($row['description']) ?></td>
              <td><?= htmlspecialchars($row['duration']) ?></td>
              <td>—</td>
              <td>
                <button
                  class="btn btn-primary btn-sm"
                  data-bs-toggle="modal"
                  data-bs-target="#quizModal"
                  data-exam-id="<?php echo $row['exam_id']; ?>"
                  data-exam-title="<?php echo htmlspecialchars($row['exam_title'], ENT_QUOTES); ?>"
                  data-description="<?php echo htmlspecialchars($row['description'], ENT_QUOTES); ?>"
                  data-duration="<?php echo htmlspecialchars($row['duration']); ?>"
                >
                  Take Quiz
                </button>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="5" class="text-center">No exams available for your department.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Quiz Confirmation Modal -->
  <div class="modal fade" id="quizModal" tabindex="-1" aria-labelledby="quizModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="quizModalLabel">Ready to Take Quiz?</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p><strong>Title:</strong> <span id="modalExamTitle"></span></p>
          <p><strong>Description:</strong> <span id="modalExamDescription"></span></p>
          <p><strong>Duration:</strong> <span id="modalExamDuration"></span></p>
          <p class="mt-3">Are you ready to take this quiz?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Not Yet</button>
          <a id="startQuizBtn" href="#" class="btn btn-primary">Start Quiz</a>
        </div>
      </div>
    </div>
  </div>

  <script>
    const quizModal = document.getElementById('quizModal');
    quizModal.addEventListener('show.bs.modal', function (event) {
      const button = event.relatedTarget;

      const examId = button.getAttribute('data-exam-id');
      const title = button.getAttribute('data-exam-title');
      const description = button.getAttribute('data-description');
      const duration = button.getAttribute('data-duration');

      document.getElementById('modalExamTitle').textContent = title;
      document.getElementById('modalExamDescription').textContent = description;
      document.getElementById('modalExamDuration').textContent = duration;
      document.getElementById('startQuizBtn').href =
        'start-exam.php?exam_id=' + examId + '&duration=' + encodeURIComponent(duration);    
    });
  </script>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</body>
</html>
