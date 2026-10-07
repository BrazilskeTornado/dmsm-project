<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/cart.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\Exception as MailException;
use PHPMailer\PHPMailer\PHPMailer;

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
    if (!mail_enabled()) {
        return false;
    }

    $smtp = smtp_config();
    $lines = ["New " . APP_NAME . " order #{$orderId}", '', 'Customer: ' . $customer['name'], 'Email: ' . $customer['email'], 'Address: ' . $customer['address'], 'Payment: ' . $customer['payment_method'], '', 'Items:'];
    foreach ($items as $item) {
        $lines[] = sprintf('%s x %d - €%.2f', $item['flavor'], $item['quantity'], $item['price'] * $item['quantity']);
    }
    $lines[] = '';
    $lines[] = sprintf('Total: €%.2f', $total);

    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $smtp['host'];
        $mail->Port = $smtp['port'];
        $mail->SMTPAuth = $smtp['username'] !== '';

        if ($mail->SMTPAuth) {
            $mail->Username = $smtp['username'];
            $mail->Password = $smtp['password'];
        }

        if ($smtp['encryption'] === 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } elseif ($smtp['encryption'] === 'tls') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }

        $mail->CharSet = 'UTF-8';
        $mail->setFrom($smtp['from_email'], $smtp['from_name']);
        $mail->addAddress(ADMIN_EMAIL);
        $mail->addReplyTo($customer['email'], $customer['name']);
        $mail->Subject = 'New ' . APP_NAME . ' order #' . $orderId;
        $mail->Body = implode("\n", $lines);
        $mail->isHTML(false);

        return $mail->send();
    } catch (MailException $exception) {
        error_log('Order email failed: ' . $exception->getMessage());
        return false;
    }
}
