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

 Date: 21/03/2025 16:22:07
*/

USE daruma_db;

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
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of cart
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for categories
-- ----------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of categories
-- ----------------------------
BEGIN;
INSERT INTO `categories` (`id`, `name`, `created_at`) VALUES (1, 'Phone', '2025-03-18 20:55:20');
INSERT INTO `categories` (`id`, `name`, `created_at`) VALUES (2, 'Laptop', '2025-03-18 20:55:20');
INSERT INTO `categories` (`id`, `name`, `created_at`) VALUES (3, 'Camera', '2025-03-18 20:55:20');
INSERT INTO `categories` (`id`, `name`, `created_at`) VALUES (4, 'Game Consoles', '2025-03-18 20:55:20');
INSERT INTO `categories` (`id`, `name`, `created_at`) VALUES (5, 'Smart Watch', '2025-03-18 20:55:20');
INSERT INTO `categories` (`id`, `name`, `created_at`) VALUES (6, 'Tablet', '2025-03-18 20:57:01');
INSERT INTO `categories` (`id`, `name`, `created_at`) VALUES (7, 'Accessories', '2025-03-18 20:58:24');
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
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of order_items
-- ----------------------------
BEGIN;
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`, `name`) VALUES (34, 12, 20, 8, 1899.00, 'Samsung Galaxy Z Fold 6');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`, `name`) VALUES (35, 12, 15, 9, 129.00, 'Apple Pencil 3');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`, `name`) VALUES (36, 12, 19, 2, 1099.00, 'Samsung Galaxy S25');
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`, `name`) VALUES (37, 13, 15, 6, 129.00, 'Apple Pencil 3');
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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of orders
-- ----------------------------
BEGIN;
INSERT INTO `orders` (`id`, `user_id`, `total_price`, `status`, `created_at`, `shipping_address`, `phone_number`, `cc_number`, `received_name`) VALUES (11, 2, 122811.00, 'pending', '2025-03-20 21:32:20', '36 18b St - Al Safa - Al Safa 2 - Dubai - United Arab Emirates', '339298888333', '123123123123', 'Bon Jovi');
INSERT INTO `orders` (`id`, `user_id`, `total_price`, `status`, `created_at`, `shipping_address`, `phone_number`, `cc_number`, `received_name`) VALUES (12, 6, 18551.00, 'pending', '2025-03-21 16:20:20', 'Jl. Soekarno Hatta No.378, Kb. Lega, Kec. Bojongloa Kidul, Kota Bandung, Jawa Barat 40235', '088220784828', 'sdasds', 'Robby Izhar Ramadhana');
INSERT INTO `orders` (`id`, `user_id`, `total_price`, `status`, `created_at`, `shipping_address`, `phone_number`, `cc_number`, `received_name`) VALUES (13, 6, 774.00, 'pending', '2025-03-21 16:21:13', 'Jl. Gatot Subroto VIK No 11 A Kec Dauh Puri Kaja Denpasar', '088220784828', 'sdasds', 'Tunjangan Transport');
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
  `category_id` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of products
