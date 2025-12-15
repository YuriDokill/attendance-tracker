<?php
function validateEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validatePassword($password)
{
    return strlen($password) >= 6;
}

function validateName($name)
{
    return !empty(trim($name)) && strlen(trim($name)) >= 2;
}

function validateDate($date)
{
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

function getRecordTypeName($type)
{
    $types = [
        'late' => 'Опоздание',
        'absence' => 'Прогул',
        'vacation' => 'Отпуск',
        'own_account' => 'За свой счет'
    ];
    
    return $types[$type] ?? 'Неизвестный тип';
}

function getStatusName($status)
{
    $statuses = [
        'pending' => 'Ожидает',
        'approved' => 'Подтверждено',
        'rejected' => 'Отклонено'
    ];
    
    return $statuses[$status] ?? 'Неизвестный статус';
}

function isAdmin($role)
{
    return $role === 'admin';
}

function isEmployee($role)
{
    return $role === 'employee';
}