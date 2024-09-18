$(document).ready(function() {
    // Добавление/удаление из избранного
    $('#favorite-btn').on('click', function() {
        var animeId = $('input[name="anime_id"]').val();  // Получаем ID аниме
        var button = $(this);  // Кнопка, на которую нажали
        // Отправляем AJAX-запрос
        $.ajax({
            url: 'add_to_favorites.php',  // URL обработчика на сервере
            type: 'POST',
            data: {
                anime_id: animeId
            },
            success: function(response) {
                // Проверяем, в избранном ли аниме
                if (button.hasClass('favorite-active')) {
                    button.removeClass('favorite-active');
                    button.html('<i class="fa fa-heart-o"></i>');
                } else {
                    button.addClass('favorite-active');
                    button.html('<i class="fa fa-heart"></i>');
                }
            },
            error: function() {
                alert('Произошла ошибка. Попробуйте снова.');
            }
        });
    });

    // Обновление статуса аниме
    $('.status-btn').on('click', function() {
        var status = $(this).attr('id').split('-')[0];  // Получаем статус из ID кнопки
        var animeId = $('input[name="anime_id"]').val();  // Получаем ID аниме
        
        // Отправляем AJAX-запрос на сервер
        $.ajax({
            url: 'update_anime_status.php',
            type: 'POST',
            data: {
                anime_id: animeId,
                status: status
            },
            success: function(response) {
                var data = JSON.parse(response);
                if (data.success) {
                    // Убираем класс активной кнопки у всех статусов
                    $('.status-btn').removeClass('status-active');
                    // Добавляем класс активной кнопке
                    $('#' + status + '-btn').addClass('status-active');
                }
            },
            error: function() {
                alert('Произошла ошибка. Попробуйте снова.');
            }
        });
    });

    // Отправка комментария
    $('#comment-form').on('submit', function(e) {
        e.preventDefault();
        var comment = $('textarea[name="comment"]').val();
        var animeId = $('input[name="anime_id"]').val();
        var avatarUrl = '<?= htmlspecialchars($user['avatar'] ?? "img/avatar.png"); ?>';
        
        if (comment.trim() === "") {
            alert('Комментарий не может быть пустым.');
            return;
        }

        $.ajax({
            url: 'add_comment.php',
            type: 'POST',
            data: { comment: comment, anime_id: animeId },
            success: function(response) {
                var data = JSON.parse(response);
                if (data.success) {
                    var newComment = `
                        <div class="comment" data-comment-id="${data.comment_id}">
                            <div class="comment-avatar">
                                <img src="${avatarUrl}" alt="Avatar" style="width: 50px; height: 50px; border-radius: 50%;">
                            </div>
                            <div class="comment-content">
                                <strong>
                                    <a href="profile.php?id=<?= $_SESSION['user_id']; ?>" style="color: white; text-decoration: none;">
                                        <?= htmlspecialchars($username); ?>
                                    </a>
                                </strong>
                                <p>${data.comment}</p>
                                <span>${data.created_at}</span>
                            </div>
                        </div>
                    `;
                    $('#comments-container').prepend(newComment);
                    $('textarea[name="comment"]').val('');
                }
            }
        });
    });

    // Удаление комментария
    $(document).on('click', '.delete-comment-btn', function() {
        var commentId = $(this).data('comment-id');
        var commentBlock = $(this).closest('.comment');
        
        if (confirm('Вы уверены, что хотите удалить этот комментарий?')) {
            $.ajax({
                url: 'delete_comment.php',
                type: 'POST',
                data: { comment_id: commentId },
                success: function(response) {
                    var data = JSON.parse(response);
                    if (data.success) {
                        // Удаляем комментарий из DOM
                        commentBlock.remove();
                    } else {
                        alert('Ошибка при удалении комментария.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Ошибка AJAX: ', error);
                    alert('Произошла ошибка при удалении комментария. Попробуйте снова.');
                }
            });
        }
    });
});
