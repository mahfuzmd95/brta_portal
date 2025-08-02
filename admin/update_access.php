<?php
include '../db/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $engine_no = $_POST['engine_no'] ?? '';
    $chassis_no = $_POST['chassis_no'] ?? '';

    if (!empty($engine_no) && !empty($chassis_no)) {
        $stmt = $conn->prepare("UPDATE sale_data SET access = 1 WHERE engine_no = ? AND chassis_no = ?");
        $stmt->bind_param("ss", $engine_no, $chassis_no);
        if ($stmt->execute()) {
            header("Location: manage_access.php?tab=unaccess&success=1");
            exit();
        } else {
            echo "Update failed!";
        }
    } else {
        echo "Invalid request.";
    }
} else {
    echo "Invalid access.";
}
?>
