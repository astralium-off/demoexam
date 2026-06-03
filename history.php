<?php
session_start();


if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

if(!isset($_SESSION['user_id'])) die('Чтобы посмотреть историю заявок, необходимо войти в аккаунт.');
$is_admin = isset($_SESSION['admin']) && $_SESSION['admin'];
include('db.php');


if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['review'])) {
    $review = $con->real_escape_string($_POST['review']);
    $user_id = (int)$_SESSION['user_id'];
    $request_id = (int)$_POST['request_id'];
    $con->query("UPDATE request SET review='$review' WHERE id='$request_id' AND user_id='$user_id'");
    echo '<div class="success-message">✓ Отзыв о курсах успешно сохранён!</div>';
}


$user_id = (int)$_SESSION['user_id'];
$query = $con->query("SELECT * FROM request WHERE user_id='$user_id' ORDER BY date DESC");
if(!$query) die('query error: ' . $con->error);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Мои заявки — Пассажирам.РФ</title>
    <!-- Roboto: современный гротеск, отличная читаемость -->
    <link href="https:
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="page-history">
    <header class="header">
        <div class="nav">
            <a href="index.php" class="logo">🚌 Пассажирам.РФ</a>
            <div class="nav-buttons">
                <?php if ($is_admin): ?>
                    <a href="admin.php" class="btn-admin">Панель администратора</a>
                    <a href="?logout=1" class="btn-exit">Выход</a>
                <?php else: ?>
                    <a href="history.php" class="btn-lk">Мои заявки</a>
                    <a href="create.php" class="btn-create">Новая заявка</a>
                    <a href="?logout=1" class="btn-exit">Выход</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <div class="container">
        <h1>📋 Мои заявки на курсы обучения</h1>

        <?php
        $i = 0;
        if($query->num_rows == 0) {
            echo '<div class="empty-state">🚌 У вас пока нет заявок на обучение.<br><br>✍️ <a href="create.php">Подать заявку на курсы водителя автобуса, электробуса или трамвая</a></div>';
        }
        while($request = $query->fetch_assoc()) {
            $i++;

            
            $status_class = 'status-new';
            $status_text = htmlspecialchars($request['status']);
            if($status_text == 'Новая') $status_class = 'status-new';
            elseif($status_text == 'Идет обучение') $status_class = 'status-processing';
            elseif($status_text == 'Обучение завершено') $status_class = 'status-completed';

            
            $venue = htmlspecialchars($request['curses']);
            $venue_icon = '';
            if(strpos($venue, 'Автобус') !== false) $venue_icon = '🚌';
            elseif(strpos($venue, 'Электробус') !== false) $venue_icon = '⚡';
            elseif(strpos($venue, 'Трамвай') !== false) $venue_icon = '🚊';
            else $venue_icon = '🚍';

            echo '
            <div class="request">
                <h2>📄 Заявка #' . $request['id'] . '</h2>
                <p><b>📅 Дата и время старта:</b> ' . htmlspecialchars($request['date']) . '</p>
                <p><b>' . $venue_icon . ' Вид транспорта:</b> ' . $venue . '</p>
                <p><b>💳 Способ оплаты:</b> ' . htmlspecialchars($request['payment']) . '</p>
                <p><b>📊 Статус:</b> <span class="' . $status_class . '">' . $status_text . '</span></p>';

            
            if(!empty($request['comment'])) {
                echo '<div class="comment-text"><b>📝 Доп. информация:</b> ' . htmlspecialchars($request['comment']) . '</div>';
            }

            
            if(!empty($request['review'])) {
                echo '<div class="review-text"><b>⭐ Ваш отзыв:</b> ' . htmlspecialchars($request['review']) . '</div>';
            }

            
            
            
            if($request['status'] === 'Обучение завершено') {
                $review_placeholder = empty($request['review'])
                    ? '✍️ Оставьте отзыв о качестве пройденных курсов обучения...'
                    : '✏️ Обновить отзыв';
                $btn_label = empty($request['review']) ? '⭐ Оставить отзыв' : '💾 Сохранить';
                echo '
                <div class="review-form">
                    <form action="" method="POST">
                        <input type="hidden" name="request_id" value="' . $request['id'] . '">
                        <input type="text" name="review" placeholder="' . $review_placeholder . '" value="' . htmlspecialchars($request['review'] ?? '') . '">
                        <button type="submit">' . $btn_label . '</button>
                    </form>
                </div>';
            }
            echo '</div>';
        }
        ?>

        <div class="create-button">
            <a href="create.php">🚌 Подать новую заявку</a>
        </div>
    </div>
</body>
</html>
