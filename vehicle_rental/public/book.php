<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST['csrf'] ?? '')) {
        $errors[] = 'Invalid CSRF token.';
    } else {
        $vehicle_id = (int)($_POST['vehicle_id'] ?? 0);
        $customer = trim($_POST['customer'] ?? '');
        $start_date = trim($_POST['start_date'] ?? '');
        $end_date = trim($_POST['end_date'] ?? '');

        if ($vehicle_id <= 0 || $customer === '' || $start_date === '' || $end_date === '') {
            $errors[] = 'All fields are required.';
        } elseif (!valid_date_range($start_date, $end_date)) {
            $errors[] = 'Invalid date range.';
        } else {
            $q = "SELECT COUNT(*) FROM bookings WHERE vehicle_id=? AND start_date <= ? AND end_date >= ?";
            $st = $pdo->prepare($q);
            $st->execute([$vehicle_id, $end_date, $start_date]);
            $conflict = (int)$st->fetchColumn();

            if ($conflict > 0) {
                $errors[] = 'Vehicle not available for selected dates.';
            } else {
                $ins = $pdo->prepare("INSERT INTO bookings (vehicle_id, customer_name, start_date, end_date) VALUES (?, ?, ?, ?)");
                $ins->execute([$vehicle_id, $customer, $start_date, $end_date]);
                $success = 'Booking confirmed.';
            }
        }
    }
}

$vehicles = $pdo->query("SELECT id, type, model, rent_price FROM vehicles ORDER BY type, model")->fetchAll();
?>

<h2>Book a Vehicle</h2>
<?php foreach ($errors as $err): ?><p class="error"><?php echo e($err); ?></p><?php endforeach; ?>
<?php if ($success): ?><p class="success"><?php echo e($success); ?></p><?php endif; ?>

<form method="post" id="bookingForm">
    <input type="hidden" name="csrf" value="<?php echo e(csrf_token()); ?>">
    <label>Vehicle</label>
    <select name="vehicle_id" id="vehicle_id" required>
        <option value="">Select vehicle</option>
        <?php foreach ($vehicles as $v): ?>
        <option value="<?php echo e($v['id']); ?>">
            <?php echo e($v['type'].' - '.$v['model'].' (Rs '.number_format($v['rent_price'],2).'/day)'); ?>
        </option>
        <?php endforeach; ?>
    </select>

    <label>Customer Name</label>
    <input type="text" name="customer" required>

    <label>Start Date</label>
    <input type="date" name="start_date" id="start_date" required>

    <label>End Date</label>
    <input type="date" name="end_date" id="end_date" required>

    <p id="availabilityMsg" class="info"></p>

    <button type="submit">Book</button>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>