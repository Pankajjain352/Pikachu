<?php
/* ============================================
   fetch_students.php – JSON API endpoint
   Returns all students as JSON (for AJAX use)
   ============================================ */
require_once 'config.php';

// Auth guard
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

// Optional filters via GET params
$search = trim($_GET['search'] ?? '');
$classFilter = trim($_GET['class'] ?? '');

$sql = "SELECT * FROM students WHERE 1=1";
$params = [];
$types = '';

if (!empty($search)) {
    $sql .= " AND (name LIKE ? OR roll_no LIKE ? OR class LIKE ?)";
    $searchWild = "%$search%";
    $params[] = $searchWild;
    $params[] = $searchWild;
    $params[] = $searchWild;
    $types .= 'sss';
}

if (!empty($classFilter)) {
    $sql .= " AND class = ?";
    $params[] = $classFilter;
    $types .= 's';
}

$sql .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

echo json_encode([
    'success' => true,
    'count' => count($students),
    'students' => $students
]);

$stmt->close();
?>
