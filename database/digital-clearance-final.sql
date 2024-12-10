-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 10, 2024 at 08:06 PM
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
-- Table structure for table `dean_cred`
--

CREATE TABLE `dean_cred` (
  `id` int NOT NULL,
  `dean_id` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `dean_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `dean_cred`
--

INSERT INTO `dean_cred` (`id`, `dean_id`, `password`, `dean_name`) VALUES
(3, '300908645', 'dc7494443252fb5336d84cb9222cfef9', 'George M. Granados');

-- --------------------------------------------------------

--
-- Table structure for table `deptartments_cred`
--

CREATE TABLE `deptartments_cred` (
  `id` int NOT NULL,
  `dept_id` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `dept_name` varchar(255) NOT NULL,
  `employee_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `deptartments_cred`
--

INSERT INTO `deptartments_cred` (`id`, `dept_id`, `password`, `dept_name`, `employee_name`) VALUES
(1, '100236527', '4c157c13dbc5ea957b3f98b9094a1b5e', 'Library', 'Irene M. Mungcal'),
(3, '100045768', '1b672093c089293e9e4a784f733ace53', 'OSA', 'Angelo A. Baltazar'),
(4, '122345342', '4e85a5b3e81d6b2c07c89d8a310143bf', 'Guidance', 'Abigail B. Wong'),
(5, '200987836', '8eff007c9b040cde002b2a28576b7f1f', 'Foreign Affairs', 'Imelda C. Stevenson'),
(6, '100239865', 'bf0fd94d53c79f71f8638de8bb4ec26e', 'Computer Lab', 'Marvin A. Reyes'),
(7, '100245876', '1da96f27dcc726e1d2bbbd945c57a8ef', 'Program Chair', 'John Doe'),
(8, '100365941', '30e0926806837aea1c1379dcbd13f455', 'Registrar', 'Daisie W. Pinzon'),
(9, '100320564', 'd70c78adb562de584741cd9afdb60417', 'Vice President', 'Roy D. Dayrit'),
(10, '100214503', '70a24a146dcf7ee3f567625028b97f5d', 'Accounting', 'Jemelyn A. Dayrit');

-- --------------------------------------------------------

--
-- Table structure for table `students_cred`
--

CREATE TABLE `students_cred` (
  `id` int NOT NULL,
  `stud_id` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `students_cred`
--

INSERT INTO `students_cred` (`id`, `stud_id`, `password`, `name`) VALUES
(1, '0121300331', '86ab38b39c039c74c6547d093ff34b66', 'Ram Yturralde'),
(3, '0121300314', '47bdef85adbe8fb68bbc809aaf55c8cc', 'Louis Tiomico'),
(4, '0121302381', 'f4629b0cb658b6157989389213bc6cae', 'Karl John Nucum'),
(5, '0122303926', '47a7d4c8e93337305ff9017722f8fff3', 'John Andre Beltran');

-- --------------------------------------------------------

--
-- Table structure for table `student_clearance`
--

CREATE TABLE `student_clearance` (
  `id` int NOT NULL,
  `stud_id` varchar(255) NOT NULL,
  `Library` int NOT NULL,
  `OSA` int NOT NULL,
  `Guidance` int NOT NULL,
  `Foreign Affairs` int NOT NULL,
  `Computer Lab` int NOT NULL,
  `Program Chair` int NOT NULL,
  `Registrar` int NOT NULL,
  `Vice President` int NOT NULL,
  `Accounting` int NOT NULL,
  `Dean` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `student_clearance`
--

INSERT INTO `student_clearance` (`id`, `stud_id`, `Library`, `OSA`, `Guidance`, `Foreign Affairs`, `Computer Lab`, `Program Chair`, `Registrar`, `Vice President`, `Accounting`, `Dean`) VALUES
(1, '0121300331', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(2, '0121300314', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(4, '0121302381', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(5, '0122303926', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `student_comment`
--

CREATE TABLE `student_comment` (
  `id` int NOT NULL,
  `stud_id` varchar(255) NOT NULL,
  `Library` text NOT NULL,
  `OSA` text NOT NULL,
  `Guidance` text NOT NULL,
  `Foreign Affairs` text NOT NULL,
  `Computer Lab` text NOT NULL,
  `Program Chair` text NOT NULL,
  `Registrar` text NOT NULL,
  `Vice President` text NOT NULL,
  `Accounting` text NOT NULL,
  `Dean` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `student_comment`
--

INSERT INTO `student_comment` (`id`, `stud_id`, `Library`, `OSA`, `Guidance`, `Foreign Affairs`, `Computer Lab`, `Program Chair`, `Registrar`, `Vice President`, `Accounting`, `Dean`) VALUES
(1, '0121300331', '', '', '', '', '', '', '', '', '', ''),
(2, '0121300314', '                                    ', '', '', '', '', '', '', '', '', ''),
(4, '0121302381', '', '', '', '', '', '', '', '', '', ''),
(5, '0122303926', 'Hello', '', '', '', '', '', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `student_date`
--

CREATE TABLE `student_date` (
  `id` int NOT NULL,
  `stud_id` varchar(255) NOT NULL,
  `Library` text NOT NULL,
  `OSA` text NOT NULL,
  `Guidance` text NOT NULL,
  `Foreign Affairs` text NOT NULL,
  `Computer Lab` text NOT NULL,
  `Program Chair` text NOT NULL,
  `Registrar` text NOT NULL,
  `Vice President` text NOT NULL,
  `Accounting` text NOT NULL,
  `Dean` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `student_date`
--

INSERT INTO `student_date` (`id`, `stud_id`, `Library`, `OSA`, `Guidance`, `Foreign Affairs`, `Computer Lab`, `Program Chair`, `Registrar`, `Vice President`, `Accounting`, `Dean`) VALUES
(1, '0121300331', '', '', '', '', '', '', '', '', '', ''),
(2, '0121300314', '', '', '', '', '', '', '', '', '', ''),
(4, '0121302381', '', '', '', '', '', '', '', '', '', ''),
(5, '0122303926', '', '', '', '', '', '', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `student_info`
--

CREATE TABLE `student_info` (
  `id` int NOT NULL,
  `stud_id` varchar(255) NOT NULL,
  `stud_name` varchar(255) NOT NULL,
  `course` varchar(255) NOT NULL,
  `contact_number` varchar(255) NOT NULL,
  `year_level` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `student_info`
--

INSERT INTO `student_info` (`id`, `stud_id`, `stud_name`, `course`, `contact_number`, `year_level`) VALUES
(1, '0121300331', 'Ram Yturralde', 'BSIT', '09052564546', 4),
(2, '0121300314', 'Louis Tiomico', 'BSIT', '09663254187', 4),
(3, '0122303926', 'John Andre Beltran', 'BSIT', '09663254187', 4),
(4, '0121302381', 'Karl John Nucum', 'BSIT', '09326614570', 4);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dean_cred`
--
ALTER TABLE `dean_cred`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `deptartments_cred`
--
ALTER TABLE `deptartments_cred`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students_cred`
--
ALTER TABLE `students_cred`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_clearance`
--
ALTER TABLE `student_clearance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_comment`
--
ALTER TABLE `student_comment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_date`
--
ALTER TABLE `student_date`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_info`
--
ALTER TABLE `student_info`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dean_cred`
--
ALTER TABLE `dean_cred`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `deptartments_cred`
--
ALTER TABLE `deptartments_cred`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `students_cred`
--
ALTER TABLE `students_cred`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `student_clearance`
--
ALTER TABLE `student_clearance`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `student_comment`
--
ALTER TABLE `student_comment`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `student_date`
--
ALTER TABLE `student_date`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `student_info`
--
ALTER TABLE `student_info`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
