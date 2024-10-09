-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 09, 2024 at 06:36 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tokyo_pos`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `total_price`, `order_date`) VALUES
(1, 1, 350.00, '2024-09-30 00:30:05'),
(2, 1, 350.00, '2024-09-30 00:33:22'),
(3, 1, 170.00, '2024-09-30 00:34:06'),
(4, 1, 690.00, '2024-09-30 00:40:34'),
(5, 1, 1050.00, '2024-09-30 00:43:24'),
(6, 1, 1210.00, '2024-09-30 00:47:36'),
(7, 1, 520.00, '2024-09-30 01:12:00'),
(8, 1, 350.00, '2024-09-30 01:19:19'),
(9, 1, 350.00, '2024-09-30 01:21:09'),
(10, 1, 350.00, '2024-09-30 01:24:39'),
(11, 1, 1370.00, '2024-09-30 01:29:12'),
(12, 1, 170.00, '2024-09-30 01:29:21'),
(13, 1, 350.00, '2024-09-30 01:40:54'),
(14, 1, 350.00, '2024-09-30 01:47:06'),
(15, 1, 350.00, '2024-09-30 01:51:05'),
(16, 1, 690.00, '2024-09-30 01:52:07'),
(17, 1, 350.00, '2024-09-30 01:53:27'),
(18, 1, 170.00, '2024-09-30 01:59:17'),
(19, 1, 520.00, '2024-10-01 19:21:24'),
(20, 1, 1050.00, '2024-10-01 19:25:58'),
(21, 1, 700.00, '2024-10-01 19:38:29'),
(22, 1, 3150.00, '2024-10-01 20:14:32'),
(23, 1, 1050.00, '2024-10-01 20:30:33'),
(24, 1, 350.00, '2024-10-01 20:34:01'),
(25, 1, 1770.00, '2024-10-01 20:40:09'),
(26, 1, 510.00, '2024-10-01 20:57:33'),
(27, 1, 700.00, '2024-10-01 20:58:05'),
(28, 1, 10000.00, '2024-10-01 21:02:18'),
(29, 1, 17650.00, '2024-10-01 21:02:40'),
(30, 1, 200.00, '2024-10-01 21:29:09'),
(31, 1, 350.00, '2024-10-01 21:29:14'),
(32, 1, 200.00, '2024-10-01 22:53:10'),
(33, 1, 700.00, '2024-10-02 15:31:58'),
(34, 1, 2000.00, '2024-10-02 15:32:10'),
(35, 1, 1050.00, '2024-10-02 15:38:33'),
(36, 3, 200.00, '2024-10-02 15:41:55'),
(37, 1, 1050.00, '2024-10-02 15:48:14'),
(38, 4, 1150.00, '2024-10-02 16:02:21');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 1, 1, 350.00),
(2, 2, 1, 1, 350.00),
(3, 3, 3, 1, 170.00),
(4, 4, 1, 1, 350.00),
(5, 4, 3, 2, 170.00),
(6, 5, 1, 3, 350.00),
(7, 6, 1, 1, 350.00),
(8, 6, 3, 3, 170.00),
(9, 6, 1, 1, 350.00),
(10, 7, 1, 1, 350.00),
(11, 7, 3, 1, 170.00),
(12, 8, 1, 1, 350.00),
(13, 9, 1, 1, 350.00),
(14, 10, 1, 1, 350.00),
(15, 11, 1, 1, 350.00),
(16, 11, 3, 6, 170.00),
(17, 12, 3, 1, 170.00),
(18, 13, 1, 1, 350.00),
(19, 14, 1, 1, 350.00),
(20, 15, 1, 1, 350.00),
(21, 16, 1, 1, 350.00),
(22, 16, 3, 1, 170.00),
(23, 16, 3, 1, 170.00),
(24, 17, 1, 1, 350.00),
(25, 18, 3, 1, 170.00),
(26, 19, 1, 1, 350.00),
(27, 19, 3, 1, 170.00),
(28, 20, 1, 1, 350.00),
(29, 20, 1, 1, 350.00),
(30, 20, 1, 1, 350.00),
(31, 21, 1, 1, 350.00),
(32, 21, 1, 1, 350.00),
(33, 22, 1, 1, 350.00),
(34, 22, 1, 1, 350.00),
(35, 22, 1, 1, 350.00),
(36, 22, 1, 1, 350.00),
(37, 22, 1, 1, 350.00),
(38, 22, 1, 1, 350.00),
(39, 22, 1, 1, 350.00),
(40, 22, 1, 1, 350.00),
(41, 22, 1, 1, 350.00),
(42, 23, 1, 1, 350.00),
(43, 23, 1, 1, 350.00),
(44, 23, 1, 1, 350.00),
(45, 24, 1, 1, 350.00),
(46, 25, 4, 1, 200.00),
(47, 25, 1, 1, 350.00),
(48, 25, 3, 1, 170.00),
(49, 25, 1, 1, 350.00),
(50, 25, 1, 1, 350.00),
(51, 25, 1, 1, 350.00),
(52, 26, 3, 3, 170.00),
(53, 27, 1, 1, 350.00),
(54, 27, 1, 1, 350.00),
(55, 28, 5, 10, 1000.00),
(56, 29, 5, 10, 1000.00),
(57, 29, 1, 15, 350.00),
(58, 29, 4, 12, 200.00),
(59, 30, 4, 1, 200.00),
(60, 31, 1, 1, 350.00),
(61, 32, 4, 1, 200.00),
(62, 33, 1, 1, 350.00),
(63, 33, 1, 1, 350.00),
(64, 34, 5, 2, 1000.00),
(65, 35, 1, 1, 350.00),
(66, 35, 1, 1, 350.00),
(67, 35, 1, 1, 350.00),
(68, 36, 4, 1, 200.00),
(69, 37, 1, 3, 350.00),
(70, 38, 4, 1, 200.00),
(71, 38, 1, 1, 350.00),
(72, 38, 4, 3, 200.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `real_price` decimal(10,2) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `real_price`, `category`, `price`, `quantity`, `description`, `created_at`, `updated_at`) VALUES
(1, 't-shirt', 0.00, NULL, 350.00, 74, NULL, '2024-09-30 01:26:30', '2024-10-02 17:02:21'),
(3, 'test', 0.00, NULL, 170.00, 0, NULL, '2024-09-30 01:33:48', '2024-10-01 21:57:33'),
(4, 'test-2', 150.00, NULL, 200.00, 10, NULL, '2024-09-30 13:56:05', '2024-10-02 17:02:21'),
(5, '7odaa', 850.00, NULL, 1000.00, 3, NULL, '2024-10-01 22:01:53', '2024-10-02 16:32:10');

-- --------------------------------------------------------

--
-- Table structure for table `product_changes`
--

CREATE TABLE `product_changes` (
  `change_id` int(11) NOT NULL,
  `original_product_id` int(11) NOT NULL,
  `new_product_id` int(11) NOT NULL,
  `change_quantity` int(11) NOT NULL,
  `change_reason` varchar(255) DEFAULT NULL,
  `change_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `changed_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `refunds`
--

CREATE TABLE `refunds` (
  `refund_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `refund_quantity` int(11) NOT NULL,
  `refund_reason` varchar(255) DEFAULT NULL,
  `refund_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `refunded_by` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `refund_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `refunds`
