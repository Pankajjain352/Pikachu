<?php
/* ============================================
   dashboard.php – Admin Dashboard
   Shows stats pulled from MySQL database
   ============================================ */
require_once 'config.php';

// Auth guard
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

$adminName = $_SESSION['admin_name'] ?? 'Admin';

// ---- Fetch dashboard stats from DB ----

// Total students
$totalResult = $conn->query("SELECT COUNT(*) AS cnt FROM students");
$totalStudents = $totalResult->fetch_assoc()['cnt'];

// Passed students (percentage >= 50)
$passResult = $conn->query("SELECT COUNT(*) AS cnt FROM students WHERE percentage >= 50");
$passedStudents = $passResult->fetch_assoc()['cnt'];

// Failed students
$failedStudents = $totalStudents - $passedStudents;

// Average percentage
$avgResult = $conn->query("SELECT ROUND(AVG(percentage), 1) AS avg_pct FROM students");
$avgPercentage = $avgResult->fetch_assoc()['avg_pct'] ?? 0;

// Recent 5 students
$recentResult = $conn->query("SELECT name, roll_no, percentage, grade, section, class FROM students ORDER BY created_at DESC LIMIT 5");
$recentStudents = [];
while ($row = $recentResult->fetch_assoc()) {
    $recentStudents[] = $row;
}

