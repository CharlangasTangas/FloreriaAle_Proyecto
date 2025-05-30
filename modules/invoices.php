<?php
require_once 'config/connection.php';

$facturas = [];
$resultado = $connection->query("
    SELECT f.*, c.nombre, c.apellidoPaterno, c.apellidoMaterno
    FROM FacturaCliente f
    JOIN Cliente c ON f.idCliente = c.idCliente
");

$facturas = [];
while ($fila = $resultado->fetch_assoc()) {
    $facturas[] = $fila;
}


// Procesar emisión de factura
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['emitirFactura'])) {
    $idVenta = intval($_POST['idVenta']);

    // Obtener datos de la venta solo si está COMPLETADA
    $venta = $connection->query("SELECT * FROM Venta WHERE idVenta = $idVenta AND estado = 'Completed'")->fetch_assoc();

    if ($venta) {
        $stmt = $connection->prepare("INSERT INTO FacturaCliente (idVenta, idCliente, subtotal, iva, total, fechaEmision) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            'iiddds',
            $venta['idVenta'],
            $venta['idCliente'],
            $venta['subtotal'],
            $venta['iva'],
            $venta['total'],
            $venta['fechaEmision']
        );
        $stmt->execute();

        echo "<script>alert('Factura emitida exitosamente.'); window.location.href = window.location.href;</script>";
        exit;
    } else {
        echo "<script>alert('No se puede emitir factura para ventas pendientes o venta no encontrada.'); window.location.href = window.location.href;</script>";
        exit;
    }
}


// Función auxiliar para obtener ventas
function obtenerVentas($connection) {
    $ventas = [];
    $query = "
        SELECT Venta.idVenta, Venta.estado, Venta.subtotal, Venta.iva, Venta.total, Venta.fechaEmision,
               Cliente.idCliente, Cliente.nombre, Cliente.apellidoPaterno, Cliente.apellidoMaterno
        FROM Venta
        JOIN Cliente ON Venta.idCliente = Cliente.idCliente
        WHERE Venta.estado = 'Completed'
    ";
    $resultado = $connection->query($query);
    while ($fila = $resultado->fetch_assoc()) {
        $ventas[] = $fila;
    }
    return $ventas;
}


$ventasDisponibles = obtenerVentas($connection);

