<?php
session_start();

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../db/config.php';

$sql = "
SELECT 
    i.invoice_no, 
    i.item_code,
    i.item_details,
    i.engine_no,
    i.chassis_no,
    s.dealer_code,
    s.dealer_name,
    s.access,  -- added access column here
    d.file_path
FROM initial_data i
LEFT JOIN sale_data s ON i.engine_no = s.engine_no OR i.chassis_no = s.chassis_no
LEFT JOIN invoice_documents d ON i.invoice_no = d.invoice_no
ORDER BY i.invoice_no DESC
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Data View - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="container mt-3">
    <h2 class="mb-3">All Data View</h2>
    
    <a href="dashboard.php" class="btn btn-outline-dark mb-2">
        <i class="bi bi-arrow-left-circle"></i> Back to Dashboard
    </a>

    <table id="mergedTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Invoice No</th>
                <th>Item Code</th>
                <th>Item Details</th>
                <th>Engine No</th>
                <th>Chassis No</th>
                <th>Dealer Code</th>
                <th>Dealer Name</th>
                <th>Access</th> <!-- new column -->
                <th>PDF</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['invoice_no']) ?></td>
                <td><?= htmlspecialchars($row['item_code']) ?></td>
                <td><?= htmlspecialchars($row['item_details']) ?></td>
                <td><?= htmlspecialchars($row['engine_no']) ?></td>
                <td><?= htmlspecialchars($row['chassis_no']) ?></td>
                <td><?= htmlspecialchars($row['dealer_code']) ?></td>
                <td><?= htmlspecialchars($row['dealer_name']) ?></td>
                <td>
                    <?php 
                    // Access: show YES (green) or NO (red)
                    if (isset($row['access']) && $row['access'] == 1) {
                        echo '<span class="badge bg-success">YES</span>';
                    } else {
                        echo '<span class="badge bg-danger">NO</span>';
                    }
                    ?>
                </td>
                <td>
                    <?php
                    $filePath = $row['file_path'];
                    if (!empty($filePath)) {
                        if (str_contains($filePath, 'drive.google.com')) {
                            echo '<a href="' . htmlspecialchars($filePath) . '" target="_blank" class="btn btn-primary btn-sm">
                                    <i class="bi bi-cloud-download"></i> Drive Link
                                  </a>';
                        } elseif (file_exists("../" . $filePath)) {
                            echo '<a href="../' . htmlspecialchars($filePath) . '" target="_blank" class="btn btn-success btn-sm">
                                    <i class="bi bi-file-earmark-arrow-down"></i> Download
                                  </a>';
                        } else {
                            echo '<span class="text-warning">File missing</span>';
                        }
                    } else {
                        echo '<span class="text-danger">Not Found</span>';
                    }
                    ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Required JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#mergedTable').DataTable({
                pageLength: 20,
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                responsive: true
            });
        });
    </script>
</body>
</html>
