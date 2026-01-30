<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
session_start();

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate($_POST['csrf'] ?? '')) {
        $errors[] = 'Invalid CSRF token.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $confirm  = trim($_POST['confirm'] ?? '');

        if ($username === '' || $password === '' || $confirm === '') {
            $errors[] = 'All fields are required.';
        } elseif ($password !== $confirm) {
            $errors[] = 'Passwords do not match.';
        } else {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $errors[] = 'Username already taken.';
            } else {
                $hash = hash('sha256', $password);
                $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'user')");
                $stmt->execute([$username, $hash]);
                $success = 'Registration successful. You can now log in.';
            }
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<h2>Register</h2>
<?php foreach ($errors as $err): ?><p class="error"><?php echo e($err); ?></p><?php endforeach; ?>
<?php if ($success): ?><p class="success"><?php echo e($success); ?></p><?php endif; ?>

<form method="post">
    <input type="hidden" name="csrf" value="<?php echo e(csrf_token()); ?>">
    <label>Username</label>
    <input type="text" name="username" required>
    <label>Password</label>
    <input type="password" name="password" required>
    <label>Confirm Password</label>
    <input type="password" name="confirm" required>
    <button type="submit">Register</button>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>