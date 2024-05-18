<?php
    include 'navbar.php'
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль водителя</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Профиль водителя</div>
                    <div class="card-body">
                        <?php
                        // Подключение к базе данных
                        include '../../vendor/connect.php';

                        session_start();

                        // Проверка наличия ID водителя в сессии
                        if(isset($_SESSION['driver_id'])) {
                            $driverID = intval($_SESSION['driver_id']);

                            // SQL запрос для получения данных о водителе, включая рост и вес
                            $sql = "SELECT d.*, 
                                        GROUP_CONCAT(lc.category_description SEPARATOR ', ') AS license_categories,
                                        height, weight
                                    FROM Drivers d
                                    LEFT JOIN LicenseCategories lc ON d.DriverID = lc.DriverID
                                    WHERE d.DriverID = $driverID
                                    GROUP BY d.DriverID";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                $row = $result->fetch_assoc();
                                ?>
                                <p><strong>Имя:</strong> <?php echo $row['first_name']; ?></p>
                                <p><strong>Фамилия:</strong> <?php echo $row['last_name']; ?></p>
                                <p><strong>Отчество:</strong> <?php echo $row['middle_name']; ?></p>
                                <p><strong>Дата рождения:</strong> <?php echo $row['date_of_birth']; ?></p>
                                <p><strong>Пол:</strong> <?php echo $row['gender']; ?></p>
                                <p><strong>Email:</strong> <?php echo $row['Email']; ?></p>
                                <p><strong>Телефон:</strong> <?php echo $row['phone_number']; ?></p>
                                <p><strong>Дата выдачи лицензии:</strong> <?php echo $row['date_of_license_issue']; ?></p>
                                <p><strong>Дата регистрации:</strong> <?php echo $row['registration_date']; ?></p>
                                <p><strong>Категории водительских прав:</strong> <?php echo $row['license_categories']; ?></p>
                                <p><strong>Рост:</strong> <?php echo $row['height']; ?></p>
                                <p><strong>Вес:</strong> <?php echo $row['weight']; ?></p>
                                <?php
                            } else {
                                echo "Нет данных о водителе с ID: " . $driverID;
                            }
                        } else {
                            echo "Не найден ID водителя в сессии.";
                        }
                        // Закрываем соединение с базой данных
                        $conn->close();
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include '../footer.php'?>;
</body>
</html>
