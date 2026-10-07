<?php
require_once __DIR__ . '/../includes/functions.php';
$pageTitle = $pageTitle ?? APP_NAME;
$currentPage = basename($_SERVER['SCRIPT_NAME'] ?? 'index.php');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> | <?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="assets/style.css">
    <!-- Replace the placeholder IDs in includes/config.php before production. -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= e(GA4_MEASUREMENT_ID) ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?= e(GA4_MEASUREMENT_ID) ?>');
    </script>
    <script type="text/javascript">
        (function(c,l,a,r,i,t,y){
            c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
            t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
            y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
        })(window, document, "clarity", "script", "<?= e(CLARITY_PROJECT_ID) ?>");
    </script>
</head>
<body>
<header class="site-header">
    <a class="logo" href="index.php">AURA<span>PERFORM</span></a>
    <nav>
        <a class="<?= $currentPage === 'index.php' ? 'active' : '' ?>" href="index.php" <?= $currentPage === 'index.php' ? 'aria-current="page"' : '' ?>>About</a>
        <a class="<?= $currentPage === 'shop.php' ? 'active' : '' ?>" href="shop.php" <?= $currentPage === 'shop.php' ? 'aria-current="page"' : '' ?>>Shop</a>
        <a class="<?= in_array($currentPage, ['cart.php', 'checkout.php'], true) ? 'active' : '' ?>" href="cart.php" <?= in_array($currentPage, ['cart.php', 'checkout.php'], true) ? 'aria-current="page"' : '' ?>>Cart <strong>(<?= cart_count() ?>)</strong></a>
    </nav>
</header>
<main>
