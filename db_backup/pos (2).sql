-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 18, 2024 at 04:31 AM
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
-- Database: `pos`
--

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `name`, `email`, `phone`, `age`, `location`, `created_at`) VALUES
(1, 'Mohamed Hesham', 'mohamedhoshame@gmail.com', NULL, NULL, NULL, '2024-10-11 23:21:36');

-- --------------------------------------------------------

--
-- Table structure for table `financial_reports`
--

CREATE TABLE `financial_reports` (
  `report_id` int(11) NOT NULL,
  `report_date` date NOT NULL,
  `revenue` decimal(10,2) DEFAULT 0.00,
  `cost_of_goods_sold` decimal(10,2) DEFAULT 0.00,
  `expenses` decimal(10,2) NOT NULL DEFAULT 0.00,
  `net_profit` decimal(10,2) GENERATED ALWAYS AS (`revenue` - `cost_of_goods_sold` - `expenses`) STORED,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_price_before_discount` decimal(10,2) NOT NULL,
  `pay_with` enum('cash','visa') DEFAULT 'cash'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `customer_id`, `total_price`, `discount`, `order_date`, `total_price_before_discount`, `pay_with`) VALUES
(1, 1, NULL, 100.00, 10.00, '2024-10-11 22:41:27', 0.00, 'cash'),
(2, 1, NULL, 150.00, 5.00, '2024-10-11 22:41:27', 0.00, 'cash'),
(3, 1, NULL, 200.00, 15.00, '2024-10-11 22:41:27', 0.00, 'cash'),
(4, 1, NULL, 250.00, 10.00, '2024-10-11 22:41:27', 0.00, 'cash'),
(5, 1, NULL, 175.00, 0.00, '2024-10-11 22:41:27', 0.00, 'cash'),
(6, 1, NULL, 225.00, 20.00, '2024-10-11 22:41:27', 0.00, 'cash'),
(7, 1, NULL, 300.00, 25.00, '2024-10-11 22:41:27', 0.00, 'cash'),
(8, 1, NULL, 350.00, 15.00, '2024-10-11 22:41:27', 0.00, 'cash'),
(9, 1, NULL, 400.00, 5.00, '2024-10-11 22:41:27', 0.00, 'cash'),
(10, 1, NULL, 450.00, 30.00, '2024-10-11 22:41:27', 0.00, 'cash'),
(12, 2, 1, 900.00, 100.00, '2024-10-11 23:21:44', 1000.00, 'cash'),
(14, 2, NULL, 0.00, 200.00, '2024-10-11 23:25:59', 1000.00, 'cash'),
(15, 2, 1, 900.00, 100.00, '2024-10-11 23:27:41', 1000.00, 'cash'),
(16, 2, NULL, 90.00, 10.00, '2024-10-11 23:28:44', 100.00, 'cash'),
(17, 2, NULL, 0.00, 50.00, '2024-10-11 23:37:34', 50.00, 'cash'),
(18, 2, NULL, 900.00, 100.00, '2024-10-11 23:47:42', 1000.00, 'cash'),
(19, 2, NULL, 980.00, 20.00, '2024-10-11 23:56:36', 1000.00, 'cash'),
(20, 2, NULL, 1000.00, 0.00, '2024-10-11 23:58:51', 1000.00, 'visa'),
(21, 2, NULL, 400.00, 100.00, '2024-10-12 00:14:21', 500.00, 'cash'),
(22, 2, NULL, 0.00, 100.00, '2024-10-12 00:19:11', 100.00, 'visa'),
(23, 2, NULL, 900.00, 100.00, '2024-10-12 12:54:30', 1000.00, 'cash'),
(26, 2, NULL, 900.00, 100.00, '2024-10-12 13:18:05', 1000.00, 'cash'),
(27, 2, NULL, 990.00, 10.00, '2024-10-12 13:18:20', 1000.00, 'cash'),
(28, 2, NULL, 5000.00, 0.00, '2024-10-12 13:18:39', 5000.00, 'cash'),
(29, 2, NULL, 40.00, 5.00, '2024-10-12 13:23:11', 45.00, 'cash'),
(30, 2, NULL, 40.00, 5.00, '2024-10-12 13:24:31', 45.00, 'visa'),
(31, 2, NULL, 195.00, 0.00, '2024-10-12 13:38:34', 195.00, 'cash'),
(32, 2, NULL, 80.00, 2.00, '2024-10-12 13:38:52', 82.00, 'visa'),
(33, 2, NULL, 50.00, 0.00, '2024-10-12 13:55:22', 50.00, 'cash'),
(34, 2, NULL, 20.00, 10.00, '2024-10-12 13:55:41', 30.00, 'cash'),
(35, 2, NULL, 60.00, 0.00, '2024-10-12 15:23:53', 60.00, 'cash'),
(36, 2, NULL, 20.00, 20.00, '2024-10-12 17:58:39', 40.00, 'cash'),
(37, 2, NULL, 15.00, 0.00, '2024-10-12 18:06:28', 15.00, 'cash'),
(38, 2, NULL, 10.00, 5.00, '2024-10-12 18:06:56', 15.00, 'visa'),
(39, 2, NULL, 400.00, 5.00, '2024-10-12 18:17:23', 405.00, 'cash'),
(40, 2, NULL, 15.00, 0.00, '2024-10-12 18:27:34', 15.00, 'cash'),
(41, 2, NULL, 15.00, 0.00, '2024-10-12 18:37:15', 15.00, 'cash'),
(42, 2, NULL, 15.00, 0.00, '2024-10-12 18:38:49', 15.00, 'cash'),
(43, 3, NULL, 200.00, 32.00, '2024-10-12 18:51:31', 232.00, 'cash');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 1, 2, 50.00),
(2, 1, 2, 1, 50.00),
(3, 2, 1, 1, 75.00),
(4, 2, 3, 3, 25.00),
(5, 3, 4, 4, 50.00),
(6, 3, 5, 1, 150.00),
(7, 4, 2, 2, 125.00),
(8, 4, 6, 1, 125.00),
(9, 5, 1, 3, 50.00),
(10, 5, 7, 2, 75.00),
(11, 6, 3, 2, 100.00),
(12, 6, 4, 3, 25.00),
(13, 7, 5, 2, 150.00),
(14, 7, 1, 2, 75.00),
(15, 8, 6, 4, 75.00),
(16, 8, 2, 1, 200.00),
(17, 9, 1, 5, 80.00),
(18, 9, 7, 2, 160.00),
(19, 10, 4, 3, 150.00),
(20, 10, 3, 2, 150.00),
(21, 29, 1, 3, 15.00),
(22, 30, 1, 1, 15.00),
(23, 30, 1, 2, 15.00),
(24, 31, 1, 1, 15.00),
(25, 31, 17, 1, 90.00),
(26, 31, 17, 1, 90.00),
(27, 32, 9, 1, 35.00),
(28, 32, 1, 1, 15.00),
(29, 32, 16, 1, 32.00),
(30, 33, 12, 1, 50.00),
(31, 34, 1, 2, 15.00),
(32, 35, 1, 4, 15.00),
(33, 36, 3, 1, 40.00),
(34, 37, 1, 1, 15.00),
(35, 38, 1, 1, 15.00),
(36, 39, 1, 1, 15.00),
(37, 39, 8, 3, 30.00),
(38, 39, 13, 2, 38.00),
(39, 39, 15, 7, 32.00),
(40, 40, 1, 1, 15.00),
(41, 40, 1, 1, 15.00),
(42, 40, 1, 1, 15.00),
(43, 41, 1, 1, 15.00),
(44, 42, 1, 1, 15.00),
(45, 43, 15, 1, 32.00),
(46, 43, 14, 5, 40.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `cost_price` decimal(10,2) NOT NULL,
  `selling_price` decimal(10,2) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `stock_level` int(11) NOT NULL DEFAULT 0,
  `reorder_point` int(11) DEFAULT 5,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `net_profit` decimal(10,2) GENERATED ALWAYS AS (`selling_price` - `cost_price`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `cost_price`, `selling_price`, `category`, `stock_level`, `reorder_point`, `deleted_at`, `created_at`) VALUES
(1, 'T-shirt Basic White', 5.00, 15.00, 'Clothing', 95, 20, NULL, '2024-10-11 18:03:10'),
(2, 'T-shirt Basic Black', 5.00, 15.00, 'Clothing', 122, 20, NULL, '2024-10-11 18:03:10'),
(3, 'Denim Jeans Slim Fittttttttttt', 20.00, 40.00, 'Clothing', 79, 10, NULL, '2024-10-11 18:03:10'),
(4, 'Denim Jeans Regular Fit', 22.00, 45.00, 'Clothing', 90, 15, NULL, '2024-10-11 18:03:10'),
(5, 'Hoodie Oversized Gray', 25.00, 55.00, 'Clothing', 70, 8, NULL, '2024-10-11 18:03:10'),
(6, 'Hoodie Oversized Black', 25.00, 55.00, 'Clothing', 65, 10, NULL, '2024-10-11 18:03:10'),
(7, 'Polo Shirt Navy', 15.00, 30.00, 'Clothing', 100, 12, NULL, '2024-10-11 18:03:10'),
(8, 'Polo Shirt White', 15.00, 30.00, 'Clothing', 92, 15, NULL, '2024-10-11 18:03:10'),
(9, 'Cargo Pants Green', 18.00, 35.00, 'Clothing', 84, 10, NULL, '2024-10-11 18:03:10'),
(10, 'Cargo Pants Black', 18.00, 35.00, 'Clothing', 90, 10, NULL, '2024-10-11 18:03:10'),
(11, 'Sweatshirt Crewneck Beige', 22.00, 50.00, 'Clothing', 60, 5, NULL, '2024-10-11 18:03:10'),
(12, 'Sweatshirt Crewneck Navy', 22.00, 50.00, 'Clothing', 54, 7, NULL, '2024-10-11 18:03:10'),
(13, 'Chino Pants Slim Fit', 18.00, 38.00, 'Clothing', 73, 10, NULL, '2024-10-11 18:03:10'),
(14, 'Chino Pants Regular Fit', 20.00, 40.00, 'Clothing', 75, 12, NULL, '2024-10-11 18:03:10'),
(15, 'Tracksuit Joggers Gray', 15.00, 32.00, 'Clothing', 92, 20, NULL, '2024-10-11 18:03:10'),
(16, 'Tracksuit Joggers Black', 15.00, 32.00, 'Clothing', 109, 25, NULL, '2024-10-11 18:03:10'),
(17, 'Winter Jacket Insulated', 50.00, 90.00, 'Clothing', 38, 5, NULL, '2024-10-11 18:03:10');

-- --------------------------------------------------------

--
-- Table structure for table `refunds`
--

CREATE TABLE `refunds` (
  `refund_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `order_item_id` int(11) DEFAULT NULL,
  `refund_amount` decimal(10,2) NOT NULL,
  `refund_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `reason` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `refunds`
--

INSERT INTO `refunds` (`refund_id`, `order_id`, `order_item_id`, `refund_amount`, `refund_date`, `reason`) VALUES
(1, 34, 31, 2.00, '2024-10-12 15:14:27', 'test'),
(2, 34, 31, 1.00, '2024-10-12 15:15:42', 'test'),
(3, 1, 1, 1.00, '2024-10-12 15:16:03', 'test'),
(4, 1, 2, 1.00, '2024-10-12 15:16:03', 'test'),
(5, 35, 32, 4.00, '2024-10-12 15:24:06', 'test'),
(6, 40, 40, 1.00, '2024-10-12 18:37:04', '123'),
(7, 40, 41, 1.00, '2024-10-12 18:37:04', '123'),
(8, 40, 42, 1.00, '2024-10-12 18:37:04', '123'),
(9, 42, 44, 1.00, '2024-10-12 18:39:03', '123'),
(10, 42, 44, 1.00, '2024-10-12 18:41:13', 'test'),
(11, 42, 44, 1.00, '2024-10-12 18:41:22', '123'),
(12, 1, 1, 2.00, '2024-10-12 18:41:34', 'test'),
(13, 1, 2, 1.00, '2024-10-12 18:41:34', 'test'),
(14, 37, 34, 1.00, '2024-10-12 18:41:54', 'test');

-- --------------------------------------------------------

--
-- Stand-in structure for view `sales_metrics`
-- (See below for the actual view)
--
CREATE TABLE `sales_metrics` (
`order_date` date
,`transaction_count` bigint(21)
,`total_sales` decimal(47,8)
,`top_selling_product` varchar(100)
,`sales_by_category` mediumtext
);

-- --------------------------------------------------------

--
-- Table structure for table `sales_reports`
--

CREATE TABLE `sales_reports` (
  `report_id` int(11) NOT NULL,
  `report_type` enum('daily','weekly','monthly') NOT NULL,
  `report_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('super_admin','admin') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `role`, `created_at`) VALUES
