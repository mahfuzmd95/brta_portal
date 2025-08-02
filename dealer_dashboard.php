<?php

session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'dealer') {
    header("Location: login.php");
    exit();
}

require_once 'db/config.php';

$dealer_code = $_SESSION['user_id'];

// মোট বিক্রিত বাইকের সংখ্যা
$totalSalesQuery = "SELECT COUNT(*) AS total_sales FROM sale_data WHERE dealer_code = '$dealer_code'";
$totalSalesResult = $conn->query($totalSalesQuery);
$totalSales = $totalSalesResult->fetch_assoc()['total_sales'] ?? 0;

// PDF ডাউনলোডযোগ্য বাইকের সংখ্যা
$pdfAvailableQuery = "
SELECT COUNT(*) AS available_pdf_count
FROM sale_data s
JOIN initial_data i ON (s.engine_no = i.engine_no OR s.chassis_no = i.chassis_no)
JOIN invoice_documents d ON i.invoice_no = d.invoice_no
WHERE s.dealer_code = '$dealer_code' AND s.access = 1
";

$pdfAvailableResult = $conn->query($pdfAvailableQuery);
$availablePdfCount = $pdfAvailableResult->fetch_assoc()['available_pdf_count'] ?? 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dealer Dashboard - BRTA Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2 class="mb-4">Welcome Dealer: <?= htmlspecialchars($_SESSION['user_name']) ?></h2>

    <div class="card mb-4">
        <div class="card-body">
            <h5>Total Bikes: <?= $totalSales ?></h5>
            <h5>Available for PDF Download: <?= $availablePdfCount ?></h5>
        </div>
    </div>

    <a href="index.php" class="btn btn-primary">Search & Download</a>
    <a href="logout.php" class="btn btn-danger float-end">Logout</a>
</body>
</html>
