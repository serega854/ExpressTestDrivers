<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include('../vendor/connect.php');

    $username = $_POST['username'];
    $password = $_POST['password'];

    $username = $conn->real_escape_string($username);
    $password = $conn->real_escape_string($password);

    $sql = "SELECT * FROM Administrators WHERE Name='$username' AND Password='$password'";
    $result = $conn->query($sql);

    if (!$result) {
        die("Ошибка SQL: " . $conn->error);
    }

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['admin_username'] = $username;
        $_SESSION['admin_id'] = $row['AdminID']; // Сохраняем ID админа в сессию

        header("Location: ../web/admin/admin_index.php");
        exit();
    } else {
        $_SESSION['login_error'] = "Неправильное имя пользователя или пароль.";
        header("Location: ../web/admin/admin_login.php");
        exit();
    }
}
?>
