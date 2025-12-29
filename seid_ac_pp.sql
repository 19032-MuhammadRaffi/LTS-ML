-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 13, 2025 at 12:02 PM
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
-- Database: `seid_ac_pp`
--

-- --------------------------------------------------------

--
-- Table structure for table `part`
--

CREATE TABLE `part` (
  `part_code` varchar(32) NOT NULL,
  `part_name` varchar(32) NOT NULL,
  `qty_press` int(11) NOT NULL,
  `qty_paint` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `part`
--

INSERT INTO `part` (`part_code`, `part_name`, `qty_press`, `qty_paint`) VALUES
('CCHS-B829JBTA', 'Base Pan', 2600, 0),
('GCAB-A646JBTA', 'Top Table', 800, 100),
('GCAB-A767JBTA', 'Front Panel', 900, 0),
('PPLT-B282JBTA', 'Side Cover', 800, 200);

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

CREATE TABLE `transaction` (
  `id` int(11) NOT NULL,
  `part_code` varchar(32) NOT NULL,
  `date_tr` datetime NOT NULL,
  `shift` enum('1','2','3') NOT NULL,
  `qty` int(11) NOT NULL,
  `status` enum('PRESS','PAINT','ASSY') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction`
--

INSERT INTO `transaction` (`id`, `part_code`, `date_tr`, `shift`, `qty`, `status`) VALUES
(15, 'CCHS-B829JBTA', '2025-12-13 14:51:26', '1', 1000, 'PRESS'),
(16, 'CCHS-B829JBTA', '2025-12-13 18:00:00', '2', 800, 'PRESS'),
(20, 'CCHS-B829JBTA', '2025-12-14 00:40:00', '3', 800, 'PRESS'),
(21, 'GCAB-A646JBTA', '2025-12-13 16:00:48', '1', 1000, 'PRESS'),
(22, 'GCAB-A767JBTA', '2025-12-13 16:00:54', '1', 1000, 'PRESS'),
(23, 'PPLT-B282JBTA', '2025-12-13 16:00:58', '1', 1000, 'PRESS'),
(24, 'PPLT-B282JBTA', '2025-12-13 16:01:24', '1', 200, 'PAINT'),
(25, 'GCAB-A767JBTA', '2025-12-13 16:01:31', '1', 100, 'PAINT'),
(26, 'GCAB-A646JBTA', '2025-12-13 16:01:35', '1', 200, 'PAINT'),
(30, 'GCAB-A646JBTA', '2025-12-13 16:07:09', '1', 100, 'ASSY'),
(31, 'GCAB-A767JBTA', '2025-12-13 16:08:52', '1', 100, 'ASSY');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `username` varchar(32) NOT NULL,
  `password` varchar(32) NOT NULL,
  `role` enum('Admin','Press','Paint','Assy') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`username`, `password`, `role`) VALUES
('admin01', 'SeidMail01', 'Admin'),
('assy01', 'SeidMail01', 'Assy'),
('paint01', 'SeidMail01', 'Paint'),
('press01', 'SeidMail01', 'Press');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `part`
--
ALTER TABLE `part`
  ADD PRIMARY KEY (`part_code`);

--
-- Indexes for table `transaction`
--
ALTER TABLE `transaction`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `transaction`
--
ALTER TABLE `transaction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
