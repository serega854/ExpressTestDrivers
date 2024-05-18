<?php
session_start();

// Удаление сессионной переменной
unset($_SESSION['admin_username']);

// Перенаправление на страницу входа
header("Location: ../web/admin/admin_login.php");
exit();
?>
