<?php

use PHPUnit\Framework\TestCase;


class SessionTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }
    
    protected function tearDown(): void
    {
        $_SESSION = [];
    }
    
    public function testSessionStart()
    {
        $this->assertArrayNotHasKey('user_id', $_SESSION);
        $this->assertArrayNotHasKey('role', $_SESSION);
        
        $_SESSION['user_id'] = 1;
        $_SESSION['role'] = 'admin';
        
        $this->assertEquals(1, $_SESSION['user_id']);
        $this->assertEquals('admin', $_SESSION['role']);
    }
    
    public function testSessionVariables()
    {
        $_SESSION['test_key'] = 'test_value';
        $this->assertArrayHasKey('test_key', $_SESSION);
        $this->assertEquals('test_value', $_SESSION['test_key']);
    }
}