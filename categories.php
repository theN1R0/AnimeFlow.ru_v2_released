<?php
session_start();
include 'db.php';
// Если пользователь авторизован, получаем его имя
$username = '';
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    if ($user) {
        $username = $user['username'];
    }
}
// Сортировка
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'title';
$order = isset($_GET['order']) ? $_GET['order'] : 'asc';
$order_by = "$sort $order";
$sort_options = [
    'title' => 'title',
    'added_at' => 'added_at',
    'rating' => 'rating',
    'year' => 'year'
];
$sort_order_toggle = ($order === 'asc') ? 'desc' : 'asc';

$year = isset($_GET['year']) ? $_GET['year'] : null;
$genre = isset($_GET['genre']) ? $_GET['genre'] : null;
$type = isset($_GET['type']) ? $_GET['type'] : null;
$status = isset($_GET['status']) ? $_GET['status'] : null;

$query = "SELECT * FROM anime WHERE 1=1";
if ($year) {
    $query .= " AND year = :year";
}
if ($genre) {
    $query .= " AND genres LIKE :genre";
}
if ($type) {
    $query .= " AND type = :type";
}
if ($status) {
    $query .= " AND status = :status";
}
$query .= " ORDER BY $order_by LIMIT :limit OFFSET :offset";

$limit = 18;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$stmt = $conn->prepare($query);
if ($year) {
    $stmt->bindParam(':year', $year, PDO::PARAM_INT);
}
if ($genre) {
    $stmt->bindValue(':genre', '%' . $genre . '%', PDO::PARAM_STR);
}
if ($type) {
    $stmt->bindParam(':type', $type, PDO::PARAM_STR);
}
if ($status) {
    $stmt->bindParam(':status', $status, PDO::PARAM_STR);
}
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$anime_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_query = "SELECT COUNT(*) FROM anime WHERE 1=1";
if ($year) {
    $total_query .= " AND year = :year";
}
if ($genre) {
    $total_query .= " AND genres LIKE :genre";
}
if ($type) {
    $total_query .= " AND type = :type";
}
if ($status) {
    $total_query .= " AND status = :status";
}
$total_stmt = $conn->prepare($total_query);
if ($year) {
    $total_stmt->bindParam(':year', $year, PDO::PARAM_INT);
}
if ($genre) {
    $total_stmt->bindValue(':genre', '%' . $genre . '%', PDO::PARAM_STR);
}
if ($type) {
    $total_stmt->bindParam(':type', $type, PDO::PARAM_STR);
}
if ($status) {
    $total_stmt->bindParam(':status', $status, PDO::PARAM_STR);
}
$total_stmt->execute();
$total_anime = $total_stmt->fetchColumn();
$total_pages = ceil($total_anime / $limit);

$base_url = "?sort=$sort&order=$order";

$stmt = $conn->prepare("SELECT * FROM anime WHERE shikimori_id IS NOT NULL ORDER BY RAND() LIMIT 1");
$stmt->execute();
$random_anime = $stmt->fetch(PDO::FETCH_ASSOC);

$genre_stmt = $conn->prepare("SELECT DISTINCT genres FROM anime");
$genre_stmt->execute();
$genres = $genre_stmt->fetchAll(PDO::FETCH_ASSOC);

$genre_list = [];
foreach ($genres as $genre_row) {
    $genre_split = explode(',', $genre_row['genres']);
    foreach ($genre_split as $g) {
        $trimmed_genre = trim($g); // Убираем пробелы
        if (!in_array($trimmed_genre, $genre_list)) {
            $genre_list[] = $trimmed_genre;
        }
    }
}

sort($genre_list);
?>

