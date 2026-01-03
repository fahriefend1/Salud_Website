<?php
require_once '../includes/config.php';

// Hapus semua session
session_destroy();

// Hapus cookies
setcookie('admin_user', '', time() - 3600, "/");
setcookie('admin_pass', '', time() - 3600, "/");

// Redirect ke login
header('Location: login.php');
exit();
?>