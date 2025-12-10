<?php
require_once '../config/database.php';
checkAdmin();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? 0;
    $action = $_POST['action'] ?? '';
    
    if ($id && in_array($action, ['approve', 'reject'])) {
        $status = $action == 'approve' ? 'approved' : 'rejected';
        
        $stmt = $pdo->prepare("UPDATE attendance_records SET status = ? WHERE id = ?");
        if ($stmt->execute([$status, $id])) {
            $_SESSION['success'] = 'Статус записи успешно обновлен!';
        } else {
            $_SESSION['error'] = 'Ошибка при обновлении статуса';
        }
    }
    
    header('Location: dashboard.php');
    exit();
}

header('Location: dashboard.php');
exit();
?>