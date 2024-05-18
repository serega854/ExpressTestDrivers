<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Результаты</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<style>
body{
    height: 1100vh;
}
</style>
<body>
<?php

include '../driver/navbar.php';
include '../../vendor/connect.php'; // Подключение к базе данных



include 'rezult/Pilot.php';
include 'rezult/Baevsky.php';
include 'rezult/Dinamomety.php';
include 'rezult/Pulsoksimetr.php';
include 'rezult/San.php';
include 'rezult/Shulte.php';
include 'rezult/Tonometr.php';
include 'rezult/Svetofor.php';


?>
</body>