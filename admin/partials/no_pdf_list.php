<?php
// initial_data টেবিল থেকে এমন invoice_no আনো, যেগুলোর জন্য invoice_documents-এ কোনো PDF নাই
$sql = "
    SELECT DISTINCT i.invoice_no
    FROM initial_data i
    LEFT JOIN invoice_documents d ON i.invoice_no = d.invoice_no
    WHERE d.invoice_no IS NULL
    ORDER BY i.invoice_no DESC
";

$result = $conn->query($sql);
?>

<h5 class="mb-3">📄 Invoices Without PDF</h5>

<?php if ($result && $result->num_rows > 0): ?>
    <ul class="list-group">
        <?php while ($row = $result->fetch_assoc()): ?>
            <li class="list-group-item"><?= htmlspecialchars($row['invoice_no']) ?></li>
        <?php endwhile; ?>
    </ul>
<?php else: ?>
    <div class="alert alert-success">সব ইনভয়েসের জন্য PDF আপলোড করা আছে।</div>
<?php endif; ?>
