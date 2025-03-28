-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 23, 2025 at 09:11 PM
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
-- Database: `biodata_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `competency_assessment`
--

CREATE TABLE `competency_assessment` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `industry_sector` varchar(255) DEFAULT NULL,
  `trade_area` varchar(255) DEFAULT NULL,
  `occupation` varchar(255) DEFAULT NULL,
  `classification_level` varchar(255) DEFAULT NULL,
  `competency` varchar(255) DEFAULT NULL,
  `specialization` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `education`
--

CREATE TABLE `education` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `school_name` varchar(100) DEFAULT NULL,
  `educational_level` varchar(50) DEFAULT NULL,
  `year_from` varchar(4) DEFAULT NULL,
  `year_to` varchar(4) DEFAULT NULL,
  `degree` varchar(100) DEFAULT NULL,
  `major` varchar(100) DEFAULT NULL,
  `minor` varchar(100) DEFAULT NULL,
  `units_earned` varchar(20) DEFAULT NULL,
  `honors` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `family_background`
--

CREATE TABLE `family_background` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `spouse_name` varchar(100) DEFAULT NULL,
  `spouse_educational_attainment` varchar(100) DEFAULT NULL,
  `spouse_occupation` varchar(100) DEFAULT NULL,
  `spouse_monthly_income` decimal(10,2) DEFAULT NULL,
  `father_name` varchar(100) DEFAULT NULL,
  `father_educational_attainment` varchar(100) DEFAULT NULL,
  `father_occupation` varchar(100) DEFAULT NULL,
  `father_monthly_income` decimal(10,2) DEFAULT NULL,
  `mother_name` varchar(100) DEFAULT NULL,
  `mother_educational_attainment` varchar(100) DEFAULT NULL,
  `mother_occupation` varchar(100) DEFAULT NULL,
  `mother_monthly_income` decimal(10,2) DEFAULT NULL,
  `guardian_name` varchar(100) DEFAULT NULL,
  `guardian_educational_attainment` varchar(100) DEFAULT NULL,
  `guardian_occupation` varchar(100) DEFAULT NULL,
  `guardian_monthly_income` decimal(10,2) DEFAULT NULL,
  `dependents` varchar(255) DEFAULT NULL,
  `dependents_age` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `license_examination`
--

CREATE TABLE `license_examination` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `license_tittle` varchar(255) DEFAULT NULL,
  `year_taken` varchar(255) DEFAULT NULL,
  `year_take` varchar(255) DEFAULT NULL,
  `examination_venue` varchar(255) DEFAULT NULL,
  `ratings` varchar(255) DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `expiry_date` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `training_seminar`
--

CREATE TABLE `training_seminar` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tittle` varchar(255) NOT NULL,
  `venue` varchar(255) NOT NULL,
  `inclusive_dates_past` varchar(50) DEFAULT NULL,
  `inclusive_dates_present` varchar(50) DEFAULT NULL,
  `certificate` varchar(50) DEFAULT NULL,
  `no_of_hours` varchar(50) DEFAULT NULL,
  `training_base` varchar(50) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `conducted_by` varchar(50) DEFAULT NULL,
  `proficiency` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nmis_code` varchar(20) DEFAULT NULL,
  `nmis_entry` int(255) DEFAULT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `firstname` varchar(50) DEFAULT NULL,
  `middlename` varchar(50) DEFAULT NULL,
  `address_street` varchar(100) DEFAULT NULL,
  `address_barangay` varchar(100) DEFAULT NULL,
  `address_district` varchar(255) DEFAULT NULL,
  `address_city` varchar(100) DEFAULT NULL,
  `address_province` varchar(100) DEFAULT NULL,
  `address_region` varchar(10) DEFAULT NULL,
  `address_zip` varchar(10) DEFAULT NULL,
  `address_boxNo` varchar(255) DEFAULT NULL,
  `sex` enum('Male','Female') DEFAULT NULL,
  `civil_status` enum('Single','Married','Widowed','Separated') DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `tel_number` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `fax_number` varchar(255) DEFAULT NULL,
  `other_contact` varchar(255) DEFAULT NULL,
  `fax` varchar(255) DEFAULT NULL,
  `employment_type` enum('Employed','Self-employed','Unemployed') DEFAULT NULL,
  `employment_status` enum('Probationary','Regular','Permanent','Contractual','Temporary') DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `birth_place` varchar(100) DEFAULT NULL,
  `citizenship` varchar(50) DEFAULT NULL,
  `religion` varchar(50) DEFAULT NULL,
  `height` decimal(5,2) DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `blood_type` varchar(5) DEFAULT NULL,
  `sss_no` varchar(20) DEFAULT NULL,
  `gsis_no` varchar(20) DEFAULT NULL,
  `tin_no` varchar(20) DEFAULT NULL,
  `distinguish_marks` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `program_type` enum('internship','tesda') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_photos`
--

CREATE TABLE `user_photos` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `photo_data` longtext DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_signatures`
--

CREATE TABLE `user_signatures` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `signature_data` text NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `work_experience`
--

CREATE TABLE `work_experience` (
  `id` int(11) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `inclusive_dates_past` varchar(50) DEFAULT NULL,
  `inclusive_dates_present` varchar(50) DEFAULT NULL,
  `monthly_salary` decimal(10,2) DEFAULT NULL,
  `occupation` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `working_experience` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `competency_assessment`
--
ALTER TABLE `competency_assessment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_competency_user` (`user_id`);

--
-- Indexes for table `education`
--
ALTER TABLE `education`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `family_background`
--
ALTER TABLE `family_background`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `license_examination`
--
ALTER TABLE `license_examination`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_license_user` (`user_id`);

--
-- Indexes for table `training_seminar`
--
ALTER TABLE `training_seminar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_training_user` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_photos`
--
ALTER TABLE `user_photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_signatures`
--
ALTER TABLE `user_signatures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_signatures_ibfk_1` (`user_id`);

--
-- Indexes for table `work_experience`
--
ALTER TABLE `work_experience`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `competency_assessment`
--
ALTER TABLE `competency_assessment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `education`
--
ALTER TABLE `education`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `family_background`
--
ALTER TABLE `family_background`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `license_examination`
--
ALTER TABLE `license_examination`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `training_seminar`
--
ALTER TABLE `training_seminar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `user_photos`
--
ALTER TABLE `user_photos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `user_signatures`
--
ALTER TABLE `user_signatures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `work_experience`
--
ALTER TABLE `work_experience`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `competency_assessment`
--
ALTER TABLE `competency_assessment`
  ADD CONSTRAINT `fk_competency_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `education`
--
ALTER TABLE `education`
  ADD CONSTRAINT `education_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `family_background`
--
ALTER TABLE `family_background`
  ADD CONSTRAINT `family_background_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `license_examination`
--
ALTER TABLE `license_examination`
  ADD CONSTRAINT `fk_license_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `training_seminar`
--
ALTER TABLE `training_seminar`
  ADD CONSTRAINT `fk_training_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_photos`
--
ALTER TABLE `user_photos`
  ADD CONSTRAINT `user_photos_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_signatures`
--
ALTER TABLE `user_signatures`
  ADD CONSTRAINT `user_signatures_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `work_experience`
--
ALTER TABLE `work_experience`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
