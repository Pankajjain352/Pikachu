<?php
/* ============================================
   config.php – Database Connection
   Student Result Management System
   ============================================ */

// Start session on every page that includes this file
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database credentials (XAMPP defaults)
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');       // Default XAMPP has no password
define('DB_NAME', 'srms_db');

// Create MySQLi connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("
        <div style='font-family:Inter,sans-serif; padding:40px; text-align:center; color:#f87171; background:#0f1117; min-height:100vh; display:flex; align-items:center; justify-content:center; flex-direction:column;'>
            <h2>⚠️ Database Connection Failed</h2>
            <p style='color:#94a3b8; margin-top:10px;'>Error: " . $conn->connect_error . "</p>
            <p style='color:#64748b; margin-top:8px;'>Make sure XAMPP (MySQL) is running and import <code>database.sql</code> in phpMyAdmin.</p>
        </div>
    ");
}

// Set charset to UTF-8
$conn->set_charset("utf8mb4");
?>
