<?php

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}


include_once("../db/config.php");

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["search"])) {
    $search_value = $_POST["search_value"];
    $query = "SELECT * FROM sales_data WHERE engine_no = ? OR chassis_no = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $search_value, $search_value);
    $stmt->execute();
    $result = $stmt->get_result();
    $sale = $result->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update"])) {
    $id = $_POST["id"];
    $dealer_code = $_POST["dealer_code"];
    $dealer_name = $_POST["dealer_name"];
    $access = $_POST["access"];
    $remarks = $_POST["remarks"];

    $update = "UPDATE sales_data SET dealer_code = ?, dealer_name = ?, access = ?, remarks = ? WHERE id = ?";
    $stmt = $conn->prepare($update);
    $stmt->bind_param("ssssi", $dealer_code, $dealer_name, $access, $remarks, $id);
    if ($stmt->execute()) {
        $message = "✅ Updated Successfully!";
    } else {
        $message = "❌ Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit Sale</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">
  <h3 class="mb-4">Edit Sale Data</h3>
  
  <?php if ($message): ?>
    <div class="alert alert-info"><?= $message ?></div>
  <?php endif; ?>

  <form method="post" class="mb-4">
    <div class="input-group">
      <input type="text" name="search_value" class="form-control" placeholder="Enter Engine No or Chassis No" required>
      <button type="submit" name="search" class="btn btn-primary">Search</button>
    </div>
  </form>

  <?php if (!empty($sale)): ?>
    <form method="post">
      <input type="hidden" name="id" value="<?= $sale['id'] ?>">
      <div class="mb-3">
        <label>Engine No:</label>
        <input type="text" class="form-control" value="<?= $sale['engine_no'] ?>" disabled>
      </div>
      <div class="mb-3">
        <label>Chassis No:</label>
        <input type="text" class="form-control" value="<?= $sale['chassis_no'] ?>" disabled>
      </div>
      <div class="mb-3">
        <label>Dealer Code:</label>
        <input type="text" name="dealer_code" class="form-control" value="<?= $sale['dealer_code'] ?>">
      </div>
      <div class="mb-3">
        <label>Dealer Name:</label>
        <input type="text" name="dealer_name" class="form-control" value="<?= $sale['dealer_name'] ?>">
      </div>
      <div class="mb-3">
        <label>Access:</label>
        <select name="access" class="form-control">
          <option value="">Select</option>
          <option value="Yes" <?= ($sale['access'] == "Yes") ? "selected" : "" ?>>Yes</option>
          <option value="No" <?= ($sale['access'] == "No") ? "selected" : "" ?>>No</option>
        </select>
      </div>
      <div class="mb-3">
        <label>Remarks:</label>
        <textarea name="remarks" class="form-control"><?= $sale['remarks'] ?></textarea>
      </div>
      <button type="submit" name="update" class="btn btn-success">Update Sale Info</button>
    </form>
  <?php endif; ?>
</body>
</html>
