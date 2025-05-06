<?php
session_start();
require_once './db/dbcon.php';

// Initialize variables
$loginError = '';
$uid = '';

// Function to sanitize input
function sanitizeInput($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Sanitize and validate input
        $uid = sanitizeInput($_POST['uid'] ?? '');
        $password = sanitizeInput($_POST['password'] ?? '');

        // Basic validation
        if (empty($uid) || empty($password)) {
            throw new Exception("UID and password are required");
        }

        // Prepare and execute query with prepared statement
        $stmt = $conn->prepare("SELECT * FROM users WHERE UID = ?");
        if (!$stmt) {
            throw new Exception("Database error: " . $conn->error);
        }

        $stmt->bind_param("s", $uid);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if ($user['password'] === $password) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['department'] = $user['department'];
                $_SESSION['UID'] = $user['UID'];

                if ($user['role'] === 'Admin') {
                    header("Location: ./admin/admin.php");
                    exit();
                } else {
                    $sanitized_dept = preg_replace('/[^A-Za-z0-9_-]/', '_', $user['department']);
                    $department_path = "./departments/$sanitized_dept/dashboard.php";
                    
                    // Check if dashboard exists for this department
                    if (file_exists($department_path)) {
                        header("Location: $department_path");
                    } else {
                        // Fallback to default dashboard if department dashboard doesn't exist
                        header("Location: ./departments/default_dashboard.php");
                    }
                    exit();
                }
            } else {
                throw new Exception("Invalid credentials");
            }
        } else {
            throw new Exception("Invalid credentials");
        }
    } catch (Exception $e) {
        $loginError = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Memo Management System</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .login-container {
      background-color: #f8f9fa;
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      padding: 2rem;
      max-width: 500px;
      width: 100%;
    }
    body {
      background-color: #e9ecef;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    main {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 1rem;
    }
    .logo-container {
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }
  </style>
</head>
<body>
  <header class="d-flex flex-wrap align-items-center justify-content-between py-3 px-4 border-bottom bg-white">
    <div class="logo-container">
      <img src="./img/5bdflogo.png" alt="5BDF Logo" height="50" width="50">
      <h4 class="m-0">Memo Management System</h4>
    </div>
  </header>

  <main>
    <div class="login-container">
      <form method="POST" action="" novalidate>
        <h1 class="h3 mb-4 fw-normal text-center">LOGIN</h1>

        <?php if ($loginError): ?>
          <div class="alert alert-danger alert-dismissible fade show">
            <?php echo htmlspecialchars($loginError); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>

        <div class="form-floating mb-3">
          <input type="text" name="uid" class="form-control" id="uid" 
                 placeholder="99999" required
                 value="<?php echo htmlspecialchars($uid); ?>">
          <label for="uid">USER</label>
        </div>

        <div class="form-floating mb-4">
          <input type="password" name="password" class="form-control" 
                 id="password" placeholder="Password" required>
          <label for="password">PASSWORD</label>
        </div>

        <button class="btn btn-primary w-100 py-2 mb-3" type="submit">Sign in</button>
        
        <div class="text-center">
          <a href="#" class="text-decoration-none">Forgot password?</a>
        </div>
        
        <p class="mt-4 mb-0 text-muted text-center">© 2025 5BDF Corporation</p>
      </form>
    </div>
  </main>

  <!-- Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>