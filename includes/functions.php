<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/cart.php';

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf(?string $token): bool
{
    return is_string($token) && hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

function send_order_notification(int $orderId, array $customer, array $items, float $total): bool
{
    $lines = ["New " . APP_NAME . " order #{$orderId}", '', 'Customer: ' . $customer['name'], 'Email: ' . $customer['email'], 'Address: ' . $customer['address'], 'Payment: ' . $customer['payment_method'], '', 'Items:'];
    foreach ($items as $item) {
        $lines[] = sprintf('%s x %d - €%.2f', $item['flavor'], $item['quantity'], $item['price'] * $item['quantity']);
    }
    $lines[] = '';
    $lines[] = sprintf('Total: €%.2f', $total);

    $headers = 'From: orders@' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . "\r\n";
    $headers .= 'Reply-To: ' . $customer['email'] . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    return mail(ADMIN_EMAIL, 'New ' . APP_NAME . ' order #' . $orderId, implode("\n", $lines), $headers);
}
