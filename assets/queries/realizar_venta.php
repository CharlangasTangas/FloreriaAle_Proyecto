<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);


include __DIR__ . "/../../config/connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['product']) || empty($_POST['product'])) {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => "No se recibieron productos."
        ]);
        exit();
    }

    $idCliente = !empty($_POST['idCliente']) ? intval($_POST['idCliente']) : null;
    $idEmpleado = isset($_POST['idEmpleado']) ? intval($_POST['idEmpleado']) : null;
    $productos = $_POST['product'];
    $cantidades = $_POST['quantity'];
    $precios = $_POST['price'];
    $stocks = $_POST['stock'];
    $metodoPago = isset($_POST['payment_method']) ? $_POST['payment_method'] : null;
    $estado = isset($_POST['status']) ? $_POST['status'] : null;
    $comentarios = !empty($_POST['notes']) ? strval($_POST['notes']) : null;

    if (!$idEmpleado) {
        http_response_code(403);
        echo json_encode([
            "status" => "error",
            "message" => "Empleado no identificado."
        ]);
        exit();
    }

    $subtotal = 0;
    foreach ($productos as $i => $idProducto) {
        $cantidad = intval($cantidades[$i]);

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

    foreach ($cantidades as $i => $cantidad) {
        $cantidad = intval($cantidad);
        $precio = floatval($precios[$i]);
        $subtotal += $cantidad * $precio;
    }

    $iva = $subtotal * 0.16;
    $total = $subtotal + $iva;
    $fecha = date('Y-m-d');

    $sqlVenta = "INSERT INTO Venta (idCliente, idEmpleado, subtotal, iva, total, fechaEmision, metodoPago, estado, comentario)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmtVenta = $connection->prepare($sqlVenta);
    $stmtVenta->bind_param("iiddsssss", $idCliente, $idEmpleado, $subtotal, $iva, $total, $fecha, $metodoPago, $estado, $comentarios);
    $stmtVenta->execute();
    $idVenta = $stmtVenta->insert_id;

    foreach ($productos as $i => $idProducto) {
        $cantidad = intval($cantidades[$i]);
        $precio = floatval($precios[$i]);
        $subtotalItem = $cantidad * $precio;

        $sqlDetalle = "INSERT INTO DetalleVenta (idVenta, idProducto, cantidad, precioUnitario, subtotal)
                       VALUES (?, ?, ?, ?, ?)";
        $stmtDetalle = $connection->prepare($sqlDetalle);
        $stmtDetalle->bind_param("iiidd", $idVenta, $idProducto, $cantidad, $precio, $subtotalItem);
        $stmtDetalle->execute();

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
