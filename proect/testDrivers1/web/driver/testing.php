<?php 
include 'navbar.php';
include '../../vendor/auto-clear-test.php';
session_start(); 
// Проверяем, авторизован ли водитель 
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
if ($result === false) { 
  die("Ошибка SQL: " . $conn->error); 
}
$row = $result->fetch_assoc(); 
$driver_id = $row['DriverID']; 
// Получаем данные о назначенных тестированиях для данного водителя 
$sql = "SELECT at.AssignmentID, at.DateEndAssigned, t.Title, t.img
        FROM AssignedTests at
        INNER JOIN Tests t ON at.TestID = t.TestID
        WHERE at.DriverID='$driver_id'";
$result = $conn->query($sql); 
if ($result === false) { 
  die("Ошибка SQL: " . $conn->error); 
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Назначенные тестирования</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

  <style>
    /* Стили для центрирования изображения и текста */
    .centered {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }
    .message {
      font-weight: bold;
      font-size: 1.5em;
      text-align: center;
      margin-top: 20px;
    }
  </style>
</head>
<body>
  <div class="container mt-5">
    
    <?php 
    if ($result->num_rows > 0) { ?>
    <h2>Назначенные тестирования</h2>
    <table class="table">
        <thead>
          <tr>
            <th></th>
            <th>Название теста</th>
            <th>Оставшееся время</th>
          </tr>
        </thead>
        <tbody id="assignedTestsTable">
          <?php 
          while ($row = $result->fetch_assoc()) {
            $endTimestamp = strtotime($row['DateEndAssigned']);
            $currentTimestamp = time();
            $remainingTime = $endTimestamp - $currentTimestamp;
            $title = $row['Title'];
            $img = $row['img']; // получаем ссылку на изображение
            $assignmentId = $row['AssignmentID']; // Получаем ID назначения
            
            // Добавляем кнопку "Начать" и привязываем обработчик события
            //echo "<td class='align-middle'><button class='btn btn-warning start-test-btn' data-title='$title'>Начать</button></td>";
            
            
            
            echo "<tr data-assignment-id='$assignmentId' data-remaining-time='$remainingTime'>";
            echo "<td class='align-middle'><img src='../../img_tests/$img' alt='$title' style='max-width: 100px;'></td>"; // выводим изображение
            echo "<td class='align-middle'>$title</td>";
            echo "<td class='align-middle'><span class='remaining-time'></span></td>";

            // Формируем путь к файлу теста в зависимости от названия теста
            $action = '';
            switch ($title) {
                case 'Тест пилотов':
                    $action = '../tests/Pilot.php';
                    break;
                case 'Светофор':
                    $action = '../tests/Svetofor.php';
                    break;
                case 'Таблицы Шульте':
                    $action = '../tests/Shulte.php';
                    break;
                case 'Кистевая динамометрия':
                  $action = '../tests/Dinamomeria.php';
                  break;
                case 'Опросник сан':
                  $action = '../tests/San.php';
                  break;
                
                case 'Тест Баевского':
                  $action = '../tests/Baevskiy.php';
                  break;
                case 'Пульсоксиметрия':
                  $action = '../tests/Pulsoksimetr.php';
                  break;
                case 'Тонометр':
                  $action = '../tests/Tonomentr.php';
                  break;

            }
            echo "<td class='align-middle'>
            <form method='POST' action='$action' onsubmit=\"return confirmStartTest('$title')\"> <!-- Форма для страницы теста -->
              <input type='hidden' name='driver_id' value='$driver_id'> <!-- Скрытое поле с ID пользователя -->
              <input type='hidden' name='test_title' value='$title'> <!-- Скрытое поле с названием теста -->
              <input type='hidden' name='assignment_id' value='$assignmentId'>
              <button type='submit' class='btn btn-warning' name='start_test'>Начать</button> <!-- Кнопка отправки формы -->
            </form>
          </td>";
    
            echo "</tr>";

            
        }
        ?>

        </tbody>
      </table>
    <?php } else { ?>
      <div class="centered">
        <img src="../../img_site/i (1).webp" alt="нет назначенных тестирований">
        <div class="message">Нет назначенных тестирований</div>
        Попробуйте обновить страницу
      </div>
    <?php } ?>
  </div>

  <?php include '../footer.php'?>;

  <script>
  function updateRemainingTime() {
    var assignedTests = document.querySelectorAll('#assignedTestsTable tr');
    assignedTests.forEach(function(test) {
      var remainingTime = parseInt(test.getAttribute('data-remaining-time'));

      if (remainingTime > 0) {
        remainingTime--;
        test.setAttribute('data-remaining-time', remainingTime);

        // Обновляем отображаемое время
        var hours = Math.floor(remainingTime / 3600);
        var minutes = Math.floor((remainingTime % 3600) / 60);
        var seconds = remainingTime % 60;
        test.querySelector('.remaining-time').textContent = hours + " часов, " + minutes + " минут, " + seconds + " секунд";
      } else { // Если время истекло, удаляем строку из таблицы
        test.remove();
        
        // Выполняем асинхронный запрос для обновления счётчика тестов
        fetch('get_test_count.php')
          .then(response => response.text())
          .then(data => {
            // Обновляем счётчик тестов
            $('.test-count').text(data);
          })
          .catch(error => console.error('Ошибка при выполнении запроса:', error));
      }
    });
  }

  setInterval(updateRemainingTime, 1000);

  //вы уверены что хотите начать тестирование
  function confirmStartTest(title) {
    return confirm("Вы точно уверены, что хотите начать тест: " + title + ". После начала, тестирование нельзя будет отменить");
}

</script>

</body>
</html>
