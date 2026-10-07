<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/order.php';

$error = $_SESSION['checkout_error'] ?? '';
unset($_SESSION['checkout_error']);
$customer = $_SESSION['checkout_form'] ?? [];
unset($_SESSION['checkout_form']);
$orderConfirmation = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer = [
        'name' => trim((string) ($_POST['name'] ?? '')),
        'email' => trim((string) ($_POST['email'] ?? '')),
        'address' => trim((string) ($_POST['address'] ?? '')),
        'payment_method' => trim((string) ($_POST['payment_method'] ?? '')),
    ];
    $allowedPayments = ['Card (demo)', 'Bank transfer', 'Cash on delivery'];

    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        $error = 'Your session expired. Please refresh and try again.';
    } elseif (!cart()) {
        $error = 'Your cart is empty. Add a product before checking out.';
    } elseif ($customer['name'] === '' || !filter_var($customer['email'], FILTER_VALIDATE_EMAIL) || $customer['address'] === '' || !in_array($customer['payment_method'], $allowedPayments, true)) {
        $error = 'Please complete all checkout fields with valid details.';
    } else {
        try {
            $orderConfirmation = create_order($customer);
        } catch (Throwable $exception) {
            $error = 'We could not place your order. Please try again.';
        }
    }
}

$pageTitle = $orderConfirmation ? 'Order confirmed' : 'Checkout';
require __DIR__ . '/components/header.php';
?>
<?php if ($orderConfirmation): ?>
    <section class="section narrow confirmation">
        <p class="eyebrow">Order received</p>
        <h1>Thanks, <?= e($customer['name']) ?>.</h1>
        <p>Your order <strong>#<?= (int) $orderConfirmation['id'] ?></strong> has been saved. We’ll contact you at <?= e($customer['email']) ?> with the next steps.</p>
        <?php if (!$orderConfirmation['mail_sent']): ?><p class="notice error">The order was saved, but the store email notification could not be sent. Check the mail server configuration.</p><?php endif; ?>
        <a class="button" href="index.php">Back to AuraPerform</a>
    </section>
<?php elseif (!cart()): ?>
    <section class="section narrow">
        <p class="eyebrow">Checkout</p>
        <h1>Your cart is empty.</h1>
        <p>Add a pouch before continuing to checkout.</p>
        <a class="button" href="shop.php">Browse flavors</a>
    </section>
<?php else: ?>
    <section class="section narrow">
        <p class="eyebrow">Final step</p>
        <h1>Complete your order.</h1>
        <div class="checkout-summary">
            <h2>Order summary</h2>
            <?php foreach (cart() as $item): ?>
                <p><span><?= e($item['flavor']) ?> × <?= (int) $item['quantity'] ?></span><strong>€<?= number_format($item['price'] * $item['quantity'], 2) ?></strong></p>
            <?php endforeach; ?>
            <p class="cart-total">Total <strong>€<?= number_format(cart_total(), 2) ?></strong></p>
        </div>
        <?php if ($error): ?><p class="notice error"><?= e($error) ?></p><?php endif; ?>
        <form class="checkout-form" method="post">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <label>Full name <input required name="name" maxlength="120" value="<?= e($customer['name'] ?? '') ?>"></label>
            <label>Email <input required type="email" name="email" maxlength="190" value="<?= e($customer['email'] ?? '') ?>"></label>
            <label>Shipping address <textarea required name="address" maxlength="500" rows="3"><?= e($customer['address'] ?? '') ?></textarea></label>
            <label>Payment method
                <select name="payment_method" required>
                    <option value="">Select one</option>
                    <?php foreach (['Card (demo)', 'Bank transfer', 'Cash on delivery'] as $payment): ?>
                        <option <?= ($customer['payment_method'] ?? '') === $payment ? 'selected' : '' ?>><?= e($payment) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <button class="button" type="submit">Place order</button>
        </form>
        <a class="back-link" href="cart.php">← Back to cart</a>
    </section>
<?php endif; ?>
<?php require __DIR__ . '/components/footer.php'; ?>
