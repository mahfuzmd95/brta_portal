<?php

$servername = "sql109.infinityfree.com";
$username = "if0_39099796";
$password = "Ma429444";  // এখানে আপনার vPanel password বসান
$database = "if0_39099796_brta_portal";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
