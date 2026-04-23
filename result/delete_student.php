<?php
/* ============================================
   delete_student.php – Delete student from DB
   ============================================ */
require_once 'config.php';

// Auth guard
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    $_SESSION['flash_msg'] = 'Invalid student ID.';
    $_SESSION['flash_type'] = 'error';
    header('Location: view_students.php');
    exit;
}

// Delete using prepared statement
$stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    $_SESSION['flash_msg'] = 'Student deleted successfully.';
    $_SESSION['flash_type'] = 'success';
} else {
    $_SESSION['flash_msg'] = 'Student not found or could not be deleted.';
    $_SESSION['flash_type'] = 'error';
}

$stmt->close();
header('Location: view_students.php');
exit;
?>