(1, 'ibrahim', '$2y$10$OiQungG/QpBMfc/lwDLG5.WB3KF9lSYR2dBzHuaFHPAVzSIdjo3Wi', 'ibrahim@example.com', 'super_admin', '2024-10-11 16:50:21'),
(2, 'flepooo', '$2y$10$OiQungG/QpBMfc/lwDLG5.WB3KF9lSYR2dBzHuaFHPAVzSIdjo3Wi', 'mohamedhoshame@gmail.com', 'super_admin', '2024-10-11 17:13:57'),
(3, 'Tokyo', '$2y$10$ABS87gYryCOBxicIkapzO.zwxQhPHc.oXp4Itoha9yETz8rufwO5.', 'mail@tokyo.com', 'super_admin', '2024-10-12 18:48:32');

-- --------------------------------------------------------

--
-- Structure for view `sales_metrics`
--
DROP TABLE IF EXISTS `sales_metrics`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `sales_metrics`  AS SELECT cast(`o`.`order_date` as date) AS `order_date`, count(distinct `o`.`order_id`) AS `transaction_count`, sum(`o`.`total_price` - `o`.`total_price` * (`o`.`discount` / 100)) AS `total_sales`, (select `p`.`product_name` from (`products` `p` join `order_items` `oi` on(`p`.`product_id` = `oi`.`product_id`)) where `oi`.`order_id` = `o`.`order_id` group by `p`.`product_id` order by sum(`oi`.`quantity`) desc limit 1) AS `top_selling_product`, group_concat(distinct concat(`p`.`category`,': ',`order_summary`.`total_quantity`) separator ', ') AS `sales_by_category` FROM (((select `o`.`order_id` AS `order_id`,`o`.`order_date` AS `order_date`,`o`.`total_price` AS `total_price`,`o`.`discount` AS `discount`,`oi`.`product_id` AS `product_id`,sum(`oi`.`quantity`) AS `total_quantity` from (`orders` `o` join `order_items` `oi` on(`o`.`order_id` = `oi`.`order_id`)) group by `o`.`order_id`,`oi`.`product_id`) `order_summary` join `products` `p` on(`order_summary`.`product_id` = `p`.`product_id`)) join `orders` `o` on(`order_summary`.`order_id` = `o`.`order_id`)) GROUP BY cast(`o`.`order_date` as date) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `financial_reports`
--
ALTER TABLE `financial_reports`
  ADD PRIMARY KEY (`report_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `customer_id` (`customer_id`);

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
-- Indexes for table `refunds`
--
ALTER TABLE `refunds`
  ADD PRIMARY KEY (`refund_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `order_item_id` (`order_item_id`);

--
-- Indexes for table `sales_reports`
--
ALTER TABLE `sales_reports`
  ADD PRIMARY KEY (`report_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `financial_reports`
--
ALTER TABLE `financial_reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `refunds`
--
ALTER TABLE `refunds`
  MODIFY `refund_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `sales_reports`
--
ALTER TABLE `sales_reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE SET NULL;

--
-- Constraints for table `refunds`
--
ALTER TABLE `refunds`
  ADD CONSTRAINT `refunds_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `refunds_ibfk_2` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`order_item_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
