<?php
include '../../config/connection.php';
header('Content-Type: application/json');

// Consulta simplificada: solo los datos que muestra la tabla en la vista
$sql = "
    SELECT 
        v.idVenta AS id,
        v.fechaEmision AS date,
        v.total,
        v.estado AS status,
        CONCAT(c.nombre, ' ', c.apellidoMaterno) AS cliente,
        CONCAT(e.nombre, ' ', e.apellidoMaterno) AS empleado
    FROM Venta v
    LEFT JOIN Cliente c ON v.idCliente = c.idCliente
    LEFT JOIN Empleado e ON v.idEmpleado = e.idEmpleado
    ORDER BY v.idVenta DESC
";

$result = $connection->query($sql);

$ventas = [];

while ($row = $result->fetch_assoc()) {
    $ventas[] = [
        'id' => $row['id'],
        'date' => $row['date'],
        'total' => number_format((float)$row['total'], 2),
        'status' => $row['status'],
        'customer' => $row['cliente'] ?? '',
        'employee' => $row['empleado'] ?? 'Sin empleado'
    ];
}

echo json_encode($ventas);

?>