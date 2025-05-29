<?php
include '../../config/connection.php';

$q = $_GET['q'] ?? '';

$sql = $connection->prepare("SELECT idCliente, nombre, apellidoPaterno, apellidoMaterno FROM Cliente WHERE condicion = 1 AND CONCAT(nombre, ' ', apellidoPaterno, ' ', apellidoMaterno) LIKE CONCAT('%', ?, '%') LIMIT 5");
$sql->bind_param("s", $q);
$sql->execute();
$result = $sql->get_result();

while ($row = $result->fetch_assoc()) {
    $id = $row['idCliente'];
    $nombreCompleto = htmlspecialchars($row['nombre'] . ' ' . $row['apellidoPaterno'] . ' ' . $row['apellidoMaterno']);
    echo "<div class='opcion-cliente p-2 hover:bg-gray-200 cursor-pointer rounded-md border border-purple-100 px-3 py-2 focus:border-purple-500 focus:outline-none' data-id='$id'>$nombreCompleto</div>";
}
?>
