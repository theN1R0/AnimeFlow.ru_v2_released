<?php
session_start();
include 'db.php';
include 'related_anime_handler.php';
date_default_timezone_set('Europe/Moscow');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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
// Обработка загрузки аватара
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['avatar_image'])) {
    $avatar_file = $_FILES['avatar_image']['tmp_name'];
    $avatar_path = 'img/avatars/' . basename($_FILES['avatar_image']['name']);
    if (move_uploaded_file($avatar_file, $avatar_path)) {
        $sql = "UPDATE users SET avatar = :avatar WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['avatar' => $avatar_path, 'id' => $user_id]);
        header('Location: profile?id=' . $user_id);
        exit;
    }
}
if (!isset($_GET['id'])) {
    die("Аниме не найдено.");
}
$anime_id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM anime WHERE shikimori_id = ?");
$stmt->execute([$anime_id]);
$anime = $stmt->fetch();
// Если аниме не найдено
if (!$anime) {
    header("Location: /404.php"); 
    exit();
}
// Проверка избранное
$is_favorite = false;
if (isset($_SESSION['user_id'])) {
    $stmt_favorite_check = $conn->prepare("SELECT COUNT(*) FROM favorite_anime WHERE user_id = ? AND anime_id = ?");
    $stmt_favorite_check->execute([$_SESSION['user_id'], $anime['id']]);
    $is_favorite = $stmt_favorite_check->fetchColumn() > 0;
}
// Проверка статуса аниме
$current_status = '';
if (isset($_SESSION['user_id'])) {
    $stmt_status_check = $conn->prepare("SELECT status FROM user_anime_status WHERE user_id = ? AND anime_id = ?");
    $stmt_status_check->execute([$_SESSION['user_id'], $anime['id']]);
    $current_status = $stmt_status_check->fetchColumn();
}
// Проверка, нужно ли проверять связанное аниме
if ($anime['related_checked'] == 0) {
    checkAndAddRelatedAnime($anime_id, $conn, $genreMapping, $typeMapping, $statusMapping);
    $stmt_update_checked = $conn->prepare("UPDATE anime SET related_checked = 1 WHERE shikimori_id = ?");
    $stmt_update_checked->execute([$anime_id]);
    if (!empty($anime['related_anime'])) {
        $related_anime_ids = explode(',', $anime['related_anime']);
        $placeholders = str_repeat('?,', count($related_anime_ids) - 1) . '?';
        $stmt_update_related_checked = $conn->prepare("UPDATE anime SET related_checked = 1 WHERE shikimori_id IN ($placeholders)");
        $stmt_update_related_checked->execute($related_anime_ids);
    }
}
// Получаем связанные аниме
$related_anime_list = [];
if (!empty($anime['related_anime'])) {
    $related_anime_ids = explode(',', $anime['related_anime']);  // Разбиваем список связанных аниме по запятой
    $placeholders = str_repeat('?,', count($related_anime_ids) - 1) . '?';  // Создаем placeholders для SQL запроса
    $stmt_related = $conn->prepare("SELECT title, shikimori_id FROM anime WHERE shikimori_id IN ($placeholders)");
    $stmt_related->execute($related_anime_ids);  // Выполняем запрос к базе данных с использованием связанных ID
    $related_anime_list = $stmt_related->fetchAll(PDO::FETCH_ASSOC);  // Получаем все связанные аниме
}
$stmt_comments = $conn->prepare("
    SELECT comments.*, users.username, users.avatar 
    FROM comments 
    JOIN users ON comments.user_id = users.id 
    WHERE comments.anime_id = ? 
    ORDER BY comments.created_at DESC
");
$stmt_comments->execute([$anime['id']]);
$comments = $stmt_comments->fetchAll(PDO::FETCH_ASSOC);
//получение случайного аниме 
$stmt = $conn->prepare("SELECT * FROM anime WHERE shikimori_id IS NOT NULL ORDER BY RAND() LIMIT 1");
$stmt->execute();
$random_anime = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="keywords" content="аниме онлайн, смотреть аниме бесплатно, смотреть аниме онлайн бесплатно в хорошем качестве, онлайн бесплатно в хорошем качестве, аниме в хорошем качестве, лучшие аниме, аниме 2024, новые аниме, аниме сериалы, аниме фильмы, аниме без рекламы, аниме жанры, аниме топ, популярные аниме, смотреть аниме HD, аниме онлайн бесплатно, лучшие аниме сериалы, аниме новинки, аниме онлайн 2024, лучшие аниме онлайн, аниме HD, бесплатное аниме, аниме потоковое видео, топ аниме, манга и аниме, аниме без подписки">
    <meta name="viewport" content="width=1024">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= htmlspecialchars($anime['title']); ?> | Смотреть онлайн бесплатно на AnimeFlow</title>
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
    <link rel="stylesheet" href="css/anime-details.css?v=<?php echo time(); ?>" type="text/css">
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
    <div id="preloder">
        <div class="loader"></div>
    </div>
    <?php include 'header.php'; ?>
    <!-- Аниме инфо -->
    <section class="anime-details spad">
        <div class="container">
            <div class="anime__details__content">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="anime__details__pic set-bg" data-setbg="<?= htmlspecialchars($anime['cover_url']); ?>">
                        </div>
                        <div class="status-btns">
                            <form id="favorite-form" action="javascript:void(0);" method="POST">
                                <input type="hidden" name="anime_id" value="<?= $anime['id']; ?>">
                                <button type="button" class="favorite-btn <?= $is_favorite ? 'favorite-active' : '' ?>" id="favorite-btn" title="В избранное">
                                    <i class="fa <?= $is_favorite ? 'fa-heart' : 'fa-heart-o' ?>"></i>
                                </button>
                            </form>
                            <form id="status-form" action="javascript:void(0);" method="POST">
                                <button type="button" class="status-btn <?= ($current_status === 'watching') ? 'status-active' : '' ?>" id="watching-btn" title="Смотрю">
                                    <i class="fa fa-eye"></i> 
                                </button>
                                <button type="button" class="status-btn <?= ($current_status === 'completed') ? 'status-active' : '' ?>" id="completed-btn" title="Просмотрено">
                                    <i class="fa fa-check"></i> 
                                </button>
                                <button type="button" class="status-btn <?= ($current_status === 'dropped') ? 'status-active' : '' ?>" id="dropped-btn" title="Брошено">
                                    <i class="fa fa-times"></i> 
                                </button>
                                <button type="button" class="status-btn <?= ($current_status === 'planned') ? 'status-active' : '' ?>" id="planned-btn" title="Запланировано">
                                    <i class="fa fa-clock-o"></i> 
                                </button>
                            </form>
                        </div>
                        <?php if ($_SESSION['role'] == 'admin'): ?>
                            <div class="admin-buttons">
                                <form action="delete_anime.php" method="POST" onsubmit="return confirm('Вы уверены, что хотите удалить это аниме?');">
                                    <input type="hidden" name="shikimori_id" value="<?= htmlspecialchars($anime['shikimori_id']); ?>">
                                    <button type="submit" class="btn btn-danger">Удалить</button>
                                </form>
                                <form action="javascript:void(0);" method="POST">
                                    <button type="submit" class="btn btn-danger" id="updateRelatedAnime" data-anime-id="<?= $anime['shikimori_id']; ?>">Обновить</button>
                                </form>
                            </div>
                        <?php endif; ?>
                        <style>
                            .admin-buttons {
                                display: flex;
                                justify-content: center;
                                gap: 10px;
                                margin-top: 10px;
                            }

                            .admin-buttons form {
                                margin: 0;
                            }

                            .admin-buttons button {
                                display: inline-block;
                                width: 120px;
                            }
                        </style>
                        <div class="anime__details__rating">
                            <div class="rating">
                                <?php 
                                    $full_stars = floor($anime['rating']);
                                    $half_star = $anime['rating'] - $full_stars >= 0.5;
                                    for ($i = 1; $i <= 10; $i++) {
                                        if ($i <= $full_stars) {
                                            echo '<a href="#"><i class="fa fa-star"></i></a>';
                                        } elseif ($half_star && $i == $full_stars + 1) {
                                            echo '<a href="#"><i class="fa fa-star-half-o"></i></a>';
                                        } else {
                                            echo '<a href="#"><i class="fa fa-star-o"></i></a>';
                                        }
                                    }
                                ?>
                            </div>
                            <span><?= htmlspecialchars($anime['rating']); ?> / 10</span>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <div class="anime__details__text">
                            <div class="anime__details__title">
                                <h3><?= htmlspecialchars($anime['title']); ?></h3>
                                <span><?= htmlspecialchars($anime['alternative_title']); ?></span>
                            </div>
                            <div class="line-divider"></div>
                            <p><?= htmlspecialchars_decode($anime['description']); ?></p>
                            <div class="line-divider"></div>
                            <div class="anime__details__widget">
                                <div class="row">
                                    <div class="col-lg-6 col-md-6">
                                        <ul>
                                            <li><span>Тип:</span> <span><?= htmlspecialchars($anime['type']); ?></span></li>
                                            <li><span>Студия:</span> <span><?= htmlspecialchars($anime['studio']); ?></span></li>
                                            <li><span>Дата выхода:</span> <span><?= htmlspecialchars($anime['year']); ?></span></li>
                                            <li><span>Статус:</span> <span><?= htmlspecialchars($anime['status']); ?></span></li>
                                            <li><span>Жанр:</span> <span><?= htmlspecialchars($anime['genres']); ?></span></li>
                                        </ul>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <ul>
                                            <li><span>Оценка:</span> <span><?= htmlspecialchars($anime['rating']); ?></span></li>
                                            <li><span>Продолжительность:</span> <span><?= htmlspecialchars($anime['duration']); ?> мин.</span></li>
                                            <li><span>Эпизоды:</span> <span><?= htmlspecialchars($anime['episodes']); ?></span></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="line-divider"></div>
            </div>
            <div id="kodik-player"></div>
            <div class="line-divider"></div>
            <!-- Контейнер для рекламного блока -->
            <div id="ad-container" style="text-align: center; margin: 20px 0;">
                <div id="yandex_rtb_R-A-11910896-2"></div>
                <script>
                window.yaContextCb.push(() => {
                    Ya.Context.AdvManager.render({
                        "blockId": "R-A-11910896-2",
                        "renderTo": "yandex_rtb_R-A-11910896-2"
                    })
                })
                </script>
            </div>
            <div class="line-divider"></div>
            <?php if (!empty($related_anime_list)): ?>
                <section class="related-anime-section">
                    <h3 id="toggleRelatedAnime" class="toggle-related-anime-btn" style="cursor: pointer;">
                        Связанное аниме (<?= count($related_anime_list); ?>)
                    </h3>
                    <div id="relatedAnimeContainer" class="related-anime-grid" style="display: none; opacity: 0;">
                        <?php foreach ($related_anime_list as $related_anime): ?>
                            <div class="related-anime-item">
                                <a href="anime-details.php?id=<?= htmlspecialchars($related_anime['shikimori_id']); ?>">
                                    <img src="https://shikimori.one/system/animes/original/<?= htmlspecialchars($related_anime['shikimori_id']); ?>.jpg" alt="<?= htmlspecialchars($related_anime['title']); ?>" class="related-anime-cover">
                                    <p class="related-anime-title"><?= htmlspecialchars($related_anime['title']); ?></p>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <div class="line-divider"></div>
            <?php endif; ?>
                <!-- Форма для комментариев -->
                <section class="comment-section">
                    <h3>Оставить комментарий</h3>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <form id="comment-form" method="POST">
                            <input type="hidden" name="anime_id" value="<?= $anime_id; ?>">
                            <textarea name="comment" rows="3" placeholder="Ваш комментарий..." required></textarea>
                            <button type="submit" class="btn btn-primary">Отправить</button>
                        </form>
                    <?php else: ?>
                        <p>Пожалуйста, <a href="login.php">войдите</a>, чтобы оставить комментарий.</p>
                    <?php endif; ?>
                </section>
                <section class="comments-list">
                    <h3>Комментарии</h3>
                    <div id="comments-container">
                        <?php if ($comments): ?>
                            <?php foreach ($comments as $comment): ?>
                                <div class="comment" data-comment-id="<?= $comment['id']; ?>">
                                    <div class="comment-avatar">
                                        <a href="profile.php?id=<?= $comment['user_id']; ?>">
                                            <img src="<?= htmlspecialchars($comment['avatar']) ?: 'img/avatar.png'; ?>" alt="Avatar">
                                        </a>
                                    </div>
                                    <div class="comment-content">
                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                            <strong>
                                                <a href="profile.php?id=<?= $comment['user_id']; ?>" style="color: white; text-decoration: none;">
                                                    <?= htmlspecialchars($comment['username']); ?>
                                                </a>
                                            </strong>
                                            <?php if ($_SESSION['user_id'] == $comment['user_id'] || $_SESSION['role'] === 'admin'): ?>
                                                <button class="delete-comment-btn" data-comment-id="<?= $comment['id']; ?>" style="color: red; cursor: pointer;">&times;</button>
                                            <?php endif; ?>
                                        </div>
                                        <p><?= htmlspecialchars($comment['comment']); ?></p>
                                        <span><?= date('d M Y H:i', strtotime($comment['created_at'])); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>Пока нет комментариев. Будьте первым!</p>
                        <?php endif; ?>
                    </div>
                </section>
                <script>
                var kodikAddPlayers = {
                    width: "1140",
                    height: "700",
                    shikimoriID: "<?= htmlspecialchars($anime['shikimori_id']); ?>"  // Передаем ID Shikimori
                };
                !function(e,n,t,r,a){r=e.createElement(n),a=e.getElementsByTagName(n)
                [0],r.async=!0,r.src=t,a.parentNode.insertBefore(r,a)}
                (document,"script","//kodik-add.com/add-players.min.js");
                </script>
        </div>
        </section>
    </script>
    <!-- Yandex.RTB R-A-11910896-1 -->
    <script>
    window.yaContextCb.push(() => {
        Ya.Context.AdvManager.render({
            "blockId": "R-A-11910896-1",
            "type": "floorAd",
            "platform": "desktop"
        })
    })
    </script>
        <!-- Yandex.RTB R-A-11910896-2 -->
    <div id="yandex_rtb_R-A-11910896-2"></div>
    <?php include 'footer.php'; ?>
    <!-- Js -->
    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.nice-select.min.js"></script>
    <script src="js/mixitup.min.js"></script>
    <script src="js/jquery.slicknav.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/anime-details.js"></script>
    <script src="js/main.js"></script>
<script>
$(document).ready(function() {
    // Добавление в избранное
    $('#favorite-btn').on('click', function() {
        var animeId = $('input[name="anime_id"]').val();
        var button = $(this);
        $.post('add_to_favorites.php', { anime_id: animeId }, function(response) {
            button.toggleClass('favorite-active');
            button.html(button.hasClass('favorite-active') ? '<i class="fa fa-heart"></i>' : '<i class="fa fa-heart-o"></i>');
        }).fail(function() {
            alert('Произошла ошибка. Попробуйте снова.');
        });
    });
    // Изменение статуса аниме
    $('.status-btn').on('click', function() {
        var status = $(this).attr('id').split('-')[0];
        var animeId = $('input[name="anime_id"]').val();
        $.post('update_anime_status.php', { anime_id: animeId, status: status }, function(response) {
            $('.status-btn').removeClass('status-active');
            $('#' + status + '-btn').addClass('status-active');
        }).fail(function() {
            alert('Произошла ошибка. Попробуйте снова.');
        });
    });
    // Добавление комментария
    $('#comment-form').on('submit', function(e) {
        e.preventDefault();
        var comment = $('textarea[name="comment"]').val().trim();
        var animeId = $('input[name="anime_id"]').val();
        var avatarUrl = '<?= htmlspecialchars($user['avatar'] ?? "img/avatar.png"); ?>';  // Правильный путь к аватарке
        if (!comment) {
            alert('Комментарий не может быть пустым.');
            return;
        }
        $.post('add_comment.php', { comment: comment, anime_id: animeId }, function(response) {
            var data = JSON.parse(response);
            if (data.success) {
                var newComment = `
                    <div class="comment" data-comment-id="${data.comment_id}">
                        <div class="comment-avatar">
                            <a href="profile.php?id=<?= $_SESSION['user_id']; ?>">
                                <img src="${avatarUrl}" alt="Avatar" style="width: 50px; height: 50px; border-radius: 50%;">
                            </a>
                        </div>
                        <div class="comment-content">
                            <strong>
                                <a href="profile.php?id=<?= $_SESSION['user_id']; ?>" style="color: white; text-decoration: none;"><?= htmlspecialchars($username); ?></a>
                            </strong>
                            <p>${data.comment}</p>
                            <span>${data.created_at}</span>
                        </div>
                    </div>
                `;
                $('#comments-container').prepend(newComment);
                $('textarea[name="comment"]').val('');
            }
        }).fail(function() {
            alert('Ошибка при добавлении комментария.');
        });
    });
    // Удаление комментария
    $(document).on('click', '.delete-comment-btn', function() {
        var commentId = $(this).data('comment-id');
        var commentBlock = $(this).closest('.comment');
        if (confirm('Вы уверены, что хотите удалить этот комментарий?')) {
            $.post('delete_comment.php', { comment_id: commentId }, function(response) {
                var data = JSON.parse(response);
                if (data.success) {
                    commentBlock.remove();
                } else {
                    alert('Ошибка при удалении комментария.');
                }
            }).fail(function() {
                alert('Произошла ошибка при удалении комментария.');
            });
        }
    });
});
</script>
<script>
$(document).ready(function() {
    $('#toggleRelatedAnime').on('click', function() {
        var relatedAnimeContainer = $('#relatedAnimeContainer');
        if (relatedAnimeContainer.css('opacity') == 0) {
            relatedAnimeContainer.css('display', 'grid');
            relatedAnimeContainer.animate({ opacity: 1 }, 500);
            relatedAnimeContainer.css('max-height', relatedAnimeContainer[0].scrollHeight + 'px');
            $('.related-anime-item').each(function(index) {
                $(this).delay(index * 100).queue(function(next) {
                    $(this).addClass('visible');
                    next();
                });
            });
        } else {
            $('.related-anime-item').each(function(index) {
                $(this).delay(index * 50).queue(function(next) {
                    $(this).removeClass('visible');
                    next();
                });
            });
            relatedAnimeContainer.css('max-height', '0');
            setTimeout(function() {
                relatedAnimeContainer.animate({ opacity: 0 }, 300, function() {
                    relatedAnimeContainer.css('display', 'none');
                });
            }, $('.related-anime-item').length * 50);
        }
    });
});
</script>
<script>
    document.getElementById('updateRelatedAnime').addEventListener('click', function() {
        var animeId = this.getAttribute('data-anime-id');
        var button = this;

        fetch('check_related_anime.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'anime_id=' + animeId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
            } else {
                alert('Ошибка: ' + data.message);
            }
        })
        .catch(error => {
            alert('Произошла ошибка при обновлении связанных аниме');
        });
    });
</script>
</body>
</html>
