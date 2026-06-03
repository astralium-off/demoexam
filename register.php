<?php
session_start();


include('db.php');


if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['admin']) && $_SESSION['admin']) {
        header('Location: admin.php');
    } else {
        header('Location: create.php');
    }
    exit;
}

$error = false;
$error_message = '';
$success = false;
$form_data = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = trim($_POST['login']);
    $password = $_POST['password'];
    $fullname = trim($_POST['fullname']);
    $birthdate = trim($_POST['birthdate'] ?? '');
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);

    $form_data = compact('login', 'fullname', 'birthdate', 'phone', 'email');

    
    $errors = [];

    if (empty($login)) {
        $errors[] = 'Логин обязателен для заполнения';
    } elseif (!preg_match('/^[a-zA-Z0-9]{6,}$/', $login)) {
        $errors[] = 'Логин должен содержать только латиницу и цифры, минимум 6 символов';
    }

    if (empty($password)) {
        $errors[] = 'Пароль обязателен для заполнения';
    } elseif (strlen($password) < 8) {
        $errors[] = 'Пароль должен содержать минимум 8 символов';
    }

    if (empty($fullname)) {
        $errors[] = 'ФИО обязательно для заполнения';
    } elseif (strlen($fullname) < 5) {
        $errors[] = 'Введите полное ФИО';
    }

    if (empty($birthdate)) {
        $errors[] = 'Дата рождения обязательна для заполнения';
    } else {
        $dt = DateTime::createFromFormat('Y-m-d', $birthdate);
        if (!$dt || $dt->format('Y-m-d') !== $birthdate) {
            $errors[] = 'Введите корректную дату рождения';
        } else {
            $today = new DateTime('today');
            if ($dt > $today) {
                $errors[] = 'Дата рождения не может быть в будущем';
            } else {
                $age = $today->diff($dt)->y;
                if ($age < 18) {
                    $errors[] = 'Для записи на курсы необходимо быть совершеннолетним (18+)';
                }
            }
        }
    }

    if (empty($phone)) {
        $errors[] = 'Телефон обязателен для заполнения';
    } elseif (!preg_match('/^\+7\(\d{3}\)\d{3}-\d{2}-\d{2}$/', $phone)) {
        $errors[] = 'Телефон должен быть в формате +7(XXX)XXX-XX-XX';
    }

    if (empty($email)) {
        $errors[] = 'Email обязателен для заполнения';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Введите корректный email';
    }

    if (empty($errors)) {
        
        $stmt = $con->prepare("SELECT id FROM users WHERE login = ?");
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = true;
            $error_message = 'Пользователь с таким логином уже существует';
            $stmt->close();
        } else {
            $stmt->close();

            
            $stmt2 = $con->prepare("SELECT id FROM users WHERE email = ?");
            $stmt2->bind_param("s", $email);
            $stmt2->execute();
            $result2 = $stmt2->get_result();

            if ($result2->num_rows > 0) {
                $error = true;
                $error_message = 'Пользователь с таким email уже существует';
                $stmt2->close();
            } else {
                $stmt2->close();

                
                $stmt3 = $con->prepare("INSERT INTO users (login, password, fullname, birthdate, phone, email) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt3->bind_param("ssssss", $login, $password, $fullname, $birthdate, $phone, $email);

                if ($stmt3->execute()) {
                    $success = true;
                    header('refresh:2;url=login.php');
                } else {
                    $error = true;
                    $error_message = 'Ошибка при регистрации: ' . $con->error;
                }
                $stmt3->close();
            }
        }
    } else {
        $error = true;
        $error_message = implode('<br>', $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация — Пассажирам.РФ</title>
    <link href="https:
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="page-register">
    <div class="container">
        <div class="logo">
            <h1>🚌 Пассажирам.РФ</h1>
            <p>Курсы обучения водителей пассажирского транспорта</p>
        </div>

        <div class="form-header">
            <h2>Регистрация слушателя</h2>
            <p>Создайте аккаунт, чтобы подать заявку на курсы вождения автобуса, электробуса или трамвая</p>
        </div>

        <?php if ($error): ?>
            <div class="error-message">
                ⚠️ <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success-message">
                ✅ Регистрация успешно завершена!<br>
                <small>Перенаправление на страницу входа...</small>
            </div>
        <?php endif; ?>

        <?php if (!$success): ?>
        <form method="POST" action="" id="registerForm">
            <div class="form-row">
                <div class="form-group">
                    <label for="fullname">
                        <span>👤</span> ФИО
                    </label>
                    <input type="text" id="fullname" name="fullname"
                           value="<?php echo htmlspecialchars($form_data['fullname'] ?? ''); ?>"
                           placeholder="Иванов Иван Иванович" required>
                    <span class="hint">Полное имя слушателя курсов</span>
                </div>

                <div class="form-group">
                    <label for="birthdate">
                        <span>🎂</span> Дата рождения
                    </label>
                    <input type="date" id="birthdate" name="birthdate"
                           value="<?php echo htmlspecialchars($form_data['birthdate'] ?? ''); ?>" required>
                    <span class="hint">К обучению допускаются лица старше 18 лет</span>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="phone">
                        <span>📱</span> Телефон
                    </label>
                    <input type="tel" id="phone" name="phone"
                           value="<?php echo htmlspecialchars($form_data['phone'] ?? ''); ?>"
                           placeholder="+7(XXX)XXX-XX-XX"
                           pattern="\+7\(\d{3}\)\d{3}-\d{2}-\d{2}" required>
                    <span class="hint">Формат: +7(XXX)XXX-XX-XX (для связи с учебным центром)</span>
                </div>

                <div class="form-group">
                    <label for="email">
                        <span>📧</span> Email
                    </label>
                    <input type="email" id="email" name="email"
                           value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>"
                           placeholder="student@passazhiram.ru" required>
                    <span class="hint">На этот адрес придёт подтверждение заявки на обучение</span>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="login">
                        <span>🔑</span> Логин
                    </label>
                    <input type="text" id="login" name="login"
                           value="<?php echo htmlspecialchars($form_data['login'] ?? ''); ?>"
                           placeholder="driver2026"
                           pattern="[a-zA-Z0-9]{6,}" required>
                    <span class="hint">Латиница и цифры, минимум 6 символов</span>
                </div>

                <div class="form-group">
                    <label for="password">
                        <span>🔒</span> Пароль
                    </label>
                    <input type="password" id="password" name="password"
                           placeholder="Минимум 8 символов" minlength="8" required>
                    <span class="hint" id="passwordHint">Пароль должен содержать минимум 8 символов</span>
                </div>
            </div>

            <div class="form-group">
                <label for="confirm_password">
                    <span>✅</span> Подтверждение пароля
                </label>
                <input type="password" id="confirm_password" name="confirm_password"
                       placeholder="Повторите пароль" required>
                <span class="hint" id="confirmHint"></span>
            </div>

            <button type="submit" class="btn-register" id="submitBtn">
                📝 Зарегистрироваться и подать заявку
            </button>
        </form>
        <?php endif; ?>

        <div class="form-footer">
            <p>Уже зарегистрированы? <a href="login.php" class="login-link">Войти в личный кабинет →</a></p>
            <a href="index.php" class="back-home">← На главную (выбор транспорта)</a>
        </div>
    </div>

    <script>
        const form = document.getElementById('registerForm');
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        const confirmHint = document.getElementById('confirmHint');
        const passwordHint = document.getElementById('passwordHint');
        const submitBtn = document.getElementById('submitBtn');

        if (password) {
            password.addEventListener('input', function() {
                const value = this.value;
                if (value.length >= 8) {
                    passwordHint.innerHTML = '✅ Пароль принят';
                    passwordHint.style.color = '#28A745';
                } else {
                    passwordHint.innerHTML = '⚠️ Минимум 8 символов';
                    passwordHint.style.color = '#dc3545';
                }

                if (confirmPassword.value) {
                    checkPasswordsMatch();
                }
            });
        }

        function checkPasswordsMatch() {
            if (password.value === confirmPassword.value && password.value.length >= 8) {
                confirmHint.innerHTML = '✅ Пароли совпадают';
                confirmHint.style.color = '#28A745';
                return true;
            } else if (confirmPassword.value.length > 0) {
                confirmHint.innerHTML = '❌ Пароли не совпадают';
                confirmHint.style.color = '#dc3545';
                return false;
            }
            return false;
        }

        if (confirmPassword) {
            confirmPassword.addEventListener('input', checkPasswordsMatch);
        }

        const phone = document.getElementById('phone');
        if (phone) {
            phone.addEventListener('input', function(e) {
                let value = this.value;
                if (value.length === 1 && value !== '+') {
                    this.value = '+' + value;
                }
            });
        }

        
        const birthdateInput = document.getElementById('birthdate');
        if (birthdateInput) {
            const today = new Date();
            const y = today.getFullYear();
            const m = String(today.getMonth() + 1).padStart(2, '0');
            const d = String(today.getDate()).padStart(2, '0');
            birthdateInput.max = `${y}-${m}-${d}`;
            birthdateInput.min = '1900-01-01';
        }

        if (form) {
            form.addEventListener('submit', function(e) {
                if (password.value !== confirmPassword.value) {
                    e.preventDefault();
                    showInlineError('Пароли не совпадают');
                    confirmPassword.style.borderColor = '#dc3545';
                    return false;
                }

                if (password.value.length < 8) {
                    e.preventDefault();
                    showInlineError('Пароль должен быть не менее 8 символов');
                    password.style.borderColor = '#dc3545';
                    return false;
                }

                const phonePattern = /^\+7\(\d{3}\)\d{3}-\d{2}-\d{2}$/;
                if (!phonePattern.test(phone.value)) {
                    e.preventDefault();
                    showInlineError('Укажите телефон в формате +7(XXX)XXX-XX-XX');
                    phone.style.borderColor = '#dc3545';
                    return false;
                }

                const loginPattern = /^[a-zA-Z0-9]{6,}$/;
                const login = document.getElementById('login');
                if (!loginPattern.test(login.value)) {
                    e.preventDefault();
                    showInlineError('Логин: только латиница и цифры, минимум 6 символов');
                    login.style.borderColor = '#dc3545';
                    return false;
                }

                if (!birthdateInput.value) {
                    e.preventDefault();
                    showInlineError('Укажите дату рождения');
                    birthdateInput.style.borderColor = '#dc3545';
                    return false;
                }

                submitBtn.innerHTML = '⏳ Обработка...';
                submitBtn.disabled = true;
            });
        }

        function showInlineError(message) {
            const existingError = document.querySelector('.error-message');
            if (existingError) {
                existingError.remove();
            }

            const formHeader = document.querySelector('.form-header');
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.innerHTML = `⚠️ ${message}`;
            formHeader.insertAdjacentElement('afterend', errorDiv);

            setTimeout(() => {
                errorDiv.style.opacity = '0';
                setTimeout(() => errorDiv.remove(), 300);
            }, 3000);
        }

        const inputs = document.querySelectorAll('input');
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                this.style.borderColor = '#CED4DA';
            });
        });

        function createDecor() {
            for (let i = 0; i < 8; i++) {
                const decor = document.createElement('div');
                decor.className = 'bg-decor';
                const size = Math.random() * 80 + 40;
                decor.style.width = size + 'px';
                decor.style.height = size + 'px';
                decor.style.left = Math.random() * 100 + '%';
                decor.style.bottom = '-' + size + 'px';
                decor.style.animationDuration = Math.random() * 15 + 10 + 's';
                decor.style.animationDelay = Math.random() * 5 + 's';
                document.body.appendChild(decor);
            }
        }

        createDecor();
    </script>
    <script src="phone.js"></script>
</body>
</html>
