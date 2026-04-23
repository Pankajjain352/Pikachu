-- ============================================
-- Student Result Management System (SRMS)
-- Database Setup Script
-- Run this in phpMyAdmin or MySQL CLI
-- ============================================

-- Create the database
CREATE DATABASE IF NOT EXISTS srms_db;
USE srms_db;

-- ============================================
-- Admin table
-- ============================================
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin (password: admin123)
INSERT INTO admins (username, password, full_name) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator');

-- ============================================
-- Students table
-- ============================================
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    roll_no VARCHAR(20) NOT NULL UNIQUE,
    class VARCHAR(10) NOT NULL,
    section VARCHAR(5) DEFAULT 'A',
    math INT NOT NULL CHECK (math BETWEEN 0 AND 100),
    science INT NOT NULL CHECK (science BETWEEN 0 AND 100),
    english INT NOT NULL CHECK (english BETWEEN 0 AND 100),
    hindi INT NOT NULL CHECK (hindi BETWEEN 0 AND 100),
    social_science INT NOT NULL CHECK (social_science BETWEEN 0 AND 100),
    total INT GENERATED ALWAYS AS (math + science + english + hindi + social_science) STORED,
    percentage DECIMAL(5,2) GENERATED ALWAYS AS ((math + science + english + hindi + social_science) / 5) STORED,
    grade VARCHAR(5) GENERATED ALWAYS AS (
        CASE
            WHEN (math + science + english + hindi + social_science) / 5 >= 90 THEN 'A+'
            WHEN (math + science + english + hindi + social_science) / 5 >= 80 THEN 'A'
            WHEN (math + science + english + hindi + social_science) / 5 >= 65 THEN 'B'
            WHEN (math + science + english + hindi + social_science) / 5 >= 50 THEN 'C'
            ELSE 'F'
        END
    ) STORED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Insert sample dummy data (12 students)
-- ============================================
INSERT INTO students (name, roll_no, class, section, math, science, english, hindi, social_science) VALUES
('Arihant Sharma',    '2024001', '12th', 'A', 92, 88, 78, 85, 90),
('Priya Verma',       '2024002', '12th', 'A', 75, 82, 90, 88, 70),
('Rahul Singh',       '2024003', '11th', 'B', 55, 60, 65, 70, 50),
('Sneha Patel',       '2024004', '10th', 'A', 95, 97, 92, 90, 94),
('Amit Kumar',        '2024005', '12th', 'B', 40, 38, 45, 50, 35),
('Kavya Nair',        '2024006', '11th', 'A', 83, 79, 88, 72, 80),
('Rohit Mehra',       '2024007', '10th', 'C', 67, 72, 58, 65, 60),
('Ananya Gupta',      '2024008', '12th', 'A', 88, 91, 85, 80, 87),
('Vikram Joshi',      '2024009', '11th', 'B', 48, 52, 44, 55, 42),
('Divya Agarwal',     '2024010', '10th', 'A', 76, 80, 72, 68, 74),
('Siddharth Rao',     '2024011', '12th', 'C', 60, 65, 55, 70, 62),
('Meera Iyer',        '2024012', '11th', 'A', 90, 85, 95, 88, 92);
