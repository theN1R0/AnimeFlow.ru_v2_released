<?php
session_start();
include 'db.php';

if (isset($_POST['anime_id']) && isset($_SESSION['user_id'])) {
    $anime_id = $_POST['anime_id'];
    $user_id = $_SESSION['user_id'];

    // Логика добавления/удаления аниме из избранного
    $stmt_check = $conn->prepare("SELECT COUNT(*) FROM favorite_anime WHERE user_id = :user_id AND anime_id = :anime_id");
    $stmt_check->execute(['user_id' => $user_id, 'anime_id' => $anime_id]);
    $is_favorite = $stmt_check->fetchColumn();

    if ($is_favorite == 0) {
        // Если аниме не в избранном, добавляем его
        $stmt = $conn->prepare("INSERT INTO favorite_anime (user_id, anime_id) VALUES (:user_id, :anime_id)");
        $stmt->execute(['user_id' => $user_id, 'anime_id' => $anime_id]);
        echo "added";
    } else {
        // Если аниме уже в избранном, удаляем его
        $stmt = $conn->prepare("DELETE FROM favorite_anime WHERE user_id = :user_id AND anime_id = :anime_id");
        $stmt->execute(['user_id' => $user_id, 'anime_id' => $anime_id]);
        echo "removed";
    }
} else {
    echo "error";
}
?>
