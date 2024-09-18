<?php
include 'db.php'; // Подключение к базе данных

// Маппинг для жанров, статусов и типов (можно использовать те же переменные, что и в admin.php)
$genreMapping = [
    "Action" => "Экшен",
    "Adventure" => "Приключения",
    "Cars" => "Машины",
    "Comedy" => "Комедия",
    "Dementia" => "Безумие",
    "Demons" => "Демоны",
    "Drama" => "Драма",
    "Ecchi" => "Этти",
    "Fantasy" => "Фэнтези",
    "Game" => "Игры",
    "Harem" => "Гарем",
    "Historical" => "Исторический",
    "Horror" => "Ужасы",
    "Josei" => "Дзёсей",
    "Kids" => "Детский",
    "Magic" => "Магия",
    "Martial Arts" => "Боевые искусства",
    "Mecha" => "Меха",
    "Military" => "Военный",
    "Music" => "Музыка",
    "Mystery" => "Мистика",
    "Parody" => "Пародия",
    "Police" => "Полиция",
    "Psychological" => "Психологический",
    "Romance" => "Романтика",
    "Samurai" => "Самураи",
    "School" => "Школа",
    "Sci-Fi" => "Научная фантастика",
    "Seinen" => "Сэйнэн",
    "Shoujo" => "Сёдзё",
    "Shoujo Ai" => "Сёдзё Ай",
    "Shounen" => "Сёнэн",
    "Shounen Ai" => "Сёнэн Ай",
    "Slice of Life" => "Повседневность",
    "Space" => "Космос",
    "Sports" => "Спорт",
    "Super Power" => "Суперсила",
    "Supernatural" => "Сверхъестественное",
    "Thriller" => "Триллер",
    "Vampire" => "Вампиры",
    "Yaoi" => "Яой",
    "Yuri" => "Юри",
];

// Статусы
$statusMapping = [
    "anons" => "Еще не вышло",
    "ongoing" => "Сейчас выходит",
    "released" => "Завершено",
];

// Типы
$typeMapping = [
    "tv" => "ТВ",
    "movie" => "Фильм",
    "ova" => "OVA",
    "ona" => "ONA",
    "special" => "Спешл",
    "music" => "Музыкальное видео",
];

// Функция для добавления аниме по Shikimori ID с фильтрацией важнейших данных
function addAnimeByShikimoriId($shikimori_id, $conn, $genreMapping, $typeMapping, $statusMapping) {
    $anime_details_url = "https://shikimori.one/api/animes/$shikimori_id";
    $anime_details_response = file_get_contents($anime_details_url);
    $anime_details = json_decode($anime_details_response, true);

    if (!$anime_details || !isset($anime_details['id'])) {
        return false;
    }

    // Получаем информацию о франшизе
    $franchise_url = "https://shikimori.one/api/animes/$shikimori_id/franchise";
    $franchise_response = file_get_contents($franchise_url);
    $franchise_details = json_decode($franchise_response, true);

    // Собираем ID связанных аниме
    $related_anime_ids = [];
    if (isset($franchise_details['nodes']) && is_array($franchise_details['nodes'])) {
        foreach ($franchise_details['nodes'] as $node) {
            if (isset($node['id']) && $node['id'] != $shikimori_id) {
                $related_anime_ids[] = $node['id'];
            }
        }
        $related_anime = implode(',', $related_anime_ids);
    } else {
        $related_anime = null;
    }

    // Фильтрация описания
    $description = isset($anime_details['description']) ? htmlspecialchars_decode($anime_details['description']) : 'Описания нет <span style="font-size: 18px;">&#128546;</span>';
    $description = preg_replace('/\[[^\]]*\][\s,]*/', '', $description); // Убираем теги в квадратных скобках
    $description = str_replace(['[', ']'], '', $description); // Убираем остаточные квадратные скобки

    // Собираем данные аниме
    $genres = implode(', ', array_map(function ($genre) use ($genreMapping) {
        return $genreMapping[$genre['russian']] ?? $genre['russian'];
    }, $anime_details['genres']));

    $type = $typeMapping[$anime_details['kind']] ?? $anime_details['kind'];
    $status = $statusMapping[$anime_details['status']] ?? $anime_details['status'];
    $title = $anime_details['russian'] ?? $anime_details['name'];
    $studio = $anime_details['studios'][0]['name'] ?? 'Unknown Studio';
    $rating = $anime_details['score'] ?? 0;
    $episodes = $anime_details['episodes'] ?? 0;
    $duration = $anime_details['duration'] ?? 0;
    $cover_url = 'https://shikimori.one' . $anime_details['image']['original'];
    $year = date('Y', strtotime($anime_details['aired_on']));

    // Пропуск аниме, если не хватает важных данных
    if (
        empty($title) || 
        $title === 'Unknown Title' || 
        empty($genres) || 
        $genres === 'Unknown Genre' || 
        empty($type) || 
        $type === 'Unknown Type' || 
        empty($studio) || 
        $studio === 'Unknown Studio' || 
        empty($status) || 
        $status === 'Unknown Status' || 
        empty($cover_url) || 
        empty($year) || 
        $year === 'Unknown Year'
    ) {
        return false; // Пропуск, если какие-то важные поля отсутствуют
    }

    // Пропуск аниме с рейтингом 0
    if ($rating == 0) {
        return false;
    }

    // Добавляем аниме в базу данных
    $stmt = $conn->prepare("INSERT INTO anime (title, description, alternative_title, type, genres, studio, status, rating, episodes, duration, cover_url, shikimori_id, year, related_anime) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $description, $anime_details['name'], $type, $genres, $studio, $status, $rating, $episodes, $duration, $cover_url, $shikimori_id, $year, $related_anime]);

    return true;
}

// Функция для проверки и добавления связанных аниме
function checkAndAddRelatedAnime($anime_id, $conn, $genreMapping, $typeMapping, $statusMapping) {
    $stmt = $conn->prepare("SELECT related_anime FROM anime WHERE shikimori_id = ?");
    $stmt->execute([$anime_id]);
    $related_anime_data = $stmt->fetchColumn();

    if (!empty($related_anime_data)) {
        $related_anime_ids = explode(',', $related_anime_data);

        foreach ($related_anime_ids as $related_id) {
            // Проверка существования аниме в базе
            $stmt_check = $conn->prepare("SELECT COUNT(*) FROM anime WHERE shikimori_id = ?");
            $stmt_check->execute([$related_id]);
            $exists = $stmt_check->fetchColumn();

            // Если аниме не найдено в базе — добавляем его через API
            if ($exists == 0) {
                addAnimeByShikimoriId($related_id, $conn, $genreMapping, $typeMapping, $statusMapping);
            }
        }
    }
}