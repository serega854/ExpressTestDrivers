<?php
session_start();

// Проверяем, назначено ли тестирование текущему пользователю
$driverID = $_SESSION['driver_id'] ?? null;
$testID = 4; // Идентификатор теста
include '../../vendor/connect.php'; // Подключаемся к базе данных

$sql_check_assignment = "SELECT * FROM AssignedTests WHERE DriverID = $driverID AND TestID = $testID";
$result_assignment = $conn->query($sql_check_assignment);

if ($result_assignment->num_rows == 0) {
    // Если тестирование не назначено текущему пользователю, перенаправляем на главную страницу
    header("Location: ../driver/driver_index.php");
    exit();
}

// Включаем файл только один раз
include_once '../driver/navbar.php';

if (!isset($_SESSION['driver_email'])) {
  header("Location: ../driver/driver_login.php");
  exit();
}

// Получаем данные из POST запроса
$driver_id = $_POST['driver_id'];
$test_title = $_POST['test_title'];
$assignment_id = $_POST['assignment_id'];

// Функция для генерации случайного набора таблицы Шульте
function generateShulteTable()
{
  $table = "<div class='container1 text-center' id='shulteContainer'>";
  $numbers = range(1, 25);
  shuffle($numbers);
  foreach ($numbers as $number) {
    $table .= "<div class='cell1' data-number='$number'>$number</div>";
  }
  $table .= "</div>";
  return $table;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Таблицы Шульте</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    /* Стили для отображения таблицы */
    .container1 {
      display: flex;
      flex-wrap: wrap;
      width: 400px;
      height: 400px;
      margin: auto; /* Центрируем таблицу */
    }

    .cell1 {
      width: 80px;
      height: 80px;
      border: 1px solid black;
      display: flex;
      justify-content: center;
      align-items: center;
      cursor: pointer;
      transition: background-color 0.3s ease-in-out;
    }
  </style>
</head>
<body>
  <div class="container mt-5">
    <h2 class="text-center">Программа "Таблицы Шульте"</h2>
    <p>Как только вы нажмете Начать пойдет таймер от 20 секунд, вам нужно будет успеть за это время расставить как можно больше квадратов в порядке возрастания. Если вы кликаете на квадраты в правильном порядке они загораются зеленым цветом, если кликаете на квадрат не по порядку возрастания он мигает красным цветом</p>
    
    <p>Совет: отдалитесь от экрана и используйте периферийное зрение</p>

    <p>Важно: после кнопки начать поле перегенерируется и сразу запустится таймер</p>
    
    <div class="text-center mb-3">
      <button class="btn btn-primary" id="startButton">Начать</button>
    </div>
    <div id="shulteContainer">
      <?php echo generateShulteTable(); ?>
    </div>
    <div id="timer" class="text-center"></div>
    <div id="result" class="text-center mt-3"></div>
    <div id="exitButton" class="text-center mt-3" style="display: none;">
      <a href="../driver/driver_index.php" class="btn btn-primary">Выход</a>
    </div>
  </div>

  <?php include '../footer.php' ?>;

  <script>
    let startTime;
    let timerInterval;
    let isStarted = false;
    let correctNumbers = 1;
    let clickedNumbers = [];

    // Функция для перемешивания клеток
  function shuffleCells() {
    const shulteContainer = document.getElementById('shulteContainer');
    const cells = shulteContainer.querySelectorAll('.cell1');
    const numbers = Array.from(cells).map(cell => parseInt(cell.getAttribute('data-number')));
    numbers.sort(() => Math.random() - 0.5); // Перемешиваем числа в случайном порядке
    cells.forEach((cell, index) => {
      cell.setAttribute('data-number', numbers[index]); // Обновляем номера в атрибутах клеток
      cell.textContent = numbers[index]; // Обновляем текст клеток (необязательно, но для удобства)
    });
  }

  document.getElementById('startButton').addEventListener('click', function() {
    startTime = Date.now();
    timerInterval = setInterval(updateTimer, 1000);
    document.getElementById('startButton').disabled = true;
    isStarted = true;
    shuffleCells(); // Перемешивание клеток при нажатии кнопки "Начать"
    regenerateTable(); // Перегенерация таблицы при нажатии кнопки "Начать"
  });

    // Функция для перегенерации таблицы Шульте
    function regenerateTable() {
      const shulteContainer = document.getElementById('shulteContainer');
      shulteContainer.innerHTML = generateShulteTable();
      correctNumbers = 1;
      clickedNumbers = [];
    }

    function updateTimer() {
      if (isStarted) {
        const elapsedSeconds = Math.floor((Date.now() - startTime) / 1000);
        const remainingSeconds = 20 - elapsedSeconds;
        document.getElementById('timer').textContent = `Осталось времени: ${remainingSeconds} секунд`;
        if (remainingSeconds <= 0) {
          clearInterval(timerInterval);
          document.getElementById('timer').textContent = 'Время вышло!';
          checkResult();
          document.getElementById('exitButton').style.display = 'block';
          document.querySelectorAll('.cell1').forEach(cell => {
            cell.style.pointerEvents = 'none'; // Блокируем клики на квадраты
            cell.style.backgroundColor = 'gray'; // Устанавливаем серый цвет
          });
          sendClickedNumbersToServer(); // Отправляем данные о кликах на сервер
        }
      }
    }

    // Обработчик клика по квадрату
    document.querySelectorAll('.cell1').forEach(cell => {
      cell.addEventListener('click', function() {
        if (isStarted) {
          handleCellClick(this);
        }
      });
    });

    // Функция обработки клика по квадрату
    function handleCellClick(cell) {
      const selectedNumber = parseInt(cell.getAttribute('data-number'));
      if (!clickedNumbers.includes(selectedNumber)) {
        if (cell.style.backgroundColor !== 'red') { // Добавленная проверка: если квадратик уже красный, игнорировать клик
          if (selectedNumber === correctNumbers) {
            cell.style.backgroundColor = 'green';
            correctNumbers++;
            clickedNumbers.push(selectedNumber);
            if (correctNumbers === 26) {
              clearInterval(timerInterval);
              const endTime = Date.now();
              const timeTaken = (endTime - startTime) / 1000;
              const result = `Вы успели расставить все квадратики за ${timeTaken} секунд`;
              document.getElementById('result').textContent = result;
              document.getElementById('exitButton').style.display = 'block';
              // Здесь можно отправить результаты на сервер
              sendClickedNumbersToServer(); // Отправляем данные о кликах на сервер
            }
          } else {
            cell.style.backgroundColor = 'red';
            setTimeout(() => {
              cell.style.backgroundColor = '';
            }, 1000);
          }
        }
      }
    }

    // Функция проверки результата
    function checkResult() {
      const clickedCount = clickedNumbers.length;
      const result = `Количество расставленных квадратиков: ${clickedCount}`;
      document.getElementById('result').textContent = result;
    }

    // Отправляем данные о кликах на сервер
// Отправляем данные о кликах на сервер
function sendClickedNumbersToServer() {
    const formData = new FormData();
    formData.append('clickedNumbers', JSON.stringify(clickedNumbers));

    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(text => console.log(text))
    .catch(error => console.error('Ошибка отправки данных:', error));
}

  </script>
</body>
</html>

<?php
// Добавляем результаты тестирования в базу данных
if (isset($_SESSION['driver_id']) && isset($_POST['clickedNumbers']) && is_array($_POST['clickedNumbers'])) {
    $driverID = $_SESSION['driver_id'];
    $dateTimeCompleted = date('Y-m-d H:i:s');
    $count = sizeof($_POST['clickedNumbers']); // Используем sizeof(), который возвращает количество элементов в массиве, или 0, если массив пуст
    $sql_insert_result = "INSERT INTO Passed_Shulte_Table (TestID, DriverID, DateTimeCompleted, Count) VALUES ($testID, $driverID, '$dateTimeCompleted', $count)";
    $conn->query($sql_insert_result);
}
?>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clickedNumbers = json_decode($_POST['clickedNumbers'], true);

    // Проверка наличия данных и выполнение необходимых действий (например, сохранение в базу данных)
    if (!empty($clickedNumbers)) {
        session_start();
        include '../../vendor/connect.php'; // Подключаемся к базе данных

        // Добавляем результаты тестирования в базу данных
        if (isset($_SESSION['driver_id'])) {
            $driverID = $_SESSION['driver_id'];
            $dateTimeCompleted = date('Y-m-d H:i:s');
            $count = count($clickedNumbers); // Получаем количество кликов
            $testID = 4; // Идентификатор теста

            $sql_insert_result = "INSERT INTO Passed_Shulte_Table (TestID, DriverID, DateTimeCompleted, Count) VALUES ($testID, $driverID, '$dateTimeCompleted', $count)";
            $conn->query($sql_insert_result);
            echo "Данные успешно сохранены";
            // Выполнение SQL-запроса для удаления записи из базы данных
         

            $delete_sql = "DELETE FROM AssignedTests WHERE DriverID = $driverID AND TestID = $testID";
          if ($conn->query($delete_sql) === true) {
              // Успешно удалено
          } else {
              echo "Ошибка при удалении записи: " . $conn->error;
          }
        }
    } 
}

?>
