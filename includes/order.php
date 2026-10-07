<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';

function create_order(array $customer): array
{
    $items = cart();
    $total = cart_total();
    $pdo = database();
    $pdo->beginTransaction();

    try {
        $order = $pdo->prepare('INSERT INTO orders (customer_name, customer_email, shipping_address, payment_method, total_amount) VALUES (?, ?, ?, ?, ?)');
        $order->execute([$customer['name'], $customer['email'], $customer['address'], $customer['payment_method'], $total]);
        $orderId = (int) $pdo->lastInsertId();
        $item = $pdo->prepare('INSERT INTO order_items (order_id, flavor_id, flavor_name, quantity, unit_price) VALUES (?, ?, ?, ?, ?)');

        foreach ($items as $cartItem) {
            $item->execute([$orderId, $cartItem['flavor_id'], $cartItem['flavor'], $cartItem['quantity'], $cartItem['price']]);
        }

        $pdo->commit();
        $_SESSION['cart'] = [];

        return [
            'id' => $orderId,
            'items' => $items,
            'total' => $total,
            'mail_sent' => send_order_notification($orderId, $customer, $items, $total),
        ];
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $exception;
    }
}
