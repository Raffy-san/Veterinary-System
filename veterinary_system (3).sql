-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 30, 2025 at 09:33 AM
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

INSERT INTO `medical_records` (`id`, `pet_id`, `visit_type`, `visit_date`, `weight`, `temperature`, `diagnosis`, `treatment`, `medications`, `follow_up_date`, `notes`, `created_at`) VALUES
(5, 12, 'Treatment', '2025-08-29', '30 lbs', '40 C', 'test', 'test', 'test', NULL, 'test', '2025-08-29 18:25:14'),
(6, 18, 'Vaccination', '2025-08-30', '20 lbs', '40 C', 'The Cat was bittten by the dog and became weak', 'test', 'test', '2025-09-08', 'test', '2025-08-29 18:55:21');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('active','inactive') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `owners`
--

INSERT INTO `owners` (`id`, `user_id`, `name`, `email`, `phone`, `emergency`, `address`, `created_at`, `status`) VALUES
(2, 5, 'Rafael F. Sanoria', 'rafaelsanoria506@gmail.com', '09667928517', '9667928517', 'R. Kangleon Street, Brgy. Mambajao, Maasin City, Southern Leyte', '2025-08-19 14:59:06', 'active'),
(7, 10, 'Alexander Dave Besin Fajardo', 'alexander.fajardo@gmail.com', '09667928519', '09667928519', 'R. Kangleon Street, Brgy. Mambajao, Maasin City, Southern Leyte', '2025-08-27 08:36:48', 'active');

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
  `gender` text DEFAULT NULL CHECK (`gender` in ('male','female','unknown')),
  `weight` varchar(255) DEFAULT NULL,
  `color` varchar(255) DEFAULT NULL,
  `notes` longtext DEFAULT NULL,
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pets`
--

INSERT INTO `pets` (`id`, `owner_id`, `name`, `species`, `breed`, `age`, `gender`, `weight`, `color`, `notes`, `registered_at`) VALUES
(12, 2, 'Danny', 'Dog', 'Golden Retriever', 4, 'male', '30 lbs', 'Golden', 'Likes to cuddle with other people and can be very aggresive when playing with others.', '2025-08-21 06:53:22'),
(18, 7, 'Broskie', 'Cat', 'Persian', 5, 'male', '30 lbs', 'Gray', 'He likes to cuddle and play with other people', '2025-08-29 10:54:25'),
(19, 7, 'melissa', 'Bird', 'Woodpecker', 2, 'female', '5 lbs', 'red', 'sometimes a bit loud in the night', '2025-08-30 07:03:06');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) NOT NULL,
  `username` varchar(191) NOT NULL,
  `password` text NOT NULL,
  `access_type` enum('admin','owner') NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `access_type`, `status`) VALUES
(2, 'admin123', '$2y$10$fzbZet12ooCL8O7EIeaB7O.TdYdFLrOCotUptRCaZvz7rl8jjHTlu', 'admin', 'active'),
(5, 'rafael123', '$2y$10$B6BXuzDGBnuLx6Q6CXZYsO5FTbrI1lFd3kBHiQmY0Zdh.jmDFu0iO', 'owner', 'active'),
(10, 'alexander123', '$2y$10$Y8lBwUB/vD19vshg0bSRI.o/mn6gUxLyWNAvPU5FEHOEh3HXYUAOW', 'owner', 'active');

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
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `medical_records`
--
ALTER TABLE `medical_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `owners`
--
ALTER TABLE `owners`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `pets`
--
ALTER TABLE `pets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

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
