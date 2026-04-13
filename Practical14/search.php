<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=utf-8');

$query = trim($_GET['q'] ?? '');
$connection = getPractical14Connection();

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
    $sql = 'SELECT enrollment_no, full_name, branch, email, phone FROM students WHERE full_name LIKE :q OR enrollment_no LIKE :q ORDER BY id DESC LIMIT 50';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':q' => '%' . $query . '%']);
    $records = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'message' => count($records) === 0 ? 'No records found.' : '',
        'data' => $records,
    ]);
} catch (PDOException $exception) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Search failed: ' . $exception->getMessage(),
        'data' => [],
    ]);
}
