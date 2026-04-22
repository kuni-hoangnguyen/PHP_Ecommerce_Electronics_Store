-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.4.3 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for ecommerce_almus
CREATE DATABASE IF NOT EXISTS `ecommerce_almus` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;
USE `ecommerce_almus`;


-- Dumping structure for table ecommerce_almus.categories
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ecommerce_almus.categories: ~2 rows (approximately)
INSERT INTO `categories` (`id`, `name`, `description`, `icon`, `status`) VALUES
	(1, 'Đồng hồ thông minh', NULL, 'bi-smartwatch', 1),
	(2, 'Tai nghe Bluetooth', NULL, 'bi-earbuds', 1);


-- Dumping structure for table ecommerce_almus.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `role` enum('customer','admin') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'customer',
  `is_active` tinyint NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_email` (`email`),
  KEY `idx_users_role` (`role`),
  KEY `idx_users_is_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ecommerce_almus.users: ~2 rows (approximately)
INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `phone`, `address`, `role`, `is_active`, `created_at`) VALUES
	(1, 'Khách hàng 1', 'user1@gmail.com', '$2y$12$ii19aeKfPa/SHtWcmFCtM.0UtgtUaXlQ4VV6FJI7IVI6V3s3R5Hp2', NULL, NULL, 'customer', 1, '2026-03-25 18:42:40'),
	(2, 'Admin', 'admin@gmail.com', '$2y$12$ii19aeKfPa/SHtWcmFCtM.0UtgtUaXlQ4VV6FJI7IVI6V3s3R5Hp2', NULL, NULL, 'admin', 1, '2026-04-01 17:08:06');

