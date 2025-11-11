<?php
session_start();
require_once __DIR__ . '/../../includes/functions.php';

$config = getConfig();
$error = null;

if (!empty($_SESSION['admin_logged_in'])) {
    header('Location: /public/admin/dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    if (hash_equals($config['admin_password'], $password)) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: /public/admin/dashboard.php');
        exit;
    }
    $error = 'Invalid password.';
}

include __DIR__ . '/../../includes/header.php';
?>
<div class="row justify-content-center">
    <div class="col-md-4">
        <h1 class="h4 mb-3">Admin Login</h1>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>
</div>
<?php include __DIR__ . '/../../includes/footer.php';
