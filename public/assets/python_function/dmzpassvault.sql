-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 14, 2025 at 12:34 PM
-- Server version: 11.6.2-MariaDB-log
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS dmzpassvault;
CREATE USER IF NOT EXISTS vault_adm IDENTIFIED BY 'V@ult_4dm12345';
GRANT ALL PRIVILEGES ON dmzpassvault.* TO vault_adm;
GRANT ALTER ON mysql.* TO vault_adm;
GRANT CREATE USER ON *.* TO vault_adm;

USE dmzpassvault;

--
-- Database: `dmzpassvault`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_accounts`
--

CREATE TABLE `admin_accounts` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `admin_accounts`
--

INSERT INTO `admin_accounts` (`id`, `username`, `email`, `password_hash`, `created_at`) VALUES
(1, 'admin', 'admin@example.com', 'hashed_password_here', '2025-06-14 18:01:51');

-- --------------------------------------------------------

--
-- Table structure for table `identities`
--

CREATE TABLE `identities` (
  `id` varchar(10) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `platform_id` varchar(10) NOT NULL,
  `hostname` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `functionality` varchar(100) DEFAULT NULL,
  `ip_addr_srv` varchar(45) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_audit_logs`
--

CREATE TABLE `password_audit_logs` (
  `id` int(11) NOT NULL,
  `identity_id` varchar(10) NOT NULL,
  `event_type` enum('created','updated','accessed') NOT NULL,
  `event_time` datetime DEFAULT current_timestamp(),
  `triggered_by` varchar(100) DEFAULT NULL,
  `actor_ip_addr` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_jobs`
--

CREATE TABLE `password_jobs` (
  `id` int(11) NOT NULL,
  `identity_id` varchar(10) NOT NULL,
  `scheduled_at` datetime NOT NULL,
  `status` enum('pending','completed','failed') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_vaults`
--

CREATE TABLE `password_vaults` (
  `id` varchar(10) NOT NULL,
  `identity_id` varchar(10) NOT NULL,
  `encrypted_password` blob NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `last_accessed_at` datetime DEFAULT NULL,
  `last_changed_at` datetime DEFAULT current_timestamp(),
  `last_changed_by` varchar(100) DEFAULT NULL,
  `last_changed_ip` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;


--
-- Table structure for table `platforms`
--

CREATE TABLE `platforms` (
  `id` varchar(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `platforms`
--

INSERT INTO `platforms` (`id`, `name`, `description`) VALUES
('PF001', 'Linux', 'Linux-based server platform');
INSERT INTO `platforms` (`id`, `name`, `description`) VALUES
('PF002', 'MariaDB', 'Database platform');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_accounts`
--
ALTER TABLE `admin_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `identities`
--
ALTER TABLE `identities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `platform_id` (`platform_id`);

--
-- Indexes for table `password_audit_logs`
--
ALTER TABLE `password_audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `identity_id` (`identity_id`);

--
-- Indexes for table `password_jobs`
--
ALTER TABLE `password_jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `identity_id` (`identity_id`);

--
-- Indexes for table `password_vaults`
--
ALTER TABLE `password_vaults`
  ADD PRIMARY KEY (`id`),
  ADD KEY `identity_id` (`identity_id`);

--
-- Indexes for table `platforms`
--
ALTER TABLE `platforms`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_accounts`
--
ALTER TABLE `admin_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `password_audit_logs`
--
ALTER TABLE `password_audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `password_jobs`
--
ALTER TABLE `password_jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `identities`
--
ALTER TABLE `identities`
  ADD CONSTRAINT `identities_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin_accounts` (`id`),
  ADD CONSTRAINT `identities_ibfk_2` FOREIGN KEY (`platform_id`) REFERENCES `platforms` (`id`);

--
-- Constraints for table `password_audit_logs`
--
ALTER TABLE `password_audit_logs`
  ADD CONSTRAINT `password_audit_logs_ibfk_1` FOREIGN KEY (`identity_id`) REFERENCES `identities` (`id`);

--
-- Constraints for table `password_jobs`
--
ALTER TABLE `password_jobs`
  ADD CONSTRAINT `password_jobs_ibfk_1` FOREIGN KEY (`identity_id`) REFERENCES `identities` (`id`);

--
-- Constraints for table `password_vaults`
--
ALTER TABLE `password_vaults`
  ADD CONSTRAINT `password_vaults_ibfk_1` FOREIGN KEY (`identity_id`) REFERENCES `identities` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
