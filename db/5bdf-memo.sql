-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 17, 2025 at 04:50 AM
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
-- Database: `5bdf-memo`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `announcement_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT 'ANNOUNCEMENT',
  `date_created` datetime NOT NULL,
  `what` text NOT NULL,
  `where` varchar(255) NOT NULL,
  `when` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`announcement_id`, `title`, `date_created`, `what`, `where`, `when`) VALUES
(20, 'ANNOUNCEMENT', '2025-05-01 13:47:28', 'test', 'test', '2025-05-01 13:53:00'),
(21, 'ANNOUNCEMENT', '2025-05-01 13:48:03', 'test2', 'test2', '2025-05-01 13:54:00'),
(22, 'ANNOUNCEMENT', '2025-05-01 13:49:08', 'test3', 'test3', '2025-05-01 13:51:00'),
(23, 'ANNOUNCEMENT', '2025-05-01 13:49:33', 'test5', 'test5', '2025-05-01 15:49:00'),
(24, 'ANNOUNCEMENT', '2025-05-01 13:50:47', 'test1', 'test1', '2025-05-01 13:52:00'),
(28, 'ANNOUNCEMENT', '2025-05-05 14:16:53', 'hatdog', 'hatdog', '2025-05-18 18:20:00');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `department_id` int(11) NOT NULL,
  `department_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`department_id`, `department_name`) VALUES
(86, 'Accounting'),
(104, 'IT'),
(105, 'Marketing'),
(106, 'HR'),
(107, 'operations');

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `exam_id` int(11) NOT NULL,
  `exam_title` varchar(255) NOT NULL,
  `department` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `duration` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exams`
--

INSERT INTO `exams` (`exam_id`, `exam_title`, `department`, `description`, `duration`) VALUES
(12, 'C Programming', 'I.T', 'Mag exam mog loops ni sir Rod', '00:30:00'),
(13, 'To Marketing', 'operations', 'asd viot', '01:30:00'),
(14, 'To accounting', 'Accounting', 'Break it down', '01:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `exam_answers`
--

CREATE TABLE `exam_answers` (
  `answer_id` int(11) NOT NULL,
  `result_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `user_answer` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam_items`
--

CREATE TABLE `exam_items` (
  `item_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `question` text NOT NULL,
  `option_a` varchar(255) NOT NULL,
  `option_b` varchar(255) NOT NULL,
  `option_c` varchar(255) NOT NULL,
  `option_d` varchar(255) NOT NULL,
  `correct_option` enum('A','B','C','D') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam_results`
--

CREATE TABLE `exam_results` (
  `result_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `taken_on` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `memos`
--

CREATE TABLE `memos` (
  `memo_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `from_department` varchar(100) NOT NULL,
  `to_department` varchar(100) NOT NULL,
  `datetime_sent` datetime NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `is_downloaded` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `memos`
--

INSERT INTO `memos` (`memo_id`, `description`, `from_department`, `to_department`, `datetime_sent`, `file_path`, `is_downloaded`) VALUES
(67, 'Huy IT ', 'Accounting', 'IT', '2025-04-30 15:03:57', '../../uploads/TEST UPLOAD.docx', 1),
(68, 'Huy IT', 'Accounting', 'IT', '2025-04-30 15:13:40', '../../uploads/TEST UPLOAD.docx', 1),
(69, 'New Memo', 'Accounting', 'Marketing', '2025-05-01 10:03:18', '../../uploads/TEST UPLOAD.docx', 0),
(70, 'New Memo', 'Accounting', 'Marketing', '2025-05-01 10:03:25', '../../uploads/TEST UPLOAD.docx', 0),
(71, 'to Accounting', 'Marketing', 'Accounting', '2025-05-01 10:33:09', '../../uploads/TEST UPLOAD.docx', 1),
(72, 'to IT New', 'Accounting', 'IT', '2025-05-01 10:37:45', '../../uploads/doc02366820250421135406.pdf', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `UID` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(150) DEFAULT NULL,
  `department` varchar(20) NOT NULL,
  `role` enum('Admin','User') DEFAULT 'User'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `UID`, `password`, `full_name`, `department`, `role`) VALUES
(1, '99999', 'admin12345', 'admin', 'admin1', 'Admin'),
(14, '123-marketing', '123', '123', 'Marketing', 'User'),
(15, '852', '123', 'Dione Louis Nipaya', 'IT', 'User'),
(16, '123', '123', 'test', 'Accounting', 'User'),
(17, '321', '321', 'testtest', 'IT', 'User'),
(18, '456', '456', 'test-Marketing', 'Marketing', 'User'),
(19, '123123123', '123123123', 'Dione Louis Nipaya', 'IT', 'User');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`announcement_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`department_id`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`exam_id`);

--
-- Indexes for table `exam_answers`
--
ALTER TABLE `exam_answers`
  ADD PRIMARY KEY (`answer_id`),
  ADD KEY `result_id` (`result_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `exam_items`
--
ALTER TABLE `exam_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `exam_id` (`exam_id`);

--
-- Indexes for table `exam_results`
--
ALTER TABLE `exam_results`
  ADD PRIMARY KEY (`result_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `exam_id` (`exam_id`);

--
-- Indexes for table `memos`
--
ALTER TABLE `memos`
  ADD PRIMARY KEY (`memo_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`UID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `announcement_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `department_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `exam_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `exam_answers`
--
ALTER TABLE `exam_answers`
  MODIFY `answer_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exam_items`
--
ALTER TABLE `exam_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `exam_results`
--
ALTER TABLE `exam_results`
  MODIFY `result_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `memos`
--
ALTER TABLE `memos`
  MODIFY `memo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `exam_answers`
--
ALTER TABLE `exam_answers`
  ADD CONSTRAINT `exam_answers_ibfk_1` FOREIGN KEY (`result_id`) REFERENCES `exam_results` (`result_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_answers_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `exam_items` (`item_id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_items`
--
ALTER TABLE `exam_items`
  ADD CONSTRAINT `exam_items_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`exam_id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_results`
--
ALTER TABLE `exam_results`
  ADD CONSTRAINT `exam_results_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_results_ibfk_2` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`exam_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
