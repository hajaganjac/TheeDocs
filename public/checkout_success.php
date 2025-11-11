<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';

$orderId = isset($_GET['order']) ? (int)$_GET['order'] : null;

include __DIR__ . '/../includes/header.php';
?>
<div class="text-center py-5">
    <h1 class="display-6">Thank you for your order!</h1>
    <?php if ($orderId): ?>
        <p class="lead">Your order number is <strong>#<?= $orderId ?></strong>. A confirmation email has been sent.</p>
    <?php else: ?>
        <p class="lead">A confirmation email has been sent to you.</p>
    <?php endif; ?>
    <a href="/public/index.php" class="btn btn-primary mt-4">Continue Shopping</a>
</div>
<?php include __DIR__ . '/../includes/footer.php';
