<?php
session_start();
include 'db.php'; // Подключение к базе данных
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SESSION['role'] != 'admin') {
    die("У вас нет доступа к этой странице.");
}

$error_message = '';
$success_message = '';

// Маппинг для жанров, статусов и типов
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

// Функция для добавления аниме по Shikimori ID
// Функция для добавления аниме по Shikimori ID с фильтрацией важных данных
function addAnimeByShikimoriId($shikimori_id, $conn, $genreMapping, $typeMapping, $statusMapping) {
    // Получаем основные данные аниме
    $anime_details_url = "https://shikimori.one/api/animes/$shikimori_id";
    $anime_details_response = file_get_contents($anime_details_url);
    $anime_details = json_decode($anime_details_response, true);

    if (!$anime_details || !isset($anime_details['id'])) {
        return false;
    }

    // Теперь используем API франшизы для получения всех связанных аниме
    $franchise_url = "https://shikimori.one/api/animes/$shikimori_id/franchise";
    $franchise_response = file_get_contents($franchise_url);
    $franchise_details = json_decode($franchise_response, true);

    // Получаем ID всех связанных аниме из франшизы
    $related_anime_ids = [];
    if (isset($franchise_details['nodes']) && is_array($franchise_details['nodes'])) {
        foreach ($franchise_details['nodes'] as $node) {
            if (isset($node['id']) && $node['id'] != $shikimori_id) {
                $related_anime_ids[] = $node['id'];
            }
        }

        // Преобразуем массив ID в строку, разделенную запятыми
        $related_anime = implode(',', $related_anime_ids);
    } else {
        $related_anime = null;
    }

    // Получение остальных данных аниме (описание, жанры, студии и т.д.)
    // Проверка описания
    $description = isset($anime_details['description']) ? htmlspecialchars_decode($anime_details['description']) : 'Описания нет <span style="font-size: 18px;">&#128546;</span>';

    // Фильтрация описания
    $description = preg_replace('/\[[^\]]*\][\s,]*/', '', $description);
    $description = str_replace(['[', ']'], '', $description);

    // Получение жанров
    $genres = isset($anime_details['genres']) && is_array($anime_details['genres']) ? implode(', ', array_map(function ($genre) use ($genreMapping) {
        return $genreMapping[$genre['russian']] ?? $genre['russian'];
    }, $anime_details['genres'])) : 'Unknown Genre';

    // Получение типа
    $type = isset($anime_details['kind']) ? ($typeMapping[$anime_details['kind']] ?? $anime_details['kind']) : 'Unknown Type';

    // Получение статуса
    $status = isset($anime_details['status']) ? ($statusMapping[$anime_details['status']] ?? $anime_details['status']) : 'Unknown Status';

    $title = isset($anime_details['russian']) ? $anime_details['russian'] : (isset($anime_details['name']) ? $anime_details['name'] : 'Unknown Title');
    $alternative_title = isset($anime_details['name']) ? $anime_details['name'] : null;
    $studio = isset($anime_details['studios'][0]['name']) ? $anime_details['studios'][0]['name'] : 'Unknown Studio';
    $rating = isset($anime_details['score']) ? $anime_details['score'] : 0;
    $episodes = isset($anime_details['episodes']) ? $anime_details['episodes'] : 0;
    $duration = isset($anime_details['duration']) ? $anime_details['duration'] : 0;
    $cover_url = isset($anime_details['image']['original']) ? 'https://shikimori.one' . $anime_details['image']['original'] : null;
    $year = isset($anime_details['aired_on']) ? date('Y', strtotime($anime_details['aired_on'])) : 'Unknown Year';

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
    $stmt->execute([$title, $description, $alternative_title, $type, $genres, $studio, $status, $rating, $episodes, $duration, $cover_url, $shikimori_id, $year, $related_anime]);

    return true;
}



if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['find_related'])) {
    // Получаем все аниме из базы данных
    $stmt = $conn->query("SELECT shikimori_id, related_anime FROM anime WHERE shikimori_id IS NOT NULL");
    $anime_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Получаем лимит проверок из формы
    $check_limit = isset($_POST['check_limit']) ? intval($_POST['check_limit']) : 10;

    // Счётчик проверенных аниме
    $checked_count = 0;

    foreach ($anime_list as $anime) {
        // Проверяем, если у аниме уже есть связанные аниме
        if (!is_null($anime['related_anime']) && $anime['related_anime'] !== '') {
            continue; // Пропускаем, если related_anime уже заполнено
        }

        // Проверяем лимит
        if ($checked_count >= $check_limit) {
            break;
        }

        $shikimori_id = $anime['shikimori_id'];

        // Для каждого аниме выполняем запрос на Shikimori API франшизы
        $franchise_url = "https://shikimori.one/api/animes/$shikimori_id/franchise";
        $franchise_response = file_get_contents($franchise_url);
        $franchise_details = json_decode($franchise_response, true);

        // Получаем связанные аниме из франшизы
        $related_anime_ids = [];
        if (isset($franchise_details['nodes']) && is_array($franchise_details['nodes'])) {
            foreach ($franchise_details['nodes'] as $node) {
                if (isset($node['id']) && $node['id'] != $shikimori_id) {
                    $related_anime_ids[] = $node['id'];
                }
            }

            // Преобразуем массив ID в строку, разделенную запятыми
            $related_anime = implode(',', $related_anime_ids);
        }

        // Если связанных аниме нет, присваиваем "no_related"
        if (empty($related_anime)) {
            $related_anime = 'no_related';
        }

        // Обновляем базу данных, добавляя связанные аниме или метку no_related
        $stmt_update = $conn->prepare("UPDATE anime SET related_anime = ? WHERE shikimori_id = ?");
        $stmt_update->execute([$related_anime, $shikimori_id]);

        $checked_count++; // Увеличиваем счётчик
    }

    // Возвращаем результат в формате JSON для обновления на клиенте
    header('Content-Type: application/json');
    echo json_encode(['checked_count' => $checked_count]);
    exit;  // Останавливаем дальнейшее выполнение
}

