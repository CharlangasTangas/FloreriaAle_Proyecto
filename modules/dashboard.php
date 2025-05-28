<<<<<<< Updated upstream
<div class="flex flex-col gap-6">
=======
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
>>>>>>> Stashed changes
    <div>
        <h2 class="text-2xl font-bold tracking-tight">Dashboard</h2>
        <p class="text-gray-500">Overview of your store performance and activity.</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <!-- Revenue Card -->
        <div class="rounded-lg border bg-white shadow">
            <div class="flex flex-row items-center justify-between space-y-0 p-4 pb-2">
                <h3 class="text-sm font-medium">Total Revenue</h3>
                <i class="fas fa-dollar-sign text-gray-500 h-4 w-4"></i>
            </div>
            <div class="p-4 pt-0">
                <div class="text-2xl font-bold">$45,231.89</div>
                <p class="text-xs text-gray-500">
                    <span class="text-emerald-500 flex items-center">
                        <i class="fas fa-arrow-up mr-1"></i>
                        +20.1%
                    </span>
                    from last month
                </p>
            </div>
        </div>

        <!-- Sales Card -->
        <div class="rounded-lg border bg-white shadow">
            <div class="flex flex-row items-center justify-between space-y-0 p-4 pb-2">
                <h3 class="text-sm font-medium">Sales</h3>
                <i class="fas fa-shopping-cart text-gray-500 h-4 w-4"></i>
            </div>
            <div class="p-4 pt-0">
                <div class="text-2xl font-bold">+2,350</div>
                <p class="text-xs text-gray-500">
                    <span class="text-emerald-500 flex items-center">
                        <i class="fas fa-arrow-up mr-1"></i>
                        +12.2%
                    </span>
                    from last month
                </p>
            </div>
        </div>

        <!-- Users Card -->
        <div class="rounded-lg border bg-white shadow">
            <div class="flex flex-row items-center justify-between space-y-0 p-4 pb-2">
                <h3 class="text-sm font-medium">Active Users</h3>
                <i class="fas fa-users text-gray-500 h-4 w-4"></i>
            </div>
            <div class="p-4 pt-0">
                <div class="text-2xl font-bold">+573</div>
                <p class="text-xs text-gray-500">
                    <span class="text-emerald-500 flex items-center">
                        <i class="fas fa-arrow-up mr-1"></i>
                        +4.9%
                    </span>
                    from last month
                </p>
            </div>
        </div>

        <!-- Inventory Card -->
        <div class="rounded-lg border bg-white shadow">
            <div class="flex flex-row items-center justify-between space-y-0 p-4 pb-2">
                <h3 class="text-sm font-medium">Inventory</h3>
                <i class="fas fa-box text-gray-500 h-4 w-4"></i>
            </div>
            <div class="p-4 pt-0">
                <div class="text-2xl font-bold">12,234</div>
                <p class="text-xs text-gray-500">
                    <span class="text-red-500 flex items-center">
                        <i class="fas fa-arrow-down mr-1"></i>
                        -2.5%
                    </span>
                    from last month
                </p>
            </div>
        </div>
    </div>

    <!-- Charts and Tables -->
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-7">
        <!-- Sales Chart -->
        <div class="rounded-lg border bg-white shadow lg:col-span-4">
            <div class="flex flex-row items-center justify-between p-4">
                <h3 class="font-medium">Sales Overview</h3>
                <i class="fas fa-chart-bar text-gray-500 h-4 w-4"></i>
            </div>
            <div class="p-4 pt-0">
                <div class="h-[240px] w-full">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Sales -->
        <div class="rounded-lg border bg-white shadow lg:col-span-3">
            <div class="p-4">
                <h3 class="font-medium">Recent Sales</h3>
            </div>
            <div class="p-4 pt-0">
                <table class="w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="pb-2 text-left font-medium">Customer</th>
                            <th class="pb-2 text-right font-medium">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b">
                            <td class="py-2">John Smith</td>
                            <td class="py-2 text-right">$129.99</td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-2">Sarah Johnson</td>
                            <td class="py-2 text-right">$89.50</td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-2">Michael Brown</td>
                            <td class="py-2 text-right">$249.99</td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-2">Emily Davis</td>
                            <td class="py-2 text-right">$45.75</td>
                        </tr>
                        <tr>
                            <td class="py-2">Robert Wilson</td>
                            <td class="py-2 text-right">$199.99</td>
                        </tr>
                    </tbody>
                </table>
                <div class="flex items-center justify-end pt-4">
                    <a href="?page=sales" class="inline-flex items-center rounded-md border px-3 py-1 text-sm hover:bg-gray-100">
                        View All
                        <i class="fas fa-arrow-right ml-1 h-4 w-4"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Info Cards -->
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <!-- Low Stock Items -->
        <div class="rounded-lg border bg-white shadow">
            <div class="p-4">
                <h3 class="font-medium">Low Stock Items</h3>
            </div>
            <div class="p-4 pt-0">
                <table class="w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="pb-2 text-left font-medium">Product</th>
                            <th class="pb-2 text-right font-medium">Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b">
                            <td class="py-2">Printer Paper</td>
                            <td class="py-2 text-right text-red-500">5</td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-2">USB-C Cables</td>
                            <td class="py-2 text-right text-red-500">8</td>
                        </tr>
                        <tr>
                            <td class="py-2">Wireless Mouse</td>
                            <td class="py-2 text-right text-red-500">3</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Purchases -->
        <div class="rounded-lg border bg-white shadow">
            <div class="p-4">
                <h3 class="font-medium">Recent Purchases</h3>
            </div>
            <div class="p-4 pt-0">
                <table class="w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="pb-2 text-left font-medium">Product</th>
                            <th class="pb-2 text-right font-medium">Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b">
                            <td class="py-2">Laptop Chargers</td>
                            <td class="py-2 text-right">25</td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-2">Wireless Keyboards</td>
                            <td class="py-2 text-right">15</td>
                        </tr>
                        <tr>
                            <td class="py-2">HDMI Cables</td>
                            <td class="py-2 text-right">50</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Invoices -->
        <div class="rounded-lg border bg-white shadow">
            <div class="p-4">
                <h3 class="font-medium">Recent Invoices</h3>
            </div>
            <div class="p-4 pt-0">
                <table class="w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="pb-2 text-left font-medium">Invoice #</th>
                            <th class="pb-2 text-right font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b">
                            <td class="py-2">INV-001</td>
                            <td class="py-2 text-right">
                                <span class="rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-800">Paid</span>
                            </td>
                        </tr>
                        <tr class="border-b">
                            <td class="py-2">INV-002</td>
                            <td class="py-2 text-right">
                                <span class="rounded-full bg-yellow-100 px-2 py-1 text-xs font-medium text-yellow-800">Pending</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-2">INV-003</td>
                            <td class="py-2 text-right">
                                <span class="rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-800">Paid</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js for the sales chart -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('salesChart').getContext('2d');
        
        const salesData = {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Sales',
                data: [900, 1200, 1800, 2400, 2800, 3200, 3800, 4200, 4600, 5200, 5800, 6400],
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true
            }]
        };
        
        new Chart(ctx, {
            type: 'line',
            data: salesData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>