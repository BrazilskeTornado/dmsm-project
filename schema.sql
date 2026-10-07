CREATE DATABASE IF NOT EXISTS auraperform CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE auraperform;

CREATE TABLE IF NOT EXISTS products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    description TEXT NOT NULL,
    active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS flavors (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL,
    name VARCHAR(80) NOT NULL,
    description VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    active TINYINT(1) NOT NULL DEFAULT 1,
    FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS orders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(120) NOT NULL,
    customer_email VARCHAR(190) NOT NULL,
    shipping_address TEXT NOT NULL,
    payment_method VARCHAR(40) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS order_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    flavor_id INT UNSIGNED NOT NULL,
    flavor_name VARCHAR(80) NOT NULL,
    quantity INT UNSIGNED NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (flavor_id) REFERENCES flavors(id)
) ENGINE=InnoDB;

INSERT INTO products (id, name, description)
VALUES (1, 'AuraPerform Ionic Creatine Mix', 'Intra-workout ionic hydration with creatine and performance ingredients.')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO flavors (product_id, name, description, price) VALUES
(1, 'Citrus Charge', 'Bright lemon-lime with a clean finish.', 29.90),
(1, 'Berry Voltage', 'Tart berry flavor for a refreshing lift.', 29.90),
(1, 'Tropical Current', 'Mango and passionfruit-inspired tropical blend.', 29.90)
ON DUPLICATE KEY UPDATE description = VALUES(description), price = VALUES(price);