<!DOCTYPE html>
<html lang="zxx">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Ваши любимые аниме. Смотрите онлайн бесплатно с полным списком жанров, сезонов и новинок только на AnimeFlow. Откройте мир японской анимации с нами!">
    <meta name="keywords" content="аниме онлайн, смотреть аниме бесплатно, смотреть аниме онлайн бесплатно в хорошем качестве, онлайн бесплатно в хорошем качестве, аниме в хорошем качестве, лучшие аниме, аниме 2024, новые аниме, аниме сериалы, аниме фильмы, аниме без рекламы, аниме жанры, аниме топ, популярные аниме, смотреть аниме HD, аниме онлайн бесплатно, лучшие аниме сериалы, аниме новинки, аниме онлайн 2024, лучшие аниме онлайн, аниме HD, бесплатное аниме, аниме потоковое видео, топ аниме, манга и аниме, аниме без подписки">
    <meta name="viewport" content="width=1024">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Список аниме | AnimeFlow</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="css/elegant-icons.css" type="text/css">
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slicknav/1.0.10/slicknav.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slicknav/1.0.10/jquery.slicknav.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/14.6.3/nouislider.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/14.6.3/nouislider.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="apple-touch-icon" sizes="180x180" href="img/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="img/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="img/favicon-16x16.png">
    <link rel="manifest" href="img/site.webmanifest">
    <link rel="mask-icon" href="img/safari-pinned-tab.svg" color="#5bbad5">
    <link rel="shortcut icon" href="img/favicon.ico">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="msapplication-config" content="img/browserconfig.xml">
    <meta name="theme-color" content="#ffffff">
        <!-- Yandex.RTB -->
    <script>window.yaContextCb=window.yaContextCb||[]</script>
    <script src="https://yandex.ru/ads/system/context.js" async></script>
