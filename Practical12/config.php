<?php
$DB_HOST = 'sql100.infinityfree.com';
$DB_PORT = '3306';
$DB_NAME = 'if0_41652976_wp_lab_practical12';
$DB_USER = 'if0_41652976';
$DB_PASS = 'Aryan2878980';

function ensurePractical12Schema(PDO $pdo): void
{
    $sql = "CREATE TABLE IF NOT EXISTS student_event_registrations (
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
    )";

    $pdo->exec($sql);
}

function getDatabaseConnection(): array
{
    global $DB_HOST, $DB_PORT, $DB_NAME, $DB_USER, $DB_PASS;

    $lastError = '';

    try {
        $dbDsn = "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset=utf8mb4";
        $pdo = new PDO($dbDsn, $DB_USER, $DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        ensurePractical12Schema($pdo);
        return ['pdo' => $pdo, 'error' => ''];
    } catch (PDOException $exception) {
        $lastError = $exception->getMessage();
    }

    return ['pdo' => null, 'error' => 'Database connection failed: ' . $lastError . ' (Create this database first in InfinityFree panel: ' . $DB_NAME . ')'];
}
