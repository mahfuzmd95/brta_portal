<?php
session_start();
require_once 'db/config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE user_id = '$user_id' AND password = '$password' LIMIT 1";
    $result = $conn->query($sql);

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_type'] = $user['user_type'];
        $_SESSION['user_name'] = $user['user_name'];

        if ($user['user_type'] === 'admin') {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: dealer_dashboard.php");
        }
        exit();
    } else {
        $error = "Invalid user ID or password!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <title>Login - BRTA Portal</title>
    <link rel="icon" type="image/png" href="images/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-card {
            max-width: 500px;
            margin: 80px auto;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .login-card .card-body {
            padding: 30px;
        }
        h4 {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>

</head>
<body class="container mt-5" style="max-width: 500px;">    

    <div class="card login-card">
        <div class="card-body">
            <h3 class="mb-4 text-center fw-bold">Login to BRTA Portal</h3>

            <form method="POST">
                <div class="mb-3">
                    <label>User ID</label>
                    <input type="text" name="user_id" class="form-control" placeholder="Enter your user ID" required>
                </div>
                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter the password" required>
                </div>
                <div class="d-grid gap-2">
                <button class="btn btn-primary" type="submit">Login</button>
                <button class="btn btn-secondary" type="reset">Reset</button>
                </div>
            </form>
    </div>
        <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
    </div>

        
</body>
</html>
