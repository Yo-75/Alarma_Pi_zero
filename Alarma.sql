-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 30, 2024 at 09:43 AM
-- Server version: 8.0.32-0ubuntu0.20.04.2
-- PHP Version: 7.4.3-4ubuntu2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `Alarma`
--

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int NOT NULL,
  `event` varchar(25) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `details` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `data` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `events`
--

-- --------------------------------------------------------

--
-- Table structure for table `Logins`
--

CREATE TABLE `Logins` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `data` datetime NOT NULL,
  `IP` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `mails`
--

CREATE TABLE `mails` (
  `id` int NOT NULL,
  `mesaj` varchar(255) NOT NULL,
  `data` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `presiune`
--

CREATE TABLE `presiune` (
  `id` int NOT NULL,
  `value` int NOT NULL,
  `data` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;


-- --------------------------------------------------------

--
-- Table structure for table `Useri`
--

CREATE TABLE `Useri` (
  `id` int NOT NULL,
  `nume` varchar(50) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(120) NOT NULL,
  `activ` bit(1) NOT NULL DEFAULT b'0',
  `Is_admin` bit(1) NOT NULL DEFAULT b'0',
  `last_login` datetime DEFAULT NULL,
  `image` varchar(20) NOT NULL,
  `Can_View_Records` bit(1) NOT NULL DEFAULT b'0',
  `Can_Delete_Records` bit(1) NOT NULL DEFAULT b'0',
  `Can_Modify_Times` bit(1) NOT NULL DEFAULT b'0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;



INSERT INTO `Useri` (`id`, `nume`, `username`, `password`, `activ`, `Is_admin`, `last_login`, `image`, `Can_View_Records`, `Can_Delete_Records`, `Can_Modify_Times`) VALUES
(1, 'Eugen Stefan', 'Eu', 'f561aaf6ef0bf14d4208bb46a4ccb3ad', b'1', b'1', '2021-10-23 18:55:55', 'ktsnu3d01.png', b'0', b'0', b'0'),
(3, 'test', 'test', 'f561aaf6ef0bf14d4208bb46a4ccb3ad', b'1', b'0', '2021-02-12 20:25:26', 'jra8nqua3.png', b'1', b'1', b'1');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Logins`
--
ALTER TABLE `Logins`
  ADD PRIMARY KEY (`id`),
  ADD KEY `key_Useri_Logins_1` (`user_id`);

--
-- Indexes for table `mails`
--
ALTER TABLE `mails`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `presiune`
--
ALTER TABLE `presiune`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setari`
--
ALTER TABLE `setari`
  ADD PRIMARY KEY (`parametru`);

--
-- Indexes for table `temperatura`
--
ALTER TABLE `temperatura`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Useri`
--
ALTER TABLE `Useri`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Logins`
--
ALTER TABLE `Logins`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mails`
--
ALTER TABLE `mails`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Useri`
--
ALTER TABLE `Useri`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Logins`
--
ALTER TABLE `Logins`
  ADD CONSTRAINT `key_Useri_Logins_1` FOREIGN KEY (`user_id`) REFERENCES `Useri` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
