CREATE DATABASE IF NOT EXISTS users_db;
USE users_db;

--"4" tabla mi nekunk a users
CREATE TABLE IF NOT EXISTS `4` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    profile_pic VARCHAR(255) DEFAULT NULL,
    failed_attempts INT DEFAULT 0,
    last_failed_login DATETIME DEFAULT NULL,
    is_admin TINYINT(1) DEFAULT 0
);

--orders tabla ahova mentjuk a rendeleseket
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(255) NOT NULL,
    customer_email VARCHAR(255) NOT NULL,
    customer_address VARCHAR(255) NOT NULL,
    card_type VARCHAR(50) NOT NULL,
    card_number VARCHAR(50) NOT NULL,
    expiry VARCHAR(10) NOT NULL,
    cvv VARCHAR(10) NOT NULL,
    order_data TEXT NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'Not Processed',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);


-- admin user
-- Password is admin123
INSERT INTO `4` (name, email, password, profile_pic, is_admin)
VALUES (
    'Admin User',
    'admin@gmail.com',
    '$2y$10$e0NRgFZhzF0v4mQYpxV4..mTZP2fPy0bbLp2Q.qH7iZQZ8LEKPm3G',
    NULL,
    1
);

-- normal user not admin
INSERT INTO `4` (name, email, password, profile_pic, is_admin)
VALUES (
    '11tomy11',
    'kapcsandi.tomi@gmail.com',
    '$2y$10$2QdV1d3n3k8A7f4Z5xSVWOZk2lIx1ScFqE0fG3V8yXID5wL3Uu8XS',
    NULL,
    0
);
