<?php
session_start();
include '../../vendor/connect.php';


//удаление назначенного тестирования
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['start_test'])) {
    $test_title = $_POST['test_title'];
    $assignment_id = $_POST['assignment_id'];
    // Дополнительная проверка и обработка данных перед выполнением SQL-запроса
    
    // Выполнение SQL-запроса для удаления записи из базы данных
    $delete_sql = "DELETE FROM AssignedTests WHERE AssignmentID='$assignment_id'";
    if ($conn->query($delete_sql) === true) {
        // Успешно удалено
    } else {
        echo "Ошибка при удалении записи: " . $conn->error;
    }
}
