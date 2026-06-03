<?php
session_start();

// Выход из системы
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

// Проверяем, установлен ли ключ admin в сессии
$is_admin = isset($_SESSION['admin']) && $_SESSION['admin'];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Пассажирам.РФ — курсы обучения водителей пассажирского транспорта</title>
  <!-- Roboto: современный гротеск, отличная читаемость -->
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
<link rel="icon" type="image/x-icon" href="favicon.ico">
</head>
<body class="page-home">

<header class="header">
  <div class="nav">
    <a href="index.php" class="logo">🚌 Пассажирам.РФ</a>
    <div class="nav-buttons">
      <?php if (!isset($_SESSION['user_id'])): ?>
        <a href="login.php" class="btn-login">Войти</a>
        <a href="register.php" class="btn-register">Регистрация</a>
      <?php elseif ($is_admin): ?>
        <a href="admin.php" class="btn-admin">Панель администратора</a>
        <a href="?logout=1" class="btn-exit">Выход</a>
      <?php elseif (isset($_SESSION['user_id'])): ?>
        <a href="history.php" class="btn-lk">Мои заявки</a>
        <a href="create.php" class="btn-create">Новая заявка</a>
        <a href="?logout=1" class="btn-exit">Выход</a>
      <?php endif; ?>
    </div>
  </div>
</header>

<!-- Слайдер с видами пассажирского транспорта -->
<div class="slideshow-container">
  <div class="mySlides fade">
    <img src="assets/banner1.jpg" alt="Городской автобус">
    <div class="slide-text">🚌 Курсы водителей городского автобуса</div>
  </div>

  <div class="mySlides fade">
    <img src="assets/banner2.jpg" alt="Электробус">
    <div class="slide-text">⚡ Обучение управлению электробусом</div>
  </div>

  <div class="mySlides fade">
    <img src="assets/banner3.jpg" alt="Трамвай">
    <div class="slide-text">🚊 Подготовка водителей трамвая</div>
  </div>

  <div class="mySlides fade">
    <img src="assets/banner4.jpg" alt="Учебный класс">
    <div class="slide-text">🎓 Записывайтесь к нам</div>
  </div>

  <a class="prev" onclick="plusSlides(-1)">❮</a>
  <a class="next" onclick="plusSlides(1)">❯</a>
</div>

<div class="dot-container">
  <span class="dot" onclick="currentSlide(1)"></span>
  <span class="dot" onclick="currentSlide(2)"></span>
  <span class="dot" onclick="currentSlide(3)"></span>
  <span class="dot" onclick="currentSlide(4)"></span>
</div>

<!-- Основной блок: виды транспорта для обучения -->
<section class="features-section">
  <div class="features-title">
    <h1>Выберите вид пассажирского транспорта для обучения</h1>
    <p style="font-size: 18px; margin-top: 12px; color: #5a6268;">Очные курсы вождения автобуса, электробуса и трамвая — подача заявки онлайн</p>
  </div>
  
  <div class="features-grid">
    <div class="feature-card">
      <div class="feature-icon">🚌</div>
      <h3>Автобус</h3>
      <p>Обучение управлению городским автобусом: правила перевозки пассажиров, маршруты, безопасность движения, практическое вождение на учебном автобусе.</p>
      <a href="create.php" class="btn-demo">Подать заявку</a>
    </div>
    
    <div class="feature-card">
      <div class="feature-icon">⚡</div>
      <h3>Электробус</h3>
      <p>Современные технологии: устройство электробуса, особенности зарядки и эксплуатации, эко-движение в городе, практика вождения с инструктором.</p>
      <a href="create.php" class="btn-demo">Подать заявку</a>
    </div>
    
    <div class="feature-card">
      <div class="feature-icon">🚊</div>
      <h3>Трамвай</h3>
      <p>Подготовка водителей трамвая: рельсовый транспорт, схемы движения, безопасность на путях, практическое вождение на учебной линии депо.</p>
      <a href="create.php" class="btn-demo">Подать заявку</a>
    </div>
  </div>
</section>

<!-- Информационная панель -->
<div class="info-banner">
  <div>
    <p><strong>🎯 Запись на курсы обучения водителей</strong><br>Подайте онлайн-заявку: выберите вид транспорта, удобное время старта занятий и способ оплаты — администратор согласует группу обучения.</p>
  </div>
  <div class="badge">Очно • Быстро • Качественно</div>
</div>

<footer class="footer">
  © 2025 Пассажирам.РФ — информационная система записи на курсы обучения водителей пассажирского транспорта города: автобус, электробус, трамвай.
</footer>

<script>
// Слайдер
let slideIndex = 1;
showSlides(slideIndex);

function plusSlides(n) {
  showSlides(slideIndex += n);
}

function currentSlide(n) {
  showSlides(slideIndex = n);
}

function showSlides(n) {
  let slides = document.getElementsByClassName("mySlides");
  let dots = document.getElementsByClassName("dot");

  if (n > slides.length) { slideIndex = 1; }
  if (n < 1) { slideIndex = slides.length; }

  for (let i = 0; i < slides.length; i++) {
    slides[i].style.display = "none";
  }
  for (let i = 0; i < dots.length; i++) {
    dots[i].className = dots[i].className.replace(" active", "");
  }

  if (slides[slideIndex-1]) slides[slideIndex-1].style.display = "block";
  if (dots[slideIndex-1]) dots[slideIndex-1].className += " active";
}

let slideInterval = setInterval(() => plusSlides(1), 4000);

const container = document.querySelector('.slideshow-container');
if (container) {
  container.addEventListener('mouseenter', () => clearInterval(slideInterval));
  container.addEventListener('mouseleave', () => {
    slideInterval = setInterval(() => plusSlides(1), 4000);
  });
}

document.addEventListener('DOMContentLoaded', function() {
  showSlides(slideIndex);
});
</script>
</body>
</html>
