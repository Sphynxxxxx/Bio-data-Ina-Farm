-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 02, 2025 at 08:59 AM
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

--
-- Dumping data for table `competency_assessment`
--

INSERT INTO `competency_assessment` (`id`, `user_id`, `industry_sector`, `trade_area`, `occupation`, `classification_level`, `competency`, `specialization`) VALUES
(1, 7, 'qwqwqwqwqw', 'wqwq', 'wqwqwq', 'wqwqwqw', 'wqwqalsk', 'sdfewfdwf'),
(2, 9, 'qwqwqwqwqw', 'wqwq', 'wqwqwq', 'wqwqwqw', 'wqwqalsk', 'sdfewfdwf'),
(3, 10, 'qwqwqwqwqw', 'wqwq', 'wqwqwq', 'wqwqwqw', 'wqwqalsk', 'sdfewfdwf'),
(4, 11, 'qwqwqwqwqw', 'wqwq', 'wqwqwq', 'wqwqwqw', 'wqwqalsk', 'sdfewfdwf');

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

--
-- Dumping data for table `education`
--

INSERT INTO `education` (`id`, `user_id`, `school_name`, `educational_level`, `year_from`, `year_to`, `degree`, `major`, `minor`, `units_earned`, `honors`) VALUES
(1, 4, 'sasa', 'Elementary', '1212', '2121', '', '', '', '', ''),
(4, 7, 'talacuan elementary school', '', '2000', '2020', 'n/a', 'n/a', 'n/a', '66', '90'),
(6, 9, 'talacuan elementary school', '', '2000', '2020', 'n/a', 'n/a', 'n/a', '66', '90'),
(7, 10, 'talacuan elementary school', '', '2000', '2020', 'n/a', 'n/a', 'n/a', '66', '90'),
(8, 11, 'talacuan elementary school', '', '2000', '2020', 'n/a', 'n/a', 'n/a', '66', '90'),
(9, 12, 'talacuan elementary school', 'Secondary', '', '', '', '', '', '', '');

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

--
-- Dumping data for table `license_examination`
--

INSERT INTO `license_examination` (`id`, `user_id`, `license_tittle`, `year_taken`, `year_take`, `examination_venue`, `ratings`, `remarks`, `expiry_date`) VALUES
(1, 7, 'kahskhsas', '2025', NULL, 'ajisdjsdmw', '90', '90', '2025-03-26'),
(2, 9, 'kahskhsas', '2025', NULL, 'ajisdjsdmw', '90', '90', '2025-03-26'),
(3, 10, 'kahskhsas', '2025', NULL, 'ajisdjsdmw', '90', '90', '2025-03-26'),
(4, 11, 'kahskhsas', '2025', NULL, 'ajisdjsdmw', '90', '90', '2025-03-26'),
(5, 11, 'sasassa', '2025', NULL, 'sasasa', '90', '90', '2025-03-11');

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

--
-- Dumping data for table `training_seminar`
--

