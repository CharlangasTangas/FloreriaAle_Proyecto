<?php
include '../../config/connection.php';

$q = isset($_GET['q']) ? $connection->real_escape_string($_GET['q']) : '';

if (strlen($q) < 2) {
    exit;
}

$sql = "SELECT idProveedor, nombre, apellidoPaterno, apellidoMaterno FROM Proveedor WHERE nombre LIKE '%$q%' LIMIT 10";
$result = $connection->query($sql);

while ($row = $result->fetch_assoc()) {
    $id = $row['idProveedor'];
    $nombre = htmlspecialchars($row['nombre'] . ' ' . $row['apellidoPaterno'] . ' ' . $row['apellidoMaterno'], ENT_QUOTES, 'UTF-8');
    echo "<div class='opcion-proveedor cursor-pointer px-4 py-2 hover:bg-purple-100' data-id='$id'>$nombre</div>";
}
?>
