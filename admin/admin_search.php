<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../db/config.php';

$resultData = null;
$message = '';

// Handle search
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_key'])) {
    $search = $conn->real_escape_string($_POST['search_key']);

    // Step 1: Search in initial_data
    $initSql = "SELECT * FROM initial_data WHERE engine_no = '$search' OR chassis_no = '$search' LIMIT 1";
    $initResult = $conn->query($initSql);

    if ($initResult->num_rows > 0) {
        $initRow = $initResult->fetch_assoc();

        $invoice = $initRow['invoice_no'];
        $item = $initRow['item_details'];
        $engineNo = $initRow['engine_no'];
        $chassisNo = $initRow['chassis_no'];

        // Step 2: Search for PDF in invoice_documents
        $pdfSql = "SELECT file_path FROM invoice_documents WHERE invoice_no = '$invoice' LIMIT 1";
        $pdfResult = $conn->query($pdfSql);

        $pdfPath = '';
        $pdfExists = false;
        $googleLink = '';

        if ($pdfResult->num_rows > 0) {
            $pdfRow = $pdfResult->fetch_assoc();
            $rawPath = $pdfRow['file_path'];

            // Check if it's a Google Drive or external link
            if (strpos($rawPath, 'http') === 0) {
                $googleLink = $rawPath;
                $pdfExists = false;
            } else {
                $fileFullPath = '../' . ltrim($rawPath, '/');
                $pdfExists = file_exists($fileFullPath);
                $pdfPath = $pdfExists ? $fileFullPath : '';
            }
        }

        // Step 3: Get dealer info from sale_data
        $dealerCode = 'N/A';
        $dealerName = 'N/A';

        $saleSql = "SELECT * FROM sale_data WHERE engine_no = '$engineNo' OR chassis_no = '$chassisNo' LIMIT 1";
        $saleResult = $conn->query($saleSql);

        if ($saleResult->num_rows > 0) {
            $saleRow = $saleResult->fetch_assoc();
            $dealerCode = $saleRow['dealer_code'];
            $dealerName = $saleRow['dealer_name'];
        }

        // Final result
        $resultData = [
            'invoice' => $invoice,
            'dealer_code' => $dealerCode,
            'dealer_name' => $dealerName,
            'engine_no' => $engineNo,
            'chassis_no' => $chassisNo,
            'item' => $item,
            'pdf_exists' => $pdfExists,
            'pdf_path' => $pdfPath,
            'google_link' => $googleLink
        ];
    } else {
        $message = "<div class='alert alert-danger'>No data found for given Engine/Chassis number.</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Search PDF</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="container mt-3">
    <h2 class="mb-4">🔍 Admin PDF Search - Engine No / Chassis No</h2>
    <!-- Back Button -->
    <a href="dashboard.php" class="btn btn-outline-dark mb-3">
        <i class="bi bi-arrow-left-circle"></i> Back to Dashboard
    </a>


    <?php if (!empty($message)) echo $message; ?>

    <!-- Search Form -->
    <form method="POST" class="mb-4">
        <div class="">
            <input type="text" name="search_key" class="form-control mb-3" placeholder="Enter Engine No or Chassis No" required>
            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Search</button>
        </div>
    </form>
    

    <?php if ($resultData): ?>
        <div class="card p-4 shadow">
            <h5>🧾 Invoice No: <strong><?= $resultData['invoice'] ?></strong></h5>
            <p class="mb-2">
                <strong>Dealer Code:</strong> <?= $resultData['dealer_code'] ?><br>
                <strong>Dealer Name:</strong> <?= $resultData['dealer_name'] ?><br>
                <strong>Engine No:</strong> <?= $resultData['engine_no'] ?><br>
                <strong>Chassis No:</strong> <?= $resultData['chassis_no'] ?><br>
                <strong>Item:</strong> <?= $resultData['item'] ?>
            </p>

            <?php if ($resultData['pdf_exists']): ?>
                <a href="<?= $resultData['pdf_path'] ?>" class="btn btn-success me-2" target="_blank">
                    <i class="bi bi-download"></i> Download PDF
                </a>
            <?php elseif (!empty($resultData['google_link'])): ?>
                <a href="<?= $resultData['google_link'] ?>" class="btn btn-warning" target="_blank">
                    <i class="bi bi-box-arrow-up-right"></i> View PDF on Google Drive
                </a>
            <?php else: ?>
                <div class="alert alert-warning mt-2">No PDF available locally or on Google Drive.</div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</body>
</html>
