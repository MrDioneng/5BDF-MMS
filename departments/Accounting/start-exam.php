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
    exit();
}

if (!isset($_GET['exam_id'])) {
    echo "No exam selected.";
    exit();
}

$exam_id = intval($_GET['exam_id']);

// Prepare and execute the query
$sql = "SELECT * FROM exam_items WHERE exam_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    // Handle prepare statement error
    echo "Database error: " . htmlspecialchars($conn->error);
    exit();
}

$stmt->bind_param("i", $exam_id);

if (!$stmt->execute()) {
    // Handle execution error
    echo "Query execution failed: " . htmlspecialchars($stmt->error);
    exit();
}

$result = $stmt->get_result();

if (!$result) {
    echo "Failed to retrieve questions.";
    exit();
}


// Get exam duration
$exam_sql = "SELECT duration FROM exams WHERE exam_id = ?";
$exam_stmt = $conn->prepare($exam_sql);
$exam_stmt->bind_param("i", $exam_id);
$exam_stmt->execute();
$exam_result = $exam_stmt->get_result();
$exam_data = $exam_result->fetch_assoc();
$duration_minutes = (int)$exam_data['duration'];

$duration = isset($_GET['duration']) ? $_GET['duration'] : '00:30:00'; // fallback


?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
  <title>Exam</title>
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
                <a href="memo.php" class="nav-link text-white exam-nav-link">
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


    <div class="container mt-5">
      <h2 class="mb-4">Exam Questions</h2>

      <?php if ($result->num_rows > 0): ?>
        <div class="alert alert-warning text-center" id="timer">
          Time Remaining: <strong><span id="time-remaining"></span></strong>
        </div>

        <form action="submit_exam.php" method="POST">
          <input type="hidden" name="exam_id" value="<?php echo htmlspecialchars($exam_id); ?>">

          <?php $qNum = 1; ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <div class="mb-4 p-4 bg-light border rounded">
              <p><strong><?php echo $qNum++ . ". " . htmlspecialchars($row['question']); ?></strong></p>
              <?php foreach (['a', 'b', 'c', 'd'] as $opt): ?>
                <div class="form-check">
                  <input
                    class="form-check-input"
                    type="radio"
                    name="answers[<?php echo (int)$row['item_id']; ?>]"
                    id="q<?php echo (int)$row['item_id'] . $opt; ?>"
                    value="<?php echo $opt; ?>"
                    required
                  >
                  <label class="form-check-label" for="q<?php echo (int)$row['item_id'] . $opt; ?>">
                    <?php echo htmlspecialchars($row["option_$opt"]); ?>
                  </label>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endwhile; ?>

          <button type="submit" class="btn btn-success">Submit Answers</button>
        </form>
      <?php else: ?>
        <p>No questions found for this exam.</p>
      <?php endif; ?>
    </div>

    <div class="modal fade" id="timesUpModal" tabindex="-1" aria-labelledby="timesUpLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">
          <div class="modal-header">
            <h5 class="modal-title" id="timesUpLabel">Time's Up!</h5>
          </div>
          <div class="modal-body">
            The exam time has ended. Your answers will now be submitted automatically.
          </div>
          <div class="modal-footer">
            <button type="button" id="submitExamBtn" class="btn btn-primary">OK</button>
          </div>
        </div>
      </div>
    </div>

<script>
  const examId = "<?php echo $exam_id; ?>";
  const durationStr = "<?php echo $duration; ?>"; // e.g., "01:00:00"
  const timerDisplay = document.getElementById('time-remaining');

  function parseDuration(hms) {
    const [hours, minutes, seconds] = hms.split(':').map(Number);
    return (hours * 3600) + (minutes * 60) + seconds;
  }

  function formatTime(seconds) {
    const hrs = Math.floor(seconds / 3600).toString().padStart(2, '0');
    const mins = Math.floor((seconds % 3600) / 60).toString().padStart(2, '0');
    const secs = (seconds % 60).toString().padStart(2, '0');
    return `${hrs}:${mins}:${secs}`;
  }

  function getRemainingTime() {
    const now = Math.floor(Date.now() / 1000);
    const storedStart = localStorage.getItem(`exam_${examId}_start`);
    const durationSeconds = parseDuration(durationStr);

    if (storedStart) {
      const elapsed = now - parseInt(storedStart);
      return Math.max(durationSeconds - elapsed, 0);
    } else {
      // First time loading the page
      localStorage.setItem(`exam_${examId}_start`, now);
      return durationSeconds;
    }
  }

  let timeLeft = getRemainingTime();

  function updateTimer() {
    timerDisplay.textContent = formatTime(timeLeft);
    if (timeLeft <= 0) {
      clearInterval(timerInterval);
      alert("Time's up! Submitting your answers.");
      window.removeEventListener("beforeunload", beforeUnloadHandler);
      localStorage.removeItem(`exam_${examId}_start`);
      document.querySelector('form').submit();
    }
    timeLeft--;
  }

  function beforeUnloadHandler(e) {
    e.preventDefault();
    e.returnValue = "You are currently taking an exam. Are you sure you want to leave?";
    return '';
  }

  window.addEventListener("beforeunload", beforeUnloadHandler);

  updateTimer();
  const timerInterval = setInterval(updateTimer, 1000);
</script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</body>
</html>