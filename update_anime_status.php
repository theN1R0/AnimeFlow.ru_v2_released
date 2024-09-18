<?php
session_start();
include 'db.php'; // Подключение к базе данных

// Проверка, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Пожалуйста, войдите в систему.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$anime_id = intval($_POST['anime_id']);
$status = $_POST['status'] ?? '';

$valid_statuses = ['watching', 'completed', 'dropped', 'planned'];

// Проверка валидности статуса
if (!in_array($status, $valid_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Неверный статус.']);
    exit;
}

// Проверяем, есть ли запись для этого аниме и пользователя
$stmt = $conn->prepare("SELECT COUNT(*) FROM user_anime_status WHERE user_id = ? AND anime_id = ?");
$stmt->execute([$user_id, $anime_id]);
$exists = $stmt->fetchColumn();

// Если запись уже существует, обновляем статус, если нет — создаем
if ($exists > 0) {
    $stmt = $conn->prepare("UPDATE user_anime_status SET status = ? WHERE user_id = ? AND anime_id = ?");
    $stmt->execute([$status, $user_id, $anime_id]);
} else {
    $stmt = $conn->prepare("INSERT INTO user_anime_status (user_id, anime_id, status) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $anime_id, $status]);
}

echo json_encode(['success' => true, 'message' => 'Статус успешно обновлен.']);
?>
