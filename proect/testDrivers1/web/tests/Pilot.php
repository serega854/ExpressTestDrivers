<?php
session_start();

// Проверяем, назначено ли тестирование текущему пользователю
$driverID = $_SESSION['driver_id'] ?? null;
$testID = 2; // Идентификатор теста
include '../../vendor/connect.php'; // Подключаемся к базе данных

$sql_check_assignment = "SELECT * FROM AssignedTests WHERE DriverID = $driverID AND TestID = $testID";
$result_assignment = $conn->query($sql_check_assignment);

if ($result_assignment->num_rows == 0) {
    // Если тестирование не назначено текущему пользователю, перенаправляем на главную страницу
    header("Location: ../driver/driver_index.php");
    exit();
}
$flag = 0;
// Обработка сохранения результатов
if(isset($_POST['save_results'])) {
    $bestResult = $_POST['best_result'];
    $sql = "INSERT INTO PassedPilot (TestID, DriverID, DateTimeCompleted, BestAttemptResult) VALUES ($testID, $driverID, NOW(), ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("d", $bestResult);
    $stmt->execute();
    $stmt->close();

    // Удаляем запись из таблицы AssignedTests
    $sql_delete_assignment = "DELETE FROM AssignedTests WHERE DriverID = $driverID AND TestID = $testID";
    if ($conn->query($sql_delete_assignment) === TRUE) {
        echo "Запись в таблице AssignedTests успешно удалена.";
    } else {
        echo "Ошибка при удалении записи из таблицы AssignedTests: " . $conn->error;
    }
}

?>


<!DOCTYPE html>
<html>
<head>
<?php

include '../driver/navbar.php';
include '../footer.php' ?>
<title>Тест пилотов</title>
<!-- Подключаем Bootstrap CSS -->
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<style>
  #game-container {
    width: 600px;
    height: 400px;
    border: 2px solid black;
    position: relative;
    margin: 0 auto;
    margin-top: 100px;
    padding-top: 100px; /* Добавляем верхний отступ для учета отступа */
    padding-left: 100px; /* Добавляем левый отступ для учета отступа */
  }
  #test-conteiner{
    position: absolute; /* Позиционирование абсолютное */
  top: 100px; /* Укажите желаемую вертикальную позицию */
  left: 50%; /* Укажите желаемую горизонтальную позицию */
  transform: translateX(-50%); /* Центрирование контейнера по горизонтали */
  }

  .player {
    width: 50px;
    height: 50px;
    background-color: red;
    position: absolute;
    cursor: pointer;
  }

  .enemy {
    width: 50px;
    height: 50px;
    background-color: blue;
    position: absolute;
  }
  
  #time-survived {
    font-size: 16px;
    margin-top: 20px;
  }

  /* Стили для кнопок */
  #button-container {
    text-align: center;
    margin-top: 20px;
  }

  /* Стили для Bootstrap кнопок */
  .btn {
    margin: 5px;
  }
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>


<div id='test-conteiner'>
<h2>Тест пилотов</h2>
<p>Зажмите левую кнопку мыши на красном квадрате и уклоняйтесь от синих квадратов. Игра будет длиться до тех пор пока вы не столкнетесь с синим квадратом, черной стеной или не отпустите левую кнопку мыши. Нужно продержаться как можно дольше. Всего дается 3 попытки</p>

    <div id="game-container">
    <div class="player"></div>
    <div class="enemy" id="enemy0" style="top: 60px; left: 270px;"></div>
    <div class="enemy" id="enemy1" style="top: 330px; left: 300px;"></div>
    <div class="enemy" id="enemy2" style="top: 320px; left: 70px;"></div>
    <div class="enemy" id="enemy3" style="top: 70px; left: 70px;"></div>
    </div>

    <div id="results-container" class="text-center">

    <div id="all-results" class="mb-4"></div>
    <h5 id="best-result" class="font-weight-bold" style="display:none;"></h5>
    </div>


    <!-- Контейнер для кнопок -->
    <div id="button-container">
    <button id="start-button" class="btn btn-primary">Начать</button>
    <button id="exit-button" class="btn btn-danger" style="display:none;">Сохранить результат</button>
    <!--<div id="console"></div>-->

    </div>
