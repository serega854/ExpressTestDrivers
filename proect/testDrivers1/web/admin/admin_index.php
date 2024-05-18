<?php
session_start();

// Проверка наличия сессии администратора
if (!isset($_SESSION['admin_username'])) {
    header("Location: ../admin/admin_login.php");
    exit();
}

// Подключение к базе данных
include '../../vendor/connect.php';

// Проверяем, был ли передан параметр id для удаления пользователя
if(isset($_GET['id'])) {
    // Получаем ID пользователя, которого нужно удалить
    $driverId = $_GET['id'];

    // Подготовка SQL запросов для удаления связанных записей из таблиц
    $sql_delete_passedpilot = "DELETE FROM PassedPilot WHERE DriverID = ?";
    $sql_delete_passed_baevsky = "DELETE FROM Passed_Baevsky WHERE DriverID = ?";
    $sql_delete_passed_dynamometry = "DELETE FROM Passed_Dynamometry WHERE DriverID = ?";
    $sql_delete_passed_pulseoximetry = "DELETE FROM Passed_PulseOximetry WHERE DriverID = ?";
    $sql_delete_passed_san = "DELETE FROM Passed_SAN WHERE DriverID = ?";
    $sql_delete_passed_shulte_table = "DELETE FROM Passed_Shulte_Table WHERE DriverID = ?";
    $sql_delete_passed_tonometer = "DELETE FROM Passed_Tonometer WHERE DriverID = ?";
    $sql_delete_passed_traffic_lights_false_starts = "DELETE FROM Passed_Traffic_Lights_FalseStarts WHERE DriverID = ?";
    $sql_delete_assigned_tests = "DELETE FROM AssignedTests WHERE DriverID = ?";
    $sql_delete_LicenseCategories = "DELETE FROM LicenseCategories WHERE DriverID = ?";

    // Подготовка и выполнение запросов для удаления связанных записей
    $stmt_passedpilot = $conn->prepare($sql_delete_passedpilot);
    $stmt_passedpilot->bind_param("i", $driverId);
    $stmt_passedpilot->execute();

    $stmt_passed_baevsky = $conn->prepare($sql_delete_passed_baevsky);
    $stmt_passed_baevsky->bind_param("i", $driverId);
    $stmt_passed_baevsky->execute();

    $stmt_passed_dynamometry = $conn->prepare($sql_delete_passed_dynamometry);
    $stmt_passed_dynamometry->bind_param("i", $driverId);
    $stmt_passed_dynamometry->execute();

    $stmt_passed_pulseoximetry = $conn->prepare($sql_delete_passed_pulseoximetry);
    $stmt_passed_pulseoximetry->bind_param("i", $driverId);
    $stmt_passed_pulseoximetry->execute();

    $stmt_passed_san = $conn->prepare($sql_delete_passed_san);
    $stmt_passed_san->bind_param("i", $driverId);
    $stmt_passed_san->execute();

    $stmt_passed_shulte_table = $conn->prepare($sql_delete_passed_shulte_table);
    $stmt_passed_shulte_table->bind_param("i", $driverId);
    $stmt_passed_shulte_table->execute();

    $stmt_passed_tonometer = $conn->prepare($sql_delete_passed_tonometer);
    $stmt_passed_tonometer->bind_param("i", $driverId);
    $stmt_passed_tonometer->execute();

    $stmt_passed_traffic_lights_false_starts = $conn->prepare($sql_delete_passed_traffic_lights_false_starts);
    $stmt_passed_traffic_lights_false_starts->bind_param("i", $driverId);
    $stmt_passed_traffic_lights_false_starts->execute();

    // Подготовка и выполнение запроса для удаления записей из таблицы AssignedTests
    $stmt_assigned_tests = $conn->prepare($sql_delete_assigned_tests);
    $stmt_assigned_tests->bind_param("i", $driverId);
    $stmt_assigned_tests->execute();


    $sql_delete_LicenseCategories= $conn->prepare($sql_delete_LicenseCategories);
    $sql_delete_LicenseCategories->bind_param("i", $driverId);
    $sql_delete_LicenseCategories->execute();

    // Подготовка и выполнение запроса для удаления водителя из таблицы Drivers
    $sql_delete_driver = "DELETE FROM Drivers WHERE DriverID = ?";
    $stmt_delete_driver = $conn->prepare($sql_delete_driver);
    $stmt_delete_driver->bind_param("i", $driverId);
    if ($stmt_delete_driver->execute()) {
        // Если удаление прошло успешно, перенаправляем обратно на страницу списка водителей
        header("Location: admin_index.php");
        exit();
    } else {
        // Если произошла ошибка при удалении, выводим сообщение об ошибке
        echo "Ошибка при удалении водителя: " . $conn->error;
    }

    // Закрываем соединение с базой данных и запросы
    $stmt_passedpilot->close();
    $stmt_passed_baevsky->close();
    $stmt_passed_dynamometry->close();
    $stmt_passed_pulseoximetry->close();
    $stmt_passed_san->close();
    $stmt_passed_shulte_table->close();
    $stmt_passed_tonometer->close();
    $stmt_passed_traffic_lights_false_starts->close();
    $stmt_assigned_tests->close();
    $stmt_delete_driver->close();
    $sql_delete_LicenseCategories->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Главная страница администратора</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container mt-5">
    <h1>Список водителей</h1>
    <table id="driversTable" class="table">
        <thead>
        <tr>
            <th scope="col">ФИО</th>
            <th scope="col">Дата последнего прохождения</th>
            <th scope="col">Действие</th>
            <th scope="col">Удалить</th> <!-- Добавлена новая ячейка для кнопки удаления -->
        </tr>
        </thead>
        <tbody>
        <?php
        // Объединенный запрос для выбора последней даты прохождения из всех таблиц
        $sql = "SELECT Drivers.DriverID, CONCAT(Drivers.last_name, ' ', Drivers.first_name, ' ', Drivers.middle_name) AS FullName, 
                IFNULL(MAX(LastTestDate), 'Еще не проходил тестирования') AS LastTestDate
                FROM (
                    SELECT DriverID, MAX(DateTimeCompleted) AS LastTestDate FROM (
                        SELECT DriverID, DateTimeCompleted FROM PassedPilot
                        UNION ALL
                        SELECT DriverID, DateTimeCompleted FROM Passed_Baevsky
                        UNION ALL
                        SELECT DriverID, DateTimeCompleted FROM Passed_Dynamometry
                        UNION ALL
                        SELECT DriverID, DateTimeCompleted FROM Passed_PulseOximetry
                        UNION ALL
                        SELECT DriverID, DateTimeCompleted FROM Passed_SAN
                        UNION ALL
                        SELECT DriverID, DateTimeCompleted FROM Passed_Shulte_Table
                        UNION ALL
                        SELECT DriverID, DateTimeCompleted FROM Passed_Tonometer
                        UNION ALL
                        SELECT DriverID, DateTimeCompleted FROM Passed_Traffic_Lights_FalseStarts
                    ) AS all_tests
                    GROUP BY DriverID
                ) AS last_dates
                RIGHT JOIN Drivers ON last_dates.DriverID = Drivers.DriverID
                GROUP BY Drivers.DriverID, FullName
                ORDER BY LastTestDate DESC;";

        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["FullName"] . "</td>";
                echo "<td>" . $row["LastTestDate"] . "</td>";
                // Создаем ссылку для перехода на страницу с информацией о водителе
                echo '<td><a href="admin_aboutDriver.php?id=' . $row["DriverID"] . '" class="btn btn-primary">Подробнее</a></td>';
                // Добавляем кнопку удаления пользователя
                echo '<td><a href="?id=' . $row["DriverID"] . '" class="btn btn-danger">Удалить</a></td>';
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>Нет данных</td></tr>";
        }
        $conn->close();
        ?>
        </tbody>
    </table>
    <a href="reg_users.php"><button class="btn btn-success mt-3">Добавить водителя</button></a>
</div>

<?php include '../footer.php'?>;

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" charset="utf8"
        src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>
<script>
    $(document).ready(function () {
        $('#driversTable').DataTable({
            "scrollY": "500px", // Прокрутка, если больше 7 строк
            "scrollCollapse": true,
            "paging": false, // Отключение пагинации
            "searching": true, // Включение поиска
            "language": {
                "search": "Поиск"
            },
            "info": false, // Отключение информации о количестве записей
            "columnDefs": [
                {"orderable": false, "targets": -1} // Убрать сортировку для последнего столбца
            ]
        });

        // Обновление таблицы DataTables при изменении размера окна браузера
        $(window).resize(function () {
            $('#driversTable').DataTable().columns.adjust().draw();
        });
    });
</script>
</body>
</html>
