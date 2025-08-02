<?php
session_start();

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../db/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_type = $conn->real_escape_string($_POST['user_type']);
    $user_id = $conn->real_escape_string($_POST['user_id']);
    $user_name = $conn->real_escape_string($_POST['user_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']); // ❌ No hashing here (as per your request)

    // Check for duplicate user_id
    $checkSql = "SELECT id FROM users WHERE user_id = '$user_id'";
    $checkResult = $conn->query($checkSql);

    if ($checkResult->num_rows > 0) {
        $_SESSION['message'] = '<div class="alert alert-danger">User ID already exists.</div>';
    } else {
        $insertSql = "INSERT INTO users (user_type, user_id, user_name, email, password)
                      VALUES ('$user_type', '$user_id', '$user_name', '$email', '$password')";

        if ($conn->query($insertSql)) {
            $_SESSION['message'] = '<div class="alert alert-success">User added successfully!</div>';
        } else {
            $_SESSION['message'] = '<div class="alert alert-danger">Error: ' . $conn->error . '</div>';
        }
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add User - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

</head>
<body class="container mt-5">
    <h2 class="mb-4">Add New User</h2>

    <?php
    if (isset($_SESSION['message'])) {
        echo $_SESSION['message'];
        unset($_SESSION['message']);
    }
    ?>

    <form method="POST">
        <div class="mb-3">
            <label>User Type</label>
            <select name="user_type" class="form-control" required>
                <option value="">Select User Type</option>
                <option value="admin">Admin</option>
                <option value="dealer">Dealer</option>
            </select>
        </div>
        <div class="mb-3">
            <label>User ID</label>
            <input type="text" name="user_id" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="user_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control">
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="text" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Add User</button>
        <input type="reset" class="btn btn-secondary" value="Clear">
        
        <a href="dashboard.php" class="btn btn-outline-dark float-end">
            <i class="bi bi-arrow-left-circle"></i> Back to Dashboard
        </a>

    </form>
</body>
</html>
