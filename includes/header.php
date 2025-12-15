<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Tracker</title>
    <link rel="stylesheet" href="/attendance-tracker/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <h1>Attendance Tracker</h1>
            </div>
            <nav>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <span>Привет, <?php echo htmlspecialchars($_SESSION['name']); ?></span>
                    <a href="/attendance-tracker/auth/logout.php" class="logout-btn">Выйти</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    <main class="container">