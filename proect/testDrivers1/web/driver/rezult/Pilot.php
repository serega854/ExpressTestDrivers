<style>
    .table-container {
        max-height: 300px !important;
        overflow-y: auto !important;
    }
</style>
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

$driverID = $_SESSION['driver_id'] ?? null;

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

// Запрос для таблицы PassedPilot
$sql_query = "SELECT PassedPilotID, TestID, DateTimeCompleted, BestAttemptResult FROM PassedPilot WHERE DriverID = $driverID";

// Выполнение запроса и получение данных
$pilot_data = executeQuery($conn, $sql_query);

// Вывод данных
echo "<div class='container rez'>";
echo "<h3 class='text-center'>Ваши результаты Тестирования Пилот</h3>";
if (empty($pilot_data)) {
    echo "<p class='text-center'>Данные отсутствуют</p>";
} else {
    echo "<div class='table-responsive table-container'>";
    echo "<table class='table table-bordered'>";
    echo "<thead>";
    echo "<tr>";
    foreach ($pilot_data[0] as $column_name => $value) {
        if ($column_name !== 'PassedPilotID') {
            echo "<th>$column_name</th>";
        }
    }
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    foreach ($pilot_data as $row) {
        echo "<tr>";
        foreach ($row as $column_name => $value) {
            if ($column_name !== 'PassedPilotID') {
                echo "<td>$value</td>";
            }
        }
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
    echo "</div>";
    
    // Графики
    echo "<div class='chart-container'>";
    // Первый график - динамика результатов по тестированию пилот
    echo "<canvas id='pilot-test-dynamics-chart' width='400' height='200'></canvas>";
    // Второй график - общая динамика результатов по тестированию пилот
    echo "<canvas id='overall-pilot-test-dynamics-chart' width='400' height='200'></canvas>";
    // Третий график - индивидуальный график пользователя
    echo "<canvas id='individual-pilot-test-chart' width='400' height='200'></canvas>";
    echo "</div>";
}
echo "</div>";

// Запрос для получения среднеарифметического результата по тесту пилот для всех пользователей за каждый день и время
$average_query = "SELECT DateTimeCompleted, AVG(BestAttemptResult) AS AverageResult FROM PassedPilot GROUP BY DateTimeCompleted ORDER BY DateTimeCompleted";

// Выполнение запроса и получение данных
$average_data = executeQuery($conn, $average_query);

// Форматирование данных для графика общей динамики результатов
$averageDates = array_column($average_data, 'DateTimeCompleted');
$averageResults = array_column($average_data, 'AverageResult');

// График: общая динамика результатов по тестированию пилот
echo "<script>";
echo "var overallPilotTestDynamicsChartCtx = document.getElementById('overall-pilot-test-dynamics-chart').getContext('2d');";
echo "var overallPilotTestDynamicsChart = new Chart(overallPilotTestDynamicsChartCtx, {";
echo "    type: 'line',";
echo "    data: {";
echo "        labels: " . json_encode($averageDates) . ",";
echo "        datasets: [{";
echo "            label: 'Результат по тесту пилот среди всех пользователей',";
echo "            data: " . json_encode($averageResults) . ",";
echo "            borderColor: 'rgba(255, 99, 132, 1)',";
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
$individualDates = array_column($pilot_data, 'DateTimeCompleted');
$individualResults = array_column($pilot_data, 'BestAttemptResult');

// График: индивидуальный график результатов по тестированию пилот для пользователя
echo "<script>";
echo "var individualPilotTestChartCtx = document.getElementById('individual-pilot-test-chart').getContext('2d');";
echo "var individualPilotTestChart = new Chart(individualPilotTestChartCtx, {";
echo "    type: 'line',";
echo "    data: {";
echo "        labels: " . json_encode($individualDates) . ",";
echo "        datasets: [{";
echo "            label: 'Результаты по тесту пилот',";
echo "            data: " . json_encode($individualResults) . ",";
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