<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';

$cart = getCart();
$totals = calculateCartTotals($cart);
$error = $_SESSION['checkout_error'] ?? null;
unset($_SESSION['checkout_error']);

if (empty($totals['items'])) {
    header('Location: /public/cart.php');
    exit;
}

include __DIR__ . '/../includes/header.php';
?>
<h1 class="h3 mb-4">Checkout</h1>
<?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<div class="row">
    <div class="col-md-6">
        <h2 class="h5">Billing Details</h2>
        <form method="post" action="/public/process_checkout.php">
            <div class="mb-3">
                <label for="customer_name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="customer_name" name="customer_name" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Shipping Address</label>
                <textarea class="form-control" id="address" name="address" rows="4" required></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Payment Method</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="payment_method" id="simulated" value="simulated" checked>
                    <label class="form-check-label" for="simulated">Simulated Payment (no real charges)</label>
                </div>
            </div>
            <button type="submit" class="btn btn-success">Place Order</button>
        </form>
    </div>
    <div class="col-md-6">
        <h2 class="h5">Order Summary</h2>
        <ul class="list-group mb-3">
            <?php foreach ($totals['items'] as $item): ?>
                <li class="list-group-item d-flex justify-content-between">
                    <span><?= htmlspecialchars($item['product']['name']) ?> x<?= $item['quantity'] ?></span>
                    <span>$<?= number_format($item['lineTotal'], 2) ?></span>
                </li>
            <?php endforeach; ?>
            <li class="list-group-item d-flex justify-content-between">
                <span>Total</span>
                <strong>$<?= number_format($totals['subtotal'], 2) ?></strong>
            </li>
        </ul>
        <div class="alert alert-secondary">This is a simulated payment. No charges will be made.</div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php';