// Обработка запросов
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['submit_ids'])) { // Добавление по ID
        $shikimori_ids_input = isset($_POST['shikimori_ids']) ? trim($_POST['shikimori_ids']) : '';
        if (!empty($shikimori_ids_input)) {
            $shikimori_ids = array_map('trim', explode(',', $shikimori_ids_input));

            foreach ($shikimori_ids as $shikimori_id) {
                // Проверка наличия аниме в базе данных по shikimori_id
                $stmt_check = $conn->prepare("SELECT COUNT(*) FROM anime WHERE shikimori_id = ?");
                $stmt_check->execute([$shikimori_id]);
                $exists = $stmt_check->fetchColumn();

                if ($exists == 0) {
                    // Если аниме не существует в базе данных, добавляем его
                    $added = addAnimeByShikimoriId($shikimori_id, $conn, $genreMapping, $typeMapping, $statusMapping);
                    if (!$added) {
                        $error_message .= "Не удалось добавить аниме с ID $shikimori_id.<br>";
                    }
                }
            }

            $success_message = 'Аниме по указанным ID успешно добавлены в базу данных!';
        }
    } else { // Стандартное добавление по страницам
        $page_number = isset($_POST['page_number']) ? intval($_POST['page_number']) : 1;
        $anime_count = isset($_POST['anime_count']) ? intval($_POST['anime_count']) : 10;
        $processed_anime_count = 0;
        $required_anime_count = $anime_count;

        while ($processed_anime_count < $anime_count) {
            $api_url = "https://shikimori.one/api/animes?page=$page_number&limit=$anime_count&order=popularity";
            $response = file_get_contents($api_url);
            $anime_data = json_decode($response, true);

            if (!empty($anime_data)) {
                foreach ($anime_data as $anime) {
                    $shikimori_id = isset($anime['id']) ? $anime['id'] : null;

                    // Проверка наличия аниме в базе данных по shikimori_id
                    $stmt_check = $conn->prepare("SELECT COUNT(*) FROM anime WHERE shikimori_id = ?");
                    $stmt_check->execute([$shikimori_id]);
                    $exists = $stmt_check->fetchColumn();

                    if ($exists > 0) {
                        continue;
                    }

                    $added = addAnimeByShikimoriId($shikimori_id, $conn, $genreMapping, $typeMapping, $statusMapping);
                    if ($added) {
                        $processed_anime_count++;
                    }

                    if ($processed_anime_count >= $required_anime_count) {
                        break;
                    }
                }

                $page_number++;

                // Если было добавлено недостаточно аниме, продолжаем до тех пор, пока не наберем нужное количество
                if ($processed_anime_count < $required_anime_count) {
                    $anime_count = $required_anime_count - $processed_anime_count;
                }
            } else {
                $error_message = 'Не удалось получить данные с Shikimori API.';
                break;
            }
        }

        $success_message = "Аниме успешно добавлены в базу данных! Добавлено: $processed_anime_count.";
    }

    // После успешного добавления данных - перенаправление на ту же страницу для предотвращения повторной отправки формы
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=1024"> <!-- фиксированная ширина как на ПК -->
    <title>Админ-панель</title>
    <link rel="apple-touch-icon" sizes="180x180" href="img/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="img/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="img/favicon-16x16.png">
    <link rel="manifest" href="img/site.webmanifest">
    <link rel="mask-icon" href="img/safari-pinned-tab.svg" color="#5bbad5">
    <link rel="shortcut icon" href="img/favicon.ico">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="msapplication-config" content="img/browserconfig.xml">
    <meta name="theme-color" content="#ffffff">
    <!-- Встроенные стили -->
    <style>
        body {
            font-family: 'Mulish', sans-serif;
            background-color: #0B0C2A;
            color: white;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #151531;
            padding: 30px;
            border-radius: 10px;
        }

        h1 {
            color: #FF6B6B;
            text-align: center;
            font-size: 2.5em;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            font-size: 1.1em;
            margin-bottom: 5px;
        }

        input, button {
            width: 100%;
            padding: 10px;
            font-size: 1em;
            border: none;
            border-radius: 5px;
            background-color: #1C1C3A;
            color: white;
        }

        button {
            background-color: #FF6B6B;
            cursor: pointer;
        }

        button:hover {
            background-color: #e65858;
        }

        .message {
            margin-top: 20px;
            text-align: center;
            font-size: 1.2em;
        }

        .error {
            color: #FF6B6B;
        }

        .success {
            color: #4CAF50;
        }

        /* Стили для списка аниме и поиска */
        .anime-list-container {
            margin-top: 30px;
        }

        .anime-list {
            max-height: 300px;
            overflow-y: auto;
            padding: 0;
            list-style: none;
        }

        .anime-list li {
            padding: 10px;
            border-bottom: 1px solid #1C1C3A;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .anime-list li button {
            width: auto;
            background-color: #FF6B6B;
        }

        .anime-list li button:hover {
            background-color: #e65858;
        }

        .search-bar {
            margin-bottom: 20px;
        }

        .search-bar input {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #1C1C3A;
            color: white;
            background-color: #1C1C3A;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Админ-панель: Добавить аниме</h1>

        <?php if ($error_message): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <!-- Форма добавления по Shikimori ID -->
        <form action="" method="POST">
            <div class="form-group">
                <label for="shikimori_ids">Введите Shikimori ID (через запятую):</label>
                <input type="text" name="shikimori_ids" id="shikimori_ids">
            </div>
            <button type="submit" name="submit_ids">Добавить по ID</button>
        </form>
        <hr>
        <!-- Форма для указания количества проверяемых аниме 
        <form id="relatedCheckForm">
            <div class="form-group">
                <label for="check_limit">Количество аниме для проверки:</label>
                <input type="number" name="check_limit" id="check_limit" value="10">
            </div>
            <button type="submit">Найти связанные аниме</button>
        </form>
        <hr>

        <!-- Область для отображения прогресса -->
        <div class="progress-container">
            <p>Проверено аниме: <span id="checked_count">0</span></p>
        </div>
        <!-- Форма стандартного добавления аниме -->
        <form action="" method="POST">
            <div class="form-group">
                <label for="page_number">Номер страницы:</label>
                <input type="number" name="page_number" value="1">
            </div>
            <div class="form-group">
                <label for="anime_count">Количество аниме:</label>
                <input type="number" name="anime_count" value="10">
            </div>
            <button type="submit">Загрузить аниме</button>
        </form>

        <hr>

        <!-- Отображение общего количества аниме -->
        <h3>Всего аниме в базе данных: 
            <?php
            $stmt_count = $conn->query("SELECT COUNT(*) FROM anime");
            echo $stmt_count->fetchColumn();
            ?>
        </h3>

        <!-- Поиск по списку аниме -->
        <div class="anime-list-container">
            <div class="search-bar">
                <input type="text" id="animeSearch" placeholder="Поиск по аниме...">
            </div>

            <!-- Список аниме -->
            <ul class="anime-list" id="animeList">
                <?php
                $stmt_anime = $conn->query("SELECT shikimori_id, title FROM anime ORDER BY title ASC");
                while ($row = $stmt_anime->fetch(PDO::FETCH_ASSOC)): ?>
                    <li data-title="<?php echo strtolower($row['title']); ?>">
                        <?php echo $row['title'] . ' (ID: ' . $row['shikimori_id'] . ')'; ?>
                        <form method="POST" action="delete_anime.php" style="display:inline;">
                            <input type="hidden" name="shikimori_id" value="<?php echo $row['shikimori_id']; ?>">
                            <button type="submit">Удалить</button>
                        </form>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>
    </div>

    <!-- Js Plugins -->
    <script src="js/jquery-3.3.1.min.js"></script>
    <script>
        // Функция для поиска по списку аниме
        document.getElementById('animeSearch').addEventListener('input', function() {
            const searchValue = this.value.toLowerCase().trim();
            const animeItems = document.querySelectorAll('#animeList li');

            animeItems.forEach(function(item) {
                const title = item.getAttribute('data-title').toLowerCase();
                if (title.includes(searchValue)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    </script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#relatedCheckForm').on('submit', function(e) {
            e.preventDefault();  // Останавливаем отправку формы обычным способом

            let checkLimit = $('#check_limit').val();  // Получаем значение лимита

            $.ajax({
                url: '',  // URL текущей страницы
                type: 'POST',
                data: {
                    find_related: true,
                    check_limit: checkLimit
                },
                success: function(response) {
                    // Обновляем область счётчика
                    let checkedCount = response.checked_count || 0;
                    $('#checked_count').text(checkedCount);

                    // Выводим сообщение о завершении
                    alert('Проверка завершена! Проверено: ' + checkedCount + ' аниме.');
                },
                error: function(xhr, status, error) {
                    console.log('Ошибка:', error);
                }
            });
        });
    });
</script>

</body>
</html>
