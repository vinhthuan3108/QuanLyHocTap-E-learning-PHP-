-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 05, 2025 at 01:44 PM
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
-- Table structure for table `answer`
--

CREATE TABLE `answer` (
  `answer_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer_text` text NOT NULL,
  `is_correct` tinyint(1) DEFAULT 0,
  `points` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `answer`
--

INSERT INTO `answer` (`answer_id`, `question_id`, `answer_text`, `is_correct`, `points`) VALUES
(14, 5, '4', 1, 0.00),
(15, 5, '5', 0, 0.00),
(16, 5, '6', 0, 0.00),
(17, 5, '7', 0, 0.00),
(18, 6, '7', 1, 0.00),
(23, 8, '2', 1, 0.00),
(24, 8, '34', 0, 0.00),
(25, 8, '4', 0, 0.00),
(26, 8, '5', 0, 0.00),
(27, 9, 's', 1, 0.00),
(28, 10, 'In waterfall model, customer involved each phase', 0, 0.00),
(29, 10, 'In waterfall phases run parallel', 0, 0.00),
(30, 10, 'In waterfall, requirement can change frequency', 0, 0.00),
(31, 10, 'In waterfall, testing occurs late.', 1, 0.00),
(32, 11, 'A. Tiêu chí vận hành sản phẩm', 0, 0.00),
(33, 11, 'B. ý C và D đúng', 0, 0.00),
(34, 11, 'C. ý A và D sai', 0, 0.00),
(35, 11, 'D. cả 3 ý trên đều sai', 1, 0.00),
(36, 12, '1', 1, 0.00);

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
  `status` char(1) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`course_id`, `course_background`, `course_code`, `course_name`, `course_description`, `teacher_id`, `start_date`, `end_date`, `status`, `price`) VALUES
(1, '1.jpg', 'SOT366', 'Phát triển mã nguồn mở', '20 slide pdf, 20 video, 80 câu bài tập', 9, '2025-01-01', '2025-12-12', 'A', 5000.00),
(2, '2.jpg', 'SOT357', 'Kiểm thử phần mềm', 'No description', 9, '2025-01-01', '2025-12-12', 'A', 450000.00),
(3, '3.jpg', 'SOT344', 'Trí tuệ nhân tạo', 'No description', 9, '2025-01-01', '2025-12-12', 'A', 600000.00),
(9, 'TAB100_TAB100_tienganha1_a2.jpg', 'TAB100', 'Tiếng Anh A1-A2', 'Tiếng Anh 20 video', 26, '2025-11-07', '2026-01-30', 'A', 4000.00),
(11, 'TAB101_TAB101_tienganh_b1.jpg', 'TAB101', 'Tiếng Anh B1', 'Tiếng Anh B1 20 video', 26, '2025-10-09', '2025-12-26', 'A', 6000.00),
(12, 'TOC100_TOC100_TinHocDaiCuong.jpg', 'TOC100', 'Tin Học Đại Cương', 'Tin học đại cương', 26, '2025-10-08', '2025-12-25', 'A', 0.00);

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

--
-- Dumping data for table `course_contents`
--

