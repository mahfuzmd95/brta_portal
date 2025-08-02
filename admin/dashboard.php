<?php
session_start();

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$admin_name = $_SESSION['user_name'] ?? 'Admin';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - BRTA Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .dashboard-card {
            transition: transform 0.2s;
        }
        .dashboard-card:hover {
            transform: scale(1.03);
        }
    </style>
</head>
<body class="bg-light">

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>👋 Welcome Admin: <span class="text-primary"><?= htmlspecialchars($admin_name) ?></span></h3>
        <a href="../logout.php" class="btn btn-danger">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </div>

    <div class="row g-4">

        <div class="col-md-4">
            <a href="upload_excel.php" class="btn btn-outline-primary dashboard-card w-100 shadow">
                <i class="bi bi-file-earmark-spreadsheet"></i> Upload Excel
            </a>
        </div>

        <div class="col-md-4">
            <a href="upload_pdf.php" class="btn btn-outline-primary dashboard-card w-100 shadow">
                <i class="bi bi-file-earmark-pdf"></i> Upload PDF
            </a>
        </div>

        <div class="col-md-4">
            <a href="upload_sale_data.php" class="btn btn-outline-primary dashboard-card w-100 shadow">
                <i class="bi bi-cloud-upload"></i> Upload Sale Data
            </a>
        </div>

        <div class="col-md-4">
            <a href="manage_users.php" class="btn btn-outline-success dashboard-card w-100 shadow">
                <i class="bi bi-people"></i> Manage Users
            </a>
        </div>

        <div class="col-md-4">
            <a href="add_user.php" class="btn btn-outline-success dashboard-card w-100 shadow">
                <i class="bi bi-person-plus"></i> Add New User
            </a>
        </div>

        <div class="col-md-4">
            <a href="merged_view.php" class="btn btn-outline-success dashboard-card w-100 shadow">
                <i class="bi bi-eye-fill"></i> View All Data
            </a>
        </div>

        <div class="col-md-4">
            <a href="admin_search.php" class="btn btn-outline-success dashboard-card w-100 shadow">
                <i class="bi bi-search"></i> Search PDF (Engine/Chassis)
            </a>
        </div>
        <div class="col-md-4">
            <a href="manage_access.php" class="btn btn-outline-success dashboard-card w-100 shadow">
                <i class="bi bi-tools"></i> Manage All Data
            </a>
        </div>

    </div>
</div>

</body>
</html>
