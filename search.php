<?php
include 'db.php'; // Подключение к базе данных

// Проверяем, был ли передан запрос
if (isset($_GET['query'])) {
    $searchQuery = "%" . strtolower($_GET['query']) . "%";

    // Запрос к базе данных на получение соответствующих аниме
    $stmt = $conn->prepare("SELECT shikimori_id, title FROM anime WHERE LOWER(title) LIKE ?");
    $stmt->execute([$searchQuery]);
    $animeList = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Возвращаем данные в формате JSON
    header('Content-Type: application/json');
    echo json_encode($animeList);
}
?>
