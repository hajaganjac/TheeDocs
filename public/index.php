<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';

$products = fetchAllProducts();
$config = getConfig();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['quantity'])) {
    $productId = (int)$_POST['product_id'];
    $quantity = max(1, (int)$_POST['quantity']);
    addToCart($productId, $quantity);
    header('Location: /public/cart.php');
    exit;
}

include __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Latest AI Products</h1>
    <a href="/public/cart.php" class="btn btn-primary">View Cart</a>
</div>
<?php if (empty($products)): ?>
    <div class="alert alert-info">No products available yet. Check back soon!</div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($products as $product): ?>
            <div class="col-md-4">
                <div class="card h-100">
                    <?php if (!empty($product['image_path']) && file_exists(__DIR__ . '/../' . $product['image_path'])): ?>
                        <img src="/<?= htmlspecialchars($product['image_path']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
                    <?php else: ?>
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height:200px;">
                            <span class="text-muted">No image</span>
                        </div>
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                        <p class="card-text flex-grow-1"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold">$<?= number_format($product['price'], 2) ?></span>
                            <form method="post" class="d-flex align-items-center">
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                <input type="number" min="1" name="quantity" value="1" class="form-control me-2" style="width:80px;">
                                <button type="submit" class="btn btn-success">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php include __DIR__ . '/../includes/footer.php';
