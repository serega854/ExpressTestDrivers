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

// Запрос для таблицы Passed_PulseOximetry
$sql_query = "SELECT PassedPulseOximetryID, TestID, DateTimeCompleted, PulseRate, BloodOxygenSaturation, ResultPhoto FROM Passed_PulseOximetry WHERE DriverID = $driverID";

// Выполнение запроса и получение данных
$pulse_oximetry_data = executeQuery($conn, $sql_query);

// Вывод данных
echo "<div class='container rez'>";
echo "<h3 class='text-center'>Ваши результаты Пульсоксиметрии</h3>";
if (empty($pulse_oximetry_data)) {
    echo "<p class='text-center'>Данные отсутствуют</p>";
} else {
    echo "<div class='table-responsive table-container'>";
    echo "<table class='table table-bordered'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>Дата и время</th>";
    echo "<th>Пульс, уд/мин</th>";
    echo "<th>Уровень кислорода в крови, %</th>";
    echo "<th>Фото результата</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    foreach ($pulse_oximetry_data as $row) {
        echo "<tr>";
        echo "<td>{$row['DateTimeCompleted']}</td>";
        echo "<td>{$row['PulseRate']}</td>";
        echo "<td>{$row['BloodOxygenSaturation']}</td>";
        echo "<td><img src='{$row['ResultPhoto']}' alt='Result Photo' style='width: 100px; height: auto;'></td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
    echo "</div>";
    
    // Графики
    echo "<div class='chart-container'>";
    // Первый график - динамика результатов по пульсоксиметрии
    echo "<canvas id='pulse-oximetry-dynamics-chart' width='400' height='200'></canvas>";
    // Второй график - общая динамика результатов по пульсоксиметрии
    echo "<canvas id='overall-pulse-oximetry-dynamics-chart' width='400' height='200'></canvas>";
    // Третий график - индивидуальный график пользователя
    echo "<canvas id='individual-pulse-oximetry-chart' width='400' height='200'></canvas>";
    echo "</div>";
}
echo "</div>";

// Запрос для получения среднеарифметической пульса и уровня кислорода в крови для всех пользователей за каждый день и время
$average_pulse_oximetry_query = "SELECT DateTimeCompleted, AVG(PulseRate) AS AveragePulseRate, AVG(BloodOxygenSaturation) AS AverageBloodOxygenSaturation FROM Passed_PulseOximetry GROUP BY DateTimeCompleted ORDER BY DateTimeCompleted";

// Выполнение запроса и получение данных
$average_pulse_oximetry_data = executeQuery($conn, $average_pulse_oximetry_query);

// Форматирование данных для графика общей динамики результатов пульсоксиметрии
$averagePulseOximetryDates = array_column($average_pulse_oximetry_data, 'DateTimeCompleted');
$averagePulseRate = array_column($average_pulse_oximetry_data, 'AveragePulseRate');
$averageBloodOxygenSaturation = array_column($average_pulse_oximetry_data, 'AverageBloodOxygenSaturation');

// График: общая динамика результатов по пульсоксиметрии
echo "<script>";
echo "var overallPulseOximetryDynamicsChartCtx = document.getElementById('overall-pulse-oximetry-dynamics-chart').getContext('2d');";
echo "var overallPulseOximetryDynamicsChart = new Chart(overallPulseOximetryDynamicsChartCtx, {";
echo "    type: 'line',";
echo "    data: {";
echo "        labels: " . json_encode($averagePulseOximetryDates) . ",";
echo "        datasets: [{";
echo "            label: 'Средний пульс всех пользователей',";
echo "            data: " . json_encode($averagePulseRate) . ",";
echo "            borderColor: 'rgba(255, 99, 132, 1)',";
echo "            borderWidth: 1,";
echo "            pointStyle: 'circle',";
echo "            fill: false";
echo "        }, {";
echo "            label: 'Средний уровень кислорода в крови всех пользователей',";
echo "            data: " . json_encode($averageBloodOxygenSaturation) . ",";
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
$individualPulseOximetryDates = array_column($pulse_oximetry_data, 'DateTimeCompleted');
$individualPulseRate = array_column($pulse_oximetry_data, 'PulseRate');
$individualBloodOxygenSaturation = array_column($pulse_oximetry_data, 'BloodOxygenSaturation');

// График: индивидуальный график результатов по пульсоксиметрии для пользователя
echo "<script>";
echo "var individualPulseOximetryChartCtx = document.getElementById('individual-pulse-oximetry-chart').getContext('2d');";
echo "var individualPulseOximetryChart = new Chart(individualPulseOximetryChartCtx, {";
echo "    type: 'line',";
echo "    data: {";
echo "        labels: " . json_encode($individualPulseOximetryDates) . ",";
echo "        datasets: [{";
echo "            label: 'Ваш пульс',";
echo "            data: " . json_encode($individualPulseRate) . ",";
echo "            borderColor: 'rgba(255, 99, 132, 1)',";
echo "            borderWidth: 1,";
echo "            fill: false";
echo "        }, {";
echo "            label: 'Ваш уровень кислорода в крови',";
echo "            data: " . json_encode($individualBloodOxygenSaturation) . ",";
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
