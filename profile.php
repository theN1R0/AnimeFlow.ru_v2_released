<?php
session_start();
include 'db.php';
date_default_timezone_set('Europe/Moscow');

if (!isset($_GET['id'])) {
    die("Профиль не найден.");
}

$user_id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user_profile = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user_profile) {
    header("Location: /404.php");
    exit();
}

$current_user_id = $_SESSION['user_id'] ?? null;

$default_avatar = 'img/avatar.png';
$default_banner = 'img/background2.png';

$avatar = $user_profile['avatar'] ? htmlspecialchars($user_profile['avatar']) : $default_avatar;
$banner = $user_profile['cover_image'] ? htmlspecialchars($user_profile['cover_image']) : $default_banner;

$username_profile = !empty($user_profile['username']) ? htmlspecialchars($user_profile['username']) : 'Не указано';
$first_name = !empty($user_profile['first_name']) ? htmlspecialchars($user_profile['first_name']) : 'Не указано';
$last_name = !empty($user_profile['last_name']) ? htmlspecialchars($user_profile['last_name']) : 'Не указано';
$email = !empty($user_profile['email']) ? htmlspecialchars($user_profile['email']) : 'Не указано';
$status = !empty($user_profile['status']) ? htmlspecialchars($user_profile['status']) : 'Не указано';
$dob = !empty($user_profile['dob']) ? htmlspecialchars($user_profile['dob']) : '0000-00-00';
$gender = !empty($user_profile['gender']) ? htmlspecialchars($user_profile['gender']) : 'Не указано';
$country = !empty($user_profile['country']) ? htmlspecialchars($user_profile['country']) : 'Не указано';
$city = !empty($user_profile['city']) ? htmlspecialchars($user_profile['city']) : 'Не указано';

$username_header = 'Гость'; 
$avatar_header = 'img/avatar.png';

if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT username, avatar FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user_header = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user_header) {
        $username_header = htmlspecialchars($user_header['username']);
        $avatar_header = !empty($user_header['avatar']) ? htmlspecialchars($user_header['avatar']) : 'img/avatar.png';
    }
}