INSERT INTO `training_seminar` (`id`, `user_id`, `tittle`, `venue`, `inclusive_dates_past`, `inclusive_dates_present`, `certificate`, `no_of_hours`, `training_base`, `category`, `conducted_by`, `proficiency`) VALUES
(2, 7, 'sasas', 'sasa', '2025-03-11', '2025-03-15', 'asasasas', '12121', '890', 'iu212', '989', '98129819'),
(4, 9, 'sasas', 'sasa', '2025-03-11', '2025-03-15', 'asasasas', '12121', '890', 'iu212', '989', '98129819'),
(5, 10, 'sasas', 'sasa', '2025-03-11', '2025-03-15', 'asasasas', '12121', '890', 'iu212', '989', '98129819'),
(6, 11, 'sasas', 'sasa', '2025-03-11', '2025-03-15', 'asasasas', '12121', '890', 'iu212', '989', '98129819');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nmis_code`, `nmis_entry`, `lastname`, `firstname`, `middlename`, `address_street`, `address_barangay`, `address_district`, `address_city`, `address_province`, `address_region`, `address_zip`, `address_boxNo`, `sex`, `civil_status`, `contact_number`, `tel_number`, `email`, `fax_number`, `other_contact`, `fax`, `employment_type`, `employment_status`, `birthdate`, `birth_place`, `citizenship`, `religion`, `height`, `weight`, `blood_type`, `sss_no`, `gsis_no`, `tin_no`, `distinguish_marks`, `created_at`) VALUES
(4, '121212', NULL, 'aqwqw', 'qwqw', 'qqwqw', 'asasa', 'sasa', NULL, 'sasa', 'sasas', 'sas', '5000', NULL, 'Male', 'Single', '09123456777', NULL, 'qwerty@gmail.com', NULL, NULL, NULL, 'Employed', 'Regular', '0000-00-00', '', '', '', 162.00, 50.00, 'a', '666', '666', '66', NULL, '2025-02-08 16:30:31'),
(7, '131313', NULL, 'Uy', 'rynie', 'C', '1st street', 'talacuan', '2nd district', 'iloilo city', 'leon', '6', '5000', '090909', 'Male', '', '09165789087', '1234567890', 'rynie@gmail.com', '900990', '', NULL, 'Self-employed', 'Contractual', '0000-00-00', 'daswqw', 'american', 'catholic', 6.00, 55.00, 'b', '09897979', '78909008', '1235334', '1212121', '2025-03-02 07:29:57'),
(9, '131313', NULL, 'Uy', 'rynie', 'C', '1st street', 'talacuan', '2nd district', 'iloilo city', 'leon', '6', '5000', '090909', 'Male', '', '09165789087', '1234567890', 'rynie@gmail.com', '900990', '', NULL, 'Self-employed', 'Contractual', '0000-00-00', 'daswqw', 'american', 'catholic', 6.00, 55.00, 'b', '09897979', '78909008', '1235334', '1212121', '2025-03-02 07:33:57'),
(10, '131313', NULL, 'Uy', 'rynie', 'C', '1st street', 'talacuan', '2nd district', 'iloilo city', 'leon', '6', '5000', '090909', 'Male', '', '09165789087', '1234567890', 'rynie@gmail.com', '900990', '', NULL, 'Self-employed', 'Contractual', '0000-00-00', 'daswqw', 'american', 'catholic', 6.00, 55.00, 'b', '09897979', '78909008', '1235334', '1212121', '2025-03-02 07:34:53'),
(11, '131313', NULL, 'Uy', 'rynie', 'C', '1st street', 'talacuan', '2nd district', 'iloilo city', 'leon', '6', '5000', '090909', 'Male', '', '09165789087', '1234567890', 'rynie@gmail.com', '900990', '', NULL, 'Self-employed', 'Contractual', '0000-00-00', 'daswqw', 'american', 'catholic', 6.00, 55.00, 'b', '09897979', '78909008', '1235334', '1212121', '2025-03-02 07:43:15'),
(12, '131313', NULL, 'Uy', 'rynie', 'C', '1st street', 'talacuan', '2nd district', 'iloilo city', 'leon', '6', '5000', '090909', 'Male', 'Married', '', '', '', '', '', NULL, 'Employed', 'Temporary', '0000-00-00', '', '', '', 0.00, 0.00, '', '', '', '', '', '2025-03-02 07:47:16');

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
-- Dumping data for table `work_experience`
--

INSERT INTO `work_experience` (`id`, `company_name`, `position`, `inclusive_dates_past`, `inclusive_dates_present`, `monthly_salary`, `occupation`, `status`, `working_experience`, `user_id`) VALUES
(2, 'rynie condo', 'owner', '2025-03-02', '2025-03-21', 100000.00, 'wqwqwq', 'dskdnwkdnw', '100', 7),
(4, 'rynie condo', 'owner', '2025-03-02', '2025-03-21', 100000.00, 'wqwqwq', 'dskdnwkdnw', '100', 9),
(5, 'rynie condo', 'owner', '2025-03-02', '2025-03-21', 100000.00, 'wqwqwq', 'dskdnwkdnw', '100', 10),
(6, 'rynie condo', 'owner', '2025-03-02', '2025-03-21', 100000.00, 'wqwqwq', 'dskdnwkdnw', '100', 11);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `education`
--
ALTER TABLE `education`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `family_background`
--
ALTER TABLE `family_background`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `license_examination`
--
ALTER TABLE `license_examination`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `training_seminar`
--
ALTER TABLE `training_seminar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `work_experience`
--
ALTER TABLE `work_experience`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
-- Constraints for table `work_experience`
--
ALTER TABLE `work_experience`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
