<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
require '../db/config.php';

$message = '';

if (isset($_POST['upload'])) {
    $invoice_no = $conn->real_escape_string($_POST['invoice_no']);
    $upload_type = $_POST['upload_type'];

    if ($upload_type === "file") {
        $pdf_file = $_FILES['pdf_file'];
        $file_type = mime_content_type($pdf_file['tmp_name']);

        if ($file_type === "application/pdf") {
            $target_dir = "../downloads/";
            $new_filename = $invoice_no . "_" . time() . ".pdf";
            $target_file = $target_dir . $new_filename;
            $file_path_db = "downloads/" . $new_filename;

            if (move_uploaded_file($pdf_file['tmp_name'], $target_file)) {
                $check = $conn->query("SELECT * FROM invoice_documents WHERE invoice_no = '$invoice_no'");
                if ($check->num_rows > 0) {
                    $old = $check->fetch_assoc();
                    $old_file = "../" . $old['file_path'];
                    if (file_exists($old_file)) unlink($old_file);

                    $stmt = $conn->prepare("UPDATE invoice_documents SET file_path = ? WHERE invoice_no = ?");
                    $stmt->bind_param("ss", $file_path_db, $invoice_no);
                    $stmt->execute();

                    $message = "<div class='alert alert-info mt-3'>Old PDF replaced successfully!</div>";
                } else {
                    $stmt = $conn->prepare("INSERT INTO invoice_documents (invoice_no, file_path) VALUES (?, ?)");
                    $stmt->bind_param("ss", $invoice_no, $file_path_db);
                    $stmt->execute();

                    $message = "<div class='alert alert-success mt-3'>New PDF uploaded successfully!</div>";
                }
            } else {
                $message = "<div class='alert alert-danger mt-3'>Failed to upload the PDF file!</div>";
            }
        } else {
            $message = "<div class='alert alert-warning mt-3'>Only PDF files are allowed!</div>";
        }

    } elseif ($upload_type === "link") {
        $google_drive_link = $conn->real_escape_string($_POST['google_drive_link']);

        if (filter_var($google_drive_link, FILTER_VALIDATE_URL) && strpos($google_drive_link, 'drive.google.com') !== false) {
            $check = $conn->query("SELECT * FROM invoice_documents WHERE invoice_no = '$invoice_no'");
            if ($check->num_rows > 0) {
                $stmt = $conn->prepare("UPDATE invoice_documents SET file_path = ? WHERE invoice_no = ?");
                $stmt->bind_param("ss", $google_drive_link, $invoice_no);
                $stmt->execute();

                $message = "<div class='alert alert-info mt-3'>Google Drive link updated successfully!</div>";
            } else {
                $stmt = $conn->prepare("INSERT INTO invoice_documents (invoice_no, file_path) VALUES (?, ?)");
                $stmt->bind_param("ss", $invoice_no, $google_drive_link);
                $stmt->execute();

                $message = "<div class='alert alert-success mt-3'>Google Drive link uploaded successfully!</div>";
            }
        } else {
            $message = "<div class='alert alert-warning mt-3'>Please enter a valid Google Drive URL!</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Invoice PDF - BRTA Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function toggleFields() {
            const type = document.querySelector('input[name="upload_type"]:checked').value;
            document.getElementById('file_upload_group').style.display = type === 'file' ? 'block' : 'none';
            document.getElementById('link_upload_group').style.display = type === 'link' ? 'block' : 'none';
        }
    </script>
</head>
<body class="container mt-5">
    <h2>Upload Invoice PDF</h2>

    <?= $message ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label>Invoice No:</label>
            <input type="text" name="invoice_no" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Upload Type:</label><br>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="upload_type" value="file" checked onchange="toggleFields()">
                <label class="form-check-label">Upload PDF File</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="upload_type" value="link" onchange="toggleFields()">
                <label class="form-check-label">Google Drive Link</label>
            </div>
        </div>

        <div class="mb-3" id="file_upload_group">
            <label>Select PDF File:</label>
            <input type="file" name="pdf_file" accept="application/pdf" class="form-control">
        </div>

        <div class="mb-3" id="link_upload_group" style="display: none;">
            <label>Google Drive PDF Link:</label>
            <input type="url" name="google_drive_link" class="form-control">
        </div>

        <button type="submit" name="upload" class="btn btn-primary">Upload</button>
        <a href="dashboard.php" class="btn btn-outline-dark float-end">
            <i class="bi bi-arrow-left-circle"></i> Back to Dashboard
        </a>
    </form>

    <script>toggleFields();</script>
</body>
</html>