// Избранное
$stmt_favorite = $conn->prepare("
    SELECT anime.id, anime.shikimori_id, anime.title, anime.cover_url 
    FROM favorite_anime 
    JOIN anime ON favorite_anime.anime_id = anime.id 
    WHERE favorite_anime.user_id = :user_id
");
$stmt_favorite->execute(['user_id' => $user_id]);
$favorites = $stmt_favorite->fetchAll(PDO::FETCH_ASSOC);

// Просмотрено
$stmt_completed = $conn->prepare("
    SELECT anime.id, anime.shikimori_id, anime.title, anime.cover_url 
    FROM user_anime_status 
    JOIN anime ON user_anime_status.anime_id = anime.id 
    WHERE user_anime_status.user_id = :user_id AND user_anime_status.status = 'completed'
");
$stmt_completed->execute(['user_id' => $user_id]);
$completed = $stmt_completed->fetchAll(PDO::FETCH_ASSOC);

// Смотрю
$stmt_watching = $conn->prepare("
    SELECT anime.id, anime.shikimori_id, anime.title, anime.cover_url 
    FROM user_anime_status 
    JOIN anime ON user_anime_status.anime_id = anime.id 
    WHERE user_anime_status.user_id = :user_id AND user_anime_status.status = 'watching'
");
$stmt_watching->execute(['user_id' => $user_id]);
$watching = $stmt_watching->fetchAll(PDO::FETCH_ASSOC);

// Запланировано
$stmt_planned = $conn->prepare("
    SELECT anime.id, anime.shikimori_id, anime.title, anime.cover_url 
    FROM user_anime_status 
    JOIN anime ON user_anime_status.anime_id = anime.id 
    WHERE user_anime_status.user_id = :user_id AND user_anime_status.status = 'planned'
");
$stmt_planned->execute(['user_id' => $user_id]);
$planned = $stmt_planned->fetchAll(PDO::FETCH_ASSOC);

// Брошено
$stmt_dropped = $conn->prepare("
    SELECT anime.id, anime.shikimori_id, anime.title, anime.cover_url 
    FROM user_anime_status 
    JOIN anime ON user_anime_status.anime_id = anime.id 
    WHERE user_anime_status.user_id = :user_id AND user_anime_status.status = 'dropped'
");
$stmt_dropped->execute(['user_id' => $user_id]);
$dropped = $stmt_dropped->fetchAll(PDO::FETCH_ASSOC);

$favorites_count = count($favorites);
$completed_count = count($completed);
$watching_count = count($watching);
$planned_count = count($planned);
$dropped_count = count($dropped);

$stmt = $conn->prepare("SELECT * FROM anime WHERE shikimori_id IS NOT NULL ORDER BY RAND() LIMIT 1");
$stmt->execute();
$random_anime = $stmt->fetch(PDO::FETCH_ASSOC);
if (!empty($dob) && $dob != '0000-00-00') {
    $formatted_dob = (new DateTime($dob))->format('d.m.Y');
} else {
    $formatted_dob = 'Не указано';
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=1024">
    <title><?php echo $username_profile; ?> - Профиль</title>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Mulish:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="css/elegant-icons.css" type="text/css">
    <link rel="stylesheet" href="css/nice-select.css" type="text/css">
    <link rel="stylesheet" href="css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="css/slicknav.min.css" type="text/css">
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>" type="text/css">
    <link rel="stylesheet" href="css/profile.css?v=<?php echo time(); ?>" type="text/css">
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
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="./profile?id=<?= $_SESSION['user_id']; ?>">
                                <img src="<?= htmlspecialchars($avatar_header); ?>" alt="Аватар" style="width: 40px; height: 40px; border-radius: 50%;">
                            </a>
                            <a href="./profile?id=<?= $_SESSION['user_id']; ?>" style="margin-left: 10px; color: white;">
                                <?= htmlspecialchars($username_header); ?>
                            </a>
                        <?php else: ?>
                            <a href="./login" style="display: flex; align-items: center;">
                                <img src="img/avatar.png" alt="Аватар" style="width: 40px; height: 40px; border-radius: 50%;">
                                <span style="margin-left: 10px; color: white;">Гость</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div id="mobile-menu-wrap"></div>
        </div>
    </header>
    <main class="content-container">
        <div class="profile-banner" style="background-image: url('<?php echo $banner; ?>');">
            <?php if ($user_id == $current_user_id): ?>
            <div class="profile-buttons">
                <form action="update_profile.php" method="POST" enctype="multipart/form-data" style="display: inline-block;">
                    <label for="banner-upload" class="profile-btn">Изменить баннер</label>
                    <input type="file" id="banner-upload" name="banner_image" style="display:none;">
                </form>
                <form action="update_profile.php" method="POST" enctype="multipart/form-data" style="display: inline-block;">
                    <label for="avatar-upload" class="profile-btn">Изменить аватар</label>
                    <input type="file" id="avatar-upload" name="avatar_image" style="display:none;">
                </form>
                <button id="editProfileBtn" class="profile-btn">Редактировать</button>
                <div id="editProfileModal" class="modal">
                    <div class="modal-content">
                        <span class="close">&times;</span>
                        <form id="editProfileForm" method="POST" action="update_profile.php">
                            <div class="form-group">
                                <label for="first_name">Имя</label>
                                <input type="text" name="first_name" id="first_name" value="<?= htmlspecialchars($first_name); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="last_name">Фамилия</label>
                                <input type="text" name="last_name" id="last_name" value="<?= htmlspecialchars($last_name); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="dob">Дата рождения</label>
                                <input type="date" name="dob" id="dob" value="<?= htmlspecialchars($dob); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="country">Страна</label>
                                <input type="text" name="country" id="country" value="<?= htmlspecialchars($country); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="city">Город</label>
                                <input type="text" name="city" id="city" value="<?= htmlspecialchars($city); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="gender">Пол</label>
                                <select name="gender" id="gender" required>
                                    <option value="Мужской" <?= ($gender == 'Мужской') ? 'selected' : ''; ?>>Мужской</option>
                                    <option value="Женский" <?= ($gender == 'Женский') ? 'selected' : ''; ?>>Женский</option>
                                    <option value="Не указано" <?= ($gender == 'Не указано') ? 'selected' : ''; ?>>Не указано</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="status">Статус</label>
                                <input type="text" name="status" id="status" value="<?= htmlspecialchars($status); ?>">
                            </div>
                            <button type="submit" class="btn btn-primary">Сохранить</button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <div class="profile-content">
            <div class="profile-avatar">
                <img src="<?php echo $avatar; ?>" alt="Avatar">
            </div>
            <div class="profile-details">
                <h2><?php echo $username_profile; ?></h2>
                <div class="profile-details row">
                    <div class="col-md-6">
                        <p><strong>Имя:</strong> <?php echo $first_name; ?></p>
                        <p><strong>Фамилия:</strong> <?php echo $last_name; ?></p>
                        <p><strong>Email:</strong> <?php echo $email; ?></p>
                        <p><strong>Статус:</strong> <?php echo $status; ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Дата рождения:</strong> <?php echo $formatted_dob; ?></p>
                        <p><strong>Пол:</strong> <?php echo $gender; ?></p>
                        <p><strong>Страна:</strong> <?php echo $country; ?></p>
                        <p><strong>Город:</strong> <?php echo $city; ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="profile-favorites-section">
            <h3>
                <span class="anime-category active" data-category="favorites">
                    Избранное (<?= $favorites_count; ?>)
                </span>
                <span class="anime-category" data-category="completed">
                    Просмотрено (<?= $completed_count; ?>)
                </span>
                <span class="anime-category" data-category="watching">
                    Смотрю (<?= $watching_count; ?>)
                </span>
                <span class="anime-category" data-category="dropped">
                    Брошено (<?= $dropped_count; ?>)
                </span>
                <span class="anime-category" data-category="planned">
                    Запланировано (<?= $planned_count; ?>)
                </span>
            </h3>
            <div id="anime-list">
                <!-- Избранное -->
                <div class="favorites-grid" data-category="favorites">
                    <?php if (empty($favorites)): ?>
                        <div class="empty-message">Тут пока ничего нет 😿</div>
                    <?php else: ?>
                        <?php foreach ($favorites as $anime): ?>
                            <a href="anime-details?id=<?= htmlspecialchars($anime['shikimori_id']); ?>" class="favorite-anime">
                                <img src="<?= htmlspecialchars($anime['cover_url']); ?>" alt="<?= htmlspecialchars($anime['title']); ?>">
                                <p><?= htmlspecialchars($anime['title']); ?></p>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <!-- Просмотрено -->
                <div class="favorites-grid" data-category="completed" style="display: none;">
                    <?php if (empty($completed)): ?>
                        <div class="empty-message">Тут пока ничего нет 😿</div>
                    <?php else: ?>
                        <?php foreach ($completed as $anime): ?>
                            <a href="anime-details?id=<?= htmlspecialchars($anime['shikimori_id']); ?>" class="favorite-anime">
                                <img src="<?= htmlspecialchars($anime['cover_url']); ?>" alt="<?= htmlspecialchars($anime['title']); ?>">
                                <p><?= htmlspecialchars($anime['title']); ?></p>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <!-- Смотрю -->
                <div class="favorites-grid" data-category="watching" style="display: none;">
                    <?php if (empty($watching)): ?>
                        <div class="empty-message">Тут пока ничего нет 😿</div>
                    <?php else: ?>
                        <?php foreach ($watching as $anime): ?>
                            <a href="anime-details?id=<?= htmlspecialchars($anime['shikimori_id']); ?>" class="favorite-anime">
                                <img src="<?= htmlspecialchars($anime['cover_url']); ?>" alt="<?= htmlspecialchars($anime['title']); ?>">
                                <p><?= htmlspecialchars($anime['title']); ?></p>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <!-- Запланировано -->
                <div class="favorites-grid" data-category="planned" style="display: none;">
                    <?php if (empty($planned)): ?>
                        <div class="empty-message">Тут пока ничего нет 😿</div>
                    <?php else: ?>
                        <?php foreach ($planned as $anime): ?>
                            <a href="anime-details?id=<?= htmlspecialchars($anime['shikimori_id']); ?>" class="favorite-anime">
                                <img src="<?= htmlspecialchars($anime['cover_url']); ?>" alt="<?= htmlspecialchars($anime['title']); ?>">
                                <p><?= htmlspecialchars($anime['title']); ?></p>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <!-- Брошено -->
                <div class="favorites-grid" data-category="dropped" style="display: none;">
                    <?php if (empty($dropped)): ?>
                        <div class="empty-message">Тут пока ничего нет 😿</div>
                    <?php else: ?>
                        <?php foreach ($dropped as $anime): ?>
                            <a href="anime-details?id=<?= htmlspecialchars($anime['shikimori_id']); ?>" class="favorite-anime">
                                <img src="<?= htmlspecialchars($anime['cover_url']); ?>" alt="<?= htmlspecialchars($anime['title']); ?>">
                                <p><?= htmlspecialchars($anime['title']); ?></p>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
        </div>
    </main>
 <?php
include 'footer.php';
?>
    <script>
        // Автоматическая отправка формы при выборе файла
        document.getElementById('banner-upload').addEventListener('change', function() {
            this.form.submit();
        });

        document.getElementById('avatar-upload').addEventListener('change', function() {
            this.form.submit();
        });
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const categories = document.querySelectorAll('.anime-category');
        const animeLists = document.querySelectorAll('.favorites-grid');

        categories.forEach(category => {
            category.addEventListener('click', function(event) {
                event.preventDefault();

                categories.forEach(c => c.classList.remove('active'));

                this.classList.add('active');

                animeLists.forEach(list => {
                    list.classList.add('hidden');
                    setTimeout(() => {
                        list.style.display = 'none';
                    }, 500);
                });

                const selectedCategory = this.getAttribute('data-category');
                const targetList = document.querySelector(`.favorites-grid[data-category="${selectedCategory}"]`);
                
                if (targetList) {
                    setTimeout(() => {
                        targetList.style.display = 'grid';
                        targetList.classList.remove('hidden');
                    }, 500);
                }
            });
        });
    });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var modal = document.getElementById('editProfileModal');
            var btn = document.getElementById('editProfileBtn');
            var span = document.getElementsByClassName('close')[0];
            var currentYear = new Date().getFullYear(); // Получаем текущий год

            // Открытие модального окна
            btn.onclick = function() {
                modal.style.display = 'block';
            }

            // Закрытие модального окна кнопкой "Закрыть"
            span.onclick = function() {
                modal.style.display = 'none';
            }

            // Закрытие модального окна по нажатию клавиши Esc
            window.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    modal.style.display = 'none';
                }
            });

            // Отключаем закрытие по клику вне модального окна
            window.onclick = function(event) {
                if (event.target === modal) {
                    event.stopPropagation(); // Отключаем событие
                }
            };

            // Ограничение на ввод только 4 цифр для года и диапазона от 1900 до текущего года при потере фокуса
            const dobInput = document.getElementById('dob');
            dobInput.addEventListener('blur', function() {  // Событие срабатывает, когда поле теряет фокус
                let parts = dobInput.value.split('-'); // Предполагаем, что дата в формате ГГГГ-ММ-ДД
                if (parts.length === 3) {
                    let year = parseInt(parts[0], 10);
                    if (year < 1900) {
                        parts[0] = '1900'; // Если год меньше 1900, устанавливаем 1900
                    } else if (year > currentYear) {
                        parts[0] = currentYear.toString(); // Если год больше текущего, устанавливаем текущий год
                    }
                    dobInput.value = parts.join('-'); // Обновляем поле с ограничением на год
                }
            });

            // Ограничение на ввод только 4 цифр для года
            dobInput.addEventListener('input', function() {
                let parts = dobInput.value.split('-'); // Предполагаем, что дата в формате ГГГГ-ММ-ДД
                if (parts.length === 3 && parts[0].length > 4) { // Ограничиваем год до 4 символов
                    parts[0] = parts[0].slice(0, 4);
                    dobInput.value = parts.join('-');
                }
            });
        });
    </script>
</body>
</html>
