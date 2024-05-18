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

// Запрос для таблицы Passed_Shulte_Table
$sql_query = "SELECT PassedShulteTableID, TestID, DateTimeCompleted, Count FROM Passed_Shulte_Table WHERE DriverID = $driverID";

// Выполнение запроса и получение данных
$shulte_table_data = executeQuery($conn, $sql_query);

// Вывод данных
echo "<div class='container rez'>";
echo "<h3 class='text-center'>Ваши результаты теста Шульте</h3>";
if (empty($shulte_table_data)) {
    echo "<p class='text-center'>Данные отсутствуют</p>";
} else {
    echo "<div class='table-responsive table-container'>";
    echo "<table class='table table-bordered'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>Дата и время</th>";
    echo "<th>Количество</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    foreach ($shulte_table_data as $row) {
        echo "<tr>";
        echo "<td>{$row['DateTimeCompleted']}</td>";
        echo "<td>{$row['Count']}</td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
    echo "</div>";
    
    // Графики
    echo "<div class='chart-container'>";
    // График - индивидуальный график пользователя
    echo "<canvas id='individual-shulte-chart' width='400' height='200'></canvas>";
    // График - общая динамика результатов теста Шульте
    echo "<canvas id='overall-shulte-chart' width='400' height='200'></canvas>";
    echo "</div>";
}
echo "</div>";

// Запрос для получения среднего количества результатов теста Шульте для всех пользователей за каждый день и время
$average_shulte_query = "SELECT DateTimeCompleted, AVG(Count) AS AverageCount FROM Passed_Shulte_Table GROUP BY DateTimeCompleted ORDER BY DateTimeCompleted";

// Выполнение запроса и получение данных
$average_shulte_data = executeQuery($conn, $average_shulte_query);

// Форматирование данных для графика общей динамики результатов теста Шульте
$averageShulteDates = array_column($average_shulte_data, 'DateTimeCompleted');
$averageShulteCount = array_column($average_shulte_data, 'AverageCount');

// График: общая динамика результатов теста Шульте
echo "<script>";
echo "var overallShulteChartCtx = document.getElementById('overall-shulte-chart').getContext('2d');";
echo "var overallShulteChart = new Chart(overallShulteChartCtx, {";
echo "    type: 'line',";
echo "    data: {";
echo "        labels: " . json_encode($averageShulteDates) . ",";
echo "        datasets: [{";
echo "            label: 'Среднее количество всех пользователей',";
echo "            data: " . json_encode($averageShulteCount) . ",";
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
$individualShulteDates = array_column($shulte_table_data, 'DateTimeCompleted');
$individualShulteCount = array_column($shulte_table_data, 'Count');

// График: индивидуальный график результатов теста Шульте для пользователя
echo "<script>";
echo "var individualShulteChartCtx = document.getElementById('individual-shulte-chart').getContext('2d');";
echo "var individualShulteChart = new Chart(individualShulteChartCtx, {";
echo "    type: 'line',";
echo "    data: {";
echo "        labels: " . json_encode($individualShulteDates) . ",";
echo "        datasets: [{";
echo "            label: 'Ваш результат',";
echo "            data: " . json_encode($individualShulteCount) . ",";
echo "            borderColor: 'rgba(255, 99, 132, 1)',";
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