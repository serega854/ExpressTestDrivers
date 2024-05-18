<?php
session_start();
include '../../vendor/connect.php';

$driverID = $_SESSION['driver_id'] ?? null;
$testID = 6; // Идентификатор теста

$sql_check_assignment = "SELECT * FROM AssignedTests WHERE DriverID = $driverID AND TestID = $testID";
$result_assignment = $conn->query($sql_check_assignment);


if ($result_assignment->num_rows == 0) {
    header("Location: ../driver/driver_index.php");
    exit();
}

// Получаем данные о массе, росте и дате рождения водителя
$sql = "SELECT weight, height, date_of_birth FROM Drivers WHERE DriverID = $driverID";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $driver_weight = $row['weight'];
    $driver_height = $row['height'];

    // Вычисляем возраст на основе даты рождения
    $date_of_birth = new DateTime($row['date_of_birth']);
    $today = new DateTime('today');
    $driver_age = $date_of_birth->diff($today)->y;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include '../../vendor/connect.php';

    $targetDir = "../../img_user/";
    $pulseImagePath = $targetDir . basename($_FILES['pulse_photo']['name']);
    $pressureImagePath = $targetDir . basename($_FILES['bp_photo']['name']);

    if (move_uploaded_file($_FILES['pulse_photo']['tmp_name'], $pulseImagePath) && move_uploaded_file($_FILES['bp_photo']['tmp_name'], $pressureImagePath)) {
        $sql = "INSERT INTO Passed_Baevsky (TestID, DriverID, DateTimeCompleted, AdaptabilityCoefficient, PulsePhoto, PressurePhoto) 
                VALUES (?, ?, NOW(), ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        // Преобразуем значения полей формы в числа с плавающей точкой
        $p1 = floatval($_POST['p1']);
        $p2 = floatval($_POST['p2']);
        $p3 = floatval($_POST['p3']);
        $p4 = floatval($_POST['p4']);
        $p5 = floatval($_POST['p5']);
        $p6 = floatval($_POST['p6']);

        // Вычисляем AdaptabilityCoefficient по формуле
        $adaptabilityCoefficient = (0.011 * $p1) + (0.014 * $p2) + (0.008 * $p3) + (0.009 * $p5) - (0.009 * $p6) + (0.014 * $p4) - 0.27;

        // Привязываем параметры к типу данных с плавающей точкой
        $stmt->bind_param("iidss", $testID, $driverID, $adaptabilityCoefficient, $pulseImagePath, $pressureImagePath);

        if ($stmt->execute()) {
            $delete_sql = "DELETE FROM AssignedTests WHERE DriverID = $driverID AND TestID = $testID";
            if ($conn->query($delete_sql) === true) {
                // Выводим AdaptabilityCoefficient в алерт
                echo "<script>alert('Адаптационный коэффициент: $adaptabilityCoefficient');</script>";
                header("Location: Baevskiy.php");
                exit();
            } else {
                echo "Ошибка при удалении записи: " . $conn->error;
            }
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        $stmt->close();
    } 

    $conn->close();
}
?>

<?php

include '../driver/navbar.php';
include '../footer.php' ?>

<!DOCTYPE html>
<html>
<head>
    <title>Расчет адаптационного потенциала по методике Баевского</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .test-baevskiy {
            width: 600px;
            margin: 0 auto;
            margin-bottom: 100px;
        }
        .form-control, .btn {
            max-width: 600px;
        }
        .image-container {
            display: flex;
            align-items: center;
        }
        .custom-file-input, .custom-file-label {
            height: 100px;
        }
        .image-container > * {
            margin-bottom: 15px;
        }
        .img-medic {
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <div class="container test-baevskiy">
        <h2>Расчет адаптационного потенциала по методике Баевского</h2>
       
        <form name="myform" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>Частота пульса (уд/мин):</label>
                <input type="number" name="p1" class="form-control" required>
            </div>
            <div class="form-group mt-3 image-container">
                <div>
                    <img src="../../img_site/9_ed67ce34a43d40c9b2beb18183858725 — копия.jpeg" alt="Camera Icon" class='img-medic' style="height: 150px; margin-right: 10px;">
                    <p>Пример фото</p>
                </div>
                <div class="custom-file" style="width: 70%;">
                    <input type="file" name="pulse_photo" class="custom-file-input" accept="image/*">
                    <label class="custom-file-label">Приложите фотографию результата на выданном вам пульсоксиметре</label>
                </div>
            </div>
            <div class="form-group mt-3">
                <label>Верхнее давление (мм рт. ст.):</label>
                <input type="number" name="p2" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Нижнее давление (мм рт. ст.):</label>
                <input type="number" name="p3" class="form-control" required>
            </div>
            <div class="form-group mt-3 image-container">
                <div>    
                    <img src="../../img_site/9_ed67ce34a43d40c9b2beb18183858725.jpeg" alt="Camera Icon" class='img-medic' style="height: 150px; margin-right: 10px;">
                    <p>Пример фото</p>
                </div>
                <div class="custom-file" style="width: 70%;">
                    <input type="file" name="bp_photo" class="custom-file-input" accept="image/*">
                    <label class="custom-file-label">Приложите фотографию результата на выданном вам пульсоксиметре</label>
                </div>
            </div>
            <div class="form-group mt-3">
                <label>Возраст (лет):</label>
                <input type="number" name="p4" class="form-control" value="<?= $driver_age; ?>" required>
            </div>
            <div class="form-group">
                <label>Масса тела (кг):</label>
                <input type="text" name="p5" class="form-control" value="<?= $driver_weight; ?>" required>
            </div>
            <div class="form-group">
                <label>Рост (см):</label>
                <input type="text" name="p6" class="form-control" value="<?= $driver_height; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary mt-3" id="calculateBtn">Рассчитать</button>
            <br><br>
           
        </form>
    </div>
</body>
</html>
<?php include '../footer.php';?>

<script>
// Обновляем метку с именем файла при выборе файла
document.addEventListener("DOMContentLoaded", function() {
    var fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(function(input) {
        input.addEventListener("change", function() {
            var fileName = this.files[0].name;
            var label = this.nextElementSibling;
            label.innerText = fileName;
        });
    });
});
</script>