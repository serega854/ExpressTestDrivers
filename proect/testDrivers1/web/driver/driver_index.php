

<?php include 'navbar.php' ?>
<?php 

session_start(); 

if (!isset($_SESSION['driver_email'])) { 
  header("Location: driver_login.php"); 
  exit(); 
} 
// Подключаемся к базе данных 
include('../../vendor/connect.php'); 
// Получаем ID водителя из сессии 
$driver_email = $_SESSION['driver_email']; 
$sql = "SELECT DriverID FROM Drivers WHERE Email='$driver_email'"; 
$result = $conn->query($sql); 
if (!$result) { 
  die("Ошибка SQL: " . $conn->error); 
} 
$row = $result->fetch_assoc(); 
$driver_id = $row['DriverID']; 
// Получаем данные о назначенных тестированиях для данного водителя 
$sql = "SELECT at.TestID, at.DateAssigned, at.DateEndAssigned, at.IsCompleted, t.Title, t.img
        FROM AssignedTests at
        INNER JOIN Tests t ON at.TestID = t.TestID
        WHERE at.DriverID='$driver_id'";

$result = $conn->query($sql); 
if (!$result) { 
  die("Ошибка SQL: " . $conn->error); 
} 
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Программа предрейсовой диагностики</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .content {
            margin-left: 5px; /* Уменьшенный сдвиг контента влево */
            margin-bottom: 500px; /* Отступ снизу */
        }
        .rounded-img {
            border-radius: 10px; /* Закругление углов у изображения */
            max-width: 100px; /* Уменьшение максимальной ширины изображения */
        }
        .highlight {
            background-color: #ffffff; /* Цвет фона для карточки */
            padding: 20px; /* Поля вокруг карточки */
            border-radius: 10px; /* Закругление углов карточки */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Тень карточки */
            margin-top: 20px; /* Отступ сверху карточки */
            display: flex; /* Использовать flexbox */
            align-items: center; /* Выравнивать элементы по вертикали */
            text-align: left; /* Выравниваем текст по левому краю */
            transition: all 0.3s ease; /* Плавное изменение при наведении */
        }
        .highlight img {
            margin-right: 20px; /* Отступ справа для изображения */
        }
        .highlight .highlight-text {
            max-width: calc(100% - 120px); /* Ограничение ширины текста внутри карточки */
        }
        .header-text {
            font-size: 24px; /* Размер шрифта для заголовка */
            margin-bottom: 10px; /* Отступ снизу заголовка */
        }
        body {
            background-image: url('img_site/66145c30a85ef_1712610387_66145c30a85c7 (3) (1).png');
            background-size: cover;
            background-position: bottom center;
            background-attachment: fixed; /* Фиксируем изображение */
        }

      .highlight {
    
             height: 200px; /* Вы можете изменить значение высоты по вашему усмотрению */
        }

/* Медиа-запрос для изменения расположения карточек на один столбик при ширине экрана менее 1000 пикселей */
@media (max-width: 1000px) {
    .highlight {
        flex-direction: column; /* Расположение карточек одна под другой */
        justify-content: center; /* Выравнивание по центру по вертикали */
        align-items: flex-start; /* Выравнивание элементов по верхнему краю */
        height: 400px; /* Высота карточек */
    }
}

@media (max-width: 767px) {
    .highlight {
        flex-direction: row; /* Возвращаем расположение карточек в строку */
        justify-content: flex-start; /* Выравнивание элементов по левому краю */
        align-items: center; /* Выравнивание по центру по вертикали */
        height: auto; /* Автоматическая высота для карточек */
    }
}

p{
    font-size: 20px;
}

/* Стили для увеличения карточек при наведении и отбрасывания тени */
.highlight:hover {
    transform: scale(1.05); /* Увеличение карточки */
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.2); /* Увеличение тени при наведении */
}


.content1{
    margin-bottom: 200px;
}

    </style>
</head>
<body>
    
<div class="container mt-3">
        <div class="col-md-12 text-center header-text">
            <img src="../../img_site/shipping-600x600.png" alt="Иконка" height="65">
            <span style="font-size:32px; font-weight: bold">Программа предрейсовой диагностики!</span>
            <p>Мы предоставляем логистическим компаниям сервис для экспресс тестирования водителей, проверяя их психоэмоциональное и физическое состояние за 15 минут перед выходом на рейс</p>
        </div>
        <div class="content1 row mt-3">
            <div class="col-md-6">
                <div class="highlight">
                    <img src="../../img_site/2.png" alt="Изображение" class="img-fluid rounded-img">
                    <div class="highlight-text">
                        <h2>Экономическая эффективность</h2>
                        <p>Более эффективное использование экономических ресурсов</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="highlight">
                    <img src="../../img_site/3.png" alt="Изображение" class="img-fluid rounded-img">
                    <div class="highlight-text">
                        <h2>Повышение безопасности</h2>
                        <p>Выявление отклонений у водителей на раннем этапе</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="highlight">
                    <img src="../../img_site/1.png" alt="Изображение" class="img-fluid rounded-img">
                    <div class="highlight-text">
                        <h2>Повышение репутации</h2>
                        <p>Безаварийность улучшит отношение клиентов к вашему сервису</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="highlight">
                    <img src="../../img_site/5.png" alt="Изображение" class="img-fluid rounded-img">
                    <div class="highlight-text">
                        <h2>Четкий контроль</h2>
                        <p>Контроль каждого сотрудника, а также понимание состояния среднестатистического сотрудника</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include '../footer.php'?>;
    
</body>
</html>


