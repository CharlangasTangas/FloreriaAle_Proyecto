
<?php
include '../../config/connection.php';
header('Content-Type: application/json');

$idProveedor = $_POST['idProveedor'] ?? null;
$idEmpleado = $_POST['idEmpleado'] ?? null;
$fecha = $_POST['date'] ?? date('Y-m-d');
$metodoPago = $_POST['payment_method'] ?? 'Cash';
$comentarios = $_POST['notes'] ?? '';
$total = $_POST['grand_total_purchase'] ?? 0;
$estado = 'Completed';

$productIds = $_POST['product'] ?? [];
$quantities = $_POST['quantity'] ?? [];
$costs = $_POST['cost'] ?? [];

if (!$idEmpleado || empty($productIds)) {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
    exit;
}

$connection->begin_transaction();

try {
    $stmt = $connection->prepare("INSERT INTO Compra (idProveedor, idEmpleado, fechaCompra, metodoPago, comentarios, total, estado) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iisssds", $idProveedor, $idEmpleado, $fecha, $metodoPago, $comentarios, $total, $estado);
    $stmt->execute();

    $idCompra = $stmt->insert_id;

    $stmtDetalle = $connection->prepare("INSERT INTO DetalleCompra (idCompra, idProducto, cantidad, precioUnitario, subtotal) VALUES (?, ?, ?, ?, ?)");

    foreach ($productIds as $index => $idProducto) {
        $cantidad = $quantities[$index];
        $precioUnitario = $costs[$index];
        $subtotal = $cantidad * $precioUnitario;

        $stmtDetalle->bind_param("iiidd", $idCompra, $idProducto, $cantidad, $precioUnitario, $subtotal);
        $stmtDetalle->execute();

        $stmtStock = $connection->prepare("UPDATE Producto SET stock = stock + ? WHERE idProducto = ?");
        $stmtStock->bind_param("ii", $cantidad, $idProducto);
        $stmtStock->execute();
    }

    $connection->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $connection->rollback();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
