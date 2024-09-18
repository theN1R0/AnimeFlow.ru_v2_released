<?php
$host = 'localhost';
$dbname = 'animeflow_site';
$username = 'animeflow_s_usr';
$password = 'Rogovoy007';


try {
    // Подключение к базе данных через PDO
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // Установка атрибутов PDO для обработки ошибок
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Проверка успешного подключения, временный отладочный вывод
    // echo "Подключение к базе данных установлено!";
    
} catch (PDOException $e) {
    // Выводим сообщение об ошибке при подключении
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

// Включаем отображение ошибок для отладки
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Проверка соединения
if (!$conn) {
    die("Ошибка подключения к базе данных");
} else {
    // Убедитесь, что этот вывод временный, и уберите его, когда все заработает
    // echo "Соединение успешно!";
}
?>
