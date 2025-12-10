<?php
require_once '../config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? 'employee';
    
    $errors = [];
    
    if (empty($name)) {
        $errors[] = 'Имя обязательно для заполнения';
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Введите корректный email';
    }
    
    if (empty($password)) {
        $errors[] = 'Пароль обязателен для заполнения';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Пароль должен содержать минимум 6 символов';
    }
    
    if ($password !== $confirm_password) {
        $errors[] = 'Пароли не совпадают';
    }
    
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $errors[] = 'Пользователь с таким email уже существует';
        }
    }
    
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        
        if ($stmt->execute([$name, $email, $hashedPassword, $role])) {
            $success = 'Регистрация успешна! Теперь вы можете войти.';
            $_POST = [];
        } else {
            $error = 'Ошибка при регистрации';
        }
    } else {
        $error = implode('<br>', $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация - Attendance Tracker</title>
    <link rel="stylesheet" href="/attendance-tracker/css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-form">
            <h2>Регистрация</h2>
            
            <?php if ($error): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label>Имя *</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Пароль * (минимум 6 символов)</label>
                    <input type="password" name="password" required minlength="6">
                </div>
                
                <div class="form-group">
                    <label>Подтверждение пароля *</label>
                    <input type="password" name="confirm_password" required minlength="6">
                </div>
                
                <div class="form-group">
                    <label>Роль:</label>
                    <select name="role">
                        <option value="employee" <?php echo (($_POST['role'] ?? '') == 'employee') ? 'selected' : ''; ?>>Сотрудник</option>
                        <option value="admin" <?php echo (($_POST['role'] ?? '') == 'admin') ? 'selected' : ''; ?>>Администратор</option>
                    </select>
                </div>
                
                <button type="submit" class="btn">Зарегистрироваться</button>
            </form>
            
            <p>Уже есть аккаунт? <a href="login.php">Войти</a></p>
        </div>
    </div>
</body>
</html>