// Grade distribution
$gradeStats = [];
$gradeQuery = $conn->query("
    SELECT 
        SUM(CASE WHEN percentage >= 90 THEN 1 ELSE 0 END) AS a_plus,
        SUM(CASE WHEN percentage >= 80 AND percentage < 90 THEN 1 ELSE 0 END) AS a_grade,
        SUM(CASE WHEN percentage >= 65 AND percentage < 80 THEN 1 ELSE 0 END) AS b_grade,
        SUM(CASE WHEN percentage >= 50 AND percentage < 65 THEN 1 ELSE 0 END) AS c_grade,
        SUM(CASE WHEN percentage < 50 THEN 1 ELSE 0 END) AS f_grade
    FROM students
");
$gradeStats = $gradeQuery->fetch_assoc();
$gradeTotal = max($totalStudents, 1); // avoid division by zero

// Check for flash messages
$flash = $_SESSION['flash_msg'] ?? '';
$flashType = $_SESSION['flash_type'] ?? 'info';
unset($_SESSION['flash_msg'], $_SESSION['flash_type']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard – SRMS</title>
  <meta name="description" content="Admin Dashboard – Student Result Management System" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>

  <div class="app-layout">

    <!-- ==================== SIDEBAR ==================== -->
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-header">
        <div class="s-logo"><i class="fas fa-graduation-cap"></i></div>
        <span class="s-title">SRMS</span>
      </div>
      <nav class="sidebar-nav">
        <div class="nav-label">Main</div>
        <a href="dashboard.php" class="nav-item active">
          <i class="fas fa-chart-pie"></i> Dashboard
        </a>
        <a href="add_student.php" class="nav-item">
          <i class="fas fa-user-plus"></i> Add Student
        </a>
        <a href="view_students.php" class="nav-item">
          <i class="fas fa-users"></i> View Students
        </a>
        <div class="nav-label">Reports</div>
        <a href="result.php" class="nav-item">
          <i class="fas fa-file-lines"></i> Results
        </a>
      </nav>
      <div class="sidebar-footer">
        <a href="logout.php" class="nav-item">
          <i class="fas fa-right-from-bracket"></i> Logout
        </a>
      </div>
    </aside>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- ==================== MAIN CONTENT ==================== -->
    <div class="main-wrapper">

      <header class="top-header">
        <div class="left">
          <button class="hamburger" id="hamburgerBtn"><i class="fas fa-bars"></i></button>
          <h1 class="page-title">Dashboard</h1>
        </div>
        <div class="right">
          <div class="admin-badge">
            <div class="avatar"><?= strtoupper(substr($adminName, 0, 2)) ?></div>
            <span class="name"><?= htmlspecialchars($adminName) ?></span>
          </div>
        </div>
      </header>

      <main class="page-content">

        <!-- Stats Row -->
        <section class="stats-grid">
          <div class="stat-card">
            <div class="stat-icon purple"><i class="fas fa-user-graduate"></i></div>
            <div class="stat-value"><?= $totalStudents ?></div>
            <div class="stat-label">Total Students</div>
          </div>
          <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-circle-check"></i></div>
            <div class="stat-value"><?= $passedStudents ?></div>
            <div class="stat-label">Students Passed</div>
          </div>
          <div class="stat-card">
            <div class="stat-icon pink"><i class="fas fa-circle-xmark"></i></div>
            <div class="stat-value"><?= $failedStudents ?></div>
            <div class="stat-label">Students Failed</div>
          </div>
          <div class="stat-card">
            <div class="stat-icon yellow"><i class="fas fa-trophy"></i></div>
            <div class="stat-value"><?= $avgPercentage ?>%</div>
            <div class="stat-label">Average Percentage</div>
          </div>
        </section>

        <!-- Quick Info Grid -->
        <section class="quick-grid">

          <!-- Recent Students -->
          <div class="content-card">
            <div class="card-header">
              <h2><i class="fas fa-clock-rotate-left"></i> Recently Added</h2>
              <a href="view_students.php" class="btn btn-secondary btn-sm">View All</a>
            </div>
            <div class="card-body">
              <?php if (empty($recentStudents)): ?>
                <div class="empty-state">
                  <i class="fas fa-inbox"></i>
                  <h3>No students yet</h3>
                  <p>Add your first student to get started.</p>
                </div>
              <?php else: ?>
                <div class="recent-list">
                  <?php
                  $colors = ['#6366f1', '#ec4899', '#10b981', '#f59e0b', '#8b5cf6'];
                  foreach ($recentStudents as $i => $s):
                    $initials = implode('', array_map(fn($w) => strtoupper($w[0] ?? ''), explode(' ', $s['name'])));
                    $initials = substr($initials, 0, 2);
                    $passed = $s['percentage'] >= 50;
                  ?>
                    <div class="recent-item">
                      <div class="r-avatar" style="background: <?= $colors[$i % 5] ?>"><?= $initials ?></div>
                      <div class="r-info">
                        <h4><?= htmlspecialchars($s['name']) ?></h4>
                        <p>Roll: <?= htmlspecialchars($s['roll_no']) ?> &bull; <?= $s['percentage'] ?>% &bull; Grade <?= $s['grade'] ?></p>
                      </div>
                      <span class="badge <?= $passed ? 'badge-success' : 'badge-danger' ?>"><?= $passed ? 'Pass' : 'Fail' ?></span>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>
          </div>

          <!-- Grade Distribution -->
          <div class="content-card">
            <div class="card-header">
              <h2><i class="fas fa-chart-bar"></i> Grade Distribution</h2>
            </div>
            <div class="card-body">
              <?php
              $grades = [
                ['label' => 'A+ (90–100%)', 'val' => $gradeStats['a_plus'], 'class' => 'a-plus'],
                ['label' => 'A (80–89%)',    'val' => $gradeStats['a_grade'], 'class' => 'a'],
                ['label' => 'B (65–79%)',    'val' => $gradeStats['b_grade'], 'class' => 'b'],
                ['label' => 'C (50–64%)',    'val' => $gradeStats['c_grade'], 'class' => 'c'],
                ['label' => 'Fail (<50%)',   'val' => $gradeStats['f_grade'], 'class' => 'f'],
              ];
              foreach ($grades as $g):
                $pct = round(($g['val'] / $gradeTotal) * 100);
              ?>
                <div class="grade-bar-container">
                  <div class="grade-bar-label">
                    <span><?= $g['label'] ?></span>
                    <span><?= $g['val'] ?> (<?= $pct ?>%)</span>
                  </div>
                  <div class="grade-bar"><div class="fill <?= $g['class'] ?>" style="width: <?= $pct ?>%"></div></div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

        </section>
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
    <?php if ($flash): ?>
      showToast(<?= json_encode($flash) ?>, <?= json_encode($flashType) ?>);
    <?php endif; ?>
  </script>
</body>
</html>
