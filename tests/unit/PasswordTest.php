<?php

use PHPUnit\Framework\TestCase;

class PasswordTest extends TestCase
{
    public function testPasswordHash()
    {
        $password = 'test123';
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        $this->assertTrue(password_verify($password, $hash));
        $this->assertFalse(password_verify('wrongpassword', $hash));
        $this->assertNotEquals($password, $hash);
    }
    
    public function testPasswordHashDifferentSalts()
    {
        $password = 'test123';
        $hash1 = password_hash($password, PASSWORD_DEFAULT);
        $hash2 = password_hash($password, PASSWORD_DEFAULT);
        
        $this->assertNotEquals($hash1, $hash2);
        $this->assertTrue(password_verify($password, $hash1));
        $this->assertTrue(password_verify($password, $hash2));
    }
    
    public function testPasswordNeedsRehash()
    {
        $password = 'test123';
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        $this->assertFalse(password_needs_rehash($hash, PASSWORD_DEFAULT));
        
        $md5Hash = md5($password);
        $this->assertTrue(password_needs_rehash($md5Hash, PASSWORD_DEFAULT));
    }
}