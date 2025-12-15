<?php
if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');
}
if (!defined('DB_NAME')) {
    define('DB_NAME', 'attendance_tracker');
}
if (!defined('DB_USER')) {
    define('DB_USER', 'root');
}
if (!defined('DB_PASS')) {
    define('DB_PASS', '');
}

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch(PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function checkAuth() {
    if (!isset($_SESSION['user_id'])) {
        redirect('/attendance-tracker/auth/login.php');
    }
}

function checkAdmin() {
    checkAuth();
    if ($_SESSION['role'] !== 'admin') {
        die('Доступ запрещен. Только для администраторов.');
    }
}

function checkEmployee() {
    checkAuth();
    if ($_SESSION['role'] !== 'employee') {
        die('Доступ запрещен. Только для сотрудников.');
    }
}