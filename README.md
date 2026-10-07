# AuraPerform

AuraPerform is a lightweight PHP/MySQL e-commerce prototype for an intra-workout ionic drink mix enhanced with creatine and performance ingredients.

The prototype includes:

- A dark, responsive landing page
- Product and flavor catalog
- Session-based shopping cart
- Dedicated checkout page
- MySQL/MariaDB order storage
- Native PHP email notification
- Google Analytics 4 and Microsoft Clarity snippets
- Responsive dark/neon frontend styling

## Requirements

- PHP 8.0 or newer
- MySQL or MariaDB
- Apache, XAMPP, or PHP's built-in development server
- PHP PDO MySQL extension
- PHP mail transport configured for order notifications

## Project structure

```text
dmsm/
├── index.php                  # Landing/about page
├── shop.php                   # Product and flavor catalog
├── cart.php                   # Cart review, quantity updates, item removal
├── checkout.php               # Checkout form and order confirmation
├── schema.sql                 # Database schema and seed flavor data
├── assets/
│   └── style.css              # Shared responsive dark/neon design system
├── components/
│   ├── header.php              # Shared header, navigation, analytics
│   └── footer.php              # Shared footer
└── includes/
    ├── config.php              # Database, email, and analytics configuration
    ├── cart.php                # Cart session operations
    ├── functions.php           # Shared helpers and email notification
    └── order.php               # Transactional order creation
```

## Installation

### 1. Create the database

Import [schema.sql](schema.sql) into MySQL or MariaDB:

```bash
mysql -u root -p < schema.sql
```

This creates the `auraperform` database and seeds the initial product with three flavors:

- Citrus Charge
- Berry Voltage
- Tropical Current

### 2. Configure the application

Open [includes/config.php](includes/config.php) and update the database, email, and tracking values:

```php
const ADMIN_EMAIL = 'your-store-email@example.com';
const GA4_MEASUREMENT_ID = 'G-XXXXXXXXXX';
const CLARITY_PROJECT_ID = 'xxxxxxxxxx';

const DB_HOST = '127.0.0.1';
const DB_NAME = 'auraperform';
const DB_USER = 'root';
const DB_PASS = '';
```

Do not commit real credentials or production secrets to source control.

### 3. Run locally with XAMPP

Place the project in:

```text
C:\xampp\htdocs\dmsm
```

Start Apache and MySQL from the XAMPP Control Panel, then open:

```text
http://localhost/dmsm/index.php
```

Alternatively, run PHP's built-in server from the project directory:

```bash
php -S 127.0.0.1:8085 -t .
```

Then visit:

```text
http://127.0.0.1:8085/index.php
```

## Customer flow

```text
Landing page
    ↓
Shop / flavor catalog
    ↓
Cart
    ↓
Checkout
    ↓
Order confirmation
```

The active page is highlighted in the shared navigation. Cart and checkout are treated as one active shopping section.

## Order processing

Orders are stored in two tables:

- `orders` — customer details, payment method, total, and timestamp
- `order_items` — flavor, quantity, and unit price for each order line

Checkout uses a database transaction. If any order item fails to save, the order is rolled back.

After a successful database commit:

1. The session cart is cleared.
2. If `MAIL_ENABLED` is `true`, an email notification is sent to `ADMIN_EMAIL`.
3. The customer sees the order confirmation page.

If the database order succeeds but email delivery fails, the order remains saved and the confirmation page displays a warning.

For local XAMPP development, `MAIL_ENABLED` is `false` by default. Set it to `true` only after the real SMTP provider values have been configured, while keeping order creation functional during setup.

## Analytics

The shared [components/header.php](components/header.php) includes:

- Google Analytics 4 via `gtag.js`
- Microsoft Clarity

Replace the placeholder IDs in [includes/config.php](includes/config.php) with real project IDs before production.

## Payment note

The payment options are demo-only:

- Card (demo)
- Bank transfer
- Cash on delivery

No card details are collected or processed. A real deployment should integrate a payment provider such as Stripe, Adyen, or a local payment gateway and use hosted/tokenized payment fields.

## Email with PHPMailer

PHPMailer is installed through Composer and sends order notifications through a real authenticated SMTP provider. PHPMailer itself is the mail client; it still requires an SMTP server belonging to an email provider. No local mail simulator is used.

Use [.env.example](.env.example) as the environment variable reference. If you prefer entering SMTP settings directly in PHP, copy [includes/config.local.php.example](includes/config.local.php.example) to `includes/config.local.php` and replace the example values. This local file is ignored by Git.

Example:

```php
return [
    'mail_enabled' => true,
    'smtp_host' => 'smtp.your-provider.com',
    'smtp_port' => 587,
    'smtp_username' => 'orders@your-domain.com',
    'smtp_password' => 'your-smtp-password',
    'smtp_encryption' => 'tls',
    'mail_from_email' => 'orders@your-domain.com',
    'mail_from_name' => 'AuraPerform',
];
```

For a single local/server deployment this is convenient. Do not commit `includes/config.local.php`; it contains a secret.

For local XAMPP development, use the SMTP settings from your real email provider. For example, a provider may give you:

```text
SMTP host: smtp.example.com
SMTP port: 587
Encryption: tls
Username: orders@example.com
Password: SMTP password or app password
```

For Apache/XAMPP, add the real values to the relevant Apache virtual host or `<Directory>` block:

```apache
MAIL_ENABLED=true
SMTP_HOST=smtp.example.com
SMTP_PORT=587
SMTP_USERNAME=orders@example.com
SMTP_PASSWORD=replace-with-secret
SMTP_ENCRYPTION=tls
MAIL_FROM_EMAIL=orders@example.com
MAIL_FROM_NAME=AuraPerform
```

In Apache syntax, use `SetEnv`, for example:

```apache
SetEnv MAIL_ENABLED true
SetEnv SMTP_HOST smtp.example.com
SetEnv SMTP_PORT 587
SetEnv SMTP_USERNAME orders@example.com
SetEnv SMTP_PASSWORD replace-with-secret
SetEnv SMTP_ENCRYPTION tls
SetEnv MAIL_FROM_EMAIL orders@example.com
SetEnv MAIL_FROM_NAME AuraPerform
```

Port `587` normally uses `tls`; port `465` normally uses `ssl`. Use the exact host, port, encryption, username, and password provided by your email provider. Restart Apache after changing values. Never commit SMTP passwords.

For Gmail, use an App Password rather than the normal account password. For Seznam or another provider, use the SMTP host and credentials supplied in that provider's documentation.

After changing environment variables, restart Apache/PHP and submit a test order. If delivery fails, inspect the PHP/Apache error log.

## Security and production checklist

Before production:

- Move database credentials to environment variables.
- Use HTTPS.
- Configure authenticated SMTP through PHPMailer.
- Replace demo payment handling with a real payment provider.
- Add rate limiting and abuse protection.
- Add a privacy policy and cookie/analytics consent flow.
- Configure stricter session cookie settings.
- Add order status and administration tools.
- Add automated tests for cart, checkout validation, and order creation.
