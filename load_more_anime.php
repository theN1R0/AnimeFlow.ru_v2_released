<?php
include 'db.php'; // Подключение к базе данных

$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
$limit = 25; // Загружаем по 25 аниме за раз

// Подсчитываем общее количество аниме
$totalAnimeStmt = $conn->prepare("SELECT COUNT(*) as total FROM anime");
$totalAnimeStmt->execute();
$totalAnimes = $totalAnimeStmt->fetchColumn();

// Заголовок с общим количеством аниме, чтобы клиент знал, сколько всего записей
header("X-Total-Count: $totalAnimes");

// Используем подготовленный запрос с параметрами
$stmt = $conn->prepare("SELECT * FROM anime ORDER BY rating DESC LIMIT :limit OFFSET :offset");

// Привязываем параметры к запросу
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();
$animes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Выводим аниме
foreach ($animes as $anime) {
    echo '<div class="col-lg-2_4 col-md-4 col-sm-6" style="flex: 0 0 20%; max-width: 20%;">';
    echo '<div class="product__item">';
    echo '<a href="anime-details.php?id=' . htmlspecialchars($anime['shikimori_id']) . '">'; 
    echo '<div class="product__item__pic set-bg" data-setbg="' . htmlspecialchars($anime['cover_url']) . '" data-rating="' . htmlspecialchars($anime['rating']) . '">';
    
    // Плашка с количеством серий
    if ($anime['episodes'] == 0) {
        echo '<div class="ep" style="background-color: #800080;">Выходит</div>';
    } else {
        echo '<div class="ep">' . htmlspecialchars($anime['episodes']) . ' серий</div>';
    }

    // Плашка с рейтингом
    if (!empty($anime['rating'])) {
        echo '<div class="rating-badge" style="background-color: #FFD700; color: #000;">';
        echo '<i class="fa fa-star"></i> ' . htmlspecialchars($anime['rating']);
        echo '</div>';
    }

    // Всплывающая подсказка
    echo '<div class="anime-tooltip">';
    echo '<h4>' . htmlspecialchars($anime['title']) . '</h4>';
    echo '<p>' . htmlspecialchars(mb_strimwidth($anime['description'], 0, 250, '...')) . '</p>';
    echo '<ul>';
    echo '<li><strong>Тип:</strong> ' . htmlspecialchars($anime['type']) . '</li>';
    echo '<li><strong>Год выпуска:</strong> ' . htmlspecialchars($anime['year']) . '</li>';
    echo '<li><strong>Студия:</strong> ' . htmlspecialchars($anime['studio']) . '</li>';
    echo '<li><strong>Длина серии:</strong> ' . htmlspecialchars($anime['duration']) . ' мин.</li>';
    echo '</ul>';
    echo '</div>';

    echo '</div></a>';
    echo '<div class="product__item__text">';
    echo '<ul>';
    $genres = explode(',', $anime['genres']);
    foreach ($genres as $genre) {
        echo '<li>' . htmlspecialchars($genre) . '</li>';
    }
    echo '</ul>';
    echo '<h5><a href="anime-details.php?id=' . htmlspecialchars($anime['shikimori_id']) . '">' . htmlspecialchars($anime['title']) . '</a></h5>';
    echo '</div></div></div>';
}
?>
