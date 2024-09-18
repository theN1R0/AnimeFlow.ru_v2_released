<?php
session_start();
include 'db.php'; // Подключение к базе данных

// Убедимся, что пользователь — администратор
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Недостаточно прав']);
    exit;
}

// Проверяем, был ли передан ID аниме
if (!isset($_POST['anime_id'])) {
    echo json_encode(['success' => false, 'message' => 'ID аниме не передан']);
    exit;
}

$anime_id = intval($_POST['anime_id']);

// Найдем, в каких аниме наш ID содержится в списке related_anime
$stmt = $conn->prepare("SELECT shikimori_id, related_anime FROM anime WHERE FIND_IN_SET(?, related_anime)");
$stmt->execute([$anime_id]);
$related_anime_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($related_anime_data) {
    // Обработаем все аниме, в которых наш ID находится в related_anime
    $our_related_anime = [];
    foreach ($related_anime_data as $related_anime) {
        $shikimori_id = $related_anime['shikimori_id'];
        $related_anime_list = explode(',', $related_anime['related_anime']);

        // Уберем наш ID из related_anime у другого аниме
        $new_related_anime_list = array_filter($related_anime_list, function($id) use ($anime_id) {
            return $id != $anime_id;
        });
        $new_related_anime = implode(',', $new_related_anime_list);

        // Обновим related_anime у другого аниме, исключив наш ID
        $stmt_update = $conn->prepare("UPDATE anime SET related_anime = ? WHERE shikimori_id = ?");
        $stmt_update->execute([$new_related_anime, $shikimori_id]);

        // Добавим это аниме в наш список связанных
        $our_related_anime[] = $shikimori_id;
    }

    // Теперь обновим related_anime для нашего аниме
    if (!empty($our_related_anime)) {
        $our_related_anime_str = implode(',', $our_related_anime);
        $stmt_update_our = $conn->prepare("UPDATE anime SET related_anime = ? WHERE shikimori_id = ?");
        $stmt_update_our->execute([$our_related_anime_str, $anime_id]);
    }

    echo json_encode(['success' => true, 'message' => 'Связанные аниме успешно обновлены']);
} else {
    echo json_encode(['success' => false, 'message' => 'Наш ID не найден в других связанных аниме']);
}
?>
