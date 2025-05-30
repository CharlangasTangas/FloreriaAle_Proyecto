<?php
include '../../config/connection.php';

$idVenta = $_GET['id'] ?? null;

if (!$idVenta) {
  echo json_encode(null);
  exit;
}

$sql = "SELECT v.idVenta, v.fechaEmision, v.metodoPago, v.estado, v.total, v.comentarios,
               c.idCliente, c.nombre AS nombreCliente,
               e.idEmpleado, e.nombre AS nombreEmpleado
        FROM Venta v
        LEFT JOIN Cliente c ON v.idCliente = c.idCliente
        LEFT JOIN Empleado e ON v.idEmpleado = e.idEmpleado
        WHERE v.idVenta = ?";

$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $idVenta);
$stmt->execute();
$result = $stmt->get_result();
$venta = $result->fetch_assoc();

if (!$venta) {
  echo json_encode(null);
  exit;
}

$productos = [];
$sqlProductos = "SELECT dv.idProducto, p.nombre, dv.cantidad, p.precioVenta
                 FROM DetalleVenta dv
                 JOIN Producto p ON dv.idProducto = p.idProducto
                 WHERE dv.idVenta = ?";
$stmt2 = $connection->prepare($sqlProductos);
$stmt2->bind_param("i", $idVenta);
$stmt2->execute();
$result2 = $stmt2->get_result();

while ($row = $result2->fetch_assoc()) {
  $productos[] = $row;
}

$venta["productos"] = $productos;

echo json_encode($venta);
?>
