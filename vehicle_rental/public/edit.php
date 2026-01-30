<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM vehicles WHERE id = ?");
$stmt->execute([$id]);
$vehicle = $stmt->fetch();

if (!$vehicle) {
    http_response_code(404);
    echo "Vehicle not found.";
    exit;
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST['csrf'] ?? '')) {
        $errors[] = 'Invalid CSRF token.';
    } else {
        $type = trim($_POST['type'] ?? '');
        $model = trim($_POST['model'] ?? '');
        $rent_price = $_POST['rent_price'] ?? '';
        $available = isset($_POST['available']) ? 1 : 0;

        if ($type === '' || $model === '' || $rent_price === '') {
            $errors[] = 'All fields are required.';
        } elseif (!is_numeric($rent_price) || $rent_price <= 0) {
            $errors[] = 'Rent price must be a positive number.';
        }

        if (!$errors) {
            $stmt = $pdo->prepare("UPDATE vehicles SET type=?, model=?, rent_price=?, available=? WHERE id=?");
            $stmt->execute([$type, $model, $rent_price, $available, $id]);
            $success = 'Vehicle updated successfully.';

            // Refresh data
            $stmt = $pdo->prepare("SELECT * FROM vehicles WHERE id = ?");
            $stmt->execute([$id]);
            $vehicle = $stmt->fetch();
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<h2>Edit Vehicle #<?php echo e($vehicle['id']); ?></h2>
<?php foreach ($errors as $err): ?><p class="error"><?php echo e($err); ?></p><?php endforeach; ?>
<?php if ($success): ?><p class="success"><?php echo e($success); ?></p><?php endif; ?>

<form method="post">
    <input type="hidden" name="csrf" value="<?php echo e(csrf_token()); ?>">
    <label>Type</label>
    <input type="text" name="type" value="<?php echo e($vehicle['type']); ?>" required>
    <label>Model</label>
    <input type="text" name="model" value="<?php echo e($vehicle['model']); ?>" required>
    <label>Rent Price (per day)</label>
    <input type="number" name="rent_price" step="0.01" min="0" value="<?php echo e($vehicle['rent_price']); ?>" required>
    <label><input type="checkbox" name="available" <?php echo $vehicle['available'] ? 'checked' : ''; ?>> Available</label>
    <button type="submit">Save</button>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>