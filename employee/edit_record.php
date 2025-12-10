<?php
require_once '../config/database.php';
checkEmployee();

$error = '';
$success = '';

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM attendance_records WHERE id = ? AND user_id = ? AND status = 'pending'");
$stmt->execute([$id, $_SESSION['user_id']]);
$record = $stmt->fetch();

if (!$record) {
    die('Запись не найдена или недоступна для редактирования');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $date = $_POST['date'] ?? '';
    $type = $_POST['type'] ?? '';
    $reason = $_POST['reason'] ?? '';
    
    if (empty($date) || empty($type)) {
        $error = 'Пожалуйста, заполните все обязательные поля';
    } else {
        $stmt = $pdo->prepare("UPDATE attendance_records SET date = ?, type = ?, reason = ? WHERE id = ? AND user_id = ?");
        
        if ($stmt->execute([$date, $type, $reason, $id, $_SESSION['user_id']])) {
            $success = 'Запись успешно обновлена!';
            $record['date'] = $date;
            $record['type'] = $type;
            $record['reason'] = $reason;
        } else {
            $error = 'Ошибка при обновлении записи';
        }
    }
}
?>

<?php include '../includes/header.php'; ?>

<h2>Редактировать запись</h2>

<?php if ($error): ?>
    <div class="error-message"><?php echo $error; ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="success-message"><?php echo $success; ?></div>
<?php endif; ?>

<form method="POST" action="">
    <div class="form-group">
        <label for="date">Дата *</label>
        <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($record['date']); ?>" required>
    </div>
    
    <div class="form-group">
        <label for="type">Тип отсутствия *</label>
        <select id="type" name="type" required>
            <option value="">Выберите тип</option>
            <option value="late" <?php echo ($record['type'] == 'late') ? 'selected' : ''; ?>>Опоздание</option>
            <option value="absence" <?php echo ($record['type'] == 'absence') ? 'selected' : ''; ?>>Прогул</option>
            <option value="vacation" <?php echo ($record['type'] == 'vacation') ? 'selected' : ''; ?>>Отпуск</option>
            <option value="own_account" <?php echo ($record['type'] == 'own_account') ? 'selected' : ''; ?>>За свой счет</option>
        </select>
    </div>
    
    <div class="form-group">
        <label for="reason">Причина</label>
        <textarea id="reason" name="reason" rows="4"><?php echo htmlspecialchars($record['reason']); ?></textarea>
    </div>
    
    <button type="submit" class="btn">Сохранить изменения</button>
    <a href="dashboard.php" class="btn" style="background: #6c757d; margin-left: 10px;">Отмена</a>
</form>

<?php include '../includes/footer.php'; ?>