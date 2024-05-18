<?php
session_start();

// Проверка наличия сессии администратора
if (!isset($_SESSION['admin_username'])) {
    header("Location: admin_login.php");
    exit();
}

include '../../vendor/connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Проверяем, получен ли ID теста через POST-запрос
    if (isset($_POST['test_id']) && isset($_POST['driver_id'])) {
        // Преобразуем полученное значение ID теста и ID водителя в целые числа для безопасности
        $testID = intval($_POST['test_id']);
        $driverID = intval($_POST['driver_id']);

        // SQL запрос для удаления теста из таблицы AssignedTests
        $sql_delete_test = "DELETE FROM AssignedTests WHERE TestID = $testID AND DriverID = $driverID";

        if ($conn->query($sql_delete_test) === TRUE) {
            // Успешно удален тест, перенаправляем пользователя обратно на страницу водителя
            header("Location: admin_aboutDriver.php?id=$driverID");
        } else {
            echo "Ошибка при отмене теста: " . $conn->error;
        }
    } else {
        echo "Не передан ID теста для отмены.";
    }
}

$conn->close();
?>
