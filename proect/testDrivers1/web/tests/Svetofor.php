<?php
session_start();

// Проверяем, назначено ли тестирование текущему пользователю
$driverID = $_SESSION['driver_id'] ?? null;
$testID = 3; // Идентификатор теста
include '../../vendor/connect.php'; // Подключаемся к базе данных

$sql_check_assignment = "SELECT * FROM AssignedTests WHERE DriverID = $driverID AND TestID = $testID";
$result_assignment = $conn->query($sql_check_assignment);

if ($result_assignment->num_rows == 0) {
    // Если тестирование не назначено текущему пользователю, перенаправляем на главную страницу
    header("Location: ../driver/driver_index.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Тест на реакцию светофора</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .circle {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin: 20px;
        }
        .red {
            background-color: red;
        }
        .yellow {
            background-color: yellow;
        }
        .green {
            background-color: green;
        }
        #green-btn {
            display: none; /* Скрыть кнопку изначально */
        }
        canvas {
            max-width: 400px;
            margin: 20px auto;
        }
        @media screen and (min-width: 700px) {
            #traffic-light {
                margin-left: 36%;
            }
        }
        @media screen and (min-width: 768px) {
            #traffic-light {
                margin-left: 40%;
            }
        }
        @media screen and (min-width: 992px) {
            #traffic-light {
                margin-left: 42%;
            }
        }
        @media screen and (min-width: 1200px) {
            #traffic-light {
                margin-left: 44%;
            }
        }
        .game{
            margin-bottom: 200px;
        }
       
    </style>
