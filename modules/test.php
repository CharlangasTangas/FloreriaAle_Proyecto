<?php

include_once "config/connection.php"; // Ajusta ruta si es necesario

//var_dump($connection); // Esto debe mostrar: object(mysqli)...

if ($connection && !$connection->connect_error) {
    echo "Tas conectado";
} else {
    echo "Error de conexión: " . $connection->connect_error;
}
?>
