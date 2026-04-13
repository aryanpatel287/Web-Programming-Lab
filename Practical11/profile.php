<?php
session_start();

if (!isset($_SESSION['profile'])) {
    header('Location: index.php');
    exit;
}

$profile = $_SESSION['profile'];
$imagePath = trim((string) ($profile['image'] ?? ''));
$hasImage = $imagePath !== '' && is_file(__DIR__ . '/' . $imagePath);

function showValue($value, $fallback = 'Not provided') {
    $value = trim((string) $value);
    return htmlspecialchars($value !== '' ? $value : $fallback);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="page single-page">
        <section class="panel profile-page">
            <div class="profile-header">
                <?php if ($hasImage): ?>
                    <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="Profile picture">
                <?php else: ?>
                    <div class="avatar-placeholder">
                        <?php echo strtoupper(htmlspecialchars(substr($profile['name'], 0, 1))); ?>
                    </div>
                <?php endif; ?>

                <div>
                    <h1><?php echo showValue($profile['name']); ?></h1>
                    <p><?php echo showValue($profile['occupation'], 'Occupation not provided'); ?></p>
                </div>
            </div>

            <div class="details-grid">
                <div>
                    <span>Email</span>
                    <strong><?php echo showValue($profile['email']); ?></strong>
                </div>
                <div>
                    <span>Phone</span>
                    <strong><?php echo showValue($profile['phone']); ?></strong>
                </div>
                <div>
                    <span>City</span>
                    <strong><?php echo showValue($profile['city']); ?></strong>
                </div>
                <div>
                    <span>Gender</span>
                    <strong><?php echo showValue($profile['gender']); ?></strong>
                </div>
                <div>
                    <span>Date of Birth</span>
                    <strong><?php echo showValue($profile['birthDate']); ?></strong>
                </div>
            </div>

            <div class="bio-box">
                <span>Short Bio</span>
                <p><?php echo nl2br(showValue($profile['bio'], 'No bio added.')); ?></p>
            </div>

            <a class="button-link" href="index.php">Create Another Profile</a>
        </section>
    </main>
</body>
</html>
