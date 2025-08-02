<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require '../db/config.php';
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Sale Data - BRTA Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="container mt-5">

    <h2 class="mb-4">Upload Sale Data (Excel File)</h2>

    <!-- Upload Form -->
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="excel_file" accept=".xlsx, .xls" class="form-control mb-3" required>

        <!-- Overwrite Option -->
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="overwrite" value="1" id="overwriteCheck" checked>
            <label class="form-check-label" for="overwriteCheck">
                Overwrite duplicates instead of skipping
            </label>
        </div>

        <button type="submit" name="upload" class="btn btn-success">
            <i class="bi bi-upload"></i> Upload
        </button>
    </form>

    <hr>

    <!-- Download Template -->
    <a href="../uploads/sale_data_template.xlsx" download class="btn btn-success mb-3">
        <i class="bi bi-download"></i> Download Excel Template
    </a>

    <a href="dashboard.php" class="btn btn-outline-dark float-end">
        <i class="bi bi-arrow-left-circle"></i> Back to Dashboard
    </a>

    <!-- Upload Handling -->
    <?php
    if (isset($_POST['upload'])) {
        $file = $_FILES['excel_file']['tmp_name'];

        try {
            $spreadsheet = IOFactory::load($file);
            $data = $spreadsheet->getActiveSheet()->toArray();
        } catch (Exception $e) {
            echo "<div class='alert alert-danger mt-3'>Failed to read Excel file.</div>";
            exit();
        }

        $inserted = 0;
        $skipped = 0;
        $updated = 0;

        $overwrite = isset($_POST['overwrite']) && $_POST['overwrite'] == '1';

        foreach ($data as $index => $row) {
            if ($index == 0) continue; // Skip header

            list($chassis, $engine, $dealerCode, $dealerName, $access) = $row;

            // Check for existing record
            $check = $conn->prepare("SELECT id FROM sale_data WHERE chassis_no = ? AND engine_no = ?");
            $check->bind_param("ss", $chassis, $engine);
            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) {
                if ($overwrite) {
                    // Overwrite existing
                    $stmt = $conn->prepare("UPDATE sale_data SET dealer_code=?, dealer_name=?, access=? WHERE chassis_no=? AND engine_no=?");
                    $stmt->bind_param("ssiss", $dealerCode, $dealerName, $access, $chassis, $engine);
                    $stmt->execute();
                    $updated++;
                } else {
                    $skipped++;
                }
            } else {
                // Insert new
                $stmt = $conn->prepare("INSERT INTO sale_data (chassis_no, engine_no, dealer_code, dealer_name, access) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssi", $chassis, $engine, $dealerCode, $dealerName, $access);
                $stmt->execute();
                $inserted++;
            }
        }

        echo "<div class='alert alert-info mt-3'>Inserted: $inserted | Updated: $updated | Skipped (duplicate): $skipped</div>";
    }
    ?>
</body>
</html>
