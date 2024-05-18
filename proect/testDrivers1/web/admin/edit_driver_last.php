<?php
session_start();

// Проверка наличия сессии администратора
if (!isset($_SESSION['admin_username'])) {
    header("Location: admin_login.php");
    exit();
}

include '../../vendor/connect.php';
include 'navbar.php';

// Проверяем, получены ли ID водителя через параметр запроса
if(isset($_GET['id'])) {
    $driverID = intval($_GET['id']);

    // Получаем текущую дату последнего теста водителя
    $getLastTestDateSql = "SELECT DateCompleted FROM DriverTests WHERE DriverID = $driverID ORDER BY DateCompleted DESC LIMIT 1";
    $lastTestDateResult = $conn->query($getLastTestDateSql);

    if ($lastTestDateResult->num_rows > 0) {
        $row = $lastTestDateResult->fetch_assoc();
        $lastTestDate = $row['DateCompleted'];
    } else {
        $lastTestDate = ''; // Если нет данных о тестах, установим пустую дату
    }

    // Обработка отправленной формы
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['last_test_date'])) {
            $newLastTestDate = $_POST['last_test_date']; // Получаем новую дату последнего теста из формы

            // Проверяем, была ли уже запись о тесте для данного водителя
            if ($lastTestDate === '') {
                // Если записи о тесте нет, вставляем новую запись с указанной датой
                $insertTestDateSql = "INSERT INTO DriverTests (DriverID, DateCompleted) VALUES ($driverID, '$newLastTestDate')";

                if ($conn->query($insertTestDateSql) === TRUE) {
                    echo "Дата последнего теста успешно добавлена.";
                    // После успешной вставки, можно выполнить дополнительные действия, например, перенаправление пользователя на другую страницу
                } else {
                    echo "Ошибка при добавлении даты последнего теста: " . $conn->error;
                }
            } else {
                // Если запись о тесте уже существует, обновляем существующую дату
                $updateLastTestDateSql = "UPDATE DriverTests SET DateCompleted = '$newLastTestDate' WHERE DriverID = $driverID";

                if ($conn->query($updateLastTestDateSql) === TRUE) {
                    echo "Дата последнего теста успешно обновлена.";
                    header("Location: admin_aboutDriver.php?id=$driverID");
                    // После успешного обновления, можно выполнить дополнительные действия, например, перенаправление пользователя на другую страницу
                } else {
                    echo "Ошибка при обновлении даты последнего теста: " . $conn->error;
                }
            }
        }
    }
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    Редактирование даты последнего теста водителя
                </div>
                <div class="card-body">
                    <form action="" method="POST">
                        <div class="form-group">
                            <label for="last_test_date">Дата последнего теста:</label>
                            <input type="date" class="form-control" id="last_test_date" name="last_test_date" value="<?php echo $lastTestDate; ?>">
                        </div>
                        <button type="submit" class="btn btn-primary">Сохранить</button>
              
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
    echo "Не передан ID водителя в параметре запроса.";
}

$conn->close();
?>
