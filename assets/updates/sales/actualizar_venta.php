<?php
include '../../../config/connection.php';
header('Content-Type: application/json');

$idVenta = $_POST['idVenta'] ?? null;
$fecha = $_POST['fecha'] ?? null;
$metodo = $_POST['metodo'] ?? null;
$estado = $_POST['estado'] ?? null;
$comentarios = $_POST['comentario'] ?? null;
$total = $_POST['total'] ?? null;
$productos = $_POST['productos'] ?? [];
$cantidades = $_POST['cantidades'] ?? [];
$precios = $_POST['precios'] ?? [];

if (!$idVenta || !$fecha || !$metodo || !$estado || !$total || empty($productos)) {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
    exit;
}

$connection->begin_transaction();

try {
    $sqlVenta = "UPDATE Venta SET fechaEmision = ?, metodoPago = ?, estado = ?, comentario = ?, total = ? WHERE idVenta = ?";
    $stmt = $connection->prepare($sqlVenta);
    $stmt->bind_param("ssssdi", $fecha, $metodo, $estado, $comentarios, $total, $idVenta);
    $stmt->execute();

    $connection->query("DELETE FROM DetalleVenta WHERE idVenta = $idVenta");

    // Guardar los nuevos productos en DetalleVenta con subtotal
    $sqlDetalle = "INSERT INTO DetalleVenta (idVenta, idProducto, cantidad, precioUnitario, subtotal) VALUES (?, ?, ?, ?, ?)";
    $stmtDetalle = $connection->prepare($sqlDetalle);

    for ($i = 0; $i < count($productos); $i++) {
        $idProducto = $productos[$i];
        $cantidad = $cantidades[$i];
        $precio = $precios[$i];
        $subtotal = $cantidad * $precio;

        $stmtDetalle->bind_param("iiidd", $idVenta, $idProducto, $cantidad, $precio, $subtotal);
        $stmtDetalle->execute();
    }

    $connection->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $connection->rollback();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
