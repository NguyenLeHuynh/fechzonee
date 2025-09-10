-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 10, 2025 at 11:38 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fechzone`
--

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `full_name` varchar(150) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `username`, `password_hash`, `role`, `full_name`, `email`, `phone`, `address`, `created_at`) VALUES
(1, 'lhuynh123', '$2y$10$1ydbJ5LGviDsfnW2a3h9d.tYtAOKUBKT5jop0jcQwyiAKVfnNKoZG', 'user', NULL, NULL, NULL, NULL, '2025-09-10 19:02:37'),
(2, 'lehuynh1', '$2y$10$sRQwEW7HXL2WD.kPsbj3Je3zOl8JWCSJQrHEc7RUzB3MV3CdXWwDS', 'admin', NULL, NULL, NULL, NULL, '2025-09-10 19:03:28');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(10) UNSIGNED NOT NULL,
  `customer_id` int(10) UNSIGNED NOT NULL,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','paid','shipped','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_id`, `total_amount`, `status`, `created_at`) VALUES
(2, 1, 119.00, 'pending', '2025-09-10 19:02:49');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 2, 10, 1, 119.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock` int(11) NOT NULL DEFAULT 0,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `stock`, `image_url`, `created_at`) VALUES
(1, 'FechBook 14 (2025)', 2399.00, 15, 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?q=80&w=1200&auto=format&fit=crop', '2025-09-10 18:50:32'),
(2, 'FechBook Pro 16 (Mới)', 3299.00, 10, 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?q=80&w=1400&auto=format&fit=crop', '2025-09-10 18:50:32'),
(3, 'FechTower RGB i7/RTX 4070', 1899.00, 8, 'https://images.unsplash.com/photo-1516116216624-53e697fedbea?q=80&w=1400&auto=format&fit=crop', '2025-09-10 18:50:32'),
(4, 'FechTower RGB i9/RTX 4080', 2899.00, 5, 'https://images.unsplash.com/photo-1516116216624-53e697fedbea?q=80&w=1600&auto=format&fit=crop', '2025-09-10 18:50:32'),
(5, 'Màn hình 27\" 144Hz IPS', 299.00, 25, 'https://images.unsplash.com/photo-1527443154391-507e9dc6c5cc?q=80&w=1400&auto=format&fit=crop', '2025-09-10 18:50:32'),
(6, 'Màn hình 34\" Ultrawide 144Hz', 699.00, 12, 'https://images.unsplash.com/photo-1587202372775-98926bb02c31?q=80&w=1400&auto=format&fit=crop', '2025-09-10 18:50:32'),
(7, 'Bàn phím cơ RGB 87 phím', 99.00, 60, 'https://images.unsplash.com/photo-1518779578993-ec3579fee39f?q=80&w=1400&auto=format&fit=crop', '2025-09-10 18:50:32'),
(8, 'Chuột không dây 2.4G', 39.00, 80, 'https://images.unsplash.com/photo-1587825140400-5fc7f8d757d6?q=80&w=1400&auto=format&fit=crop', '2025-09-10 18:50:32'),
(9, 'Tai nghe gaming 7.1', 79.00, 40, 'https://images.unsplash.com/photo-1518441902119-0f3c44f0c075?q=80&w=1400&auto=format&fit=crop', '2025-09-10 18:50:32'),
(10, 'SSD NVMe 1TB Gen4', 119.00, 50, 'https://images.unsplash.com/photo-1518770660439-4636190af475?q=80&w=1400&auto=format&fit=crop', '2025-09-10 18:50:32'),
(11, 'đ', 12.00, 1, NULL, '2025-09-10 19:15:47');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_items_order` (`order_id`),
  ADD KEY `fk_items_product` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
