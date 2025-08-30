-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 27, 2025 at 07:11 PM
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
-- Database: `med_platform`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `username`, `password`) VALUES
(1, 'admin', '0192023a7bbd73250516f069df18b500');

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `appointment_id` int(10) UNSIGNED NOT NULL,
  `patient_id` int(10) UNSIGNED NOT NULL,
  `doctor_id` int(10) UNSIGNED NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `symptoms` text DEFAULT NULL,
  `report_path` varchar(512) DEFAULT NULL,
  `status` enum('Pending','Accepted','Declined') NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `accepted_at` timestamp NULL DEFAULT NULL,
  `report_text` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`appointment_id`, `patient_id`, `doctor_id`, `appointment_date`, `appointment_time`, `symptoms`, `report_path`, `status`, `created_at`, `updated_at`, `accepted_at`, `report_text`) VALUES
(19, 3, 4, '0001-11-11', '11:01:00', '111', 'uploads/reports/1756235196_life_story__3_.pdf', 'Accepted', '2025-08-26 19:06:36', NULL, '2025-08-26 19:06:45', 'Title: From the Cradle of Resilience: Shivam Tanaji Shirsath\'s Journey In the heart of a bustling village nestled between the verdant hills of India, a star was born. His name was Shivam Tanaji Shirsath, the first-born son of Tanaji Santosh Shirsath, a humble farmer, and Kaushalya, a teacher who nurtured dreams with the same care as she did the tender plants in their small garden. Born in the inspired halls of Boys Town Public School, Shivam\'s curious mind and insatiable thirst for knowledge ignited a flame within him, guiding him towards a future filled with promise. As he grew from a chubby-cheeked toddler into a responsible and disciplined young man, he was cultivated by the warm embrace of his family and the nourishing soil of his roots. Armed with a thirst for knowledge and the unwavering support of his parents, Shivam embarked on a journey to Sandip Institute of Engineering and Management. His eyes burned with determination as he readied himself for the challenge that lay before him. The date, September 5th, 2024, marked the dawn of his transformation into an engineer, a scholar, and a beacon of hope for his family and community. Shivam withstood the trials and tribulations of the academic world with a resilience forged from the fires of adversity. He mastered the languages of the world - Marathi, Hindi, and English - and learnt to dance with the infinity of technology, engineering, and programming languages such as PHP, MySQL, Oracle, Python, Java, and its advanced verbose. His journey was not without hardship, for every peak there is a valley to conquer, and every success is a testament to the trials that came before. However, Shivam discovered that it was his failures that often served as the stepping stones for his successes. Whether it was a stumble in his studies or a misstep in his ambitions, Shivam learned that it was aboard the shipwreck of yesterday\'s troubles that he could build the treasure chest of wisdom and fortitude. Today, as Shivam braces himself for the horizon of his future, he carries with him the warm memories of his Mother\'s love, the steadfast strength of his Father\'s hand, the perseverance of his siblings\' spirits, and the unwavering support of the people who knew him as more than just a student, but as a beacon of hope and inspiration. Dreaming of a better tomorrow, Shivam Tanaji Shirsath looks not just towards the stars, but at the mirrors of his accomplishments and failures. As he graduates on August 5th, 2027, he whispers to himself, \"The journey has only just begun.\" For in the face of adversity, Shivam has discovered that the steel of his dreams is tempered by hardship, and from that crucible shall rise an unstoppable force that will surely conquer the world. ');

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `log_id` int(10) UNSIGNED NOT NULL,
  `appointment_id` int(10) UNSIGNED DEFAULT NULL,
  `user_type` enum('Admin','Doctor','Patient','System') NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `action` varchar(120) NOT NULL,
  `notes` text DEFAULT NULL,
  `action_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `doctor_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `specialization` varchar(120) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`doctor_id`, `name`, `email`, `password`, `specialization`, `password_hash`, `created_at`) VALUES
