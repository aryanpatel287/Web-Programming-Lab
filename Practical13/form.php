<?php
require_once __DIR__ . '/config.php';

$id = (int) ($_GET['id'] ?? 0);
$isEdit = $id > 0;
$message = '';
$messageType = '';

$student = [
    'enrollment_no' => '',
    'full_name' => '',
    'branch' => '',
    'email' => '',
    'phone' => '',
];

$connection = getPractical13Connection();
if ($connection['error'] !== '') {
    $message = $connection['error'];
    $messageType = 'error';
} else {
    $pdo = $connection['pdo'];

    if ($isEdit) {
        $stmt = $pdo->prepare('SELECT id, enrollment_no, full_name, branch, email, phone FROM students WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $existing = $stmt->fetch();

        if (!$existing) {
            header('Location: index.php?status=error&message=' . urlencode('Record not found.'));
            exit;
        }

        $student = $existing;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $student['enrollment_no'] = trim($_POST['enrollment_no'] ?? '');
        $student['full_name'] = trim($_POST['full_name'] ?? '');
        $student['branch'] = trim($_POST['branch'] ?? '');
        $student['email'] = trim($_POST['email'] ?? '');
        $student['phone'] = trim($_POST['phone'] ?? '');

        if (
            $student['enrollment_no'] === '' ||
            $student['full_name'] === '' ||
            $student['branch'] === '' ||
            $student['email'] === '' ||
            $student['phone'] === ''
        ) {
            $message = 'Please fill all required fields.';
            $messageType = 'error';
        } elseif (!filter_var($student['email'], FILTER_VALIDATE_EMAIL)) {
            $message = 'Please enter a valid email address.';
            $messageType = 'error';
        } else {
            try {
                $duplicateSql = 'SELECT id FROM students WHERE enrollment_no = :enrollment_no';
                if ($isEdit) {
                    $duplicateSql .= ' AND id <> :id';
                }

                $duplicateStmt = $pdo->prepare($duplicateSql);
                $params = [':enrollment_no' => $student['enrollment_no']];
                if ($isEdit) {
                    $params[':id'] = $id;
                }
                $duplicateStmt->execute($params);

                if ($duplicateStmt->fetch()) {
                    $message = 'Enrollment number already exists.';
                    $messageType = 'error';
                } else {
                    if ($isEdit) {
                        $sql = 'UPDATE students SET enrollment_no = :enrollment_no, full_name = :full_name, branch = :branch, email = :email, phone = :phone WHERE id = :id';
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([
                            ':enrollment_no' => $student['enrollment_no'],
                            ':full_name' => $student['full_name'],
                            ':branch' => $student['branch'],
                            ':email' => $student['email'],
                            ':phone' => $student['phone'],
                            ':id' => $id,
                        ]);
                    } else {
                        $sql = 'INSERT INTO students (enrollment_no, full_name, branch, email, phone) VALUES (:enrollment_no, :full_name, :branch, :email, :phone)';
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute([
                            ':enrollment_no' => $student['enrollment_no'],
                            ':full_name' => $student['full_name'],
                            ':branch' => $student['branch'],
                            ':email' => $student['email'],
                            ':phone' => $student['phone'],
                        ]);
                    }

                    header('Location: index.php?status=saved');
                    exit;
                }
            } catch (PDOException $exception) {
                $message = 'Unable to save record: ' . $exception->getMessage();
                $messageType = 'error';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practical 13 - <?php echo $isEdit ? 'Edit Student' : 'Add Student'; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="page form-page">
        <section class="panel">
            <h1><?php echo $isEdit ? 'Edit Student Record' : 'Add Student Record'; ?></h1>
            <p class="hint">Fill student details and submit.</p>

            <?php if ($message !== ''): ?>
                <div class="message <?php echo htmlspecialchars($messageType); ?>"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <form method="post" action="">
                <label for="enrollment_no">Enrollment No <span>*</span></label>
                <input type="text" id="enrollment_no" name="enrollment_no" value="<?php echo htmlspecialchars($student['enrollment_no']); ?>" required>

                <label for="full_name">Full Name <span>*</span></label>
                <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($student['full_name']); ?>" required>

                <label for="branch">Branch <span>*</span></label>
                <select id="branch" name="branch" required>
                    <option value="">-- Select Branch --</option>
                    <option value="CE" <?php echo $student['branch'] === 'CE' ? 'selected' : ''; ?>>Computer Engineering</option>
                    <option value="EC" <?php echo $student['branch'] === 'EC' ? 'selected' : ''; ?>>Electronics &amp; Communication</option>
                    <option value="ME" <?php echo $student['branch'] === 'ME' ? 'selected' : ''; ?>>Mechanical Engineering</option>
                    <option value="CV" <?php echo $student['branch'] === 'CV' ? 'selected' : ''; ?>>Civil Engineering</option>
                    <option value="EE" <?php echo $student['branch'] === 'EE' ? 'selected' : ''; ?>>Electrical Engineering</option>
                </select>

                <label for="email">Email <span>*</span></label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>

                <label for="phone">Phone <span>*</span></label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($student['phone']); ?>" required>

                <button type="submit"><?php echo $isEdit ? 'Update Record' : 'Save Record'; ?></button>
                <a class="button-link secondary" href="index.php">Back to List</a>
            </form>
        </section>
    </main>
</body>
</html>
