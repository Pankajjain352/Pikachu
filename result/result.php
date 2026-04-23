<?php
/* ============================================
   result.php – Student Result Display
   Search by roll number, shows detailed card
   ============================================ */
require_once 'config.php';

// Auth guard
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

$adminName = $_SESSION['admin_name'] ?? 'Admin';

// Check if roll number was passed (via GET)
$searchRoll = trim($_GET['roll'] ?? '');
$student = null;
$searched = false;

if (!empty($searchRoll)) {
    $searched = true;
    $stmt = $conn->prepare("SELECT * FROM students WHERE roll_no = ?");
    $stmt->bind_param("s", $searchRoll);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $student = $result->fetch_assoc();
    }
    $stmt->close();
}

// Subject labels
$subjectLabels = [
    'math'            => 'Mathematics',
    'science'         => 'Science',
    'english'         => 'English',
    'hindi'           => 'Hindi',
    'social_science'  => 'Social Science'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Results – SRMS</title>
  <meta name="description" content="View student results with marks, percentage, and grade" />
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
        <a href="add_student.php" class="nav-item"><i class="fas fa-user-plus"></i> Add Student</a>
        <a href="view_students.php" class="nav-item"><i class="fas fa-users"></i> View Students</a>
        <div class="nav-label">Reports</div>
        <a href="result.php" class="nav-item active"><i class="fas fa-file-lines"></i> Results</a>
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
          <h1 class="page-title">Student Results</h1>
        </div>
        <div class="right">
          <div class="admin-badge">
            <div class="avatar"><?= strtoupper(substr($adminName, 0, 2)) ?></div>
            <span class="name"><?= htmlspecialchars($adminName) ?></span>
          </div>
        </div>
      </header>

      <main class="page-content">

        <!-- Search Area -->
        <div class="content-card">
          <div class="card-body">
            <div class="result-search-area">
              <h2><i class="fas fa-search" style="color: var(--primary-light); margin-right: 8px;"></i>Search Student Result</h2>
              <p>Enter the student's roll number to view their detailed result</p>
              <form class="result-search-form" method="GET" action="result.php">
                <input type="text" name="roll" id="resultRollInput" placeholder="Enter Roll Number (e.g. 2024001)" value="<?= htmlspecialchars($searchRoll) ?>" />
                <button type="submit" class="btn btn-primary" style="width: auto;">
                  <i class="fas fa-search"></i> Search
                </button>
              </form>
            </div>
          </div>
        </div>

        <?php if ($searched && $student): ?>
        <!-- ==================== RESULT CARD ==================== -->
        <div class="mt-3">
          <div class="result-card-container">
            <div class="result-card">

              <!-- Header -->
              <div class="result-card-header">
                <h3><?= htmlspecialchars($student['name']) ?></h3>
                <p>Class <?= htmlspecialchars($student['class']) ?> – Section <?= htmlspecialchars($student['section']) ?></p>
              </div>

              <!-- Info Grid -->
              <div class="result-info-grid">
                <div class="result-info-item">
                  <div class="label">Roll Number</div>
                  <div class="value"><?= htmlspecialchars($student['roll_no']) ?></div>
                </div>
                <div class="result-info-item">
                  <div class="label">Class & Section</div>
                  <div class="value"><?= htmlspecialchars($student['class']) ?> – <?= htmlspecialchars($student['section']) ?></div>
                </div>
                <div class="result-info-item">
                  <div class="label">Exam</div>
                  <div class="value">Annual Examination 2026</div>
                </div>
                <div class="result-info-item">
                  <div class="label">Status</div>
                  <div class="value">
                    <?php $passed = $student['percentage'] >= 50; ?>
                    <span class="badge <?= $passed ? 'badge-success' : 'badge-danger' ?>">
                      <?= $passed ? 'PASSED' : 'FAILED' ?>
                    </span>
                  </div>
                </div>
              </div>

              <!-- Marks Table -->
              <div class="result-marks-table">
                <table>
                  <thead>
                    <tr>
                      <th>Subject</th>
                      <th>Max Marks</th>
                      <th>Obtained</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($subjectLabels as $key => $label):
                      $marks = $student[$key];
                      $subPass = $marks >= 33;
                    ?>
                      <tr>
                        <td style="color: var(--text-primary); font-weight: 500;"><?= $label ?></td>
                        <td>100</td>
                        <td style="font-weight: 600; color: <?= $subPass ? 'var(--success)' : 'var(--danger)' ?>;"><?= $marks ?></td>
                        <td><span class="badge <?= $subPass ? 'badge-success' : 'badge-danger' ?>"><?= $subPass ? 'Pass' : 'Fail' ?></span></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>

              <!-- Summary -->
              <div class="result-summary">
                <div class="result-summary-item">
                  <div class="s-label">Total Marks</div>
                  <div class="s-value primary"><?= $student['total'] ?> / 500</div>
                </div>
                <div class="result-summary-item">
                  <div class="s-label">Percentage</div>
                  <div class="s-value success"><?= $student['percentage'] ?>%</div>
                </div>
                <div class="result-summary-item">
                  <div class="s-label">Grade</div>
                  <div class="s-value accent"><?= $student['grade'] ?></div>
                </div>
              </div>

            </div>

            <!-- Print Button -->
            <button class="btn btn-secondary print-btn mt-2" onclick="window.print()">
              <i class="fas fa-print"></i> Print Result
            </button>
          </div>
        </div>

        <?php elseif ($searched && !$student): ?>
        <!-- Not Found -->
        <div class="mt-3">
          <div class="content-card">
            <div class="card-body">
              <div class="empty-state">
                <i class="fas fa-user-slash"></i>
                <h3>Student Not Found</h3>
                <p>No student found with roll number "<strong><?= htmlspecialchars($searchRoll) ?></strong>". Please check and try again.</p>
              </div>
            </div>
          </div>
        </div>
        <?php endif; ?>

      </main>

      <footer class="app-footer">
        <p>&copy; 2026 SRMS – Student Result Management System</p>
        <p>Crafted with <i class="fas fa-heart" style="color: var(--accent);"></i> by Admin</p>
      </footer>
    </div>
  </div>

  <div class="toast-container" id="toastContainer"></div>
  <script src="js/script.js"></script>
</body>
</html>
