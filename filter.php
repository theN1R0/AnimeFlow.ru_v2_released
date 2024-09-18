<?php
include 'db.php';

// Получаем параметры фильтра
$start_year = isset($_GET['start_year']) ? (int)$_GET['start_year'] : null;
$end_year = isset($_GET['end_year']) ? (int)$_GET['end_year'] : null;
$genre = isset($_GET['genre']) ? $_GET['genre'] : null;
$type = isset($_GET['type']) ? $_GET['type'] : null;
$status = isset($_GET['status']) ? $_GET['status'] : null;

// Обратное преобразование жанров, типов и статусов
if ($genre && isset($genreMapping[$genre])) {
    $genre = $genreMapping[$genre];
}

if ($type && isset($typeMapping[$type])) {
    $type = $typeMapping[$type];
}

if ($status && isset($statusMapping[$status])) {
    $status = $statusMapping[$status];
}

// Построение запроса с фильтрами
$query = "SELECT * FROM anime WHERE 1=1";
$params = [];

if ($start_year && $end_year) {
    $query .= " AND year BETWEEN :start_year AND :end_year";
    $params[':start_year'] = $start_year;
    $params[':end_year'] = $end_year;
}

if ($genre) {
    $query .= " AND genres LIKE :genre";
    $params[':genre'] = '%' . $genre . '%';
}

if ($type) {
    $query .= " AND type = :type";
    $params[':type'] = $type;
}

if ($status) {
    $query .= " AND status = :status";
    $params[':status'] = $status;
}

// Выполнение запроса
$stmt = $conn->prepare($query);
$stmt->execute($params);
$anime_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Возвращаем результат в формате JSON
echo json_encode($anime_list);
?>
