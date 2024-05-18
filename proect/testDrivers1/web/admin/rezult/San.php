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

// Запрос для таблицы Passed_SAN
$sql_query = "SELECT PassedSANID, TestID, DateTimeCompleted, SelfPerception, Activity, Mood FROM Passed_SAN WHERE DriverID = $driverID";

// Выполнение запроса и получение данных
$san_data = executeQuery($conn, $sql_query);

// Форматирование данных для индивидуального графика пользователя
$sanDates = array_column($san_data, 'DateTimeCompleted');
$sanSelfPerception = array_column($san_data, 'SelfPerception');
$sanActivity = array_column($san_data, 'Activity');
$sanMood = array_column($san_data, 'Mood');

// Вывод таблицы с результатами теста САН
echo "<div class='container rez'>";
echo "<h3 class='text-center'>Ваши результаты теста САН</h3>";
if (empty($san_data)) {
    echo "<p class='text-center'>Данные отсутствуют</p>";
} else {
    echo "<div class='table-responsive table-container'>";
    echo "<table class='table table-bordered'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>Дата и время</th>";
    echo "<th>Самовосприятие</th>";
    echo "<th>Активность</th>";
    echo "<th>Настроение</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    foreach ($san_data as $row) {
        echo "<tr>";
        echo "<td>{$row['DateTimeCompleted']}</td>";
        echo "<td>{$row['SelfPerception']}</td>";
        echo "<td>{$row['Activity']}</td>";
        echo "<td>{$row['Mood']}</td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
    echo "</div>";
}

// Создание графика для пользователя
echo "<div class='chart-container'>";
echo "<canvas id='individual-san-chart' width='400' height='200'></canvas>";
echo "</div>";

// Создание графика для всех пользователей
echo "<div class='chart-container'>";
echo "<canvas id='overall-san-chart' width='400' height='200'></canvas>";
echo "</div>";

// График: индивидуальный график пользователя
echo "<script>";
echo "var individualSanChartCtx = document.getElementById('individual-san-chart').getContext('2d');";
echo "var individualSanChart = new Chart(individualSanChartCtx, {";
echo "    type: 'line',";
echo "    data: {";
echo "        labels: " . json_encode($sanDates) . ",";
echo "        datasets: [";
echo "            {";
echo "                label: 'Самовосприятие',";
echo "                data: " . json_encode($sanSelfPerception) . ",";
echo "                borderColor: 'rgba(255, 99, 132, 1)',";
echo "                borderWidth: 1,";
echo "                fill: false";
echo "            },";
echo "            {";
echo "                label: 'Активность',";
echo "                data: " . json_encode($sanActivity) . ",";
echo "                borderColor: 'rgba(54, 162, 235, 1)',";
echo "                borderWidth: 1,";
echo "                fill: false";
echo "            },";
echo "            {";
echo "                label: 'Настроение',";
echo "                data: " . json_encode($sanMood) . ",";
echo "                borderColor: 'rgba(75, 192, 192, 1)',";
echo "                borderWidth: 1,";
echo "                fill: false";
echo "            }";
echo "        ]";
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

// Запрос для получения средних значений Mood, Activity и Self Perception для всех пользователей
$average_san_query = "SELECT DateTimeCompleted, AVG(SelfPerception) AS AverageSelfPerception, AVG(Activity) AS AverageActivity, AVG(Mood) AS AverageMood FROM Passed_SAN GROUP BY DateTimeCompleted ORDER BY DateTimeCompleted";

// Выполнение запроса и получение данных
$average_san_data = executeQuery($conn, $average_san_query);

// Форматирование данных для графика средних значений для всех пользователей
$averageSanDates = array_column($average_san_data, 'DateTimeCompleted');
$averageSanSelfPerception = array_column($average_san_data, 'AverageSelfPerception');
$averageSanActivity = array_column($average_san_data, 'AverageActivity');
$averageSanMood = array_column($average_san_data, 'AverageMood');

// График: график средних значений для всех пользователей
echo "<script>";
echo "var overallSanChartCtx = document.getElementById('overall-san-chart').getContext('2d');";
echo "var overallSanChart = new Chart(overallSanChartCtx, {";
echo "    type: 'line',";
echo "    data: {";
echo "        labels: " . json_encode($averageSanDates) . ",";
echo "        datasets: [";
echo "            {";
echo "                label: 'Среднее самовосприятие среди всех пользователей',";
echo "                data: " . json_encode($averageSanSelfPerception) . ",";
echo "                borderColor: 'rgba(255, 99, 132, 1)',";
echo "                borderWidth: 1,";
echo "                fill: false";
echo "            },";
echo "            {";
echo "                label: 'Средняя активность среди всех пользователей',";
echo "                data: " . json_encode($averageSanActivity) . ",";
echo "                borderColor: 'rgba(54, 162, 235, 1)',";
echo "                borderWidth: 1,";
echo "                fill: false";
echo "            },";
echo "            {";
echo "                label: 'Среднее настроение среди всех пользователей',";
echo "                data: " . json_encode($averageSanMood) . ",";
echo "                borderColor: 'rgba(75, 192, 192, 1)',";
echo "                borderWidth: 1,";
echo "                fill: false";
echo "            }";
echo "        ]";
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
