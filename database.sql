-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 06, 2024 at 09:55 AM
-- Server version: 8.0.39-cll-lve
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tahcoino_thrdex313`
--

-- --------------------------------------------------------

--
-- Table structure for table `sell_orders`
--

CREATE TABLE `sell_orders` (
  `id` int NOT NULL,
  `public_key` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `amount` decimal(29,19) DEFAULT NULL,
  `usdt_receiver_address` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `price_in_usdt` decimal(29,5) NOT NULL,
  `private_key` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` enum('active','successful') COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'active'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `successful_orders`
--

CREATE TABLE `successful_orders` (
  `id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `usdt_receiver_address` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sell_orders`
--
ALTER TABLE `sell_orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `successful_orders`
--
ALTER TABLE `successful_orders`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `sell_orders`
--
ALTER TABLE `sell_orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `successful_orders`
--
ALTER TABLE `successful_orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
