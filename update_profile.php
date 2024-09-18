<?php
session_start();
include 'db.php'; // Подключение к базе данных

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    die('Вы не авторизованы.');
}

$user_id = $_SESSION['user_id'];

// Ограничение на размер файла (в байтах), например, 5 МБ
$maxFileSize = 5 * 1024 * 1024; // 5 MB

// Получаем текущие пути к аватару и баннеру из базы данных
$stmt = $conn->prepare("SELECT avatar, cover_image FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user_data = $stmt->fetch(PDO::FETCH_ASSOC);

$current_avatar = $user_data['avatar'];
$current_banner = $user_data['cover_image'];

// Получаем данные из формы
$first_name = htmlspecialchars($_POST['first_name']);
$last_name = htmlspecialchars($_POST['last_name']);
$dob = htmlspecialchars($_POST['dob']);
$country = htmlspecialchars($_POST['country']);
$city = htmlspecialchars($_POST['city']);
$gender = htmlspecialchars($_POST['gender']);
$status = htmlspecialchars($_POST['status']);

// Обновляем данные пользователя в базе данных
$stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, dob = ?, country = ?, city = ?, gender = ?, status = ? WHERE id = ?");
$stmt->execute([$first_name, $last_name, $dob, $country, $city, $gender, $status, $user_id]);

// Проверяем, был ли загружен аватар
if (isset($_FILES['avatar_image']) && $_FILES['avatar_image']['error'] === UPLOAD_ERR_OK) {
    // Проверка размера файла
    if ($_FILES['avatar_image']['size'] > $maxFileSize) {
        echo "<script>alert('Файл аватара слишком большой. Максимальный размер файла: 5 МБ.'); window.location.href = 'profile?id=$user_id';</script>";
        exit();
    }

    $avatar_tmp_path = $_FILES['avatar_image']['tmp_name'];
    $avatar_name = 'avatar_' . $user_id . '_' . basename($_FILES['avatar_image']['name']);
    $avatar_destination = 'img/avatars/' . $avatar_name;

    // Удаляем старый аватар, если он существует и не является стандартным
    if ($current_avatar && $current_avatar !== 'img/avatar.png' && file_exists($current_avatar)) {
        unlink($current_avatar);
    }

    // Перемещаем загруженный файл
    if (move_uploaded_file($avatar_tmp_path, $avatar_destination)) {
        // Обновляем путь к аватару в базе данных
        $stmt = $conn->prepare("UPDATE users SET avatar = ? WHERE id = ?");
        $stmt->execute([$avatar_destination, $user_id]);
    } else {
        echo "<script>alert('Не удалось сохранить аватар.'); window.location.href = 'profile?id=$user_id';</script>";
        exit();
    }
}

// Проверяем, был ли загружен баннер
if (isset($_FILES['banner_image']) && $_FILES['banner_image']['error'] === UPLOAD_ERR_OK) {
    // Проверка размера файла
    if ($_FILES['banner_image']['size'] > $maxFileSize) {
        echo "<script>alert('Файл баннера слишком большой. Максимальный размер файла: 5 МБ.'); window.location.href = 'profile?id=$user_id';</script>";
        exit();
    }

    $banner_tmp_path = $_FILES['banner_image']['tmp_name'];
    $banner_name = 'banner_' . $user_id . '_' . basename($_FILES['banner_image']['name']);
    $banner_destination = 'img/banners/' . $banner_name;

    // Удаляем старый баннер, если он существует и не является стандартным
    if ($current_banner && $current_banner !== 'img/background2.png' && file_exists($current_banner)) {
        unlink($current_banner);
    }

    // Перемещаем загруженный файл
    if (move_uploaded_file($banner_tmp_path, $banner_destination)) {
        // Обновляем путь к баннеру в базе данных
        $stmt = $conn->prepare("UPDATE users SET cover_image = ? WHERE id = ?");
        $stmt->execute([$banner_destination, $user_id]);
    } else {
        echo "<script>alert('Не удалось сохранить баннер.'); window.location.href = 'profile?id=$user_id';</script>";
        exit();
    }
}

// Перенаправляем обратно на страницу профиля
header('Location: profile?id=' . $user_id);
exit();
