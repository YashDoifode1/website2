<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../config.php';

session_start();

// Read token from URL
$token = $_GET['token'] ?? '';
$error = '';
$success = '';

// Validate token exists
if (!$token) {
    die("Invalid password reset link.");
}

// Get DB connection
$pdo = getDbConnection();

// Check token validity
$stmt = $pdo->prepare("SELECT id, reset_expires FROM admin_users WHERE reset_token = ? LIMIT 1");
$stmt->execute([$token]);
$admin = $stmt->fetch();

if (!$admin) {
    die("Invalid reset token.");
}

if (strtotime($admin['reset_expires']) < time()) {
    die("This reset link has expired. Please request a new one.");
}

// If form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    // Validation
    if (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {

        // Hash new password
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // Update admin password
        $update = $pdo->prepare("
            UPDATE admin_users 
            SET password_hash = ?, reset_token = NULL, reset_expires = NULL
            WHERE id = ?
        ");

        $update->execute([$hash, $admin['id']]);

        $success = "Your password has been reset successfully!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <style>
        body { font-family: Arial; background: #f3f3f3; padding:40px; }
        .box { max-width: 400px; margin:auto; background:#fff; padding:25px; border-radius:8px; }
        input { width:100%; padding:10px; margin-bottom:15px; }
        button { background:#333; color:#fff; padding:10px; width:100%; border:none; cursor:pointer; }
        .msg { padding:10px; margin-bottom:15px; border-radius:4px; }
        .error { background:#ffb3b3; }
        .success { background:#b3ffb3; }
    </style>
</head>
<body>

<div class="box">
    <h2>Reset Password</h2>

    <?php if ($error): ?>
        <div class="msg error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="msg success"><?= htmlspecialchars($success) ?></div>
        <a href="login.php"><button>Go to Login</button></a>
    <?php else: ?>

    <form method="POST">
        <label>New Password</label>
        <input type="password" name="password" required>

        <label>Confirm Password</label>
        <input type="password" name="confirm" required>

        <button type="submit">Reset Password</button>
    </form>

    <?php endif; ?>
</div>

</body>
</html>
