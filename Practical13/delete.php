<?php
require_once __DIR__ . '/config.php';

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: index.php?status=error&message=' . urlencode('Invalid record id.'));
    exit;
}

$connection = getPractical13Connection();
if ($connection['error'] !== '') {
    header('Location: index.php?status=error&message=' . urlencode($connection['error']));
    exit;
}

try {
    $pdo = $connection['pdo'];
    $stmt = $pdo->prepare('DELETE FROM students WHERE id = :id');
    $stmt->execute([':id' => $id]);

    header('Location: index.php?status=deleted');
    exit;
} catch (PDOException $exception) {
    header('Location: index.php?status=error&message=' . urlencode('Delete failed: ' . $exception->getMessage()));
    exit;
}