</div>
<script>
  $(document).ready(function() {
    
    var gameAttempts = 0; // Переменная для отслеживания попыток
    var results = []; // Массив для хранения результатов всех попыток
    var gameRunning = true;
    var initialEnemySpeed = 2000; // initial speed
    var enemySpeed = initialEnemySpeed;
    var accelerationRate = 0.9; // acceleration rate
    var player = $('.player');
    var container = $('#game-container');
    var playerWidth = player.width();
    var playerHeight = player.height();
    var containerOffsetX = container.offset().left;
    var containerOffsetY = container.offset().top;
    var rotationTimer; // Таймер для непрерывного вращения квадрата
    var gameEndTime;
    var gameOverShown = false; // Флаг для отслеживания отображения окна окончания игры

    player.css({
        'top': (container.height() - playerHeight) / 2,
        'left': (container.width() - playerWidth) / 2
    });

    var mousePressed = false; // Переменная для отслеживания нажатия кнопки мыши


    
    // Smoothly move enemies
    function moveEnemies() {
        if (mousePressed && gameRunning) { // Проверяем состояние нажатия кнопки мыши
            $('.enemy').each(function() {
                var randomTop = Math.floor(Math.random() * 8) * 50;
                var randomLeft = Math.floor(Math.random() * 12) * 50;
                var enemy = $(this);
                enemy.animate({ top: randomTop, left: randomLeft }, {
                    duration: enemySpeed,
                    complete: function() {
                        if (gameRunning) {
                            moveEnemies(); // вызываем moveEnemies после завершения анимации
                        }
                    }
                });
            });
            enemySpeed *= accelerationRate; // Increase enemy speed over time
        }
    }

    // Start moving enemies when mouse button is pressed
    $(document).mousedown(function(event) {
    if (!gameRunning) return;
    mousePressed = true;
   
}).mouseup(function() {
    mousePressed = false; // Отслеживаем отпускание кнопки мыши
    stopRotationTimer(); // Остановить таймер вращения
});


// Start moving enemies when mouse button is pressed on the player
$('.player').mousedown(function(event) {
    if (!gameRunning) return;
    mousePressed = true;
    startRotationTimer(); // Запустить таймер вращения
    moveEnemies();
}).mouseup(function() {
    mousePressed = false; // Отслеживаем отпускание кнопки мыши
    stopRotationTimer(); // Остановить таймер вращения
});


    // Smoothly move player
    $(document).mousemove(function(event) {
        if (mousePressed && gameRunning) { // Проверяем состояние нажатия кнопки мыши
            rotateSquare(event.pageX, event.pageY);
        }
    });

    // Функция для непрерывного вращения квадрата
    function rotateSquare(mouseX, mouseY) {
        var centerX = player.position().left + playerWidth / 2;
        var centerY = player.position().top + playerHeight / 2;
        var deltaX = mouseX - centerX;
        var deltaY = mouseY - centerY;
        var distance = Math.sqrt(deltaX * deltaX + deltaY * deltaY);
        var rotationRadius = 20; // Радиус вращения
        
        var newX = mouseX - containerOffsetX - playerWidth / 2 + rotationRadius * (deltaX / distance);
        var newY = mouseY - containerOffsetY - playerHeight / 2 + rotationRadius * (deltaY / distance);
        
        if (newX < 0 || newX + playerWidth > 600 || newY < 0 || newY + playerHeight > 400) {
            endGame();
        } else {
            player.stop(true, true).css({ 'left': newX, 'top': newY });
            checkCollision(player);
        }
    }

    // Запустить таймер для непрерывного вращения квадрата
    function startRotationTimer() {
        rotationTimer = setInterval(function() {
            var mouseX = player.position().left + playerWidth / 2;
            var mouseY = player.position().top + playerHeight / 2;
            rotateSquare(mouseX, mouseY);
        }, 50); // Частота обновления позиции квадрата
    }

    // Остановить таймер вращения
    function stopRotationTimer() {
        clearInterval(rotationTimer);
    }

    // Check collision with enemies
    function checkCollision(player) {
        var playerPos = player.position();
        $('.enemy').each(function() {
            var enemyPos = $(this).position();
            if (Math.abs(playerPos.left - enemyPos.left) < 50 && Math.abs(playerPos.top - enemyPos.top) < 50) {
                endGame();
                $('.enemy').stop(); // Остановка всех синих квадратов при столкновении
            }
        });
    }
// End the game
function endGame() {
    if (gameRunning) {
        gameEndTime = new Date(); // Запоминаем время окончания игры
        gameRunning = false;
        if (!gameOverShown) {
            gameOverShown = true;
            var timeSurvived = gameEndTime - gameStartTime; // Вычисляем время, проведенное в игре в миллисекундах
            var seconds = Math.floor(timeSurvived / 1000);
            var milliseconds = timeSurvived % 1000;
            var resultString = seconds + ' секунд и ' + milliseconds + ' миллисекунд';
            results.push(resultString); // Добавляем результат в массив
            gameAttempts++; // Увеличиваем количество попыток
            
            $('#all-results').append('<p>Попытка ' + gameAttempts + ': ' + resultString + '</p>'); // Выводим результат текущей попытки
            
            if (gameAttempts >= 3) {
                exitGame();
                $("#start-button").hide(); // Скрываем кнопку "Начать"
                $("#exit-button").show(); // Показываем кнопку "Выход"
                var bestResult = Math.max(...numericResults.filter(result => !isNaN(result))); // Находим минимальное значение в массиве чисел

                $('#best-result').text('Лучший результат: ' + bestResult).show(); // Выводим лучший результат
            } else {
                $('#time-survived').text('Попытка ' + gameAttempts + ': Вы продержались ' + seconds + ' секунд и ' + milliseconds + ' миллисекунд.'); // Выводим результат текущей попытки
                $("#start-button").show(); // Показываем кнопку "Начать" для следующей попытки
            }
        }
    }
    // Сброс всех квадратов на исходные позиции без анимации
    $('.player').css({'top': (container.height() - playerHeight) / 2, 'left': (container.width() - playerWidth) / 2});
    if (gameRunning) {
        $('.enemy').css({'top': '60px', 'left': '270px'});
        $('#enemy1').css({'top': '330px', 'left': '300px'});
        $('#enemy2').css({'top': '320px', 'left': '70px'});
        $('#enemy3').css({'top': '70px', 'left': '70px'});
    }
}

    // Start the game
    var gameStartTime = new Date(); // Запоминаем время начала игры
    $("#start-button").hide(); // Скрываем кнопку "Начать"
    $("#exit-button").hide(); // Скрываем кнопку "Выход"

    // End the game if mouse is released over player
    $('.player').mouseup(function() {
        if (gameRunning) {
            endGame();
            $('.enemy').stop(); // Остановка всех синих квадратов при окончании игры
        }
    });

    // Код для кнопки "Начать"
    $("#start-button").click(function() {
        restartGame();
    });

    // Код для кнопки "Выход"
    $("#exit-button").click(function() {
        exitGame();
    });

    function restartGame() {
        gameRunning = true;
        gameOverShown = false;
        gameStartTime = new Date();
        $('#time-survived').text('');

        // Возвращаем синие кубики на исходные позиции
        $('.enemy').stop(true, true).each(function(index) {
            switch(index) {
                case 0:
                    $(this).css({ 'top': '60px', 'left': '270px' });
                    break;
                case 1:
                    $(this).css({ 'top': '330px', 'left': '300px' });
                    break;
                case 2:
                    $(this).css({ 'top': '320px', 'left': '70px' });
                    break;
                case 3:
                    $(this).css({ 'top': '70px', 'left': '70px' });
                    break;
            }
        });
        $('.player').css({ 'left': (container.width() - playerWidth) / 2, 'top': (container.height() - playerHeight) / 2 }); // сбросить позицию игрока
        enemySpeed = initialEnemySpeed; // сбросить скорость врагов
        moveEnemies(); // начать анимацию движения врагов заново
        $("#start-button").hide(); // Скрыть кнопку "Начать"
    }

    function exitGame() {
    var numericResults = results.map(function(result) {
        // Извлекаем числовые значения секунд и миллисекунд из строки
        var matches = result.match(/\d+/g); // Извлекаем все числа из строки
        var seconds = parseInt(matches[0]); // Первое число - количество секунд
        var milliseconds = parseInt(matches[1]); // Второе число - количество миллисекунд
        return seconds * 1000 + milliseconds; // Преобразуем время в миллисекундах
    });
    // Вставьте следующий код в функцию endGame перед строкой с нахождением лучшего результата
    //$('#console').text('Results: ' + results.join(', '));


    var bestResult = Math.max(...numericResults); // Находим минимальное значение в массиве чисел
    $('#best-result').text('Лучший результат: ' + bestResult / 1000 + ' секунд').show(); // Выводим лучший результат
    $('#time-survived').html('Вы продержались: <br>' + results.join('<br>')); // Выводим результаты всех попыток
    $("#start-button").hide(); // Скрываем кнопку "Начать"
    $("#exit-button").hide(); // Скрываем кнопку "Выход"



}



});


