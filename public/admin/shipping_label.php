<?php
session_start();
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../lib/pdf.php';
ensureLoggedIn();

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo 'Missing order id';
    exit;
}

$orderId = (int)$_GET['id'];
$db = getDb();
$stmt = $db->prepare('SELECT * FROM orders WHERE id = :id');
$stmt->bindValue(':id', $orderId, SQLITE3_INTEGER);
$result = $stmt->execute();
$order = $result->fetchArray(SQLITE3_ASSOC);

if (!$order) {
    http_response_code(404);
    echo 'Order not found';
    exit;
}

$itemStmt = $db->prepare('SELECT p.name, oi.quantity, oi.price FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = :order_id');
$itemStmt->bindValue(':order_id', $orderId, SQLITE3_INTEGER);
$itemResult = $itemStmt->execute();
$items = [];
$total = 0.0;
while ($row = $itemResult->fetchArray(SQLITE3_ASSOC)) {
    $lineTotal = $row['price'] * $row['quantity'];
    $items[] = [
        'product' => ['name' => $row['name']],
        'quantity' => $row['quantity'],
        'lineTotal' => $lineTotal,
    ];
    $total += $lineTotal;
}
$order['total'] = $total;

$config = getConfig();
$pdf = generateShippingLabelPdf($order, $items, $config['store_name']);

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="shipping-label-' . $orderId . '.pdf"');
header('Content-Length: ' . strlen($pdf));

echo $pdf;
exit;
