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

$showSuccessModal = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $to_department = mysqli_real_escape_string($conn, $_POST['to_department']);
    $from_department = $_SESSION['department'];
    $datetime_sent = date('Y-m-d H:i:s');
    $file_path = null;

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
        $showSuccessModal = true;
    }
}

// Fetch departments excluding the current department
$departments = [];
$current_department = $_SESSION['department'];
$result = mysqli_query($conn, "SELECT department_name FROM departments WHERE department_name != '$current_department' GROUP BY department_name");
while ($row = mysqli_fetch_assoc($result)) {
    $departments[] = $row['department_name'];
}

// Fetch memos
$memos = [];
$memo_result = mysqli_query($conn, "SELECT * FROM memos WHERE to_department = '$current_department' ORDER BY datetime_sent DESC");
while ($row = mysqli_fetch_assoc($memo_result)) {
    $memos[] = $row;
}

// Handle memo file download
if (isset($_GET['download_memo_id'])) {
    $memo_id = $_GET['download_memo_id'];

    // Update the memo as downloaded in the database
    $update_sql = "UPDATE memos SET is_downloaded = 1 WHERE memo_id = $memo_id";
    if (mysqli_query($conn, $update_sql)) {
        // Fetch the file path for the memo
        $file_sql = "SELECT file_path FROM memos WHERE memo_id = $memo_id";
        $file_result = mysqli_query($conn, $file_sql);
        $file_row = mysqli_fetch_assoc($file_result);

        if ($file_row && file_exists($file_row['file_path'])) {
            // Trigger the file download
            header("Content-Type: application/octet-stream");
            header("Content-Disposition: attachment; filename=" . basename($file_row['file_path']));
            readfile($file_row['file_path']);
            
            // After download, reload the page to reflect the changes
            echo '<script>window.location.href = window.location.href;</script>';
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $_SESSION['department']; ?> Department</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Top Navigation -->
<div class="px-3 py-2 mb-5 text-bg-dark border-bottom">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-6 col-md-4">
                <span class="fs-3 text-white"><img src="../../img/5bdflogo.png" alt="5BDF Logo" style="width: 50px; height: auto;"> <?php echo $_SESSION['department']; ?></span>
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
                        <a href="exams.php" class="nav-link text-white">
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
                <form action="../../index.php" method="POST">
                    <button class="btn btn-outline-danger ms-2" onclick="window.location.href='logout.php'">Logout</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="container my-4">
    <div class="row mb-3 align-items-center">
        <div class="col-md-4">
            <h2 class="m-0">Memo</h2>
        </div>
        <div class="col-md-4 text-center">
            <input type="text" class="form-control" id="searchInput" placeholder="Search Memo..." onkeyup="filterMemos()">
        </div>
        <div class="col-md-4 text-end">
            <a href="./sent.php" class="btn btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-clock-history" viewBox="0 0 16 16">
                    <path d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022zm2.004.45a7 7 0 0 0-.985-.299l.219-.976q.576.129 1.126.342zm1.37.71a7 7 0 0 0-.439-.27l.493-.87a8 8 0 0 1 .979.654l-.615.789a7 7 0 0 0-.418-.302zm1.834 1.79a7 7 0 0 0-.653-.796l.724-.69q.406.429.747.91zm.744 1.352a7 7 0 0 0-.214-.468l.893-.45a8 8 0 0 1 .45 1.088l-.95.313a7 7 0 0 0-.179-.483m.53 2.507a7 7 0 0 0-.1-1.025l.985-.17q.1.58.116 1.17zm-.131 1.538q.05-.254.081-.51l.993.123a8 8 0 0 1-.23 1.155l-.964-.267q.069-.247.12-.501m-.952 2.379q.276-.436.486-.908l.914.405q-.24.54-.555 1.038zm-.964 1.205q.183-.183.35-.378l.758.653a8 8 0 0 1-.401.432z"/>
                    <path d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0z"/>
                    <path d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5"/>
                </svg>
            </a>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exampleModal">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-send" viewBox="0 0 16 16">
                    <path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576zm6.787-8.201L1.591 6.602l4.339 2.76z"/>
                </svg>
            </button>
        </div>
    </div>

    <table class="table table-striped table-bordered" id="memosTable">
        <thead>
            <tr>
                <th>Description</th>
                <th>From</th>
                <th>To</th>
                <th>Date and Time</th>
                <th>File</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($memos) > 0): ?>
                <?php foreach ($memos as $memo): ?>
                    <tr>
                        <td style="<?= $memo['is_downloaded'] ? '' : 'font-weight: bold;' ?>">
                            <?= htmlspecialchars($memo['description']) ?>
                        </td>
                        <td style="<?= $memo['is_downloaded'] ? '' : 'font-weight: bold;' ?>">
                            <?= htmlspecialchars($memo['from_department']) ?>
                        </td>
                        <td style="<?= $memo['is_downloaded'] ? '' : 'font-weight: bold;' ?>">
                            <?= htmlspecialchars($memo['to_department']) ?>
                        </td>
                        <td style="<?= $memo['is_downloaded'] ? '' : 'font-weight: bold;' ?>">
                            <?= date('Y-m-d H:i:s', strtotime($memo['datetime_sent'])) ?>
                        </td>
                        <td>
                            <?php if ($memo['file_path']): ?>
                                <a href="?download_memo_id=<?= $memo['memo_id'] ?>" class="btn btn-primary">Download</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">No memos found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Create Memo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="1" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="to_department" class="form-label">To Department</label>
                        <select class="form-select" id="to_department" name="to_department" required>
                            <option value="" disabled selected>Select Department</option>
                            <?php foreach ($departments as $department): ?>
                                <option value="<?= $department ?>"><?= $department ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="file" class="form-label">File</label>
                        <input class="form-control" type="file" id="file" name="file">
                    </div>
                    <button type="submit" class="btn btn-primary">Send Memo</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function filterMemos() {
    let input = document.getElementById("searchInput");
    let filter = input.value.toUpperCase();
    let table = document.getElementById("memosTable");
    let tr = table.getElementsByTagName("tr");

    for (let i = 1; i < tr.length; i++) {
        let tdDescription = tr[i].getElementsByTagName("td")[0]; // Description column
        let tdFrom = tr[i].getElementsByTagName("td")[1];         // From column
        let tdTo = tr[i].getElementsByTagName("td")[2];           // To column
        let tdDate = tr[i].getElementsByTagName("td")[3];         // Date column
        let tdFile = tr[i].getElementsByTagName("td")[4];         // File column
        
        if (tdDescription || tdFrom || tdTo || tdDate || tdFile) {
            let descriptionText = tdDescription.textContent || tdDescription.innerText;
            let fromText = tdFrom.textContent || tdFrom.innerText;
            let toText = tdTo.textContent || tdTo.innerText;
            let dateText = tdDate.textContent || tdDate.innerText;
            let fileText = tdFile.textContent || tdFile.innerText;

            // Check if any of the columns contain the search term
            if (
                descriptionText.toUpperCase().includes(filter) || 
                fromText.toUpperCase().includes(filter) || 
                toText.toUpperCase().includes(filter) || 
                dateText.toUpperCase().includes(filter) || 
                fileText.toUpperCase().includes(filter)
            ) {
                tr[i].style.display = ""; // Show row
            } else {
                tr[i].style.display = "none"; // Hide row
            }
        }
    }
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
</body>
</html>