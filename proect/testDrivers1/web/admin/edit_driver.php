<?php
session_start();

// Проверка наличия сессии администратора
if (!isset($_SESSION['admin_username'])) {
    header("Location: admin_login.php");
    exit();
}

include '../../vendor/connect.php';
include 'navbar.php';

// Проверяем, получены ли ID водителя и поле для редактирования через параметры запроса
if(isset($_GET['id']) && isset($_GET['field'])) {
    // Преобразуем полученные значения ID водителя и поля в целые числа и строку соответственно
    $driverID = intval($_GET['id']);
    $field = $_GET['field'];

    // Проверяем, был ли отправлен POST-запрос для сохранения изменений
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Получаем новое значение поля из POST-запроса
        $newValue = $_POST[$field];

        // Если поле для изменения - LastTestDate, то игнорируем его
        if ($field !== 'LastTestDate') {
            // SQL-запрос для обновления данных водителя
            $updateSql = "UPDATE Drivers SET $field = '$newValue' WHERE DriverID = $driverID";

            if ($conn->query($updateSql) === TRUE) {
                echo "Данные успешно обновлены.";
                header("Location: admin_aboutDriver.php?id=$driverID");
            } else {
                echo "Ошибка при обновлении данных: " . $conn->error;
            }
        } else {
            echo "Поле LastTestDate не может быть изменено непосредственно.";
        }
    }

    // SQL-запрос для получения данных о водителе и его последнем прохождении теста
    $sql = "SELECT Drivers.*, DriverTests.DateCompleted AS LastTestDate
            FROM Drivers
            LEFT JOIN DriverTests ON Drivers.DriverID = DriverTests.DriverID
            WHERE Drivers.DriverID = $driverID
            ORDER BY DriverTests.DateCompleted DESC
            LIMIT 1";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $value = $row[$field];
        $lastTestDate = $row['LastTestDate'];

        // Функция для определения типа поля ввода в зависимости от названия поля
        function getInputType($fieldName, $value, $conn, $driverID, $lastTestDate) {
            switch ($fieldName) {
                case 'date_of_birth':
                case 'date_of_license_issue':
                case 'registration_date':
                case 'LastTestDate':
                    return '<input type="date" class="form-control" id="' . $fieldName . '" name="' . $fieldName . '" value="' . ($fieldName === 'LastTestDate' ? $lastTestDate : $value) . '">';
                case 'gender':
                    return '<input type="radio" id="male" name="gender" value="Male" ' . ($value === 'Male' ? 'checked' : '') . '> <label for="male">Мужчина</label> <input type="radio" id="female" name="gender" value="Female" ' . ($value === 'Female' ? 'checked' : '') . '> <label for="female">Женщина</label>' ;
               
                default:
                    return '<input type="text" class="form-control" id="' . $fieldName . '" name="' . $fieldName . '" value="' . $value . '">';
            }
        }
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    Редактирование данных водителя
                </div>
                <div class="card-body">
                    <form action="" method="POST">
                        <div class="form-group">
                            <label for="<?php echo $field; ?>"><?php echo ucfirst(str_replace('_', ' ', $field)); ?></label>
                            <?php echo getInputType($field, $value, $conn, $driverID, $lastTestDate); ?>
                        </div>
                        <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                        <a href="javascript:history.back()" class="btn btn-secondary">Назад</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../footer.php'?>;
<?php
    } else {
        echo "Нет данных о водителе с ID: " . $driverID;
    }
} else {
    echo "Не передан ID водителя или название поля в параметрах запроса.";
}

$conn->close();
?>
