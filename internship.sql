-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 01, 2022 at 07:53 AM
-- Server version: 10.4.21-MariaDB
-- PHP Version: 8.0.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `internship`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_table`
--

CREATE TABLE `admin_table` (
  `admin_id` int(11) NOT NULL,
  `admin_email_address` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `admin_password` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `admin_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `platform_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `platform_address` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `platform_no` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `platform_logo` varchar(200) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `admin_table`
--

INSERT INTO `admin_table` (`admin_id`, `admin_email_address`, `admin_password`, `admin_name`, `platform_name`, `platform_address`, `platform_no`, `platform_logo`) VALUES
(1, 'admin@admin.com', 'password', 'DAMS', 'DAMS Hospital', '81, UppeHill, Nairobi', '0741287410', '../images/1875435485.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `application_table`
--

CREATE TABLE `application_table` (
  `application_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `internship_id` int(11) NOT NULL,
  `internship_number` int(11) NOT NULL,
  `cv` text COLLATE utf8_unicode_ci NOT NULL,
  `application_date` date NOT NULL,
  `status` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `student_come_for_interview` enum('No','Yes') COLLATE utf8_unicode_ci NOT NULL,
  `company_comment` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `application_number` int(100) NOT NULL,
  `application_time` date NOT NULL,
  `application_day` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `company_table`
--

CREATE TABLE `company_table` (
  `company_id` int(11) NOT NULL,
  `company_email_address` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `company_password` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `company_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `company_profile_image` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `company_phone_no` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `company_address` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `company_type` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `company_status` enum('Active','Inactive') COLLATE utf8_unicode_ci NOT NULL,
  `company_added_on` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `company_table`
--

INSERT INTO `company_table` (`company_id`, `company_email_address`, `company_password`, `company_name`, `company_profile_image`, `company_phone_no`, `company_address`, `company_type`, `company_status`, `company_added_on`) VALUES
(10, 'sammy20@gmail.com', '123', 'Smmy Naizer', '../images/159880007.jpg', '8523697410', '56, Metro Manila, PHL', 'MBBS MD (Medicine)', 'Active', '2022-06-23 17:20:03'),
(11, '12@gmail.com', '123', 'MMU', '../images/1927174493.jpg', '097643', 'ero', '', 'Active', '2022-06-30 14:44:40'),
(12, '1@gmail.com', '123', 'MMU', '../images/1029470317.jpg', '097643', 'ero', '', 'Active', '2022-06-30 14:49:54'),
(13, '123@gmail.com', '123', 'MMU', '../images/280752201.jpg', '097643', 'ero', 'Business', 'Active', '2022-06-30 14:52:37');

-- --------------------------------------------------------

--
-- Table structure for table `internship_table`
--

CREATE TABLE `internship_table` (
  `internship_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `company_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `job_title` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `job_description` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `internship_status` enum('Active','Inactive') COLLATE utf8_unicode_ci NOT NULL,
  `internship_date` date NOT NULL,
  `internship_day` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `internship_table`
--

INSERT INTO `internship_table` (`internship_id`, `company_id`, `company_name`, `job_title`, `job_description`, `start_date`, `end_date`, `internship_status`, `internship_date`, `internship_day`) VALUES
(30, 10, '', 'sam', 'sammy', '2022-10-17', '2028-11-24', 'Active', '0000-00-00', '0000-00-00'),
(31, 11, '', 'ertgyuik', 'programming skills', '2022-07-06', '2022-05-31', 'Active', '0000-00-00', '0000-00-00'),
(32, 12, '', 'internship', 'sammy', '2022-07-08', '2023-01-06', 'Active', '0000-00-00', '0000-00-00');

-- --------------------------------------------------------

--
-- Table structure for table `student_table`
--

CREATE TABLE `student_table` (
  `student_id` int(11) NOT NULL,
  `student_email_address` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `student_password` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `student_first_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `student_last_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `student_date_of_birth` date NOT NULL,
  `student_gender` enum('Male','Female','Other') COLLATE utf8_unicode_ci NOT NULL,
  `student_address` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `student_phone_no` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `student_speciality` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `student_added_on` datetime NOT NULL,
  `student_verification_code` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `email_verify` enum('No','Yes') COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `student_table`
--

INSERT INTO `student_table` (`student_id`, `student_email_address`, `student_password`, `student_first_name`, `student_last_name`, `student_date_of_birth`, `student_gender`, `student_address`, `student_phone_no`, `student_speciality`, `student_added_on`, `student_verification_code`, `email_verify`) VALUES
(52, 'sam@gmail.com', '123', 'Samwel', 'Odiwuor', '2022-06-06', 'Male', 'kayole', '0769743381', 'Single', '2022-06-24 15:05:10', '7dea6db33640d1e78cad71b5cdd8bb66', 'Yes'),
(53, '1@gmail.com', '123', 'sam', 'odi', '2022-06-01', 'Male', 'q2wert', '0786543212', 'it', '2022-06-29 22:53:16', 'd85ff73c023c79c94ba11099d5aa8a23', 'Yes'),
(54, '2@gmail.com', '123', 'sam', 'odi', '2022-06-07', 'Male', 'nyayo-highrise', '0786543212', 'it', '2022-06-29 23:11:14', '07af9f4417b1888100faffd67953ea08', 'Yes');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_table`
--
ALTER TABLE `admin_table`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `application_table`
--
ALTER TABLE `application_table`
  ADD PRIMARY KEY (`application_id`);

--
-- Indexes for table `company_table`
--
ALTER TABLE `company_table`
  ADD PRIMARY KEY (`company_id`);

--
-- Indexes for table `internship_table`
--
ALTER TABLE `internship_table`
  ADD PRIMARY KEY (`internship_id`);

--
-- Indexes for table `student_table`
--
ALTER TABLE `student_table`
  ADD PRIMARY KEY (`student_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_table`
--
ALTER TABLE `admin_table`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `application_table`
--
ALTER TABLE `application_table`
  MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `company_table`
--
ALTER TABLE `company_table`
  MODIFY `company_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `internship_table`
--
ALTER TABLE `internship_table`
  MODIFY `internship_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `student_table`
--
ALTER TABLE `student_table`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
