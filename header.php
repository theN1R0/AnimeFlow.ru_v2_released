<?php
session_start();
include 'db.php';
date_default_timezone_set('Europe/Moscow');
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

$username = 'Гость'; 
$avatar = 'img/avatar.png';

if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT username, avatar FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        $username = htmlspecialchars($user['username']);
        $avatar = !empty($user['avatar']) ? htmlspecialchars($user['avatar']) : 'img/avatar.png';
    }
    $avatar_with_timestamp = $avatar . '?t=' . time();
} else {
    $avatar_with_timestamp = $avatar;
}

$stmt_anime = $conn->query("SELECT shikimori_id, title FROM anime ORDER BY title ASC");
$animeList = $stmt_anime->fetchAll(PDO::FETCH_ASSOC);
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Ваши любимые аниме. Смотрите онлайн бесплатно с полным списком жанров, сезонов и новинок только на AnimeFlow. Откройте мир японской анимации с нами!">
    <meta name="keywords" content="аниме онлайн, смотреть аниме бесплатно, смотреть аниме онлайн бесплатно в хорошем качестве, онлайн бесплатно в хорошем качестве, аниме в хорошем качестве, лучшие аниме, аниме 2024, новые аниме, аниме сериалы, аниме фильмы, аниме без рекламы, аниме жанры, аниме топ, популярные аниме, смотреть аниме HD, аниме онлайн бесплатно, лучшие аниме сериалы, аниме новинки, аниме онлайн 2024, лучшие аниме онлайн, аниме HD, бесплатное аниме, аниме потоковое видео, топ аниме, манга и аниме, аниме без подписки">
    <title>AnimeFlow - Смотрите аниме онлайн бесплатно</title>
    <meta name="viewport" content="width=1024"> <!-- фиксированная ширина как на ПК -->
</head>
<header class="header">
    <div class="container">
        <div class="row">
            <div class="col-lg-2">
                <div class="header__logo">
                    <a href="./">
                        <img src="img/logo.png" alt="Логотип">
                    </a>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="header__nav">
                    <nav class="header__menu mobile-menu">
                        <ul>
                            <li><a href="./">Главная</a></li>
                            <li><a href="./categories">Список аниме</a></li>
                            <li><a href="./anime-details?id=<?= htmlspecialchars($random_anime['shikimori_id']); ?>">Случайное</a></li>
                            <li><a href="./copyright">Контакты</a></li>
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                <li><a href="./admin">Админ-панель</a></li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="header__right">
                    <a href="#" class="search-switch"><span class="icon_search"></span></a>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="./profile?id=<?= $_SESSION['user_id']; ?>">
                            <img src="<?= $avatar_with_timestamp ?>" alt="Аватар" style="width: 40px; height: 40px; border-radius: 50%;">
                        </a>
                        <a href="./profile?id=<?= $_SESSION['user_id']; ?>" style="margin-left: 10px; color: white;">
                            <?= htmlspecialchars($username); ?>
                        </a>
                    <?php else: ?>
                        <a href="./login" style="display: flex; align-items: center;">
                            <img src="<?= $avatar_with_timestamp ?>" alt="Аватар" style="width: 40px; height: 40px; border-radius: 50%;">
                            <span style="margin-left: 10px; color: white;">Гость</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div id="mobile-menu-wrap"></div>
    </div>
