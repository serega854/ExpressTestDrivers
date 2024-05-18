

<?php
/*
session_start();

include 'connect.php';

// SQL запрос для удаления истекших тестов из таблицы AssignedTests
$sql_delete_expired_tests = "DELETE FROM AssignedTests WHERE DATE_ADD(DateAssigned, INTERVAL TimeToComplete HOUR) < NOW() AND IsCompleted = 0";

if ($conn->query($sql_delete_expired_tests) === TRUE) {
    $response = array('success' => true, 'message' => 'Удалено истекших тестов: ' . $conn->affected_rows);
    echo json_encode($response);
} else {
    $response = array('success' => false, 'message' => 'Ошибка при удалении истекших тестов: ' . $conn->error);
    echo json_encode($response);
}


$conn->close();
*/
?>
