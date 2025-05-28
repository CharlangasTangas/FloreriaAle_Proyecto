<?php
require_once '<config\connection.php'; // Asegúrate de tener bien la ruta del archivo

// Obtener facturas existentes
$facturas = [];
$resultado = $connection->query("SELECT * FROM FacturaCliente");
while ($fila = $resultado->fetch_assoc()) {
    $facturas[] = $fila;
}

// Procesar emisión de factura
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['emitirFactura'])) {
    $idVenta = $_POST['idVenta'];

    // Obtener datos de la venta
    $venta = $connection->query("SELECT * FROM Venta WHERE idVenta = $idVenta")->fetch_assoc();
    if ($venta) {
        $stmt = $connection->prepare("INSERT INTO FacturaCliente (idVenta, idCliente, subtotal, iva, total, date, status) VALUES (?, ?, ?, ?, ?, ?, 'Pendiente')");
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
        header("Location: facturas.php");
        exit;
    }
}

// Función auxiliar para obtener ventas
function obtenerVentas($connection) {
    $ventas = [];
    $resultado = $connection->query("SELECT * FROM Venta");
    while ($fila = $resultado->fetch_assoc()) {
        $ventas[] = $fila;
    }
    return $ventas;
}
$ventasDisponibles = obtenerVentas($connection);
?>

<<<<<<< HEAD
<<<<<<< Updated upstream
<?php if ($view_invoice && $current_invoice): ?>
<!-- Invoice Detail View -->
<div class="flex flex-col gap-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold tracking-tight">Invoice #<?php echo $current_invoice['invoice_number']; ?></h2>
            <p class="text-gray-500">View and manage invoice details.</p>
        </div>
        <div class="flex gap-2">
            <button type="button" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium hover:bg-gray-50" onclick="window.print()">
                <i class="fas fa-print mr-2"></i> Print
            </button>
            <button type="button" class="inline-flex items-center rounded-md bg-blue-500 px-3 py-2 text-sm font-medium text-white hover:bg-blue-600">
                <i class="fas fa-file-pdf mr-2"></i> Download PDF
            </button>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Facturas - Florería Ale</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">

    <!-- Encabezado -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold">Facturas</h1>
        <p class="text-gray-600">Facturación de Florería Ale</p>

    </div>

    <!-- Botón Emitir Factura -->
    <div class="flex justify-end">
    <button onclick="openFacturaModal()" class="mb-4 inline-flex items-center rounded-md bg-purple-700 px-4 py-2 text-white hover:bg-purple-800">
        Emitir Factura
    </button>
    </div>


    <!-- Tabla de Facturas -->
    <div class="bg-white p-4 rounded shadow">
        <table class="min-w-full">
            <thead>
                <tr class="bg-gray-100">
                    <th class="text-left p-2">Factura #</th>
                    <th class="text-left p-2">Cliente</th>
                    <th class="text-left p-2">Fecha Emisión</th>
                    <th class="text-left p-2">Venta ID</th>
                    <th class="text-left p-2">Total</th>
                    <th class="text-left p-2">Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($facturas as $factura): ?>
                <tr class="border-b">
                    <td class="p-2"><?= $factura['id'] ?></td>
                    <td class="p-2"><?= $factura['idCliente'] ?></td>
                    <td class="p-2"><?= $factura['date'] ?></td>
                    <td class="p-2"><?= $factura['idVenta'] ?></td>
                    <td class="p-2">$<?= number_format($factura['total'], 2) ?></td>
                    <td class="p-2"><?= $factura['status'] ?></td>
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

            <form method="POST" action="facturas.php">
                <label for="idVenta" class="block font-medium mb-1">Seleccionar ID de Venta:</label>
                <select name="idVenta" id="idVenta" onchange="cargarDatosVenta(this.value)" class="w-full border rounded px-3 py-2 mb-4">
                    <option value="">-- Seleccionar --</option>
                    <?php foreach ($ventasDisponibles as $venta): ?>
                        <option value="<?= $venta['idVenta'] ?>"><?= $venta['idVenta'] ?></option>
                    <?php endforeach; ?>
                </select>

                <div id="ventaDetalles" class="text-sm text-gray-700">
                    <!-- Los detalles se mostrarán aquí -->
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" name="emitirFactura" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Confirmar y Emitir
                    </button>
                </div>
            </form>
        </div>

    </div>

    <!-- Botón Emitir Factura -->
    <div class="flex justify-end">
    <button onclick="openFacturaModal()" class="mb-4 inline-flex items-center rounded-md bg-purple-700 px-4 py-2 text-white hover:bg-purple-800">
        Emitir Factura
    </button>
    </div>


    <!-- Tabla de Facturas -->
    <div class="bg-white p-4 rounded shadow">
        <table class="min-w-full">
            <thead>
                <tr class="bg-gray-100">
                    <th class="text-left p-2">Factura #</th>
                    <th class="text-left p-2">Cliente</th>
                    <th class="text-left p-2">Fecha Emisión</th>
                    <th class="text-left p-2">Venta ID</th>
                    <th class="text-left p-2">Total</th>
                    <th class="text-left p-2">Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($facturas as $factura): ?>
                <tr class="border-b">
                    <td class="p-2"><?= $factura['id'] ?></td>
                    <td class="p-2"><?= $factura['idCliente'] ?></td>
                    <td class="p-2"><?= $factura['date'] ?></td>
                    <td class="p-2"><?= $factura['idVenta'] ?></td>
                    <td class="p-2">$<?= number_format($factura['total'], 2) ?></td>
                    <td class="p-2"><?= $factura['status'] ?></td>
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

            <form method="POST" action="facturas.php">
                <label for="idVenta" class="block font-medium mb-1">Seleccionar ID de Venta:</label>
                <select name="idVenta" id="idVenta" onchange="cargarDatosVenta(this.value)" class="w-full border rounded px-3 py-2 mb-4">
                    <option value="">-- Seleccionar --</option>
                    <?php foreach ($ventasDisponibles as $venta): ?>
                        <option value="<?= $venta['idVenta'] ?>"><?= $venta['idVenta'] ?></option>
                    <?php endforeach; ?>
                </select>

                <div id="ventaDetalles" class="text-sm text-gray-700">
                    <!-- Los detalles se mostrarán aquí -->
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

</body>
</html>

<?php
// Lógica AJAX para devolver detalles de venta si es una petición GET
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['cargarVenta'])) {
    $idVenta = intval($_GET['cargarVenta']);

    $venta = $connection->query("SELECT * FROM Venta WHERE idVenta = $idVenta")->fetch_assoc();
    $detalles = $connection->query("SELECT * FROM DetalleVenta WHERE idVenta = $idVenta");

    if ($venta):
        ?>
        <p><strong>Cliente:</strong> <?= $venta['idCliente'] ?></p>
        <p><strong>Empleado:</strong> <?= $venta['idEmpleado'] ?></p>
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
    endif;
    exit;
endif;
}
?>
