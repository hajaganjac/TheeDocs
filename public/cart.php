<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';

if (isset($_GET['remove'])) {
    removeFromCart((int)$_GET['remove']);
    header('Location: /public/cart.php');
    exit;
}

$cart = getCart();
$totals = calculateCartTotals($cart);

include __DIR__ . '/../includes/header.php';
?>
<h1 class="h3 mb-4">Your Cart</h1>
<?php if (empty($totals['items'])): ?>
    <div class="alert alert-info">Your cart is empty. <a href="/public/index.php">Continue shopping</a>.</div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($totals['items'] as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['product']['name']) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td>$<?= number_format($item['product']['price'], 2) ?></td>
                        <td>$<?= number_format($item['lineTotal'], 2) ?></td>
                        <td><a href="?remove=<?= $item['product']['id'] ?>" class="btn btn-sm btn-outline-danger">Remove</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-between align-items-center mt-4">
        <span class="fw-bold">Subtotal: $<?= number_format($totals['subtotal'], 2) ?></span>
        <div>
            <a href="/public/index.php" class="btn btn-outline-secondary">Continue Shopping</a>
            <a href="/public/checkout.php" class="btn btn-primary ms-2">Proceed to Checkout</a>
        </div>
    </div>
<?php endif; ?>
<?php include __DIR__ . '/../includes/footer.php';
