SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `action_master` (
  `action_id` int(11) NOT NULL,
  `action_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `area_master` (
  `area_id` int(11) NOT NULL,
  `area_name` varchar(50) NOT NULL,
  `description` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `category_master` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `cause_master` (
  `cause_id` int(11) NOT NULL,
  `cause_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `defect_master` (
  `defect_id` int(11) NOT NULL,
  `defect_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `line_drop_action` (
  `line_drop_id` int(11) NOT NULL,
  `action_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `line_drop_category` (
  `line_drop_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `line_drop_cause` (
  `line_drop_id` int(11) NOT NULL,
  `cause_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `line_drop_defect` (
  `line_drop_id` int(11) NOT NULL,
  `defect_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `line_drop_header` (
  `line_drop_id` int(11) NOT NULL,
  `product_id` varchar(50) NOT NULL,
  `model_id` int(11) NOT NULL,
  `area_id` int(11) NOT NULL,
  `drop_datetime` datetime DEFAULT current_timestamp(),
  `dropped_by` int(11) NOT NULL,
  `repair_datetime` datetime DEFAULT NULL,
  `repaired_by` int(11) DEFAULT NULL,
  `line_name` varchar(50) DEFAULT NULL,
  `status` enum('OPEN','CLOSE') DEFAULT 'OPEN'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `model_action` (
  `model_id` int(11) NOT NULL,
  `action_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `model_category` (
  `model_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `model_cause` (
  `model_id` int(11) NOT NULL,
  `cause_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `model_defect` (
  `model_id` int(11) NOT NULL,
  `defect_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `model_master` (
  `model_id` int(11) NOT NULL,
  `model_code` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `product_master` (
  `product_id` varchar(50) NOT NULL,
  `model_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `user_master` (
  `user_id` int(11) NOT NULL,
  `username` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `area` varchar(32) NOT NULL,
  `role` enum('OPERATOR','LEADER','QC','ADMIN') NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `action_master`
  ADD PRIMARY KEY (`action_id`);

ALTER TABLE `area_master`
  ADD PRIMARY KEY (`area_id`),
  ADD UNIQUE KEY `area_name` (`area_name`);

ALTER TABLE `category_master`
  ADD PRIMARY KEY (`category_id`);

ALTER TABLE `cause_master`
  ADD PRIMARY KEY (`cause_id`);

ALTER TABLE `defect_master`
  ADD PRIMARY KEY (`defect_id`);

ALTER TABLE `line_drop_action`
  ADD PRIMARY KEY (`line_drop_id`,`action_id`),
  ADD KEY `action_id` (`action_id`);

ALTER TABLE `line_drop_category`
  ADD PRIMARY KEY (`line_drop_id`,`category_id`),
  ADD KEY `category_id` (`category_id`);

ALTER TABLE `line_drop_cause`
  ADD PRIMARY KEY (`line_drop_id`,`cause_id`),
  ADD KEY `cause_id` (`cause_id`);

ALTER TABLE `line_drop_defect`
  ADD PRIMARY KEY (`line_drop_id`,`defect_id`),
  ADD KEY `defect_id` (`defect_id`);

ALTER TABLE `line_drop_header`
  ADD PRIMARY KEY (`line_drop_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `model_id` (`model_id`),
  ADD KEY `area_id` (`area_id`),
  ADD KEY `dropped_by` (`dropped_by`),
  ADD KEY `repaired_by` (`repaired_by`);

ALTER TABLE `model_action`
  ADD PRIMARY KEY (`model_id`,`action_id`),
  ADD KEY `action_id` (`action_id`);

ALTER TABLE `model_category`
  ADD PRIMARY KEY (`model_id`,`category_id`),
  ADD KEY `category_id` (`category_id`);

ALTER TABLE `model_cause`
  ADD PRIMARY KEY (`model_id`,`cause_id`),
  ADD KEY `cause_id` (`cause_id`);

ALTER TABLE `model_defect`
  ADD PRIMARY KEY (`model_id`,`defect_id`),
  ADD KEY `defect_id` (`defect_id`);

ALTER TABLE `model_master`
  ADD PRIMARY KEY (`model_id`),
  ADD UNIQUE KEY `model_code` (`model_code`);

ALTER TABLE `product_master`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `model_id` (`model_id`);

ALTER TABLE `user_master`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

ALTER TABLE `action_master`
  MODIFY `action_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `area_master`
  MODIFY `area_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `category_master`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `cause_master`
  MODIFY `cause_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `defect_master`
  MODIFY `defect_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `line_drop_header`
  MODIFY `line_drop_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `model_master`
  MODIFY `model_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `user_master`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `line_drop_action`
  ADD CONSTRAINT `line_drop_action_ibfk_1` FOREIGN KEY (`line_drop_id`) REFERENCES `line_drop_header` (`line_drop_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `line_drop_action_ibfk_2` FOREIGN KEY (`action_id`) REFERENCES `action_master` (`action_id`);

ALTER TABLE `line_drop_category`
  ADD CONSTRAINT `line_drop_category_ibfk_1` FOREIGN KEY (`line_drop_id`) REFERENCES `line_drop_header` (`line_drop_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `line_drop_category_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `category_master` (`category_id`);

ALTER TABLE `line_drop_cause`
  ADD CONSTRAINT `line_drop_cause_ibfk_1` FOREIGN KEY (`line_drop_id`) REFERENCES `line_drop_header` (`line_drop_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `line_drop_cause_ibfk_2` FOREIGN KEY (`cause_id`) REFERENCES `cause_master` (`cause_id`);

ALTER TABLE `line_drop_defect`
  ADD CONSTRAINT `line_drop_defect_ibfk_1` FOREIGN KEY (`line_drop_id`) REFERENCES `line_drop_header` (`line_drop_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `line_drop_defect_ibfk_2` FOREIGN KEY (`defect_id`) REFERENCES `defect_master` (`defect_id`);

ALTER TABLE `line_drop_header`
  ADD CONSTRAINT `line_drop_header_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product_master` (`product_id`),
  ADD CONSTRAINT `line_drop_header_ibfk_2` FOREIGN KEY (`model_id`) REFERENCES `model_master` (`model_id`),
  ADD CONSTRAINT `line_drop_header_ibfk_3` FOREIGN KEY (`area_id`) REFERENCES `area_master` (`area_id`),
  ADD CONSTRAINT `line_drop_header_ibfk_4` FOREIGN KEY (`dropped_by`) REFERENCES `user_master` (`user_id`),
  ADD CONSTRAINT `line_drop_header_ibfk_5` FOREIGN KEY (`repaired_by`) REFERENCES `user_master` (`user_id`);

ALTER TABLE `model_action`
  ADD CONSTRAINT `model_action_ibfk_1` FOREIGN KEY (`model_id`) REFERENCES `model_master` (`model_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `model_action_ibfk_2` FOREIGN KEY (`action_id`) REFERENCES `action_master` (`action_id`) ON DELETE CASCADE;

ALTER TABLE `model_category`
  ADD CONSTRAINT `model_category_ibfk_1` FOREIGN KEY (`model_id`) REFERENCES `model_master` (`model_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `model_category_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `category_master` (`category_id`) ON DELETE CASCADE;

ALTER TABLE `model_cause`
  ADD CONSTRAINT `model_cause_ibfk_1` FOREIGN KEY (`model_id`) REFERENCES `model_master` (`model_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `model_cause_ibfk_2` FOREIGN KEY (`cause_id`) REFERENCES `cause_master` (`cause_id`) ON DELETE CASCADE;

ALTER TABLE `model_defect`
  ADD CONSTRAINT `model_defect_ibfk_1` FOREIGN KEY (`model_id`) REFERENCES `model_master` (`model_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `model_defect_ibfk_2` FOREIGN KEY (`defect_id`) REFERENCES `defect_master` (`defect_id`) ON DELETE CASCADE;

ALTER TABLE `product_master`
  ADD CONSTRAINT `product_master_ibfk_1` FOREIGN KEY (`model_id`) REFERENCES `model_master` (`model_id`);
COMMIT;