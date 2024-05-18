<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Водитель</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<style>
body{
    height: 320vh;
}
</style>
<body>
    <?php
include 'navbar.php';
include '../../vendor/connect.php';
       //авто удаление
       include '../../vendor/auto-clear-test.php';
    

    // Проверяем, получен ли ID водителя через параметр запроса
    if(isset($_GET['id'])) {
        // Преобразуем полученное значение ID водителя в целое число для безопасности
        $driverID = intval($_GET['id']);
        
        session_start();
        $_SESSION['iduser'] = $driverID;

        // SQL-запрос для получения данных о водителе и его лицензиях
        $sql = "SELECT Drivers.*, MAX(DriverTests.DateCompleted) AS LastTestDate,
                    GROUP_CONCAT(LicenseCategories.category_description SEPARATOR ', ') AS LicenseCategories
            FROM Drivers
            LEFT JOIN DriverTests ON Drivers.DriverID = DriverTests.DriverID
            LEFT JOIN LicenseCategories ON Drivers.DriverID = LicenseCategories.DriverID
            WHERE Drivers.DriverID = $driverID
            GROUP BY Drivers.DriverID";

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
    ?>
            <div class="container mt-5">
                <div class="row justify-content-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center">
                                <a href="admin_index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i></a>
                                <h4 class="ml-3 mb-0">Редактировать пользователя</h4>
                            </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Поле</th>
                                        <th>Значение</th>
                                        <th>Действие</th>
                                    </tr>
                                    <?php foreach ($row as $key => $value) { ?>
                                        <tr>
                                            <td><?php echo $key; ?></td>
                                            <td><?php echo $value; ?></td>
                                            <td>
                                                <?php if ($key !== 'DriverID') { ?>
                                                    <?php if ($key === 'LastTestDate') { ?>
                                                        <a href="edit_driver_last.php?id=<?php echo $driverID; ?>" class="btn btn-sm btn-primary"><i class="fas fa-pencil-alt"></i></a>
                                                    <?php } else { ?>
                                                        <a href="<?php echo $key === 'LicenseCategories' ? 'edit_driver_category.php?id=' . $driverID . '&field=' . $key : 'edit_driver.php?id=' . $driverID . '&field=' . $key; ?>" class="btn btn-sm btn-primary"><i class="fas fa-pencil-alt"></i></a>
                                                    <?php } ?>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            // если есть назначенные тесты
    $sql_assigned_tests = "SELECT Tests.TestID, Tests.Title, Tests.Description, Tests.img, AssignedTests.TimeToComplete, DATE_ADD(AssignedTests.DateAssigned, INTERVAL AssignedTests.TimeToComplete HOUR) AS EndTime
    FROM AssignedTests
    INNER JOIN Tests ON AssignedTests.TestID = Tests.TestID
    WHERE AssignedTests.DriverID = $driverID AND AssignedTests.IsCompleted = 0";
    $result_assigned_tests = $conn->query($sql_assigned_tests);

    if ($result_assigned_tests->num_rows > 0) {
    ?>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <h4>Назначенные тесты</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered ">
                            <thead>
                                <tr>
                                    <th>Название теста</th>
                                    <th>Описание</th>
                                    <th>Картинка</th>
                                    <th>Время на прохождение (часы)</th>
                                    <th>Оставшееся время</th> <!-- Новая ячейка для отображения оставшегося времени -->
                                    <th>Отменить</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while ($row_assigned_test = $result_assigned_tests->fetch_assoc()) {
                                    // Получаем конечное время теста
                                    $endTime = strtotime($row_assigned_test['EndTime']);
                                    $currentTime = time();
                                    // Вычисляем оставшееся время в секундах
                                    $timeLeft = $endTime - $currentTime;
                                    // Переводим оставшееся время из секунд в формат часов:минут:секунд
                                    $formattedTimeLeft = gmdate("H:i:s", $timeLeft);
                                    ?>

                                    <tr>
                                        <td><?php echo $row_assigned_test['Title']; ?></td>
                                        <td><?php echo $row_assigned_test['Description']; ?></td>
                                        <td><img src="../../img_tests/<?php echo $row_assigned_test['img']; ?>" alt="<?php echo $row_assigned_test['Title']; ?>" style="max-width: 100px;"></td>
                                        <td><?php echo $row_assigned_test['TimeToComplete']; ?></td>
                                        <td class="remaining-time"><?php echo $formattedTimeLeft; ?></td> <!-- Отображаем оставшееся время с классом "remaining-time" -->
                                        <td>
                                            <form method="post" action="cancel_test.php">
                                                <input type="hidden" name="test_id" value="<?php echo $row_assigned_test['TestID']; ?>">
                                                <input type="hidden" name="driver_id" value="<?php echo $driverID; ?>">
                                                <button type="submit" name="cancel_test" class="btn btn-danger">Отменить</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }
    ?>
            

            <!-- Код для назначения тестирования -->
            <div class="container mt-5">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <h4>Назначение тестирования</h4>
                        <form method="post" action="assign_test.php?id=<?php echo $driverID; ?>" id="assignTestForm" onsubmit="return confirmAssignTests()">


                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Название теста</th>
                                            <th>Описание</th>
                                            <th>Картинка</th>
                                            <th>Назначить</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Запрос для получения списка тестов, которые еще не назначены данному пользователю или назначены, но еще не завершены
                                        $sql_tests = "SELECT * FROM Tests WHERE TestID NOT IN (
                                                        SELECT TestID FROM AssignedTests WHERE DriverID = $driverID AND IsCompleted = 0
                                                      )";
                                        $result_tests = $conn->query($sql_tests);
                                        if ($result_tests->num_rows > 0) {
                                            while ($row_test = $result_tests->fetch_assoc()) {
                                                ?>
                                                <tr>
                                                    <td><?php echo $row_test['Title']; ?></td>
                                                    <td><?php echo $row_test['Description']; ?></td>
                                                    <td><img src="../../img_tests/<?php echo $row_test['img']; ?>" alt="<?php echo $row_test['Title']; ?>" style="max-width: 100px;"></td>
                                                    <td style="text-align: center; vertical-align: middle;">
                                    <span style="font-size: 12px;">Кол-во часов</span> <!-- Текст "кол-во часов" -->
                                    <br> <!-- Добавляем перенос строки -->
                                    <input type="checkbox" name="assign_test[]" value="<?php echo $row_test['TestID']; ?>" style="transform: scale(2); margin-right: 5px;"> <!-- Увеличиваем размер галочки -->
                                    <select name="time_to_complete[]" style="margin-left: 5px;"> <!-- Отступ слева -->
                                        <?php
                                        // Generate options for time selection (1 to 100 hours)
                                        for ($i = 1; $i <= 100; $i++) {
                                            echo "<option value='$i'>$i</option>";
                                        }
                                        ?>
                                    </select>
                                </td>




                                                </tr>
                                            <?php }
                                        } else {
                                            ?>
                                            <tr>
                                                <td colspan="4">Нет доступных тестов.</td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-center">
                                <button type="submit" name="assign_tests" class="btn btn-primary">Назначить тестирование</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


    <?php
        } else {
            echo "Нет данных о водителе с ID: " . $driverID;
        }
    } else {
        echo "Не передан ID водителя в параметре запроса.";
    }
    


    ?>

<?php include '../footer.php'?>;
</body>

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  // Функция для обновления оставшегося времени каждую секунду
  function updateRemainingTime() {
    var remainingTimeElements = document.querySelectorAll('.remaining-time');
    
    remainingTimeElements.forEach(function(element) {
        var timeParts = element.innerHTML.split(':');
        var hours = parseInt(timeParts[0]);
        var minutes = parseInt(timeParts[1]);
        var seconds = parseInt(timeParts[2]);
        
        if (seconds > 0) {
            seconds--;
        } else {
            seconds = 59;
            if (minutes > 0) {
                minutes--;
            } else {
                minutes = 59;
                if (hours > 0) {
                    hours--;
                }
            }
        }

        element.innerHTML = (hours < 10 ? "0" : "") + hours + ":" + (minutes < 10 ? "0" : "") + minutes + ":" + (seconds < 10 ? "0" : "") + seconds;
        
        // Проверка истечения времени теста
        if (hours === 0 && minutes === 0 && seconds === 0) {
            // Получение ID теста и водителя из родительских элементов
            var testID = element.parentNode.querySelector('input[name="test_id"]').value;
            var driverID = element.parentNode.querySelector('input[name="driver_id"]').value;
            
            // Отправка AJAX запроса на удаление теста
            $.ajax({
                url: 'cancel_test.php',
                type: 'POST',
                data: {
                    test_id: testID,
                    driver_id: driverID
                },
                success: function(response) {
                    // Перезагрузка страницы для обновления списка тестов
                    window.location.reload();
                },
                error: function(xhr, status, error) {
                    console.error('Ошибка при удалении теста:', error);
                }
            });
        }
    });
}


    setInterval(updateRemainingTime, 1000);

    function confirmAssignTests() {
        var selectedTests = document.querySelectorAll('input[name="assign_test[]"]:checked');
        if (selectedTests.length === 0) {
            alert("Не выбраны тесты для назначения");
            return false; // Отмена отправки формы
        }
        
        var testNames = [];
        selectedTests.forEach(function(test) {
            testNames.push(test.parentNode.parentNode.cells[0].innerText); // Получение названий тестов
        });

        return confirm("Вы уверены, что хотите назначить следующие тесты?\n\n" + testNames.join("\n"));
    }
</script>

</html>


<?php
    include 'rezult/Pilot.php';
    include 'rezult/Baevsky.php';
    include 'rezult/Dinamomety.php';

    include 'rezult/Pulsoksimetr.php';
    include 'rezult/San.php';
    include 'rezult/Shulte.php';
    include 'rezult/Tonometr.php';
    include 'rezult/Svetofor.php';

?>