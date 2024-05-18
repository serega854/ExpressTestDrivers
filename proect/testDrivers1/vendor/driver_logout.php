<?php
session_start();

// Удаление сессионной переменной
unset($_SESSION['driver_email']);

// Перенаправление на страницу входа
header("Location: ../web/driver/driver_login.php");
exit();
?>
