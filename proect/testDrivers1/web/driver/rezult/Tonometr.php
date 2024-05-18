<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Результаты</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<?php

include '../../vendor/connect.php'; // Подключение к базе данных

// Проверяем, установлена ли переменная сессии для ID водителя
$driverID = $_SESSION['driver_id'] ?? null;

// Проверка, существует ли функция перед ее объявлением
if (!function_exists('executeQuery')) {
    // Функция для выполнения SQL-запроса и возврата результатов
    function executeQuery($conn, $sql)
    {
        $result = $conn->query($sql);
        $data = array();
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }
}

// Запрос для таблицы Passed_Tonometer
$sql_query = "SELECT PassedTonometerID, TestID, DateTimeCompleted, UpperPressure, LowerPressure,ImagePath  FROM Passed_Tonometer WHERE DriverID = $driverID";

// Выполнение запроса и получение данных
$tonometer_data = executeQuery($conn, $sql_query);

// Вывод данных
echo "<div class='container rez'>";
echo "<h3 class='text-center'>Ваши результаты Тестирования Тонометрии</h3>";
if (empty($tonometer_data)) {
    echo "<p class='text-center'>Данные отсутствуют</p>";
} else {
    echo "<div class='table-responsive table-container'>";
    echo "<table class='table table-bordered'>";
    echo "<thead>";
    echo "<tr>";
    foreach ($tonometer_data[0] as $column_name => $value) {
        if ($column_name !== 'PassedTonometerID' && $column_name !== 'ImagePath') {
            echo "<th>$column_name</th>";
        }
    }
    echo "<th>Изображение</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    foreach ($tonometer_data as $row) {
        echo "<tr>";
        foreach ($row as $column_name => $value) {
            if ($column_name !== 'PassedTonometerID' && $column_name !== 'ImagePath') {
                echo "<td>$value</td>";
            }
        }
        // Вывод изображения
        echo "<td><img src='{$row['ImagePath']}' alt='Tonometer Image' style='width: 100px; height: auto;'></td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
    echo "</div>";
    
    // Графики
    echo "<div class='chart-container'>";
    // Первый график - динамика результатов по тестированию тонометрии
    echo "<canvas id='tonometer-test-dynamics-chart' width='400' height='200'></canvas>";
    // Второй график - общая динамика результатов по тестированию тонометрии
    echo "<canvas id='overall-tonometer-test-dynamics-chart' width='400' height='200'></canvas>";
    // Третий график - индивидуальный график пользователя
    echo "<canvas id='individual-tonometer-test-chart' width='400' height='200'></canvas>";
    echo "</div>";
}
echo "</div>";

// Запрос для получения среднеарифметической силы рук для всех пользователей за каждый день и время
$average_query = "SELECT DateTimeCompleted, AVG(UpperPressure) AS AverageUpperPressure, AVG(LowerPressure) AS AverageLowerPressure FROM Passed_Tonometer GROUP BY DateTimeCompleted ORDER BY DateTimeCompleted";

// Выполнение запроса и получение данных
$average_data = executeQuery($conn, $average_query);

// Форматирование данных для графика общей динамики результатов
$averageDates = array_column($average_data, 'DateTimeCompleted');
$averageUpperPressure = array_column($average_data, 'AverageUpperPressure');
$averageLowerPressure = array_column($average_data, 'AverageLowerPressure');


// График: общая динамика результатов по тестированию тонометрии
echo "<script>";
echo "var overallTonometerTestDynamicsChartCtx = document.getElementById('overall-tonometer-test-dynamics-chart').getContext('2d');";
echo "var overallTonometerTestDynamicsChart = new Chart(overallTonometerTestDynamicsChartCtx, {";
echo "    type: 'line',";
echo "    data: {";
echo "        labels: " . json_encode($averageDates) . ",";
echo "        datasets: [{";
echo "            label: 'Среднее верхнее давление всех пользователей',";
echo "            data: " . json_encode($averageUpperPressure) . ",";
echo "            borderColor: 'rgba(255, 99, 132, 1)',";
echo "            borderWidth: 1,";
echo "            pointStyle: 'circle',";
echo "            fill: false";
echo "        }, {";
echo "            label: 'Среднее нижнее давление всех пользователей',";
echo "            data: " . json_encode($averageLowerPressure) . ",";
echo "            borderColor: 'rgba(54, 162, 235, 1)',";
echo "            borderWidth: 1,";
echo "            pointStyle: 'circle',";
echo "            fill: false";
echo "        }]";
echo "    },";
echo "    options: {";
echo "        scales: {";
echo "            xAxes: [{";
echo "                type: 'time',";
echo "                time: {";
echo "                    unit: 'day',";
echo "                    displayFormats: {";
echo "                        day: 'MMM D, YYYY HH:mm:ss'";
echo "                    }";
echo "                }";
echo "            }],";
echo "            yAxes: [{";
echo "                ticks: {";
echo "                    beginAtZero: true";
echo "                }";
echo "            }]";
echo "        }";
echo "    }";
echo "});";
echo "</script>";

// Форматирование данных для индивидуального графика пользователя
$individualDates = array_column($tonometer_data, 'DateTimeCompleted');
$individualUpperPressure = array_column($tonometer_data, 'UpperPressure');
$individualLowerPressure = array_column($tonometer_data, 'LowerPressure');

// График: индивидуальный график результатов пользователя
echo "<script>";
echo "var individualTonometerTestChartCtx = document.getElementById('individual-tonometer-test-chart').getContext('2d');";
echo "var individualTonometerTestChart = new Chart(individualTonometerTestChartCtx, {";
echo "    type: 'line',";
echo "    data: {";
echo "        labels: " . json_encode($individualDates) . ",";
echo "        datasets: [{";
echo "            label: 'Ваше верхнее давление',";
echo "            data: " . json_encode($individualUpperPressure) . ",";
echo "            borderColor: 'rgba(255, 99, 132, 1)',";
echo "            borderWidth: 1,";
echo "            fill: false";
echo "        }, {";
echo "            label: 'Ваше нижнее давление',";
echo "            data: " . json_encode($individualLowerPressure) . ",";
echo "            borderColor: 'rgba(54, 162, 235, 1)',";
echo "            borderWidth: 1,";
echo "            fill: false";
echo "        }]";
echo "    },";
echo "    options: {";
echo "        scales: {";
echo "            xAxes: [{";
echo "                type: 'time',";
echo "                time: {";
echo "                    unit: 'day',";
echo "                    displayFormats: {";
echo "                        day: 'MMM D, YYYY HH:mm:ss'";
echo "                    }";
echo "                }";
echo "            }],";
echo "            yAxes: [{";
echo "                ticks: {";
echo "                    beginAtZero: true";
echo "                }";
echo "            }]";
echo "        }";
echo "    }";
echo "});";
echo "</script>";
?>

</html>