</head>
<body>
<?php include 'header.php'; ?>
    <section class="product-page spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="product__page__content">
                        <div class="product__page__title">
                            <div class="row">
                                <div class="col-lg-8 col-md-8 col-sm-6">
                                    <div class="section-title">
                                        <h4>Список аниме</h4>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-6">
                                    <div class="product__page__filter">
                                        <p>Сортировать по:</p>
                                        <select id="sortSelect">
                                            <option value="" disabled selected>Выбрать</option>
                                            <option value="?sort=title&order=asc">Названию ▲</option>
                                            <option value="?sort=title&order=desc">Названию ▼</option>
                                            <option value="?sort=added_at&order=asc">Дате добавления ▲</option>
                                            <option value="?sort=added_at&order=desc">Дате добавления ▼</option>
                                            <option value="?sort=rating&order=asc">Рейтингу ▲</option>
                                            <option value="?sort=rating&order=desc">Рейтингу ▼</option>
                                            <option value="?sort=year&order=asc">Году выхода ▲</option>
                                            <option value="?sort=year&order=desc">Году выхода ▼</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Добавляем идентификатор для контейнера с аниме -->
                        <div class="row" id="anime-list">
                            <?php foreach ($anime_list as $anime): ?>
                            <div class="col-lg-4 col-md-6 col-sm-6">
                                <div class="product__item">
                                    <a href="anime-details?id=<?= htmlspecialchars($anime['shikimori_id']); ?>">
                                        <div class="product__item__pic set-bg" data-setbg="<?= htmlspecialchars($anime['cover_url']); ?>" data-rating="<?= htmlspecialchars($anime['rating']) ?>">
                                            <?php if ($anime['episodes'] == 0): ?>
                                                <div class="ep" style="background-color: #800080;">Выходит</div>
                                            <?php elseif ($anime['episodes'] == 1): ?>
                                                <div class="ep"><?= htmlspecialchars($anime['episodes']); ?> серия</div>
                                            <?php else: ?>
                                                <div class="ep"><?= htmlspecialchars($anime['episodes']); ?> серий</div>
                                            <?php endif; ?>  
                                            <?php if (!empty($anime['rating'])): ?>
                                                <div class="rating-badge" style="background-color: #FFD700; color: #000;">
                                                    <i class="fa fa-star"></i> <?= htmlspecialchars($anime['rating']); ?>
                                                </div>
                                            <?php endif; ?>
                                            <div class="anime-tooltip">
                                                <h4><?= htmlspecialchars($anime['title']); ?></h4>
                                                <p><?= htmlspecialchars_decode(mb_strimwidth($anime['description'], 0, 250, '...')); ?></p>
                                                <ul>
                                                    <li><strong>Тип:</strong> <?= htmlspecialchars($anime['type']); ?></li>
                                                    <li><strong>Год выпуска:</strong> <?= htmlspecialchars($anime['year']); ?></li>
                                                    <li><strong>Студия:</strong> <?= htmlspecialchars($anime['studio']); ?></li>
                                                    <li><strong>Длина серии:</strong> <?= htmlspecialchars($anime['duration']); ?> мин.</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </a>
                                    <div class="product__item__text">
                                        <ul>
                                            <?php 
                                            $genres = explode(',', $anime['genres']); 
                                            foreach ($genres as $genre): 
                                            ?>
                                                <li><?= htmlspecialchars($genre); ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                        <h5><a href="anime-details?id=<?= htmlspecialchars($anime['shikimori_id']); ?>"><?= htmlspecialchars($anime['title']); ?></a></h5>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <div class="col-lg-4 col-md-6 col-sm-8">
                <div class="filter-box">
                    <div class="product__sidebar">
                        <div class="product__sidebar__view">
                            <div class="section-title">
                                <h5>Фильтр</h5>
                            </div>
                            <div class="filter-section">
                                <label for="yearRangeSlider">Год</label>
                                <div id="yearRangeSlider"></div>
                            </div>
                            <div class="filter-section">
                                <label for="genres">Жанры</label>
                                <select id="genres" class="form-select">
                                    <option value="">Выберите жанр</option>
                                    <?php foreach ($genre_list as $genre): ?>
                                        <option value="<?= htmlspecialchars(strtolower($genre)); ?>"><?= htmlspecialchars($genre); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="filter-section">
                                <label for="animeType">Тип</label>
                                <select id="animeType" class="form-select">
                                    <option value="">Выберите тип</option>
                                    <option value="tv">ТВ</option>
                                    <option value="movie">Фильм</option>
                                    <option value="special">Спешл</option>
                                    <option value="ova">OVA</option>
                                    <option value="ona">ONA</option>
                                </select>
                            </div>
                            <div class="filter-section">
                                <label for="status">Статус</label>
                                <select id="status" class="form-select">
                                    <option value="">Выберите статус</option>
                                    <option value="completed">Завершено</option>
                                    <option value="ongoing">В процессе</option>
                                    <option value="anons">Сейчас выходит</option>
                                </select>
                            </div>
                            <div class="filter-section">
                                <button type="button" class="btn btn-primary" id="applyFilters">Применить фильтры</button>
                            </div>
                        </div>
                    </div>
                            <div id="ad-container-filter" style="text-align: center; margin-top: 20px;">
                                <!-- Yandex.RTB R-A-11910896-3 -->
                                <div id="yandex_rtb_R-A-11910896-3"></div>
                                <script>
                                window.yaContextCb.push(() => {
                                    Ya.Context.AdvManager.render({
                                        "blockId": "R-A-11910896-3",
                                        "renderTo": "yandex_rtb_R-A-11910896-3"
                                    });
                                });
                                </script>
                            </div>
                </div>
            </div>
        </div>
    </section>
<?php include 'footer.php'; ?>
    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
    <script>
        document.getElementById('sortSelect').addEventListener('change', function () {
            let baseUrl = window.location.origin + window.location.pathname;
            let selectedSort = this.value;
            let urlParams = new URLSearchParams(window.location.search);
            
            // Если уже есть параметры фильтрации, то оставляем их
            urlParams.set('sort', selectedSort.split('&')[0].split('=')[1]); 
            urlParams.set('order', selectedSort.split('&')[1].split('=')[1]);
            window.location.href = baseUrl + '?' + urlParams.toString();
        });
    </script>
<script>
// Функция для отображения плашек рейтингов и инициализации всплывающих подсказок
function updateRatingAndTooltips() {
    // Плашки с рейтингом
    $('.product__item__pic').each(function() {
        var rating = $(this).data('rating');
        if (rating && rating !== 'N/A') {
            var ratingBadge = $('<div>').addClass('rating-badge').css({
                'background-color': '#FFD700',
                'color': '#000'
            }).html('<i class="fa fa-star"></i> ' + rating);
            $(this).append(ratingBadge);
        }
    });

    // Инициализация всплывающих подсказок
    $('.product__item__pic').on('mouseenter', function() {
        $(this).find('.anime-tooltip').fadeIn(200);
    }).on('mouseleave', function() {
        $(this).find('.anime-tooltip').fadeOut(200);
    });
}

