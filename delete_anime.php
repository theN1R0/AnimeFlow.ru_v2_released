<?php
// Стартуем сессию и подключаем базу данных
session_start();
include 'db.php'; // Подключение к базе данных

// Проверяем, авторизован ли пользователь и имеет ли он права администратора
if ($_SESSION['role'] != 'admin') {
    die("У вас нет доступа к этой странице.");
}

// Проверяем, был ли передан shikimori_id
if (isset($_POST['shikimori_id'])) {
    $shikimori_id = intval($_POST['shikimori_id']);

    try {
        // Начало транзакции
        $conn->beginTransaction();

        // Удаление аниме из базы данных
        $stmt = $conn->prepare("DELETE FROM anime WHERE shikimori_id = ?");
        $stmt->execute([$shikimori_id]);

        // Проверяем успешность удаления
        if ($stmt->rowCount() > 0) {
            // Теперь нужно удалить этот shikimori_id из related_anime других аниме
            $stmt_related = $conn->prepare("SELECT id, related_anime FROM anime WHERE related_anime IS NOT NULL");
            $stmt_related->execute();
            $anime_list = $stmt_related->fetchAll(PDO::FETCH_ASSOC);

            foreach ($anime_list as $anime) {
                $related_anime_ids = explode(',', $anime['related_anime']);
                // Если удаляемое аниме есть в списке связанных, удаляем его
                if (in_array($shikimori_id, $related_anime_ids)) {
                    // Удаляем id из массива
                    $updated_related = array_filter($related_anime_ids, function($id) use ($shikimori_id) {
                        return $id != $shikimori_id;
                    });

                    // Преобразуем обратно в строку
                    $updated_related_anime = !empty($updated_related) ? implode(',', $updated_related) : null;

                    // Обновляем поле related_anime
                    $stmt_update = $conn->prepare("UPDATE anime SET related_anime = ? WHERE id = ?");
                    $stmt_update->execute([$updated_related_anime, $anime['id']]);
                }
            }

            // Фиксируем транзакцию
            $conn->commit();

            // Перенаправляем обратно на страницу администратора с сообщением об успешном удалении
            $_SESSION['success_message'] = "Аниме успешно удалено!";
        } else {
            // Перенаправляем обратно на страницу администратора с сообщением об ошибке
            $_SESSION['error_message'] = "Ошибка при удалении аниме!";
        }

    } catch (Exception $e) {
        // Откатываем транзакцию в случае ошибки
        $conn->rollBack();
        $_SESSION['error_message'] = "Произошла ошибка: " . $e->getMessage();
    }

} else {
    $_SESSION['error_message'] = "shikimori_id не был передан!";
}

// Перенаправляем обратно на страницу админа
header("Location: /");
exit;
?>
