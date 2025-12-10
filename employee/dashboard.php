<?php
require_once '../config/database.php';
checkEmployee();

$stmt = $pdo->prepare("SELECT * FROM attendance_records WHERE user_id = ? ORDER BY date DESC");
$stmt->execute([$_SESSION['user_id']]);
$records = $stmt->fetchAll();
?>

<?php include '../includes/header.php'; ?>

<h2>Мои записи об отсутствиях</h2>

<a href="add_record.php" class="btn">+ Добавить запись</a>

<table>
    <thead>
        <tr>
            <th>Дата</th>
            <th>Тип</th>
            <th>Причина</th>
            <th>Статус</th>
            <th>Действия</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($records)): ?>
            <tr>
                <td colspan="5">Нет записей</td>
            </tr>
        <?php else: ?>
            <?php foreach ($records as $record): ?>
            <tr>
                <td><?php echo htmlspecialchars($record['date']); ?></td>
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
                <td><?php echo htmlspecialchars($record['reason']); ?></td>
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
                <td>
                    <?php if ($record['status'] == 'pending'): ?>
                        <a href="edit_record.php?id=<?php echo $record['id']; ?>">Редактировать</a>
                    <?php else: ?>
                        <span class="no-action">—</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>