updateRatingAndTooltips();

// Загрузка дополнительного контента при скролле
let page = 1;
let isLoading = false;
let hasMoreData = true;

function loadMoreAnime() {
    if (isLoading || !hasMoreData) return;
    isLoading = true;

    let sort = "<?= $sort ?>";
    let order = "<?= $order ?>";
    let year = "<?= $year ?>";
    let genre = "<?= $genre ?>";
    let type = "<?= $type ?>";
    let status = "<?= $status ?>";

    page++;

    let url = `load_more_anime_list.php?sort=${sort}&order=${order}&year=${year}&genre=${genre}&type=${type}&status=${status}&page=${page}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                hasMoreData = false;
                return;
            }

            const animeListContainer = document.getElementById('anime-list');
            data.forEach(anime => {
                let episodesBadge = '';
                if (anime.episodes == 0) {
                    episodesBadge = '<div class="ep" style="background-color: #800080;">Выходит</div>';
                } else if (anime.episodes == 1) {
                    episodesBadge = '<div class="ep">1 серия</div>';
                } else if (anime.episodes > 1 && anime.episodes < 5) {
                    episodesBadge = `<div class="ep">${anime.episodes} серии</div>`;
                } else {
                    episodesBadge = `<div class="ep">${anime.episodes} серий</div>`;
                }

                let animeHTML = `
                    <div class="col-lg-4 col-md-6 col-sm-6">
                        <div class="product__item">
                            <a href="anime-details?id=${anime.shikimori_id}">
                                <div class="product__item__pic set-bg" style="background-image: url('${anime.cover_url}')" data-rating="${anime.rating}">
                                    ${episodesBadge}
                                    ${anime.rating ? '<div class="rating-badge" style="background-color: #FFD700; color: #000;"><i class="fa fa-star"></i> ' + anime.rating + '</div>' : ''}
                                    <div class="anime-tooltip">
                                        <h4>${anime.title}</h4>
                                        <p>${anime.description.substring(0, 250)}...</p>
                                        <ul>
                                            <li><strong>Тип:</strong> ${anime.type}</li>
                                            <li><strong>Год выпуска:</strong> ${anime.year}</li>
                                            <li><strong>Студия:</strong> ${anime.studio}</li>
                                            <li><strong>Длина серии:</strong> ${anime.duration} мин.</li>
                                        </ul>
                                    </div>
                                </div>
                            </a>
                            <div class="product__item__text">
                                <ul>
                                    ${anime.genres.split(',').map(genre => `<li>${genre}</li>`).join('')}
                                </ul>
                                <h5><a href="anime-details?id=${anime.shikimori_id}">${anime.title}</a></h5>
                            </div>
                        </div>
                    </div>
                `;
                animeListContainer.insertAdjacentHTML('beforeend', animeHTML);
            });

            updateRatingAndTooltips();

            isLoading = false;
        })
        .catch(error => {
            console.error('Ошибка при загрузке данных:', error);
            isLoading = false;
        });
}

window.addEventListener('scroll', function() {
    const scrollHeight = document.documentElement.scrollHeight;
    const scrollTop = document.documentElement.scrollTop || window.pageYOffset;
    const clientHeight = document.documentElement.clientHeight;

    if (scrollTop + clientHeight >= scrollHeight - 200) {
        loadMoreAnime();
    }
});
</script>
<script>
    const genreMapping = {
        "action": "Экшен",
        "adventure": "Приключения",
        "cars": "Машины",
        "comedy": "Комедия",
        "dementia": "Безумие",
        "demons": "Демоны",
        "drama": "Драма",
        "ecchi": "Этти",
        "fantasy": "Фэнтези",
        "game": "Игры",
        "harem": "Гарем",
        "historical": "Исторический",
        "horror": "Ужасы",
        "josei": "Дзёсей",
        "kids": "Детский",
        "magic": "Магия",
        "martial arts": "Боевые искусства",
        "mecha": "Меха",
        "military": "Военный",
        "music": "Музыка",
        "mystery": "Мистика",
        "parody": "Пародия",
        "police": "Полиция",
        "psychological": "Психологический",
        "romance": "Романтика",
        "samurai": "Самураи",
        "school": "Школа",
        "sci-fi": "Научная фантастика",
        "seinen": "Сэйнэн",
        "shoujo": "Сёдзё",
        "shoujo ai": "Сёдзё Ай",
        "shounen": "Сёнэн",
        "shounen ai": "Сёнэн Ай",
        "slice of life": "Повседневность",
        "space": "Космос",
        "sports": "Спорт",
        "super power": "Суперсила",
        "supernatural": "Сверхъестественное",
        "thriller": "Триллер",
        "vampire": "Вампиры",
        "yaoi": "Яой",
        "yuri": "Юри"
    };

    const statusMapping = {
        "completed": "Завершено",
        "ongoing": "Сейчас выходит",
        "anons": "Еще не вышло"
    };

    const typeMapping = {
        "tv": "ТВ",
        "movie": "Фильм",
        "ova": "OVA",
        "ona": "ONA",
        "special": "Спешл",
        "music": "Музыкальное видео"
    };

    var slider = document.getElementById('yearRangeSlider');

    noUiSlider.create(slider, {
        start: [1959, 2025],
        connect: true,
        range: {
            'min': 1959,
            'max': 2025
        },
        step: 1,
        tooltips: true,
        format: {
            to: function (value) {
                return Math.round(value);
            },
            from: function (value) {
                return Number(value);
            }
        }
    });

    document.getElementById('applyFilters').addEventListener('click', function() {
        var startYear = slider.noUiSlider.get()[0];
        var endYear = slider.noUiSlider.get()[1];
        var genre = document.getElementById('genres').value;
        var type = document.getElementById('animeType').value;
        var status = document.getElementById('status').value;

        if (genre && genreMapping[genre.toLowerCase()]) {
            genre = genreMapping[genre.toLowerCase()];
        }

        if (type && typeMapping[type.toLowerCase()]) {
            type = typeMapping[type.toLowerCase()];
        }

        if (status && statusMapping[status.toLowerCase()]) {
            status = statusMapping[status.toLowerCase()];
        }

        var query = `filter.php?start_year=${startYear}&end_year=${endYear}`;
        if (genre) query += `&genre=${genre}`;
        if (type) query += `&type=${type}`;
        if (status) query += `&status=${status}`;

        console.log('Отправка запроса:', query);

        fetch(query)
            .then(response => response.json())
            .then(data => {
                console.log('Полученные данные:', data);
                const animeListContainer = document.getElementById('anime-list');
                animeListContainer.innerHTML = '';

                const pagination = document.querySelector('.product__pagination');
                if (pagination) {
                    pagination.style.display = 'none';
                }

                if (data.length === 0) {
                    animeListContainer.innerHTML = '<p>Ничего не найдено по выбранным фильтрам.</p>';
                } else {
                    data.forEach(anime => {
                        let animeHTML = `
                            <div class="col-lg-4 col-md-6 col-sm-6">
                                <div class="product__item">
                                    <a href="anime-details?id=${anime.shikimori_id}">
                                        <div class="product__item__pic set-bg" style="background-image: url('${anime.cover_url}')">
                                            <div class="ep">${anime.episodes} серий</div>
                                        </div>
                                    </a>
                                    <div class="product__item__text">
                                        <ul>
                                            ${anime.genres.split(',').map(genre => `<li>${genre}</li>`).join('')}
                                        </ul>
                                        <h5><a href="anime-details?id=${anime.shikimori_id}">${anime.title}</a></h5>
                                    </div>
                                </div>
                            </div>
                        `;
                        animeListContainer.insertAdjacentHTML('beforeend', animeHTML);
                    });
                }
            })
            .catch(error => {
                console.error('Ошибка при выполнении запроса:', error);
                document.getElementById('anime-list').innerHTML = '<p>Ошибка загрузки данных. Попробуйте позже.</p>';
            });
    });
</script>
</html>
