<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username && $password) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        // Password stored as SHA-256 hash
        if ($user && hash('sha256', $password) === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['username'] = $user['username'];
            header("Location: index.php");
            exit;
        } else {
            $error = "Invalid credentials.";
        }
    } else {
        $error = "Please enter username and password.";
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<h2>Login</h2>
<?php if ($error): ?><p class="error"><?php echo e($error); ?></p><?php endif; ?>

<form method="post">
    <input type="hidden" name="csrf" value="<?php echo e(csrf_token()); ?>">
    <label>Username</label>
    <input type="text" name="username" required>
    <label>Password</label>
    <input type="password" name="password" required>
    <button type="submit">Login</button>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>