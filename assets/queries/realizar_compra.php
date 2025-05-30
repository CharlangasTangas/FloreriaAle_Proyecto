<?php
include '../../config/connection.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Método inválido");
    }

    $idProveedor = $_POST['idProveedor'];
    $fecha = $_POST['date'];
    $metodoPago = $_POST['payment_method'];
    $comentarios = $_POST['notes'];

    // Calcular subtotal, iva y total
    $subtotal = 0;
    foreach ($_POST['subtotal'] as $sub) {
        $subtotal += floatval($sub);
    }
    $iva = round($subtotal * 0.16, 2);
    $total = round($subtotal + $iva, 2);

    $connection->begin_transaction();

    // Insertar en tabla Compra
    $sqlCompra = "INSERT INTO Compra (idProveedor, subtotal, iva, total, fechaCompra, metodoPago, comentario) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $connection->prepare($sqlCompra);
    $stmt->bind_param("idddsss", $idProveedor, $subtotal, $iva, $total, $fecha, $metodoPago, $comentarios);
    $stmt->execute();
    $idCompra = $stmt->insert_id;

    // Insertar detalles
    $productos = $_POST['product'];
    $costos = $_POST['cost'];
    $cantidades = $_POST['quantity'];
    $subtotales = $_POST['subtotal'];

    $sqlDetalle = "INSERT INTO DetalleCompra (idCompra, idProducto, precioUnitario, cantidad, subtotal)
                   VALUES (?, ?, ?, ?, ?)";
    $stmtDetalle = $connection->prepare($sqlDetalle);

    for ($i = 0; $i < count($productos); $i++) {
        $idProducto = $productos[$i];
        $precio = $costos[$i];
        $cantidad = $cantidades[$i];
        $sub = $subtotales[$i];

        $stmtDetalle->bind_param("iiddi", $idCompra, $idProducto, $precio, $cantidad, $sub);
        $stmtDetalle->execute();

        // Actualizar stock del producto
        $sqlUpdate = "UPDATE Producto SET stock = stock + ? WHERE idProducto = ?";
        $stmtUpdate = $connection->prepare($sqlUpdate);
        $stmtUpdate->bind_param("ii", $cantidad, $idProducto);
        $stmtUpdate->execute();
    }

    $connection->commit();

    echo json_encode(["status" => "success"]);
} catch (Exception $e) {
    $connection->rollback();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
