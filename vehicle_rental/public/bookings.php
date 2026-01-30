<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';

$stmt = $pdo->query("
    SELECT b.id, b.customer_name, b.start_date, b.end_date, b.status,
           v.type, v.model, v.rent_price
    FROM bookings b
    JOIN vehicles v ON b.vehicle_id = v.id
    ORDER BY b.start_date DESC
");
$bookings = $stmt->fetchAll();
?>

<h2>All Bookings</h2>

<?php if (!empty($bookings)): ?>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Customer</th>
            <th>Vehicle</th>
            <th>Price/day</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($bookings as $b): ?>
        <tr>
            <td><?php echo e($b['id']); ?></td>
            <td><?php echo e($b['customer_name']); ?></td>
            <td><?php echo e($b['type'].' - '.$b['model']); ?></td>
            <td><?php echo e(number_format($b['rent_price'], 2)); ?></td>
            <td><?php echo e($b['start_date']); ?></td>
            <td><?php echo e($b['end_date']); ?></td>
            <td><?php echo e($b['status']); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<p>No bookings found.</p>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>