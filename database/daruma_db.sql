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

 Date: 15/03/2025 18:43:04
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of cart
-- ----------------------------
BEGIN;
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
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of products
-- ----------------------------
BEGIN;
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (1, 'iPhone 16 Pro', 'White Titanium', '256GB Storage, 5G', 1599.00, 10, 'images/products/iphone_16_pro.png', '2025-03-15 13:58:47');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (2, 'iPhone 16', 'Teal', '128GB Storage, 5G', 1299.00, 15, 'images/products/iphone_16.png', '2025-03-15 13:58:47');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (3, 'iPhone 15 Pro', 'Natural Titanium', '256GB Storage, 5G', 1399.00, 8, 'images/products/iphone_15.png', '2025-03-15 13:58:47');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (4, 'iPhone 16', 'Pink', '128GB Storage, 5G', 1299.00, 12, 'images/products/iphone_16.png', '2025-03-15 13:58:47');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (5, 'MacBook Pro 14\"', 'M3 Chip', '16GB RAM, 512GB SSD', 1999.00, 5, 'images/products/macbookpro14.png', '2025-03-15 13:58:47');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (6, 'MacBook Air 13\"', 'M2 Chip', '8GB RAM, 256GB SSD', 1099.00, 7, 'images/products/macbookair13.png', '2025-03-15 13:58:47');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (7, 'iPad Pro 12.9\"', 'M2 Chip', '256GB Storage, Wi-Fi', 1299.00, 9, 'images/products/ipadpro12.png', '2025-03-15 13:58:47');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (8, 'Apple Watch Ultra', 'Titanium Case', 'GPS + Cellular', 799.00, 20, 'images/products/applewatchultra.png', '2025-03-15 13:58:47');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (9, 'AirPods Pro 2', 'MagSafe', 'Active Noise Cancellation', 249.00, 30, 'images/products/airpodspro2.png', '2025-03-15 13:58:47');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (10, 'Magic Keyboard', 'Wireless', 'Rechargeable, Backlit Keys', 99.00, 25, 'images/products/magickeyboard.png', '2025-03-15 13:58:47');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (11, 'iPhone 16 Pro', 'Black Titanium', '512GB Storage, 5G', 1799.00, 8, 'images/products/iphone_16_pro.png', '2025-03-15 13:58:47');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (12, 'iPhone 16', 'Blue', '256GB Storage, 5G', 1399.00, 14, 'images/products/iphone_16.png', '2025-03-15 13:58:47');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (13, 'iPhone 15 Pro', 'Blue Titanium', '512GB Storage, 5G', 1499.00, 6, 'images/products/iphone_15.png', '2025-03-15 13:58:47');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (14, 'iPhone 16', 'Green', '128GB Storage, 5G', 1299.00, 10, 'images/products/iphone_16.png', '2025-03-15 13:58:47');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (15, 'MacBook Pro 16\"', 'M3 Max Chip', '32GB RAM, 1TB SSD', 2999.00, 3, 'images/products/macbookpro14.png', '2025-03-15 13:58:47');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (16, 'MacBook Air 15\"', 'M2 Chip', '16GB RAM, 512GB SSD', 1299.00, 5, 'images/products/macbookair13.png', '2025-03-15 13:58:47');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (17, 'MacBook Pro 14\"', 'M3 Pro Chip', '32GB RAM, 512GB SSD', 2499.00, 4, 'images/products/macbookpro14.png', '2025-03-15 13:58:47');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (18, 'MacBook Air 13\"', 'M3 Chip', '8GB RAM, 256GB SSD', 1199.00, 7, 'images/products/macbookair13.png', '2025-03-15 13:58:47');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (19, 'iPad Pro 12.9\"', 'M3 Chip', '512GB Storage, Wi-Fi + Cellular', 1599.00, 6, 'images/products/ipadpro12.png', '2025-03-15 13:58:47');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (20, 'iPad Air 11\"', 'M2 Chip', '128GB Storage, Wi-Fi', 799.00, 10, 'images/products/ipadpro12.png', '2025-03-15 13:58:47');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (21, 'iPad Mini 8.3\"', 'A15 Chip', '256GB Storage, Wi-Fi + Cellular', 999.00, 8, 'images/products/ipadpro12.png', '2025-03-15 13:58:47');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (22, 'iPad 10.9\"', 'A14 Chip', '64GB Storage, Wi-Fi', 499.00, 12, 'images/products/ipadpro12.png', '2025-03-15 13:58:47');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (23, 'Apple Watch Ultra 2', 'Titanium Case', 'GPS + Cellular, New S9 Chip', 899.00, 18, 'images/products/applewatchultra.png', '2025-03-15 13:58:47');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (24, 'Apple Watch Series 9', 'Stainless Steel Case', 'GPS + Cellular', 599.00, 20, 'images/products/applewatchultra.png', '2025-03-15 13:58:47');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (25, 'Apple Watch SE', 'Aluminum Case', 'GPS Only', 279.00, 22, 'images/products/applewatchultra.png', '2025-03-15 13:58:47');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (26, 'Apple Watch Nike', 'Nike Sport Band', 'GPS + Cellular', 699.00, 15, 'images/products/applewatchultra.png', '2025-03-15 13:58:47');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (27, 'iPhone 16 Pro', 'Graphite Titanium', '1TB Storage, 5G', 1999.00, 5, 'images/products/iphone_16_pro.png', '2025-03-15 13:58:47');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (28, 'iPhone 16', 'Yellow', '256GB Storage, 5G', 1399.00, 12, 'images/products/iphone_16.png', '2025-03-15 13:58:47');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (29, 'iPhone 15 Pro Max', 'Silver Titanium', '512GB Storage, 5G', 1699.00, 7, 'images/products/iphone_15.png', '2025-03-15 13:58:47');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (30, 'iPhone 16', 'Purple', '512GB Storage, 5G', 1499.00, 8, 'images/products/iphone_16.png', '2025-03-15 13:58:47');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (31, 'iPhone 15', 'Black', '128GB Storage, 5G', 1199.00, 15, 'images/products/iphone_15.png', '2025-03-15 13:58:47');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (32, 'MacBook Pro 16\"', 'M3 Ultra Chip', '64GB RAM, 2TB SSD', 3999.00, 2, 'images/products/macbookpro14.png', '2025-03-15 13:58:47');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (33, 'MacBook Air 13\"', 'M3 Chip', '16GB RAM, 512GB SSD', 1399.00, 6, 'images/products/macbookair13.png', '2025-03-15 13:58:47');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (34, 'MacBook Pro 14\"', 'M3 Pro Chip', '16GB RAM, 1TB SSD', 2699.00, 5, 'images/products/macbookpro14.png', '2025-03-15 13:58:47');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (35, 'MacBook Air 15\"', 'M2 Chip', '8GB RAM, 256GB SSD', 1199.00, 10, 'images/products/macbookair13.png', '2025-03-15 13:58:47');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (36, 'MacBook Pro 14\"', 'M2 Max Chip', '32GB RAM, 1TB SSD', 3199.00, 3, 'images/products/macbookpro14.png', '2025-03-15 13:58:47');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (37, 'iPad Pro 12.9\"', 'M3 Chip', '1TB Storage, Wi-Fi + Cellular', 1899.00, 4, 'images/products/ipadpro12.png', '2025-03-15 13:58:47');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (38, 'iPad Air 11\"', 'M2 Chip', '256GB Storage, Wi-Fi + Cellular', 999.00, 6, 'images/products/ipadpro12.png', '2025-03-15 13:58:47');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (39, 'iPad Mini 8.3\"', 'A16 Chip', '128GB Storage, Wi-Fi', 749.00, 10, 'images/products/ipadpro12.png', '2025-03-15 13:58:47');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (40, 'iPad 10.9\"', 'A15 Chip', '256GB Storage, Wi-Fi', 599.00, 12, 'images/products/ipadpro12.png', '2025-03-15 13:58:47');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (41, 'iPad Pro 11\"', 'M2 Chip', '512GB Storage, Wi-Fi + Cellular', 1499.00, 5, 'images/products/ipadpro12.png', '2025-03-15 13:58:47');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (42, 'Apple Watch Ultra 2', 'Carbon Titanium', 'GPS + Cellular, New S9 Chip', 999.00, 8, 'images/products/applewatchultra.png', '2025-03-15 13:58:47');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (43, 'Apple Watch Series 9', 'Aluminum Case', 'GPS Only', 399.00, 12, 'images/products/applewatchultra.png', '2025-03-15 13:58:47');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (44, 'Apple Watch SE', 'Silver Case', 'GPS + Cellular', 349.00, 14, 'images/products/applewatchultra.png', '2025-03-15 13:58:47');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (45, 'Apple Watch Nike', 'Nike Sport Loop', 'GPS Only', 499.00, 10, 'images/products/applewatchultra.png', '2025-03-15 13:58:47');
INSERT INTO `products` (`id`, `name`, `edition`, `description`, `price`, `stock`, `image`, `created_at`) VALUES (46, 'Apple Watch Hermès', 'Leather Band', 'GPS + Cellular', 1499.00, 5, 'images/products/applewatchultra.png', '2025-03-15 13:58:47');
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Records of users
-- ----------------------------
BEGIN;
INSERT INTO `users` (`id`, `name`, `email`, `password`, `address`, `phone`, `role`, `created_at`) VALUES (1, 'Robby Ramadhana', 'izharramadhana@gmail.com', '$2y$10$nEXIkepPKTFV/AoFpihFYe9jjriHiCedOsC8UfPxa6Y4VdooBH56a', NULL, NULL, 'customer', '2025-03-15 16:54:12');
INSERT INTO `users` (`id`, `name`, `email`, `password`, `address`, `phone`, `role`, `created_at`) VALUES (2, 'Admin Daruma', 'admin@daruma.com', '$2y$10$9hNSQdKKvQ72AHFoRjnwvurZo5IZquqrQyjKxECYz27/RWNnRCbe6', NULL, NULL, 'admin', '2025-03-15 17:33:57');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
