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
    <title>Profile</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="page narrow-page">
        <section class="panel">
            <h1>Profile Page</h1>
            <p class="welcome">Session username: <?php echo htmlspecialchars($username); ?></p>
            <p class="hint">This page confirms that the login state is maintained across pages using PHP sessions.</p>

            <div class="actions">
                <a class="button-link secondary" href="dashboard.php">Back to Dashboard</a>
                <form method="post" action="logout.php">
                    <button type="submit">Logout</button>
                </form>
            </div>
        </section>
    </main>
</body>
</html>
