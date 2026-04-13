<?php
session_start();

$message = '';
$maxFileSize = 2 * 1024 * 1024;
$allowedTypes = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/gif' => 'gif',
    'image/webp' => 'webp',
];

function oldValue($field) {
        $message = 'Please enter the required details: full name, email, and profile picture.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $occupation = trim($_POST['occupation'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $birthDate = trim($_POST['birth_date'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $profileImage = '';

    if ($name === '' || $email === '') {
        $message = 'Please enter the required details: full name and email.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address.';
    } elseif (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['profile_picture'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $message = 'File upload failed. Please try again.';
        } elseif (!is_uploaded_file($file['tmp_name'])) {
            $message = 'Invalid uploaded file.';
        } elseif ($file['size'] > $maxFileSize) {
            $message = 'Image size must be 2 MB or less.';
        } else {
            $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
            if ($fileInfo === false) {
                $message = 'Unable to validate file type.';
            } else {
                $mimeType = finfo_file($fileInfo, $file['tmp_name']);
                finfo_close($fileInfo);

                $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

                if (!array_key_exists($mimeType, $allowedTypes) || $allowedTypes[$mimeType] !== $extension) {
                    $message = 'Only JPG, PNG, GIF, and WEBP image files are allowed.';
                } else {
                    $uploadDir = __DIR__ . '/uploads';
                    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
                        $message = 'Upload directory is not available.';
                    } else {
                        $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $name);
                        $fileName = $safeName . '_' . time() . '.' . $extension;
                        $uploadPath = $uploadDir . '/' . $fileName;

                        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                            $profileImage = 'uploads/' . $fileName;
                        } else {
                            $message = 'Unable to save the uploaded file.';
                        }
                    }
                }
            }
        }
    }

    if ($message === '') {
        $_SESSION['profile'] = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'city' => $city,
            'occupation' => $occupation,
            'gender' => $gender,
            'birthDate' => $birthDate,
            'bio' => $bio,
            'image' => $profileImage,
        ];

        header('Location: profile.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practical 11 - Create Profile</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="page single-page">
        <section class="panel form-panel">
            <h1>Create User Profile</h1>
            <p class="hint">Full name, email, and profile picture are required. Other details are optional.</p>

            <?php if ($message !== ''): ?>
                <div class="message error"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <form method="post" action="" enctype="multipart/form-data">
                <div class="form-grid">
                    <div>
                        <label for="name">Full Name <span>*</span></label>
                        <input type="text" id="name" name="name" value="<?php echo oldValue('name'); ?>" required>
                    </div>

                    <div>
                        <label for="email">Email <span>*</span></label>
                        <input type="email" id="email" name="email" value="<?php echo oldValue('email'); ?>" required>
                    </div>

                    <div>
                        <label for="phone">Phone</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo oldValue('phone'); ?>">
                    </div>

                    <div>
                        <label for="city">City</label>
                        <input type="text" id="city" name="city" value="<?php echo oldValue('city'); ?>">
                    </div>

                    <div>
                        <label for="occupation">Occupation</label>
                        <input type="text" id="occupation" name="occupation" value="<?php echo oldValue('occupation'); ?>">
                    </div>

                    <div>
                        <label for="gender">Gender</label>
                        <select id="gender" name="gender">
                            <option value="">Select gender</option>
                            <option value="Female" <?php echo ($_POST['gender'] ?? '') === 'Female' ? 'selected' : ''; ?>>Female</option>
                            <option value="Male" <?php echo ($_POST['gender'] ?? '') === 'Male' ? 'selected' : ''; ?>>Male</option>
                            <option value="Other" <?php echo ($_POST['gender'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>

                    <div>
                        <label for="birth_date">Date of Birth</label>
                        <input type="date" id="birth_date" name="birth_date" value="<?php echo oldValue('birth_date'); ?>">
                    </div>

                    <div>
                        <label for="profile_picture">Profile Picture <span>*</span></label>
                        <input type="file" id="profile_picture" name="profile_picture" accept="image/jpeg,image/png,image/gif,image/webp" required>
                    </div>
                </div>

                <label for="bio">Short Bio</label>
                <textarea id="bio" name="bio" rows="4"><?php echo oldValue('bio'); ?></textarea>

                <button type="submit">Create Profile Page</button>
            </form>
        </section>
    </main>
</body>
</html>
