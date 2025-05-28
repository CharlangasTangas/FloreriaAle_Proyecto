<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include __DIR__ . "../../config/connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productos = $_POST['product'];
    $cantidades = $_POST['quantity'];
    $precios = $_POST['price'];
    $stocks = $_POST['stock'];
    $metodoPago = $_POST['metodoPago'];
    $estado = $_POST['estado'];

    $idCliente = !empty($_POST['idCliente']) ? intval($_POST['idCliente']) : null;
    $idEmpleado = intval($_POST['idEmpleado']);

    $subtotal = 0;

    // Validar stock antes de hacer cambios
    foreach ($productos as $i => $idProducto) {
        $cantidad = intval($cantidades[$i]);

        // Consultar stock actual desde la base de datos (por si cambió)
        $sqlCheck = "SELECT stock FROM Producto WHERE idProducto = ?";
        $stmtCheck = $connection->prepare($sqlCheck);
        $stmtCheck->bind_param("i", $idProducto);
        $stmtCheck->execute();
        $result = $stmtCheck->get_result();
        $row = $result->fetch_assoc();

        if (!$row || $row['stock'] < $cantidad) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Stock insuficiente para el producto con ID $idProducto"
            ]);
            exit();
        }
    }

    // Calcular subtotal
    foreach ($cantidades as $i => $cantidad) {
        $cantidad = intval($cantidad);
        $precio = floatval($precios[$i]);
        $subtotal += $cantidad * $precio;
    }

    $iva = $subtotal * 0.16;
    $total = $subtotal + $iva;
    $fecha = date('Y-m-d');

    // Insertar venta
    $sqlVenta = "INSERT INTO Venta (idCliente, idEmpleado, subtotal, iva, total, fechaEmision, metodoPago, estado)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmtVenta = $connection->prepare($sqlVenta);
    $stmtVenta->bind_param("iiddsss", $idCliente, $idEmpleado, $subtotal, $iva, $total, $fecha, $metodoPago, $estado);
    $stmtVenta->execute();
    $idVenta = $stmtVenta->insert_id;

    // Insertar detalles y actualizar stock
    foreach ($productos as $i => $idProducto) {
        $cantidad = intval($cantidades[$i]);
        $precio = floatval($precios[$i]);
        $subtotalItem = $cantidad * $precio;

        // Insertar detalle
        $sqlDetalle = "INSERT INTO DetalleVenta (idVenta, idProducto, cantidad, precioUnitario, subtotal)
                       VALUES (?, ?, ?, ?, ?)";
        $stmtDetalle = $connection->prepare($sqlDetalle);
        $stmtDetalle->bind_param("iiidd", $idVenta, $idProducto, $cantidad, $precio, $subtotalItem);
        $stmtDetalle->execute();

        // Restar stock
        $sqlStock = "UPDATE Producto SET stock = stock - ? WHERE idProducto = ?";
        $stmtStock = $connection->prepare($sqlStock);
        $stmtStock->bind_param("ii", $cantidad, $idProducto);
        $stmtStock->execute();
    }

    echo json_encode([
        "status" => "success",
        "message" => "Venta realizada correctamente"
    ]);
    exit();
}
?>
