<?php
session_start();
require_once __DIR__ . '/../../includes/functions.php';
ensureLoggedIn();

$db = getDb();
$config = getConfig();
$message = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $imagePath = null;

    if ($name === '' || $price <= 0) {
        $error = 'Name and price are required.';
    } else {
        if (!empty($_FILES['image']['name'])) {
            $uploadDir = rtrim($config['upload_dir'], '/') . '/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $filename = uniqid('product_', true) . '.' . $extension;
            $destination = $uploadDir . $filename;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                $imagePath = 'uploads/' . $filename;
            } else {
                $error = 'Failed to upload image.';
            }
        }

        if (!$error) {
            $stmt = $db->prepare('INSERT INTO products (name, description, price, image_path) VALUES (:name, :description, :price, :image_path)');
            $stmt->bindValue(':name', $name, SQLITE3_TEXT);
            $stmt->bindValue(':description', $description, SQLITE3_TEXT);
            $stmt->bindValue(':price', $price, SQLITE3_FLOAT);
            $stmt->bindValue(':image_path', $imagePath, SQLITE3_TEXT);
            $stmt->execute();
            $message = 'Product added successfully.';
        }
    }
}

$products = fetchAllProducts();

include __DIR__ . '/../../includes/header.php';
?>
<h1 class="h3 mb-4">Manage Products</h1>
<?php if ($message): ?>
    <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<div class="row">
    <div class="col-md-5">
        <h2 class="h5">Add New Product</h2>
        <form method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="name" class="form-label">Product Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="4"></textarea>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price (USD)</label>
                <input type="number" class="form-control" id="price" name="price" min="0" step="0.01" required>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Product Image</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                <div class="form-text">Supported formats: jpg, png, gif. Max size depends on server settings.</div>
            </div>
            <button type="submit" class="btn btn-primary">Add Product</button>
        </form>
    </div>
    <div class="col-md-7">
        <h2 class="h5">Current Products</h2>
        <?php if (empty($products)): ?>
            <div class="alert alert-info">No products found.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Added</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td style="width:100px;">
                                    <?php if (!empty($product['image_path']) && file_exists(__DIR__ . '/../../' . $product['image_path'])): ?>
                                        <img src="/<?= htmlspecialchars($product['image_path']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="img-fluid rounded">
                                    <?php else: ?>
                                        <span class="text-muted">No image</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($product['name']) ?></strong><br>
                                    <small class="text-muted">ID: <?= $product['id'] ?></small>
                                </td>
                                <td>$<?= number_format($product['price'], 2) ?></td>
                                <td><?= htmlspecialchars($product['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php include __DIR__ . '/../../includes/footer.php';
