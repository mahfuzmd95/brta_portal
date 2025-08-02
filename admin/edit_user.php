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
$message = '';

// Get existing user info
$user = $conn->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();

if (!$user) {
    echo "<div class='alert alert-danger'>User not found.</div>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_name = $conn->real_escape_string($_POST['user_name']);
    $new_email = $conn->real_escape_string($_POST['email']);
    $new_password = $conn->real_escape_string($_POST['password']);

    if (!empty($new_password)) {
        // Update everything including password
        $update_sql = "UPDATE users SET user_name = '$new_name', email = '$new_email', password = '$new_password' WHERE id = $user_id";
    } else {
        // Update without changing password
        $update_sql = "UPDATE users SET user_name = '$new_name', email = '$new_email' WHERE id = $user_id";
    }

    if ($conn->query($update_sql)) {
        $message = '<div class="alert alert-success">User updated successfully!</div>';
        $user['user_name'] = $new_name;
        $user['email'] = $new_email;
        if (!empty($new_password)) {
            $user['password'] = $new_password;
        }
    } else {
        $message = '<div class="alert alert-danger">Error: ' . $conn->error . '</div>';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2 class="mb-4">Edit Dealer</h2>

    <?= $message ?>

    <form method="POST">
        <div class="mb-3">
            <label>User ID</label>
            <input type="text" class="form-control" value="<?= $user['user_id'] ?>" readonly>
        </div>
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="user_name" class="form-control" value="<?= $user['user_name'] ?>" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="<?= $user['email'] ?>">
        </div>
        <div class="mb-3">
            <label>Password (leave blank to keep old)</label>
            <input type="text" name="password" class="form-control" value="<?= $user['password'] ?>">
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="manage_users.php" class="btn btn-secondary">Back</a>

        <a href="dashboard.php" class="btn btn-outline-dark float-end">
            <i class="bi bi-arrow-left-circle"></i> Back to Dashboard
        </a>
    </form>
</body>
</html>
