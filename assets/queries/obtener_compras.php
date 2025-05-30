<?php
include '../../config/connection.php';

$sql = "
    SELECT 
        c.idCompra AS id,
        CONCAT(p.nombre, ' ', p.apellidoMaterno) AS supplier,
        c.fechaCompra AS date,
        c.total
    FROM Compra c
    JOIN Proveedor p ON c.idProveedor = p.idProveedor
    ORDER BY c.fechaCompra DESC
";

$result = $connection->query($sql);
$compras = [];

while ($row = $result->fetch_assoc()) {
    $compras[] = [
        'id' => $row['id'],
        'supplier' => $row['supplier'],
        'date' => $row['date'],
        'total' => number_format($row['total'], 2)
    ];
}

header('Content-Type: application/json');
echo json_encode($compras);
?>
