<?php
session_start();

// Only allow admins
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../db/config.php';

if (!isset($_GET['id'])) {
    header("Location: manage_users.php");
    exit();
}

$user_id = intval($_GET['id']);

// Prevent deleting admin itself or others accidentally
if ($_SESSION['user_id'] == $user_id) {
    $_SESSION['message'] = '<div class="alert alert-warning">You cannot delete your own account.</div>';
    header("Location: manage_users.php");
    exit();
}

$deleteSql = "DELETE FROM users WHERE id = $user_id";

if ($conn->query($deleteSql)) {
    $_SESSION['message'] = '<div class="alert alert-success">User deleted successfully.</div>';
} else {
    $_SESSION['message'] = '<div class="alert alert-danger">Error deleting user: ' . $conn->error . '</div>';
}

header("Location: manage_users.php");
exit();