-- ----------------------------
BEGIN;
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`, `category_id`) VALUES (1, 'iPhone 16 Pro Max', 'Black Titanium', '512GB Storage, 5G, A18 Pro Chip, 6.7-inch OLED', 1799.00, 15, 'images/products/1742483391_iPhone_16_Pro_Max.png', '2025-03-05 10:45:32', 1);
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`, `category_id`) VALUES (2, 'iPhone 16 Pro', 'White Titanium', '256GB Storage, 5G, A18 Pro Chip, 6.1-inch OLED', 1599.00, 20, 'images/products/1742483422_iPhone 16 Pro.png', '2025-03-08 14:32:18', 1);
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`, `category_id`) VALUES (3, 'iPhone 16', 'Midnight', '128GB Storage, 5G, A17 Chip, 6.1-inch OLED', 1199.00, 25, 'images/products/1742483442_iPhone 16.png', '2025-03-12 09:21:05', 1);
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`, `category_id`) VALUES (4, 'iPhone SE 3', 'Product Red', '128GB Storage, 4.7-inch Retina, A15 Bionic', 429.00, 30, 'images/products/1742483453_iPhone SE 3.png', '2025-03-18 17:11:42', 1);
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`, `category_id`) VALUES (5, 'MacBook Air M3', 'Space Gray', '13.6-inch Liquid Retina, M3 Chip, 16GB RAM, 512GB SSD', 1499.00, 10, 'images/products/1742483464_MacBook Air M3.png', '2025-03-07 11:53:20', 2);
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`, `category_id`) VALUES (6, 'MacBook Pro 14 M3 Pro', 'Silver', '14-inch Liquid Retina XDR, M3 Pro Chip, 32GB RAM, 1TB SSD', 2399.00, 8, 'images/products/1742483476_MacBook Pro 14 M3 Pro.png', '2025-03-14 22:08:37', 2);
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`, `category_id`) VALUES (7, 'MacBook Pro 16 M3 Max', 'Space Black', '16-inch Liquid Retina XDR, M3 Max Chip, 64GB RAM, 2TB SSD', 3499.00, 5, 'images/products/1742483488_MacBook Pro 16 M3 Max.png', '2025-03-09 15:30:51', 2);
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`, `category_id`) VALUES (8, 'iPad Pro 13 M2', 'Space Gray', '13-inch Liquid Retina XDR, M2 Chip, 512GB Storage', 1299.00, 12, 'images/products/1742483500_iPad Pro 13 M2.png', '2025-03-11 18:25:19', 6);
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`, `category_id`) VALUES (9, 'iPad Air 6', 'Blue', '11-inch Liquid Retina, M2 Chip, 256GB Storage', 899.00, 20, 'images/products/1742483511_iPad Air 6.png', '2025-03-06 13:45:55', 6);
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`, `category_id`) VALUES (10, 'iPad 10th Gen', 'Silver', '10.9-inch Liquid Retina, A14 Bionic, 128GB Storage', 599.00, 25, 'images/products/1742483521_iPad 10th Gen.png', '2025-03-16 19:40:28', 6);
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`, `category_id`) VALUES (11, 'Apple Watch Ultra 2', 'Titanium', '49mm Always-On Retina, Dual-Frequency GPS, 36hr Battery Life', 799.00, 15, 'images/products/1742483534_Apple Watch Ultra 2.png', '2025-03-02 07:52:34', 5);
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`, `category_id`) VALUES (12, 'Apple Watch Series 9', 'Midnight Aluminum', '45mm Always-On Retina, S9 Chip, Blood Oxygen & ECG', 499.00, 20, 'images/products/1742483546_Apple Watch Series 9.png', '2025-03-04 16:22:09', 5);
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`, `category_id`) VALUES (13, 'AirPods Pro 2', 'White', 'Active Noise Cancellation, Spatial Audio, MagSafe Charging', 249.00, 30, 'images/products/1742483558_AirPods Pro 2.png', '2025-03-13 08:19:11', 7);
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`, `category_id`) VALUES (14, 'AirPods Max', 'Sky Blue', 'Over-Ear, Active Noise Cancellation, Spatial Audio', 549.00, 10, 'images/products/1742483571_AirPods Max.png', '2025-03-10 12:33:26', 7);
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`, `category_id`) VALUES (15, 'Apple Pencil 3', 'White', 'Magnetic Charging, Hover Function, USB-C', 129.00, 35, 'images/products/1742483583_Apple Pencil 3.png', '2025-03-17 21:09:44', 7);
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`, `category_id`) VALUES (16, 'Magic Keyboard for iPad Pro 13', 'Black', 'Backlit Keys, Trackpad, Floating Design', 349.00, 20, 'images/products/1742483595_Magic Keyboard for iPad Pro 13.png', '2025-03-01 05:47:16', 7);
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`, `category_id`) VALUES (17, 'Samsung Galaxy S25 Ultra', 'Black Titanium', '512GB Storage, 5G, Exynos 2500, 6.8-inch Dynamic AMOLED', 1499.00, 10, 'images/products/1742484656_Samsung Galaxy S25 Ultra.png', '2025-03-03 10:20:30', 1);
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`, `category_id`) VALUES (18, 'Samsung Galaxy S25+', 'Phantom Silver', '256GB Storage, 5G, Snapdragon 8 Gen 4, 6.7-inch Dynamic AMOLED', 1299.00, 15, 'images/products/1742484613_Samsung Galaxy S25+.png', '2025-03-09 12:15:42', 1);
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`, `category_id`) VALUES (19, 'Samsung Galaxy S25', 'Phantom Black', '128GB Storage, 5G, Snapdragon 8 Gen 4, 6.2-inch Dynamic AMOLED', 1099.00, 18, 'images/products/1742484596_Samsung Galaxy S25.png', '2025-03-12 14:45:37', 1);
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`, `category_id`) VALUES (20, 'Samsung Galaxy Z Fold 6', 'Mystic Bronze', '512GB Storage, 5G, Snapdragon 8 Gen 4, 7.6-inch Foldable AMOLED', 1899.00, 0, 'images/products/1742484584_Samsung Galaxy Z Fold 6.png', '2025-03-15 17:05:22', 1);
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`, `category_id`) VALUES (21, 'Samsung Galaxy Z Flip 6', 'Lavender', '256GB Storage, 5G, Snapdragon 8 Gen 4, 6.7-inch Foldable AMOLED', 1099.00, 12, 'images/products/1742484572_Samsung Galaxy Z Flip 6.png', '2025-03-18 20:30:19', 1);
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`, `category_id`) VALUES (59, 'PlayStation 5', 'Standard Edition', '825GB SSD, Ray Tracing, 4K Gaming, DualSense Controller', 499.00, 15, 'images/products/1742526689_PlayStation 5.jpg', '2025-03-06 14:22:30', 4);
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`, `category_id`) VALUES (60, 'PlayStation 5 Digital', 'Digital Edition', '825GB SSD, Ray Tracing, 4K Gaming, No Disc Drive', 399.00, 20, 'images/products/1742526677_PlayStation 5 Digital.png', '2025-03-09 11:15:42', 4);
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`, `category_id`) VALUES (61, 'Xbox Series X', 'Black', '1TB SSD, 4K Gaming, 120 FPS, Game Pass Support', 499.00, 10, 'images/products/1742526645_xbox series x.png', '2025-03-12 10:30:37', 4);
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`, `category_id`) VALUES (62, 'Xbox Series S', 'White', '512GB SSD, 1440p Gaming, 120 FPS, Game Pass Support', 299.00, 25, 'images/products/1742526661_Xbox Series s.png', '2025-03-15 15:45:22', 4);
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`, `category_id`) VALUES (63, 'Nintendo Switch OLED', 'White', '7-inch OLED Screen, Handheld & Docked Mode, Joy-Con Controllers', 349.00, 18, 'images/products/1742526522_Nintendo Switch OLED.png', '2025-03-18 18:30:19', 4);
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`, `category_id`) VALUES (64, 'Produck Testing', 'Black Titanium XXX', 'text', 900000.00, 0, 'images/products/1742528013_vas-pitcher-vintage-dengan-buah-pada-latar-belakang-gelap.jpg', '2025-03-21 11:33:33', 6);
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of users
-- ----------------------------
BEGIN;
INSERT INTO `users` (`id`, `name`, `email`, `password`, `address`, `phone`, `role`, `created_at`) VALUES (2, 'Admin Daruma', 'admin@daruma.com', '$2y$10$9hNSQdKKvQ72AHFoRjnwvurZo5IZquqrQyjKxECYz27/RWNnRCbe6', NULL, NULL, 'admin', '2025-03-15 18:33:57');
INSERT INTO `users` (`id`, `name`, `email`, `password`, `address`, `phone`, `role`, `created_at`) VALUES (6, 'Robby Ramadhana', 'izharramadhana@gmail.com', '$2y$10$bOduuhyA7YXiWVQOy5Mq8uXJ6e.e3f71USJoVbFoa.l/gYRFIJf3u', NULL, NULL, 'customer', '2025-03-20 21:57:23');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
