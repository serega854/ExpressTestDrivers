<?php
session_start();

include '../../vendor/connect.php';

if(isset($_POST['assign_tests'])) {
    $driverID = $_GET['id'];

    // Получаем выбранные тесты и время для каждого теста
    $assignedTests = $_POST['assign_test'];
    $timeToComplete = $_POST['time_to_complete'];

    // Проверяем, что выбраны тесты для назначения
    if(empty($assignedTests)) {
        echo "Не выбраны тесты для назначения";
        exit();
    }

    // Подготавливаем SQL запрос для вставки данных в таблицу AssignedTests
    $sql_insert_assigned_tests = "INSERT INTO AssignedTests (AdminID, DriverID, TestID, DateAssigned, IsCompleted, TimeToComplete, DateEndAssigned) VALUES ";
    $values = array();

    // Цикл для формирования значений для каждого назначенного теста
    for($i = 0; $i < count($assignedTests); $i++) {
        $testID = $assignedTests[$i];
        $time = $timeToComplete[$i];
        $dateAssigned = date('Y-m-d H:i:s');
        $dateEndAssigned = date('Y-m-d H:i:s', strtotime("+$time hours")); // Вычисляем дату окончания тестирования

        // Добавляем значения в массив для запроса
        $values[] = "({$_SESSION['admin_id']}, $driverID, $testID, '$dateAssigned', 0, $time, '$dateEndAssigned')";
    }

    // Формируем окончательный запрос
    $sql_insert_assigned_tests .= implode(",", $values);

    // Выполняем запрос на вставку данных
    if($conn->query($sql_insert_assigned_tests) === TRUE) {
        // Создаем сессию назначенного тестирования
$_SESSION['assigned_tests'] = array('driver_id' => $driverID, 'assigned_tests_ids' => $assignedTests);

        header("Location: admin_aboutDriver.php?id=$driverID");
    } else {
        echo "Ошибка при назначении тестов: " . $conn->error;
    }

    $conn->close();
}
?>
