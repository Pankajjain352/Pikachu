<?php
/* ============================================
   logout.php – Destroy session and redirect
   ============================================ */
require_once 'config.php';

// Destroy all session data
$_SESSION = [];
session_destroy();

// Redirect to login page
header('Location: index.php');
exit;
?>
