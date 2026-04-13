<?php
$DB_HOST = 'YOUR_DB_HOST';
$DB_PORT = 'YOUR_DB_PORT';
$DB_NAME = 'YOUR_DB_NAME';
$DB_USER = 'YOUR_DB_USER';
$DB_PASS = 'YOUR_DB_PASSWORD';

function ensurePractical14Schema(PDO $pdo): void
{
    $sql = "CREATE TABLE IF NOT EXISTS students (
        id INT AUTO_INCREMENT PRIMARY KEY,
        enrollment_no VARCHAR(20) NOT NULL UNIQUE,
        full_name VARCHAR(120) NOT NULL,
        branch VARCHAR(20) NOT NULL,
        email VARCHAR(120) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    $pdo->exec($sql);
}

function getPractical14Connection(): array
{
    global $DB_HOST, $DB_PORT, $DB_NAME, $DB_USER, $DB_PASS;

    $lastError = '';

    try {
        $dbDsn = "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset=utf8mb4";
        $pdo = new PDO($dbDsn, $DB_USER, $DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        ensurePractical14Schema($pdo);
        return ['pdo' => $pdo, 'error' => ''];
    } catch (PDOException $exception) {
        $lastError = $exception->getMessage();
    }

    return ['pdo' => null, 'error' => 'Database connection failed: ' . $lastError . ' (Create this database first in InfinityFree panel: ' . $DB_NAME . ')'];
}
