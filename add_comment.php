<?php
session_start();
include 'db.php'; // Подключение к базе данных
date_default_timezone_set('Europe/Moscow');


// Проверка на наличие комментария, ID аниме и ID пользователя
if (isset($_POST['comment']) && isset($_POST['anime_id']) && isset($_SESSION['user_id'])) {
    $comment = trim($_POST['comment']);
    $anime_id = intval($_POST['anime_id']);
    $user_id = $_SESSION['user_id'];

    // Проверяем, что комментарий не пустой
    if (!empty($comment)) {
        // SQL запрос на добавление комментария в базу данных
        $stmt = $conn->prepare("INSERT INTO comments (user_id, anime_id, comment, created_at) VALUES (?, ?, ?, NOW())");
        if ($stmt->execute([$user_id, $anime_id, $comment])) {
            // Успешный ответ с данными для отображения комментария
            echo json_encode([
                'success' => true,
                'username' => $_SESSION['username'], // Отправляем имя пользователя из сессии
                'comment' => $comment,
                'created_at' => date('d M Y H:i') // Форматирование даты
            ]);
        } else {
            // Ошибка при добавлении в базу данных
            echo json_encode(['success' => false, 'error' => 'Ошибка при добавлении комментария в базу данных.']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Комментарий не может быть пустым.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Некорректные данные.']);
}
?>
