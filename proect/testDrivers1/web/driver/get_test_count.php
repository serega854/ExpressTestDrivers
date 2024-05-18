<?php
session_start();

include '../../vendor/connect.php';
include '../../vendor/auto-clear-test.php';
if (isset($_SESSION['driver_email'])) {
    $driver_email = $_SESSION['driver_email'];

    // Получаем ID водителя из сессии
    $sql = "SELECT DriverID FROM Drivers WHERE Email='$driver_email'"; 
    $result = $conn->query($sql); 
    if (!$result) { 
      die("Ошибка SQL: " . $conn->error); 
    } 
    $row = $result->fetch_assoc(); 
    $driver_id = $row['DriverID']; 

    // Получаем количество назначенных тестирований для данного водителя
    $sql = "SELECT COUNT(*) AS testCount FROM AssignedTests WHERE DriverID='$driver_id'";
    $result = $conn->query($sql);
    if ($result) {
      $row = $result->fetch_assoc();
      $testCount = $row['testCount'];
    } else {
      // Обработка ошибки
      $testCount = 0;
    }

    
}
?>
