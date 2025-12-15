<?php
define('TEST_ENV', true);

if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_NAME')) define('DB_NAME', 'attendance_tracker_test');
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASS')) define('DB_PASS', '');

require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/TestDatabase.php';

$_SESSION = [];
$_POST = [];
$_GET = [];