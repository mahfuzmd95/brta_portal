<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['engine_or_chassis'])) {
    $value = $conn->real_escape_string(trim($_POST['engine_or_chassis']));

    // বাইক খুঁজে বের করো
    $sql = "SELECT * FROM sale_data WHERE engine_no = '$value' OR chassis_no = '$value' LIMIT 1";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $currentAccess = $row['access'];
        $newAccess = $currentAccess == 1 ? 0 : 1;

        // Access Toggle করো
        $updateSql = "UPDATE sale_data SET access = $newAccess WHERE id = {$row['id']}";
        if ($conn->query($updateSql)) {
            $accessText = $newAccess ? "✅ <strong>ON</strong>" : "❌ <strong>OFF</strong>";
            $message = "
                <div class='alert alert-success'>
                    <strong>Access Updated Successfully!</strong><br>
                    🔧 Access: $accessText<br>
                    🧾 Engine No: <strong>{$row['engine_no']}</strong><br>
                    🛠️ Chassis No: <strong>{$row['chassis_no']}</strong><br>
                    🧍 Dealer: <strong>{$row['dealer_name']}</strong> ({$row['dealer_code']})
                </div>
            ";
        } else {
            $message = "<div class='alert alert-danger'>❌ Error updating access!</div>";
        }
    } else {
        $message = "<div class='alert alert-warning'>🚫 No matching engine/chassis found in <code>sale_data</code>.</div>";
    }
}
?>

<form method="POST" class="mb-3">
    <label for="engine_or_chassis" class="form-label">Enter Engine No or Chassis No:</label>
    <input type="text" name="engine_or_chassis" id="engine_or_chassis" class="form-control" required>
    <button type="submit" class="btn btn-primary mt-2">Toggle Access</button>
</form>

<?php if (isset($message)) echo $message; ?>
