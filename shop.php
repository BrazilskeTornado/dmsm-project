<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';

$message = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? null)) {
        $error = 'Your session expired. Please refresh and try again.';
    } else {
        try {
            add_to_cart((int) ($_POST['flavor_id'] ?? 0), (int) ($_POST['quantity'] ?? 1));
            $message = 'Added to your cart.';
        } catch (Throwable $exception) {
            $error = $exception->getMessage();
        }
    }
}

$flavors = database()->query('SELECT id, name, description, price FROM flavors WHERE active = 1 ORDER BY id')->fetchAll();
$pageTitle = 'Shop';
require __DIR__ . '/components/header.php';
?>
<section class="section narrow">
    <p class="eyebrow">The formula</p>
    <h1>Pick your flavor.</h1>
    <p class="lead">One 30-serving pouch. €29.90 per pouch. Mix one scoop with 500 ml of cold water.</p>
    <?php if ($message): ?><p class="notice success"><?= e($message) ?></p><?php endif; ?>
    <?php if ($error): ?><p class="notice error"><?= e($error) ?></p><?php endif; ?>
    <div class="shop-grid">
        <?php foreach ($flavors as $flavor): ?>
            <form class="flavor-card" method="post">
                <span class="flavor-dot flavor-<?= (int) $flavor['id'] ?>"></span>
                <h2><?= e($flavor['name']) ?></h2>
                <p><?= e($flavor['description']) ?></p>
                <strong>€<?= number_format((float) $flavor['price'], 2) ?></strong>
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="flavor_id" value="<?= (int) $flavor['id'] ?>">
                <label>Quantity
                    <span class="quantity-control">
                        <button class="quantity-button" type="button" data-quantity-action="decrease" aria-label="Decrease quantity">−</button>
                        <input type="number" name="quantity" value="1" min="1" max="20" aria-label="Quantity of <?= e($flavor['name']) ?>">
                        <button class="quantity-button" type="button" data-quantity-action="increase" aria-label="Increase quantity">+</button>
                    </span>
                </label>
                <button class="button" type="submit">Add to cart</button>
            </form>
        <?php endforeach; ?>
    </div>
    <p class="shop-cart-link"><a class="button" href="cart.php">View cart (<?= cart_count() ?>)</a></p>
</section>
<?php require __DIR__ . '/components/footer.php'; ?>