</header>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
   (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
   m[i].l=1*new Date();
   for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
   k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
   (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

   ym(98159894, "init", {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true,
        webvisor:true
   });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/98159894" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
<!-- Yandex.RTB -->
<script>window.yaContextCb=window.yaContextCb||[]</script>
<script src="https://yandex.ru/ads/system/context.js" async></script>
<div class="search-model">
    <div class="h-100 d-flex align-items-center justify-content-center">
        <div class="search-close-switch"><i class="icon_close"></i></div>
        <form class="search-model-form">
            <input type="text" id="search-input" placeholder="Поиск... ">
            <div id="search-results" class="search-results">
                <ul id="search-results-list"></ul>
            </div>
        </form>
    </div>
</div>
<script>
    var allAnime = <?php echo json_encode($animeList); ?>;
</script>
<script>
    // Открытие поиска
    document.querySelector('.search-switch').addEventListener('click', function() {
        document.querySelector('.search-model').style.display = 'flex';
        document.getElementById('search-input').focus();
    });

    // Закрытие поиска через крестик
    document.querySelector('.search-close-switch').addEventListener('click', function() {
        document.querySelector('.search-model').style.display = 'none';
    });

    // Закрытие поиска по нажатию клавиши Esc
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            document.querySelector('.search-model').style.display = 'none';
        }
    });

    document.getElementById('search-input').addEventListener('input', function() {
        const searchValue = this.value.toLowerCase().trim();
        const resultsContainer = document.getElementById('search-results');
        const resultsList = document.getElementById('search-results-list');
        
        resultsList.innerHTML = '';

        if (searchValue.length > 0) {
            const filteredAnime = allAnime.filter(anime => {
                const titleMatch = anime.title.toLowerCase().includes(searchValue);
                const idMatch = anime.shikimori_id.toString().includes(searchValue);  // Проверка по Shikimori ID
                return titleMatch || idMatch;  // Возвращаем true, если совпадает название или ID
            });

            if (filteredAnime.length > 0) {
                resultsContainer.style.display = 'block';
                filteredAnime.forEach(anime => {
                    const listItem = document.createElement('li');
                    listItem.textContent = anime.title;  // Отображаем только название
                    listItem.addEventListener('click', function() {
                        window.location.href = `anime-details.php?id=${anime.shikimori_id}`;
                    });
                    resultsList.appendChild(listItem);
                });
            } else {
                resultsList.innerHTML = '<li>Ничего не найдено</li>';
            }
        } else {
            resultsContainer.style.display = 'none';
        }
    });
</script>
<style>
    .search-model {
        position: fixed;
        top: 0;
        left: 0;
        height: 100%;
        width: 100%;
        background: rgba(0, 0, 0, 0.8);
        z-index: 999;
        display: none;
        align-items: center;
        justify-content: center;
    }


.header__right .search-switch {
    position: relative; /* Устанавливаем относительное позиционирование */
    z-index: 1000; /* Поднимаем на уровень выше */
    margin-right: 20px; /* Отступ справа */
    margin-left: 10px; /* Немного отодвигаем от аватара */
}

.search-results {
    position: absolute;
    top: 65px;
    width: 500px;
    background-color: rgba(0, 0, 0, 0.9);
    border-radius: 10px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    z-index: 999;
    display: none;
    max-height: 690px;
    overflow-y: auto;
    scrollbar-width: none;
}

.search-results::-webkit-scrollbar {
    display: none;
}

    .search-results ul {
        list-style-type: none;
        padding: 0;
    }

    .search-results li {
        padding: 10px;
        font-size: 18px;
        font-weight: bold;
        color: white;
        cursor: pointer;
    }

    .search-results li:hover {
        background-color: rgba(255, 255, 255, 0.1); 
        border-radius: 8px;
    }

    .search-close-switch {
        position: absolute;
        right: 20px;
        top: 20px;
        cursor: pointer;
        color: white;
        font-size: 24px;
    }

.header {
    display: flex;
    align-items: center;
}

.header__right {
    display: flex !important;
    align-items: center !important;
    justify-content: flex-end !important;
    height: 100%;
}

.header__right a:last-child {
    margin-left: -10px !important;
}

.header__right img {
    width: 40px !important;
    height: 40px !important;
    border-radius: 50% !important;
    object-fit: cover !important;
    flex-shrink: 0 !important;
}

.header__right a {
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    max-width: 200px !important;
    color: white !important;
    text-align: right !important;
}

    .header__right .icon_search {
        margin-right: 0;
    }

</style>
