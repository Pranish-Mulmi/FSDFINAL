<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';

$type = trim($_GET['type'] ?? '');
$min_price = trim($_GET['min_price'] ?? '');
$max_price = trim($_GET['max_price'] ?? '');
$availability = trim($_GET['availability'] ?? '');

$query = "SELECT id, type, model, rent_price, available FROM vehicles WHERE 1=1";
$params = [];

if ($type !== '') {
    $query .= " AND LOWER(type) LIKE LOWER(?)";
    $params[] = "%$type%";
}
if ($min_price !== '' && is_numeric($min_price)) {
    $query .= " AND rent_price >= ?";
    $params[] = $min_price;
}
if ($max_price !== '' && is_numeric($max_price)) {
    $query .= " AND rent_price <= ?";
    $params[] = $max_price;
}
if ($availability !== '' && ($availability === '1' || $availability === '0')) {
    $query .= " AND available = ?";
    $params[] = (int)$availability;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$results = $stmt->fetchAll();
?>

<h2>Search Vehicles</h2>
<form method="get">
    <label>Type</label>
    <input type="text" name="type" value="<?php echo e($type); ?>" placeholder="Car, Bike, Scooter">
    <label>Min Price</label>
    <input type="number" step="0.01" name="min_price" value="<?php echo e($min_price); ?>">
    <label>Max Price</label>
    <input type="number" step="0.01" name="max_price" value="<?php echo e($max_price); ?>">
    <label>Availability</label>
    <select name="availability">
        <option value="">Any</option>
        <option value="1" <?php echo $availability==='1'?'selected':''; ?>>Available</option>
        <option value="0" <?php echo $availability==='0'?'selected':''; ?>>Not Available</option>
    </select>
    <button type="submit">Search</button>
</form>

<?php if (!empty($results)): ?>
<table>
    <thead>
        <tr>
            <th>ID</th><th>Type</th><th>Model</th><th>Price/day</th><th>Available</th><th>Image</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($results as $v): ?>
        <tr>
            <td><?php echo e($v['id']); ?></td>
            <td><?php echo e($v['type']); ?></td>
            <td><?php echo e($v['model']); ?></td>
            <td><?php echo e(number_format($v['rent_price'], 2)); ?></td>
            <td><?php echo $v['available'] ? 'Yes' : 'No'; ?></td>
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
<?php else: ?>
<p>No results found.</p>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>