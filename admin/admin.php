<?php
session_start();
require_once '../db/dbcon.php';

$departments_result = $conn->query("SELECT department_name FROM departments ORDER BY department_name ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'add_user') {
        $uid = trim($_POST['uid'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $full_name = trim($_POST['full_name'] ?? '');
        $department = trim($_POST['department'] ?? '');

        if ($uid && $password && $full_name && $department) {
            $stmt = $conn->prepare("INSERT INTO users (UID, password, full_name, department) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $uid, $password, $full_name, $department);

            if ($stmt->execute()) {
                $_SESSION['success'] = "User created successfully!";
            } else {
                $_SESSION['error'] = "Error adding user: " . $stmt->error;
            }
            header("Location: admin.php");
            exit;
        } else {
            $_SESSION['error'] = "All fields are required.";
            header("Location: admin.php");
            exit;
        }
    }

    if ($action === 'update_user') {
        $user_id = intval($_POST['user_id']);
        $full_name = trim($_POST['full_name']);
        $department = trim($_POST['department']);
        $role = trim($_POST['role']);

        $stmt = $conn->prepare("UPDATE users SET full_name=?, department=?, role=? WHERE user_id=?");
        $stmt->bind_param("sssi", $full_name, $department, $role, $user_id);

        if ($stmt->execute()) {
            $_SESSION['success'] = "User updated successfully!";
        } else {
            $_SESSION['error'] = "Error updating user: " . $stmt->error;
        }
        header("Location: admin.php");
        exit;
    }

    if ($action === 'delete_user') {
        $user_id = intval($_POST['user_id']);

        $stmt = $conn->prepare("DELETE FROM users WHERE user_id=?");
        $stmt->bind_param("i", $user_id);

        if ($stmt->execute()) {
            $_SESSION['success'] = "User deleted successfully!";
        } else {
            $_SESSION['error'] = "Error deleting user: " . $stmt->error;
        }
        header("Location: admin.php");
        exit;
    }

    if ($action === 'add_department') {
        $department_name = trim($_POST['department_name'] ?? '');

        if (!empty($department_name)) {
            $stmt = $conn->prepare("INSERT INTO departments (department_name) VALUES (?)");
            $stmt->bind_param("s", $department_name);

            if ($stmt->execute()) {
                // Create folder and dashboard
                $sanitized_name = preg_replace('/[^A-Za-z0-9_\-]/', '_', $department_name);
                $folder_path = '../departments/' . $sanitized_name;
                if (!file_exists($folder_path)) {
                    mkdir($folder_path, 0777, true);
                }

                $dashboard_file = $folder_path . '/dashboard.php';
                if (!file_exists($dashboard_file)) {
                    $dashboard_content = <<<EOD
<?php
session_start();
require_once '../../db/dbcon.php';
date_default_timezone_set('Asia/Manila');

if (!isset(\$_SESSION['department'])) {
    header("Location: ../../index.php");
    exit;
}

\$showSuccessModal = false;

// Handle form submission
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
                    <button type="submit" class="btn btn-outline-light">Log Out</button>
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
            <a href="#" class="btn btn-secondary">Sent</a>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exampleModal">+</button>
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
                        <td><?= htmlspecialchars(\$memo['from_department']) ?></td>
                        <td><?= htmlspecialchars(\$memo['to_department']) ?></td>
                        <td><?= date('Y-m-d H:i:s', strtotime(\$memo['datetime_sent'])) ?></td>
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
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
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

</body>
</html>


EOD;
                    file_put_contents($dashboard_file, $dashboard_content);
                }

                $_SESSION['success'] = "Department created successfully with dashboard!";
            } else {
                $_SESSION['error'] = "Error adding department: " . $stmt->error;
            }
            header("Location: admin.php");
            exit;
        } else {
            $_SESSION['error'] = "Department name is required.";
            header("Location: admin.php");
            exit;
        }
    }
}

// Handle Search and Sort
$search_term = $_GET['search'] ?? '';
$department_term = $_GET['department'] ?? '';
$sort_by = $_GET['sort'] ?? 'full_name';
$order = ($_GET['order'] ?? 'asc') === 'desc' ? 'desc' : 'asc';

// Sanitize column names to avoid SQL Injection
$allowed_sort = ['user_id', 'UID', 'full_name', 'department', 'role'];
if (!in_array($sort_by, $allowed_sort)) {
    $sort_by = 'full_name';
}

$query = "SELECT * FROM users WHERE full_name LIKE ? AND department LIKE ? ORDER BY $sort_by $order";
$stmt = $conn->prepare($query);
$search_like = "%$search_term%";
$department_like = "%$department_term%";
$stmt->bind_param("ss", $search_like, $department_like);
$stmt->execute();
$users = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Users</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<header class="d-flex flex-wrap align-items-center justify-content-between py-3 px-4 border-bottom">
  <div class="d-flex align-items-center gap-2">
    <img src="../img/5bdflogo.png" alt="5BDF Logo" height="50" width="50">
    <h4 class="m-0">Admin</h4>
  </div>
  <nav>
    <ul class="nav">
      <li class="nav-item"><a href="#" class="nav-link px-2 link-secondary">Home</a></li>
      <li class="nav-item"><a href="./admin.php" class="nav-link px-2">Users</a></li>
      <li class="nav-item"><a href="#" class="nav-link px-2">Memos</a></li>
      <li class="nav-item"><a href="#" class="nav-link px-2">Announcements</a></li>
    </ul>
  </nav>
  <div>
    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#createModal">Create</button>
    <button class="btn btn-outline-secondary ms-2" data-bs-toggle="modal" data-bs-target="#searchModal">Search</button>
    <button class="btn btn-outline-danger ms-2" onclick="window.location.href='../index.php'">Logout</button>
  </div>
</header>

<?php if (isset($_SESSION['success'])): ?>
<div class="alert alert-success alert-dismissible fade show m-3" role="alert">
  <?= htmlspecialchars($_SESSION['success']) ?>
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php unset($_SESSION['success']); endif; ?>

<?php if (isset($_SESSION['error'])): ?>
<div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
  <?= htmlspecialchars($_SESSION['error']) ?>
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php unset($_SESSION['error']); endif; ?>

<div class="container my-4">
  <table class="table table-striped">
    <thead>
      <tr>
        <th><a href="?sort=user_id&order=<?= $order === 'asc' ? 'desc' : 'asc' ?>">ID</a></th>
        <th><a href="?sort=UID&order=<?= $order === 'asc' ? 'desc' : 'asc' ?>">UID</a></th>
        <th><a href="?sort=full_name&order=<?= $order === 'asc' ? 'desc' : 'asc' ?>">Full Name</a></th>
        <th><a href="?sort=department&order=<?= $order === 'asc' ? 'desc' : 'asc' ?>">Department</a></th>
        <th><a href="?sort=role&order=<?= $order === 'asc' ? 'desc' : 'asc' ?>">Role</a></th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row = $users->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['user_id']) ?></td>
        <td><?= htmlspecialchars($row['UID']) ?></td>
        <td><?= htmlspecialchars($row['full_name']) ?></td>
        <td><?= htmlspecialchars($row['department']) ?></td>
        <td><?= htmlspecialchars($row['role']) ?></td>
        <td>
          <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#updateModal<?= $row['user_id'] ?>">Update</button>
          <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $row['user_id'] ?>">Delete</button>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<!-- Create Options Modal -->
<div class="modal fade" id="createModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Create Options</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body d-flex flex-column gap-3">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal" data-bs-dismiss="modal">User</button>
        <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#addDepartmentModal" data-bs-dismiss="modal">Department</button>
      </div>
    </div>
  </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="admin.php">
      <div class="modal-header">
        <h5 class="modal-title">Add User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="action" value="add_user">
        <div class="mb-3">
          <label>UID</label>
          <input type="text" name="uid" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Password</label>
          <input type="text" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Full Name</label>
          <input type="text" name="full_name" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Department</label>
          <select name="department" class="form-select" required>
            <option disabled selected value="">Choose Department</option>
            <?php
            $departments_result2 = $conn->query("SELECT department_name FROM departments ORDER BY department_name ASC");
            while ($dept = $departments_result2->fetch_assoc()):
            ?>
              <option value="<?= htmlspecialchars($dept['department_name']) ?>"><?= htmlspecialchars($dept['department_name']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Add User</button>
      </div>
    </form>
  </div>
</div>

<!-- Add Department Modal -->
<div class="modal fade" id="addDepartmentModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="admin.php">
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

<!-- Search Modal -->
<div class="modal fade" id="searchModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="GET" action="admin.php">
      <div class="modal-header">
        <h5 class="modal-title">Search User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label>Search Full Name</label>
          <input type="text" name="search" class="form-control" value="<?= htmlspecialchars($search_term) ?>" placeholder="Enter full name">
        </div>
        <div class="mb-3">
          <label>Search Department</label>
          <input type="text" name="department" class="form-control" value="<?= htmlspecialchars($department_term) ?>" placeholder="Enter department">
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Search</button>
      </div>
    </form>
  </div>
</div>

<!-- Update Modals for each user -->
<?php 
$users->data_seek(0); // Reset pointer to beginning
while($row = $users->fetch_assoc()): 
?>
<div class="modal fade" id="updateModal<?= $row['user_id'] ?>" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="admin.php">
      <div class="modal-header">
        <h5 class="modal-title">Update User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="action" value="update_user">
        <input type="hidden" name="user_id" value="<?= $row['user_id'] ?>">
        <div class="mb-3">
          <label>Full Name</label>
          <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($row['full_name']) ?>" required>
        </div>
        <div class="mb-3">
          <label>Department</label>
          <select name="department" class="form-select" required>
            <?php
            $dept_result = $conn->query("SELECT department_name FROM departments ORDER BY department_name ASC");
            while ($dept = $dept_result->fetch_assoc()):
              $selected = ($dept['department_name'] == $row['department']) ? 'selected' : '';
            ?>
              <option value="<?= htmlspecialchars($dept['department_name']) ?>" <?= $selected ?>>
                <?= htmlspecialchars($dept['department_name']) ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="mb-3">
          <label>Role</label>
          <select name="role" class="form-select" required>
            <option value="User" <?= $row['role'] == 'User' ? 'selected' : '' ?>>User</option>
            <option value="Admin" <?= $row['role'] == 'Admin' ? 'selected' : '' ?>>Admin</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save changes</button>
      </div>
    </form>
  </div>
</div>
<?php endwhile; ?>

<!-- Delete Modals for each user -->
<?php 
$users->data_seek(0); // Reset pointer to beginning
while($row = $users->fetch_assoc()): 
?>
<div class="modal fade" id="deleteModal<?= $row['user_id'] ?>" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="admin.php">
      <div class="modal-header">
        <h5 class="modal-title">Delete User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="action" value="delete_user">
        <input type="hidden" name="user_id" value="<?= $row['user_id'] ?>">
        <p>Are you sure you want to delete <strong><?= htmlspecialchars($row['full_name']) ?></strong>?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-danger">Delete</button>
      </div>
    </form>
  </div>
</div>
<?php endwhile; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>