</head>
<body>
    <?php

    include '../driver/navbar.php';
    include '../footer.php' ?>
    <div class="container text-center game">
        <h1>Тест на реакцию светофора</h1>
        <p>Когда вы нажмете "Начать" загорится красный цвет и станет доступна кнопка "Нажмите, когда загорится зеленый"
        Ваша задача нажать на эту кнопку как только загорится зеленый
        Всего дается 3 попытки.
        Если вы нажмете слишком рано ничего страшного, нажмите начать и продолжайте, обратите внимание на то что администратору видно кол-во фальшстартов
        </p>
        <div id="traffic-light">
            <div class="circle bg-secondary"></div>
            <div class="circle yellow"></div>
            <div class="circle bg-secondary"></div>
        </div>
        <button id="start-btn" class="btn btn-primary mt-3">Начать</button>
        <button id="green-btn" class="btn btn-success mt-3">Нажмите, когда загорится зеленый</button>
        <div id="exit-btn" class="text-center mt-3" style="display: none;">
          <a href="../driver/driver_index.php" class="btn btn-primary">Выход</a>
        </div>
        <p id="result" class="mt-3"></p>
        <p id="attempts" class="mt-3"></p>
        <p id="false-starts" class="mt-3"></p>

        <canvas id="myChart"></canvas>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    $(document).ready(function(){
        var startTime;
        var endTime;
        var greenTimeout;
        var attempts = 0;
        var totalReactionTime = 0;
        var reactionTimes = [];
        var clickedBeforeGreen = false;
        var falseStarts = 0;

        $('#start-btn').on('click', function() {
        clickedBeforeGreen = false;
        $('#result').text('');
        $('#traffic-light .circle').removeClass('bg-danger bg-success').addClass('bg-secondary');
        $('#traffic-light .circle:nth-child(2)').removeClass('yellow').addClass('bg-secondary');
        $('#traffic-light .circle:nth-child(1)').removeClass('bg-secondary').addClass('red');
        attempts++; // Увеличиваем количество попыток при начале новой
        $('#attempts').text('Номер попытки: ' + attempts); // Обновляем отображение количества попыток
        $('#false-starts').text('Фальшстарты: ' + falseStarts); // Обновляем отображение количества фальшстартов
        $(this).hide();
        $('#green-btn').show();
        setTimeout(function() {
            $('#green-btn').prop('disabled', false);
        }, 500);

        clearInterval(greenTimeout);
        var randomTime = Math.floor(Math.random() * (7000 - 2000 + 1)) + 2000;
        greenTimeout = setTimeout(function() {
            if (!clickedBeforeGreen) {
                $('#traffic-light .circle:nth-child(3)').removeClass('bg-secondary').addClass('green');
                startTime = new Date();
            }
        }, randomTime);
    });


        $('#green-btn').on('click', function() {
        clearInterval(greenTimeout);
        if ($('#traffic-light .circle:nth-child(3)').hasClass('green') && !clickedBeforeGreen) {
            endTime = new Date();
            var reactionTime = endTime - startTime;
            totalReactionTime += reactionTime;
            reactionTimes.push(reactionTime);
            $('#result').append('Попытка ' + attempts + ': ' + reactionTime + ' мс<br>'); // Исправлено здесь
            if (attempts === 3) {
                $('#exit-btn').show();
                $('#start-btn').hide();
                var averageTime = totalReactionTime / 3;
                $('#result').append('Среднее время: ' + averageTime.toFixed(2) + ' мс<br>');
                $('#green-btn').prop('disabled', true);
                $('#traffic-light .circle:nth-child(3)').removeClass('green').addClass('bg-secondary');
                drawChart();
                saveResults(); // Добавлено сохранение результатов теста
            }
        } else {
            if (clickedBeforeGreen) {
                return;
            }
            $('#result').text('Слишком рано');
            if (attempts >= 1) {
                attempts--;
                falseStarts++;
            }
            $('#false-starts').text('Фальшстарты: ' + falseStarts); // Обновляем отображение количества фальшстартов
            clickedBeforeGreen = true;
            $('#start-btn').prop('disabled', false);
            $('#traffic-light .circle:nth-child(3)').removeClass('green').addClass('bg-secondary');
            var randomTime = Math.floor(Math.random() * (7000 - 2000 + 1)) + 2000;
            greenTimeout = setTimeout(function() {
                if (!clickedBeforeGreen) {
                    $('#traffic-light .circle:nth-child(3)').removeClass('bg-secondary').addClass('green');
                    startTime = new Date();
                }
            }, randomTime);
        }

        $('#start-btn').show();
        $(this).hide();
        $('#traffic-light .circle').removeClass('red green').addClass('bg-secondary');

        
        if (attempts === 3) {
                $('#start-btn').hide(); // Скрыть кнопку "Начать"
            }
    });



        function drawChart() {
            var ctx = document.getElementById('myChart').getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Попытка 1', 'Попытка 2', 'Попытка 3'],
                    datasets: [{
                        label: 'Время реакции (мс)',
                        data: reactionTimes,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // Функция для сохранения результатов теста в базе данных
        function saveResults() {
            $.ajax({
                url: window.location.href,
                type: 'POST',
                data: {
                    attempts: attempts,
                    falseStarts: falseStarts,
                    averageTime: totalReactionTime / 3
                },
                success: function(response) {
                    console.log(response);
                },
                error: function(xhr, status, error) {
                    console.error('Ошибка сохранения результатов:', error);
                }
            });
        }
    });
    </script>
</body>
</html>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $attempts = $_POST['attempts'] ?? 0;
    $falseStarts = $_POST['falseStarts'] ?? 0;
    $averageTime = $_POST['averageTime'] ?? 0;

    // Проверка наличия данных и выполнение необходимых действий (например, сохранение в базу данных)
    if ($attempts > 0 && $falseStarts >= 0 && $averageTime > 0) {
        $dateTimeCompleted = date('Y-m-d H:i:s');

        // Добавляем результаты тестирования в базу данных
        if (isset($_SESSION['driver_id'])) {
            $driverID = $_SESSION['driver_id'];
            $testID = 3; // Идентификатор теста

            $sql_insert_result = "INSERT INTO Passed_Traffic_Lights_FalseStarts (TestID, DriverID, DateTimeCompleted, Count, AverageTimeTaken) VALUES ($testID, $driverID, '$dateTimeCompleted', $falseStarts, $averageTime)";
            $conn->query($sql_insert_result);
            echo "Данные успешно сохранены";
            $delete_sql = "DELETE FROM AssignedTests WHERE DriverID = $driverID AND TestID = $testID";
          if ($conn->query($delete_sql) === true) {
              // Успешно удалено
          } else {
              echo "Ошибка при удалении записи: " . $conn->error;
          }
        }
    } else {
        echo "Данные о результате теста не были получены или не корректны";
    }
}

?>
