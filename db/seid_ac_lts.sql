-- ======================================
-- DATABASE
-- ======================================
CREATE DATABASE IF NOT EXISTS seid_ac_lts;
USE seid_ac_lts;

-- ======================================
-- USER MASTER
-- ======================================
CREATE TABLE user_master (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(30) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    fullname VARCHAR(50),
    role ENUM('OPERATOR','LEADER','QC','ADMIN') NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ======================================
-- MASTER TABLES
-- ======================================
CREATE TABLE model_master (
    model_id INT AUTO_INCREMENT PRIMARY KEY,
    model_code VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE defect_master (
    defect_id INT AUTO_INCREMENT PRIMARY KEY,
    defect_name VARCHAR(50) NOT NULL
);

CREATE TABLE category_master (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(50) NOT NULL
);

CREATE TABLE cause_master (
    cause_id INT AUTO_INCREMENT PRIMARY KEY,
    cause_name VARCHAR(50) NOT NULL
);

CREATE TABLE action_master (
    action_id INT AUTO_INCREMENT PRIMARY KEY,
    action_name VARCHAR(50) NOT NULL
);

CREATE TABLE area_master (
    area_id INT AUTO_INCREMENT PRIMARY KEY,
    area_name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(100)
);

-- ======================================
-- MODEL RELATION (CHECKLIST CONFIG)
-- ======================================
CREATE TABLE model_defect (
    model_id INT,
    defect_id INT,
    PRIMARY KEY (model_id, defect_id),
    FOREIGN KEY (model_id) REFERENCES model_master(model_id)
        ON DELETE CASCADE,
    FOREIGN KEY (defect_id) REFERENCES defect_master(defect_id)
        ON DELETE CASCADE
);

CREATE TABLE model_category (
    model_id INT,
    category_id INT,
    PRIMARY KEY (model_id, category_id),
    FOREIGN KEY (model_id) REFERENCES model_master(model_id)
        ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES category_master(category_id)
        ON DELETE CASCADE
);

CREATE TABLE model_cause (
    model_id INT,
    cause_id INT,
    PRIMARY KEY (model_id, cause_id),
    FOREIGN KEY (model_id) REFERENCES model_master(model_id)
        ON DELETE CASCADE,
    FOREIGN KEY (cause_id) REFERENCES cause_master(cause_id)
        ON DELETE CASCADE
);

CREATE TABLE model_action (
    model_id INT,
    action_id INT,
    PRIMARY KEY (model_id, action_id),
    FOREIGN KEY (model_id) REFERENCES model_master(model_id)
        ON DELETE CASCADE,
    FOREIGN KEY (action_id) REFERENCES action_master(action_id)
        ON DELETE CASCADE
);

-- ======================================
-- PRODUCT / SERIAL
-- ======================================
CREATE TABLE product_master (
    product_id VARCHAR(50) PRIMARY KEY,
    model_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (model_id) REFERENCES model_master(model_id)
);

-- ======================================
-- LINE DROP HEADER (HISTORY)
-- ======================================
CREATE TABLE line_drop_header (
    line_drop_id INT AUTO_INCREMENT PRIMARY KEY,

    product_id VARCHAR(50) NOT NULL,
    model_id INT NOT NULL,
    area_id INT NOT NULL,

    drop_datetime DATETIME DEFAULT CURRENT_TIMESTAMP,
    dropped_by INT NOT NULL,

    repair_datetime DATETIME NULL,
    repaired_by INT NULL,

    line_name VARCHAR(50),
    status ENUM('OPEN','CLOSE') DEFAULT 'OPEN',

    FOREIGN KEY (product_id) REFERENCES product_master(product_id),
    FOREIGN KEY (model_id) REFERENCES model_master(model_id),
    FOREIGN KEY (area_id) REFERENCES area_master(area_id),
    FOREIGN KEY (dropped_by) REFERENCES user_master(user_id),
    FOREIGN KEY (repaired_by) REFERENCES user_master(user_id)
);

-- ======================================
-- LINE DROP DETAIL (CHECKLIST RESULT)
-- ======================================
CREATE TABLE line_drop_defect (
    line_drop_id INT,
    defect_id INT,
    PRIMARY KEY (line_drop_id, defect_id),
    FOREIGN KEY (line_drop_id) REFERENCES line_drop_header(line_drop_id)
        ON DELETE CASCADE,
    FOREIGN KEY (defect_id) REFERENCES defect_master(defect_id)
);

CREATE TABLE line_drop_category (
    line_drop_id INT,
    category_id INT,
    PRIMARY KEY (line_drop_id, category_id),
    FOREIGN KEY (line_drop_id) REFERENCES line_drop_header(line_drop_id)
        ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES category_master(category_id)
);

CREATE TABLE line_drop_cause (
    line_drop_id INT,
    cause_id INT,
    PRIMARY KEY (line_drop_id, cause_id),
    FOREIGN KEY (line_drop_id) REFERENCES line_drop_header(line_drop_id)
        ON DELETE CASCADE,
    FOREIGN KEY (cause_id) REFERENCES cause_master(cause_id)
);

CREATE TABLE line_drop_action (
    line_drop_id INT,
    action_id INT,
    PRIMARY KEY (line_drop_id, action_id),
    FOREIGN KEY (line_drop_id) REFERENCES line_drop_header(line_drop_id)
        ON DELETE CASCADE,
    FOREIGN KEY (action_id) REFERENCES action_master(action_id)
);
