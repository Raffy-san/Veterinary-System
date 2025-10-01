-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 01, 2025 at 08:44 AM
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
-- Database: `veterinary_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `medical_records`
--

CREATE TABLE `medical_records` (
  `id` int(11) NOT NULL,
  `pet_id` int(10) UNSIGNED NOT NULL,
  `visit_type` text NOT NULL,
  `visit_date` date NOT NULL,
  `veterinarian` text NOT NULL,
  `weight` text DEFAULT NULL,
  `temperature` text DEFAULT NULL,
  `diagnosis` text DEFAULT NULL,
  `treatment` text DEFAULT NULL,
  `medications` text DEFAULT NULL,
  `follow_up_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medical_records`
--

INSERT INTO `medical_records` (`id`, `pet_id`, `visit_type`, `visit_date`, `veterinarian`, `weight`, `temperature`, `diagnosis`, `treatment`, `medications`, `follow_up_date`, `notes`, `created_at`) VALUES
(8, 3, 'Routine Checkup', '2025-09-18', 'Dr. Fernando Cadavero', '20lbs', '40 C', 'adasdasdasdasdasdasdasdasdasdasdasdadasdasdasdasdasdasdasdasdasdas', 'asdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasd', '', NULL, '', '2025-09-18 20:33:14');

-- --------------------------------------------------------

--
-- Table structure for table `owners`
--

CREATE TABLE `owners` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `name` text NOT NULL,
  `email` text DEFAULT NULL,
  `phone` varchar(32) DEFAULT NULL,
  `emergency` varchar(32) DEFAULT NULL,
  `address` longtext NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `owners`
--

INSERT INTO `owners` (`id`, `user_id`, `name`, `email`, `phone`, `emergency`, `address`, `created_at`, `status`) VALUES
(4, 6, 'Alexis Montalbo', 'alexis@gmail.com', '09667928520', '09667928520', 'R. Kangleon Street, Brgy. Asuncion, Maasin City, Southern Leyte', '2025-10-01 06:34:35', 'Inactive'),
(5, 7, 'Rafael F. Sanoria', 'rafaelsanoria506@gmail.com', '09667928517', '09667928517', 'R. Kangleon Street, Brgy. Mambajao, Maasin City, Southern Leyte', '2025-09-30 06:03:23', 'Active'),
(7, 10, 'Alexander Dave Besin Fajardo', 'alexander.fajardo@gmail.com', '09667928518', '09667928518', 'R. Kangleon Street, Brgy. Mambajao, Maasin City, Southern Leyte', '2025-10-01 06:34:29', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `pets`
--

CREATE TABLE `pets` (
  `id` int(10) UNSIGNED NOT NULL,
  `owner_id` bigint(20) DEFAULT NULL,
  `name` text NOT NULL,
  `species` text NOT NULL,
  `breed` text DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `gender` enum('Male','Female') DEFAULT NULL,
  `weight` varchar(255) DEFAULT NULL,
  `color` varchar(255) DEFAULT NULL,
  `notes` longtext DEFAULT NULL,
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pets`
--

INSERT INTO `pets` (`id`, `owner_id`, `name`, `species`, `breed`, `age`, `gender`, `weight`, `color`, `notes`, `registered_at`) VALUES
(3, 4, 'Goldie', 'Dog', 'Golden Retriever', 4, 'Male', '30 lbs', 'Golden', 'He tends to be cuddle with other people and sometimes can cause harm He tends to be cuddle with other people and sometimes can cause harmHe tends to be cuddle with other people and sometimes can cause harmHe tends to be cuddle with other people and sometimes can cause harmHe tends to be cuddle with other people and sometimes can cause harm', '2025-09-18 08:33:58'),
(5, 4, 'Browniesss', 'Dog', 'German Sheperd', 5, 'Male', '30 lbs', 'Brown', 'nothing', '2025-09-18 07:18:14'),
(6, 4, 'Danny', 'Cat', 'Persian', 2, 'Male', '20lbs', 'Gray', 'asdasdasdasdasasdasdasdasdasasdasdasdasdasasdasdasdasdasasdasdasdasdasasdasdasdasdasasdasdasdasdasasdasdasdasdasasdasdasdasdasasdasdasdasdasasdasdasdasdasasdasdasdasdasasdasdasdasdasasdasdasdasdasasdasdasdasdasasdasdasdasdasasdasdasdasdasasdasdasdasdasasdasdasdasdasasdasdasdasdasasdasdasdasdasasdasdasdasdasasdasdasdasdas', '2025-09-18 12:43:49'),
(7, 4, 'Grayye', 'Bird', 'Woodpecker', 2, 'Female', '5 lbs', 'red', 'nothing', '2025-09-18 07:13:50');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) NOT NULL,
  `email` varchar(191) NOT NULL,
  `password` text NOT NULL,
  `access_type` enum('admin','owner') NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `access_type`, `status`, `reset_token`, `reset_expires`) VALUES
(1, 'admin@gmail.com', '$2y$10$J9O4mBk/UTvjIavfGHjhWe3SKELXNFVkKv4MTQMc.tloxlHEa3P2u', 'admin', 'Active', NULL, NULL),
(6, 'alexis@gmail.com', '$2y$10$XLbhgAPdgOclSnydHxnAoubQneB3Oq8W8eMPUtgHU8pDnHscEVBdq', 'owner', 'Inactive', NULL, NULL),
(7, 'rafaelsanoria506@gmail.com', '$2y$10$.vwk0tuOt2jUxO1mqQQ3d.onMgKh.M/xpulGplCekLgPQjfOpPq6C', 'owner', 'Active', NULL, NULL),
(10, 'alexander.fajardo@gmail.com', '$2y$10$.gWrIWDEgY6lfHx3fT6uYeuBzlAjn7JasDbLpLyeGQrdGbAsYmg6C', 'owner', 'Active', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `medical_records`
--
ALTER TABLE `medical_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_medrec_pet_id` (`pet_id`);

--
-- Indexes for table `owners`
--
ALTER TABLE `owners`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user` (`user_id`);

--
-- Indexes for table `pets`
--
ALTER TABLE `pets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `owner` (`owner_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `medical_records`
--
ALTER TABLE `medical_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `owners`
--
ALTER TABLE `owners`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pets`
--
ALTER TABLE `pets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `medical_records`
--
ALTER TABLE `medical_records`
  ADD CONSTRAINT `fk_medrec_pet` FOREIGN KEY (`pet_id`) REFERENCES `pets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `owners`
--
ALTER TABLE `owners`
  ADD CONSTRAINT `user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pets`
--
ALTER TABLE `pets`
  ADD CONSTRAINT `owner` FOREIGN KEY (`owner_id`) REFERENCES `owners` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
