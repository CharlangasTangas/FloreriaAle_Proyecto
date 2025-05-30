<?php
include '../../config/connection.php';
header('Content-Type: application/json');

$idVenta = $_GET['id'] ?? null;
if (!$idVenta) {
    echo json_encode(['error' => 'Falta ID de venta']);
    exit;
}

$sql = "
SELECT 
    v.idVenta,
    v.fechaEmision,
    v.total,
    v.estado,
    v.metodoPago,
    v.comentario,
    v.idCliente,
    c.nombre AS nombreCliente,
    e.idEmpleado,
    e.nombre AS nombreEmpleado,
    dv.idProducto,
    dv.cantidad,
    dv.precioUnitario,
    p.nombre AS nombreProducto,
    p.stock
FROM Venta v
LEFT JOIN Cliente c ON v.idCliente = c.idCliente
LEFT JOIN Empleado e ON v.idEmpleado = e.idEmpleado
LEFT JOIN DetalleVenta dv ON v.idVenta = dv.idVenta
LEFT JOIN Producto p ON dv.idProducto = p.idProducto
WHERE v.idVenta = $idVenta
";

$result = $connection->query($sql);
$venta = [];
$productos = [];

while ($row = $result->fetch_assoc()) {
    if (empty($venta)) {
        $venta = [
            'idVenta' => $row['idVenta'],
            'fechaEmision' => $row['fechaEmision'],
            'estado' => $row['estado'],
            'metodoPago' => $row['metodoPago'],
            'comentarios' => $row['comentario'],
            'idCliente' => $row['idCliente'],
            'nombreCliente' => $row['nombreCliente'] ?? 'Cliente no registrado',
            'idEmpleado' => $row['idEmpleado'],
            'productos' => []
        ];
    }

    $venta['productos'][] = [
        'idProducto' => $row['idProducto'],
        'nombreProducto' => $row['nombreProducto'],
        'cantidad' => $row['cantidad'],
        'precioUnitario' => $row['precioUnitario'],
        'stock' => $row['stock']
    ];
}

echo json_encode($venta);
?>
