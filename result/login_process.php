<?php
/* ============================================
   login_process.php – Handle Login POST
   Validates credentials against MySQL database
   ============================================ */
require_once 'config.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Sanitize inputs
$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

// Validate inputs
if (empty($username) || empty($password)) {
    $_SESSION['login_error'] = 'Please fill in all fields.';
    header('Location: index.php');
    exit;
}

// Query database with prepared statement
$stmt = $conn->prepare("SELECT id, username, password, full_name FROM admins WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $admin = $result->fetch_assoc();

    // Verify password using password_verify (bcrypt)
    if (password_verify($password, $admin['password'])) {
        // Set session variables
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_name'] = $admin['full_name'];

        // Redirect to dashboard
        header('Location: dashboard.php');
        exit;
    }
}

// If we reach here, credentials are invalid
$_SESSION['login_error'] = 'Invalid username or password!';
header('Location: index.php');
exit;
?>
