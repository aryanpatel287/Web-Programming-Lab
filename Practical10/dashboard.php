<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}

$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="page narrow-page">
        <section class="panel">
            <h1>Dashboard</h1>
            <p class="welcome">Welcome, <?php echo htmlspecialchars($username); ?>.</p>
            <p class="hint">Your username is stored in a session variable and remains available on other pages.</p>

            <div class="actions">
                <a class="button-link" href="profile.php">Open Profile Page</a>
                <form method="post" action="logout.php">
                    <button type="submit">Logout</button>
                </form>
            </div>
        </section>
    </main>
</body>
</html>
