<?php
require_once __DIR__ . '/config.php';

$message = '';
$messageType = '';
$students = [];

if (isset($_GET['status'])) {
    if ($_GET['status'] === 'saved') {
        $message = 'Student record saved successfully.';
        $messageType = 'success';
    } elseif ($_GET['status'] === 'deleted') {
        $message = 'Student record deleted successfully.';
        $messageType = 'success';
    } elseif ($_GET['status'] === 'error') {
        $message = trim($_GET['message'] ?? 'Something went wrong.');
        $messageType = 'error';
    }
}

$connection = getPractical13Connection();
if ($connection['error'] !== '') {
    $message = $connection['error'];
    $messageType = 'error';
} else {
    try {
        $pdo = $connection['pdo'];
        $students = $pdo->query('SELECT id, enrollment_no, full_name, branch, email, phone FROM students ORDER BY id DESC')->fetchAll();
    } catch (PDOException $exception) {
        $message = 'Unable to load records: ' . $exception->getMessage();
        $messageType = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practical 13 - Student Information Management</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="page">
        <section class="panel">
            <div class="header-row">
                <div>
                    <h1>Student Information Management System</h1>
                    <p class="hint">Read student records and use Edit/Delete actions for each row.</p>
                </div>
                <a class="button-link" href="form.php">Add Student</a>
            </div>

            <?php if ($message !== ''): ?>
                <div class="message <?php echo htmlspecialchars($messageType); ?>"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Enrollment No</th>
                            <th>Name</th>
                            <th>Branch</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($students) === 0): ?>
                            <tr>
                                <td colspan="6" class="empty-row">No student records found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($student['enrollment_no']); ?></td>
                                    <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($student['branch']); ?></td>
                                    <td><?php echo htmlspecialchars($student['email']); ?></td>
                                    <td><?php echo htmlspecialchars($student['phone']); ?></td>
                                    <td>
                                        <div class="action-row">
                                            <a class="small-link" href="form.php?id=<?php echo (int) $student['id']; ?>">Edit</a>
                                            <a class="small-link danger" href="delete.php?id=<?php echo (int) $student['id']; ?>" onclick="return confirm('Delete this record?');">Delete</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>
