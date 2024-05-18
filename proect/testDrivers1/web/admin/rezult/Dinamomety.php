<style>
    .table-container {
        max-height: 300px;
        overflow-y: auto;
    }
</style>
<?php

include '../../vendor/connect.php'; // Подключение к базе данных

session_start();

$driverID = $_SESSION['iduser'];

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

// Запрос для таблицы Passed_Dynamometry
$sql_query = "SELECT PassedDynamometryID, TestID, DateTimeCompleted, LeftHandStrength, RightHandStrength, LeftPhoto, RightPhoto FROM Passed_Dynamometry WHERE DriverID = $driverID";

// Выполнение запроса и получение данных
$dynamometry_data = executeQuery($conn, $sql_query);

// Вывод данных
echo "<div class='container rez'>";
echo "<h3 class='text-center'>Ваши результаты Тестирования Динамометрии</h3>";
if (empty($dynamometry_data)) {
    echo "<p class='text-center'>Данные отсутствуют</p>";
} else {
    echo "<div class='table-responsive table-container'>";
    echo "<table class='table table-bordered'>";
    echo "<thead>";
    echo "<tr>";
    foreach ($dynamometry_data[0] as $column_name => $value) {
        if ($column_name !== 'PassedDynamometryID' && $column_name !== 'LeftPhoto' && $column_name !== 'RightPhoto') {
            echo "<th>$column_name</th>";
        }
    }
    echo "<th>Left Photo</th>";
    echo "<th>Right Photo</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    foreach ($dynamometry_data as $row) {
        echo "<tr>";
        foreach ($row as $column_name => $value) {
            if ($column_name !== 'PassedDynamometryID' && $column_name !== 'LeftPhoto' && $column_name !== 'RightPhoto') {
                echo "<td>$value</td>";
            }
        }

        echo "<td><img src='{$row['LeftPhoto']}' alt='Left Hand Photo' style='width: 100px; height: auto;'></td>";
        echo "<td><img src='{$row['RightPhoto']}' alt='Right Hand Photo' style='width: 100px; height: auto;'></td>";
        
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
    echo "</div>";
    
    // Графики
    echo "<div class='chart-container'>";
    // Первый график - динамика результатов по тестированию динамометрии
    echo "<canvas id='dynamometry-test-dynamics-chart' width='400' height='200'></canvas>";
    // Второй график - общая динамика результатов по тестированию динамометрии
    echo "<canvas id='overall-dynamometry-test-dynamics-chart' width='400' height='200'></canvas>";
    // Третий график - индивидуальный график пользователя
    echo "<canvas id='individual-dynamometry-test-chart' width='400' height='200'></canvas>";
    echo "</div>";
}
echo "</div>";

// Запрос для получения среднеарифметической силы рук для всех пользователей за каждый день и время
// Запрос для получения среднеарифметической силы рук для всех пользователей за каждый день и время
$average_query = "SELECT DateTimeCompleted, AVG(LeftHandStrength) AS AverageLeftHandStrength, AVG(RightHandStrength) AS AverageRightHandStrength FROM Passed_Dynamometry GROUP BY DateTimeCompleted ORDER BY DateTimeCompleted";

// Выполнение запроса и получение данных
$average_data = executeQuery($conn, $average_query);

// Форматирование данных для графика общей динамики результатов
$averageDates = array_column($average_data, 'DateTimeCompleted');
$averageLeftHandStrength = array_column($average_data, 'AverageLeftHandStrength');
$averageRightHandStrength = array_column($average_data, 'AverageRightHandStrength');


// График: общая динамика результатов по тестированию динамометрии
echo "<script>";
echo "var overallDynamometryTestDynamicsChartCtx = document.getElementById('overall-dynamometry-test-dynamics-chart').getContext('2d');";
echo "var overallDynamometryTestDynamicsChart = new Chart(overallDynamometryTestDynamicsChartCtx, {";
echo "    type: 'line',";
echo "    data: {";
echo "        labels: " . json_encode($averageDates) . ",";
echo "        datasets: [{";
echo "            label: 'Средняя сила левой руки всех пользователей',";
echo "            data: " . json_encode($averageLeftHandStrength) . ",";
echo "            borderColor: 'rgba(255, 99, 132, 1)',";
echo "            borderWidth: 1,";
echo "            pointStyle: 'circle',";
echo "            fill: false";
echo "        }, {";
echo "            label: 'Средняя сила правой руки всех пользователей',";
echo "            data: " . json_encode($averageRightHandStrength) . ",";
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
$individualDates = array_column($dynamometry_data, 'DateTimeCompleted');
$individualLeftHandStrength = array_column($dynamometry_data, 'LeftHandStrength');
$individualRightHandStrength = array_column($dynamometry_data, 'RightHandStrength');

// График: индивидуальный график результатов по тестированию динамометрии для пользователя
echo "<script>";
echo "var individualDynamometryTestChartCtx = document.getElementById('individual-dynamometry-test-chart').getContext('2d');";
echo "var individualDynamometryTestChart = new Chart(individualDynamometryTestChartCtx, {";
echo "    type: 'line',";
echo "    data: {";
echo "        labels: " . json_encode($individualDates) . ",";
echo "        datasets: [{";
echo "            label: 'Ваша сила левой руки',";
echo "            data: " . json_encode($individualLeftHandStrength) . ",";
echo "            borderColor: 'rgba(255, 99, 132, 1)',";
echo "            borderWidth: 1,";
echo "            fill: false";
echo "        }, {";
echo "            label: 'Ваша сила правой руки',";
echo "            data: " . json_encode($individualRightHandStrength) . ",";
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
