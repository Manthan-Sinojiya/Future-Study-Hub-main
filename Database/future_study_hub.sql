-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 12, 2024 at 04:43 PM
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
-- Database: `future_study_hub`
--

-- --------------------------------------------------------

--
-- Table structure for table `videos`
--

CREATE TABLE `videos` (
  `video_id` int(11) NOT NULL,
  `video_title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `subject` int(11) NOT NULL,
  `module` int(11) NOT NULL,
  `topic_number` int(11) NOT NULL,
  `video_path` varchar(255) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `videos`
--

INSERT INTO `videos` (`video_id`, `video_title`, `description`, `subject`, `module`, `topic_number`, `video_path`, `teacher_id`, `created_at`) VALUES
(1, 'Education for Life', 'Helps in reading comprehension', 3, 1, 1, 'uploads/Education For Life ⭐ Short Film.mp4', 2, '2024-08-30 13:38:07'),
(2, 'Teamwork', 'Understanding teamwork dynamics', 1, 2, 17, 'uploads/good teamwork and bad teamwork.mp4', 2, '2024-08-30 13:39:45'),
(3, 'Learning', 'Teaching basics and methods', 1, 1, 20, 'uploads/videoplayback (1).mp4', 3, '2024-08-30 13:41:17'),
(4, 'Life', 'Life lessons and principles', 4, 1, 23, 'uploads/videoplayback.mp4', 2, '2024-08-30 13:43:10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `videos`
--
ALTER TABLE `videos`
  ADD PRIMARY KEY (`video_id`),
  ADD KEY `subject` (`subject`),
  ADD KEY `module` (`module`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `videos`
--
ALTER TABLE `videos`
  MODIFY `video_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `videos`
--
ALTER TABLE `videos`
  ADD CONSTRAINT `videos_ibfk_1` FOREIGN KEY (`subject`) REFERENCES `subject` (`sub_id`),
  ADD CONSTRAINT `videos_ibfk_2` FOREIGN KEY (`module`) REFERENCES `module` (`module_id`),
  ADD CONSTRAINT `videos_ibfk_3` FOREIGN KEY (`teacher_id`) REFERENCES `teacher_details` (`teacher_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
