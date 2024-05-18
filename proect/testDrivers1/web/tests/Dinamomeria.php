<?php
// Start session
session_start();
include '../driver/navbar.php';

// Проверяем, назначено ли тестирование текущему пользователю
$driverID = $_SESSION['driver_id'] ?? null;
$testID = 5; // Идентификатор теста
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
    $leftImagePath = $targetDir . basename($_FILES['photo1']['name']); // Construct path for saving left image
    $rightImagePath = $targetDir . basename($_FILES['photo2']['name']); // Construct path for saving right image

    // Move uploaded files to the target directory
    if (move_uploaded_file($_FILES['photo1']['tmp_name'], $leftImagePath) && move_uploaded_file($_FILES['photo2']['tmp_name'], $rightImagePath)) {
      // Prepare SQL statement to insert data into database
      $sql = "INSERT INTO Passed_Dynamometry (TestID, DriverID, DateTimeCompleted, LeftHandStrength, RightHandStrength, LeftPhoto, RightPhoto) 
              VALUES (?, ?, NOW(), ?, ?, ?, ?)";

      // Prepare and bind parameters
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("iiisss", $testID, $driverID, $input1, $input2, $leftImagePath, $rightImagePath);

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
  <title>Кистевая динамометрия</title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .hidden {
      display: none;
    }

    img {
        width: 60%;
    }

    .img-conteiner {
        text-align: center;
    }
  </style>
</head>
<body>
  <div class="container mt-5">
    <h1>Кистевая динамометрия</h1>
    <p>На вытянутой руке сожмите выданный вам динамометр, после сфотографируйте результат прибора, после повторите с другой рукой, введите данные, приложите фотографии и нажмите отправить</p>
    
    <div class="row">
      <div class="col-md-6">
        <form id="form1" action="" method="POST" enctype="multipart/form-data">
          <div class="form-group">
            <label for="input1">Левая рука</label>
            <div class="input-group">
              <input type="number" class="form-control" id="input1" name="input1" required>
              <div class="input-group-append">
                <span class="input-group-text">кг</span>
              </div>
            </div>
            <input type="file" class="form-control-file mt-2" name="photo1" accept="image/*" required>
          </div>
        </div>
        <div class="col-md-6">
          <form id="form2" action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
              <label for="input2">Правая рука</label>
              <div class="input-group">
                <input type="number" class="form-control" id="input2" name="input2" required>
                <div class="input-group-append">
                  <span class="input-group-text">кг</span>
                </div>
              </div>
              <input type="file" class="form-control-file mt-2" name="photo2" accept="image/*" required>
            </div>
          </div>
        </div>
        
        <div class="row">
          <div class="col-md-6 img-conteiner">
            <img src="../../img_site/lleft.png" class="img-fluid" alt="Left Image">
          </div>
          <div class="col-md-6 img-conteiner">
            <img src="../../img_site/right.png" class="img-fluid" alt="Right Image">
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
  </div>

</body>
</html>
<?php include '../footer.php';?>