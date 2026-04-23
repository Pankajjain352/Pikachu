<?php
/* ============================================
   view_students.php – View All Students
   Table with search, filter, edit, delete
   ============================================ */
require_once 'config.php';

// Auth guard
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

$adminName = $_SESSION['admin_name'] ?? 'Admin';

// Fetch all students
$students = [];
$result = $conn->query("SELECT * FROM students ORDER BY created_at DESC");
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

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
  <title>View Students – SRMS</title>
  <meta name="description" content="View and manage all student records" />
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
        <a href="view_students.php" class="nav-item active"><i class="fas fa-users"></i> View Students</a>
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
          <h1 class="page-title">View Students</h1>
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
            <h2><i class="fas fa-users"></i> All Students (<?= count($students) ?>)</h2>
            <a href="add_student.php" class="btn btn-success btn-sm"><i class="fas fa-plus"></i> Add New</a>
          </div>
          <div class="card-body">

            <!-- Toolbar -->
            <div class="table-toolbar">
              <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Search by name, roll, or class..." />
              </div>
              <select id="filterClass" class="toolbar-select">
                <option value="">All Classes</option>
                <option value="10th">10th</option>
                <option value="11th">11th</option>
                <option value="12th">12th</option>
              </select>
            </div>

            <!-- Table -->
            <div class="table-wrapper">
              <table class="data-table" id="studentsTable">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Roll No</th>
                    <th>Class</th>
                    <th>Total</th>
                    <th>Percentage</th>
                    <th>Grade</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="studentsBody">
                  <?php if (empty($students)): ?>
                    <!-- JS will show empty state -->
                  <?php else: ?>
                    <?php foreach ($students as $i => $s):
                      $passed = $s['percentage'] >= 50;
                      $pctColor = $s['percentage'] >= 80 ? 'var(--success)' : ($s['percentage'] >= 50 ? 'var(--warning)' : 'var(--danger)');
                      $gradeBadge = $s['percentage'] >= 80 ? 'badge-success' : ($s['percentage'] >= 50 ? 'badge-warning' : 'badge-danger');
                    ?>
                      <tr data-name="<?= strtolower(htmlspecialchars($s['name'])) ?>" 
                          data-roll="<?= strtolower(htmlspecialchars($s['roll_no'])) ?>" 
                          data-class="<?= htmlspecialchars($s['class']) ?>">
                        <td><?= $i + 1 ?></td>
                        <td style="color: var(--text-primary); font-weight: 500;"><?= htmlspecialchars($s['name']) ?></td>
                        <td><?= htmlspecialchars($s['roll_no']) ?></td>
                        <td><span class="badge badge-primary"><?= htmlspecialchars($s['class']) ?> – <?= htmlspecialchars($s['section']) ?></span></td>
                        <td><?= $s['total'] ?>/500</td>
                        <td style="font-weight: 600; color: <?= $pctColor ?>;"><?= $s['percentage'] ?>%</td>
                        <td><span class="badge <?= $gradeBadge ?>"><?= $s['grade'] ?></span></td>
                        <td><span class="badge <?= $passed ? 'badge-success' : 'badge-danger' ?>"><?= $passed ? 'Pass' : 'Fail' ?></span></td>
                        <td>
                          <div class="action-btns">
                            <a href="result.php?roll=<?= urlencode($s['roll_no']) ?>" class="action-btn" title="View Result">
                              <i class="fas fa-eye"></i>
                            </a>
                            <a href="edit_student.php?id=<?= $s['id'] ?>" class="action-btn" title="Edit">
                              <i class="fas fa-pen-to-square"></i>
                            </a>
                            <a href="delete_student.php?id=<?= $s['id'] ?>" class="action-btn delete" title="Delete"
                               onclick="return confirm('Are you sure you want to delete this student?')">
                              <i class="fas fa-trash-alt"></i>
                            </a>
                          </div>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>

            <!-- Empty state -->
            <div class="empty-state <?= !empty($students) ? 'hidden' : '' ?>" id="emptyState">
              <i class="fas fa-inbox"></i>
              <h3>No students found</h3>
              <p>Try adjusting your search or add new students.</p>
            </div>

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
    // Flash messages
    <?php if ($flash): ?>
      showToast(<?= json_encode($flash) ?>, <?= json_encode($flashType) ?>);
    <?php endif; ?>

    // ---- Client-side search and filter ----
    const searchInput = document.getElementById('searchInput');
    const filterClass = document.getElementById('filterClass');
    const tbody = document.getElementById('studentsBody');
    const emptyState = document.getElementById('emptyState');
    const table = document.getElementById('studentsTable');

    function filterTable() {
      const query = searchInput.value.toLowerCase().trim();
      const cls = filterClass.value;
      const rows = tbody.querySelectorAll('tr');
      let visibleCount = 0;

      rows.forEach((row, idx) => {
        const name = row.dataset.name || '';
        const roll = row.dataset.roll || '';
        const rowClass = row.dataset.class || '';

        const matchSearch = !query || name.includes(query) || roll.includes(query) || rowClass.toLowerCase().includes(query);
        const matchClass = !cls || rowClass === cls;

        if (matchSearch && matchClass) {
          row.style.display = '';
          visibleCount++;
          // Update row number
          row.querySelector('td:first-child').textContent = visibleCount;
        } else {
          row.style.display = 'none';
        }
      });

      // Toggle empty state
      if (visibleCount === 0) {
        table.classList.add('hidden');
        emptyState.classList.remove('hidden');
      } else {
        table.classList.remove('hidden');
        emptyState.classList.add('hidden');
      }
    }

    searchInput.addEventListener('input', filterTable);
    filterClass.addEventListener('change', filterTable);
  </script>
</body>
</html>