(1, 'Dr. Rahul Sharma', NULL, NULL, 'General Physician', 'hash_placeholder', '2025-08-26 14:25:56'),
(2, 'Dr. Priya Desai', NULL, NULL, 'Dentist', 'hash_placeholder', '2025-08-26 14:25:56'),
(3, 'Dr, Shivam Tanaji Shirsath', NULL, NULL, 'MD', '$2y$10$.jBW3qu7U6nJHkakbYtw1Or6P/eyN2HQLZo37vUNbfv/zxNMnEzA2', '2025-08-26 14:29:22'),
(4, 'Dr. Krushna Thakare', 'a@gmail.com', '$2y$10$PdWbez415QL/.jiwHwUxP.YwR033iNWLP.eeO2mKgD1LP.aPgSRp2', 'MD', '', '2025-08-26 14:55:51'),
(7, 'Nimse', 'n@gmail.com', '$2y$10$ojL2uFr2HwoGpfG0mO9J7u/6vwWUJ8n/95MjTV8XMduCcekg.8RQ6', 'MD', '', '2025-08-26 19:51:06');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `patient_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`patient_id`, `name`, `email`, `phone`, `password_hash`, `created_at`) VALUES
(1, 'Aman Kumar', 'aman@example.com', '9000000001', 'hash_placeholder', '2025-08-26 14:25:56'),
(2, 'Sita Verma', 'sita@example.com', '9000000002', 'hash_placeholder', '2025-08-26 14:25:56'),
(3, 'Shiv Shirsath', 'shivamshirsath07@gmail.com', '7218252574', '$2y$10$ZnJOD5YijbrlId4Yk4HkceWn/p2n2JXekyvl0ONZWCy8qKlCeYBDu', '2025-08-26 14:31:51'),
(6, 'nimse', 's@gmail.com', '7218252571', '$2y$10$7mk64TOLxLAeCj8D.7R.JO8u.OcRHKp.7MgkElDSUV9RKQbNupMjS', '2025-08-26 19:48:20');

-- --------------------------------------------------------

--
-- Table structure for table `report_download_tokens`
--

CREATE TABLE `report_download_tokens` (
  `token_id` int(10) UNSIGNED NOT NULL,
  `appointment_id` int(10) UNSIGNED NOT NULL,
  `token` char(64) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `symptoms_master`
--

CREATE TABLE `symptoms_master` (
  `symptom_id` int(10) UNSIGNED NOT NULL,
  `symptom_keyword` varchar(120) NOT NULL,
  `suggested_specialization` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `symptoms_master`
--

INSERT INTO `symptoms_master` (`symptom_id`, `symptom_keyword`, `suggested_specialization`) VALUES
(1, 'fever', 'General Physician'),
(2, 'cough', 'General Physician'),
(3, 'tooth pain', 'Dentist'),
(4, 'toothache', 'Dentist'),
(5, 'skin rash', 'MD');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `idx_patient_id` (`patient_id`),
  ADD KEY `idx_doctor_id` (`doctor_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_appointment` (`appointment_id`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`doctor_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`patient_id`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `report_download_tokens`
--
ALTER TABLE `report_download_tokens`
  ADD PRIMARY KEY (`token_id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `idx_appointment_token` (`appointment_id`,`token`);

--
-- Indexes for table `symptoms_master`
--
ALTER TABLE `symptoms_master`
  ADD PRIMARY KEY (`symptom_id`),
  ADD UNIQUE KEY `symptom_keyword` (`symptom_keyword`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `appointment_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `log_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `doctor_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `patient_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `report_download_tokens`
--
ALTER TABLE `report_download_tokens`
  MODIFY `token_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `symptoms_master`
--
ALTER TABLE `symptoms_master`
  MODIFY `symptom_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD CONSTRAINT `audit_log_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`appointment_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `report_download_tokens`
--
ALTER TABLE `report_download_tokens`
  ADD CONSTRAINT `report_download_tokens_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`appointment_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
