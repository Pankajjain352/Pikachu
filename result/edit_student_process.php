<?php
/* ============================================
   edit_student_process.php – Update student in DB
   Uses prepared statements for security
   ============================================ */
require_once 'config.php';

// Auth guard
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: view_students.php');
    exit;
}

// Sanitize & validate
$id       = (int)($_POST['id'] ?? 0);
$name     = trim($_POST['name'] ?? '');
$roll_no  = trim($_POST['roll_no'] ?? '');
$class    = trim($_POST['class'] ?? '');
$section  = trim($_POST['section'] ?? 'A');
$math     = $_POST['math'] ?? '';
$science  = $_POST['science'] ?? '';
$english  = $_POST['english'] ?? '';
$hindi    = $_POST['hindi'] ?? '';
$social   = $_POST['social_science'] ?? '';

// Validation
$errors = [];
if ($id <= 0)        $errors[] = 'Invalid student ID.';
if (empty($name))    $errors[] = 'Name is required.';
if (empty($roll_no)) $errors[] = 'Roll number is required.';
if (empty($class))   $errors[] = 'Class is required.';

$subjects = ['math' => $math, 'science' => $science, 'english' => $english, 'hindi' => $hindi, 'social_science' => $social];
foreach ($subjects as $subName => $val) {
    if ($val === '' || !is_numeric($val) || (int)$val < 0 || (int)$val > 100) {
        $errors[] = ucfirst(str_replace('_', ' ', $subName)) . ' marks must be 0–100.';
    }
}

// Check duplicate roll (excluding current record)
if (empty($errors)) {
    $checkStmt = $conn->prepare("SELECT id FROM students WHERE roll_no = ? AND id != ?");
    $checkStmt->bind_param("si", $roll_no, $id);
    $checkStmt->execute();
    if ($checkStmt->get_result()->num_rows > 0) {
        $errors[] = 'Roll number already exists for another student!';
    }
    $checkStmt->close();
}

if (!empty($errors)) {
    $_SESSION['flash_msg'] = implode(' ', $errors);
    $_SESSION['flash_type'] = 'error';
    header("Location: edit_student.php?id=$id");
    exit;
}

// Update record
$stmt = $conn->prepare("
    UPDATE students 
    SET name = ?, roll_no = ?, class = ?, section = ?, 
        math = ?, science = ?, english = ?, hindi = ?, social_science = ?
    WHERE id = ?
");

$math    = (int)$math;
$science = (int)$science;
$english = (int)$english;
$hindi   = (int)$hindi;
$social  = (int)$social;

$stmt->bind_param("ssssiiiiiii", $name, $roll_no, $class, $section, $math, $science, $english, $hindi, $social, $id);

if ($stmt->execute()) {
    $_SESSION['flash_msg'] = "$name updated successfully!";
    $_SESSION['flash_type'] = 'success';
    header('Location: view_students.php');
} else {
    $_SESSION['flash_msg'] = 'Database error: ' . $stmt->error;
    $_SESSION['flash_type'] = 'error';
    header("Location: edit_student.php?id=$id");
}

$stmt->close();
exit;
?>
