<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method Not Allowed. Use GET request.',
        'data' => [],
    ]);
    exit;
}

$connection = getPractical16Connection();
if ($connection['error'] !== '') {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $connection['error'],
        'data' => [],
    ]);
    exit;
}

try {
    $pdo = $connection['pdo'];
    $stmt = $pdo->query('SELECT enrollment_no, full_name, branch FROM students ORDER BY id DESC');
    $rows = $stmt->fetchAll();

    $students = array_map(static function ($row) {
        return [
            'enrollment_no' => $row['enrollment_no'],
            'name' => $row['full_name'],
            'branch' => $row['branch'],
        ];
    }, $rows);

    echo json_encode([
        'success' => true,
        'message' => 'Students fetched successfully.',
        'data' => $students,
    ]);
} catch (PDOException $exception) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Unable to fetch students: ' . $exception->getMessage(),
        'data' => [],
    ]);
}
