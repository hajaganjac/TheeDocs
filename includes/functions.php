<?php
require_once __DIR__ . '/db.php';

function getConfig(): array
{
    static $config;
    if ($config === null) {
        $config = require __DIR__ . '/../config.php';
    }
    return $config;
}

function fetchAllProducts(): array
{
    $db = getDb();
    $result = $db->query('SELECT * FROM products ORDER BY created_at DESC');
    $products = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $products[] = $row;
    }
    return $products;
}

function fetchProduct(int $id): ?array
{
    $db = getDb();
    $stmt = $db->prepare('SELECT * FROM products WHERE id = :id');
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $result = $stmt->execute();
    $product = $result->fetchArray(SQLITE3_ASSOC);
    return $product ?: null;
}

function storeCart(array $cart): void
{
    $_SESSION['cart'] = $cart;
}

function getCart(): array
{
    return $_SESSION['cart'] ?? [];
}

function addToCart(int $productId, int $quantity): void
{
    $cart = getCart();
    if (isset($cart[$productId])) {
        $cart[$productId] += $quantity;
    } else {
        $cart[$productId] = $quantity;
    }
    storeCart($cart);
}

function removeFromCart(int $productId): void
{
    $cart = getCart();
    unset($cart[$productId]);
    storeCart($cart);
}

function clearCart(): void
{
    unset($_SESSION['cart']);
}

function calculateCartTotals(array $cart): array
{
    $subtotal = 0.0;
    $items = [];
    foreach ($cart as $productId => $quantity) {
        $product = fetchProduct((int)$productId);
        if (!$product) {
            continue;
        }
        $lineTotal = $product['price'] * $quantity;
        $subtotal += $lineTotal;
        $items[] = [
            'product' => $product,
            'quantity' => $quantity,
            'lineTotal' => $lineTotal,
        ];
    }
    return ['items' => $items, 'subtotal' => $subtotal];
}

function ensureLoggedIn(): void
{
    if (empty($_SESSION['admin_logged_in'])) {
        header('Location: /public/admin/login.php');
        exit;
    }
}

function sendOrderEmails(array $order, array $items): void
{
    $config = getConfig();
    $subject = sprintf('%s - Order #%d Confirmation', $config['store_name'], $order['id']);
    $headers = sprintf("From: %s\r\nContent-Type: text/plain; charset=UTF-8", $config['from_email']);

    $lines = [
        "Hi {$order['customer_name']},",
        '',
        'Thank you for your order! Here are the details:',
        '',
    ];
    foreach ($items as $item) {
        $lines[] = sprintf('- %s x%d - $%0.2f', $item['product']['name'], $item['quantity'], $item['lineTotal']);
    }
    $lines[] = '';
    $lines[] = sprintf('Total: $%0.2f', $order['total']);
    $lines[] = '';
    $lines[] = "We will notify you once your order ships.";
    $lines[] = '';
    $lines[] = 'Shipping Address:';
    $lines[] = $order['address'];
    $lines[] = '';
    $lines[] = 'Thanks,';
    $lines[] = $config['store_name'];

    $message = implode("\n", $lines);

    @mail($order['email'], $subject, $message, $headers);

    $adminSubject = sprintf('%s - New Order #%d', $config['store_name'], $order['id']);
    $adminMessage = $message . "\n\nCustomer email: {$order['email']}";
    @mail($config['admin_email'], $adminSubject, $adminMessage, $headers);
}
