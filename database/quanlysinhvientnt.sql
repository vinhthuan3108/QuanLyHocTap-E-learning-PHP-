-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 18, 2025 at 03:06 AM
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
-- Database: `quanlysinhvientnt`
--

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE `course` (
  `course_id` int(11) NOT NULL,
  `course_background` varchar(255) DEFAULT NULL,
  `course_code` varchar(10) NOT NULL,
  `course_name` varchar(255) NOT NULL,
  `course_description` text NOT NULL,
  `teacher_id` bigint(20) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` char(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`course_id`, `course_background`, `course_code`, `course_name`, `course_description`, `teacher_id`, `start_date`, `end_date`, `status`) VALUES
(1, '1.jpg', 'SOT366', 'Phát triển mã nguồn mở', 'No description', 9, '2025-01-01', '2025-12-12', 'A'),
(2, '2.jpg', 'SOT357', 'Kiểm thử phần mềm', 'No description', 9, '2025-01-01', '2025-12-12', 'A'),
(3, '3.jpg', 'SOT344', 'Trí tuệ nhân tạo', 'No description', 9, '2025-01-01', '2025-12-12', 'A'),
(5, 'TAB100_tienganha1_a2.jpg', 'TAB100', 'Tiếng Anh A1-A2', 'Tiếng Anh A1-A2', 14, '2025-08-15', '2025-11-29', 'A');

-- --------------------------------------------------------

--
-- Table structure for table `course_contents`
--

CREATE TABLE `course_contents` (
  `contents_id` int(11) NOT NULL,
  `topic_id` int(11) NOT NULL,
  `title_content` varchar(255) NOT NULL,
  `content_type` varchar(100) NOT NULL,
  `description_content` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_member`
--

CREATE TABLE `course_member` (
  `member_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `student_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_member`
--

INSERT INTO `course_member` (`member_id`, `course_id`, `student_id`) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 3, 1),
(4, 1, 2),
(5, 2, 2),
(6, 3, 2),
(7, 1, 3),
(8, 2, 3),
(9, 3, 3),
(10, 1, 4),
(11, 2, 4),
(12, 3, 4),
(13, 1, 5),
(14, 2, 5),
(15, 3, 5),
(16, 1, 6),
(17, 2, 6),
(18, 3, 6),
(19, 1, 7),
(20, 2, 7),
(21, 3, 7),
(22, 1, 8),
(23, 2, 8),
(24, 3, 8);

-- --------------------------------------------------------

--
-- Table structure for table `course_schedule`
--

CREATE TABLE `course_schedule` (
  `course_schedule_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `day_of_week` varchar(1) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_schedule`
--

INSERT INTO `course_schedule` (`course_schedule_id`, `course_id`, `day_of_week`, `start_time`, `end_time`) VALUES
(1, 1, '3', '16:00:00', '20:30:00'),
(2, 2, '2', '09:30:00', '11:30:00'),
(3, 3, '3', '07:00:00', '08:30:00'),
(5, 5, '4', '07:30:00', '10:15:00');

-- --------------------------------------------------------

--
-- Table structure for table `embedded_contents`
--

CREATE TABLE `embedded_contents` (
  `embedded_id` int(11) NOT NULL,
  `course_content_id` int(11) DEFAULT NULL,
  `embed_code` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `file_contents`
--

CREATE TABLE `file_contents` (
  `file_id` int(11) NOT NULL,
  `course_content_id` int(11) DEFAULT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_size` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grade`
--

CREATE TABLE `grade` (
  `grade_id` int(11) NOT NULL,
  `column_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `score` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grade`
--

INSERT INTO `grade` (`grade_id`, `column_id`, `member_id`, `score`) VALUES
(73, 1, 1, 6),
(74, 1, 4, 1),
(75, 1, 7, 4),
(76, 1, 10, 9),
(77, 1, 13, 8),
(78, 1, 16, 3),
(79, 1, 19, 8),
(80, 1, 22, 1),
(81, 2, 1, 9),
(82, 2, 4, 3),
(83, 2, 7, 0),
(84, 2, 10, 0),
(85, 2, 13, 2),
(86, 2, 16, 8),
(87, 2, 19, 8),
(88, 2, 22, 10),
(89, 3, 1, 2),
(90, 3, 4, 7),
(91, 3, 7, 9),
(92, 3, 10, 0),
(93, 3, 13, 8),
(94, 3, 16, 10),
(95, 3, 19, 3),
(96, 3, 22, 3),
(97, 4, 2, 1),
(98, 4, 5, 2),
(99, 4, 8, 7),
(100, 4, 11, 4),
(101, 4, 14, 4),
(102, 4, 17, 8),
(103, 4, 20, 4),
(104, 4, 23, 10),
(105, 5, 2, 2),
(106, 5, 5, 10),
(107, 5, 8, 2),
(108, 5, 11, 2),
(109, 5, 14, 8),
(110, 5, 17, 8),
(111, 5, 20, 6),
(112, 5, 23, 10),
(113, 6, 2, 2),
(114, 6, 5, 3),
(115, 6, 8, 4),
(116, 6, 11, 5),
(117, 6, 14, 5),
(118, 6, 17, 10),
(119, 6, 20, 3),
(120, 6, 23, 9),
(121, 7, 3, 9),
(122, 7, 6, 5),
(123, 7, 9, 3),
(124, 7, 12, 8),
(125, 7, 15, 7),
(126, 7, 18, 4),
(127, 7, 21, 3),
(128, 7, 24, 7),
(129, 8, 3, 1),
(130, 8, 6, 10),
(131, 8, 9, 1),
(132, 8, 12, 8),
(133, 8, 15, 6),
(134, 8, 18, 6),
(135, 8, 21, 8),
(136, 8, 24, 3),
(137, 9, 3, 3),
(138, 9, 6, 7),
(139, 9, 9, 6),
(140, 9, 12, 5),
(141, 9, 15, 10),
(142, 9, 18, 4),
(143, 9, 21, 6),
(144, 9, 24, 9);

-- --------------------------------------------------------

--
-- Table structure for table `grade_column`
--

CREATE TABLE `grade_column` (
  `column_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `grade_column_name` varchar(255) NOT NULL,
  `proportion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grade_column`
--

INSERT INTO `grade_column` (`column_id`, `course_id`, `grade_column_name`, `proportion`) VALUES
(1, 1, 'Quá trình', 20),
(2, 1, 'Giữa kỳ', 30),
(3, 1, 'Cuối kỳ', 50),
(4, 2, 'Quá trình', 40),
(5, 2, 'Giữa kỳ', 10),
(6, 2, 'Cuối kỳ', 50),
(7, 3, 'Quá trình', 30),
(8, 3, 'Giữa kỳ', 30),
(9, 3, 'Cuối kỳ', 30);

-- --------------------------------------------------------

--
-- Table structure for table `post`
--

CREATE TABLE `post` (
  `post_id` int(11) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`role_id`, `role_name`) VALUES
(1, 'student'),
(2, 'teacher'),
(3, 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `text_contents`
--

CREATE TABLE `text_contents` (
  `text_id` int(11) NOT NULL,
  `course_content_id` int(11) DEFAULT NULL,
  `text_content` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `topics`
--

CREATE TABLE `topics` (
  `topic_id` int(11) NOT NULL,
  `title_topic` varchar(255) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` bigint(20) NOT NULL,
  `full_name` varchar(50) NOT NULL,
  `date_of_birth` date NOT NULL,
  `gender` char(1) NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `email` varchar(255) NOT NULL,
  `citizen_id` varchar(15) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `verification_code` varchar(10) NOT NULL,
  `email_verified_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `full_name`, `date_of_birth`, `gender`, `address`, `phone`, `email`, `citizen_id`, `image`, `verification_code`, `email_verified_at`) VALUES
(1, 'Vĩnh Thuận', '2005-10-01', 'M', 'Xã Vĩnh Phương, Thành phố Nha Trang, Khánh Hòa', '0349396534', 'vinhthuan@gmail.com', '056205001946', NULL, '', NULL),
(2, 'Trần Ngọc Hân', '2003-07-15', 'F', 'Phường Tân Lập, Thành phố Nha Trang, Khánh Hòa', '0358237456', 'tranngochan@gmail.com', '056205001952', NULL, '', NULL),
(3, 'Lê Việt Hùng', '1999-11-02', 'M', 'Phường Phước Long, Thành phố Nha Trang, Khánh Hòa', '0394728165', 'lethanhbinh@gmail.com', '056205001953', NULL, '', NULL),
(4, 'Phạm Thị Mỹ Duyên', '2001-04-23', 'F', 'Phường 7, Thành phố Tuy Hòa, Phú Yên', '0387429513', 'phammyduyen@gmail.com', '056205001954', NULL, '', NULL),
(5, 'Ngô Đức Toàn', '2002-06-30', 'M', 'Thị trấn Vạn Giã, Huyện Vạn Ninh, Khánh Hòa', '0374638295', 'ngoductoan@gmail.com', '056205001955', NULL, '', NULL),
(6, 'Đặng Hoàng Yến', '2004-09-11', 'F', 'Xã Cam Hải Tây, Huyện Cam Lâm, Khánh Hòa', '0362859437', 'danghoangyen@gmail.com', '056205001956', NULL, '', NULL),
(7, 'Võ Hữu Tài', '2000-05-09', 'M', 'Phường Ninh Hiệp, Thị xã Ninh Hòa, Khánh Hòa', '0371948256', 'vohuutai@gmail.com', '056205001957', NULL, '', NULL),
(8, 'Nguyễn Thị Thu Hà', '2003-02-18', 'F', 'Phường Phú Lâm, Thành phố Tuy Hòa, Phú Yên', '0392748615', 'nguyenthuhuha@gmail.com', '056205001958', NULL, '', NULL),
(9, 'Huỳnh Xuân Nam', '2005-12-01', 'M', 'Xã An Phú, Thành phố Tuy Hòa, Phú Yên', '0349396524', 'huynhxuannam@gmail.com', '056205001943', NULL, '', NULL),
(10, 'Nguyễn Minh Tài', '2000-01-20', 'M', 'Xã Đại Lãnh, Huyện Vạn Ninh, Khánh Hòa', '0372424264', 'nguyenminhtai@gmail.com', '056205000218', NULL, '', NULL),
(14, 'Lê Văn Lương', '2000-01-20', 'M', 'Xã Đại Lãnh, Huyện Vạn Ninh, Khánh Hòa', '0372424264', 'levanluong@gmail.com', '056205000218', NULL, '', NULL),
(20, 'Huỳnh Thành', '2025-11-19', 'M', 'phú yên', '0947138175', 'nam.hx.64cntt@ntu.edu.vn', '054204000313', 'male.jpeg', '', '2025-11-17 08:22:31');

-- --------------------------------------------------------

--
-- Table structure for table `user_account`
--

CREATE TABLE `user_account` (
  `account_id` bigint(20) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_account`
--

INSERT INTO `user_account` (`account_id`, `username`, `password`, `user_id`, `reset_token`, `reset_token_expiry`) VALUES
(1, 'vinhthuan', '$2y$10$euldWUB.2PJIm0h0pzHSFuOqCe.6tHxl719XO.b9v0pJqTi5zyYpG', 1, NULL, NULL),
(2, 'tranngochan', '$2y$10$ipgQvkqB6bOjBN0EBT46dOLVcxJfOkCQJE5bPqKz.dbIpdNNlS9GO', 2, NULL, NULL),
(3, 'leviethung', '$2y$10$DFeOujHc4U/cdIVCLD4.8OYvg9fjc8LzXeFyO74uKbPS5bvY7eIVO', 3, NULL, NULL),
(4, 'phamthimyduyen', '$2y$10$LP6SHOMHeiB3Vg2UaoPnDukdpow.R3mXTLq.dHgNDxA92GPUwhFUy', 4, NULL, NULL),
(5, 'ngoductoan', '$2y$10$v5HpCvxbaxzOqOLxz0M1Ye9wkTJwnMH4midej73ysREJByHDCq/pm', 5, NULL, NULL),
(6, 'danghoangyen', '$2y$10$ILrlBJTHjx1CwFjreUiGvOnDJ9ys8KheSNCo6qAdf5OVCkPHqGaCG', 6, NULL, NULL),
(7, 'vohuutai', '$2y$10$Ohevfrm.1PkDcwfjDkUFHeR3mhEPAFS4kwm42VnmX3dmBZLNiX3kW', 7, NULL, NULL),
(8, 'nguyenthithuha', '$2y$10$lhu63fUdKlrULX9Fq/EkfeMuNLsHdUjxMyo.3GxSMx3BEFPeHP9ju', 8, NULL, NULL),
(9, 'huynhxuannam', '$2y$10$gsUuHnxcuhHi3.I2rp/qOO1hsa1qDg6H/vlDsQAQ5/PJANPS.x2p.', 9, NULL, NULL),
(10, 'nguyenminhtai', '$2y$10$9rBDhRO0P23WNAqDffm/DOy2rxOGD4FJ3FSOpcKWIKVo/wK3O7Xtm', 10, NULL, NULL),
(14, 'levanluong', '$2y$10$7.0R3THPF9nVc4i1xNkNDO3YRzLgbkXTEcGdyMJATx4KYjavvcgYe', 14, NULL, NULL),
(15, 'thanhhuynh', '$2y$10$F8d8sKsXYW5g4dtlKLNlnOl8H67VlY9o9mzscHhjgLT1D1wN31Xo6', 20, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_role`
--

CREATE TABLE `user_role` (
  `user_id` bigint(20) NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_role`
--

INSERT INTO `user_role` (`user_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 2),
(10, 3),
(20, 1);

-- --------------------------------------------------------

--
-- Table structure for table `video_contents`
--

CREATE TABLE `video_contents` (
  `video_id` int(11) NOT NULL,
  `course_content_id` int(11) DEFAULT NULL,
  `video_url` varchar(255) NOT NULL,
  `video_size` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`course_id`),
  ADD KEY `fk_course_teacher_id` (`teacher_id`);

--
-- Indexes for table `course_contents`
--
ALTER TABLE `course_contents`
  ADD PRIMARY KEY (`contents_id`),
  ADD KEY `topic_id` (`topic_id`);

--
-- Indexes for table `course_member`
--
ALTER TABLE `course_member`
  ADD PRIMARY KEY (`member_id`),
  ADD KEY `fk_course_member_student_id` (`student_id`),
  ADD KEY `fk_course_member_course_id` (`course_id`);

--
-- Indexes for table `course_schedule`
--
ALTER TABLE `course_schedule`
  ADD PRIMARY KEY (`course_schedule_id`),
  ADD KEY `fk_course_schedule_course_id` (`course_id`);

--
-- Indexes for table `embedded_contents`
--
ALTER TABLE `embedded_contents`
  ADD PRIMARY KEY (`embedded_id`),
  ADD KEY `course_content_id` (`course_content_id`);

--
-- Indexes for table `file_contents`
--
ALTER TABLE `file_contents`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `course_content_id` (`course_content_id`);

--
-- Indexes for table `grade`
--
ALTER TABLE `grade`
  ADD PRIMARY KEY (`grade_id`),
  ADD KEY `fk_grade_member_id` (`member_id`),
  ADD KEY `fk_grade_column_id` (`column_id`);

--
-- Indexes for table `grade_column`
--
ALTER TABLE `grade_column`
  ADD PRIMARY KEY (`column_id`);

--
-- Indexes for table `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `text_contents`
--
ALTER TABLE `text_contents`
  ADD PRIMARY KEY (`text_id`),
  ADD KEY `course_content_id` (`course_content_id`);

--
-- Indexes for table `topics`
--
ALTER TABLE `topics`
  ADD PRIMARY KEY (`topic_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_account`
--
ALTER TABLE `user_account`
  ADD PRIMARY KEY (`account_id`),
  ADD KEY `fk_user_account_user_id` (`user_id`);

--
-- Indexes for table `user_role`
--
ALTER TABLE `user_role`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `fk_user_role_role_id` (`role_id`);

--
-- Indexes for table `video_contents`
--
ALTER TABLE `video_contents`
  ADD PRIMARY KEY (`video_id`),
  ADD KEY `course_content_id` (`course_content_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `course`
--
ALTER TABLE `course`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `course_contents`
--
ALTER TABLE `course_contents`
  MODIFY `contents_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `course_member`
--
ALTER TABLE `course_member`
  MODIFY `member_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `course_schedule`
--
ALTER TABLE `course_schedule`
  MODIFY `course_schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `embedded_contents`
--
ALTER TABLE `embedded_contents`
  MODIFY `embedded_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `file_contents`
--
ALTER TABLE `file_contents`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grade`
--
ALTER TABLE `grade`
  MODIFY `grade_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `grade_column`
--
ALTER TABLE `grade_column`
  MODIFY `column_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `post`
--
ALTER TABLE `post`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `text_contents`
--
ALTER TABLE `text_contents`
  MODIFY `text_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `topics`
--
ALTER TABLE `topics`
  MODIFY `topic_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `user_account`
--
ALTER TABLE `user_account`
  MODIFY `account_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `video_contents`
--
ALTER TABLE `video_contents`
  MODIFY `video_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `course`
--
ALTER TABLE `course`
  ADD CONSTRAINT `fk_course_teacher_id` FOREIGN KEY (`teacher_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `course_contents`
--
ALTER TABLE `course_contents`
  ADD CONSTRAINT `course_contents_ibfk_1` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`topic_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `course_member`
--
ALTER TABLE `course_member`
  ADD CONSTRAINT `fk_course_member_course_id` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_course_member_student_id` FOREIGN KEY (`student_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `course_schedule`
--
ALTER TABLE `course_schedule`
  ADD CONSTRAINT `fk_course_schedule_course_id` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `embedded_contents`
--
ALTER TABLE `embedded_contents`
  ADD CONSTRAINT `embedded_contents_ibfk_1` FOREIGN KEY (`course_content_id`) REFERENCES `course_contents` (`contents_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `file_contents`
--
ALTER TABLE `file_contents`
  ADD CONSTRAINT `file_contents_ibfk_1` FOREIGN KEY (`course_content_id`) REFERENCES `course_contents` (`contents_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `grade`
--
ALTER TABLE `grade`
  ADD CONSTRAINT `fk_grade_column_id` FOREIGN KEY (`column_id`) REFERENCES `grade_column` (`column_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_grade_member_id` FOREIGN KEY (`member_id`) REFERENCES `course_member` (`member_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `post_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `post_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `text_contents`
--
ALTER TABLE `text_contents`
  ADD CONSTRAINT `text_contents_ibfk_1` FOREIGN KEY (`course_content_id`) REFERENCES `course_contents` (`contents_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `topics`
--
ALTER TABLE `topics`
  ADD CONSTRAINT `topics_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_account`
--
ALTER TABLE `user_account`
  ADD CONSTRAINT `fk_user_account_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_role`
--
ALTER TABLE `user_role`
  ADD CONSTRAINT `fk_user_role_role_id` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_user_role_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `video_contents`
--
ALTER TABLE `video_contents`
  ADD CONSTRAINT `video_contents_ibfk_1` FOREIGN KEY (`course_content_id`) REFERENCES `course_contents` (`contents_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

