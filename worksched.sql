-- phpMyAdmin SQL Dump
-- version 4.6.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 18, 2016 at 03:27 AM
-- Server version: 5.7.13
-- PHP Version: 5.6.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `worksched`
--

-- --------------------------------------------------------

--
-- Table structure for table `shift`
--

CREATE TABLE `shift` (
  `id` int(10) NOT NULL,
  `manager_id` int(10) NOT NULL,
  `break` float DEFAULT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Work shift table';

--
-- Dumping data for table `shift`
--

INSERT INTO `shift` (`id`, `manager_id`, `break`, `start_time`, `end_time`, `created_at`, `updated_at`) VALUES
(1, 1, 0.5, '2016-07-16 08:00:00', '2016-07-16 16:00:00', '2016-07-15 00:00:00', '2016-07-15 00:00:00'),
(2, 1, 0.5, '2016-07-17 08:00:00', '2016-07-17 16:00:00', '2016-07-15 00:00:00', '2016-07-15 00:00:00'),
(3, 3, 0.65, '2016-07-17 10:00:00', '2016-07-17 18:00:00', '2016-07-15 00:00:00', '2016-07-17 03:17:25'),
(4, 3, 0.6, '2016-07-17 10:00:00', '2016-07-17 18:00:00', '2016-07-15 00:00:00', '2016-07-18 02:42:49'),
(5, 1, 0.5, '2016-07-21 08:00:00', '2016-07-21 16:00:00', '2016-07-17 02:19:07', '2016-07-17 02:19:07'),
(6, 3, 0.5, '2016-07-21 16:00:00', '2016-07-21 23:59:00', '2016-07-17 02:22:30', '2016-07-17 02:22:30'),
(7, 1, 0.5, '2016-07-23 13:00:00', '2016-07-23 21:00:00', '2016-07-17 03:23:04', '2016-07-17 03:23:04'),
(8, 3, 0.6, '2016-07-23 06:00:00', '2016-07-23 12:00:00', '2016-07-17 03:29:58', '2016-07-17 03:29:58'),
(9, 3, 0.5, '2016-07-24 02:00:00', '2016-07-24 10:00:00', '2016-07-18 01:32:03', '2016-07-18 01:32:03'),
(10, 1, 0.5, '2016-07-25 00:00:00', '2016-07-25 08:00:00', '2016-07-18 01:57:10', '2016-07-18 01:57:10'),
(11, 1, 0.5, '2016-07-25 08:00:00', '2016-07-25 16:00:00', '2016-07-18 01:58:43', '2016-07-18 01:58:43'),
(12, 1, 0.5, '2016-07-26 08:00:00', '2016-07-26 16:00:00', '2016-07-18 02:08:52', '2016-07-18 02:08:52'),
(13, 1, 0.5, '2016-07-30 00:00:00', '2016-07-30 08:00:00', '2016-07-18 02:49:01', '2016-07-18 02:49:01');

-- --------------------------------------------------------

--
-- Table structure for table `shift_assignment`
--

CREATE TABLE `shift_assignment` (
  `id` int(10) NOT NULL,
  `shift_id` int(10) NOT NULL,
  `employee_id` int(10) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Link shift to employees';

--
-- Dumping data for table `shift_assignment`
--

INSERT INTO `shift_assignment` (`id`, `shift_id`, `employee_id`, `created_at`, `updated_at`) VALUES
(1, 1, 2, '2016-07-15 00:00:00', '2016-07-15 00:00:00'),
(2, 1, 4, '2016-07-15 00:00:00', '2016-07-15 00:00:00'),
(3, 1, 5, '2016-07-15 00:00:00', '2016-07-15 00:00:00'),
(4, 1, 6, '2016-07-15 00:00:00', '2016-07-15 00:00:00'),
(5, 2, 2, '2016-07-15 00:00:00', '2016-07-15 00:00:00'),
(6, 3, 5, '2016-07-15 00:00:00', '2016-07-17 03:05:58'),
(7, 4, NULL, '2016-07-15 00:00:00', '2016-07-15 00:00:00'),
(8, 4, 5, '2016-07-15 00:00:00', '2016-07-15 00:00:00'),
(9, 3, 5, '2016-07-15 00:00:00', '2016-07-15 00:00:00'),
(10, 5, 2, '2016-07-17 02:19:07', '2016-07-17 02:19:07'),
(11, 6, 5, '2016-07-17 02:22:30', '2016-07-17 02:22:30'),
(12, 3, 2, '2016-07-17 03:07:19', '2016-07-17 03:07:19'),
(13, 6, 2, '2016-07-17 03:09:28', '2016-07-17 03:09:28'),
(14, 7, 6, '2016-07-17 03:23:04', '2016-07-18 02:06:24'),
(15, 8, 5, '2016-07-17 03:29:58', '2016-07-17 03:29:58'),
(16, 9, 4, '2016-07-18 01:32:03', '2016-07-18 01:32:03'),
(17, 10, 4, '2016-07-18 01:57:10', '2016-07-18 01:57:10'),
(18, 11, 4, '2016-07-18 01:58:43', '2016-07-18 01:58:43'),
(19, 12, 4, '2016-07-18 02:08:52', '2016-07-18 02:08:52'),
(20, 7, 6, '2016-07-18 02:43:17', '2016-07-18 02:43:17'),
(21, 9, 6, '2016-07-18 02:45:58', '2016-07-18 02:45:58'),
(22, 13, 2, '2016-07-18 02:49:01', '2016-07-18 02:49:01'),
(23, 13, 5, '2016-07-18 02:49:30', '2016-07-18 02:49:30');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(10) NOT NULL,
  `name` varchar(50) NOT NULL,
  `role` varchar(10) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `wtoken` varchar(20) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='User table';

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`, `role`, `email`, `phone`, `wtoken`, `created_at`, `updated_at`) VALUES
(1, 'jon', 'manager', 'nik.nikhassan+1@gmail.com', '6153637777', 'iamjon', '2016-07-15 00:00:00', '2016-07-15 00:00:00'),
(2, 'arya', 'employee', 'nik.nikhassan+2@gmail.com', '6347889999', 'iamarya', '2016-07-15 00:00:00', '2016-07-15 00:00:00'),
(3, 'dany', 'manager', 'nik.nikhassan+3@gmail.com', '6347848377', 'iamdany', '2016-07-15 00:00:00', '2016-07-15 00:00:00'),
(4, 'hodor', 'employee', 'nik.nikhassan+4@gmail.com', '6347329932', 'iamhodor', '2016-07-15 00:00:00', '2016-07-15 00:00:00'),
(5, 'bran', 'employee', 'nik.nikhassan+5@gmail.com', '6733736348', 'iambran', '2016-07-15 00:00:00', '2016-07-15 00:00:00'),
(6, 'rob', 'employee', 'nik.nikhassan+6@gmail.com', '8389909030', 'iamrob', '2016-07-15 00:00:00', '2016-07-15 00:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `shift`
--
ALTER TABLE `shift`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shift_assignment`
--
ALTER TABLE `shift_assignment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `shift`
--
ALTER TABLE `shift`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT for table `shift_assignment`
--
ALTER TABLE `shift_assignment`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
