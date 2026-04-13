CREATE DATABASE IF NOT EXISTS wp_lab_practical12;
USE wp_lab_practical12;

CREATE TABLE IF NOT EXISTS student_event_registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    dob DATE NOT NULL,
    gender ENUM('male', 'female', 'other') NOT NULL,
    tshirt_color VARCHAR(7) DEFAULT '#4a90d9',
    enrollment_number VARCHAR(12) NOT NULL,
    branch VARCHAR(10) NOT NULL,
    semester TINYINT NOT NULL,
    cgpa DECIMAL(4,2) NULL,
    event_name VARCHAR(120) NOT NULL,
    team_size TINYINT NULL,
    skills VARCHAR(255) NULL,
    portfolio_url VARCHAR(255) NULL,
    experience TEXT NULL,
    expectations TEXT NULL,
    heard_from VARCHAR(30) NOT NULL,
    terms_accepted TINYINT(1) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
