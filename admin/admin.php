<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}
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
  <title>Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
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
    <div class="row mb-3 align-items-center">
        <div class="col-md-4">
            <h2 class="m-0">Users</h2>
        </div>
        <div class="col-md-4 text-center">
            <input type="text" class="form-control" id="searchInput" placeholder="Search User..." onkeyup="filterUser()">
        </div>
        <div class="col-md-4 text-end">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addUserModal">New</button>
        </div>
    </div>
  <table class="table table-striped table-bordered" id="usersTable">
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
<script>
function filterUser() {
    let input = document.getElementById("searchInput");
    let filter = input.value.toUpperCase();
    let table = document.getElementById("usersTable"); // Make sure your table's ID is 'usersTable'
    let tr = table.getElementsByTagName("tr");

    for (let i = 1; i < tr.length; i++) {
        let tdUID = tr[i].getElementsByTagName("td")[0];
        let tdFullName = tr[i].getElementsByTagName("td")[1];
        let tdDepartment = tr[i].getElementsByTagName("td")[2];
        let tdRole = tr[i].getElementsByTagName("td")[3];

        if (tdUID || tdFullName || tdDepartment || tdRole) {
            let uidText = tdUID.textContent || tdUID.innerText;
            let nameText = tdFullName.textContent || tdFullName.innerText;
            let deptText = tdDepartment.textContent || tdDepartment.innerText;
            let roleText = tdRole.textContent || tdRole.innerText;

            if (
                uidText.toUpperCase().includes(filter) ||
                nameText.toUpperCase().includes(filter) ||
                deptText.toUpperCase().includes(filter) ||
                roleText.toUpperCase().includes(filter)
            ) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>