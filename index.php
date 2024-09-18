<?php
session_start();
include 'db.php';

$username = '';
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    if ($user) {
        $username = $user['username'];
    }
}

$stmt = $conn->prepare("SELECT * FROM anime ORDER BY added_at DESC LIMIT 3");
$stmt->execute();
$latest_anime = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT * FROM anime WHERE rating >= 8 ORDER BY RAND() LIMIT 5");
$stmt->execute();
$random_popular_anime = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT *, rating FROM anime ORDER BY rating DESC LIMIT 25");
$stmt->execute();
$popular_anime = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT * FROM anime WHERE shikimori_id IS NOT NULL ORDER BY RAND() LIMIT 1");
$stmt->execute();
$random_anime = $stmt->fetch(PDO::FETCH_ASSOC);
$avatar = $user['avatar'] ? htmlspecialchars($user['avatar']) : $default_avatar;
$banner = $user['cover_image'] ? htmlspecialchars($user['cover_image']) : $default_banner;

$description = $_POST['description'];
?>
<!DOCTYPE html>
<html lang="zxx">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Ваши любимые аниме. Смотрите онлайн бесплатно с полным списком жанров, сезонов и новинок только на AnimeFlow. Откройте мир японской анимации с нами!">
    <meta name="keywords" content="аниме онлайн, смотреть аниме бесплатно, смотреть аниме онлайн бесплатно в хорошем качестве, онлайн бесплатно в хорошем качестве, аниме в хорошем качестве, лучшие аниме, аниме 2024, новые аниме, аниме сериалы, аниме фильмы, аниме без рекламы, аниме жанры, аниме топ, популярные аниме, смотреть аниме HD, аниме онлайн бесплатно, лучшие аниме сериалы, аниме новинки, аниме онлайн 2024, лучшие аниме онлайн, аниме HD, бесплатное аниме, аниме потоковое видео, топ аниме, манга и аниме, аниме без подписки">
    <meta name="viewport" content="width=1024">
    <meta name="yandex-verification" content="2db15e54d9fc0173" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>AnimeFlow</title>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Mulish:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="css/elegant-icons.css" type="text/css">
    <link rel="stylesheet" href="css/nice-select.css" type="text/css">
    <link rel="stylesheet" href="css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="css/slicknav.min.css" type="text/css">
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>" type="text/css">
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
</head>
<body>
    <div id="preloder">
        <div class="loader"></div>
    </div>
<?php include 'header.php'; ?>
<section class="hero">
    <div class="container">
        <div class="hero__slider owl-carousel">
            <?php foreach ($random_popular_anime as $anime): ?> <!-- $latest_anime для последних -->
                <div class="hero__items">
                    <div class="blurred-bg" style="background-image: url('<?= htmlspecialchars($anime['cover_url']); ?>');"></div>
                    <img class="clear-image" src="<?= htmlspecialchars($anime['cover_url']); ?>" alt="<?= htmlspecialchars($anime['title']); ?>">
                    <div class="hero__text">
                        <div class="label"><?= htmlspecialchars($anime['genres']); ?></div>
                        <h2><?= htmlspecialchars($anime['title']); ?></h2>
                        <p><?= mb_strimwidth(htmlspecialchars_decode($anime['description']), 0, 100, "..."); ?></p>
                        <a href="anime-details?id=<?= htmlspecialchars($anime['shikimori_id']); ?>">
                            <span>Смотреть </span> 
                            <i class="fa fa-play" style="font-size: 1.2em; vertical-align: middle;"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<section class="product spad">
    <div class="container">
        <div class="row">
            <div class="trending__product">
                <div class="section-title">
                    <h4>Популярное сейчас</h4>
                </div>
                <div class="row" id="anime-list">
                    <?php foreach ($popular_anime as $anime): ?>
                        <div class="col-lg-2_4 col-md-4 col-sm-6" style="flex: 0 0 20%; max-width: 20%;">
                            <div class="product__item">
                                <a href="anime-details?id=<?= $anime['shikimori_id']; ?>">
                                    <div class="product__item__pic set-bg" 
                                        data-setbg="<?= htmlspecialchars($anime['cover_url']); ?>" 
                                        data-rating="<?= !empty($anime['rating']) ? htmlspecialchars($anime['rating']) : 'N/A'; ?>">
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
                                    <h5><a href="anime-details?id=<?= $anime['shikimori_id']; ?>"><?= htmlspecialchars($anime['title']); ?></a></h5>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="navigation-buttons">
                    <div class="left-arrow">
                        <button id="prev-page" class="left-btn"><i class="fa fa-arrow-left"></i></button>
                    </div>
                    <div class="right-arrow">
                        <button id="next-page" class="right-btn"><i class="fa fa-arrow-right"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include 'footer.php'; ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="js/jquery-3.3.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.nice-select.min.js"></script>
