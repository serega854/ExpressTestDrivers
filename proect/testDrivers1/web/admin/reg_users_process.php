<?php
session_start();

// Проверка наличия сессии администратора
if (!isset($_SESSION['admin_username'])) {
    header("Location: admin_login.php");
    exit();
}

// Подключение к базе данных
include '../../vendor/connect.php';

// Получение данных из формы
$email = $_POST['email'];
$password = $_POST['password'];
$last_name = $_POST['last_name'];
$first_name = $_POST['first_name'];
$middle_name = $_POST['middle_name'] ?? null;
$date_of_birth = $_POST['date_of_birth'];
$gender = $_POST['gender'];
$phone_number = $_POST['phone_number'] ?? null;
$date_of_license_issue = date('Y-m-d'); // Получаем текущую дату в формате ГГГГ-ММ-ДД
$registration_date = date('Y-m-d'); // Получаем текущую дату в формате ГГГГ-ММ-ДД
// Дополнительные данные
$weight = $_POST['weight'] ?? null;
$height = $_POST['height'] ?? null;
// Ассоциативный массив для сопоставления типа автомобиля и кодов категорий прав
$categories_mapping = array(
    '1' => array('b', 'легковой автомобиль'),
    '2' => array('c', 'грузовой автомобиль'),
    '3' => array('be', 'легковой автомобиль с прицепом'),
    '4' => array('ce', 'грузовой автомобиль с прицепом')
);

// Подготовка SQL-запроса для добавления водителя
$sql = "INSERT INTO Drivers (Email, Password, last_name, first_name, middle_name, date_of_birth, gender, phone_number, date_of_license_issue, registration_date, weight, height) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

// Проверяем успешность подготовки запроса
if (!$stmt) {
    // Если произошла ошибка, выводим сообщение об ошибке
    echo "Ошибка при подготовке запроса: " . $conn->error;
    exit();
}

// Привязываем параметры
$stmt->bind_param("ssssssssssss", $email, $password, $last_name, $first_name, $middle_name, $date_of_birth, $gender, $phone_number, $date_of_license_issue, $registration_date, $weight, $height);
// Выполнение запроса
if ($stmt->execute()) {
    // Если добавление прошло успешно, получаем ID только что добавленного водителя
    $driver_id = $stmt->insert_id;

    // Проверяем, выбраны ли какие-либо категории прав
    if(isset($_POST['license_categories']) && is_array($_POST['license_categories'])) {
        // Получаем выбранные категории прав
        $selected_categories = $_POST['license_categories'];

        // Подготовка SQL-запроса для добавления категорий прав в связанную таблицу
        $sql_insert_categories = "INSERT INTO LicenseCategories (DriverID, category_code, category_description) VALUES (?, ?, ?)";
        $stmt_insert_categories = $conn->prepare($sql_insert_categories);

        // Проверяем успешность подготовки запроса
        if (!$stmt_insert_categories) {
            // Если произошла ошибка, выводим сообщение об ошибке
            echo "Ошибка при подготовке запроса: " . $conn->error;
            exit();
        }

        // Привязываем параметры
        $stmt_insert_categories->bind_param("iss", $driver_id, $category_code, $category_description);

        // Привязываем категории прав к водителю
        foreach ($selected_categories as $category) {
            // Проверяем, есть ли выбранная категория в массиве $categories_mapping
            if (isset($categories_mapping[$category])) {
                // Получаем информацию о категории из массива $categories_mapping
                $category_info = $categories_mapping[$category];
                $category_code = $category_info[0];
                $category_description = $category_info[1];

                // Выполняем SQL-запрос для добавления категории прав
                $stmt_insert_categories->execute();
            } else {
                // Если категория не найдена в массиве $categories_mapping, выводим сообщение об ошибке
                echo "Ошибка: Не удалось найти информацию о категории прав.";
                exit();
            }
        }

        $stmt_insert_categories->close();
    }

    // Если все прошло успешно, перенаправляем на главную страницу администратора
    // Выводим значения выбранных категорий
    header('Location: admin_index.php');
    exit();
} else {
    // Если произошла ошибка, выводим сообщение об ошибке
    echo "Ошибка: " . $stmt->error;
}

// Закрываем соединение с базой данных
$stmt->close();
$conn->close();
?>