// Manejar AJAX para cargar datos de venta
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['cargarVenta'])) {
    $idVenta = intval($_GET['cargarVenta']);
    $venta = $connection->query("SELECT * FROM Venta WHERE idVenta = $idVenta")->fetch_assoc();
    $empleado = $connection->query("SELECT * FROM Empleado WHERE idEmpleado = {$venta['idEmpleado']}")->fetch_assoc();
    $detalles = $connection->query("SELECT * FROM DetalleVenta WHERE idVenta = $idVenta");

    if ($venta) {
        ob_start();
        ?>
        <p><strong>Cliente ID:</strong> <?= $venta['idCliente'] ?></p>
        <p><strong>Empleado:</strong> <?= "{$empleado['nombre']} {$empleado['apellidoPaterno']} {$empleado['apellidoMaterno']}" ?></p>
        <p><strong>Subtotal:</strong> $<?= number_format($venta['subtotal'], 2) ?></p>
        <p><strong>IVA:</strong> $<?= number_format($venta['iva'], 2) ?></p>
        <p><strong>Total:</strong> $<?= number_format($venta['total'], 2) ?></p>
        <p><strong>Fecha de Emisión:</strong> <?= $venta['fechaEmision'] ?></p>

        <h4 class="mt-4 font-semibold">Detalle de productos:</h4>
        <table class="w-full text-sm border mt-2">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border p-1">Producto</th>
                    <th class="border p-1">Cantidad</th>
                    <th class="border p-1">Precio Unitario</th>
                    <th class="border p-1">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($detalle = $detalles->fetch_assoc()): ?>
                    <tr>
                        <td class="border p-1"><?= $detalle['idProducto'] ?></td>
                        <td class="border p-1"><?= $detalle['cantidad'] ?></td>
                        <td class="border p-1">$<?= number_format($detalle['precioUnitario'], 2) ?></td>
                        <td class="border p-1">$<?= number_format($detalle['subtotal'], 2) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php
        echo ob_get_clean();
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Facturas - Florería Ale</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- jQuery (Select2 lo necesita) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
<body class="bg-gray-100 p-6">

    <div class="mb-6">
        <h1 class="text-3xl font-bold">Facturas</h1>
        <p class="text-gray-600">Facturación de Florería Ale</p>
    </div>

    <!-- Botón Emitir Factura -->
<div class="flex justify-end mb-4">
    <button onclick="openFacturaModal()" class="inline-flex items-center rounded-md bg-purple-700 px-4 py-2 text-white hover:bg-purple-800">
        Emitir Factura
    </button>
</div>
    <div class="bg-white p-4 rounded shadow">
       <table class="min-w-full">
    <thead>
        <tr class="bg-gray-100">
            <th class="text-left p-2">Factura #</th>
            <th class="text-left p-2">Cliente</th>
            <th class="text-left p-2">Fecha Emisión</th>
            <th class="text-left p-2">Venta ID</th>
            <th class="text-left p-2">Total</th>
            <th class="text-left p-2">Acciones</th> <!-- Nueva columna para el botón -->
        </tr>
    </thead>
    <tbody>
        <?php foreach ($facturas as $factura): ?>
        <tr class="border-b">
            <td class="p-2"><?= $factura['idFactura'] ?></td>
            <td class="p-2">
                <?= htmlspecialchars($factura['nombre'] . ' ' . $factura['apellidoPaterno'] . ' ' . $factura['apellidoMaterno']) ?>
            </td>
            <td class="p-2"><?= $factura['fechaEmision'] ?></td>
            <td class="p-2"><?= $factura['idVenta'] ?></td>
            <td class="p-2">$<?= number_format($factura['total'], 2) ?></td>
            <td class="p-2">
                <a href="modules/descargarFacturas.php?idFactura=<?= $factura['idFactura'] ?>" 
                   target="_blank"
                   class="bg-purple-600 text-white px-2 py-1 rounded hover:bg-purple-700">
                   Ver / Descargar PDF
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

    </div>

    <!-- Modal para emitir factura -->
<div id="facturaModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-3xl p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold">Emitir Factura</h3>
            <button onclick="closeFacturaModal()" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
        </div>

        <form method="POST" action="">
        
                <label for="idVenta" class="block font-medium mb-1">Seleccionar ID de Venta:</label>
                <select name="idVenta" id="idVenta" class="w-full border rounded px-3 py-2 mb-4">
                    <option value="">-- Seleccionar --</option>
                    <?php foreach ($ventasDisponibles as $venta): ?>
                        <option value="<?= $venta['idVenta'] ?>">
                            <?= $venta['idVenta'] ?> - <?= $venta['nombre'] . ' ' . $venta['apellidoPaterno'] . ' ' . $venta['apellidoMaterno'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>


            <div id="ventaDetalles" class="text-sm text-gray-700">
                <!-- Aquí se carga la información dinámica de la venta -->
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" name="emitirFactura" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Confirmar y Emitir
                </button>
            </div>
        </form>
    </div>
</div>


    <script>
function openFacturaModal() {
    document.getElementById('facturaModal').classList.remove('hidden');
}

function closeFacturaModal() {
    document.getElementById('facturaModal').classList.add('hidden');
    document.getElementById('ventaDetalles').innerHTML = '';
}

function cargarDatosVenta(idVenta) {
    if (!idVenta) return;

    const xhr = new XMLHttpRequest();
    xhr.open("GET", "facturas.php?cargarVenta=" + idVenta, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            document.getElementById("ventaDetalles").innerHTML = xhr.responseText;
        }
    };
    xhr.send();
}
</script>
<script>
document.getElementById('buscadorVenta').addEventListener('input', function () {
    const filtro = this.value.toLowerCase();
    const select = document.getElementById('idVenta');
    const opciones = select.querySelectorAll('option');

    opciones.forEach(option => {
        const texto = option.textContent.toLowerCase();
        if (texto.includes(filtro) || option.value === '') {
            option.style.display = '';
        } else {
            option.style.display = 'none';
        }
    });
});
</script>
<script>
$(document).ready(function() {
    $('#idVenta').select2({
        placeholder: "Buscar por ID o nombre del cliente",
        allowClear: true
    });
});
</script>



</body>
</html>