--

INSERT INTO `refunds` (`refund_id`, `product_id`, `refund_quantity`, `refund_reason`, `refund_date`, `refunded_by`, `order_id`, `refund_price`) VALUES
(1, 3, 1, 'tttt', '2024-09-30 14:44:51', 0, 8, 0.00),
(2, 3, 1, 'tttt', '2024-09-30 14:45:44', 0, 18, 0.00),
(3, 1, 1, '7878', '2024-09-30 14:48:49', 0, 1, 0.00),
(4, 1, 1, 'tttt', '2024-09-30 14:49:03', 0, 1, 0.00),
(5, 1, 2, '10', '2024-09-30 14:49:13', 0, 1, 0.00),
(6, 1, 2, '10', '2024-09-30 14:51:00', 1, 1, 0.00),
(7, 1, 8, '7979', '2024-09-30 14:51:10', 1, 1, 0.00),
(8, 1, 8, '7979', '2024-09-30 14:53:39', 1, 1, 0.00),
(9, 1, 2, 'tttt', '2024-09-30 15:19:41', 1, 1, 700.00),
(10, 1, 2, 'tttt', '2024-09-30 15:21:49', 1, 1, 700.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `role`, `created_at`) VALUES
(1, 'admin', '1234', 'admin@example.com', 'admin', '2024-09-30 01:03:45'),
(3, 'Tokyo', '123456', 'admin@example.com', 'admin', '2024-10-02 16:37:52'),
(4, 'Flepooo', '123456', 'flepooo@gmail.com', 'user', '2024-10-02 16:40:56');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `product_changes`
--
ALTER TABLE `product_changes`
  ADD PRIMARY KEY (`change_id`),
  ADD KEY `original_product_id` (`original_product_id`),
  ADD KEY `new_product_id` (`new_product_id`);

--
-- Indexes for table `refunds`
--
ALTER TABLE `refunds`
  ADD PRIMARY KEY (`refund_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `product_changes`
--
ALTER TABLE `product_changes`
  MODIFY `change_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `refunds`
--
ALTER TABLE `refunds`
  MODIFY `refund_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `product_changes`
--
ALTER TABLE `product_changes`
  ADD CONSTRAINT `product_changes_ibfk_1` FOREIGN KEY (`original_product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `product_changes_ibfk_2` FOREIGN KEY (`new_product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `refunds`
--
ALTER TABLE `refunds`
  ADD CONSTRAINT `refunds_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
