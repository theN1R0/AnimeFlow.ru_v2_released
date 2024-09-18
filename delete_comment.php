<?php
session_start();
include 'db.php'; // Подключение к базе данных

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['user_id']) && isset($_POST['comment_id'])) {
        $comment_id = intval($_POST['comment_id']);
        
        // Проверяем, является ли пользователь автором комментария или администратором
        $stmt = $conn->prepare("SELECT user_id FROM comments WHERE id = ?");
        $stmt->execute([$comment_id]);
        $comment = $stmt->fetch();
        
        if ($comment && ($comment['user_id'] == $_SESSION['user_id'] || $_SESSION['role'] === 'admin')) {
            // Удаление комментария
            $stmt_delete = $conn->prepare("DELETE FROM comments WHERE id = ?");
            $stmt_delete->execute([$comment_id]);

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'У вас нет прав на удаление этого комментария.']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Неавторизованный запрос.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Неправильный метод запроса.']);
}