<script src="js/mixitup.min.js"></script>
<script src="js/jquery.slicknav.js"></script>
<script src="js/owl.carousel.min.js"></script>
<script src="js/main.js"></script>
<script>
let currentPage = 0;
const limit = 25;
let tooltipTimeout;

function loadAnimePage(page) {
    const animeList = document.getElementById('anime-list');

    animeList.classList.remove('fade-in');
    animeList.classList.add('fade-out');

    const xhr = new XMLHttpRequest();
    const offset = page * limit;
    xhr.open('GET', 'load_more_anime.php?offset=' + offset, true);

    xhr.onload = function() {
        if (xhr.status === 200) {
            const response = xhr.responseText;
            const totalAnimes = xhr.getResponseHeader('X-Total-Count');

            setTimeout(function() {
                animeList.innerHTML = response;
                animeList.classList.remove('fade-out');
                animeList.classList.add('fade-in');

                $('.set-bg').each(function() {
                    var bg = $(this).data('setbg');
                    $(this).css('background-image', 'url(' + bg + ')');
                });

                updateRatingBadges();
                addTooltipLogic();

                $('a').off('click').on('click', function(e) {
                    window.location.href = $(this).attr('href');
                });

                if (currentPage === 0) {
                    document.getElementById('prev-page').style.display = 'none';
                } else {
                    document.getElementById('prev-page').style.display = 'inline-block';
                }

                const maxPages = Math.ceil(totalAnimes / limit);
                if (currentPage >= maxPages - 1) {
                    document.getElementById('next-page').style.display = 'none';
                } else {
                    document.getElementById('next-page').style.display = 'inline-block';
                }

                window.scrollTo({
                    top: 800,
                    behavior: 'smooth'
                });

            }, 500);
        }
    };
    xhr.send();
}

function updateRatingBadges() {
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
}

function addTooltipLogic() {
    if (window.innerWidth > 768) { 
        $('.product__item').on('mouseenter', function() {
            const $tooltip = $(this).find('.anime-tooltip');
            tooltipTimeout = setTimeout(function() {
                $tooltip.addClass('visible');
            }, 2000);
        }).on('mouseleave', function() {
            clearTimeout(tooltipTimeout);
            const $tooltip = $(this).find('.anime-tooltip');
            $tooltip.removeClass('visible');
        });
    }
}

document.getElementById('next-page').addEventListener('click', function() {
    currentPage += 1;
    loadAnimePage(currentPage);
});

document.getElementById('prev-page').addEventListener('click', function() {
    if (currentPage > 0) {
        currentPage -= 1;
        loadAnimePage(currentPage);
    }
});

</script>
<script>
$(document).ready(function() {
    $(window).resize(function() {
        if ($(window).width() < 768) {
            $('.col-lg-2_4').css({
                'flex': '0 0 33.33%',
                'max-width': '33.33%'
            });
        } else {
            $('.col-lg-2_4').css({
                'flex': '0 0 20%',
                'max-width': '20%'
            });
        }
    }).resize();
});
</script>
</body>
</html>