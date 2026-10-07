<?php
declare(strict_types=1);

session_start();

const APP_NAME = 'AuraPerform';
const ADMIN_EMAIL = 'davidsvaricek@seznam.cz';
const GA4_MEASUREMENT_ID = 'G-XXXXXXXXXX';
const CLARITY_PROJECT_ID = 'xxxxxxxxxx';

const DB_HOST = '127.0.0.1';
const DB_NAME = 'auraperform';
const DB_USER = 'root';
const DB_PASS = '';

function local_config(): array
{
    $path = __DIR__ . '/config.local.php';

    if (!is_file($path)) {
        return [];
    }

    $config = require $path;
    return is_array($config) ? $config : [];
}

function smtp_config(): array
{
    $local = local_config();

    return [
        'host' => $local['smtp_host'] ?? getenv('SMTP_HOST') ?: '',
        'port' => (int) ($local['smtp_port'] ?? getenv('SMTP_PORT') ?: 587),
        'username' => $local['smtp_username'] ?? getenv('SMTP_USERNAME') ?: '',
        'password' => $local['smtp_password'] ?? getenv('SMTP_PASSWORD') ?: '',
        'encryption' => strtolower((string) ($local['smtp_encryption'] ?? getenv('SMTP_ENCRYPTION') ?: 'tls')),
        'from_email' => $local['mail_from_email'] ?? getenv('MAIL_FROM_EMAIL') ?: '',
        'from_name' => $local['mail_from_name'] ?? getenv('MAIL_FROM_NAME') ?: APP_NAME,
    ];
}

function mail_enabled(): bool
{
    $local = local_config();
    $enabled = $local['mail_enabled'] ?? getenv('MAIL_ENABLED') ?: 'false';
    return filter_var($enabled, FILTER_VALIDATE_BOOLEAN);
}

function database(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    return $pdo;
}
