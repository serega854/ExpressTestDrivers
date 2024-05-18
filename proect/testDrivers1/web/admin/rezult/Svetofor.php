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

// Запрос для таблицы Passed_Traffic_Lights_FalseStarts
$sql_query_false_starts = "SELECT DateTimeCompleted, Count, AverageTimeTaken FROM Passed_Traffic_Lights_FalseStarts WHERE DriverID = $driverID";

// Выполнение запроса и получение данных о фальшстартах
$false_starts_data = executeQuery($conn, $sql_query_false_starts);

// Запрос для получения среднего времени всех пользователей и общего количества фальшстартов
$sql_query_all_false_starts = "SELECT DateTimeCompleted, SUM(Count) AS TotalFalseStarts, AVG(AverageTimeTaken) AS AverageTimeTaken  FROM Passed_Traffic_Lights_FalseStarts GROUP BY DateTimeCompleted";

// Выполнение запроса и получение данных о фальшстартах всех пользователей
$all_false_starts_data = executeQuery($conn, $sql_query_all_false_starts);

// Подготовка данных для графика с фальшстартами пользователя и средним временем
$false_starts_dates = array_column($false_starts_data, 'DateTimeCompleted');
$false_starts_counts = array_column($false_starts_data, 'Count');
$average_times_taken = array_column($false_starts_data, 'AverageTimeTaken');

// Подготовка данных для графика с фальшстартами всех пользователей и средним временем
$all_false_starts_dates = array_column($all_false_starts_data, 'DateTimeCompleted');
$all_false_starts_counts = array_column($all_false_starts_data, 'TotalFalseStarts');
$all_average_times_taken = array_column($all_false_starts_data, 'AverageTimeTaken');


// Вывод таблицы результатов пользователя
echo "<div class='container'>";
echo "<h3 class='text-center'>Результаты тестирования Светофор</h3>";

if (empty($false_starts_data)) {
    echo "<p class='text-center'>Данные отсутствуют</p>";
} else {
    echo "<div class='table-responsive'>";
    echo "<table class='table table-bordered'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>Дата и время</th>";
    echo "<th>Количество фальшстартов</th>";
    echo "<th>Среднее время</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    foreach ($false_starts_data as $row) {
        echo "<tr>";
        echo "<td>{$row['DateTimeCompleted']}</td>";
        echo "<td>{$row['Count']}</td>";
        echo "<td>{$row['AverageTimeTaken']}</td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
    echo "</div>";
}

echo "</div>";

// График фальшстартов и среднего времени пользователя
echo "<div class='chart-container'>";
echo "<canvas id='user-false-starts-chart' width='400' height='200'></canvas>";
echo "</div>";

// График фальшстартов и среднего времени всех пользователей
echo "<div class='chart-container'>";
echo "<canvas id='all-users-false-starts-chart' width='400' height='200'></canvas>";
echo "</div>";

echo "</div>";

// Генерация скрипта для создания графика фальшстартов и среднего времени пользователя
echo "<script>";
echo "var userFalseStartsChartCtx = document.getElementById('user-false-starts-chart').getContext('2d');";
echo "var userFalseStartsChart = new Chart(userFalseStartsChartCtx, {";
echo "    type: 'line',";
echo "    data: {";
echo "        labels: " . json_encode($false_starts_dates) . ",";
echo "        datasets: [{";
echo "            label: 'Количество фальшстартов',";
echo "            data: " . json_encode($false_starts_counts) . ",";
echo "            borderColor: 'rgba(255, 99, 132, 1)',";
echo "            borderWidth: 1,";
echo "            fill: false";
echo "        }, {";
echo "            label: 'Среднее время',";
echo "            data: " . json_encode($average_times_taken) . ",";
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

// Генерация скрипта для создания графика фальшстартов и среднего времени всех пользователей
echo "<script>";
echo "var allUsersFalseStartsChartCtx = document.getElementById('all-users-false-starts-chart').getContext('2d');";
echo "var allUsersFalseStartsChart = new Chart(allUsersFalseStartsChartCtx, {";
echo "    type: 'line',";
echo "    data: {";
echo "        labels: " . json_encode($all_false_starts_dates) . ",";
echo "        datasets: [{";
echo "            label: 'Количество фальшстартов всех пользователей',";
echo "            data: " . json_encode($all_false_starts_counts) . ",";
echo "            borderColor: 'rgba(255, 99, 132, 1)',";
echo "            borderWidth: 1,";
echo "            fill: false";
echo "        }, {";
echo "            label: 'Среднее время для всех пользователей',";
echo "            data: " . json_encode($all_average_times_taken) . ",";
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
