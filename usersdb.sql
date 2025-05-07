-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 07, 2025 at 04:02 AM
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
-- Database: `usersdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `admintbl`
--

CREATE TABLE `admintbl` (
  `idno` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admintbl`
--

INSERT INTO `admintbl` (`idno`, `name`, `password`) VALUES
(123, 'admin', 'password');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `announcement_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `announcement_text`, `created_at`) VALUES
(1, 'hi', '2025-03-13 12:10:47'),
(2, 'Submit', '2025-03-13 12:12:05'),
(3, 'Submit', '2025-03-13 12:12:37'),
(4, '', '2025-03-13 12:14:07'),
(5, 'hi', '2025-03-13 12:14:35'),
(7, 'Hi everyone today we will have an exam starting at 10:30 to 11:30 AM.', '2025-03-13 12:16:24'),
(8, 'Good evening', '2025-03-13 12:48:11'),
(9, 'hi', '2025-03-14 13:16:51'),
(12, 'hello', '2025-03-20 03:40:40');

-- --------------------------------------------------------

--
-- Table structure for table `computers`
--

CREATE TABLE `computers` (
  `id` int(11) NOT NULL,
  `lab_id` int(11) DEFAULT NULL,
  `pc_number` int(11) DEFAULT NULL,
  `status` enum('available','in_use','offline','maintenance') DEFAULT 'available',
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `computer_sessions`
--

CREATE TABLE `computer_sessions` (
  `id` int(11) NOT NULL,
  `computer_id` int(11) DEFAULT NULL,
  `student_id` varchar(20) DEFAULT NULL,
  `session_start` datetime DEFAULT NULL,
  `session_end` datetime DEFAULT NULL,
  `duration_minutes` int(11) GENERATED ALWAYS AS (timestampdiff(MINUTE,`session_start`,`session_end`)) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedbacks`
--

