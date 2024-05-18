<style>
    .table-container {
        max-height: 300px;
        overflow-y: auto;
    }
</style>


<?php

include '../../vendor/connect.php'; // Подключение к базе данных

$driverID = $_SESSION['driver_id'] ?? null;

// Check if the function exists before declaring it
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

// Запрос для таблицы Passed_Baevsky
$sql_query = "SELECT PassedBaevskyID, TestID, DateTimeCompleted, AdaptabilityCoefficient, PulsePhoto, PressurePhoto  FROM Passed_Baevsky WHERE DriverID = $driverID";

// Выполнение запроса и получение данных
$baevsky_data = executeQuery($conn, $sql_query);

// Вывод данных
echo "<div class='container rez'>";
echo "<h3 class='text-center'>Ваши результаты Тестирования Адаптации Баевского</h3>";
if (empty($baevsky_data)) {
    echo "<p class='text-center'>Данные отсутствуют</p>";
} else {
    echo "<div class='table-responsive table-container'>";
    echo "<table class='table table-bordered'>";
    echo "<thead>";
    echo "<tr>";
    foreach ($baevsky_data[0] as $column_name => $value) {
        if ($column_name !== 'PassedBaevskyID' && $column_name !== 'PulsePhoto' && $column_name !== 'PressurePhoto') {
            echo "<th>$column_name</th>";
        }
    }
    echo "<th>Left Photo</th>";
    echo "<th>Right Photo</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    foreach ($baevsky_data as $row) {
        echo "<tr>";
        foreach ($row as $column_name => $value) {
            if ($column_name !== 'PassedBaevskyID' && $column_name !== 'PulsePhoto' && $column_name !== 'PressurePhoto') {
                echo "<td>$value</td>";
            }
        }

        echo "<td><img src='{$row['PulsePhoto']}' alt='Pulse Photo' style='width: 100px; height: auto;'></td>";
        echo "<td><img src='{$row['PressurePhoto']}' alt='Pressure Photo' style='width: 100px; height: auto;'></td>";
        
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
    echo "</div>";
    
    // Графики
    echo "<div class='chart-container'>";
    // Первый график - динамика результатов по тестированию адаптации Баевского
    echo "<canvas id='baevsky-test-dynamics-chart' width='400' height='200'></canvas>";
    // Второй график - общая динамика результатов по тестированию адаптации Баевского
    echo "<canvas id='overall-baevsky-test-dynamics-chart' width='400' height='200'></canvas>";
    // Третий график - индивидуальный график пользователя
    echo "<canvas id='individual-baevsky-test-chart' width='400' height='200'></canvas>";
    echo "</div>";
}
echo "</div>";

// Запрос для получения среднеарифметического коэффициента адаптации для всех пользователей за каждый день и время
$average_query = "SELECT DateTimeCompleted, AVG(AdaptabilityCoefficient) AS AverageCoefficient FROM Passed_Baevsky GROUP BY DateTimeCompleted ORDER BY DateTimeCompleted";

// Выполнение запроса и получение данных
$average_data = executeQuery($conn, $average_query);

// Форматирование данных для графика общей динамики результатов
$averageDates = array_column($average_data, 'DateTimeCompleted');
$averageCoefficients = array_column($average_data, 'AverageCoefficient');

// График: общая динамика результатов по тестированию адаптации Баевского
echo "<script>";
echo "var overallBaevskyTestDynamicsChartCtx = document.getElementById('overall-baevsky-test-dynamics-chart').getContext('2d');";
echo "var overallBaevskyTestDynamicsChart = new Chart(overallBaevskyTestDynamicsChartCtx, {";
echo "    type: 'line',";
echo "    data: {";
echo "        labels: " . json_encode($averageDates) . ",";
echo "        datasets: [{";
echo "            label: 'Коэффициент адаптации среди всех пользователей',";
echo "            data: " . json_encode($averageCoefficients) . ",";
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
$individualDates = array_column($baevsky_data, 'DateTimeCompleted');
$individualCoefficients = array_column($baevsky_data, 'AdaptabilityCoefficient');

// График: индивидуальный график результатов по тестированию адаптации Баевского для пользователя
echo "<script>";
echo "var individualBaevskyTestChartCtx = document.getElementById('individual-baevsky-test-chart').getContext('2d');";
echo "var individualBaevskyTestChart = new Chart(individualBaevskyTestChartCtx, {";
echo "    type: 'line',";
echo "    data: {";
echo "        labels: " . json_encode($individualDates) . ",";
echo "        datasets: [{";
echo "            label: 'Коэффициент адаптации',";
echo "            data: " . json_encode($individualCoefficients) . ",";
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
