<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require '../db/config.php';
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$message = '';

// Handle upload
if (isset($_POST['upload'])) {
    $file = $_FILES['excel_file']['tmp_name'];
    $fileName = $_FILES['excel_file']['name'];
    $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);

    // Allow only Excel files
    if (!in_array(strtolower($fileExt), ['xlsx', 'xls'])) {
        $message = "<div class='alert alert-danger'>Only Excel files (.xlsx, .xls) are allowed!</div>";
    } else {
        if ($file) {
            $spreadsheet = IOFactory::load($file);
            $data = $spreadsheet->getActiveSheet()->toArray();

            $inserted = 0;
            $updated = 0;

            for ($i = 1; $i < count($data); $i++) {
                $row = $data[$i];
                $invoice_no = $conn->real_escape_string($row[0]);
                $item_code = $conn->real_escape_string($row[1]);
                $item_details = $conn->real_escape_string($row[2]);
                $engine_no = $conn->real_escape_string($row[3]);
                $chassis_no = $conn->real_escape_string($row[4]);

                // Check for duplicates
                $check_sql = "SELECT id FROM initial_data WHERE engine_no = '$engine_no' OR chassis_no = '$chassis_no'";
                $check_result = $conn->query($check_sql);

                if ($check_result->num_rows > 0) {
                    // If exists, update it
                    $update_sql = "UPDATE initial_data SET 
                        invoice_no = '$invoice_no',
                        item_code = '$item_code',
                        item_details = '$item_details',
                        engine_no = '$engine_no',
                        chassis_no = '$chassis_no'
                        WHERE engine_no = '$engine_no' OR chassis_no = '$chassis_no'";
                    $conn->query($update_sql);
                    $updated++;
                } else {
                    // If not, insert new row
                    $insert_sql = "INSERT INTO initial_data (invoice_no, item_code, item_details, engine_no, chassis_no)
                                VALUES ('$invoice_no', '$item_code', '$item_details', '$engine_no', '$chassis_no')";
                    $conn->query($insert_sql);
                    $inserted++;
                }
            }

            // Redirect with summary
            header("Location: upload_excel.php?inserted=$inserted&updated=$updated");
            exit();
        } else {
            $message = "<div class='alert alert-danger'>Invalid file!</div>";
        }
    }
}

// Show success message after redirect
if (isset($_GET['inserted']) || isset($_GET['updated'])) {
    $inserted = $_GET['inserted'] ?? 0;
    $updated = $_GET['updated'] ?? 0;
    $message = "<div class='alert alert-success mt-3'>
        Excel file processed successfully!<br>
        <strong>New Inserted:</strong> $inserted<br>
        <strong>Updated (Duplicates):</strong> $updated
    </div>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Upload Excel - BRTA Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Upload Invoice wise Engine & Chassis (Excel File)</h2>

    <?= $message ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <input type="file" name="excel_file" class="form-control" required>
        </div>
        <button type="submit" name="upload" class="btn btn-primary">Upload</button>
    </form>
    <hr>

    <a href="../uploads/BRTA_Excel_Template.xlsx" download class="btn btn-success mb-3">
        <i class="bi bi-download"></i> Download Excel Template
    </a>


    <a href="dashboard.php" class="btn btn-outline-dark float-end">
            <i class="bi bi-arrow-left-circle"></i> Back to Dashboard
        </a>
</body>
</html>