CREATE TABLE `feedbacks` (
  `id` int(11) NOT NULL,
  `user_id` varchar(50) DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `labs`
--

CREATE TABLE `labs` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `location` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lab_resources`
--

CREATE TABLE `lab_resources` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `original_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `status` enum('enabled','disabled') NOT NULL DEFAULT 'enabled',
  `link` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lab_resources`
--

INSERT INTO `lab_resources` (`id`, `title`, `description`, `file_name`, `original_name`, `file_path`, `status`, `link`) VALUES
(5, 'nwe', 'new', '681a99443b074.pdf', 'TESTQUA SKloud Youth & Admin - SKLoud Youth.pdf', 'uploads/681a99443b074.pdf', 'enabled', 'https://youtu.be/JkBwtOEvoXU?si=WFWX4GnQAzI4jbcn');

-- --------------------------------------------------------

--
-- Table structure for table `lab_schedules`
--

CREATE TABLE `lab_schedules` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lab_schedules`
--

INSERT INTO `lab_schedules` (`id`, `title`, `description`, `start_date`, `end_date`, `file_path`, `created_at`, `status`) VALUES
(19, 'sdaf', 'sadfasf', '2025-05-06', '2025-05-29', 'uploads/clear.jpg', '2025-05-06 13:18:32', 'active'),
(20, 'clouds', 'clouds', '2025-02-01', '2025-02-02', 'uploads/clouds.jpeg', '2025-05-06 13:19:16', 'active'),
(22, 'adf', 'adfas', '2025-05-06', '2025-06-07', 'uploads/clouds.jpeg', '2025-05-06 15:50:53', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `pc_status`
--

CREATE TABLE `pc_status` (
  `id` int(11) NOT NULL,
  `lab` varchar(50) DEFAULT NULL,
  `pc_number` int(11) DEFAULT NULL,
  `status` enum('offline','online') DEFAULT 'offline'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `id_number` varchar(20) DEFAULT NULL,
  `student_name` varchar(100) DEFAULT NULL,
  `purpose` text DEFAULT NULL,
  `lab` varchar(50) DEFAULT NULL,
  `time_in` time DEFAULT NULL,
  `date` date DEFAULT NULL,
  `remaining_session` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'Pending',
  `pc_number` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `id_number`, `student_name`, `purpose`, `lab`, `time_in`, `date`, `remaining_session`, `created_at`, `status`, `pc_number`) VALUES
(6, '15', 'John Aries Rizada', 'C programming', '530', '08:48:00', '2025-04-07', 25, '2025-04-30 00:48:31', 'Accepted', ''),
(7, '15', 'John Aries Rizada', 'php progrmaming', '540', '14:12:00', '2025-05-04', 26, '2025-05-04 06:12:31', 'Accepted', ''),
(8, '15', 'John Aries Rizada', 'java programming', '540', '22:19:00', '2025-05-04', 27, '2025-05-04 14:19:52', 'Accepted', ''),
(9, '11', 'bravo alpha', 'C programming', '530', '22:36:00', '2025-05-04', 29, '2025-05-04 14:36:09', 'Accepted', ''),
(10, '333', 'sdfasdf sdfasf', 'C programming', '530', '22:43:00', '2025-05-04', 28, '2025-05-04 14:43:28', 'Accepted', ''),
(11, '2222', 'asdfasdf dfasdf', 'C programming', '530', '22:45:00', '2025-05-04', 23, '2025-05-04 14:45:13', 'Accepted', ''),
(12, '15', 'John Aries Rizada', 'php programming', '544', '13:02:00', '2025-02-01', 28, '2025-05-06 22:45:48', 'Accepted', ''),
(13, '15', 'John Aries Rizada', 'Java Programming', '542', '09:52:00', '2025-05-07', 24, '2025-05-07 01:53:49', 'Accepted', '11'),
(14, '11', 'bravo alpha', 'Java Programming', '524', '10:01:00', '2025-05-07', 27, '2025-05-07 02:01:55', 'Accepted', '1');

-- --------------------------------------------------------

--
-- Table structure for table `resources`
--

CREATE TABLE `resources` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `filename` varchar(255) NOT NULL,
  `filepath` varchar(255) NOT NULL,
  `filesize` varchar(50) DEFAULT NULL,
  `filetype` varchar(50) DEFAULT NULL,
  `status` enum('enabled','disabled') DEFAULT 'enabled',
  `uploaded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sit_in_history`
--

CREATE TABLE `sit_in_history` (
  `id` int(11) NOT NULL,
  `idno` varchar(20) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `laboratory` varchar(50) DEFAULT NULL,
  `feedback` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sit_in_history`
--

INSERT INTO `sit_in_history` (`id`, `idno`, `date`, `status`, `laboratory`, `feedback`) VALUES
(1, '15', '2025-04-19 14:42:27', 'Completed', '524', NULL),
(2, '333', '2025-04-19 14:47:07', 'Completed', 'MAC Laboratory', 'thank you'),
(3, '15', '2025-04-19 15:04:04', 'Completed', 'MAC Laboratory', 'nice'),
(4, '15', '2025-04-19 15:06:42', 'Completed', 'MAC Laboratory', 'wew'),
(5, '15', '2025-04-19 15:12:59', 'Completed', '544', 'why\\r\\n'),
(6, '2222', '2025-04-19 15:28:10', 'Completed', '542', 'last'),
(7, '15', '2025-04-30 08:04:30', 'Completed', 'MAC Laboratory', NULL),
(8, '15', '2025-05-04 14:14:32', 'Completed', '544', 'reshian biot'),
(9, '15', '2025-05-04 22:21:34', 'Completed', '524', NULL),
(10, '15', '2025-05-04 22:35:36', 'Completed', '524', NULL),
(11, '2222', '2025-05-04 22:52:24', 'Completed', '542', NULL),
(12, '15', '2025-05-04 22:52:39', 'Completed', '524', 'sleepy'),
(13, '15', '2025-05-06 21:58:39', 'Completed', '530', 'sheesh\\r\\n'),
(14, '2222', '2025-05-06 21:58:41', 'Completed', '526', NULL),
(15, '333', '2025-05-06 21:58:44', 'Completed', '528', NULL),
(16, '1111', '2025-05-06 21:59:38', 'Completed', '530', NULL),
(17, '444', '2025-05-06 21:59:40', 'Completed', '526', NULL),
(18, '422', '2025-05-06 21:59:43', 'Completed', '528', NULL),
(19, '2222', '2025-05-06 22:00:00', 'Completed', '526', NULL),
(20, '15', '2025-05-07 08:26:44', 'Completed', '524', NULL),
(21, '11', '2025-05-07 08:28:11', 'Completed', '524', NULL),
(22, '11', '2025-05-07 08:28:20', 'Completed', '524', NULL),
(23, '11', '2025-05-07 08:28:36', 'Completed', 'MAC Laboratory', NULL),
(24, '15', '2025-05-07 08:38:37', 'Completed', '524', NULL),
(25, '15', '2025-05-07 08:39:06', 'Completed', '524', NULL),
(26, '11', '2025-05-07 08:39:09', 'Completed', 'MAC Laboratory', NULL),
(27, '2222', '2025-05-07 08:39:14', 'Completed', '526', NULL),
(28, '333', '2025-05-07 08:39:17', 'Completed', '528', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sit_in_records`
--

CREATE TABLE `sit_in_records` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `student_name` varchar(100) NOT NULL,
  `purpose` varchar(100) NOT NULL,
  `lab` varchar(50) NOT NULL,
  `login_time` time NOT NULL,
  `logout_time` time DEFAULT NULL,
  `date_removed` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sit_in_records`
--

INSERT INTO `sit_in_records` (`id`, `student_id`, `student_name`, `purpose`, `lab`, `login_time`, `logout_time`, `date_removed`) VALUES
(49, '15', 'John Aries Rizada', 'C Programming', '524', '08:38:32', '09:00:18', '2025-03-25 21:00:18'),
(50, '12344', 'sadfasdf ssdfa', 'C Programming', '524', '09:02:07', '09:02:07', '2025-03-25 21:02:07'),
(51, '523423', 'sadfas sdafas', 'Java Programming', 'MAC Laboratory', '09:03:54', '09:03:34', '2025-03-25 21:03:34'),
(52, '523423', 'sadfas sdafas', 'C Programming', '524', '09:03:54', '09:04:00', '2025-03-25 21:04:00'),
(53, '15', 'John Aries Rizada', 'Java Programming', '544', '08:38:32', '09:05:50', '2025-03-25 21:05:50'),
(54, '11', 'bravo alpha', 'C Programming', 'MAC Laboratory', '08:28:32', '09:05:53', '2025-03-25 21:05:53'),
(55, '22631824', 'paulo Rizada', 'Java Programming', '524', '09:05:55', '09:05:55', '2025-03-25 21:05:55'),
(56, '234231', 'sadfasf asdfasdfasdfas', 'C++ Programming', 'MAC Laboratory', '09:05:58', '09:05:58', '2025-03-25 21:05:58'),
(57, '1111', 'asdfasdf asdfasd', 'C Programming', '524', '09:59:04', '09:06:00', '2025-03-25 21:06:00'),
(58, '2222', 'asdfasdf dfasdf', 'C Programming', '542', '09:58:30', '09:06:03', '2025-03-25 21:06:03'),
(59, '333', 'sdfasdf sdfasf', 'C Programming', '524', '09:58:03', '09:06:06', '2025-03-25 21:06:06'),
(60, '1111', 'asdfasdf asdfasd', 'C Programming', '524', '09:59:04', '09:06:45', '2025-03-25 21:06:45'),
(61, '15', 'John Aries Rizada', 'Java Programming', '524', '08:38:32', '09:08:08', '2025-03-25 21:08:08'),
(62, '1111', 'asdfasdf asdfasd', 'Java Programming', 'MAC Laboratory', '09:59:04', '09:18:28', '2025-03-25 21:18:28'),
(63, '2222', 'asdfasdf dfasdf', 'C Programming', '524', '09:58:30', '09:21:01', '2025-03-25 21:21:01'),
(64, '15', 'John Aries Rizada', 'Java Programming', '544', '08:38:32', '07:44:19', '2025-04-12 19:44:19'),
(65, '15', 'John Aries Rizada', 'Java Programming', '544', '08:38:32', '07:45:22', '2025-04-12 19:45:22'),
(66, '123', 'sadfasdf sdfasdf', 'C++ Programming', 'MAC Laboratory', '07:57:44', '07:57:44', '2025-04-12 19:57:44'),
(67, '15', 'John Aries Rizada', 'Java Programming', 'MAC Laboratory', '08:38:32', '07:58:03', '2025-04-12 19:58:03'),
(68, '15', 'John Aries Rizada', 'Java Programming', '544', '08:38:32', '02:33:24', '2025-04-19 14:33:24'),
(69, '15', 'John Aries Rizada', 'C++ Programming', 'MAC Laboratory', '08:38:32', '03:06:42', '2025-04-19 15:06:42'),
(70, '15', 'John Aries Rizada', 'C Programming', '544', '08:38:32', '03:12:59', '2025-04-19 15:12:59'),
(71, '2222', 'asdfasdf dfasdf', 'Java Programming', '542', '09:58:30', '03:28:10', '2025-04-19 15:28:10'),
(72, '15', 'John Aries Rizada', 'Java Programming', 'MAC Laboratory', '08:38:32', '08:04:30', '2025-04-30 08:04:30'),
(73, '15', 'John Aries Rizada', 'Java Programming', '544', '08:38:32', '02:14:32', '2025-05-04 14:14:32'),
(74, '15', 'John Aries Rizada', 'C Programming', '524', '08:38:32', '10:21:34', '2025-05-04 22:21:34'),
(75, '15', 'John Aries Rizada', 'C Programming', '524', '08:38:32', '10:35:36', '2025-05-04 22:35:36'),
(76, '2222', 'asdfasdf dfasdf', 'Java Programming', '542', '09:58:30', '10:52:24', '2025-05-04 22:52:24'),
(77, '15', 'John Aries Rizada', 'C Programming', '524', '08:38:32', '10:52:39', '2025-05-04 22:52:39'),
(78, '15', 'John Aries Rizada', 'C# Programming', '530', '08:38:32', '09:58:39', '2025-05-06 21:58:39'),
(79, '2222', 'asdfasdf dfasdf', 'Python Programming', '526', '09:58:30', '09:58:41', '2025-05-06 21:58:41'),
(80, '333', 'sdfasdf sdfasf', 'Php Programming', '528', '09:58:03', '09:58:44', '2025-05-06 21:58:44'),
(81, '1111', 'asdfasdf asdfasd', 'C# Programming', '530', '09:59:04', '09:59:38', '2025-05-06 21:59:38'),
(82, '444', 'asdfas sdafads', 'C# Programming', '526', '09:59:40', '09:59:40', '2025-05-06 21:59:40'),
(83, '422', 'sadfasdf sdfasdf', 'Python Programming', '528', '09:59:43', '09:59:43', '2025-05-06 21:59:43'),
(84, '2222', 'asdfasdf dfasdf', 'Python Programming', '526', '09:58:30', '10:00:00', '2025-05-06 22:00:00'),
(85, '15', 'John Aries Rizada', 'C Programming', '524', '08:38:32', '08:26:44', '2025-05-07 08:26:44'),
(86, '11', 'bravo alpha', 'C Programming', '524', '08:28:32', '08:28:11', '2025-05-07 08:28:11'),
(87, '11', 'bravo alpha', 'C Programming', '524', '08:28:32', '08:28:20', '2025-05-07 08:28:20'),
(88, '11', 'bravo alpha', 'C++ Programming', 'MAC Laboratory', '08:28:32', '08:28:36', '2025-05-07 08:28:36'),
(89, '15', 'John Aries Rizada', 'C Programming', '524', '08:38:32', '08:38:37', '2025-05-07 08:38:37'),
(90, '15', 'John Aries Rizada', 'C Programming', '524', '08:38:32', '08:39:06', '2025-05-07 08:39:06'),
(91, '11', 'bravo alpha', 'C++ Programming', 'MAC Laboratory', '08:28:32', '08:39:09', '2025-05-07 08:39:09'),
(92, '2222', 'asdfasdf dfasdf', 'Python Programming', '526', '09:58:30', '08:39:14', '2025-05-07 08:39:14'),
(93, '333', 'sdfasdf sdfasf', 'Php Programming', '528', '09:58:03', '08:39:17', '2025-05-07 08:39:17');

-- --------------------------------------------------------

--
-- Table structure for table `userstbl`
--

CREATE TABLE `userstbl` (
  `idno` int(11) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `middlename` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `course` varchar(255) NOT NULL,
  `level` int(11) NOT NULL,
  `password` varchar(255) NOT NULL,
  `sessions` int(11) NOT NULL DEFAULT 30,
  `labs` varchar(255) DEFAULT NULL,
  `purpose` varchar(255) DEFAULT NULL,
  `date_login` date DEFAULT NULL,
  `login_date` time DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `points` int(11) DEFAULT 0,
  `claimed_rewards` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `userstbl`
--

INSERT INTO `userstbl` (`idno`, `lastname`, `firstname`, `middlename`, `email`, `course`, `level`, `password`, `sessions`, `labs`, `purpose`, `date_login`, `login_date`, `status`, `points`, `claimed_rewards`) VALUES
(15, 'Rizada', 'John Aries', 'Canama', 'rizadajohn5@gmail.com', 'BSIT', 3, 'admin', 24, '524', 'C Programming', '2025-03-18', '02:40:07', 'Active', 10, 3),
(11, 'alpha', 'bravo', 'cat', 'cat@gmail.com', 'BSEE', 1, 'goat', 27, 'MAC Laboratory', 'C++ Programming', '2025-03-18', '03:04:16', 'Active', 0, 0),
(22631824, 'Rizada', 'paulo', 'canama', 'paulo@gmail.com', 'BSAE', 3, 'rizada', 30, '524', 'Java Programming', '2025-03-13', '07:15:56', 'active', 0, 0),
(234231, 'asdfasdfasdfas', 'sadfasf', 'asdfasdfasdfa', 'adafs@gmail.com', 'BSAD', 3, 'sdfasdf', 30, 'MAC Laboratory', 'C++ Programming', NULL, NULL, 'active', 0, 0),
(1111, 'asdfasd', 'asdfasdf', 'asdfasdf', 'asdfasdf@gmail.com', 'BSEE', 3, 'dsfasdf', 29, '530', 'C# Programming', NULL, NULL, 'Completed', 0, 0),
(2222, 'dfasdf', 'asdfasdf', 'asdfasdf', 'sdafasd@gmail.com', 'BSSEAWD', 2, '123', 22, '526', 'Python Programming', '2025-03-13', '08:18:39', 'Completed', 3, 1),
(333, 'sdfasf', 'sdfasdf', 'sdfasdf', 'sdafa@gmail.com', 'sdfasd', 1, 'asdfs', 27, '528', 'Php Programming', '2025-03-13', '07:14:57', 'Completed', 7, 2),
(34523, 'fasdfas', 'asdfas', 'sdfasdf', 'dsfa@gmail.com', 'sdfas', 3, '444', 30, NULL, NULL, NULL, NULL, NULL, 0, 0),
(444, 'sdafads', 'asdfas', 'sadfasdf', 'sdfas@gmail.com', 'sadf', 2, '444', 29, '526', 'C# Programming', NULL, NULL, 'Completed', 2, 0),
(86876, 'sdfasdS', 'SDFASFA', 'dafasdfa', 'wdfasdfa@gmail.com', 'dadfa', 2, '132', 30, NULL, NULL, NULL, NULL, NULL, 0, 0),
(123, 'sdfasdf', 'sadfasdf', 'sdfasd', 'sdafas@gmail.com', 'sdfad', 21, '2', 30, 'MAC Laboratory', 'C++ Programming', NULL, NULL, 'active', 0, 0),
(422, 'sdfasdf', 'sadfasdf', 'sdfasd', 'sdafas@gmail.com', 'sdfad', 21, 'sdfa', 29, '528', 'Python Programming', NULL, NULL, 'Completed', 5, 0),
(5234, 'Gimenez', 'Jerick', 'Urcales', 'jerick@gmail.com', 'BSMT', 2, '4231', 30, '524', 'C Programming', '2025-03-18', '03:07:53', 'active', 0, 0),
(523423, 'sdafas', 'sadfas', 'asdfas', 'sadf2@gmail.com', 'Sdfadf', 3, 'ssdfa32', 30, '524', 'C Programming', NULL, NULL, 'active', 0, 0),
(2242, 'sadfas', 'asdfasdf', 'asdfasdf', 'asdfasd@gmail.com', 'sadfasd', 3, 'sdfsaf', 30, '524', 'C Programming', NULL, NULL, 'active', 16, 0),
(2342, 'asdfasdf', 'asdfasd', 'sdfasd', 'asdfsa@gmail.com', 'asdfas', 3, 'dsafs', 30, '524', 'C Programming', NULL, NULL, 'active', 0, 0),
(234223, 'asdfasdf', 'asdfasd', 'sdfasd', 'asdfsa@gmail.com', 'asdfas', 3, 'asdfas', 30, NULL, NULL, NULL, NULL, NULL, 0, 0),
(12344, 'ssdfa', 'sadfasdf', 'sadfas', 'sdfas@gmail.com', 'sdfasf', 2, 'sdfasf', 30, '524', 'C Programming', NULL, NULL, 'active', 0, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `computers`
--
ALTER TABLE `computers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lab_id` (`lab_id`);

--
-- Indexes for table `computer_sessions`
--
ALTER TABLE `computer_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `computer_id` (`computer_id`);

--
-- Indexes for table `feedbacks`
--
ALTER TABLE `feedbacks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `labs`
--
ALTER TABLE `labs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `lab_resources`
--
ALTER TABLE `lab_resources`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lab_schedules`
--
ALTER TABLE `lab_schedules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pc_status`
--
ALTER TABLE `pc_status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `resources`
--
ALTER TABLE `resources`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sit_in_history`
--
ALTER TABLE `sit_in_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sit_in_records`
--
ALTER TABLE `sit_in_records`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `computers`
--
ALTER TABLE `computers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `computer_sessions`
--
ALTER TABLE `computer_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedbacks`
--
ALTER TABLE `feedbacks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `labs`
--
ALTER TABLE `labs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lab_resources`
--
ALTER TABLE `lab_resources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `lab_schedules`
--
ALTER TABLE `lab_schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `pc_status`
--
ALTER TABLE `pc_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `resources`
--
ALTER TABLE `resources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sit_in_history`
--
ALTER TABLE `sit_in_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `sit_in_records`
--
ALTER TABLE `sit_in_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `computers`
--
ALTER TABLE `computers`
  ADD CONSTRAINT `computers_ibfk_1` FOREIGN KEY (`lab_id`) REFERENCES `labs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `computer_sessions`
--
ALTER TABLE `computer_sessions`
  ADD CONSTRAINT `computer_sessions_ibfk_1` FOREIGN KEY (`computer_id`) REFERENCES `computers` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
