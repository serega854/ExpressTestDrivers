<?php





// SQL-запрос для получения назначенных тестов, время которых истекло
$sql_expired_tests = "SELECT AssignmentID FROM AssignedTests WHERE DATE_ADD(DateAssigned, INTERVAL TimeToComplete HOUR) < NOW() AND IsCompleted = 0";
$result_expired_tests = $conn->query($sql_expired_tests);

if ($result_expired_tests->num_rows > 0) {
    while ($row = $result_expired_tests->fetch_assoc()) {
        // Удаляем назначенные тесты, время которых истекло
        $assignmentID = $row['AssignmentID'];
        $sql_delete_assignment = "DELETE FROM AssignedTests WHERE AssignmentID = $assignmentID";
        $conn->query($sql_delete_assignment);
    }
}


?>