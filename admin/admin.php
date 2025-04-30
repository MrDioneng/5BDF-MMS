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
      <li class="nav-item"><a href="#" class="nav-link px-2 link-">Home</a></li>
      <li class="nav-item"><a href="./admin.php" class="nav-link px-2">Users</a></li>
      <li class="nav-item"><a href="./departments.php" class="nav-link px-2">Departments</a></li>
      <li class="nav-item"><a href="#" class="nav-link px-2">Memos</a></li>
      <li class="nav-item"><a href="#" class="nav-link px-2">Announcements</a></li>
    </ul>
  </nav>
  <div>
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