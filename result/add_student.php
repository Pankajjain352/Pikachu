<?php
/* ============================================
   add_student.php – Add Student Form
   ============================================ */
require_once 'config.php';

// Auth guard
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

$adminName = $_SESSION['admin_name'] ?? 'Admin';

// Flash messages
$flash = $_SESSION['flash_msg'] ?? '';
$flashType = $_SESSION['flash_type'] ?? 'info';
unset($_SESSION['flash_msg'], $_SESSION['flash_type']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Add Student – SRMS</title>
  <meta name="description" content="Add a new student with subject marks" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>

  <div class="app-layout">

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-header">
        <div class="s-logo"><i class="fas fa-graduation-cap"></i></div>
        <span class="s-title">SRMS</span>
      </div>
      <nav class="sidebar-nav">
        <div class="nav-label">Main</div>
        <a href="dashboard.php" class="nav-item"><i class="fas fa-chart-pie"></i> Dashboard</a>
        <a href="add_student.php" class="nav-item active"><i class="fas fa-user-plus"></i> Add Student</a>
        <a href="view_students.php" class="nav-item"><i class="fas fa-users"></i> View Students</a>
        <div class="nav-label">Reports</div>
        <a href="result.php" class="nav-item"><i class="fas fa-file-lines"></i> Results</a>
      </nav>
      <div class="sidebar-footer">
        <a href="logout.php" class="nav-item"><i class="fas fa-right-from-bracket"></i> Logout</a>
      </div>
    </aside>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="main-wrapper">

      <header class="top-header">
        <div class="left">
          <button class="hamburger" id="hamburgerBtn"><i class="fas fa-bars"></i></button>
          <h1 class="page-title">Add Student</h1>
        </div>
        <div class="right">
          <div class="admin-badge">
            <div class="avatar"><?= strtoupper(substr($adminName, 0, 2)) ?></div>
            <span class="name"><?= htmlspecialchars($adminName) ?></span>
          </div>
        </div>
      </header>

      <main class="page-content">
        <div class="content-card">
          <div class="card-header">
            <h2><i class="fas fa-user-plus"></i> New Student Registration</h2>
          </div>
          <div class="card-body">
            <form id="addStudentForm" action="add_student_process.php" method="POST" autocomplete="off">

              <!-- Personal Info -->
              <div class="form-row">
                <div class="form-group" id="nameGroup">
                  <label for="studentName">Full Name <span style="color: var(--danger);">*</span></label>
                  <input type="text" id="studentName" name="name" placeholder="e.g. Arihant Sharma" />
                  <div class="error-msg">Name is required</div>
                </div>
                <div class="form-group" id="rollGroup">
                  <label for="rollNo">Roll Number <span style="color: var(--danger);">*</span></label>
                  <input type="text" id="rollNo" name="roll_no" placeholder="e.g. 2024001" />
                  <div class="error-msg">Roll number is required</div>
                </div>
              </div>

              <div class="form-row">
                <div class="form-group" id="classGroup">
                  <label for="studentClass">Class <span style="color: var(--danger);">*</span></label>
                  <select id="studentClass" name="class">
                    <option value="">Select Class</option>
                    <option value="10th">10th</option>
                    <option value="11th">11th</option>
                    <option value="12th">12th</option>
                  </select>
                  <div class="error-msg">Please select a class</div>
                </div>
                <div class="form-group">
                  <label for="section">Section</label>
                  <select id="section" name="section">
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                  </select>
                </div>
              </div>

              <!-- Subject Marks -->
              <div class="marks-section-title">
                <i class="fas fa-pen-ruler"></i> Subject Marks (out of 100)
              </div>

              <div class="form-row">
                <div class="form-group" id="mathGroup">
                  <label for="math">Mathematics <span style="color: var(--danger);">*</span></label>
                  <input type="number" id="math" name="math" min="0" max="100" placeholder="0 – 100" />
                  <div class="error-msg">Enter valid marks (0–100)</div>
                </div>
                <div class="form-group" id="scienceGroup">
                  <label for="science">Science <span style="color: var(--danger);">*</span></label>
                  <input type="number" id="science" name="science" min="0" max="100" placeholder="0 – 100" />
                  <div class="error-msg">Enter valid marks (0–100)</div>
                </div>
              </div>

              <div class="form-row">
                <div class="form-group" id="englishGroup">
                  <label for="english">English <span style="color: var(--danger);">*</span></label>
                  <input type="number" id="english" name="english" min="0" max="100" placeholder="0 – 100" />
                  <div class="error-msg">Enter valid marks (0–100)</div>
                </div>
                <div class="form-group" id="hindiGroup">
                  <label for="hindi">Hindi <span style="color: var(--danger);">*</span></label>
                  <input type="number" id="hindi" name="hindi" min="0" max="100" placeholder="0 – 100" />
                  <div class="error-msg">Enter valid marks (0–100)</div>
                </div>
              </div>

              <div class="form-row">
                <div class="form-group" id="ssGroup">
                  <label for="socialScience">Social Science <span style="color: var(--danger);">*</span></label>
                  <input type="number" id="socialScience" name="social_science" min="0" max="100" placeholder="0 – 100" />
                  <div class="error-msg">Enter valid marks (0–100)</div>
                </div>
              </div>

              <!-- Actions -->
              <div class="form-actions mt-2">
                <button type="submit" class="btn btn-success"><i class="fas fa-plus-circle"></i> Add Student</button>
                <button type="reset" class="btn btn-secondary" id="resetBtn"><i class="fas fa-rotate-left"></i> Reset</button>
              </div>

            </form>
          </div>
        </div>
      </main>

      <footer class="app-footer">
        <p>&copy; 2026 SRMS – Student Result Management System</p>
        <p>Crafted with <i class="fas fa-heart" style="color: var(--accent);"></i> by Admin</p>
      </footer>
    </div>
  </div>

  <div class="toast-container" id="toastContainer"></div>
  <script src="js/script.js"></script>
  <script>
    // Show flash messages
    <?php if ($flash): ?>
      showToast(<?= json_encode($flash) ?>, <?= json_encode($flashType) ?>);
    <?php endif; ?>

    // Client-side form validation
    document.getElementById('addStudentForm').addEventListener('submit', function (e) {
      let valid = true;

      // Validate required text fields
      function checkRequired(id, groupId) {
        const val = document.getElementById(id).value.trim();
        const g = document.getElementById(groupId);
        if (!val) { g.classList.add('error'); return false; }
        g.classList.remove('error'); return true;
      }

      // Validate marks range
      function checkMarks(id, groupId) {
        const val = document.getElementById(id).value.trim();
        const g = document.getElementById(groupId);
        if (val === '' || isNaN(val) || +val < 0 || +val > 100) { g.classList.add('error'); return false; }
        g.classList.remove('error'); return true;
      }

      if (!checkRequired('studentName', 'nameGroup')) valid = false;
      if (!checkRequired('rollNo', 'rollGroup')) valid = false;
      if (!checkRequired('studentClass', 'classGroup')) valid = false;
      if (!checkMarks('math', 'mathGroup')) valid = false;
      if (!checkMarks('science', 'scienceGroup')) valid = false;
      if (!checkMarks('english', 'englishGroup')) valid = false;
      if (!checkMarks('hindi', 'hindiGroup')) valid = false;
      if (!checkMarks('socialScience', 'ssGroup')) valid = false;

      if (!valid) {
        e.preventDefault();
        showToast('Please fix the errors above.', 'error');
      }
    });

    // Clear error on input
    document.querySelectorAll('#addStudentForm input, #addStudentForm select').forEach(el => {
      el.addEventListener('input', () => el.closest('.form-group')?.classList.remove('error'));
    });

    // Reset also clears errors
    document.getElementById('resetBtn').addEventListener('click', () => {
      document.querySelectorAll('.form-group.error').forEach(g => g.classList.remove('error'));
    });
  </script>
</body>
</html>
