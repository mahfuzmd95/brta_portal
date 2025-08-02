<?php
$sql = "
    SELECT i.engine_no, i.chassis_no, s.dealer_code, s.dealer_name
    FROM initial_data i
    LEFT JOIN sale_data s 
      ON i.engine_no = s.engine_no AND i.chassis_no = s.chassis_no
    WHERE (s.dealer_code IS NULL OR s.dealer_code = '')
       OR (s.dealer_name IS NULL OR s.dealer_name = '')
    ORDER BY i.id DESC
";

$result = $conn->query($sql);
?>

<h5 class="mb-3">🧾 Bikes With No Assigned Dealer</h5>

<?php if ($result && $result->num_rows > 0): ?>
    <div class="table-responsive">
        <table class="table table-bordered table-sm">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Engine No</th>
                    <th>Chassis No</th>
                    <th>Dealer Code</th>
                    <th>Dealer Name</th>
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
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="alert alert-success">সকল বাইকে ডিলার অ্যাসাইন করা আছে।</div>
<?php endif; ?>
