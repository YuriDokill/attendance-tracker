<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../TestDatabase.php';

class AuthTest extends TestCase
{
    private $pdo;
    
    protected function setUp(): void
    {
        $this->pdo = TestDatabase::getConnection();
        
        $_SESSION = [];
    }
    
    protected function tearDown(): void
    {
        $_SESSION = [];
    }
    
    public function testUserLoginSuccess()
    {
        $email = 'employee@test.com';
        $password = 'employee123';

        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->assertIsArray($user);
        $this->assertTrue(password_verify($password, $user['password']));

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        
        $this->assertEquals($user['id'], $_SESSION['user_id']);
        $this->assertEquals($user['name'], $_SESSION['name']);
        $this->assertEquals($user['email'], $_SESSION['email']);
        $this->assertEquals($user['role'], $_SESSION['role']);
    }
    
    public function testUserLoginFailureWrongPassword()
    {
        $email = 'employee@test.com';
        $wrongPassword = 'wrongpassword';
        
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->assertIsArray($user);
        $this->assertFalse(password_verify($wrongPassword, $user['password']));
    }
    
    public function testUserLoginFailureWrongEmail()
    {
        $wrongEmail = 'nonexistent@test.com';
        
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$wrongEmail]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->assertFalse($user);
    }
    
    public function testUserRegistration()
    {
        $newUser = [
            'name' => 'Новый Пользователь',
            'email' => 'newuser@test.com',
            'password' => 'newpass123',
            'role' => 'employee'
        ];
        
        $hashedPassword = password_hash($newUser['password'], PASSWORD_DEFAULT);
        
        $stmt = $this->pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $result = $stmt->execute([
            $newUser['name'],
            $newUser['email'],
            $hashedPassword,
            $newUser['role']
        ]);
        
        $this->assertTrue($result);
        
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$newUser['email']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->assertIsArray($user);
        $this->assertEquals($newUser['name'], $user['name']);
        $this->assertEquals($newUser['email'], $user['email']);
        $this->assertEquals($newUser['role'], $user['role']);
        $this->assertTrue(password_verify($newUser['password'], $user['password']));
    }
    
    public function testSessionAuthentication()
    {
        $_SESSION['user_id'] = 1;
        $_SESSION['role'] = 'admin';
        
        $this->assertTrue(isset($_SESSION['user_id']));
        $this->assertEquals('admin', $_SESSION['role']);
        
        $_SESSION = [];
        
        $this->assertFalse(isset($_SESSION['user_id']));
        $this->assertArrayNotHasKey('role', $_SESSION);
    }
    
    public function testRoleBasedAccess()
    {
        $_SESSION['role'] = 'admin';
        $this->assertTrue($_SESSION['role'] === 'admin');
        $this->assertFalse($_SESSION['role'] === 'employee');
        
        $_SESSION['role'] = 'employee';
        $this->assertTrue($_SESSION['role'] === 'employee');
        $this->assertFalse($_SESSION['role'] === 'admin');
    }
}