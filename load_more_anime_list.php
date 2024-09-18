<?php
include 'db.php';

// Фильтрация и параметры сортировки
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'title';
$order = isset($_GET['order']) ? $_GET['order'] : 'asc';
$year = isset($_GET['year']) ? $_GET['year'] : null;
$genre = isset($_GET['genre']) ? $_GET['genre'] : null;
$type = isset($_GET['type']) ? $_GET['type'] : null;
$status = isset($_GET['status']) ? $_GET['status'] : null;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 18;
$offset = ($page - 1) * $limit;

// Построение SQL запроса
$query = "SELECT shikimori_id, title, description, cover_url, genres, episodes, rating, type, year, studio, duration FROM anime WHERE 1=1";
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
$query .= " ORDER BY $sort $order LIMIT :limit OFFSET :offset";

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
echo json_encode($anime_list);
?>
