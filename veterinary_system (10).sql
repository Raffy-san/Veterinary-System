-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 06, 2025 at 06:28 AM
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
-- Table structure for table `death_records`
--

CREATE TABLE `death_records` (
  `id` int(11) NOT NULL,
  `pet_id` int(11) UNSIGNED NOT NULL,
  `date_of_death` date NOT NULL,
  `time_of_death` time NOT NULL,
  `cause_of_death` text DEFAULT NULL,
  `recorded_by` text DEFAULT NULL,
  `location_of_death` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp(),
  `certificate_issued` tinyint(1) DEFAULT 0,
  `certificate_number` varchar(50) DEFAULT NULL,
  `certificate_date` date DEFAULT NULL,
  `issued_by` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `death_records`
--

INSERT INTO `death_records` (`id`, `pet_id`, `date_of_death`, `time_of_death`, `cause_of_death`, `recorded_by`, `location_of_death`, `remarks`, `created_at`, `updated_at`, `certificate_issued`, `certificate_number`, `certificate_date`, `issued_by`) VALUES
(2, 11, '2025-10-05', '20:24:00', 'testt', 'test', 'test', 'test', '2025-10-05 20:24:48', '2025-10-05 20:24:48', 1, 'DC-2025-0002', '2025-10-06', 1);

-- --------------------------------------------------------

--
-- Table structure for table `medical_records`
--

CREATE TABLE `medical_records` (
  `id` int(11) NOT NULL,
  `pet_id` int(10) UNSIGNED NOT NULL,
  `visit_type` text NOT NULL,
  `visit_date` date NOT NULL,
  `visit_time` time DEFAULT NULL,
  `veterinarian` text NOT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `weight_unit` enum('Kg','Lbs') DEFAULT NULL,
  `temperature` decimal(5,2) DEFAULT NULL,
  `temp_unit` enum('C','F') DEFAULT NULL,
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

INSERT INTO `medical_records` (`id`, `pet_id`, `visit_type`, `visit_date`, `visit_time`, `veterinarian`, `weight`, `weight_unit`, `temperature`, `temp_unit`, `diagnosis`, `treatment`, `medications`, `follow_up_date`, `notes`, `created_at`) VALUES
(13, 12, 'Vaccination', '2025-10-04', '14:44:00', 'Dr. Fernando Cadavero', 25.50, 'Lbs', 40.00, 'C', 'test', 'test', 'test', NULL, 'test', '2025-10-04 20:47:08'),
(14, 13, 'Vaccination', '2025-10-05', '14:20:00', 'Dr. Fernando Cadavero', 9.50, 'Lbs', 39.00, 'C', 'test', 'test', 'test', '2025-10-11', '', '2025-10-05 14:21:01'),
(15, 13, 'Treatment', '2025-10-05', '15:45:00', 'Dr. Fernando Cadavero', 1.00, 'Kg', 1.00, 'C', 'test', 't', 't', NULL, 't', '2025-10-05 14:45:44');

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
(4, 6, 'Alexis Montalbo', 'alexis@gmail.com', '09667928520', '09667928520', 'R. Kangleon Street, Brgy. Asuncion, Maasin City, Southern Leyte', '2025-10-01 06:34:35', 'Active'),
(5, 7, 'Rafael F. Sanoria', 'rafaelsanoria506@gmail.com', '09667928517', '09667928517', 'R. Kangleon Street, Brgy. Mambajao, Maasin City, Southern Leyte', '2025-09-30 06:03:23', 'Active'),
(7, 10, 'Alexander Dave Besin Fajardo', 'alexander.fajardo@gmail.com', '09667928518', '09667928518', 'R. Kangleon Street, Brgy. Mambajao, Maasin City, Southern Leyte', '2025-10-01 06:34:29', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `reset_by_admin_id` bigint(20) NOT NULL,
  `reset_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `reset_by_admin_id`, `reset_at`) VALUES
(1, 6, 1, '2025-10-02 05:02:25');

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
  `age_unit` enum('Days','Months','Years') DEFAULT NULL,
  `gender` enum('Male','Female') DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `weight_unit` enum('Kg','Lbs') DEFAULT NULL,
  `color` varchar(255) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `notes` longtext DEFAULT NULL,
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('Alive','Dead') NOT NULL DEFAULT 'Alive'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pets`
--

INSERT INTO `pets` (`id`, `owner_id`, `name`, `species`, `breed`, `age`, `age_unit`, `gender`, `weight`, `weight_unit`, `color`, `birth_date`, `notes`, `registered_at`, `status`) VALUES
(11, 4, 'Goldie', 'Dog', 'Golden Retriever', 5, 'Years', 'Male', 30.00, 'Kg', 'Gold', '2020-12-03', 'Nothing', '2025-10-05 12:24:48', 'Dead'),
(12, 5, 'Browny', 'Dog', 'German Sheperd', 10, 'Months', 'Female', 30.00, 'Lbs', 'Brown', '2015-05-20', 'none', '2025-10-04 12:16:31', 'Alive'),
(13, 7, 'Browskie', 'Cat', 'Persian', 4, 'Years', 'Male', 10.00, 'Lbs', 'Gray', '2021-03-05', 'Nothing', '2025-10-05 06:14:26', 'Alive');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) NOT NULL,
  `email` varchar(191) NOT NULL,
  `password` text NOT NULL,
  `access_type` enum('admin','owner') NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `access_type`, `status`) VALUES
(1, 'admin@gmail.com', '$2y$10$J9O4mBk/UTvjIavfGHjhWe3SKELXNFVkKv4MTQMc.tloxlHEa3P2u', 'admin', 'Active'),
(6, 'alexis@gmail.com', '$2y$10$CRrev8wLowL91mh9Hr7AO.i/prP5lsjxWs1mIipbZ2BFXigODW2x.', 'owner', 'Active'),
(7, 'rafaelsanoria506@gmail.com', '$2y$10$.vwk0tuOt2jUxO1mqQQ3d.onMgKh.M/xpulGplCekLgPQjfOpPq6C', 'owner', 'Active'),
(10, 'alexander.fajardo@gmail.com', '$2y$10$.gWrIWDEgY6lfHx3fT6uYeuBzlAjn7JasDbLpLyeGQrdGbAsYmg6C', 'owner', 'Active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `death_records`
--
ALTER TABLE `death_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pet_id` (`pet_id`),
  ADD KEY `fk_issued_by` (`issued_by`);

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
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_admin_id` (`reset_by_admin_id`),
  ADD KEY `fk_user_id` (`user_id`);

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
-- AUTO_INCREMENT for table `death_records`
--
ALTER TABLE `death_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `medical_records`
--
ALTER TABLE `medical_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `owners`
--
ALTER TABLE `owners`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pets`
--
ALTER TABLE `pets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `death_records`
--
ALTER TABLE `death_records`
  ADD CONSTRAINT `fk_issued_by` FOREIGN KEY (`issued_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_pet_id` FOREIGN KEY (`pet_id`) REFERENCES `pets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `fk_admin_id` FOREIGN KEY (`reset_by_admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pets`
--
ALTER TABLE `pets`
  ADD CONSTRAINT `owner` FOREIGN KEY (`owner_id`) REFERENCES `owners` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
