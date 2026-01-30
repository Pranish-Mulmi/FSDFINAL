<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';

$stmt = $pdo->query("SELECT * FROM vehicles");
$vehicles = $stmt->fetchAll();
?>

<h2>Admin Dashboard - All Vehicles</h2>
<?php if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
    <a href="add.php">Add Vehicle</a>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th>ID</th><th>Type</th><th>Model</th><th>Price/day</th><th>Available</th><th>Actions</th><th>Image</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($vehicles as $v): ?>
        <tr>
            <td><?php echo e($v['id']); ?></td>
            <td><?php echo e($v['type']); ?></td>
            <td><?php echo e($v['model']); ?></td>
            <td><?php echo e($v['rent_price']); ?></td>
            <td><?php echo $v['available'] ? 'Yes' : 'No'; ?></td>
            <td>
                <?php if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a href="edit.php?id=<?php echo $v['id']; ?>" class="btn btn-edit">Edit</a>
                    <a href="delete.php?id=<?php echo $v['id']; ?>" class="btn btn-delete" onclick="return confirm('Delete this vehicle?')">Delete</a>
                <?php endif; ?>
            </td>
            <td>
                <?php if (!empty($v['image'])): ?>
                    <img src="<?php echo '/vehicle_rental' . e($v['image']); ?>" alt="Vehicle Image" width="120">
                <?php else: ?>
                    No image
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>