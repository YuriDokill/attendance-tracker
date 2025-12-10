<?php
require_once '../config/database.php';
checkEmployee();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $date = $_POST['date'] ?? '';
    $type = $_POST['type'] ?? '';
    $reason = $_POST['reason'] ?? '';
    
    if (empty($date) || empty($type)) {
        $error = 'Пожалуйста, заполните все обязательные поля';
    } else {
        $stmt = $pdo->prepare("INSERT INTO attendance_records (user_id, date, type, reason, status) VALUES (?, ?, ?, ?, 'pending')");
        
        if ($stmt->execute([$_SESSION['user_id'], $date, $type, $reason])) {
            $success = 'Запись успешно добавлена!';
            $_POST = [];
        } else {
            $error = 'Ошибка при добавлении записи';
        }
    }
}
?>

<?php include '../includes/header.php'; ?>

<h2>Добавить запись об отсутствии</h2>

<?php if ($error): ?>
    <div class="error-message"><?php echo $error; ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="success-message"><?php echo $success; ?></div>
<?php endif; ?>

<form method="POST" action="">
    <div class="form-group">
        <label for="date">Дата *</label>
        <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($_POST['date'] ?? date('Y-m-d')); ?>" required>
    </div>
    
    <div class="form-group">
        <label for="type">Тип отсутствия *</label>
        <select id="type" name="type" required>
            <option value="">Выберите тип</option>
            <option value="late" <?php echo (($_POST['type'] ?? '') == 'late') ? 'selected' : ''; ?>>Опоздание</option>
            <option value="absence" <?php echo (($_POST['type'] ?? '') == 'absence') ? 'selected' : ''; ?>>Прогул</option>
            <option value="vacation" <?php echo (($_POST['type'] ?? '') == 'vacation') ? 'selected' : ''; ?>>Отпуск</option>
            <option value="own_account" <?php echo (($_POST['type'] ?? '') == 'own_account') ? 'selected' : ''; ?>>За свой счет</option>
        </select>
    </div>
    
    <div class="form-group">
        <label for="reason">Причина</label>
        <textarea id="reason" name="reason" rows="4"><?php echo htmlspecialchars($_POST['reason'] ?? ''); ?></textarea>
    </div>
    
    <button type="submit" class="btn">Добавить запись</button>
    <a href="dashboard.php" class="btn" style="background: #6c757d; margin-left: 10px;">Отмена</a>
</form>

<?php include '../includes/footer.php'; ?>