<?php
    $host = 'localhost';
    $db   = 'timetracker'; 
    $user = 'root'; 
    $pass = 'root'; 
    $charset = 'utf8mb4';

    $conn = mysqli_connect($host, $user, $pass, $db);

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $opt = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, $user, $pass, $opt);
?>