INSERT INTO `course_contents` (`contents_id`, `topic_id`, `title_content`, `content_type`, `description_content`, `created_by`, `created_at`) VALUES
(14, 6, 'Video mở đầu về mã nguồn mở', 'video', 'Video mẫu', NULL, '2025-11-18 10:27:24'),
(15, 6, 'Chapter 1', 'file', 'Chapter 1 - Mở đầu về phát triển mã nguồn mở', NULL, '2025-11-18 10:27:59'),
(16, 6, 'Video tham khảo', 'embed', 'Lịch sử hình thành và phát triển ', NULL, '2025-11-18 10:29:10'),
(17, 7, 'Tổng quan PM', 'file', 'Phần mềm, quy trình, mô hình, chất lượng', NULL, '2025-11-30 13:01:07'),
(18, 7, 'Kiểm thử', 'video', 'Mở đầu tổng quan', NULL, '2025-11-30 13:03:24');

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
(24, 3, 8),
(28, 1, 22);

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
(9, 9, '6', '12:30:00', '15:30:00'),
(12, 11, '2', '07:30:00', '09:30:00'),
(13, 12, '5', '10:00:00', '11:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `embedded_contents`
--

CREATE TABLE `embedded_contents` (
  `embedded_id` int(11) NOT NULL,
  `course_content_id` int(11) DEFAULT NULL,
  `embed_code` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `embedded_contents`
--

INSERT INTO `embedded_contents` (`embedded_id`, `course_content_id`, `embed_code`) VALUES
(6, 16, 'https://www.youtube.com/embed/p__Lf__h2rU?si=i388Qj9p9Zo2rHC_');

-- --------------------------------------------------------

--
-- Table structure for table `exam`
--

CREATE TABLE `exam` (
  `exam_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `column_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `open_time` datetime NOT NULL,
  `close_time` datetime NOT NULL,
  `time_limit` int(11) DEFAULT NULL,
  `max_score` decimal(10,2) NOT NULL DEFAULT 10.00,
  `status` char(1) NOT NULL DEFAULT 'A',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exam`
--

INSERT INTO `exam` (`exam_id`, `course_id`, `column_id`, `title`, `description`, `open_time`, `close_time`, `time_limit`, `max_score`, `status`, `created_at`) VALUES
(9, 9, 13, 'Quá trình', 'Bài quá trình', '2025-11-30 11:03:00', '2025-12-07 11:03:00', 3, 10.00, 'A', '2025-11-30 11:04:11'),
(11, 9, 14, 'Giữa kỳ', 'GK', '2025-11-30 11:42:00', '2025-12-07 11:42:00', 3, 10.00, 'A', '2025-11-30 11:43:09'),
(12, 2, 4, 'Kiểm tra về các loại mô hình', 'Slide 1', '2025-11-30 13:03:00', '2025-12-31 13:03:00', 20, 10.00, 'A', '2025-11-30 13:04:22'),
(13, 9, 13, 'a', 'b', '2025-12-01 09:42:00', '2025-12-08 09:42:00', 3, 10.00, 'A', '2025-12-01 09:42:50');

-- --------------------------------------------------------

--
-- Table structure for table `exam_submission`
--

CREATE TABLE `exam_submission` (
  `submission_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `start_time` datetime NOT NULL,
  `submit_time` datetime DEFAULT NULL,
  `total_score` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam_submission_detail`
--

CREATE TABLE `exam_submission_detail` (
  `detail_id` int(11) NOT NULL,
  `submission_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `selected_answer_id` int(11) DEFAULT NULL,
  `text_answer` text DEFAULT NULL,
  `is_correct` tinyint(1) DEFAULT 0,
  `points_earned` decimal(10,2) DEFAULT 0.00
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

--
-- Dumping data for table `file_contents`
--

INSERT INTO `file_contents` (`file_id`, `course_content_id`, `file_name`, `file_size`) VALUES
(4, 15, 'Chapter1.pdf', 4341),
(5, 17, 'Bai01-TongQuanPM.pdf', 1992);

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
(9, 3, 'Cuối kỳ', 30),
(13, 9, 'Quá trình', 30),
(14, 9, 'Giữa Kỳ', 40);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `course_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('payos','bank_transfer') NOT NULL DEFAULT 'payos',
  `status` enum('pending','paid','cancelled','failed') NOT NULL DEFAULT 'pending',
  `order_code` bigint(20) NOT NULL,
  `checkout_url` text DEFAULT NULL,
  `qr_code` text DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `payos_order_id` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `paid_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `user_id`, `course_id`, `amount`, `payment_method`, `status`, `order_code`, `checkout_url`, `qr_code`, `transaction_id`, `payos_order_id`, `description`, `created_at`, `updated_at`, `paid_at`) VALUES
(3, 22, 2, 450000.00, 'payos', 'pending', 223065, 'https://pay.payos.vn/web/5b6af1612e224e009d82f2f1e0bd74d9', NULL, NULL, NULL, 'Kiểm thử phần mềm', '2025-11-20 16:43:15', '2025-11-20 16:43:16', NULL),
(4, 22, 1, 500000.00, 'payos', 'pending', 905511, 'https://pay.payos.vn/web/c2f29300068c4314b4b976aa01d4061f', NULL, NULL, NULL, 'Phát triển mã nguồn mở', '2025-11-20 16:45:58', '2025-11-20 16:45:58', NULL),
(5, 22, 1, 5000.00, 'payos', 'pending', 448270, 'https://pay.payos.vn/web/ae140a74a50e4a59a4c3f6df14d98a5b', NULL, NULL, NULL, 'Phát triển mã nguồn mở', '2025-11-20 17:02:26', '2025-11-20 17:02:27', NULL),
(6, 22, 1, 5000.00, 'payos', 'paid', 885124, 'https://pay.payos.vn/web/f82f424f307f4eb7b4614b02253b442b', NULL, NULL, NULL, 'Phát triển mã nguồn mở', '2025-11-20 17:07:45', '2025-11-20 17:08:01', '2025-11-20 17:08:01'),
(7, 1, 9, 4000.00, 'payos', 'pending', 472229, 'https://pay.payos.vn/web/aba1b874085d46ffa0d19100bc0ed345', NULL, NULL, NULL, 'Tiếng Anh A1-A2', '2025-11-30 15:50:17', '2025-11-30 15:50:17', NULL),
(8, 1, 9, 4000.00, 'payos', 'paid', 167416, 'https://pay.payos.vn/web/0edbf278e80945e695fca629d0b68173', NULL, NULL, NULL, 'Tiếng Anh A1-A2', '2025-11-30 15:54:09', '2025-11-30 15:54:44', '2025-11-30 15:54:44'),
(9, 1, 11, 6000.00, 'payos', 'paid', 154592, 'https://pay.payos.vn/web/08b446b0aa6f488ca1fd2567de137d24', NULL, NULL, NULL, 'Tiếng Anh B1', '2025-12-01 16:38:18', '2025-12-01 16:38:45', '2025-12-01 16:38:45');

-- --------------------------------------------------------

--
-- Table structure for table `payment_logs`
--

CREATE TABLE `payment_logs` (
  `log_id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `status` enum('pending','paid','cancelled','failed') NOT NULL,
  `log_message` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

--
-- Dumping data for table `post`
--

INSERT INTO `post` (`post_id`, `user_id`, `course_id`, `title`, `content`, `created_at`) VALUES
(2, 9, 1, 'abc', '<p>defgh</p>', '2025-11-29 15:17:21'),
(3, 9, 1, 'abc', '<p>dddd</p>', '2025-11-29 15:22:22');

-- --------------------------------------------------------

--
-- Table structure for table `question`
--

CREATE TABLE `question` (
  `question_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `question_type` enum('multiple_choice_single','multiple_choice_multiple','short_answer','essay') NOT NULL,
  `question_text` text NOT NULL,
  `points` decimal(10,2) NOT NULL DEFAULT 1.00,
  `order_num` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `question`
--

INSERT INTO `question` (`question_id`, `exam_id`, `question_type`, `question_text`, `points`, `order_num`) VALUES
(5, 9, 'multiple_choice_single', '3+1=?', 1.00, 1),
(6, 9, 'essay', '3+4=?', 9.00, 1),
(8, 11, 'multiple_choice_single', 'a', 1.00, 1),
(9, 11, 'essay', 'a', 1.00, 1),
(10, 12, 'multiple_choice_single', 'Which of the following statements is true?', 1.00, 1),
(11, 12, 'multiple_choice_single', 'Trong bộ tiêu chí đánh giá chất lượng McCall,\r\n tính/tiêu chí bảo trì được thuộc nhóm tiêu chí nào sau\r\n đây?', 2.00, 1),
(12, 12, 'essay', '1=5, 2=10,3=15,4=20, 5=? (gợi ý qua môn: không phải 25)', 7.00, 1);

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

--
-- Dumping data for table `topics`
--

INSERT INTO `topics` (`topic_id`, `title_topic`, `course_id`, `description`, `created_by`, `created_at`) VALUES
(6, 'Tổng quan về phần mềm mã nguồn mở', 1, '<p>Hiểu về kiến thức thế nào là phần mềm nguồn mở.</p><p>Khi phát triển phần mềm nguồn mở thì cần phải tuân theo nguyên tắc nào</p>', NULL, '2025-11-18 10:24:48'),
(7, 'Tuần 1', 2, '<p>Tổng quan</p>', NULL, '2025-11-30 13:00:01');

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
(1, 'Vĩnh Thuận', '2005-10-01', 'M', 'Xã Vĩnh Phương, Thành phố Nha Trang, Khánh Hòa', '0349396534', 'vinhthuan@gmail.com', '056205001946', 'vinhthuan.jpg', '', NULL),
(2, 'Trần Ngọc Hân', '2003-07-15', 'F', 'Phường Tân Lập, Thành phố Nha Trang, Khánh Hòa', '0358237456', 'tranngochan@gmail.com', '056205001952', NULL, '', NULL),
(3, 'Lê Việt Hùng', '1999-11-02', 'M', 'Phường Phước Long, Thành phố Nha Trang, Khánh Hòa', '0394728165', 'lethanhbinh@gmail.com', '056205001953', NULL, '', NULL),
(4, 'Phạm Thị Mỹ Duyên', '2001-04-23', 'F', 'Phường 7, Thành phố Tuy Hòa, Phú Yên', '0387429513', 'phammyduyen@gmail.com', '056205001954', NULL, '', NULL),
(5, 'Ngô Đức Toàn', '2002-06-30', 'M', 'Thị trấn Vạn Giã, Huyện Vạn Ninh, Khánh Hòa', '0374638295', 'ngoductoan@gmail.com', '056205001955', NULL, '', NULL),
(6, 'Đặng Hoàng Yến', '2004-09-11', 'F', 'Xã Cam Hải Tây, Huyện Cam Lâm, Khánh Hòa', '0362859437', 'danghoangyen@gmail.com', '056205001956', NULL, '', NULL),
(7, 'Võ Hữu Tài', '2000-05-09', 'M', 'Phường Ninh Hiệp, Thị xã Ninh Hòa, Khánh Hòa', '0371948256', 'vohuutai@gmail.com', '056205001957', NULL, '', NULL),
(8, 'Nguyễn Thị Thu Hà', '2003-02-18', 'F', 'Phường Phú Lâm, Thành phố Tuy Hòa, Phú Yên', '0392748615', 'nguyenthuhuha@gmail.com', '056205001958', NULL, '', NULL),
(9, 'Huỳnh Xuân Nam', '2005-12-01', 'M', 'Xã An Phú, Thành phố Tuy Hòa, Phú Yên', '0349396524', 'xuannam1234zz@gmail.com', '056205001943', 'huynhxuannam.jpg', '', NULL),
(10, 'Nguyễn Minh Tài', '2000-01-20', 'M', 'Xã Đại Lãnh, Huyện Vạn Ninh, Khánh Hòa', '0372424264', 'nguyenminhtai@gmail.com', '056205000218', 'nguyenminhtai_nguyenminhtai.jpg', '', NULL),
(20, 'Huỳnh Thành', '2025-11-19', 'M', 'phú yên', '0947138175', 'nam.hx.64cntt@ntu.edu.vn', '054204000313', 'male.jpeg', '', '2025-11-17 08:22:31'),
(22, 'Vĩnh Thuận', '2025-11-27', 'M', '123 Nha Trang, Khánh Hòa', '1111111', 'vinhthuan9@gmail.com', '111111', 'vinhthuan.jpg', '', '2025-11-18 10:57:10'),
(25, 'VinhThuanNew', '2025-10-27', 'M', '123 Nha Trang, Khánh Hòa', '11111112', 'c3lttrong.2a2.vthuan@gmail.com', '12132313233', 'vinhthuan.jpg', '', '2025-11-30 14:53:31'),
(26, 'Lê Văn Lương', '2025-11-12', 'M', '123 Nha Trang, Khánh Hòa', '11111111111', 'levanluong@gmail.com', '123443211234', 'levanluong_levanluong.jpg', '', NULL);

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
(10, 'nguyenminhtai', '$2y$10$R7yZe.DyEYW9pdMKD3gaO.Vn5/vYv3DL3gKK9oM2SKwCOwissW.lC', 10, NULL, NULL),
(15, 'thanhhuynh', '$2y$10$F8d8sKsXYW5g4dtlKLNlnOl8H67VlY9o9mzscHhjgLT1D1wN31Xo6', 20, NULL, NULL),
(17, 'vinhthuan2', '$2y$12$F.M/2iDd.RvJY7bKdJxw5ehwx9VqmeHSJCUMkDwzVlvf.j.9P1/wS', 22, NULL, NULL),
(20, 'vinhthuannew', '$2y$12$DCfWpjsinBiRrpKrC2IEuu23qRq64.otHOFhZj7LJlBpPX2jgAVnm', 25, NULL, NULL),
(26, 'levanluong', '$2y$10$Q25CyVsxSh7eylSrhUe2GeueczQDyvq1WvSFFUlInLlgN.CS1aTWW', 26, NULL, NULL);

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
(20, 1),
(22, 1),
(25, 1),
(26, 2);

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
-- Dumping data for table `video_contents`
--

INSERT INTO `video_contents` (`video_id`, `course_content_id`, `video_url`, `video_size`) VALUES
(6, 14, 'Chapter1.mkv', 1898),
(7, 18, 'Chapter1 - KTPM.mkv', 1898);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `answer`
--
ALTER TABLE `answer`
  ADD PRIMARY KEY (`answer_id`),
  ADD KEY `question_id` (`question_id`);

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
-- Indexes for table `exam`
--
ALTER TABLE `exam`
  ADD PRIMARY KEY (`exam_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `column_id` (`column_id`);

--
-- Indexes for table `exam_submission`
--
ALTER TABLE `exam_submission`
  ADD PRIMARY KEY (`submission_id`),
  ADD KEY `fk_submission_exam` (`exam_id`),
  ADD KEY `fk_submission_member` (`member_id`);

--
-- Indexes for table `exam_submission_detail`
--
ALTER TABLE `exam_submission_detail`
  ADD PRIMARY KEY (`detail_id`),
  ADD KEY `submission_id` (`submission_id`),
  ADD KEY `question_id` (`question_id`);

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
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD UNIQUE KEY `unique_order_code` (`order_code`),
  ADD KEY `fk_payments_user_id` (`user_id`),
  ADD KEY `fk_payments_course_id` (`course_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `payment_logs`
--
ALTER TABLE `payment_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `fk_payment_logs_payment_id` (`payment_id`);

--
-- Indexes for table `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `question`
--
ALTER TABLE `question`
  ADD PRIMARY KEY (`question_id`),
  ADD KEY `exam_id` (`exam_id`);

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
-- AUTO_INCREMENT for table `answer`
--
ALTER TABLE `answer`
  MODIFY `answer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `course`
--
ALTER TABLE `course`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `course_contents`
--
ALTER TABLE `course_contents`
  MODIFY `contents_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `course_member`
--
ALTER TABLE `course_member`
  MODIFY `member_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `course_schedule`
--
ALTER TABLE `course_schedule`
  MODIFY `course_schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `embedded_contents`
--
ALTER TABLE `embedded_contents`
  MODIFY `embedded_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `exam`
--
ALTER TABLE `exam`
  MODIFY `exam_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `exam_submission`
--
ALTER TABLE `exam_submission`
  MODIFY `submission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `exam_submission_detail`
--
ALTER TABLE `exam_submission_detail`
  MODIFY `detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `file_contents`
--
ALTER TABLE `file_contents`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `grade`
--
ALTER TABLE `grade`
  MODIFY `grade_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=150;

--
-- AUTO_INCREMENT for table `grade_column`
--
ALTER TABLE `grade_column`
  MODIFY `column_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `payment_logs`
--
ALTER TABLE `payment_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `post`
--
ALTER TABLE `post`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `question`
--
ALTER TABLE `question`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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
  MODIFY `topic_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `user_account`
--
ALTER TABLE `user_account`
  MODIFY `account_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `video_contents`
--
ALTER TABLE `video_contents`
  MODIFY `video_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `answer`
--
ALTER TABLE `answer`
  ADD CONSTRAINT `answer_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `question` (`question_id`) ON DELETE CASCADE;

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
-- Constraints for table `exam`
--
ALTER TABLE `exam`
  ADD CONSTRAINT `exam_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`),
  ADD CONSTRAINT `exam_ibfk_2` FOREIGN KEY (`column_id`) REFERENCES `grade_column` (`column_id`);

--
-- Constraints for table `exam_submission`
--
ALTER TABLE `exam_submission`
  ADD CONSTRAINT `fk_submission_exam` FOREIGN KEY (`exam_id`) REFERENCES `exam` (`exam_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_submission_member` FOREIGN KEY (`member_id`) REFERENCES `course_member` (`member_id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_submission_detail`
--
ALTER TABLE `exam_submission_detail`
  ADD CONSTRAINT `exam_submission_detail_ibfk_1` FOREIGN KEY (`submission_id`) REFERENCES `exam_submission` (`submission_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_submission_detail_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `question` (`question_id`) ON DELETE CASCADE;

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
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payments_course_id` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_payments_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payment_logs`
--
ALTER TABLE `payment_logs`
  ADD CONSTRAINT `fk_payment_logs_payment_id` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`payment_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `post_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `post_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `question`
--
ALTER TABLE `question`
  ADD CONSTRAINT `question_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exam` (`exam_id`) ON DELETE CASCADE;

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
