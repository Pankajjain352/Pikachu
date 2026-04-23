<?php
/* ============================================
   add_student_process.php – Insert student into DB
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
    header('Location: add_student.php');
    exit;
}

// Sanitize & validate inputs
$name     = trim($_POST['name'] ?? '');
$roll_no  = trim($_POST['roll_no'] ?? '');
$class    = trim($_POST['class'] ?? '');
$section  = trim($_POST['section'] ?? 'A');
$math     = $_POST['math'] ?? '';
$science  = $_POST['science'] ?? '';
$english  = $_POST['english'] ?? '';
$hindi    = $_POST['hindi'] ?? '';
$social   = $_POST['social_science'] ?? '';

// ---- Server-side validation ----
$errors = [];

if (empty($name))    $errors[] = 'Name is required.';
if (empty($roll_no)) $errors[] = 'Roll number is required.';
if (empty($class))   $errors[] = 'Class is required.';

// Validate marks
$subjects = ['math' => $math, 'science' => $science, 'english' => $english, 'hindi' => $hindi, 'social_science' => $social];
foreach ($subjects as $subName => $val) {
    if ($val === '' || !is_numeric($val) || (int)$val < 0 || (int)$val > 100) {
        $errors[] = ucfirst(str_replace('_', ' ', $subName)) . ' marks must be between 0 and 100.';
    }
}

// Check duplicate roll number
if (empty($errors)) {
    $checkStmt = $conn->prepare("SELECT id FROM students WHERE roll_no = ?");
    $checkStmt->bind_param("s", $roll_no);
    $checkStmt->execute();
    if ($checkStmt->get_result()->num_rows > 0) {
        $errors[] = 'Roll number already exists!';
    }
    $checkStmt->close();
}

// If validation fails, redirect back with error
if (!empty($errors)) {
    $_SESSION['flash_msg'] = implode(' ', $errors);
    $_SESSION['flash_type'] = 'error';
    header('Location: add_student.php');
    exit;
}

// ---- Insert into database ----
$stmt = $conn->prepare("
    INSERT INTO students (name, roll_no, class, section, math, science, english, hindi, social_science)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$math    = (int)$math;
$science = (int)$science;
$english = (int)$english;
$hindi   = (int)$hindi;
$social  = (int)$social;

$stmt->bind_param("ssssiiiii", $name, $roll_no, $class, $section, $math, $science, $english, $hindi, $social);

if ($stmt->execute()) {
    $_SESSION['flash_msg'] = "$name added successfully!";
    $_SESSION['flash_type'] = 'success';
} else {
    $_SESSION['flash_msg'] = 'Database error: ' . $stmt->error;
    $_SESSION['flash_type'] = 'error';
}

$stmt->close();
header('Location: add_student.php');
exit;
?>
