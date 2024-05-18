<?php
session_start();

// Проверяем, назначено ли тестирование текущему пользователю
$driverID = $_SESSION['driver_id'] ?? null;
$testID = 7; // Идентификатор теста
include '../../vendor/connect.php'; // Подключаемся к базе данных

$sql_check_assignment = "SELECT * FROM AssignedTests WHERE DriverID = $driverID AND TestID = $testID";
$result_assignment = $conn->query($sql_check_assignment);

if ($result_assignment->num_rows == 0) {
    // Если тестирование не назначено текущему пользователю, перенаправляем на главную страницу
    header("Location: ../driver/driver_index.php");
    exit();
}

include '../driver/navbar.php';
include '../footer.php';


// Подключаемся к базе данных
include '../../vendor/connect.php';
// Получаем идентификатор водителя из сессии
$driver_id = $_SESSION['driver_id'];

// Функция для вставки данных результатов опросника в базу данных
function saveResultsToDB($driverID, $testID, $samochuvstvie, $aktivnost, $nastroenie)
{
    global $conn;

    $dateTimeCompleted = date("Y-m-d H:i:s");

    $sql = "INSERT INTO Passed_SAN (TestID, DriverID, DateTimeCompleted, SelfPerception, Activity, Mood)
            VALUES ('$testID', '$driverID', '$dateTimeCompleted', '$samochuvstvie', '$aktivnost', '$nastroenie')";

    if ($conn->query($sql) === TRUE) {
        echo "Результаты успешно сохранены в базе данных.";
    } else {
        echo "Ошибка: " . $sql . "<br>" . $conn->error;
    }



}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['samochuvstvie']) && isset($_POST['aktivnost']) && isset($_POST['nastroenie'])) {
        $samochuvstvie = $_POST['samochuvstvie'];
        $aktivnost = $_POST['aktivnost'];
        $nastroenie = $_POST['nastroenie'];

        saveResultsToDB($driverID, $testID, $samochuvstvie, $aktivnost, $nastroenie);

        // Удаляем запись из таблицы AssignedTests
        $sql_delete_assignment = "DELETE FROM AssignedTests WHERE DriverID = $driverID AND TestID = $testID";
        if ($conn->query($sql_delete_assignment) === TRUE) {
            echo "Запись в таблице AssignedTests успешно удалена.";
        } else {
            echo "Ошибка при удалении записи из таблицы AssignedTests: " . $conn->error;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Таблица оценок</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<style>
.spacer {
visibility: hidden;
}
.unanswered {
background-color: #ffcccc; /* Цвет фона для выделения невыбранных строк */
}
input[type="radio"] {
    transform: scale(1.9); /* Увеличиваем размер радио-кнопок */
}

body{
    height: 200vh;
}

.results{
    margin-bottom: 100px;
}
</style>
</head>
<body>
<div class="container">
<h2>Опросник сан</h2>
<p>Вам предлагается описать свое состояние в настоящий момент, с помощью опросника, состоящего из 30 полярных признаков. Вы должны в каждой паре выбрать ту характеристику, которая наиболее точно описывает ваше состояние и отметить цифру, которая соответствует степени (силе) выраженности данной характеристики. </p>

<table class="table table-bordered">
<thead>
<tr>
<th></th>
<th>3</th>
<th>2</th>
<th>1</th>
<th>0</th>
<th>1</th>
<th>2</th>
<th>3</th>
<th></th>
</tr>
</thead>
<tbody>
<?php
$qualities = array(
1 => "Самочувствие хорошее",
2 => "Чувствую себя сильным",
3 => "Активный",
4 => "Подвижный",
5 => "Веселый",
6 => "Хорошее настроение",
7 => "Работоспособный",
8 => "Полный сил",
9 => "Быстрый",
10 => "Деятельный",
11 => "Счастливый",
12 => "Жизнерадостный",
13 => "Расслабленный",
14 => "Здоровый",
15 => "Увлеченный",
16 => "Взволнованный",
17 => "Восторженный",
18 => "Радостный",
19 => "Отдохнувший",
20 => "Свежий",
21 => "Возбужденный",
22 => "Желание работать",
23 => "Спокойный",
24 => "Оптимистичный",
25 => "Выносливый",
26 => "Бодрый",
27 => "Соображать легко",
28 => "Внимательный",
29 => "Полный надежд",
30 => "Довольный"
);

$negative_qualities = array(
1 => "Самочувствие плохое",
2 => "Чувствую себя слабым",
3 => "Пассивный",
4 => "Малоподвижный",
5 => "Грустный",
6 => "Плохое настроение",
7 => "Разбитый",
8 => "Обессиленный",
9 => "Медлительный",
10 => "Бездеятельный",
11 => "Несчастный",
12 => "Мрачный",
13 => "Напряженный",
14 => "Больной",
15 => "Безучастный",
16 => "Равнодушный",
17 => "Унылый",
18 => "Печальный",
19 => "Усталый",
20 => "Изнуренный",
21 => "Сонливый",
22 => "Желание отдохнуть",
23 => "Спокойный",
24 => "Озабоченный",
25 => "Выносливый",
26 => "Бодрый",
27 => "Соображать трудно",
28 => "Рассеянный",
29 => "Разочарованный",
30 => "Недовольный"
);

foreach ($qualities as $key => $value) {
echo "<tr>";
echo "<td>$value</td>";
for ($i = 3; $i >= -3; $i--) {
$score = abs($i - 3) + 1;
echo "<td><input type='radio' name='rating_$key' value='$score'></td>";
}
echo "<td>{$negative_qualities[$key]}</td>";
echo "</tr>";
}
?>
</tbody>
</table>
<button id="calculateButton" class="btn btn-primary">Рассчитать результаты</button>
<div id="results" style="display: none;">
<h3>Результаты:</h3>
<p>Самочувствие: <span id="samochuvstvieResult"></span></p>
<p>Активность: <span id="aktivnostResult"></span></p>
<p>Настроение: <span id="nastroenieResult"></span></p>
</div>
</div>
<script>
document.getElementById('calculateButton').addEventListener('click', function () {
    var radios = document.querySelectorAll('input[type="radio"]');
    var allAnswered = true;

    radios.forEach(function (radio) {
        var rowRadios = document.querySelectorAll('input[type="radio"][name="' + radio.name + '"]');
        var answered = false;
        rowRadios.forEach(function (rowRadio) {
            if (rowRadio.checked) {
                answered = true;
            }
        });

        if (!answered) {
            rowRadios.forEach(function (rowRadio) {
                rowRadio.parentNode.parentNode.classList.add('unanswered'); // Add class to whole row
            });
            allAnswered = false;
        } else {
            rowRadios.forEach(function (rowRadio) {
                rowRadio.parentNode.parentNode.classList.remove('unanswered'); // Remove class from whole row
            });
        }
    });

    if (!allAnswered) {
        alert("Пожалуйста, ответьте на все вопросы!");
        return;
    }

    var samochuvstvieResults = { count: 0, sum: 0 };
    var aktivnostResults = { count: 0, sum: 0 };
    var nastroenieResults = { count: 0, sum: 0 };

    radios.forEach(function(radio) {
        if (radio.checked) {
            var value = parseInt(radio.value);
            var questionNumber = parseInt(radio.name.split('_')[1]);
            if ([1, 2, 7, 8, 13, 14, 19, 20, 25, 26].includes(questionNumber)) {
                samochuvstvieResults.count++;
                samochuvstvieResults.sum += value;
            } else if ([3, 4, 9, 10, 15, 16, 21, 22, 27, 28].includes(questionNumber)) {
                aktivnostResults.count++;
                aktivnostResults.sum += value;
            } else if ([5, 6, 11, 12, 17, 18, 23, 24, 29, 30].includes(questionNumber)) {
                nastroenieResults.count++;
                nastroenieResults.sum += value;
            }
        }
    });

    var samochuvstvieAverage = samochuvstvieResults.count > 0 ? (8 - (samochuvstvieResults.sum / samochuvstvieResults.count)).toFixed(2) : "Нет данных";
    var aktivnostAverage = aktivnostResults.count > 0 ? (8 - (aktivnostResults.sum / aktivnostResults.count)).toFixed(2) : "Нет данных";
    var nastroenieAverage = nastroenieResults.count > 0 ? (8 - (nastroenieResults.sum / nastroenieResults.count)).toFixed(2) : "Нет данных";

    document.getElementById('samochuvstvieResult').innerText = samochuvstvieAverage;
    document.getElementById('aktivnostResult').innerText = aktivnostAverage;
    document.getElementById('nastroenieResult').innerText = nastroenieAverage;
    document.getElementById('calculateButton').disabled = true;
    document.getElementById('results').style.display = 'block';

    // Сохранение результатов в базу данных
    saveResults(samochuvstvieAverage, aktivnostAverage, nastroenieAverage);
});

// Функция для сохранения результатов опросника в базу данных
function saveResults(samochuvstvie, aktivnost, nastroenie) {
    var dateTimeCompleted = new Date().toISOString(); // Текущая дата и время

    // Отправка данных на сервер
    $.ajax({
        url: window.location.href, // Используем текущий URL как путь к обработчику
        type: 'POST',
        data: {
            samochuvstvie: samochuvstvie,
            aktivnost: aktivnost,
            nastroenie: nastroenie
        },
        success: function(response) {
            console.log(response); // Выводим ответ сервера в консоль

            
        },
        error: function(xhr, status, error) {
            console.error('Ошибка сохранения результатов:', error); // Выводим ошибку в консоль
        }
    });
}
</script>

</body>
</html>
