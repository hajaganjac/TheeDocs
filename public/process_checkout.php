<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';

$cart = getCart();
$totals = calculateCartTotals($cart);

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($totals['items'])) {
    header('Location: /public/cart.php');
    exit;
}

$name = trim($_POST['customer_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$address = trim($_POST['address'] ?? '');

if ($name === '' || $email === '' || $address === '') {
    $_SESSION['checkout_error'] = 'Please fill out all required fields.';
    header('Location: /public/checkout.php');
    exit;
}

$db = getDb();
$db->exec('BEGIN');
try {
    $stmt = $db->prepare('INSERT INTO orders (customer_name, email, address, total) VALUES (:name, :email, :address, :total)');
    $stmt->bindValue(':name', $name, SQLITE3_TEXT);
    $stmt->bindValue(':email', $email, SQLITE3_TEXT);
    $stmt->bindValue(':address', $address, SQLITE3_TEXT);
    $stmt->bindValue(':total', $totals['subtotal'], SQLITE3_FLOAT);
    $stmt->execute();

    $orderId = $db->lastInsertRowID();

    $itemStmt = $db->prepare('INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (:order_id, :product_id, :quantity, :price)');
    foreach ($totals['items'] as $item) {
        $itemStmt->bindValue(':order_id', $orderId, SQLITE3_INTEGER);
        $itemStmt->bindValue(':product_id', $item['product']['id'], SQLITE3_INTEGER);
        $itemStmt->bindValue(':quantity', $item['quantity'], SQLITE3_INTEGER);
        $itemStmt->bindValue(':price', $item['product']['price'], SQLITE3_FLOAT);
        $itemStmt->execute();
    }

    $db->exec('COMMIT');

    $order = [
        'id' => $orderId,
        'customer_name' => $name,
        'email' => $email,
        'address' => $address,
        'total' => $totals['subtotal'],
    ];

    sendOrderEmails($order, $totals['items']);

    clearCart();
    header('Location: /public/checkout_success.php?order=' . $orderId);
    exit;
} catch (Exception $e) {
    $db->exec('ROLLBACK');
    $_SESSION['checkout_error'] = 'Something went wrong while placing your order. Please try again.';
    header('Location: /public/checkout.php');
    exit;
}
