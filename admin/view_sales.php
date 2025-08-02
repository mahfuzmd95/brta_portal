<?php
session_start();

// Allow only admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../db/config.php';

$searchKey = '';
if (isset($_GET['search'])) {
    $searchKey = $conn->real_escape_string($_GET['search']);
}

$sql = "SELECT * FROM sales_data_old WHERE 
        invoice_no LIKE '%$searchKey%' OR 
        engine_no LIKE '%$searchKey%' OR 
        chassis_no LIKE '%$searchKey%' OR 
        dealer_name LIKE '%$searchKey%' 
        ORDER BY id DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Sales Data - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2 class="mb-4">View Sales Data</h2>

    <form method="GET" class="row mb-3">
        <div class="col-md-8">
            <input type="text" name="search" class="form-control" placeholder="Search by Invoice No, Engine No, Chassis No, Dealer Name" value="<?= htmlspecialchars($searchKey) ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Search</button>
        </div>
        <div class="col-md-2">
            <a href="view_sales.php" class="btn btn-secondary w-100">Reset</a>
        </div>
    </form>

    <?php if ($result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Invoice No</th>
                        <th>Item Code</th>
                        <th>Item Details</th>
                        <th>Engine No</th>
                        <th>Chassis No</th>
                        <th>Dealer Code</th>
                        <th>Dealer Name</th>
                        <th>Access</th>
                        <th>Remarks</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['invoice_no']) ?></td>
                            <td><?= htmlspecialchars($row['item_code']) ?></td>
                            <td><?= htmlspecialchars($row['item_details']) ?></td>
                            <td><?= htmlspecialchars($row['engine_no']) ?></td>
                            <td><?= htmlspecialchars($row['chassis_no']) ?></td>
                            <td><?= htmlspecialchars($row['dealer_code']) ?></td>
                            <td><?= htmlspecialchars($row['dealer_name']) ?></td>
                            <td><?= htmlspecialchars($row['access']) ?></td>
                            <td><?= htmlspecialchars($row['remarks']) ?></td>
                            <td>
                                <a href="edit_sale.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No records found.</div>
    <?php endif; ?>
</body>
</html>
