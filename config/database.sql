-- Database schema for A-Level Points and Weight Calculator
-- Creates database, tables, and constraints

CREATE DATABASE IF NOT EXISTS `charm_app_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `charm_app_db`;

-- Users table stores authentication and profile
CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, -- Primary key
    username VARCHAR(100) NOT NULL, -- Display name
    email VARCHAR(255) NOT NULL UNIQUE, -- Login email
    password_hash VARCHAR(255) NOT NULL, -- Hashed password
    gender ENUM('male','female') NOT NULL, -- Gender for bonus
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- Creation timestamp
) ENGINE=InnoDB;

-- O-Level subjects registered by the user (compulsory + optionals)
CREATE TABLE IF NOT EXISTS olevel_subjects (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, -- Primary key
    user_id INT UNSIGNED NOT NULL, -- Owner user
    name VARCHAR(100) NOT NULL, -- Subject name
    is_compulsory TINYINT(1) NOT NULL DEFAULT 0, -- Flag for compulsory
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Creation timestamp
    UNIQUE KEY user_subject (user_id, name), -- Prevent duplicates
    CONSTRAINT fk_os_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE -- Link to users
) ENGINE=InnoDB;

-- O-Level scores with computed bucket/weight per subject
CREATE TABLE IF NOT EXISTS olevel_scores (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, -- Primary key
    user_id INT UNSIGNED NOT NULL, -- Owner user
    subject_name VARCHAR(100) NOT NULL, -- Subject name
    grade VARCHAR(10) NOT NULL, -- Grade value (D1..F9)
    bucket ENUM('distinction','credit','pass','fail') NOT NULL, -- Grade bucket
    weight_value DECIMAL(3,1) NOT NULL, -- Weight contribution
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Creation timestamp
    UNIQUE KEY user_subject_grade (user_id, subject_name), -- One grade per subject
    CONSTRAINT fk_osc_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE -- Link to users
) ENGINE=InnoDB;

-- A-Level subjects (principles and subsidiaries)
CREATE TABLE IF NOT EXISTS alevel_subjects (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, -- Primary key
    user_id INT UNSIGNED NOT NULL, -- Owner user
    subject_name VARCHAR(100) NOT NULL, -- Subject name
    category ENUM('principle','subsidiary') NOT NULL, -- Subject category
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Creation timestamp
    UNIQUE KEY user_subject_category (user_id, subject_name), -- Prevent duplicates
    CONSTRAINT fk_as_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE -- Link to users
) ENGINE=InnoDB;

-- A-Level scores mapped to points
CREATE TABLE IF NOT EXISTS alevel_scores (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, -- Primary key
    user_id INT UNSIGNED NOT NULL, -- Owner user
    subject_name VARCHAR(100) NOT NULL, -- Subject name
    grade VARCHAR(10) NOT NULL, -- Grade value
    points INT NOT NULL, -- Points derived from grade
    category ENUM('principle','subsidiary') NOT NULL, -- Category for weighting
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Creation timestamp
    UNIQUE KEY user_subject_score (user_id, subject_name), -- One score per subject
    CONSTRAINT fk_asc_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE -- Link to users
) ENGINE=InnoDB;

-- Results/weights table storing computed totals and eligibility
CREATE TABLE IF NOT EXISTS results (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, -- Primary key
    user_id INT UNSIGNED NOT NULL, -- Owner user
    olevel_weight DECIMAL(5,2) NOT NULL DEFAULT 0, -- O-Level weight sum
    alevel_weight DECIMAL(6,2) NOT NULL DEFAULT 0, -- A-Level weighted score
    total_points INT NOT NULL DEFAULT 0, -- Raw A-Level points (principles+subs)
    gender_bonus DECIMAL(4,2) NOT NULL DEFAULT 0, -- Gender bonus
    cutoff DECIMAL(6,2) DEFAULT NULL, -- University cutoff provided by user
    total_weight DECIMAL(7,2) NOT NULL DEFAULT 0, -- Final total weight
    eligibility VARCHAR(50) DEFAULT NULL, -- Eligible/Not eligible
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Creation timestamp
    UNIQUE KEY user_result (user_id), -- One result per user
    CONSTRAINT fk_r_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE -- Link to users
) ENGINE=InnoDB;
