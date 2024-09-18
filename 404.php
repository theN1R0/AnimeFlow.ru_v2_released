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
$stmt = $conn->prepare("SELECT * FROM anime WHERE shikimori_id IS NOT NULL ORDER BY RAND() LIMIT 1");
$stmt->execute();
$random_anime = $stmt->fetch(PDO::FETCH_ASSOC);
$avatar = $user['avatar'] ? htmlspecialchars($user['avatar']) : $default_avatar;
$banner = $user['cover_image'] ? htmlspecialchars($user['cover_image']) : $default_banner;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Ваши любимые аниме. Смотрите онлайн бесплатно с полным списком жанров, сезонов и новинок только на AnimeFlow. Откройте мир японской анимации с нами!">
    <meta name="keywords" content="аниме онлайн, смотреть аниме бесплатно, смотреть аниме онлайн бесплатно в хорошем качестве, онлайн бесплатно в хорошем качестве, аниме в хорошем качестве, лучшие аниме, аниме 2024, новые аниме, аниме сериалы, аниме фильмы, аниме без рекламы, аниме жанры, аниме топ, популярные аниме, смотреть аниме HD, аниме онлайн бесплатно, лучшие аниме сериалы, аниме новинки, аниме онлайн 2024, лучшие аниме онлайн, аниме HD, бесплатное аниме, аниме потоковое видео, топ аниме, манга и аниме, аниме без подписки">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Страница не найдена</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>" type="text/css">
    <link rel="stylesheet" href="css/mobile.css?v=<?php echo time(); ?>" type="text/css">
</head>
<body>
    <?php include 'header.php'; ?> 
    <main class="container text-center" style="margin-top: 50px;">
        <img src="img/404.png" alt="404" style="width: 300px; max-width: 100%;">
        <h1 class="main-message">Ничего не найдено</h1>
    </main>
<style>
html, body {
    height: 100%;
    margin: 0;
    padding: 0;
}
body {
    display: flex;
    flex-direction: column;
}
main {
    flex: 1;
}
.main-message {
    color: white;
    font-size: 32px;
    margin-top: 20px;
    text-align: center;
}
footer {
    background-color: #1C1C3A;
    padding: 10px;
    color: white;
    text-align: center;
    position: relative;
    bottom: 0;
    width: 100%;
}
</style>
    <?php include 'footer.php'; ?>
</body>
</html>
