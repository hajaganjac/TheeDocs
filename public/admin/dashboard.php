<?php
session_start();
require_once __DIR__ . '/../../includes/functions.php';
ensureLoggedIn();

include __DIR__ . '/../../includes/header.php';
?>
<h1 class="h3 mb-4">Admin Dashboard</h1>
<div class="row g-3">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body d-flex flex-column">
                <h5 class="card-title">Orders</h5>
                <p class="card-text flex-grow-1">Manage customer orders, mark shipments, and download labels.</p>
                <a href="/public/admin/orders.php" class="btn btn-primary mt-auto">View Orders</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body d-flex flex-column">
                <h5 class="card-title">Products</h5>
                <p class="card-text flex-grow-1">Add, update, and upload images for your AI products.</p>
                <a href="/public/admin/products.php" class="btn btn-primary mt-auto">Manage Products</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body d-flex flex-column">
                <h5 class="card-title">Logout</h5>
                <p class="card-text flex-grow-1">Securely end your admin session.</p>
                <a href="/public/admin/logout.php" class="btn btn-outline-danger mt-auto">Logout</a>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../../includes/footer.php';
