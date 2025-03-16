/*
 Navicat Premium Data Transfer

 Source Server         : Mysql Localhost
 Source Server Type    : MySQL
 Source Server Version : 90001 (9.0.1)
 Source Host           : localhost:3306
 Source Schema         : daruma_db

 Target Server Type    : MySQL
 Target Server Version : 90001 (9.0.1)
 File Encoding         : 65001

 Date: 16/03/2025 21:35:46
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for cart
-- ----------------------------
DROP TABLE IF EXISTS `cart`;
CREATE TABLE `cart` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `added_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of cart
-- ----------------------------
BEGIN;
INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`, `added_at`) VALUES (41, 2, 56, 1, '2025-03-16 21:14:03');
COMMIT;

-- ----------------------------
-- Table structure for order_items
-- ----------------------------
DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of order_items
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for orders
-- ----------------------------
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','canceled') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `shipping_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `cc_number` varchar(255) DEFAULT NULL,
  `received_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of orders
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for payments
-- ----------------------------
DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `user_id` int NOT NULL,
  `payment_method` enum('credit_card','paypal','bank_transfer') NOT NULL,
  `status` enum('pending','completed','failed') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of payments
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for products
-- ----------------------------
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `edition` varchar(255) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of products
-- ----------------------------
BEGIN;
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (1, 'iPhone 16 Pro', 'White Titanium', '256GB Storage, 5G', 1599.00, 10, 'images/products/1742133806_iPhone 16 Pro.png', '2025-03-15 13:58:47');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (2, 'iPhone 16', 'Teal', '128GB Storage, 5G', 1299.00, 15, 'images/products/1742133798_iPhone 16 Pro.png', '2025-03-15 13:58:47');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (3, 'iPhone 15 Pro', 'Natural Titanium', '256GB Storage, 5G', 1399.00, 8, 'images/products/1742133784_iPhone 15 Pro.png', '2025-03-15 13:58:47');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (4, 'MacBook Pro 16\"', 'M3 Max, Space Black', '32GB RAM, 1TB SSD, 5G', 3499.00, 5, 'images/products/1742133769_MacBook Pro 16.png', '2025-03-15 14:00:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (5, 'MacBook Air 13\"', 'M2, Midnight', '16GB RAM, 512GB SSD, 5G', 1299.00, 10, 'images/products/1742133751_MacBook Air 13.png', '2025-03-15 14:00:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (6, 'Apple Watch Ultra 3', 'Titanium Case, Orange Alpine Loop', 'LTE, 49mm', 899.00, 12, 'images/products/1742133741_Apple Watch Ultra 3.png', '2025-03-15 14:00:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (7, 'AirPods Pro 3', 'White', 'Active Noise Cancellation, MagSafe', 299.00, 20, 'images/products/1742133734_AirPods Pro 3.png', '2025-03-15 14:00:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (8, 'Samsung Galaxy S24 Ultra', 'Phantom Black', '512GB Storage, 5G, 200MP Camera', 1499.00, 8, 'images/products/1742133700_Samsung Galaxy S24 Ultra.png', '2025-03-15 14:05:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (9, 'Samsung Galaxy Z Fold 6', 'Silver', '512GB Storage, 5G, Foldable', 1799.00, 6, 'images/products/1742133710_Samsung Galaxy S24 Ultra.png', '2025-03-15 14:05:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (10, 'Samsung Galaxy Watch 6 Classic', 'Black, 47mm', 'LTE, Sapphire Glass', 449.00, 15, 'images/products/1742133721_Samsung Galaxy Watch 6 Classic.png', '2025-03-15 14:05:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (11, 'Samsung Galaxy Buds 3 Pro', 'Graphite', 'ANC, Wireless Charging', 229.00, 18, 'images/products/default_image.png', '2025-03-15 14:05:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (12, 'Sony Xperia 1 VI', 'Frosted Black', '256GB Storage, 5G, 4K OLED', 1199.00, 10, 'images/products/1742133678_Sony Xperia 1 VI.png', '2025-03-15 14:10:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (13, 'Sony WH-1000XM6', 'Matte Black', 'Wireless, Noise Cancellation', 399.00, 25, 'images/products/default_image.png', '2025-03-15 14:10:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (14, 'Sony PlayStation 5 Pro', 'White', '1TB SSD, 8K HDR Gaming', 699.00, 12, 'images/products/1742133689_Sony PlayStation 5 Pro.png', '2025-03-15 14:10:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (15, 'Oppo Find X7 Pro', 'Moonlight Silver', '512GB Storage, 5G, Hasselblad Camera', 999.00, 10, 'images/products/default_image.png', '2025-03-15 14:15:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (16, 'Oppo Pad 2', 'Gold', '256GB Storage, 12.1-inch LCD', 599.00, 15, 'images/products/default_image.png', '2025-03-15 14:15:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (17, 'Oppo Enco X3', 'Midnight Blue', 'True Wireless, ANC', 199.00, 20, 'images/products/default_image.png', '2025-03-15 14:15:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (18, 'Xiaomi 14 Ultra', 'Black Ceramic', '1TB Storage, Leica Camera, 5G', 1299.00, 12, 'images/products/default_image.png', '2025-03-15 14:20:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (19, 'Xiaomi Pad 6 Pro', 'Blue', '256GB Storage, 12.6-inch AMOLED', 549.00, 18, 'images/products/default_image.png', '2025-03-15 14:20:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (20, 'Redmi Buds 5 Pro', 'Pearl White', 'Bluetooth 5.3, ANC', 149.00, 25, 'images/products/default_image.png', '2025-03-15 14:20:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (21, 'Lenovo Legion 9i', 'Shadow Black', '32GB RAM, 1TB SSD, RTX 4090', 3999.00, 5, 'images/products/default_image.png', '2025-03-15 14:30:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (22, 'Lenovo Yoga 9i', 'Storm Grey', '16GB RAM, 512GB SSD, Touchscreen', 1799.00, 8, 'images/products/default_image.png', '2025-03-15 14:30:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (23, 'Lenovo ThinkPad X1 Carbon Gen 12', 'Black', '16GB RAM, 1TB SSD, 5G', 2499.00, 6, 'images/products/default_image.png', '2025-03-15 14:30:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (24, 'Dell XPS 17', 'Titanium Silver', '32GB RAM, 1TB SSD, RTX 4070', 3199.00, 5, 'images/products/1742133660_Dell Latitude 7440.png', '2025-03-15 14:35:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (25, 'Dell Alienware m16', 'Dark Metallic', '32GB RAM, 1TB SSD, RTX 4080', 3499.00, 7, 'images/products/1742133651_Dell Latitude 7440.png', '2025-03-15 14:35:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (26, 'Dell Latitude 7440', 'Graphite Black', '16GB RAM, 512GB SSD, 5G', 2199.00, 10, 'images/products/1742133643_Dell Latitude 7440.png', '2025-03-15 14:35:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (27, 'Asus ROG Zephyrus G16', 'Eclipse Grey', '32GB RAM, 1TB SSD, RTX 4080', 3299.00, 5, 'images/products/1742133635_Asus TUF Gaming F15.png', '2025-03-15 14:40:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (28, 'Asus VivoBook 15', 'Indie Black', '8GB RAM, 512GB SSD, Ryzen 7', 899.00, 12, 'images/products/1742133625_Asus TUF Gaming F15.png', '2025-03-15 14:40:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (29, 'Asus TUF Gaming F15', 'Mecha Gray', '16GB RAM, 1TB SSD, RTX 4060', 1599.00, 8, 'images/products/1742133617_Asus TUF Gaming F15.png', '2025-03-15 14:40:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (30, 'Nvidia GeForce RTX 4090', 'Founders Edition', '24GB GDDR6X, Ray Tracing, DLSS 3', 1799.00, 15, 'images/products/1742133607_Nvidia GeForce RTX 4090 TI.png', '2025-03-15 14:45:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (31, 'Nvidia GeForce RTX 4080 Super', 'Founders Edition', '16GB GDDR6X, Ray Tracing, DLSS 3', 1399.00, 10, 'images/products/1742133593_Nvidia GeForce RTX 4080 Super.png', '2025-03-15 14:45:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (32, 'Nvidia Shield TV Pro', 'Black', '4K HDR Streaming, AI Upscaling', 199.00, 20, 'images/products/1742133581_Nvidia GeForce RTX 4080 Super.png', '2025-03-15 14:45:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (33, 'Gigabyte Aorus 17X', 'Cyber Green', '32GB RAM, 1TB SSD, RTX 4090', 3499.00, 5, 'images/products/default_image.png', '2025-03-15 14:50:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (34, 'Gigabyte GeForce RTX 4070 Ti', 'Eagle OC', '12GB GDDR6X, Ray Tracing, DLSS 3', 899.00, 12, 'images/products/default_image.png', '2025-03-15 14:50:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (35, 'Gigabyte AORUS Gen4 7000s SSD', '1TB', 'PCIe 4.0 NVMe, 7000MB/s', 199.00, 18, 'images/products/default_image.png', '2025-03-15 14:50:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (36, 'MSI Titan GT77 HX', 'Black', '64GB RAM, 2TB SSD, RTX 4090', 4999.00, 4, 'images/products/default_image.png', '2025-03-15 14:55:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (37, 'MSI Stealth 16 Studio', 'Star Blue', '32GB RAM, 1TB SSD, RTX 4080', 2999.00, 6, 'images/products/default_image.png', '2025-03-15 14:55:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (38, 'MSI MAG B760M Mortar', 'WiFi DDR5', 'LGA1700, Intel 13th Gen Ready', 199.00, 15, 'images/products/1742133564_MSI MAG B760M Mortar.png', '2025-03-15 14:55:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (39, 'Razer Blade 18', 'Mercury White', '64GB RAM, 2TB SSD, RTX 4090', 5199.00, 3, 'images/products/1742133548_Razer Basilisk V3 Pro.png', '2025-03-15 15:00:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (40, 'Razer BlackShark V2 Pro', 'Black', 'Wireless Gaming Headset, THX Spatial Audio', 179.00, 20, 'images/products/1742133537_Razer Basilisk V3 Pro.png', '2025-03-15 15:00:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (41, 'Razer Basilisk V3 Pro', 'Black', 'Wireless Gaming Mouse, Chroma RGB', 159.00, 25, 'images/products/1742133524_Razer Basilisk V3 Pro.png', '2025-03-15 15:00:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (42, 'Sony Alpha A7 IV', 'Full-Frame Mirrorless', '33MP, 4K Video, 10fps', 2499.00, 7, 'images/products/1742133493_Sony Alpha A7 IV.png', '2025-03-15 15:10:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (43, 'Sony ZV-1', 'Vlogging Camera', '20MP, 4K Video, Flip Screen', 749.00, 10, 'images/products/1742133482_Sony Alpha A7 IV.png', '2025-03-15 15:10:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (44, 'Sony FX3', 'Cinema Line', 'Full-Frame, 4K 120fps, S-Cinetone', 3899.00, 5, 'images/products/1742133468_Sony Alpha A7 IV.png', '2025-03-15 15:10:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (45, 'Canon EOS R5', 'Mirrorless', '45MP, 8K Video, Dual Pixel AF', 3899.00, 6, 'images/products/1742133451_Canon PowerShot G7X Mark III.png', '2025-03-15 15:15:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (46, 'Canon EOS 90D', 'DSLR', '32.5MP, 4K Video, 10fps Burst', 1299.00, 8, 'images/products/1742133441_Canon PowerShot G7X Mark III.png', '2025-03-15 15:15:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (47, 'Canon PowerShot G7X Mark III', 'Compact', '20MP, 4K Video, Livestream Ready', 799.00, 12, 'images/products/1742133433_Canon PowerShot G7X Mark III.png', '2025-03-15 15:15:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (48, 'Nikon Z9', 'Flagship Mirrorless', '45.7MP, 8K Video, 20fps', 5499.00, 4, 'images/products/1742133417_Nikon Coolpix P1000.png', '2025-03-15 15:20:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (49, 'Nikon D7500', 'DSLR', '20.9MP, 4K Video, 8fps Burst', 999.00, 10, 'images/products/default_image.png', '2025-03-15 15:20:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (50, 'Nikon Coolpix P1000', 'Superzoom', '125x Zoom, 4K Video, RAW Support', 999.00, 9, 'images/products/1742133403_Nikon Coolpix P1000.png', '2025-03-15 15:20:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (51, 'Fujifilm X-T5', 'Mirrorless', '40MP, 6.2K Video, Film Simulations', 1699.00, 7, 'images/products/1742133336_Fujifilm Instax Mini 12.png', '2025-03-15 15:25:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (52, 'Fujifilm X100V', 'Compact', '26MP, Fixed Lens, Classic Design', 1499.00, 6, 'images/products/1742133336_Fujifilm Instax Mini 12.png', '2025-03-15 15:25:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (53, 'Fujifilm Instax Mini 12', 'Instant Camera', 'Polaroid-Style Prints, Auto Exposure', 79.00, 20, 'images/products/1742133336_Fujifilm Instax Mini 12.png', '2025-03-15 15:25:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (54, 'Panasonic Lumix GH6', 'Micro Four Thirds', '25MP, 5.7K Video, Dual I.S.', 2199.00, 5, 'images/products/1742133322_Panasonic Lumix GH6.png', '2025-03-15 15:30:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (55, 'Panasonic Lumix S5', 'Full-Frame Mirrorless', '24MP, 4K 60fps, Dual ISO', 1899.00, 6, 'images/products/1742133311_Panasonic Lumix S5.png', '2025-03-15 15:30:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (56, 'GoPro HERO12 Black', 'Action Camera', '5.3K Video, Waterproof, HyperSmooth 6.0', 499.00, 15, 'images/products/1742133292_GoPro HERO12 Black.png', '2025-03-15 15:35:00');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (57, 'GoPro MAX', '360 Camera', '5.6K Video, 360-degree Recording, Waterproof', 549.00, 10, 'images/products/1742133280_GoPro MAX.png', '2025-03-15 15:35:00');
COMMIT;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `address` text,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('customer','admin') DEFAULT 'customer',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of users
-- ----------------------------
BEGIN;
INSERT INTO `users` (`id`, `name`, `email`, `password`, `address`, `phone`, `role`, `created_at`) VALUES (2, 'Admin Daruma', 'admin@daruma.com', '$2y$10$9hNSQdKKvQ72AHFoRjnwvurZo5IZquqrQyjKxECYz27/RWNnRCbe6', NULL, NULL, 'admin', '2025-03-15 17:33:57');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
