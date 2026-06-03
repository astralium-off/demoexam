<?php
session_start();

// Выход из аккаунта
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

if (!isset($_SESSION['user_id'])) die('Чтобы подать заявку на курсы обучения, необходимо войти в аккаунт.');
$is_admin = isset($_SESSION['admin']) && $_SESSION['admin'];

$success = false;
$error = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Доп. информация при подаче заявки — пишется в request.comment.
    // Отзыв о пройденных курсах хранится отдельно в request.review
    // и заполняется в history.php после статуса «Обучение завершено».
    $comment = $_POST['comment'] ?? '';
    $date = $_POST['date'];            // желаемая дата и время старта занятий
    $venue = $_POST['venue'];          // вид транспорта (Автобус / Электробус / Трамвай)
    $payment = $_POST['payment'];      // способ оплаты
    $status = 'Новая';                 // статус заявки

    // Валидация выбора транспорта
    $valid_transports = ['Автобус', 'Электробус', 'Трамвай'];
    if (!in_array($venue, $valid_transports, true)) {
        $error = true;
        $error_msg = 'Выберите корректный вид транспорта';
    } else {
        include('db.php');

        $user_id = (int)$_SESSION['user_id'];
        $comment = $con->real_escape_string($comment);
        $venue   = $con->real_escape_string($venue);
        $payment = $con->real_escape_string($payment);
        $date    = $con->real_escape_string($date);

        $query = $con->query("INSERT INTO request (comment, date, curses, payment, user_id, status)
                              VALUES ('$comment', '$date', '$venue', '$payment', '$user_id', '$status')");

        if (!$query) {
            $error = true;
            $error_msg = 'Ошибка: ' . $con->error;
        } else {
            $success = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заявка на обучение — Пассажирам.РФ</title>
    <!-- Roboto: современный гротеск (ясность, читаемость) -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="page-create">
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
        <h1>🚌 Заявка на курсы обучения<br>водителя пассажирского транспорта</h1>

        <?php if ($success): ?>
            <div class="success-message">
                ✅ Заявка на обучение успешно отправлена!<br><br>
                <a href="history.php">📋 Перейти к моим заявкам →</a>
                <br><br>
                Администратор проверит заявку и свяжется с вами для согласования группы обучения.
            </div>
        <?php elseif ($error): ?>
            <div class="error-message">
                ❌ Ошибка при подаче заявки: <?php echo htmlspecialchars($error_msg); ?><br>
                <a href="javascript:history.back()">◀ Попробовать снова</a>
            </div>
        <?php endif; ?>

        <?php if (!$success): ?>
        <form method="POST" action="" id="requestForm">

            <label for="venue">🚍 Вид транспорта</label>
            <select id="venue" name="venue" required>
                <option value="Автобус">🚌 Автобус (городские маршруты, ДВС)</option>
                <option value="Электробус">⚡ Электробус (современный электротранспорт)</option>
                <option value="Трамвай">🚊 Трамвай (рельсовый городской транспорт)</option>
            </select>

            <label for="date">📅 Желаемая дата и время старта занятий</label>
            <input id="date" type="datetime-local" name="date" required>
            <span class="hint-text">Укажите предпочтительную дату и время начала курсов обучения</span>

            <label for="payment">💳 Способ оплаты</label>
            <select id="payment" name="payment" required>
                <option value="наличные">💵 Наличные в кассу учебного центра</option>
                <option value="перевод">🏦 Безналичный перевод по счёту</option>
                <option value="карта">💳 Онлайн банковской картой</option>
                <option value="работодатель">🏢 Оплата от работодателя (направление)</option>
            </select>

            <label for="comment">📝 Дополнительная информация</label>
            <textarea id="comment" name="comment" placeholder="Укажите наличие водительских прав, опыт вождения, предпочтительный филиал учебного центра, особые пожелания к расписанию..."></textarea>

            <button type="submit" id="submitBtn">📋 Отправить заявку на обучение</button>
        </form>
        <?php endif; ?>
    </div>

    <script>
        // Анимация загрузки при отправке формы
        const form = document.getElementById('requestForm');
        const submitBtn = document.getElementById('submitBtn');

        if (form) {
            form.addEventListener('submit', function(e) {
                submitBtn.classList.add('loading');
                submitBtn.textContent = 'Отправка заявки';
            });
        }

        // Визуальный эффект при фокусе
        const inputs = document.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.style.transition = 'all 0.2s ease';
            });
        });

        // Минимальная дата — сегодняшняя (для datetime-local)
        const dateInput = document.getElementById('date');
        if (dateInput) {
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const minDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
            dateInput.min = minDateTime;
        }
    </script>
</body>
</html>
