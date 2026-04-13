<?php
session_start();

$validUsername = 'admin';
$validPassword = 'admin123';
$error = '';
$savedUsername = $_COOKIE['remembered_username'] ?? '';

if (isset($_SESSION['username'])) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $rememberMe = isset($_POST['remember_me']);

    if ($username === $validUsername && $password === $validPassword) {
        $_SESSION['username'] = $username;

        if ($rememberMe) {
            setcookie('remembered_username', $username, time() + (7 * 24 * 60 * 60), '/');
        } else {
            setcookie('remembered_username', '', time() - 3600, '/');
        }

        header('Location: dashboard.php');
        exit;
    }

    $error = 'Invalid username or password.';
    $savedUsername = $username;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practical 10 - Login System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="page narrow-page">
        <section class="panel">
            <h1>Login System</h1>
            <p class="hint">Use username <strong>admin</strong> and password <strong>admin123</strong>.</p>

            <?php if ($error !== ''): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="post" action="">
                <label for="username">Username</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    value="<?php echo htmlspecialchars($savedUsername); ?>"
                    required
                >

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>

                <label class="checkbox-row">
                    <input
                        type="checkbox"
                        name="remember_me"
                        <?php echo $savedUsername !== '' ? 'checked' : ''; ?>
                    >
                    <span>Remember Me</span>
                </label>

                <button type="submit">Login</button>
            </form>
        </section>
    </main>
</body>
</html>
