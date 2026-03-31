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
CREATE DATABASE IF NOT EXISTS `ecommerce_almus` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `ecommerce_almus`;

-- Dumping structure for table ecommerce_almus.categories
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ecommerce_almus.categories: ~9 rows (approximately)
INSERT INTO `categories` (`id`, `name`, `description`, `icon`, `status`) VALUES
	(1, 'Điện thoại', NULL, 'bi-phone', 1),
	(2, 'Đồng hồ thông minh', NULL, 'bi-smartwatch', 1),
	(3, 'Tai nghe Bluetooth', NULL, 'bi-earbuds', 1),
	(4, 'Sạc dự phòng', NULL, 'bi-battery-charging', 1),
	(5, 'Loa', NULL, 'bi-speaker', 1),
	(6, 'Chuột máy tính', NULL, 'bi-mouse', 1),
	(7, 'Bàn phím', NULL, 'bi-keyboard', 1),
	(8, 'Máy ảnh', NULL, 'bi-camera', 1),
	(9, 'Camera hành trình', NULL, 'bi-camera-video', 1);

-- Dumping structure for table ecommerce_almus.products
CREATE TABLE IF NOT EXISTS `products` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `short_description` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
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
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ecommerce_almus.products: ~30 rows (approximately)
INSERT INTO `products` (`id`, `category_id`, `name`, `brand`, `short_description`, `description`, `specifications`, `price`, `sale_price`, `stock`, `status`, `created_at`) VALUES
	(1, 1, 'iPhone 13 128GB', 'Apple', 'iPhone 13 chính hãng', 'Hiệu năng mạnh mẽ với chip A15', '{"os": "iOS", "ram": "4GB", "chip": "A15 Bionic", "model": "iPhone 13", "screen": "6.1-inch OLED", "battery": "3240 mAh", "network": "5G", "storage": "128GB", "camera sau": "12MP dual", "camera trước": "12MP"}', 18000000, 16500000, 50, 1, '2026-03-31 19:14:52'),
	(2, 1, 'Samsung Galaxy S22', 'Samsung', 'Flagship Samsung', 'Màn hình AMOLED 120Hz', '{"os": "Android", "ram": "8GB", "chip": "Snapdragon 8 Gen 1", "model": "Samsung Galaxy S22", "screen": "6.1-inch AMOLED", "battery": "3700 mAh", "network": "5G", "storage": "128GB", "camera sau": "50MP + 12MP + 10MP", "camera trước": "10MP"}', 17000000, 15000000, 40, 1, '2026-03-31 19:14:52'),
	(3, 1, 'Xiaomi 13 Lite', 'Xiaomi', 'Giá tốt hiệu năng cao', 'Snapdragon 7 Gen', '{"os": "Android", "ram": "8GB", "chip": "Snapdragon 7 Gen 1", "model": "Xiaomi 13 Lite", "screen": "6.55-inch AMOLED", "battery": "4500 mAh", "network": "5G", "storage": "128GB", "camera sau": "50MP + 8MP + 2MP", "camera trước": "32MP"}', 9000000, 8500000, 60, 1, '2026-03-31 19:14:52'),
	(4, 2, 'Apple Watch Series 8', 'Apple', 'Đồng hồ cao cấp', 'Theo dõi sức khỏe toàn diện', '{"os": "watchOS", "chip": "Apple S8", "model": "Apple Watch Series 8", "screen": "1.9-inch OLED", "battery": "18h", "features": "heart rate, SpO2", "connectivity": "Bluetooth/WiFi/LTE"}', 12000000, 11000000, 30, 1, '2026-03-31 19:14:52'),
	(5, 2, 'Samsung Galaxy Watch 5', 'Samsung', 'Smartwatch Android', 'Thiết kế hiện đại', '{"os": "Wear OS", "chip": "Exynos W920", "model": "Samsung Galaxy Watch 5", "screen": "1.4-inch AMOLED", "battery": "410 mAh", "features": "health tracking", "connectivity": "Bluetooth/WiFi/LTE"}', 8000000, 7500000, 35, 1, '2026-03-31 19:14:52'),
	(6, 2, 'Xiaomi Watch S1', 'Xiaomi', 'Giá rẻ', 'Pin lâu', '{"os": "RTOS", "chip": "-", "model": "Xiaomi Watch S1", "screen": "1.43-inch AMOLED", "battery": "470 mAh", "features": "fitness tracking", "connectivity": "Bluetooth"}', 4000000, 3500000, 45, 1, '2026-03-31 19:14:52'),
	(7, 3, 'AirPods Pro', 'Apple', 'Tai nghe chống ồn', 'ANC tốt', '{"chip": "H1", "type": "earbuds", "model": "AirPods Pro", "battery": "24h", "features": "ANC, spatial audio", "connectivity": "Bluetooth"}', 6000000, 5500000, 70, 1, '2026-03-31 19:14:52'),
	(8, 3, 'Sony WF-1000XM4', 'Sony', 'Chống ồn tốt nhất', 'Âm thanh cao cấp', '{"chip": "Sony V1", "type": "earbuds", "model": "Sony WF-1000XM4", "battery": "24h", "features": "ANC", "connectivity": "Bluetooth"}', 5000000, 4700000, 50, 1, '2026-03-31 19:14:52'),
	(9, 3, 'JBL Tune 230NC', 'JBL', 'Giá tốt', 'Âm bass mạnh', '{"chip": "-", "type": "earbuds", "model": "JBL Tune 230NC", "battery": "40h", "features": "ANC", "connectivity": "Bluetooth"}', 2500000, 2200000, 80, 1, '2026-03-31 19:14:52'),
	(10, 4, 'Anker 20000mAh', 'Anker', 'Dung lượng lớn', 'Sạc nhanh', '{"type": "powerbank", "model": "Anker 20000mAh", "ports": "USB-A/USB-C", "output": "fast charge", "capacity": "20000 mAh", "features": "multi device"}', 1200000, 1000000, 100, 1, '2026-03-31 19:14:52'),
	(11, 4, 'Xiaomi Power Bank 10000', 'Xiaomi', 'Nhỏ gọn', 'Sạc nhanh', '{"type": "powerbank", "model": "Xiaomi Power Bank 10000", "ports": "USB-A/USB-C", "output": "10W", "capacity": "10000 mAh", "features": "safe charging"}', 500000, 450000, 120, 1, '2026-03-31 19:14:52'),
	(12, 4, 'Baseus 30000mAh', 'Baseus', 'Siêu lớn', '3 cổng sạc', '{"type": "powerbank", "model": "Baseus 30000mAh", "ports": "USB-A/USB-C", "output": "fast charge", "capacity": "30000 mAh", "features": "high capacity"}', 900000, 850000, 60, 1, '2026-03-31 19:14:52'),
	(13, 5, 'JBL Charge 5', 'JBL', 'Loa bluetooth', 'Chống nước', '{"type": "speaker", "model": "JBL Charge 5", "battery": "7500 mAh", "features": "waterproof, bass", "connectivity": "Bluetooth"}', 4000000, 3800000, 40, 1, '2026-03-31 19:14:52'),
	(14, 5, 'Sony SRS-XB33', 'Sony', 'Bass mạnh', 'Party speaker', '{"type": "speaker", "model": "Sony SRS-XB33", "battery": "4900 mAh", "features": "extra bass, waterproof", "connectivity": "Bluetooth"}', 3500000, 3300000, 35, 1, '2026-03-31 19:14:52'),
	(15, 5, 'Xiaomi Speaker', 'Xiaomi', 'Giá rẻ', 'Âm ổn', '{"type": "speaker", "model": "Xiaomi Speaker", "battery": "-", "features": "portable", "connectivity": "Bluetooth"}', 800000, 700000, 90, 1, '2026-03-31 19:14:52'),
	(16, 6, 'Logitech G102', 'Logitech', 'Chuột gaming', 'RGB đẹp', '{"dpi": "8000", "type": "mouse", "model": "Logitech G102", "features": "gaming", "connection": "Wired USB"}', 400000, 350000, 150, 1, '2026-03-31 19:14:52'),
	(17, 6, 'Razer DeathAdder', 'Razer', 'Gaming cao cấp', 'Cảm biến tốt', '{"dpi": "16000", "type": "mouse", "model": "Razer DeathAdder", "features": "ergonomic gaming", "connection": "Wired USB"}', 900000, 850000, 100, 1, '2026-03-31 19:14:52'),
	(18, 6, 'Rapoo M100', 'Rapoo', 'Chuột văn phòng', 'Không dây', '{"dpi": "1600", "type": "mouse", "model": "Rapoo M100", "features": "wireless", "connection": "Bluetooth"}', 200000, 180000, 200, 1, '2026-03-31 19:14:52'),
	(19, 7, 'Keychron K2', 'Keychron', 'Cơ không dây', 'Layout 75%', '{"type": "keyboard", "model": "Keychron K2", "switch": "mechanical", "features": "RGB", "connection": "Bluetooth/Wired"}', 2000000, 1800000, 60, 1, '2026-03-31 19:14:52'),
	(20, 7, 'AKKO 3084', 'AKKO', 'Mechanical', 'Thiết kế đẹp', '{"type": "keyboard", "model": "AKKO 3084", "switch": "mechanical", "features": "compact", "connection": "Wired"}', 1500000, 1400000, 70, 1, '2026-03-31 19:14:52'),
	(21, 7, 'Logitech K120', 'Logitech', 'Giá rẻ', 'Bền', '{"type": "keyboard", "model": "Logitech K120", "switch": "membrane", "features": "basic", "connection": "Wired"}', 200000, 180000, 150, 1, '2026-03-31 19:14:52'),
	(22, 8, 'Canon EOS M50', 'Canon', 'Máy ảnh mirrorless', 'Quay 4K', '{"lens": "interchangeable", "type": "camera", "model": "Canon EOS M50", "video": "4K", "sensor": "24MP APS-C", "features": "mirrorless"}', 15000000, 14000000, 20, 1, '2026-03-31 19:14:52'),
	(23, 8, 'Sony A6400', 'Sony', 'Chụp nhanh', 'AF tốt', '{"lens": "interchangeable", "type": "camera", "model": "Sony A6400", "video": "4K", "sensor": "24MP APS-C", "features": "mirrorless"}', 20000000, 18500000, 15, 1, '2026-03-31 19:14:52'),
	(24, 8, 'Nikon D3500', 'Nikon', 'DSLR cơ bản', 'Dễ dùng', '{"lens": "interchangeable", "type": "camera", "model": "Nikon D3500", "video": "1080p", "sensor": "24MP APS-C", "features": "DSLR"}', 12000000, 11000000, 25, 1, '2026-03-31 19:14:52'),
	(25, 9, 'GoPro Hero 11', 'GoPro', 'Action cam', 'Chống rung', '{"type": "action_cam", "model": "GoPro Hero 11", "video": "5.3K", "battery": "1720 mAh", "features": "stabilization", "waterproof": "10m"}', 13000000, 12000000, 30, 1, '2026-03-31 19:14:52'),
	(26, 9, 'DJI Action 3', 'DJI', 'Quay mượt', 'Chống nước', '{"type": "action_cam", "model": "DJI Action 3", "video": "4K", "battery": "1770 mAh", "features": "stabilization", "waterproof": "16m"}', 9000000, 8500000, 35, 1, '2026-03-31 19:14:52'),
	(27, 9, 'Xiaomi Yi Cam', 'Xiaomi', 'Giá rẻ', 'Nhỏ gọn', '{"type": "action_cam", "model": "Xiaomi Yi Cam", "video": "4K", "battery": "1400 mAh", "features": "budget", "waterproof": "-"}', 2000000, 1800000, 60, 1, '2026-03-31 19:14:52'),
	(28, 1, 'iPhone 14 Pro', 'Apple', 'Dynamic Island', 'Camera 48MP', '{"os": "iOS", "ram": "6GB", "chip": "A16 Bionic", "model": "iPhone 14 Pro", "screen": "6.1-inch OLED", "battery": "3200 mAh", "network": "5G", "storage": "128GB", "camera sau": "48MP + 12MP + 12MP", "camera trước": "12MP"}', 25000000, 23000000, 25, 1, '2026-03-31 19:14:52'),
	(29, 3, 'AirPods 3', 'Apple', 'Tai nghe mới', 'Spatial Audio', '{"chip": "H1", "type": "earbuds", "model": "AirPods 3", "battery": "30h", "features": "spatial audio", "connectivity": "Bluetooth"}', 4500000, 4200000, 80, 1, '2026-03-31 19:14:52'),
	(30, 6, 'Logitech MX Master 3', 'Logitech', 'Chuột cao cấp', 'Dùng văn phòng', '{"dpi": "4000", "type": "mouse", "model": "Logitech MX Master 3", "features": "premium office", "connection": "Bluetooth/USB"}', 2500000, 2300000, 40, 1, '2026-03-31 19:14:52');

