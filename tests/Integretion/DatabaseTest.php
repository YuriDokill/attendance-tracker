<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../TestDatabase.php';

class DatabaseTest extends TestCase
{
    private $pdo;
    
    protected function setUp(): void
    {
        $this->pdo = TestDatabase::getConnection();
    }
    
    protected function tearDown(): void
    {
        $this->pdo->exec("DELETE FROM attendance_records");
        $this->pdo->exec("DELETE FROM users");
    }
    
    public function testDatabaseConnection()
    {
        $this->assertInstanceOf(PDO::class, $this->pdo);
        
        $result = $this->pdo->query("SELECT 1");
        $this->assertNotFalse($result);
        $this->assertEquals(1, $result->fetchColumn());
    }
    
    public function testTablesExist()
    {
        $tables = ['users', 'attendance_records'];
        
        foreach ($tables as $table) {
            $stmt = $this->pdo->query("SHOW TABLES LIKE '$table'");
            $this->assertEquals(1, $stmt->rowCount(), "Таблица $table должна существовать");
        }
    }
    
    public function testUserInsertAndRetrieve()
    {
        $stmt = $this->pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $passwordHash = password_hash('testpass', PASSWORD_DEFAULT);
        $result = $stmt->execute(['Тест Пользователь', 'test@user.com', $passwordHash, 'employee']);
        
        $this->assertTrue($result);
        $userId = $this->pdo->lastInsertId();
        
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->assertIsArray($user);
        $this->assertEquals('Тест Пользователь', $user['name']);
        $this->assertEquals('test@user.com', $user['email']);
        $this->assertEquals('employee', $user['role']);
        $this->assertTrue(password_verify('testpass', $user['password']));
    }
    
    public function testAttendanceRecordInsert()
    {
        $stmt = $this->pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $passwordHash = password_hash('testpass', PASSWORD_DEFAULT);
        $stmt->execute(['Тест для записи', 'recorduser@test.com', $passwordHash, 'employee']);
        $userId = $this->pdo->lastInsertId();
        
        $stmt = $this->pdo->prepare("INSERT INTO attendance_records (user_id, date, type, reason, status) VALUES (?, ?, ?, ?, ?)");
        $result = $stmt->execute([$userId, '2024-01-20', 'absence', 'Болезнь', 'pending']);
        
        $this->assertTrue($result);
        
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM attendance_records WHERE user_id = ?");
        $stmt->execute([$userId]);
        $count = $stmt->fetchColumn();
        $this->assertEquals(1, $count);
    }
    
    public function testForeignKeyConstraint()
    {
        $this->expectException(PDOException::class);
        
        $stmt = $this->pdo->prepare("INSERT INTO attendance_records (user_id, date, type, reason, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([9999, '2024-01-20', 'late', 'Тест', 'pending']);
    }
    
    public function testUniqueEmailConstraint()
    {
        $email = 'uniquetest_' . time() . '@test.com';
        $stmt = $this->pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $passwordHash = password_hash('test123', PASSWORD_DEFAULT);
        $stmt->execute(['Уникальный Пользователь', $email, $passwordHash, 'employee']);
        
        $stmt = $this->pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $passwordHash2 = password_hash('test456', PASSWORD_DEFAULT);
        
        $this->expectException(PDOException::class);
        $this->expectExceptionMessageMatches('/1062|23000/');
        
        $stmt->execute(['Дубликат Пользователя', $email, $passwordHash2, 'employee']);
    }
    
    public function testUpdateRecord()
    {
        $stmt = $this->pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $passwordHash = password_hash('testuser', PASSWORD_DEFAULT);
        $stmt->execute(['Тест для обновления', 'update@test.com', $passwordHash, 'employee']);
        $userId = $this->pdo->lastInsertId();
        
        $stmt = $this->pdo->prepare("INSERT INTO attendance_records (user_id, date, type, reason, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$userId, '2024-02-01', 'late', 'Тест обновления', 'pending']);
        $recordId = $this->pdo->lastInsertId();
        
        $stmt = $this->pdo->prepare("UPDATE attendance_records SET status = 'approved' WHERE id = ?");
        $result = $stmt->execute([$recordId]);
        $this->assertTrue($result);
        
        $stmt = $this->pdo->prepare("SELECT status FROM attendance_records WHERE id = ?");
        $stmt->execute([$recordId]);
        $status = $stmt->fetchColumn();
        $this->assertEquals('approved', $status);
    }
    
    public function testDeleteRecord()
    {
        $stmt = $this->pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $passwordHash = password_hash('deleteuser', PASSWORD_DEFAULT);
        $stmt->execute(['Для удаления', 'delete@test.com', $passwordHash, 'employee']);
        $userId = $this->pdo->lastInsertId();
        
        $stmt = $this->pdo->prepare("INSERT INTO attendance_records (user_id, date, type, reason, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$userId, '2024-02-02', 'absence', 'Тест удаления', 'pending']);
        
        $stmt = $this->pdo->prepare("DELETE FROM attendance_records WHERE user_id = ? AND status = 'pending'");
        $result = $stmt->execute([$userId]);
        $this->assertTrue($result);
    }
    
    public function testCascadeDelete()
    {
        $stmt = $this->pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $passwordHash = password_hash('cascadeuser', PASSWORD_DEFAULT);
        $stmt->execute(['Каскадный Пользователь', 'cascade@test.com', $passwordHash, 'employee']);
        $userId = $this->pdo->lastInsertId();
        
        $stmt = $this->pdo->prepare("INSERT INTO attendance_records (user_id, date, type, reason, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$userId, '2024-01-25', 'late', 'Тест каскада', 'pending']);
        $stmt->execute([$userId, '2024-01-26', 'absence', 'Тест каскада 2', 'approved']);
        
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM attendance_records WHERE user_id = ?");
        $stmt->execute([$userId]);
        $recordsCount = $stmt->fetchColumn();
        $this->assertGreaterThan(0, $recordsCount);
        
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM attendance_records WHERE user_id = ?");
        $stmt->execute([$userId]);
        $remainingRecords = $stmt->fetchColumn();
        $this->assertEquals(0, $remainingRecords);
    }
}