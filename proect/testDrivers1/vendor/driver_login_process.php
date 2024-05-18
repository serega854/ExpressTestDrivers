<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include('../vendor/connect.php');

    $email = $_POST['email'];
    $password = $_POST['password'];

    $email = $conn->real_escape_string($email);
    $password = $conn->real_escape_string($password);

    $sql = "SELECT * FROM Drivers WHERE Email='$email' AND Password='$password'";
    $result = $conn->query($sql);

    if (!$result) {
        die("Ошибка SQL: " . $conn->error);
    }

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['driver_id'] = $row['DriverID']; // Добавляем ID водителя в сессию
        $_SESSION['driver_email'] = $email;
        header("Location: ../web/driver/driver_index.php");
        exit();
    } else {
        $_SESSION['login_error'] = "Неправильный email или пароль.";
        header("Location: ../web/driver/driver_login.php");
        exit();
    }
}
?>
