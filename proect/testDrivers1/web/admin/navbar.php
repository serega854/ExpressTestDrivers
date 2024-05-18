<?php
session_start();

if (!isset($_SESSION['admin_username'])) {
  header("Location: ../admin/admin_login.php");
  exit();
}

if (isset($_SESSION['admin_username'])) {
    include '../../vendor/connect.php';
    $admin_username = $_SESSION['admin_username'];
    $admin_name = '';

    // Получаем логин админа из базы данных
    $sql = "SELECT Name FROM Administrators WHERE Name='$admin_username'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $admin_name = $row["Name"];
    } else {
        // Если логин админа не найден в базе данных, используем логин из сессии
        $admin_name = $admin_username;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Навигационное меню</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<style>
 
    .navbar-brand {
        padding: 0; /* Убираем внутренние отступы */
        margin: 0; /* Убираем внешние отступы */
    }

    .navbar-brand img {
        width: 45px; /* Ширина изображения */
        height: auto; /* Автоматическая высота */
    }
  </style>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
  <a class="navbar-brand" href="admin_index.php">
      <img src="../../img_site/shipping-600x600.png" alt="Иконка"> Программа предрейсовой диагностики
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ml-auto">
        <?php if (!empty($admin_name)) { ?>
            <li class="nav-item">
                <a class="nav-link" href="#">Профиль: <?= $admin_name ?></a>
            </li>
        <?php } ?>
        <li class="nav-item">
          <a class="nav-link" href="#">Тестирования</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../../vendor/admin_logout.php">Выход</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
