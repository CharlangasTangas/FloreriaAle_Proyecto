<?php
$host = 'switchback.proxy.rlwy.net';
$port = 17881;
$user = 'root';
$password = 'SOFeAfgtiulYSNhzdmXyWoPYBghRsyFY';
$database = 'floreria_db';

$connection = new mysqli($host, $user, $password, $database, $port);

/*
if ($connection->connect_error) {
    die('Conexión fallida: ' . $connection->connect_error);
}
echo "Conexión exitosa a Railway con mysqli.";
*/
?>