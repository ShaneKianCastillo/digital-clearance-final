-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 08, 2024 at 07:25 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `digital-clearance-final`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user_id`, `password`, `name`) VALUES
(1, '0121300331', 'a23ce8b43ad54109d361bd8b63d30fb3', 'Ram Yturralde'),
(3, '0121300314', '47bdef85adbe8fb68bbc809aaf55c8cc', 'Louis Tiomico'),
(4, '0121302381', 'f4629b0cb658b6157989389213bc6cae', 'Karl John L. Nucum'),
(5, '0122303926', '47a7d4c8e93337305ff9017722f8fff3', 'John Andre D. Beltran'),
(6, '100236527', '4c157c13dbc5ea957b3f98b9094a1b5e', 'Library'),
(7, '100045768', '1b672093c089293e9e4a784f733ace53', 'OSA'),
(8, '122345342', 'fe23b3e6ffc4d3cf50edb90b90a96f2e', 'Cashier'),
(9, '200987836', '2210242bd27ed116d858ef59016926db', 'Student Council'),
(10, '300908645', 'dc7494443252fb5336d84cb9222cfef9', 'Dean');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
