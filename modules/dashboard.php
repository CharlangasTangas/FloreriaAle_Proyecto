<<<<<<< HEAD
<<<<<<< Updated upstream
<div class="flex flex-col gap-6">
=======
=======
>>>>>>> compras
<?php
require_once 'config/connection.php';

$mesActual = date('m');
$anioActual = date('Y');

$ventasMes = $connection->query("
    SELECT SUM(total) AS total_ventas 
    FROM Venta 
    WHERE MONTH(fechaEmision) = $mesActual AND YEAR(fechaEmision) = $anioActual
")->fetch_assoc()['total_ventas'] ?? 0;

$comprasMes = $connection->query("
    SELECT SUM(total) AS total_compras 
    FROM Compra 
    WHERE MONTH(fechaCompra) = $mesActual AND YEAR(fechaCompra) = $anioActual
")->fetch_assoc()['total_compras'] ?? 0;

$empleadoTop = $connection->query("
    SELECT e.nombre, e.apellidoPaterno, e.apellidoMaterno, COUNT(v.idVenta) AS total_ventas
    FROM Venta v
    JOIN Empleado e ON v.idEmpleado = e.idEmpleado
    WHERE MONTH(v.fechaEmision) = $mesActual AND YEAR(v.fechaEmision) = $anioActual
    GROUP BY v.idEmpleado
    ORDER BY total_ventas DESC
    LIMIT 1
")->fetch_assoc();

$productoTop = $connection->query("
    SELECT p.nombre, SUM(dv.cantidad) AS total_vendido
    FROM DetalleVenta dv
    JOIN Venta v ON dv.idVenta = v.idVenta
    JOIN Producto p ON dv.idProducto = p.idProducto
    WHERE MONTH(v.fechaEmision) = $mesActual AND YEAR(v.fechaEmision) = $anioActual
    GROUP BY dv.idProducto
    ORDER BY total_vendido DESC
    LIMIT 1
")->fetch_assoc();
<<<<<<< Updated upstream
<<<<<<< HEAD
=======

>>>>>>> compras
=======
>>>>>>> Stashed changes
// Consulta ventas totales agrupadas por mes del año actual
$query = "
    SELECT MONTH(fechaEmision) AS mes, 
           SUM(total) AS totalVentas
    FROM Venta
    WHERE YEAR(fechaEmision) = YEAR(CURDATE())
    GROUP BY MONTH(fechaEmision)
    ORDER BY mes
";

$result = $connection->query($query);

$ventasPorMes = array_fill(1, 12, 0); // Inicializa todos los meses con 0

while ($row = $result->fetch_assoc()) {
    $mes = (int)$row['mes'];
    $ventasPorMes[$mes] = (float)$row['totalVentas'];
}

$connection->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 text-gray-900">
<div class="flex flex-col gap-6 max-w-7xl mx-auto px-4 py-8">
<<<<<<< HEAD
>>>>>>> Stashed changes
=======
>>>>>>> compras
    <div>
        <h2 class="text-2xl font-bold tracking-tight">Dashboard</h2>
        <p class="text-gray-500">Resumen del rendimiento de ventas y compras.</p>
    </div>

    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-lg border bg-white shadow">
            <div class="flex justify-between p-4 pb-2">
                <h3 class="text-sm font-medium">Ventas del Mes</h3>
                <i class="fas fa-dollar-sign text-gray-500"></i>
            </div>
            <div class="p-4 pt-0">
                <div class="text-2xl font-bold">$<?= number_format($ventasMes, 2) ?></div>
            </div>
        </div>
        <div class="rounded-lg border bg-white shadow">
            <div class="flex justify-between p-4 pb-2">
                <h3 class="text-sm font-medium">Compras del Mes</h3>
                <i class="fas fa-shopping-cart text-gray-500"></i>
            </div>
            <div class="p-4 pt-0">
                <div class="text-2xl font-bold">$<?= number_format($comprasMes, 2) ?></div>
            </div>
        </div>
        <div class="rounded-lg border bg-white shadow">
            <div class="flex justify-between p-4 pb-2">
                <h3 class="text-sm font-medium">Empleado del Mes</h3>
                <i class="fas fa-user-tie text-gray-500"></i>
            </div>
            <div class="p-4 pt-0">
                <?php if ($empleadoTop): ?>
                    <div class="text-lg font-medium">
                        <?= $empleadoTop['nombre'] . ' ' . $empleadoTop['apellidoPaterno'] ?>
                    </div>
                    <div class="text-sm text-gray-500">Ventas: <?= $empleadoTop['total_ventas'] ?></div>
                <?php else: ?>
                    <p>No hay ventas este mes.</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="rounded-lg border bg-white shadow">
            <div class="flex justify-between p-4 pb-2">
                <h3 class="text-sm font-medium">Producto más Vendido</h3>
                <i class="fas fa-box text-gray-500"></i>
            </div>
            <div class="p-4 pt-0">
                <?php if ($productoTop): ?>
                    <div class="text-lg font-medium"><?= $productoTop['nombre'] ?></div>
                    <div class="text-sm text-gray-500">Vendidos: <?= $productoTop['total_vendido'] ?></div>
                <?php else: ?>
                    <p>No hay ventas este mes.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-7">
        <div class="rounded-lg border bg-white shadow lg:col-span-7">
            <div class="flex justify-between p-4">
                <h3 class="font-medium">Ventas por Mes (Demo)</h3>
                <i class="fas fa-chart-line text-gray-500"></i>
            </div>
            <div class="p-4 pt-0">
                <canvas id="salesChart" class="h-[300px] w-full"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
    const ventasMes = <?php echo json_encode(array_values($ventasPorMes)); ?>;
</script>

<script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            datasets: [{
                label: 'Ventas',
                data: ventasMes,
                borderColor: 'rgba(59, 130, 246, 1)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

</body>
</html>
