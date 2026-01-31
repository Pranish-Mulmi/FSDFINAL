<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

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
        $imagePath = null;

        if ($type === '' || $model === '' || $rent_price === '') {
            $errors[] = 'All fields are required.';
        } elseif (!is_numeric($rent_price) || $rent_price <= 0) {
            $errors[] = 'Rent price must be a positive number.';
        }

        if (!empty($_FILES['image']['name'])) {
            $targetDir = __DIR__ . '/../uploads/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            $filename = time() . '_' . basename($_FILES['image']['name']);
            $targetFile = $targetDir . $filename;

            $allowedTypes = ['image/png', 'image/jpeg'];
            if (in_array($_FILES['image']['type'], $allowedTypes)) {
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                    $imagePath = '/uploads/' . $filename;
                } else {
                    $errors[] = 'Failed to upload image.';
                }
            } else {
                $errors[] = 'Only PNG and JPG images are allowed.';
            }
        }

        if (!$errors) {
            $stmt = $pdo->prepare("INSERT INTO vehicles (type, model, rent_price, available, image) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$type, $model, $rent_price, $available, $imagePath]);
            $success = 'Vehicle added successfully.';
        }
    }
}
require_once __DIR__ . '/../includes/header.php';
?>

<h2>Add vehicle</h2>
<?php foreach ($errors as $err): ?><p class="error"><?php echo e($err); ?></p><?php endforeach; ?>
<?php if ($success): ?><p class="success"><?php echo e($success); ?></p><?php endif; ?>

<form method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?php echo e(csrf_token()); ?>">
    <label>Type</label>
    <input type="text" name="type" required>
    <label>Model</label>
    <input type="text" name="model" required>
    <label>Rent price (per day)</label>
    <input type="number" name="rent_price" step="0.01" min="0" required>
    <label><input type="checkbox" name="available" checked> Available</label>
    <label>Vehicle Image</label>
    <input type="file" name="image">
    <button type="submit">Add</button>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>