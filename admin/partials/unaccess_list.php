<?php
// sale_data থেকে যেসব বাইকের access = 0, সেগুলো আনো
$sql = "SELECT * FROM sale_data WHERE access = 0 ORDER BY id DESC";
$result = $conn->query($sql);
?>

<h5 class="mb-3">🚫 Unaccessed Bikes (access = 0)</h5>

<?php if ($result && $result->num_rows > 0): ?>
    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
        <table class="table table-bordered table-sm">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Engine No</th>
                    <th>Chassis No</th>
                    <th>Dealer Code</th>
                    <th>Dealer Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= htmlspecialchars($row['engine_no']) ?></td>
                        <td><?= htmlspecialchars($row['chassis_no']) ?></td>
                        <td><?= htmlspecialchars($row['dealer_code']) ?></td>
                        <td><?= htmlspecialchars($row['dealer_name']) ?></td>
                        <td>
                            <form method="post" action="update_access.php" onsubmit="return confirm('Are you sure to allow access?');">
                                <input type="hidden" name="engine_no" value="<?= htmlspecialchars($row['engine_no']) ?>">
                                <input type="hidden" name="chassis_no" value="<?= htmlspecialchars($row['chassis_no']) ?>">
                                <button type="submit" class="btn btn-sm btn-success">Access On</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="alert alert-success">সব বাইকের Access দেওয়া আছে।</div>
<?php endif; ?>
