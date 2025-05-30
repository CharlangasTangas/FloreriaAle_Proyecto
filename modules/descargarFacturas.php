<?php
include '../config/connection.php';

$idFactura = $_GET['idFactura'] ?? null;

if (!$idFactura) {
    die("Factura no especificada.");
}

$queryFactura = "
SELECT f.*, c.nombre, c.apellidoPaterno, c.apellidoMaterno, c.RFC, v.fechaEmision AS fechaVenta
FROM FacturaCliente f
JOIN Cliente c ON f.idCliente = c.idCliente
JOIN Venta v ON f.idVenta = v.idVenta
WHERE f.idFactura = $idFactura
";

$factura = $connection->query($queryFactura)->fetch_assoc();

$queryDetalles = "
SELECT d.*, p.nombre AS producto
FROM DetalleVenta d
JOIN Producto p ON d.idProducto = p.idProducto
WHERE d.idVenta = {$factura['idVenta']}
";

$detalles = $connection->query($queryDetalles);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura #<?= $factura['idFactura'] ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 40px;
            background-image: url('ruta/a/tu/imagen/fondo.jpg'); /* Reemplaza con la ruta de tu imagen */
            background-size: cover;
        }
        .factura-container {
            width: 80%;
            margin: auto;
            border: 1px solid #ddd;
            padding: 20px;
            box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.2);
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
        }
        h2 {
            text-align: center;
            font-family: 'Georgia', serif;
            font-size: 32px;
            font-weight: bold;
            color: #A52A2A;
        }
        h4 {
            text-align: center;
            font-size: 18px;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        td, th {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #A52A2A;
            color: white;
            text-align: center;
        }
        .totales {
            text-align: right;
        }
        .centrado {
            text-align: center;
        }
        .info {
            margin-bottom: 10px;
            padding: 10px;
            background-color: #f8f8f8;
            border-radius: 5px;
            font-size: 16px;
        }
        .btn-print {
            margin-top: 20px;
            padding: 12px 20px;
            background-color: #A52A2A;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
        }
        .btn-print:hover {
            background-color: #8B0000;
        }
    </style>
</head>
<body>
    <div class="factura-container">
        <h2>Florería Ale</h2>
        <h4>Factura Fiscal</h4>

        <div class="info">
            <strong>Folio:</strong> <?= $factura['idFactura'] ?><br>
            <strong>Fecha:</strong> <?= $factura['fechaEmision'] ?><br>
            <strong>Cliente:</strong> <?= "{$factura['nombre']} {$factura['apellidoPaterno']} {$factura['apellidoMaterno']}" ?><br>
            <strong>RFC:</strong> <?= $factura['RFC'] ?>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $detalles->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['producto'] ?></td>
                        <td class="centrado"><?= $row['cantidad'] ?></td>
                        <td class="totales">$<?= number_format($row['precioUnitario'], 2) ?></td>
                        <td class="totales">$<?= number_format($row['subtotal'], 2) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <table style="margin-top: 20px;">
            <tr>
                <td class="totales" colspan="3"><strong>Subtotal:</strong></td>
                <td class="totales">$<?= number_format($factura['subtotal'], 2) ?></td>
            </tr>
            <tr>
                <td class="totales" colspan="3"><strong>IVA:</strong></td>
                <td class="totales">$<?= number_format($factura['iva'], 2) ?></td>
            </tr>
            <tr>
                <td class="totales" colspan="3"><strong>Total:</strong></td>
                <td class="totales">$<?= number_format($factura['total'], 2) ?></td>
            </tr>
        </table>

        <div class="centrado">
            <button class="btn-print" onclick="window.print()">Imprimir / Descargar PDF</button>
        </div>
    </div>
</body>
</html>

