<?php
require_once '../config/database.php';
checkAdmin();

$filter_date_from = $_GET['date_from'] ?? '';
$filter_date_to = $_GET['date_to'] ?? '';
$filter_type = $_GET['type'] ?? '';
$filter_status = $_GET['status'] ?? '';
$filter_name = $_GET['name'] ?? '';

$query = "SELECT ar.*, u.name as user_name, u.email 
          FROM attendance_records ar 
          JOIN users u ON ar.user_id = u.id 
          WHERE 1=1";

$params = [];

if (!empty($filter_date_from)) {
    $query .= " AND ar.date >= ?";
    $params[] = $filter_date_from;
}

if (!empty($filter_date_to)) {
    $query .= " AND ar.date <= ?";
    $params[] = $filter_date_to;
}

if (!empty($filter_type) && $filter_type != 'all') {
    $query .= " AND ar.type = ?";
    $params[] = $filter_type;
}

if (!empty($filter_status) && $filter_status != 'all') {
    $query .= " AND ar.status = ?";
    $params[] = $filter_status;
}

if (!empty($filter_name)) {
    $query .= " AND (u.name LIKE ? OR u.email LIKE ?)";
    $params[] = "%$filter_name%";
    $params[] = "%$filter_name%";
}

$query .= " ORDER BY ar.date DESC, ar.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$records = $stmt->fetchAll();

$stats_query = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
    SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
    FROM attendance_records";
$stats = $pdo->query($stats_query)->fetch();
?>

<?php include '../includes/header.php'; ?>

<h2>Панель администратора</h2>

<div class="stats-cards">
    <div class="stat-card" style="background: #f8f9fa;">
        <h3>Всего записей</h3>
        <p class="stat-number"><?php echo $stats['total']; ?></p>
    </div>
    <div class="stat-card" style="background: #fff3cd;">
        <h3>Ожидают</h3>
        <p class="stat-number"><?php echo $stats['pending']; ?></p>
    </div>
    <div class="stat-card" style="background: #d4edda;">
        <h3>Подтверждено</h3>
        <p class="stat-number"><?php echo $stats['approved']; ?></p>
    </div>
    <div class="stat-card" style="background: #f8d7da;">
        <h3>Отклонено</h3>
        <p class="stat-number"><?php echo $stats['rejected']; ?></p>
    </div>
</div>

<div class="filters">
    <h3>Фильтры</h3>
    <form method="GET" action="">
        <div class="filter-row">
            <div class="form-group">
                <label>Дата с:</label>
                <input type="date" name="date_from" value="<?php echo htmlspecialchars($filter_date_from); ?>">
            </div>
            
            <div class="form-group">
                <label>Дата по:</label>
                <input type="date" name="date_to" value="<?php echo htmlspecialchars($filter_date_to); ?>">
            </div>
            
            <div class="form-group">
                <label>Тип:</label>
                <select name="type">
                    <option value="all">Все типы</option>
                    <option value="late" <?php echo ($filter_type == 'late') ? 'selected' : ''; ?>>Опоздание</option>
                    <option value="absence" <?php echo ($filter_type == 'absence') ? 'selected' : ''; ?>>Прогул</option>
                    <option value="vacation" <?php echo ($filter_type == 'vacation') ? 'selected' : ''; ?>>Отпуск</option>
                    <option value="own_account" <?php echo ($filter_type == 'own_account') ? 'selected' : ''; ?>>За свой счет</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Статус:</label>
                <select name="status">
                    <option value="all">Все статусы</option>
                    <option value="pending" <?php echo ($filter_status == 'pending') ? 'selected' : ''; ?>>Ожидает</option>
                    <option value="approved" <?php echo ($filter_status == 'approved') ? 'selected' : ''; ?>>Подтверждено</option>
                    <option value="rejected" <?php echo ($filter_status == 'rejected') ? 'selected' : ''; ?>>Отклонено</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Поиск по имени/email:</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($filter_name); ?>" placeholder="Имя или email">
            </div>
        </div>
        
        <button type="submit" class="btn">Применить фильтры</button>
        <a href="dashboard.php" class="btn" style="background: #6c757d;">Сбросить</a>
    </form>
</div>

<h3>Все записи</h3>
<?php if (empty($records)): ?>
    <p>Нет записей, соответствующих фильтрам.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Дата</th>
                <th>Сотрудник</th>
                <th>Email</th>
                <th>Тип</th>
                <th>Причина</th>
                <th>Статус</th>
                <th>Создано</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($records as $record): ?>
            <tr>
                <td><?php echo htmlspecialchars($record['date']); ?></td>
                <td><?php echo htmlspecialchars($record['user_name']); ?></td>
                <td><?php echo htmlspecialchars($record['email']); ?></td>
                <td>
                    <?php 
                    $types = [
                        'late' => 'Опоздание',
                        'absence' => 'Прогул',
                        'vacation' => 'Отпуск',
                        'own_account' => 'За свой счет'
                    ];
                    echo $types[$record['type']] ?? $record['type'];
                    ?>
                </td>
                <td><?php echo htmlspecialchars(substr($record['reason'], 0, 50)) . (strlen($record['reason']) > 50 ? '...' : ''); ?></td>
                <td class="status-<?php echo $record['status']; ?>">
                    <?php 
                    $statuses = [
                        'pending' => 'Ожидает',
                        'approved' => 'Подтверждено',
                        'rejected' => 'Отклонено'
                    ];
                    echo $statuses[$record['status']] ?? $record['status'];
                    ?>
                </td>
                <td><?php echo date('d.m.Y H:i', strtotime($record['created_at'])); ?></td>
                <td>
                    <form method="POST" action="manage_record.php" style="display: inline;">
                        <input type="hidden" name="id" value="<?php echo $record['id']; ?>">
                        <?php if ($record['status'] == 'pending'): ?>
                            <button type="submit" name="action" value="approve" class="btn-small" style="background: #28a745;">✓</button>
                            <button type="submit" name="action" value="reject" class="btn-small" style="background: #dc3545;">✗</button>
                        <?php else: ?>
                            <span class="no-action">—</span>
                        <?php endif; ?>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>