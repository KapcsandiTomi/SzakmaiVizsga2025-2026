SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS users_db
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_hungarian_ci;

USE users_db;

-- =========================
-- USERS
-- =========================
CREATE TABLE IF NOT EXISTS `4` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    profile_pic VARCHAR(255) DEFAULT NULL,
    failed_attempts INT DEFAULT 0,
    last_failed_login DATETIME DEFAULT NULL,
    is_admin TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================
-- ORDERS 
-- =========================
CREATE TABLE IF NOT EXISTS orders (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================
-- PC CONFIG.
-- =========================
CREATE TABLE IF NOT EXISTS pc_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS pc_products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price INT NOT NULL,
    image VARCHAR(255),
    category_id INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS pc_configurations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS pc_configuration_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    configuration_id INT NOT NULL,
    category_id INT NOT NULL,
    product_id INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Értékelések
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(255) NOT NULL,
    user_name VARCHAR(255) NOT NULL,
    rating TINYINT NOT NULL, -- 1-5 csillag
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` varchar(255) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_image` varchar(500) NOT NULL,
  `product_link` varchar(500) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_favorite` (`user_id`, `product_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `4` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================
-- PC CATEG.
-- =========================
INSERT INTO pc_categories (name) VALUES
('Processor'),
('CPU Cooler'),
('Motherboard'),
('Graphics Card'),
('Memory'),
('SSD Drive'),
('Hard Drive'),
('Power Supply'),
('Computer Case'),
('System Cooling');

-- =========================
-- PC PRODUCTS 
-- =========================

-- Processzor
INSERT INTO pc_products (name, price, image, category_id) VALUES
('AMD Ryzen 5 5600G', 140.50, NULL, 1),
('Intel Core i5-12400F', 146.99, NULL, 1),
('AMD Ryzen 5 7600X', 182.25, NULL, 1),
('Intel Core i7-12700KF', 228.75, NULL, 1),
('AMD Ryzen 7 7700X', 231.25, NULL, 1),
('Intel Core i9-12900K', 387.50, NULL, 1),
('AMD Ryzen 9 7950X', 524.99, NULL, 1),
('Intel Core i5-13600K', 219.99, NULL, 1),
('AMD Ryzen 7 5800X3D', 262.50, NULL, 1),
('Intel Core i3-12100', 87.50, NULL, 1);

-- Processzor hűtő
INSERT INTO pc_products (name, price, image, category_id) VALUES
('Cooler Master Hyper 212', 32.50, NULL, 2),
('Noctua NH-D15', 82.50, NULL, 2),
('be quiet! Dark Rock Pro 4', 87.50, NULL, 2),
('Arctic Freezer 34', 40.00, NULL, 2),
('Corsair H100i RGB', 100.00, NULL, 2),
('NZXT Kraken X63', 124.99, NULL, 2),
('Deepcool Gammaxx 400', 27.50, NULL, 2),
('Thermalright Assassin King 120', 55.00, NULL, 2),
('Scythe Fuma 2', 49.99, NULL, 2),
('Noctua NH-U12S', 57.50, NULL, 2);

-- Alaplap
INSERT INTO pc_products (name, price, image, category_id) VALUES
('ASUS PRIME B550M-K', 97.50, NULL, 3),
('MSI B660M PRO', 107.50, NULL, 3),
('ASUS TUF B650-PLUS', 175.00, NULL, 3),
('Gigabyte B550 AORUS Elite', 129.99, NULL, 3),
('ASRock B660 Steel Legend', 137.50, NULL, 3),
('MSI MPG Z690 Carbon', 287.50, NULL, 3),
('ASUS ROG Strix B550-F', 154.99, NULL, 3),
('Gigabyte Z690 AORUS Master', 374.99, NULL, 3),
('ASRock X670E Taichi', 499.99, NULL, 3),
('MSI MAG B550 Tomahawk', 169.99, NULL, 3);

-- Videokártya
INSERT INTO pc_products (name, price, image, category_id) VALUES
('NVIDIA RTX 3060 12GB', 324.99, NULL, 4),
('NVIDIA RTX 4060 8GB', 399.99, NULL, 4),
('AMD Radeon RX 6700 XT', 374.99, NULL, 4),
('NVIDIA RTX 3070 8GB', 474.99, NULL, 4),
('AMD Radeon RX 6800 XT', 574.99, NULL, 4),
('NVIDIA RTX 3080 10GB', 649.99, NULL, 4),
('AMD Radeon RX 6900 XT', 799.99, NULL, 4),
('NVIDIA RTX 4090 24GB', 1374.99, NULL, 4),
('AMD Radeon RX 6600 XT', 299.99, NULL, 4),
('NVIDIA RTX 3050 8GB', 249.99, NULL, 4);

-- Memória
INSERT INTO pc_products (name, price, image, category_id) VALUES
('16GB DDR4 3200MHz Kingston', 49.99, NULL, 5),
('32GB DDR4 3600MHz Corsair', 87.50, NULL, 5),
('32GB DDR5 6000MHz Kingston', 112.50, NULL, 5),
('16GB DDR4 3000MHz Crucial', 44.99, NULL, 5),
('32GB DDR5 5200MHz G.Skill', 99.99, NULL, 5),
('64GB DDR4 3200MHz Corsair', 187.50, NULL, 5),
('16GB DDR5 4800MHz Kingston', 62.50, NULL, 5),
('32GB DDR4 3000MHz Crucial', 82.50, NULL, 5),
('64GB DDR5 6000MHz G.Skill', 224.99, NULL, 5),
('8GB DDR4 2666MHz Kingston', 27.50, NULL, 5);

-- SSD meghajtó
INSERT INTO pc_products (name, price, image, category_id) VALUES
('Kingston NV2 1TB NVMe', 54.99, NULL, 6),
('Samsung 980 1TB NVMe', 74.99, NULL, 6),
('WD Blue SN550 1TB NVMe', 59.99, NULL, 6),
('Crucial P5 500GB NVMe', 39.99, NULL, 6),
('Samsung 970 EVO 1TB NVMe', 87.50, NULL, 6),
('Seagate FireCuda 520 1TB', 99.99, NULL, 6),
('Kingston A2000 500GB NVMe', 29.99, NULL, 6),
('Crucial MX500 1TB SATA', 49.99, NULL, 6),
('WD Black SN750 1TB NVMe', 82.50, NULL, 6),
('Samsung 980 Pro 1TB NVMe', 112.50, NULL, 6);

-- Merevlemez
INSERT INTO pc_products (name, price, image, category_id) VALUES
('Seagate Barracuda 2TB HDD', 62.50, NULL, 7),
('WD Blue 1TB HDD', 44.99, NULL, 7),
('Seagate IronWolf 4TB NAS', 149.99, NULL, 7),
('WD Black 2TB HDD', 74.99, NULL, 7),
('Toshiba P300 3TB', 92.50, NULL, 7),
('Seagate Barracuda 1TB', 37.50, NULL, 7),
('WD Red 4TB NAS', 199.99, NULL, 7),
('Toshiba X300 5TB', 224.99, NULL, 7),
('Seagate FireCuda 2TB', 99.99, NULL, 7),
('WD Blue 2TB HDD', 62.50, NULL, 7);

-- Tápegység
INSERT INTO pc_products (name, price, image, category_id) VALUES
('Chieftec 600W 80+ Bronze', 59.99, NULL, 8),
('Corsair RM750 750W Gold', 112.50, NULL, 8),
('Seasonic Focus GX 650W Gold', 107.50, NULL, 8),
('Cooler Master MWE 650W Bronze', 57.50, NULL, 8),
('be quiet! Straight Power 11 750W', 124.99, NULL, 8),
('EVGA SuperNOVA 850W Gold', 137.50, NULL, 8),
('Corsair TX650M 650W Gold', 99.99, NULL, 8),
('Thermaltake Toughpower 750W Gold', 114.99, NULL, 8),
('NZXT C750 750W Gold', 122.50, NULL, 8),
('Seasonic S12III 550W Bronze', 47.50, NULL, 8);

-- Számítógépház
INSERT INTO pc_products (name, price, image, category_id) VALUES
('NZXT H510', 99.99, NULL, 9),
('Corsair 4000D Airflow', 112.50, NULL, 9),
('Fractal Design Meshify C', 124.99, NULL, 9),
('Cooler Master MasterBox NR600', 87.50, NULL, 9),
('be quiet! Pure Base 500DX', 134.99, NULL, 9),
('Phanteks Eclipse P400A', 107.50, NULL, 9),
('Lian Li Lancool II', 149.99, NULL, 9),
('Thermaltake V200', 74.99, NULL, 9),
('Cooler Master MasterCase H500', 162.50, NULL, 9),
('Corsair 275R Airflow', 97.50, NULL, 9);

-- Rendszer hűtő
INSERT INTO pc_products (name, price, image, category_id) VALUES
('Arctic P12 120mm ventilátor', 8.75, NULL, 10),
('Be Quiet! Pure Wings 2', 9.99, NULL, 10),
('Noctua NF-P12', 14.99, NULL, 10),
('Corsair AF120', 9.99, NULL, 10),
('NZXT F120', 11.25, NULL, 10),
('Cooler Master SickleFlow 120', 12.50, NULL, 10),
('Thermaltake Riing 12', 13.75, NULL, 10),
('Deepcool RF120', 9.99, NULL, 10),
('ARCTIC F12', 8.25, NULL, 10),
('Phanteks PH-F120', 11.25, NULL, 10);


-- =========================
-- USERS
-- =========================

INSERT INTO `4` (name, email, password, profile_pic, is_admin) VALUES
(
  'Admin User',
  'admin@gmail.com',
  '$2a$12$aXJKZlajILqPLy352YS0W.EGkIgSbg35ga5NnT/u73EyNvn8G0qA6',
  NULL,
  1
);

COMMIT;

