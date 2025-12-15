<?php
class TestDatabase
{
    private static $pdo = null;
    
    public static function getConnection()
    {
        if (self::$pdo === null) {
            try {
                self::$pdo = new PDO(
                    "mysql:host=" . DB_HOST . ";charset=utf8",
                    DB_USER,
                    DB_PASS,
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
                
                self::$pdo->exec("DROP DATABASE IF EXISTS " . DB_NAME);
                self::$pdo->exec("CREATE DATABASE " . DB_NAME);
                self::$pdo->exec("USE " . DB_NAME);
                
                self::createTables();
                
                self::seedTestData();
            } catch (PDOException $e) {
                die("Ошибка подключения к тестовой БД: " . $e->getMessage());
            }
        }
        
        return self::$pdo;
    }
    
    private static function createTables()
    {
        $sql = "
        CREATE TABLE users (
            id INT PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            role ENUM('employee', 'admin') DEFAULT 'employee',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
        
        CREATE TABLE attendance_records (
            id INT PRIMARY KEY AUTO_INCREMENT,
            user_id INT NOT NULL,
            date DATE NOT NULL,
            type ENUM('late', 'absence', 'vacation', 'own_account') NOT NULL,
            reason TEXT,
            status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        );";
        
        self::$pdo->exec($sql);
    }
    
    public static function seedTestData()
{
    $pdo = self::getConnection();
    
    $pdo->exec("DELETE FROM attendance_records");
    $pdo->exec("DELETE FROM users");
    $pdo->exec("ALTER TABLE users AUTO_INCREMENT = 1");
    $pdo->exec("ALTER TABLE attendance_records AUTO_INCREMENT = 1");
    
    $users = [
        ['Тестовый Администратор', 'admin@test.com', password_hash('admin123', PASSWORD_DEFAULT), 'admin'],
        ['Тестовый Сотрудник', 'employee@test.com', password_hash('employee123', PASSWORD_DEFAULT), 'employee']
    ];
    
    foreach ($users as $user) {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute($user);
    }
    
    $records = [
        [2, '2024-01-10', 'late', 'Пробки на дорогах', 'approved'],
        [2, '2024-01-15', 'vacation', 'Ежегодный отпуск', 'pending']
    ];
    
    foreach ($records as $record) {
        $stmt = $pdo->prepare("INSERT INTO attendance_records (user_id, date, type, reason, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute($record);
    }
}
}