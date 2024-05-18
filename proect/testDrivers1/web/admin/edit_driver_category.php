<?php
session_start();

// Проверка наличия сессии администратора
if (!isset($_SESSION['admin_username'])) {
    header("Location: admin_login.php");
    exit();
}

include '../../vendor/connect.php';
include 'navbar.php';

// Проверяем, получен ли ID водителя через параметр запроса
if (isset($_GET['id'])) {
    // Преобразуем полученное значение ID водителя в целое число для безопасности
    $driverID = intval($_GET['id']);

    // Проверяем, была ли отправлена форма
    // Проверяем, была ли отправлена форма
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Проверяем, были ли выбраны категории
    if (isset($_POST['categories']) && is_array($_POST['categories'])) {
        // Удаляем предыдущие категории лицензии водителя
        $sqlDelete = "DELETE FROM LicenseCategories WHERE DriverID = ?";
        $stmtDelete = $conn->prepare($sqlDelete);
        $stmtDelete->bind_param("i", $driverID);
        $stmtDelete->execute();

        // Вставляем новые категории лицензии водителя
        $sqlInsert = "INSERT INTO LicenseCategories (category_description, DriverID) VALUES (?, ?)";
        $stmtInsert = $conn->prepare($sqlInsert);
        $stmtInsert->bind_param("si", $category, $driverID);
        foreach ($_POST['categories'] as $category) {
            $stmtInsert->execute();
        }

        // После обработки формы выполняем перенаправление
        header("Location: admin_aboutDriver.php?id=$driverID");
        exit(); // Добавляем exit() после отправки заголовка
    } else {
        // Если не выбраны категории, удаляем все категории
        $sqlDeleteAll = "DELETE FROM LicenseCategories WHERE DriverID = ?";
        $stmtDeleteAll = $conn->prepare($sqlDeleteAll);
        $stmtDeleteAll->bind_param("i", $driverID);
        $stmtDeleteAll->execute();

        // После обработки формы выполняем перенаправление
        header("Location: admin_aboutDriver.php?id=$driverID");
        exit(); // Добавляем exit() после отправки заголовка
    }
}


    // Получаем текущие категории лицензии водителя
    $sqlCategories = "SELECT category_description FROM LicenseCategories WHERE DriverID = ?";
    $stmt = $conn->prepare($sqlCategories);
    $stmt->bind_param("i", $driverID);
    $stmt->execute();
    $resultCategories = $stmt->get_result();
    $selectedCategories = [];
    if ($resultCategories->num_rows > 0) {
        while ($row = $resultCategories->fetch_assoc()) {
            $selectedCategories[] = $row['category_description'];
        }
    }

    // Массив с доступными категориями
    $categories = array(
        'легковой автомобиль',
        'грузовой автомобиль',
        'легковой автомобиль с прицепом',
        'грузовой автомобиль с прицепом'
    );
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Редактировать категорию лицензии водителя
                </div>
                <div class="card-body">
                    <form method="post" action="">
                        <div class="form-group">
                            <label>Выберите категории лицензии:</label><br>
                            <?php foreach ($categories as $category) { ?>
                                <?php
                                    $checked = '';
                                    foreach ($selectedCategories as $selectedCategory) {
                                        if ($selectedCategory === $category) {
                                            $checked = 'checked';
                                            break;
                                        }
                                    }
                                ?>
                                <input type="checkbox" id="<?php echo $category; ?>" name="categories[]" value="<?php echo $category; ?>" <?php echo $checked; ?>>
                                <label for="<?php echo $category; ?>"><?php echo $category; ?></label><br>
                            <?php } ?>
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

// Выводим текущие категории лицензии водителя
echo "<div class=\"container mt-5\">";
echo "<div class=\"row justify-content-center\">";
echo "<div class=\"col-md-6\">";
echo "<div class=\"card\">";
echo "<div class=\"card-header\">Текущие категории лицензии водителя</div>";
echo "<div class=\"card-body\">";

foreach ($selectedCategories as $category) {
    echo "<span class=\"badge badge-secondary mr-2\">$category</span>";
}

echo "</div>";
echo "</div>";
echo "</div>";
echo "</div>";
echo "</div>";

$conn->close();
?>
