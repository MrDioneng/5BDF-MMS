<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}
require_once '../db/dbcon.php';

// Handle adding a department
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_department') {
    $department_name = $conn->real_escape_string($_POST['department_name']); // Sanitize the input

    // Insert query to add department
    $sql = "INSERT INTO departments (department_name) VALUES ('$department_name')";

    if ($conn->query($sql) === TRUE) {
        // Get the ID of the newly added department
        $department_id = $conn->insert_id;
        $folder_path = '../departments/' . $department_name; // Assuming the folder is named after the department ID

        // Create folder for the new department if it doesn't exist
        if (!file_exists($folder_path)) {
            mkdir($folder_path, 0777, true);
        }

        // Create the dashboard.php file if it doesn't exist
        $dashboard_file = $folder_path . '/dashboard.php';
        if (!file_exists($dashboard_file)) {
            $dashboard_content = <<<EOD
                        <?php
                        session_start();
                        if (!isset(\$_SESSION['user_id'])) {
                            header("Location: ../index.php");
                            exit();
                        }
                        require_once '../../db/dbcon.php';
                        date_default_timezone_set('Asia/Manila');

                        if (!isset(\$_SESSION['department'])) {
                            header("Location: ../../index.php");
                            exit;
                        }

                        \$showSuccessModal = false;

                        if (\$_SERVER['REQUEST_METHOD'] == 'POST') {
                            \$description = mysqli_real_escape_string(\$conn, \$_POST['description']);
                            \$to_department = mysqli_real_escape_string(\$conn, \$_POST['to_department']);
                            \$from_department = \$_SESSION['department'];
                            \$datetime_sent = date('Y-m-d H:i:s');
                            \$file_path = null;

                            if (isset(\$_FILES['file']) && \$_FILES['file']['error'] == 0) {
                                \$file_tmp = \$_FILES['file']['tmp_name'];
                                \$file_name = basename(\$_FILES['file']['name']);
                                \$file_dir = "../../uploads/";
                                \$file_path = \$file_dir . \$file_name;

                                if (!move_uploaded_file(\$file_tmp, \$file_path)) {
                                    \$file_path = null;
                                }
                            }

                            \$sql = "INSERT INTO memos (description, from_department, to_department, datetime_sent, file_path)
                                    VALUES ('\$description', '\$from_department', '\$to_department', '\$datetime_sent', '\$file_path')";
                            if (mysqli_query(\$conn, \$sql)) {
                                \$showSuccessModal = true;
                            }
                        }

                        // Fetch departments excluding the current department
                        \$departments = [];
                        \$current_department = \$_SESSION['department'];
                        \$result = mysqli_query(\$conn, "SELECT department_name FROM departments WHERE department_name != '\$current_department' GROUP BY department_name");
                        while (\$row = mysqli_fetch_assoc(\$result)) {
                            \$departments[] = \$row['department_name'];
                        }

                        // Fetch memos
                        \$memos = [];
                        \$memo_result = mysqli_query(\$conn, "SELECT * FROM memos WHERE to_department = '\$current_department' ORDER BY datetime_sent DESC");
                        while (\$row = mysqli_fetch_assoc(\$memo_result)) {
                            \$memos[] = \$row;
                        }

                        // Handle memo file download
                        if (isset(\$_GET['download_memo_id'])) {
                            \$memo_id = \$_GET['download_memo_id'];

                            // Update the memo as downloaded in the database
                            \$update_sql = "UPDATE memos SET is_downloaded = 1 WHERE memo_id = \$memo_id";
                            if (mysqli_query(\$conn, \$update_sql)) {
                                // Fetch the file path for the memo
                                \$file_sql = "SELECT file_path FROM memos WHERE memo_id = \$memo_id";
                                \$file_result = mysqli_query(\$conn, \$file_sql);
                                \$file_row = mysqli_fetch_assoc(\$file_result);

                                if (\$file_row && file_exists(\$file_row['file_path'])) {
                                    // Trigger the file download
                                    header("Content-Type: application/octet-stream");
                                    header("Content-Disposition: attachment; filename=" . basename(\$file_row['file_path']));
                                    readfile(\$file_row['file_path']);
                                    
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
                            <title><?php echo \$_SESSION['department']; ?> Department</title>
                            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
                        </head>
                        <body>

                        <!-- Top Navigation -->
                        <div class="px-3 py-2 mb-5 text-bg-dark border-bottom">
                            <div class="container">
                                <div class="row align-items-center">
                                    <div class="col-6 col-md-4">
                                        <span class="fs-3 text-white"><img src="../../img/5bdflogo.png" alt="5BDF Logo" style="width: 50px; height: auto;"> <?php echo \$_SESSION['department']; ?></span>
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
                                    <?php if (count(\$memos) > 0): ?>
                                        <?php foreach (\$memos as \$memo): ?>
                                           <tr>
                                                <td style="<?= \$memo['is_downloaded'] ? '' : 'font-weight: bold;' ?>">
                                                    <?= htmlspecialchars(\$memo['description']) ?>
                                                </td>
                                                <td style="<?= \$memo['is_downloaded'] ? '' : 'font-weight: bold;' ?>">
                                                    <?= htmlspecialchars(\$memo['from_department']) ?>
                                                </td>
                                                <td style="<?= \$memo['is_downloaded'] ? '' : 'font-weight: bold;' ?>">
                                                    <?= htmlspecialchars(\$memo['to_department']) ?>
                                                </td>
                                                <td style="<?= \$memo['is_downloaded'] ? '' : 'font-weight: bold;' ?>">
                                                    <?= date('Y-m-d H:i:s', strtotime(\$memo['datetime_sent'])) ?>
                                                </td>
                                                <td>
                                                    <?php if (\$memo['file_path']): ?>
                                                        <a href="?download_memo_id=<?= \$memo['memo_id'] ?>" class="btn btn-primary">Download</a>
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
                                                    <?php foreach (\$departments as \$department): ?>
                                                        <option value="<?= \$department ?>"><?= \$department ?></option>
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
                      EOD;
            file_put_contents($dashboard_file, $dashboard_content);
        }

        // Create the memo.php file if it doesn't exist
        $memo_file = $folder_path . '/memo.php';
        if (!file_exists($memo_file)) {
            $memo_content = <<<EOD
                <?php
                session_start();

                require_once '../../db/dbcon.php';
                date_default_timezone_set('Asia/Manila');

                if (\$_SERVER['REQUEST_METHOD'] == 'POST') {
                    \$description = mysqli_real_escape_string(\$conn, \$_POST['description']);
                    \$to_department = mysqli_real_escape_string(\$conn, \$_POST['to_department']);
                    \$from_department = \$_SESSION['department'];
                    \$datetime_sent = date('Y-m-d H:i:s');
                    \$file_path = null;

                    // Handle file upload
                    if (isset(\$_FILES['file']) && \$_FILES['file']['error'] == 0) {
                        \$file_tmp = \$_FILES['file']['tmp_name'];
                        \$file_name = basename(\$_FILES['file']['name']);
                        \$file_dir = "../../uploads/";
                        \$file_path = \$file_dir . \$file_name;

                        if (!move_uploaded_file(\$file_tmp, \$file_path)) {
                            \$file_path = null;
                        }
                    }

                    \$sql = "INSERT INTO memos (description, from_department, to_department, datetime_sent, file_path)
                            VALUES ('\$description', '\$from_department', '\$to_department', '\$datetime_sent', '\$file_path')";

                    if (mysqli_query(\$conn, \$sql)) {
                        \$_SESSION['memo_success'] = true; 
                    }
                }

                header("Location: dashboard.php");
                exit;
                ?>
              EOD;      
            file_put_contents($memo_file, $memo_content);
        }

        $sent_file = $folder_path . '/sent.php';
        if (!file_exists($sent_file)) {
            $sent_content = <<<EOD
                <?php
                session_start();
                if (!isset(\$_SESSION['user_id'])) {
                    header("Location: ../index.php");
                    exit();
                }
                require_once '../../db/dbcon.php';
                date_default_timezone_set('Asia/Manila');

                // Get the sent memos
                \$sql = "SELECT * FROM memos WHERE from_department = ? ORDER BY datetime_sent DESC";
                \$stmt = \$conn->prepare(\$sql);
                \$stmt->bind_param("s", \$_SESSION['department']);
                \$stmt->execute();
                \$result = \$stmt->get_result();

                // Fetch memos into an array
                \$memos = [];
                while (\$memo = \$result->fetch_assoc()) {
                    \$memos[] = \$memo;
                }

                // Check if memo is downloaded
                if (isset(\$_GET['download_memo_id'])) {
                    \$memo_id = \$_GET['download_memo_id'];
                    // Handle the download logic
                }
                ?>


                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <title><?php echo \$_SESSION['department']; ?> Department</title>
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
                </head>
                <body>

                <!-- Top Navigation -->
                <div class="px-3 py-2 mb-5 text-bg-dark border-bottom">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col-6 col-md-4">
                                <span class="fs-3 text-white"><img src="../../img/5bdflogo.png" alt="5BDF Logo" style="width: 50px; height: auto;"> <?php echo \$_SESSION['department']; ?></span>
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
                            <h2 class="m-0">Sent Memo</h2>
                        </div>
                        <div class="col-md-4 text-center">
                            <input type="text" class="form-control" id="searchInput" placeholder="Search Memo..." onkeyup="filterMemos()">
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="./dashboard.php" class="btn btn-secondary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-journal" viewBox="0 0 16 16">
                                <path d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2"/>
                                <path d="M1 5v-.5a.5.5 0 0 1 1 0V5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0v.5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1z"/>
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
                        <?php if (count(\$memos) > 0): ?>
                            <?php foreach (\$memos as \$memo): ?>
                                <tr>
                                    <td style="<?= \$memo['is_downloaded'] ?>">
                                        <?= htmlspecialchars(\$memo['description']) ?>
                                    </td>
                                    <td style="<?= \$memo['is_downloaded'] ?>">
                                        <?= htmlspecialchars(\$memo['from_department']) ?>
                                    </td>
                                    <td style="<?= \$memo['is_downloaded'] ?>">
                                        <?= htmlspecialchars(\$memo['to_department']) ?>
                                    </td>
                                    <td style="<?= \$memo['is_downloaded'] ?>">
                                        <?= date('Y-m-d H:i:s', strtotime(\$memo['datetime_sent'])) ?>
                                    </td>
                                    <td>
                                        <?php if (\$memo['file_path']): ?>
                                            <a href="?download_memo_id=<?= \$memo['memo_id'] ?>" class="btn btn-primary">Download</a>
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
                                            <?php foreach (\$departments as \$department): ?>
                                                <option value="<?= \$department ?>"><?= \$department ?></option>
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
                        let tdTo = tr[i].getElementsByTagName("td")[1];           // To column
                        let tdDate = tr[i].getElementsByTagName("td")[2];         // Date column
                        let tdFile = tr[i].getElementsByTagName("td")[3];         // File column
                        
                        if (tdDescription || tdTo || tdDate || tdFile) {
                            let descriptionText = tdDescription.textContent || tdDescription.innerText;
                            let toText = tdTo.textContent || tdTo.innerText;
                            let dateText = tdDate.textContent || tdDate.innerText;
                            let fileText = tdFile.textContent || tdFile.innerText;

                            // Check if any of the columns contain the search term
                            if (
                                descriptionText.toUpperCase().includes(filter) || 
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

              EOD;      
            file_put_contents($sent_file, $sent_content);
        }

        $logout_file = $folder_path . '/logout.php';
        if (!file_exists($logout_file)) {
            $logout_content = <<<EOD
                <?php
                session_start();
                session_unset();
                session_destroy();

                // Prevent caching to disable back button
                header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
                header("Cache-Control: post-check=0, pre-check=0", false);
                header("Pragma: no-cache");

                header("Location: ../index.php");
                exit();
                ?>
              EOD;      
            file_put_contents($logout_file, $logout_content);
        }

        header("Location: departments.php");
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Get departments from database
$sql = "SELECT * FROM departments";
$result = $conn->query($sql);

if (isset($_POST['update_department'])) {
    $department_id = $_POST['department_id'];
    $department_name = trim($_POST['department_name']);

    if (!empty($department_name)) {
        $stmt = $conn->prepare("UPDATE departments SET department_name = ? WHERE department_id = ?");
        $stmt->bind_param("si", $department_name, $department_id);
        $stmt->execute();

        // Redirect to avoid form resubmission
        header("Location: departments.php");
        exit();
    }
}

if (isset($_POST['delete_department'])) {
    $department_id = $_POST['department_id'];

    $stmt = $conn->prepare("DELETE FROM departments WHERE department_id = ?");
    $stmt->bind_param("i", $department_id);
    $stmt->execute();

    // Redirect to avoid form resubmission
    header("Location: departments.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
  <title>Departments</title>
</head>
<body>
  <header class="d-flex flex-wrap align-items-center justify-content-between py-3 px-4 border-bottom">
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
            </ul>
        </nav>
    <div>
        <button class="btn btn-outline-danger ms-2" onclick="window.location.href='logout.php'">Logout</button>
    </div>
  </header>

  <div class="container my-4">
    <div class="row mb-3 align-items-center">
      <div class="col-md-4">
        <h2 class="m-0">Departments</h2>
      </div>
      <div class="col-md-4 text-center">
        <input type="text" class="form-control" id="searchInput" placeholder="Search Department..." onkeyup="filterDepartments()">
      </div>
      <div class="col-md-4 text-end">
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addDepartmentModal">New</button>
      </div>
  </div>

    <!-- Departments Table -->
    <table class="table table-striped table-bordered" id="departmentsTable">
        <thead>
            <tr>
            <th>Department ID</th>
            <th>Department Name</th>
            <th>Action</th>
            </tr>
        </thead>
        <tbody id="departmentTable">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['department_id']; ?></td>
                        <td><?= $row['department_name']; ?></td>
                        <td class="text-center">
                            <!-- Update Button -->
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#updateModal<?= $row['department_id'] ?>">Update</button>
                            <!-- Delete Button -->
                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $row['department_id'] ?>">Delete</button>
                        </td>
                    </tr>

                    <!-- Update Modal for this department -->
                    <div class="modal fade" id="updateModal<?= $row['department_id'] ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <form method="POST" action="departments.php">
                            <div class="modal-content">
                                <div class="modal-header">
                                <h5 class="modal-title">Update Department</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                <input type="hidden" name="department_id" value="<?= $row['department_id'] ?>">
                                <div class="mb-3">
                                    <label class="form-label">UPDATED Department Name</label>
                                    <input type="text" class="form-control" name="department_name" value="<?= htmlspecialchars($row['department_name']) ?>" required>
                                </div>
                                </div>
                                <div class="modal-footer">
                                <button type="submit" name="update_department" class="btn btn-primary">Update</button>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>

                    <!-- Delete Modal for this department -->
                    <div class="modal fade" id="deleteModal<?= $row['department_id'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <form method="POST" action="departments.php">
                        <div class="modal-content">
                            <div class="modal-header">
                            <h5 class="modal-title">Delete Department</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                            Are you sure you want to delete "<strong><?= htmlspecialchars($row['department_name']) ?></strong>"?
                            <input type="hidden" name="department_id" value="<?= $row['department_id'] ?>">
                            </div>
                            <div class="modal-footer">
                            <button type="submit" name="delete_department" class="btn btn-danger">Delete</button>
                            </div>
                        </div>
                        </form>
                    </div>
                    </div>

                <?php endwhile; ?>
                <?php else: ?>
                <tr>
                    <td colspan="3" class="text-center">No departments present</td>
                </tr>
                <?php endif; ?>
        </tbody>
    </table>
  </div>

    <!-- Modal to add Department -->
    <div class="modal fade" id="addDepartmentModal" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content bg-white rounded" method="POST" action="departments.php">
            <div class="modal-header">
                <h5 class="modal-title">Add Department</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="action" value="add_department">
                <div class="mb-3">
                <label>Department Name</label>
                <input type="text" name="department_name" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-secondary">Add Department</button>
            </div>
            </form>
        </div>
    </div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>

<script>
  function filterDepartments() {
    let input = document.getElementById("searchInput");
    let filter = input.value.toUpperCase();
    let table = document.getElementById("departmentsTable"); // Make sure your table's ID is 'departmentsTable'
    let tr = table.getElementsByTagName("tr");

    for (let i = 1; i < tr.length; i++) { // skip header row
      let tdID = tr[i].getElementsByTagName("td")[0];
      let tdName = tr[i].getElementsByTagName("td")[1];

      if (tdID || tdName) {
        let idText = tdID.textContent || tdID.innerText;
        let nameText = tdName.textContent || tdName.innerText;

        if (
          idText.toUpperCase().includes(filter) ||
          nameText.toUpperCase().includes(filter)
        ) {
          tr[i].style.display = "";
        } else {
          tr[i].style.display = "none";
        }
      }
    }
  }
</script>


</body>
</html>

<?php
$conn->close();
?>