// Код для кнопки "Выход"
$("#exit-button").click(function() {
    window.location.href = "http://testdrivers/web/driver/driver_index.php";
});


function saveResultsToDatabase(bestResult) {
        $.ajax({
            url: '<?= $_SERVER['PHP_SELF'] ?>', // Используем текущий файл в качестве адреса обработчика
            method: 'POST',
            data: { save_results: true, driver_id: <?= $driverID ?>, best_result: bestResult },
            success: function(response) {
                console.log('Результаты успешно сохранены в базе данных.');
            },
            error: function(xhr, status, error) {
                console.error('Произошла ошибка при сохранении результатов:', error);
            }
        });
    }

    // Код для кнопки "Выход"
    $("#exit-button").click(function() {
        var bestResult = parseFloat($('#best-result').text().split(' ')[2]);
        saveResultsToDatabase(bestResult);
        window.location.href = "http://testdrivers/web/driver/driver_index.php";
    });

    // Обработка сохранения результатов
    

    var resultsSaved = false; // Переменная для отслеживания сохранения результатов

    function saveResultsToDatabase(bestResult) {
    if (!resultsSaved) { // Проверяем, были ли результаты уже сохранены
            $.ajax({
                url: '<?= $_SERVER['PHP_SELF'] ?>', // Используем текущий файл в качестве адреса обработчика
                method: 'POST',
                data: { save_results: true, driver_id: <?= $driverID ?>, best_result: bestResult },
                
                success: function(response) {
                    console.log('Результаты успешно сохранены в базе данных.');
                    resultsSaved = true; // Устанавливаем флаг, чтобы указать, что результаты сохранены
                },
                error: function(xhr, status, error) {
                    console.error('Произошла ошибка при сохранении результатов:', error);
                }
            });
        } else {
            console.log('Результаты уже были сохранены.'); // Выводим сообщение о том, что результаты уже сохранены
        }
    }


    // Код для кнопки "Выход"
    $("#exit-button").click(function() {
        var bestResult = parseFloat($('#best-result').text().split(' ')[2]);

        window.location.href = "http://testdrivers/web/driver/driver_index.php";
    });




</script>
</body>
</html>
