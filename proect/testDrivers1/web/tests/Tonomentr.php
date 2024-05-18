<?php
// Start session
session_start();
include '../driver/navbar.php';

// Проверяем, назначено ли тестирование текущему пользователю
$driverID = $_SESSION['driver_id'] ?? null;
$testID = 9; // Идентификатор теста
include '../../vendor/connect.php'; // Подключаемся к базе данных

$sql_check_assignment = "SELECT * FROM AssignedTests WHERE DriverID = $driverID AND TestID = $testID";
$result_assignment = $conn->query($sql_check_assignment);

if ($result_assignment->num_rows == 0) {
    // Если тестирование не назначено текущему пользователю, перенаправляем на главную страницу
    
    header("Location: ../driver/driver_index.php");
    exit();
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include your database connection file
    include '../../vendor/connect.php';

    // Retrieve form data
    $input1 = $_POST['input1'];
    $input2 = $_POST['input2'];

    // Handle file upload
    $targetDir = "../../img_user/"; // Target directory for saving images
    $imagePath = $targetDir . basename($_FILES['photo2']['name']); // Construct path for saving image

    // Move uploaded file to the target directory
    if (move_uploaded_file($_FILES['photo2']['tmp_name'], $imagePath)) {
        // Prepare SQL statement to insert data into database
        $sql = "INSERT INTO Passed_Tonometer (TestID, DriverID, DateTimeCompleted, UpperPressure, LowerPressure, ImagePath) 
                VALUES (?, ?, NOW(), ?, ?, ?)";

        // Prepare and bind parameters
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiiss", $testID, $driverID, $input1, $input2, $imagePath);

        // Execute the statement
        if ($stmt->execute()) {
            // Data inserted successfully
            
            // Выполнение SQL-запроса для удаления записи из базы данных
            $delete_sql = "DELETE FROM AssignedTests WHERE DriverID = $driverID AND TestID = $testID";
            if ($conn->query($delete_sql) === true) {
                // Успешно удалено
            } else {
                echo "Ошибка при удалении записи: " . $conn->error;
            }
            
            header("Location: ../driver/driver_index.php"); // Redirect to driver index page
            exit(); // Terminate script execution after redirection
        } else {
            // Error in insertion
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        // Close statement
        $stmt->close();
    } 

    // Close connection
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Form</title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .hidden {
      display: none;
    }

    .img-fluid{
        width: 60%;
        margin-top: 40px;
    }

    .img-conteiner{
        margin: 0 auto;
        margin-right:20%;
    }
  </style>
</head>
<body>
  <div class="container mt-5">
    <h1>Проверка артериального давления</h1>
    <p>Измерьте давление на руке и сфотографируйте результат тонометра, после введите данные и приложите фотографию тонометра</p>
    
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="input1">Верхнее давление</label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="input1" name="input1" required>
                        <div class="input-group-append">
                            <span class="input-group-text">мм. рт. ст.</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="input2">Нижнее давление</label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="input2" name="input2" required>
                        <div class="input-group-append">
                            <span class="input-group-text">мм. рт. ст.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6 img-conteiner">
                <input type="file" class="form-control-file mt-2" name="photo2" accept="image/*" required>
                <img src="../../img_site/9_ed67ce34a43d40c9b2beb18183858725.jpeg" class="img-fluid" alt="Left Image">
                <p>Пример фото</p>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-md-12 text-center">
                <button type="submit" class="btn btn-primary" id="submitBtn">Отправить</button>
                <a class="btn btn-secondary hidden" id="exitBtn" href="../driver/driver_index.php">Выход</a>
                <p class="hidden" id="message">Данные отправлены</p>
            </div>
        </div>
    </form>
</div>


  <!--
  <script>
    $(document).ready(function() {
      $('#form1').submit(function() {
        $('#message').removeClass('hidden');
        $('#submitBtn').attr('disabled', true);
        $('#exitBtn').removeClass('hidden');
        return false;
      });

      $('#form2').submit(function() {
        $('#message').removeClass('hidden');
        $('#submitBtn').attr('disabled', true);
        $('#exitBtn').removeClass('hidden');
        return false;
      });
    });
  </script>-->
  
</body>
</html>
<?php include '../footer.php';?>