-- Dumping structure for table ecommerce_almus.product_images
CREATE TABLE IF NOT EXISTS `product_images` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int unsigned NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_primary` tinyint DEFAULT '0',
  `sort_order` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_product_id` (`product_id`),
  CONSTRAINT `fk_product_images_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ecommerce_almus.product_images: ~34 rows (approximately)
INSERT INTO `product_images` (`id`, `product_id`, `image_path`, `is_primary`, `sort_order`) VALUES
	(1, 29, 'ap3-1.webp', 1, 0),
	(2, 7, 'app-1.webp', 1, 0),
	(3, 20, 'ak3084-1.webp', 1, 0),
	(4, 10, 'ak20k-1.webp', 1, 0),
	(5, 4, 'aws8-1.webp', 1, 0),
	(6, 12, 'bs30k-1.webp', 1, 0),
	(7, 22, 'cnm50-1.webp', 1, 0),
	(8, 26, 'djiaction3-1.webp', 1, 0),
	(9, 25, 'grhero11-1.webp', 1, 0),
	(10, 1, 'ip13-1.webp', 1, 0),
	(11, 28, 'ip14pro-1.webp', 1, 0),
	(12, 13, 'jblc5-1.webp', 1, 0),
	(13, 9, 'jbl230nc-1.webp', 1, 0),
	(14, 19, 'kck2-1.webp', 1, 0),
	(15, 16, 'ltg102-1.webp', 1, 0),
	(16, 21, 'ltk120-1.webp', 1, 0),
	(17, 30, 'ltmxmaster3-1.webp', 1, 0),
	(18, 24, 'nkd3500-1.webp', 1, 0),
	(19, 18, 'rpm100-1.webp', 1, 0),
	(20, 17, 'rzda-1.webp', 1, 0),
	(21, 2, 'sgs22-1.webp', 1, 0),
	(22, 5, 'sgw5-1.webp', 1, 0),
	(23, 23, 'sna6400-1.webp', 1, 0),
	(24, 14, 'snxb33-1.webp', 1, 0),
	(25, 8, 'swfxm4-1.webp', 1, 0),
	(26, 3, 'xm13l-1.webp', 1, 0),
	(27, 11, 'xm10k-1.webp', 1, 0),
	(28, 15, 'xmsp-1.webp', 1, 0),
	(29, 6, 'xmws1-1.webp', 1, 0),
	(30, 27, 'xmyicam-1.webp', 1, 0),
	(31, 1, 'ip13-2.webp', 0, 0),
	(32, 1, 'ip13-3.webp', 0, 0),
	(33, 1, 'ip13-4.webp', 0, 0),
	(34, 1, 'ip13-5.webp', 0, 0);

-- Dumping structure for table ecommerce_almus.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `role` enum('customer','admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'customer',
  `is_active` tinyint NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_email` (`email`),
  KEY `idx_users_role` (`role`),
  KEY `idx_users_is_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table ecommerce_almus.users: ~0 rows (approximately)
INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `phone`, `address`, `role`, `is_active`, `created_at`) VALUES
	(1, 'Khách hàng 1', 'user1@gmail.com', '$2y$12$ii19aeKfPa/SHtWcmFCtM.0UtgtUaXlQ4VV6FJI7IVI6V3s3R5Hp2', NULL, NULL, 'customer', 1, '2026-03-25 18:42:40');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
