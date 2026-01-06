-- CaftanVue Database Tables - Direct SQL
-- Run this in phpMyAdmin to create all tables with indexes

-- Table 1: Caftans
CREATE TABLE `caftans` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image_url` varchar(500) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `collection` varchar(50) NOT NULL,
  `color` varchar(50) NOT NULL,
  `size` varchar(10) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'available',
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `caftans_name_index` (`name`),
  KEY `caftans_collection_index` (`collection`),
  KEY `caftans_color_index` (`color`),
  KEY `caftans_status_index` (`status`),
  KEY `caftans_collection_status_index` (`collection`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table 2: Clients
CREATE TABLE `clients` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `cin` varchar(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clients_email_unique` (`email`),
  UNIQUE KEY `clients_cin_unique` (`cin`),
  KEY `clients_name_index` (`name`),
  KEY `clients_phone_index` (`phone`),
  KEY `clients_cin_index` (`cin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table 3: Reservations
CREATE TABLE `reservations` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `caftan_id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `total_price` decimal(10,2) NOT NULL,
  `notes` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reservations_caftan_id_index` (`caftan_id`),
  KEY `reservations_client_id_index` (`client_id`),
  KEY `reservations_start_date_index` (`start_date`),
  KEY `reservations_end_date_index` (`end_date`),
  KEY `reservations_status_index` (`status`),
  KEY `reservations_status_dates_index` (`status`, `start_date`, `end_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table 4: Admins
CREATE TABLE `admins` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admins_username_unique` (`username`),
  UNIQUE KEY `admins_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample data for testing
INSERT INTO `caftans` (`name`, `description`, `image_url`, `price`, `collection`, `color`, `size`, `status`, `is_available`, `created_at`, `updated_at`) VALUES
('Traditional Moroccan Caftan - Royal Blue', 'Exquisite hand-embroidered caftan with traditional Moroccan designs', 'https://picsum.photos/400/600?random=1', 1500.00, 'Traditional', 'Blue', 'M', 'available', 1, NOW(), NOW()),
('Modern Wedding Caftan - Gold', 'Elegant gold caftan with modern cuts and embellishments', 'https://picsum.photos/400/600?random=2', 2500.00, 'Wedding', 'Gold', 'L', 'available', 1, NOW(), NOW()),
('Casual Summer Caftan - White', 'Light and comfortable white caftan for everyday wear', 'https://picsum.photos/400/600?random=3', 800.00, 'Casual', 'White', 'S', 'available', 1, NOW(), NOW()),
('Traditional Green Caftan', 'Beautiful emerald green caftan with silver embroidery', 'https://picsum.photos/400/600?random=4', 1800.00, 'Traditional', 'Green', 'M', 'rented', 0, NOW(), NOW()),
('Modern Red Evening Caftan', 'Stunning red caftan with contemporary design', 'https://picsum.photos/400/600?random=5', 2200.00, 'Modern', 'Red', 'L', 'available', 1, NOW(), NOW()),
('Wedding Caftan - Silver', 'Luxurious silver caftan with intricate beadwork', 'https://picsum.photos/400/600?random=6', 3000.00, 'Wedding', 'Silver', 'XL', 'available', 1, NOW(), NOW());

INSERT INTO `clients` (`name`, `email`, `phone`, `address`, `cin`, `created_at`, `updated_at`) VALUES
('Fatima Hassan', 'fatima.hassan@example.com', '+212 6 12 34 56 78', '123 Rue Mohammed V, Casablanca, Morocco', 'AB123456', NOW(), NOW()),
('Amina Benali', 'amina.benali@example.com', '+212 6 98 76 54 32', '45 Avenue Hassan II, Rabat, Morocco', 'CD789012', NOW(), NOW()),
('Salma Alaoui', 'salma.alaoui@example.com', '+212 6 55 44 33 22', '78 Rue de Fes, Marrakech, Morocco', 'EF345678', NOW(), NOW());

INSERT INTO `reservations` (`caftan_id`, `client_id`, `start_date`, `end_date`, `status`, `total_price`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, '2024-06-01', '2024-06-05', 'confirmed', 1500.00, 'Wedding ceremony - needs alterations', NOW(), NOW()),
(4, 2, '2024-05-15', '2024-05-18', 'completed', 1800.00, NULL, NOW(), NOW()),
(2, 3, '2024-07-10', '2024-07-15', 'pending', 2500.00, 'Engagement party', NOW(), NOW());
