<?php
ini_set('session.cookie_lifetime', 0);
 
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'dealer') {
    header("Location: login.php");
    exit();
}

require_once 'db/config.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dealer Dashboard - BRTA Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2 class="mb-4">Welcome Dealer: <?= htmlspecialchars($_SESSION['user_name']) ?></h2>
    <a href="dealer_dashboard.php" class="btn btn-info mt-3">Go to Dashboard</a>
    <a href="logout.php" class="btn btn-danger mt-3 float-end">Logout</a>
    <hr>

    <form method="POST">
        <div class="mb-3">
            <input type="text" name="search_key" class="form-control" placeholder="Enter Engine No or Chassis No" required>
        </div>
        <button type="submit" name="search" class="btn btn-primary">Search</button>
    </form>

    <hr>

    <?php
    if (isset($_POST['search'])) {
        $search = $conn->real_escape_string($_POST['search_key']);
        $dealer_code = $_SESSION['user_id'];

        // Step 1: খুঁজে বের করো বাইকটি এই ডিলারের কিনা এবং access = 1 কি না
        $saleSql = "SELECT * FROM sale_data WHERE (engine_no = '$search' OR chassis_no = '$search') AND dealer_code = '$dealer_code' AND access = 1 LIMIT 1";
        $saleResult = $conn->query($saleSql);

        if ($saleResult->num_rows > 0) {
            $saleRow = $saleResult->fetch_assoc();
            $engineNo = $saleRow['engine_no'];
            $chassisNo = $saleRow['chassis_no'];

            // Step 2: initial_data থেকে ইনভয়েস ও item খুঁজে বের করো
            $initSql = "SELECT * FROM initial_data WHERE engine_no = '$engineNo' OR chassis_no = '$chassisNo' LIMIT 1";
            $initResult = $conn->query($initSql);

            if ($initResult->num_rows > 0) {
                $initRow = $initResult->fetch_assoc();
                $invoice = $initRow['invoice_no'];
                $item = $initRow['item_details'];

                echo "<p><strong>Engine No:</strong> {$engineNo}<br>";
                echo "<strong>Chassis No:</strong> {$chassisNo}<br>";
                echo "<strong>Item:</strong> {$item}</p>";

                // Step 3: invoice_documents থেকে PDF path বের করো
                $pdfSql = "SELECT * FROM invoice_documents WHERE invoice_no = '$invoice' LIMIT 1";
                $pdfResult = $conn->query($pdfSql);

                if ($pdfResult->num_rows > 0) {
                    $pdf = $pdfResult->fetch_assoc();
                    $pdfPath = $pdf['file_path'];

                    if (!empty($pdfPath)) {
                        if (str_contains($pdfPath, 'drive.google.com')) {
                            // যদি গুগল ড্রাইভ লিঙ্ক হয়
                            echo "<a href='" . htmlspecialchars($pdfPath) . "' class='btn btn-primary' target='_blank'>View PDF (Google Drive)</a>";
                        } elseif (file_exists(__DIR__ . '/' . $pdfPath)) {
                            echo "<a href='{$pdfPath}' class='btn btn-success' target='_blank'>Download PDF</a>";
                        } else {
                            echo "<div class='alert alert-warning'>ডাটাবেজে PDF ফাইল আছে কিন্তু সার্ভারে ফাইলটি নেই।</div>";
                        }
                    } else {
                        echo "<div class='alert alert-warning'>এই ইনভয়েসের জন্য কোন PDF আপলোড করা হয়নি।</div>";
                    }
                } else {
                    echo "<div class='alert alert-warning'>এই ইনভয়েসের জন্য কোন PDF আপলোড করা হয়নি।</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>এই Engine/Chassis এর জন্য ইনিশিয়াল ডেটাতে কোন ইনভয়েস পাওয়া যায়নি।</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>আপনার সেলস ডেটাতে অনুমোদিত কোন রেকর্ড পাওয়া যায়নি!</div>";
        }
    }
    ?>
</body>
</html>
