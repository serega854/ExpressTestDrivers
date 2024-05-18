<?php
session_start();
$driver_email = ''; // Инициализация переменной
if (!isset($_SESSION['driver_email'])) {
  header("Location: ../driver/driver_login.php");
  exit();
}
include '../../vendor/connect.php';
include 'get_test_count.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Навигационное меню</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    .navbar-brand {
      padding: 0; /* Убираем внутренние отступы */
      margin: 0; /* Убираем внешние отступы */
    }
    .navbar-brand img {
      width: 45px; /* Ширина изображения */
      height: auto; /* Автоматическая высота */
    }
    .navbar-nav .nav-link {
      font-size: 14px; /* Устанавливаем размер шрифта */
    }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="../driver/driver_index.php">
      <img src="../../img_site/shipping-600x600.png" alt="Иконка"> Программа предрейсовой диагностики
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item">
          <a class="nav-link" href="../driver/driver_profile.php">Профиль: <?= $driver_email ?></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../driver/testing.php">Пройти тестирование
            <?php if ($testCount > 0): ?>
              <span class="badge badge-danger test-count"><?php echo $testCount; ?></span>
            <?php endif; ?>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Мои результаты</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../../vendor/driver_logout.php">Выход</a>
        </li>
      </ul>
    </div>
  </div>
</nav>





</body>
</html>
