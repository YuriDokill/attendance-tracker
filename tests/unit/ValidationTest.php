<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../includes/functions.php';


class ValidationTest extends TestCase
{
    public function testValidateEmail()
    {
        $this->assertTrue(validateEmail('test@example.com'));
        $this->assertTrue(validateEmail('user.name@domain.co.uk'));
        $this->assertFalse(validateEmail('invalid-email'));
        $this->assertFalse(validateEmail('@domain.com'));
        $this->assertFalse(validateEmail('user@'));
    }
    
    public function testValidatePassword()
    {
        $this->assertTrue(validatePassword('123456'));
        $this->assertTrue(validatePassword('password123'));
        $this->assertFalse(validatePassword('12345'));
        $this->assertFalse(validatePassword(''));
        $this->assertFalse(validatePassword('123'));
    }
    
    public function testValidateName()
    {
        $this->assertTrue(validateName('Иван'));
        $this->assertTrue(validateName('John Doe'));
        $this->assertFalse(validateName(''));
        $this->assertFalse(validateName(' '));
        $this->assertFalse(validateName('A'));
    }
    
    public function testValidateDate()
    {
        $this->assertTrue(validateDate('2024-01-15'));
        $this->assertTrue(validateDate('2023-12-31'));
        $this->assertFalse(validateDate('2024-01-32'));
        $this->assertFalse(validateDate('invalid-date'));
        $this->assertFalse(validateDate('15-01-2024'));
    }
    
    public function testGetRecordTypeName()
    {
        $this->assertEquals('Опоздание', getRecordTypeName('late'));
        $this->assertEquals('Прогул', getRecordTypeName('absence'));
        $this->assertEquals('Отпуск', getRecordTypeName('vacation'));
        $this->assertEquals('За свой счет', getRecordTypeName('own_account'));
        $this->assertEquals('Неизвестный тип', getRecordTypeName('invalid'));
    }
    
    public function testGetStatusName()
    {
        $this->assertEquals('Ожидает', getStatusName('pending'));
        $this->assertEquals('Подтверждено', getStatusName('approved'));
        $this->assertEquals('Отклонено', getStatusName('rejected'));
        $this->assertEquals('Неизвестный статус', getStatusName('invalid'));
    }
    
    public function testRoleFunctions()
    {
        $this->assertTrue(isAdmin('admin'));
        $this->assertFalse(isAdmin('employee'));
        $this->assertFalse(isAdmin('invalid'));
        
        $this->assertTrue(isEmployee('employee'));
        $this->assertFalse(isEmployee('admin'));
        $this->assertFalse(isEmployee('invalid'));
    }
}