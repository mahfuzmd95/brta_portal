<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
require_once '../db/config.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Bike Access - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

</head>
<body class="container mt-4">
    <h3 class="mb-4">Manage Bike Access</h3>

    <a href="dashboard.php" class="btn btn-outline-dark float-end">
            <i class="bi bi-arrow-left-circle"></i> Back to Dashboard
    </a>

    <ul class="nav nav-tabs" id="accessTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="toggle-tab" data-bs-toggle="tab" data-bs-target="#toggle" type="button">Toggle Access</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="unaccess-tab" data-bs-toggle="tab" data-bs-target="#unaccess" type="button">Unaccess List</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="nopdf-tab" data-bs-toggle="tab" data-bs-target="#nopdf" type="button">No PDF List</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="nodealer-tab" data-bs-toggle="tab" data-bs-target="#nodealer" type="button">No Dealer List</button>
        </li>
    </ul>

    <div class="tab-content pt-3" id="accessTabsContent">
        <!-- Toggle Access Form -->
        <div class="tab-pane fade show active" id="toggle" role="tabpanel">
            <?php include 'partials/toggle_access.php'; ?>
        </div>

        <!-- Unaccess List -->
        <div class="tab-pane fade" id="unaccess" role="tabpanel">
            <?php include 'partials/unaccess_list.php'; ?>
        </div>

        <!-- No PDF List -->
        <div class="tab-pane fade" id="nopdf" role="tabpanel">
            <?php include 'partials/no_pdf_list.php'; ?>
        </div>

        <!-- No Dealer List -->
        <div class="tab-pane fade" id="nodealer" role="tabpanel">
            <?php include 'partials/no_dealer_list.php'; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
