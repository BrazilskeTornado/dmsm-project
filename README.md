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
2. An email notification is sent to `ADMIN_EMAIL`.
3. The customer sees the order confirmation page.

If the database order succeeds but email delivery fails, the order remains saved and the confirmation page displays a warning.

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

## Email note

The prototype uses PHP's native `mail()` function. Production deployments should use authenticated SMTP through a mail provider, preferably via PHPMailer or another maintained mail library.

## Security and production checklist

Before production:

- Move database credentials to environment variables.
- Use HTTPS.
- Configure authenticated SMTP.
- Replace demo payment handling with a real payment provider.
- Add rate limiting and abuse protection.
- Add a privacy policy and cookie/analytics consent flow.
- Configure stricter session cookie settings.
- Add order status and administration tools.
- Add automated tests for cart, checkout validation, and order creation.

