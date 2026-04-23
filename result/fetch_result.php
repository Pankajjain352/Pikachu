<?php
/* ============================================
   fetch_result.php – JSON API endpoint
   Returns a single student's result by roll_no
   ============================================ */
require_once 'config.php';

// Auth guard
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

$roll = trim($_GET['roll'] ?? '');

if (empty($roll)) {
    echo json_encode(['success' => false, 'error' => 'Roll number is required.']);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM students WHERE roll_no = ?");
$stmt->bind_param("s", $roll);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $student = $result->fetch_assoc();
    echo json_encode([
        'success' => true,
        'student' => $student
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'No student found with roll number: ' . $roll
    ]);
}

$stmt->close();
?>
