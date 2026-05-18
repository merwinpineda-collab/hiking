-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 11, 2026 at 06:35 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_hiking1`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_activity_log`
--

CREATE TABLE `tb_activity_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `actor_type` enum('admin','user','system') NOT NULL DEFAULT 'user',
  `actor_email` varchar(255) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `detail` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(512) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_admin`
--

CREATE TABLE `tb_admin` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_admin`
--

INSERT INTO `tb_admin` (`admin_id`, `username`, `email`, `password`, `created_at`) VALUES
(1, 'admin', 'admin@wilderpath.com', '123456', '2026-04-18 14:06:06'),
(3, 'admin', 'admin@email.com', '123456', '2026-04-18 14:07:21');

-- --------------------------------------------------------

--
-- Table structure for table `tb_analytics_log`
--

CREATE TABLE `tb_analytics_log` (
  `log_id` int(11) NOT NULL,
  `event_type` varchar(80) NOT NULL,
  `event_data` text DEFAULT NULL,
  `user_email` varchar(150) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_reserve`
--

CREATE TABLE `tb_reserve` (
  `reserve_id` int(11) NOT NULL,
  `fullname` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `number` varchar(20) NOT NULL,
  `num_people` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `difficulty` enum('Easy','Moderate','Hard') NOT NULL DEFAULT 'Easy',
  `notes` text DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled') NOT NULL DEFAULT 'pending',
  `guide_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `confirmed_at` datetime DEFAULT NULL,
  `email_sent` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_tourguides`
--

CREATE TABLE `tb_tourguides` (
  `guide_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `age` tinyint(3) UNSIGNED NOT NULL DEFAULT 18,
  `gender` enum('Male','Female','Other') NOT NULL,
  `address` text NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(150) NOT NULL,
  `experience_years` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `specialization` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `contact_details` text DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_userinfo`
--

CREATE TABLE `tb_userinfo` (
  `user_id` int(11) NOT NULL,
  `firstname` varchar(80) NOT NULL,
  `middlename` varchar(80) DEFAULT NULL,
  `lastname` varchar(80) NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `age` tinyint(3) UNSIGNED NOT NULL,
  `number` varchar(20) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_userinfo`
--

INSERT INTO `tb_userinfo` (`user_id`, `firstname`, `middlename`, `lastname`, `gender`, `age`, `number`, `email`, `password`, `is_active`, `created_at`) VALUES
(1, 'Arvie ', 'Catedrilla', 'Ramos', 'Male', 21, '09667115373', 'arvie@gmail.com', '123456', 1, '2026-05-11 15:31:00');

-- --------------------------------------------------------

--
-- Table structure for table `walkin_bookings`
--

CREATE TABLE `walkin_bookings` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `contact_number` varchar(50) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `booking_date` date NOT NULL,
  `time_slot` varchar(100) NOT NULL,
  `number_of_guests` int(11) NOT NULL,
  `service_name` varchar(255) DEFAULT NULL,
  `booking_type` enum('Walk-in') NOT NULL DEFAULT 'Walk-in',
  `booking_status` enum('Pending','Confirmed','Completed') NOT NULL DEFAULT 'Confirmed',
  `payment_status` enum('Paid','Pending') NOT NULL DEFAULT 'Paid',
  `payment_method` enum('Cash','GCash','Card') NOT NULL DEFAULT 'Cash',
  `amount_paid` decimal(10,2) DEFAULT NULL,
  `created_by_admin` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `walkin_bookings`
--

INSERT INTO `walkin_bookings` (`id`, `full_name`, `contact_number`, `email`, `booking_date`, `time_slot`, `number_of_guests`, `service_name`, `booking_type`, `booking_status`, `payment_status`, `payment_method`, `amount_paid`, `created_by_admin`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'Guian Latoza', '09667115373', 'arvie@gmail.com', '2026-05-19', '7:00', 100, 'emba', 'Walk-in', 'Confirmed', 'Paid', 'Cash', 100000.00, NULL, 'srgwrgh', '2026-05-11 15:46:20', '2026-05-11 15:46:20');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_activity_log`
--
ALTER TABLE `tb_activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_actor` (`actor_email`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_time` (`created_at`);

--
-- Indexes for table `tb_admin`
--
ALTER TABLE `tb_admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `tb_analytics_log`
--
ALTER TABLE `tb_analytics_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_event_type` (`event_type`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `tb_reserve`
--
ALTER TABLE `tb_reserve`
  ADD PRIMARY KEY (`reserve_id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_date` (`date`),
  ADD KEY `fk_guide_id` (`guide_id`);

--
-- Indexes for table `tb_tourguides`
--
ALTER TABLE `tb_tourguides`
  ADD PRIMARY KEY (`guide_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `tb_userinfo`
--
ALTER TABLE `tb_userinfo`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `walkin_bookings`
--
ALTER TABLE `walkin_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_booking_date` (`booking_date`),
  ADD KEY `idx_booking_status` (`booking_status`),
  ADD KEY `idx_payment_status` (`payment_status`),
  ADD KEY `idx_created_by_admin` (`created_by_admin`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_activity_log`
--
ALTER TABLE `tb_activity_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_admin`
--
ALTER TABLE `tb_admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tb_analytics_log`
--
ALTER TABLE `tb_analytics_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_reserve`
--
ALTER TABLE `tb_reserve`
  MODIFY `reserve_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_tourguides`
--
ALTER TABLE `tb_tourguides`
  MODIFY `guide_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_userinfo`
--
ALTER TABLE `tb_userinfo`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `walkin_bookings`
--
ALTER TABLE `walkin_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_reserve`
--
ALTER TABLE `tb_reserve`
  ADD CONSTRAINT `fk_reserve_guide` FOREIGN KEY (`guide_id`) REFERENCES `tb_tourguides` (`guide_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `walkin_bookings`
--
ALTER TABLE `walkin_bookings`
  ADD CONSTRAINT `fk_walkin_created_by_admin` FOREIGN KEY (`created_by_admin`) REFERENCES `tb_admin` (`admin_id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
