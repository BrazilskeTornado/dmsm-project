<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';

$message = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        $error = 'Your session expired. Please refresh and try again.';
    } elseif (isset($_POST['remove_flavor_id'])) {
        remove_from_cart((int) $_POST['remove_flavor_id']);
        $message = 'Item removed from your cart.';
    } else {
        update_cart($_POST['quantities'] ?? []);
        $message = 'Cart updated.';
    }
}

$pageTitle = 'Your cart';
require __DIR__ . '/components/header.php';
?>
<section class="section narrow">
    <p class="eyebrow">Your order</p>
    <h1>Shopping cart.</h1>
    <?php if ($message): ?><p class="notice success"><?= e($message) ?></p><?php endif; ?>
    <?php if ($error): ?><p class="notice error"><?= e($error) ?></p><?php endif; ?>
    <?php if (!cart()): ?>
        <p>Your cart is empty. Browse the formula and choose a flavor to get started.</p>
        <a class="button" href="shop.php">Browse flavors</a>
    <?php else: ?>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <ul class="cart-list cart-edit-list">
                <?php foreach (cart() as $item): ?>
                    <li>
                        <span><?= e($item['flavor']) ?><small>€<?= number_format($item['price'], 2) ?> per pouch</small></span>
                        <label>Qty <input type="number" name="quantities[<?= (int) $item['flavor_id'] ?>]" value="<?= (int) $item['quantity'] ?>" min="1" max="20"></label>
                        <strong>€<?= number_format($item['price'] * $item['quantity'], 2) ?></strong>
                        <button class="link-button" type="submit" name="remove_flavor_id" value="<?= (int) $item['flavor_id'] ?>">Remove</button>
                    </li>
                <?php endforeach; ?>
            </ul>
            <p class="cart-total">Total <strong>€<?= number_format(cart_total(), 2) ?></strong></p>
            <button class="button" type="submit">Update cart</button>
            <a class="button button-secondary" href="checkout.php">Continue to checkout</a>
        </form>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/components/footer.php'; ?>