-- Dumping structure for table ecommerce_almus.coupons
CREATE TABLE IF NOT EXISTS `coupons` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_type` enum('percent','fixed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percent',
  `discount_value` decimal(15,2) NOT NULL,
  `usage_limit` int unsigned NOT NULL DEFAULT '0',
  `used_count` int unsigned NOT NULL DEFAULT '0',
  `expires_at` datetime DEFAULT NULL,
  `is_active` tinyint NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_coupons_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ecommerce_almus.coupons: ~0 rows (approximately)


-- Dumping structure for table ecommerce_almus.products
CREATE TABLE IF NOT EXISTS `products` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `short_description` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `specifications` json DEFAULT NULL,
  `price` decimal(15,0) NOT NULL,
  `sale_price` decimal(15,0) DEFAULT NULL,
  `stock` int unsigned DEFAULT '0',
  `status` tinyint DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_category_id` (`category_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ecommerce_almus.products: ~27 rows (approximately)
INSERT INTO `products` (`id`, `category_id`, `name`, `brand`, `short_description`, `description`, `specifications`, `price`, `sale_price`, `stock`, `status`, `created_at`) VALUES
	(4, 1, 'Apple Watch Series 8', 'Apple', 'Đồng hồ cao cấp', 'Theo dõi sức khỏe toàn diện', '{"os": "watchOS", "chip": "Apple S8", "model": "Apple Watch Series 8", "screen": "1.9-inch OLED", "battery": "18h", "features": "heart rate, SpO2", "connectivity": "Bluetooth/WiFi/LTE"}', 12000000, 11000000, 30, 1, '2026-03-31 19:14:52'),
	(5, 1, 'Samsung Galaxy Watch 5', 'Samsung', 'Smartwatch Android', 'Thiết kế hiện đại', '{"os": "Wear OS", "chip": "Exynos W920", "model": "Samsung Galaxy Watch 5", "screen": "1.4-inch AMOLED", "battery": "410 mAh", "features": "health tracking", "connectivity": "Bluetooth/WiFi/LTE"}', 8000000, 7500000, 34, 1, '2026-03-31 19:14:52'),
	(6, 1, 'Xiaomi Watch S1', 'Xiaomi', 'Giá rẻ', 'Pin lâu', '{"os": "RTOS", "chip": "-", "model": "Xiaomi Watch S1", "screen": "1.43-inch AMOLED", "battery": "470 mAh", "features": "fitness tracking", "connectivity": "Bluetooth"}', 4000000, 3500000, 45, 1, '2026-03-31 19:14:52'),
	(7, 2, 'AirPods Pro', 'Apple', 'Tai nghe chống ồn', 'ANC tốt', '{"chip": "H1", "type": "earbuds", "model": "AirPods Pro", "battery": "24h", "features": "ANC, spatial audio", "connectivity": "Bluetooth"}', 6000000, 5500000, 69, 1, '2026-03-31 19:14:52'),
	(8, 2, 'Sony WF-1000XM4', 'Sony', 'Chống ồn tốt nhất', 'Âm thanh cao cấp', '{"chip": "Sony V1", "type": "earbuds", "model": "Sony WF-1000XM4", "battery": "24h", "features": "ANC", "connectivity": "Bluetooth"}', 5000000, 4700000, 48, 1, '2026-03-31 19:14:52'),
	(9, 2, 'JBL Tune 230NC', 'JBL', 'Giá tốt', 'Âm bass mạnh', '{"chip": "-", "type": "earbuds", "model": "JBL Tune 230NC", "battery": "40h", "features": "ANC", "connectivity": "Bluetooth"}', 2500000, 2200000, 80, 1, '2026-03-31 19:14:52'),
	(29, 2, 'AirPods 3', 'Apple', 'Tai nghe mới', 'Spatial Audio', '{"chip": "H1", "type": "earbuds", "model": "AirPods 3", "battery": "30h", "features": "spatial audio", "connectivity": "Bluetooth"}', 4500000, 4200000, 80, 1, '2026-03-31 19:14:52'),
	(35, 1, 'Apple Watch SE 2', 'Apple', 'Giá dễ tiếp cận', 'Smartwatch cơ bản của Apple', '{"os": "watchOS", "chip": "S8", "model": "SE 2", "screen": "1.78 OLED", "battery": "18h", "features": "heart rate", "connectivity": "Bluetooth/WiFi"}', 8000000, 7500000, 25, 1, '2026-04-06 17:05:28'),
	(36, 1, 'Samsung Galaxy Watch 6', 'Samsung', 'Đời mới', 'Thiết kế đẹp', '{"os": "Wear OS", "chip": "Exynos W930", "model": "Watch 6", "screen": "1.5 AMOLED", "battery": "425mAh", "features": "health tracking", "connectivity": "Bluetooth/WiFi/LTE"}', 9000000, 8500000, 40, 1, '2026-04-06 17:05:28'),
	(37, 1, 'Huawei Watch GT 4', 'Huawei', 'Pin trâu', 'Thiết kế thể thao', '{"os": "HarmonyOS", "chip": "-", "model": "GT 4", "screen": "1.43 AMOLED", "battery": "14 days", "features": "fitness tracking", "connectivity": "Bluetooth"}', 6000000, 5500000, 50, 1, '2026-04-06 17:05:28'),
	(38, 1, 'Amazfit GTR 4', 'Amazfit', 'Giá tốt', 'Nhiều tính năng', '{"os": "Zepp OS", "chip": "-", "model": "GTR 4", "screen": "1.43 AMOLED", "battery": "14 days", "features": "GPS, health", "connectivity": "Bluetooth"}', 5000000, 4700000, 60, 1, '2026-04-06 17:05:28'),
	(39, 1, 'Garmin Venu 2', 'Garmin', 'Thể thao', 'Chuyên fitness', '{"os": "Garmin OS", "chip": "-", "model": "Venu 2", "screen": "1.3 AMOLED", "battery": "11 days", "features": "sports tracking", "connectivity": "Bluetooth"}', 10000000, 9500000, 20, 1, '2026-04-06 17:05:28'),
	(40, 1, 'Fitbit Sense 2', 'Fitbit', 'Sức khỏe', 'Theo dõi stress', '{"os": "Fitbit OS", "chip": "-", "model": "Sense 2", "screen": "AMOLED", "battery": "6 days", "features": "stress tracking", "connectivity": "Bluetooth"}', 7000000, 6500000, 35, 1, '2026-04-06 17:05:28'),
	(41, 1, 'Xiaomi Watch 2 Pro', 'Xiaomi', 'Giá rẻ', 'Wear OS', '{"os": "Wear OS", "chip": "Snapdragon W5+", "model": "Watch 2 Pro", "screen": "1.43 AMOLED", "battery": "495mAh", "features": "health", "connectivity": "Bluetooth"}', 5500000, 5200000, 45, 1, '2026-04-06 17:05:28'),
	(42, 1, 'Realme Watch 3 Pro', 'Realme', 'Giá mềm', 'Cơ bản', '{"os": "RTOS", "chip": "-", "model": "Watch 3 Pro", "screen": "1.78 AMOLED", "battery": "10 days", "features": "fitness", "connectivity": "Bluetooth"}', 3000000, 2700000, 70, 1, '2026-04-06 17:05:28'),
	(43, 1, 'OPPO Watch X', 'OPPO', 'Thiết kế đẹp', 'Hiện đại', '{"os": "Wear OS", "chip": "Snapdragon", "model": "Watch X", "screen": "AMOLED", "battery": "100h", "features": "health", "connectivity": "Bluetooth"}', 8000000, 7600000, 30, 1, '2026-04-06 17:05:28'),
	(44, 1, 'TicWatch Pro 5', 'Mobvoi', 'Pin trâu', 'Dual display', '{"os": "Wear OS", "chip": "Snapdragon W5+", "model": "Pro 5", "screen": "AMOLED", "battery": "80h", "features": "fitness", "connectivity": "Bluetooth"}', 9000000, 8500000, 22, 1, '2026-04-06 17:05:28'),
	(45, 2, 'AirPods Pro 2', 'Apple', 'ANC xịn', 'Âm thanh tốt', '{"chip": "H2", "type": "earbuds", "model": "AirPods Pro 2", "battery": "30h", "features": "ANC", "connectivity": "Bluetooth"}', 7000000, 6500000, 55, 1, '2026-04-06 17:05:28'),
	(46, 2, 'Sony WF-1000XM5', 'Sony', 'ANC top', 'Âm thanh đỉnh', '{"chip": "Sony V2", "type": "earbuds", "model": "XM5", "battery": "32h", "features": "ANC", "connectivity": "Bluetooth"}', 7000000, 6700000, 40, 1, '2026-04-06 17:05:28'),
	(47, 2, 'Samsung Galaxy Buds 2 Pro', 'Samsung', 'Hi-Fi', 'Chống ồn', '{"chip": "Samsung", "type": "earbuds", "model": "Buds 2 Pro", "battery": "29h", "features": "ANC", "connectivity": "Bluetooth"}', 5000000, 4700000, 60, 1, '2026-04-06 17:05:28'),
	(48, 2, 'Xiaomi Buds 4 Pro', 'Xiaomi', 'Giá tốt', 'Ổn định', '{"chip": "-", "type": "earbuds", "model": "Buds 4 Pro", "battery": "38h", "features": "ANC", "connectivity": "Bluetooth"}', 3500000, 3200000, 80, 1, '2026-04-06 17:05:28'),
	(49, 2, 'JBL Live Pro 2', 'JBL', 'Bass mạnh', 'Trẻ trung', '{"chip": "-", "type": "earbuds", "model": "Live Pro 2", "battery": "40h", "features": "ANC", "connectivity": "Bluetooth"}', 3000000, 2700000, 90, 1, '2026-04-06 17:05:28'),
	(50, 2, 'Anker Soundcore Liberty 4', 'Anker', 'Giá rẻ', 'Ngon bổ rẻ', '{"chip": "-", "type": "earbuds", "model": "Liberty 4", "battery": "28h", "features": "ANC", "connectivity": "Bluetooth"}', 2500000, 2300000, 100, 1, '2026-04-06 17:05:28'),
	(51, 2, 'Beats Studio Buds', 'Beats', 'Phong cách', 'Âm mạnh', '{"chip": "-", "type": "earbuds", "model": "Studio Buds", "battery": "24h", "features": "ANC", "connectivity": "Bluetooth"}', 4000000, 3700000, 65, 1, '2026-04-06 17:05:28'),
	(52, 2, 'Nothing Ear (2)', 'Nothing', 'Thiết kế độc', 'Trong suốt', '{"chip": "-", "type": "earbuds", "model": "Ear 2", "battery": "36h", "features": "ANC", "connectivity": "Bluetooth"}', 4500000, 4200000, 50, 1, '2026-04-06 17:05:28'),
	(53, 2, 'Edifier NeoBuds Pro', 'Edifier', 'Hi-Res', 'Âm tốt', '{"chip": "-", "type": "earbuds", "model": "NeoBuds Pro", "battery": "24h", "features": "ANC", "connectivity": "Bluetooth"}', 3500000, 3200000, 75, 1, '2026-04-06 17:05:28'),
	(54, 2, 'SoundPEATS Air4', 'SoundPEATS', 'Giá rẻ', 'Cơ bản', '{"chip": "-", "type": "earbuds", "model": "Air4", "battery": "26h", "features": "ANC", "connectivity": "Bluetooth"}', 1500000, 1300000, 120, 1, '2026-04-06 17:05:28');


-- Dumping structure for table ecommerce_almus.product_images
CREATE TABLE IF NOT EXISTS `product_images` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int unsigned NOT NULL,
  `image_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_primary` tinyint DEFAULT '0',
  `sort_order` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_product_id` (`product_id`),
  CONSTRAINT `fk_product_images_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ecommerce_almus.product_images: ~27 rows (approximately)
INSERT INTO `product_images` (`id`, `product_id`, `image_path`, `is_primary`, `sort_order`) VALUES
	(1, 29, 'ap3-1.webp', 1, 0),
	(2, 7, 'app-1.webp', 1, 0),
	(5, 4, 'aws8-1.webp', 1, 0),
	(13, 9, 'jbl230nc-1.webp', 1, 0),
	(22, 5, 'sgw5-1.webp', 1, 0),
	(25, 8, 'swfxm4-1.webp', 1, 0),
	(29, 6, 'xmws1-1.webp', 1, 0),
	(44, 45, 'airpods-pro-2-1.webp', 1, 0),
	(45, 38, 'twgtr42-1.webp', 1, 0),
	(46, 50, 'soundcore-liberty-4-1.webp', 1, 0),
	(47, 35, 'apwse2-1.webp', 1, 0),
	(48, 51, 'beats-studio-buds-1.webp', 1, 0),
	(49, 53, 'Edifier-NeoBuds-Pro-2-1.webp', 1, 0),
	(50, 40, 'Fitbit-Sense-2-1.webp', 1, 0),
	(51, 39, 'venu-2-1.webp', 1, 0),
	(52, 37, 'hwwgt4-1.webp', 1, 0),
	(53, 49, 'jbllivepro2-1.webp', 1, 0),
	(54, 52, 'nothing-ear-2-1.webp', 1, 0),
	(55, 43, 'oppo-watch-x-1.webp', 1, 0),
	(56, 42, 'realme-watch-3-pro-1.webp', 1, 0),
	(57, 47, 'samsung-galaxy-buds-2-1.webp', 1, 0),
	(58, 36, 'ssgw6-1.webp', 1, 0),
	(59, 46, 'sony-wf-1000xm5-1.webp', 1, 0),
	(60, 54, 'soundPEATS-air4-1.webp', 1, 0),
	(61, 44, 'ticwatch-pro-5-1.webp', 1, 0),
	(62, 48, 'buds-4-pro-1.webp', 1, 0),
	(63, 41, 'xiaomi-watch-2-pro-1.webp', 1, 0);


-- Dumping structure for table ecommerce_almus.orders
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `total_amount` decimal(15,0) NOT NULL DEFAULT '0',
  `payment_method` enum('cash','bank_transfer') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash',
  `status` enum('pending','confirmed','shipping','completed','canceled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_orders_user` (`user_id`),
  KEY `idx_orders_status` (`status`),
  KEY `idx_orders_created_at` (`created_at`),
  CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ecommerce_almus.orders: ~21 rows (approximately)
INSERT INTO `orders` (`id`, `user_id`, `name`, `email`, `phone`, `address`, `total_amount`, `payment_method`, `status`, `created_at`) VALUES
	(1, 1, 'Khách hàng 1', 'user1@gmail.com', '0123456789', 'VN', 12600000, 'cash', 'canceled', '2026-04-01 22:58:30'),
	(2, 1, 'Khách 2', 'admin@gmail.com', '0123456789', '111', 7500000, 'cash', 'pending', '2026-04-03 23:29:46'),
	(3, 1, 'Khách 3', 'user3@gmail.com', '0900000003', 'HCM', 12000000, 'bank_transfer', 'confirmed', '2026-04-02 23:39:50'),
	(4, 1, 'Khách 4', 'user4@gmail.com', '0900000004', 'HN', 4000000, 'cash', 'shipping', '2026-04-02 23:39:50'),
	(5, 1, 'Khách 5', 'user5@gmail.com', '0900000005', 'DN', 6000000, 'cash', 'completed', '2026-04-01 23:39:50'),
	(6, 1, 'Khách 6', 'user6@gmail.com', '0900000006', 'HCM', 5000000, 'bank_transfer', 'pending', '2026-03-31 23:39:50'),
	(7, 1, 'Khách 7', 'user7@gmail.com', '0900000007', 'HN', 7000000, 'cash', 'confirmed', '2026-04-01 23:39:50'),
	(8, 1, 'Khách 8', 'user8@gmail.com', '0900000008', 'HCM', 9000000, 'cash', 'shipping', '2026-04-02 23:39:50'),
	(9, 1, 'Khách 9', 'user9@gmail.com', '0900000009', 'DN', 4500000, 'bank_transfer', 'completed', '2026-03-29 23:39:50'),
	(10, 1, 'Khách 10', 'user10@gmail.com', '0900000010', 'HCM', 11000000, 'cash', 'pending', '2026-03-26 23:39:50'),
	(11, 1, 'Khách 11', 'user11@gmail.com', '0900000011', 'HN', 3000000, 'cash', 'confirmed', '2026-03-03 23:39:50'),
	(12, 1, 'Khách 12', 'user12@gmail.com', '0900000012', 'HCM', 7500000, 'bank_transfer', 'shipping', '2026-02-03 23:39:50'),
	(13, 1, 'Khách 13', 'user13@gmail.com', '0900000013', 'DN', 6500000, 'cash', 'completed', '2026-01-03 23:39:50'),
	(14, 1, 'Khách 14', 'user14@gmail.com', '0900000014', 'HCM', 5000000, 'cash', 'pending', '2025-12-03 23:39:50'),
	(15, 1, 'Khách 15', 'user15@gmail.com', '0900000015', 'HN', 8000000, 'bank_transfer', 'confirmed', '2025-12-03 23:39:50'),
	(16, 1, 'Khách 16', 'user16@gmail.com', '0900000016', 'HCM', 4200000, 'cash', 'shipping', '2026-02-03 23:39:50'),
	(17, 1, 'Khách 17', 'user17@gmail.com', '0900000017', 'DN', 4700000, 'cash', 'completed', '2026-03-03 23:39:50'),
	(18, 1, 'Khách 18', 'user18@gmail.com', '0900000018', 'HCM', 2200000, 'bank_transfer', 'pending', '2026-02-03 23:39:50'),
	(19, 1, 'Khách 19', 'user19@gmail.com', '0900000019', 'HN', 3500000, 'cash', 'confirmed', '2026-03-03 23:39:50'),
	(20, 1, 'Khách 20', 'user20@gmail.com', '0900000020', 'HCM', 6000000, 'cash', 'shipping', '2026-03-03 23:39:50'),
	(21, 1, 'Khách 21', 'user21@gmail.com', '0900000021', 'DN', 7500000, 'bank_transfer', 'completed', '2026-04-03 23:39:50');


-- Dumping structure for table ecommerce_almus.order_details
CREATE TABLE IF NOT EXISTS `order_details` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned NOT NULL,
  `product_id` int unsigned NOT NULL,
  `quantity` int unsigned NOT NULL,
  `unit_price` decimal(15,0) NOT NULL,
  `sub_total` decimal(15,0) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_order_details_order` (`order_id`),
  KEY `idx_order_details_product` (`product_id`),
  CONSTRAINT `fk_order_details_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_order_details_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `chk_order_details_quantity` CHECK ((`quantity` > 0))
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ecommerce_almus.order_details: ~22 rows (approximately)
INSERT INTO `order_details` (`id`, `order_id`, `product_id`, `quantity`, `unit_price`, `sub_total`, `created_at`) VALUES
	(1, 1, 29, 3, 4200000, 12600000, '2026-04-03 22:58:30'),
	(2, 2, 5, 1, 7500000, 7500000, '2026-04-03 23:29:46'),
	(3, 2, 5, 1, 8000000, 8000000, '2026-04-03 23:39:50'),
	(4, 3, 4, 1, 12000000, 12000000, '2026-04-03 23:39:50'),
	(5, 4, 6, 1, 4000000, 4000000, '2026-04-03 23:39:50'),
	(6, 5, 7, 1, 6000000, 6000000, '2026-04-03 23:39:50'),
	(7, 6, 8, 1, 5000000, 5000000, '2026-04-03 23:39:50'),
	(8, 7, 5, 1, 7000000, 7000000, '2026-04-03 23:39:50'),
	(9, 8, 4, 1, 9000000, 9000000, '2026-04-03 23:39:50'),
	(10, 9, 29, 1, 4500000, 4500000, '2026-04-03 23:39:50'),
	(11, 10, 4, 1, 11000000, 11000000, '2026-04-03 23:39:50'),
	(12, 11, 9, 1, 3000000, 3000000, '2026-04-03 23:39:50'),
	(13, 12, 5, 1, 7500000, 7500000, '2026-04-03 23:39:50'),
	(14, 13, 7, 1, 6500000, 6500000, '2026-04-03 23:39:50'),
	(15, 14, 8, 1, 5000000, 5000000, '2026-04-03 23:39:50'),
	(16, 15, 5, 1, 8000000, 8000000, '2026-04-03 23:39:50'),
	(17, 16, 29, 1, 4200000, 4200000, '2026-04-03 23:39:50'),
	(18, 17, 8, 1, 4700000, 4700000, '2026-04-03 23:39:50'),
	(19, 18, 9, 1, 2200000, 2200000, '2026-04-03 23:39:50'),
	(20, 19, 6, 1, 3500000, 3500000, '2026-04-03 23:39:50'),
	(21, 20, 7, 1, 6000000, 6000000, '2026-04-03 23:39:50'),
	(22, 21, 5, 1, 7500000, 7500000, '2026-04-03 23:39:50');


-- Dumping structure for table ecommerce_almus.cart_items
CREATE TABLE IF NOT EXISTS `cart_items` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `product_id` int unsigned NOT NULL,
  `quantity` int unsigned NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cart_product` (`user_id`,`product_id`) USING BTREE,
  KEY `idx_cart_items_product` (`product_id`),
  CONSTRAINT `fk_cart_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `FK_cart_items_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `chk_cart_items_quantity` CHECK ((`quantity` > 0))
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ecommerce_almus.cart_items: ~0 rows (approximately)


/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;