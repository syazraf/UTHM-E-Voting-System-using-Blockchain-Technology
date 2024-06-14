-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 11, 2024 at 08:00 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

USE `uthm_vote_system`;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `uthm_vote_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `login_attempts` int(11) DEFAULT 0,
  `last_login_attempt` timestamp NULL DEFAULT NULL,
  `verification_code` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `login_attempts`, `last_login_attempt`, `verification_code`) VALUES
(1, 'syazsyaraf', '$2y$10$D2ajNYh/8aHpwbKcuXsQ.u6Xjm3.e8UpnF8hJkFytNC/EtTyyfUZK', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `name`, `date`, `created_at`) VALUES
(1, 'Mpp UTHM', '2024-06-11', '2024-06-11 04:37:23'),
(2, 'Kelab Kepimpinan', '2024-06-12', '2024-06-11 04:37:23'),
(3, 'Section 1 Leader', '2024-06-12', '2024-06-11 04:37:23');

-- --------------------------------------------------------

--
-- Table structure for table `candidates`
--

CREATE TABLE `candidates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `matric_number` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `event_id` int(11) NOT NULL,
  `status` enum('pending','verified') DEFAULT 'pending',
  `verification_code` varchar(10) NOT NULL,
  `manifesto` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `login_attempts` int(11) DEFAULT 0,
  `last_login_attempt` timestamp NULL DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`),
  CONSTRAINT `candidates_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `candidates`
--

INSERT INTO `candidates` (`id`, `matric_number`, `name`, `email`, `gender`, `phone_number`, `password`, `event_id`, `status`, `verification_code`, `manifesto`, `created_at`, `login_attempts`, `last_login_attempt`, `profile_picture`) VALUES
(1, 'AI210033', 'AHMAD', 'ahmad33@gmail.com', 'male', '012-3342121', '$2y$10$TrT9KGaERFufNQdIOjvi7e3XwI4X5rcyxYAU/WYOYO.hLaCxa.2My', 2, 'verified', '770767', NULL, '2024-06-11 04:50:15', 0, NULL, NULL),
(2, 'AI219980', 'MANAP', 'manappp@gmail.com', 'male', '019-2225643', '$2y$10$u61boL0SWiUCkaT81LTs/e6bBg3Xz2P5ql3/8Jq9mkJ40XUCZHvbS', 1, 'verified', '990007', NULL, '2024-06-11 05:11:30', 0, NULL, NULL),
(3, 'AI213444', 'BORA', 'b0r4@gmail.com', 'female', '017-4325516', '$2y$10$eg.jv8tJPtQa7B/RHFpTAe3v9FhgfJuco2keeFqQ00xFhGAOaNqQe', 3, 'verified', '318055', NULL, '2024-06-11 07:05:55', 0, NULL, NULL),
(4, 'AI213442', 'BOB', 'bob22@gmail.com', 'male', '018-2345432', '$2y$10$FEg568R6Cprh4zFKhzPBfOiyy8jszqmE47gUvkrXL7S4FiXKSjCx2', 1, 'verified', '254148', NULL, '2024-06-11 15:26:30', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sms_verifications`
--

CREATE TABLE `sms_verifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `phone_number` varchar(20) NOT NULL,
  `code` varchar(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `matric_number` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `status` enum('pending','verified') DEFAULT 'pending',
  `verification_code` varchar(10) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `login_attempts` int(11) DEFAULT 0,
  `last_login_attempt` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `matric_number` (`matric_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `matric_number`, `name`, `email`, `password`, `gender`, `phone_number`, `status`, `verification_code`, `profile_picture`, `registered_at`, `login_attempts`, `last_login_attempt`) VALUES
(1, 'AI210987', 'AMANDA', 'amanda44@gmail.com', '$2y$10$AVIcMHq2eRir2i3pT6MncuxxIdZxkwz9wlCiNR2zfQVxp5azPt8gC', 'female', '015-4321154', 'verified', '489978', NULL, '2024-06-11 04:53:45', 2, '2024-06-11 12:57:21'),
(2, 'AI218897', 'THOMAS', 'thom44@gmail.com', '$2y$10$fBgS6FAKjuaWpGuYPvrNG.1GyIZ6UQVSNuGPiIrfSpETHlMv6hay2', 'male', '013-2220987', 'verified', '416858', NULL, '2024-06-11 05:14:59', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE `votes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `voter_id_hash` varchar(64) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `candidate_id` (`candidate_id`),
  FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--

ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `candidates`
--

ALTER TABLE `candidates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `events`
--

ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sms_verifications`
--

ALTER TABLE `sms_verifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `students`
--

ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `votes`
--

ALTER TABLE `votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
