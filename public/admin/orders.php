<?php
session_start();
require_once __DIR__ . '/../../includes/functions.php';
ensureLoggedIn();

$db = getDb();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $orderId = (int)$_POST['order_id'];
    $stmt = $db->prepare('UPDATE orders SET status = :status WHERE id = :id');
    $stmt->bindValue(':status', 'shipped', SQLITE3_TEXT);
    $stmt->bindValue(':id', $orderId, SQLITE3_INTEGER);
    $stmt->execute();
    header('Location: /public/admin/orders.php');
    exit;
}

$result = $db->query('SELECT * FROM orders ORDER BY created_at DESC');
$orders = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $orders[] = $row;
}

include __DIR__ . '/../../includes/header.php';
?>
<h1 class="h3 mb-4">Orders</h1>
<?php if (empty($orders)): ?>
    <div class="alert alert-info">No orders yet.</div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Email</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Placed</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td>#<?= $order['id'] ?></td>
                    <td><?= htmlspecialchars($order['customer_name']) ?></td>
                    <td><?= htmlspecialchars($order['email']) ?></td>
                    <td>$<?= number_format($order['total'], 2) ?></td>
                    <td>
                        <span class="badge bg-<?= $order['status'] === 'shipped' ? 'success' : 'secondary' ?>">
                            <?= ucfirst($order['status']) ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($order['created_at']) ?></td>
                    <td class="d-flex gap-2">
                        <?php if ($order['status'] !== 'shipped'): ?>
                            <form method="post" class="d-inline">
                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-success">Mark Shipped</button>
                            </form>
                        <?php endif; ?>
                        <a href="/public/admin/shipping_label.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-outline-primary">PDF Label</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php include __DIR__ . '/../../includes/footer.php';
