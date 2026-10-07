<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

function cart(): array
{
    return $_SESSION['cart'] ?? [];
}

function cart_count(): int
{
    return array_sum(array_column(cart(), 'quantity'));
}

function cart_total(): float
{
    return array_reduce(cart(), static function (float $total, array $item): float {
        return $total + ($item['price'] * $item['quantity']);
    }, 0.0);
}

function add_to_cart(int $flavorId, int $quantity): void
{
    $quantity = max(1, min($quantity, 20));
    $statement = database()->prepare('SELECT id, name, price FROM flavors WHERE id = ? AND active = 1');
    $statement->execute([$flavorId]);
    $flavor = $statement->fetch();

    if (!$flavor) {
        throw new InvalidArgumentException('Please select a valid flavor.');
    }

    $existingQuantity = $_SESSION['cart'][$flavorId]['quantity'] ?? 0;
    $_SESSION['cart'][$flavorId] = [
        'flavor_id' => (int) $flavor['id'],
        'flavor' => $flavor['name'],
        'price' => (float) $flavor['price'],
        'quantity' => min(20, $existingQuantity + $quantity),
    ];
}

function update_cart(array $quantities): void
{
    foreach ($quantities as $flavorId => $quantity) {
        $flavorId = (int) $flavorId;
        if (!isset($_SESSION['cart'][$flavorId])) {
            continue;
        }

        $quantity = (int) $quantity;
        if ($quantity < 1) {
            unset($_SESSION['cart'][$flavorId]);
            continue;
        }

        $_SESSION['cart'][$flavorId]['quantity'] = min(20, $quantity);
    }
}

function remove_from_cart(int $flavorId): void
{
    unset($_SESSION['cart'][$flavorId]);
}
