<?php
session_start();
include 'db.php'; // Подключаем базу данных

// Проверяем, что запрос пришел через AJAX и что он содержит нужные параметры
if (isset($_POST['randomPopular']) && $_POST['randomPopular'] == 'true') {
    // Выбираем случайные популярные аниме
    $stmt = $conn->prepare("SELECT * FROM anime ORDER BY RAND() LIMIT 3");
} else {
    // Выбираем последние добавленные аниме
    $stmt = $conn->prepare("SELECT * FROM anime ORDER BY added_at DESC LIMIT 3");
}

$stmt->execute();
$animeList = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Формируем HTML для вывода аниме
foreach ($animeList as $anime) {
    echo '<div class="hero__items">';
    echo '<div class="blurred-bg" style="background-image: url(\''. htmlspecialchars($anime['cover_url']) .'\');"></div>';
    echo '<img class="clear-image" src="'. htmlspecialchars($anime['cover_url']) .'" alt="'. htmlspecialchars($anime['title']) .'">';
    echo '<div class="hero__text">';
    echo '<div class="label">'. htmlspecialchars($anime['genres']) .'</div>';
    echo '<h2>'. htmlspecialchars($anime['title']) .'</h2>';
    echo '<p>'. mb_strimwidth(htmlspecialchars_decode($anime['description']), 0, 100, "...") .'</p>';
    echo '<a href="anime-details?id='. htmlspecialchars($anime['shikimori_id']) .'"><span>Смотреть сейчас</span><i class="fa fa-angle-right"></i></a>';
    echo '</div></div>';
}
?>
