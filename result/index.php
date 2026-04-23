<?php
/* ============================================
   index.php – Admin Login Page
   ============================================ */
require_once 'config.php';

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

// Check for messages from login_process.php
$error = $_SESSION['login_error'] ?? '';
$success = $_SESSION['login_success'] ?? '';
unset($_SESSION['login_error'], $_SESSION['login_success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Login – SRMS</title>
  <meta name="description" content="Student Result Management System – Admin Login Portal" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>

  <!-- Login Page -->
  <div class="login-page">
    <div class="login-container">
      <div class="login-card">

        <!-- Logo -->
        <div class="logo">
          <div class="logo-icon">
            <i class="fas fa-graduation-cap"></i>
          </div>
          <h1>SRMS</h1>
          <p>Student Result Management System</p>
        </div>

        <!-- Login Form -->
        <form id="loginForm" action="login_process.php" method="POST" autocomplete="off">
          <!-- Username -->
          <div class="form-group" id="userGroup">
            <label for="username">Username</label>
            <div class="input-wrapper">
              <input type="text" id="username" name="username" placeholder="Enter your username" autofocus required />
              <i class="fas fa-user"></i>
            </div>
            <div class="error-msg">Please enter a username</div>
          </div>

          <!-- Password -->
          <div class="form-group" id="passGroup">
            <label for="password">Password</label>
            <div class="input-wrapper">
              <input type="password" id="password" name="password" placeholder="Enter your password" required />
              <i class="fas fa-lock"></i>
            </div>
            <div class="error-msg">Please enter a password</div>
          </div>

          <!-- Submit -->
          <button type="submit" class="btn btn-primary" id="loginBtn">
            <i class="fas fa-right-to-bracket"></i> Sign In
          </button>
        </form>

        <!-- Credentials Hint -->
        <div class="login-hint">
          <p><i class="fas fa-circle-info"></i> Default credentials</p>
          <p>Username: <span>admin</span> &nbsp;|&nbsp; Password: <span>admin123</span></p>
        </div>

      </div>
    </div>
  </div>

  <!-- Toast container -->
  <div class="toast-container" id="toastContainer"></div>

  <script src="js/script.js"></script>
  <script>
    // Show server-side messages as toasts
    <?php if ($error): ?>
      showToast(<?= json_encode($error) ?>, 'error');
    <?php endif; ?>
    <?php if ($success): ?>
      showToast(<?= json_encode($success) ?>, 'success');
    <?php endif; ?>

    // Client-side validation
    document.getElementById('loginForm').addEventListener('submit', function (e) {
      const user = document.getElementById('username').value.trim();
      const pass = document.getElementById('password').value.trim();
      let valid = true;

      document.getElementById('userGroup').classList.remove('error');
      document.getElementById('passGroup').classList.remove('error');

      if (!user) { document.getElementById('userGroup').classList.add('error'); valid = false; }
      if (!pass) { document.getElementById('passGroup').classList.add('error'); valid = false; }

      if (!valid) e.preventDefault();
    });

    // Clear error on input
    document.getElementById('username').addEventListener('input', () => document.getElementById('userGroup').classList.remove('error'));
    document.getElementById('password').addEventListener('input', () => document.getElementById('passGroup').classList.remove('error'));
  </script>
</body>
</html>
