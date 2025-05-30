<?php
// Sample sales data - in a real application, this would come from a database
$sales = [
    ['id' => 1, 'invoice' => 'INV-001', 'customer' => 'John Smith', 'date' => '2023-04-15', 'total' => 129.99, 'status' => 'Completed'],
    ['id' => 2, 'invoice' => 'INV-002', 'customer' => 'Sarah Johnson', 'date' => '2023-04-16', 'total' => 89.50, 'status' => 'Completed'],
    ['id' => 3, 'invoice' => 'INV-003', 'customer' => 'Michael Brown', 'date' => '2023-04-18', 'total' => 249.99, 'status' => 'Completed'],
    ['id' => 4, 'invoice' => 'INV-004', 'customer' => 'Emily Davis', 'date' => '2023-04-20', 'total' => 45.75, 'status' => 'Pending'],
    ['id' => 5, 'invoice' => 'INV-005', 'customer' => 'Robert Wilson', 'date' => '2023-04-22', 'total' => 199.99, 'status' => 'Completed'],
];

include 'config/connection.php';
?>

<div class="flex flex-col gap-6">
    <div>
        <h2 class="text-2xl text-purple-950 font-bold tracking-tight">Administrador de Ventas</h2>
        <p class="text-gray-500">Crea y administra las transacciones de tus ventas.</p>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <!-- New Sale Form -->
        <div class="rounded-lg border bg-white shadow">
            <div class="p-4 border-b">
                <h3 class="font-medium text-purple-950">Crear venta</h3>
            </div>
            <div class="p-4">
                <form method="POST" id="sale-form">
                    <input type="hidden" name="action" value="create_sale">
                    
                    <div class="mb-4 grid grid-cols-2 gap-4">
                        <div class="relative">
                            <label for="customer" class="mb-1 block text-sm font-medium text-purple-800">Cliente</label>
                            <input type="text" id="customer" name="customer" autocomplete="off"
                                class="w-full rounded-md border border-purple-100 px-3 py-2 focus:border-purple-500 focus:outline-none"
                                placeholder="Nombre de Cliente">
                            <input type="hidden" id="idCliente" name="idCliente">

                            <div id="sugerencias"
                                class="absolute z-50 bg-white border border-purple-200 rounded-md mt-1 w-full max-h-48 overflow-y-auto hidden shadow-lg">
                            </div>
                        </div>


                        <div>
                            <label for="date" class="mb-1 block text-sm font-medium text-purple-800 ">Fecha</label>
                            <input type="date" id="date" name="date" class="w-full rounded-md border border-purple-100 px-3 py-2 focus:border-purple-500 focus:outline-none" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <!-- <label class="mb-1 block text-sm font-medium text-purple-800">Productos</label> -->
                        <div class="rounded-md border border-purple-100">
                            <table class="w-full" id="sale-items">
                                <thead>
                                    <tr class="border-b bg-purple-50">
                                        <th class="p-2 text-left text-sm font-medium text-purple-800">Producto</th>
                                        <th class="p-2 text-left text-sm font-medium text-purple-800">Precio</th>
                                        <th class="p-2 text-left text-sm font-medium text-purple-800">Cantidad</th>
                                        <th class="p-2 text-left text-sm font-medium text-purple-800">Total</th>
                                        <th class="p-2 text-left text-sm font-medium text-purple-800"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="border-b" id="item-row-template">
                                        <td class="p-2">
                                            <select name="product[]" class="product-select w-full rounded-md border border-purple-100 px-2 py-1 text-sm focus:border-purple-500 focus:outline-none">
                                                <option value="">Seleccionar</option>
                                                <!-- Cargar productos al SELECT -->
                                                <?php
                                                    $sql = "SELECT idProducto, nombre, stock, precioVenta FROM Producto";
                                                    $result = $connection->query($sql);

                                                    while ($row = $result->fetch_assoc()) {
                                                        $id = $row['idProducto'];
                                                        $nombre = $row['nombre'];
                                                        $stock = $row['stock'];
                                                        $precioVenta = $row['precioVenta'];
                                                        echo "<option value=\"$id\" data-price=\"$precioVenta\" data-stock=\"$stock\">$nombre</option>";
                                                    }
                                                ?>
                                            </select>
                                            <input type="hidden" class="item-stock-hidden" name="stock[]">
                                        </td>
                                        <td class="p-2">
                                            <input type="text" name="price[]" class="item-price w-full rounded-md border border-purple-100 px-2 py-1 text-sm focus:border-purple-500 focus:outline-none" readonly>
                                        </td>
                                        <td class="p-2">
                                            <input type="number" name="quantity[]" min="1" value="1" class="item-quantity w-full rounded-md border border-purple-100 px-2 py-1 text-sm focus:border-purple-500 focus:outline-none">
                                        </td>
                                        <td class="p-2">
                                            <input type="text" name="total[]" class="item-total w-full rounded-md border border-purple-100 px-2 py-1 text-sm focus:border-purple-500 focus:outline-none" readonly>
                                        </td>
                                        <td class="p-2">
                                            <button type="button" class="remove-item rounded-md p-1 text-purple-500 hover:bg-red-50">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <button type="button" id="add-item" class="text-purple-950 mt-2 inline-flex items-center rounded-md border border-purple-200 bg-white px-3 py-2 text-sm font-medium hover:bg-gray-50">
                            <i class="fas fa-plus mr-2 text-purple-950"></i> Agregar
                        </button>
                    </div>
                    
                    <div class="mb-4 grid grid-cols-2 gap-4">

                        <?php if (isset($_SESSION['idEmpleado'])): ?>
                            <input type="hidden" name="idEmpleado" value="<?php echo $_SESSION['idEmpleado']; ?>">
                        <?php else: ?>
                            <script>alert('⚠️ No se encontró idEmpleado en la sesión');</script>
                        <?php endif; ?>

                        
                        <div>
                            <label for="payment-method" class="mb-1 block text-sm font-medium text-purple-800">Método de Pago</label>
                            <select id="payment-method" name="payment_method" class="w-full rounded-md border border-purple-300 px-3 py-2 focus:border-purple-500 focus:outline-none">
                                <option value="Cash">Efectivo</option>
                                <option value="Credit Card">Débito</option>
                                <option value="Debit Card">Crédito</option>
                                <option value="Other">Otro</option>
                            </select>
                        </div>
                        <div>
                            <label for="status" class="mb-1 block text-sm font-medium text-purple-800">Estado</label>
                            <select id="status" name="status" class="w-full rounded-md border border-purple-300 px-3 py-2 focus:border-purple-500 focus:outline-none">
                                <option value="Select">Seleccionar</option>
                                <option value="Completed">Completado</option>   
                                <option value="Pending">Pendiente</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="notes" class="mb-1 block text-sm font-medium text-purple-800">Comentarios</label>
                        <textarea id="notes" name="notes" rows="2" class="w-full rounded-md border border-purple-300 px-3 py-2 focus:border-purple-500 focus:outline-none" placeholder="Comentarios adicionales"></textarea>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="text-lg font-bold text-purple-800">
                            Total: $<span id="grand-total">0.00</span>
                            <input type="hidden" name="grand_total" id="grand-total-input" value="0">
                        </div>

                            <button type="button" onclick="realizarVenta()" class="px-4 bg-purple-700  hover:bg-purple-500 text-white font-semibold py-3 rounded-lg text-lg transition hover:-translate-y-1 hover:shadow-lg">
                                Realizar Venta
                            </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal de Venta Exitosa -->
        <div id="modal-venta-exitosa" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden">
            <div class="bg-white rounded-2xl p-8 shadow-xl w-full max-w-md text-center animate-fade-in">
                <h2 class="text-2xl font-bold text-purple-700 mb-4">✅ ¡Venta realizada con éxito!</h2>
                <p class="text-gray-600 mb-6">La venta ha sido registrada correctamente.</p>
                <button onclick="cerrarModalVenta()" class="bg-purple-700 hover:bg-purple-600 text-white font-semibold py-2 px-6 rounded-lg transition">
                    Cerrar
                </button>
            </div>
        </div>

        <style>
        @keyframes fade-in {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }
        </style>
        
        <!-- Recent Sales -->
        <div class="rounded-lg border bg-white shadow">
            <div class="p-4 border-b">
                <h3 class="font-medium text-purple-950">Ventas Recientes</h3>
            </div>
            <div class="p-4">
                <div class="mb-4 flex items-center gap-2">
                    <i class="fas fa-search text-purple-500"></i>
                    <input type="text" id="sale-search" placeholder="Buscar ventas" class="w-full rounded-md border border-purple-300 px-3 py-2 focus:border-purple-500 focus:outline-none">
                </div>
                <div class="rounded-md border border-purple-100">
                    <table id="tabla-ventas" class="w-full">
                        <thead>
                            <tr class="border-b bg-purple-50">
                                <th class="p-3 text-left font-medium text-purple-800">Id</th>
                                <th class="p-3 text-left font-medium text-purple-800">Empleado</th>
                                <th class="p-3 text-left font-medium text-purple-800">Cliente</th>
                                <th class="p-3 text-left font-medium text-purple-800">Fecha</th>
                                <th class="p-3 text-left font-medium text-purple-800">Total</th>
                                <th class="p-3 text-left font-medium text-purple-800">Acción</th> <!-- ESTA COLUMNA ES CLAVE -->
                            </tr>
                        </thead>

                        <tbody id="ventas-tbody">
                            <tr><td colspan="6" class="text-center p-4 text-gray-500">Cargando ventas...</td></tr>
                        </tbody>


                    </table>
                </div>
                <div class="mt-4 text-right">
                    <a href="?page=invoices" class="text-purple-500 hover:underline">Ver todas</a>
                </div>
            </div>
        </div>

        <!-- Modal Editar Venta -->
        <div id="modal-editar-venta" class="fixed inset-0 hidden z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-2xl p-6 shadow-xl w-full max-w-4xl overflow-y-auto max-h-[90vh]">
            <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-purple-800">✏️ Editar Venta</h2>
            <button onclick="cerrarModalEditarVenta()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
            </div>
            <form id="form-editar-venta">
            <input type="hidden" name="idVenta" id="edit-idVenta">
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                <label class="block text-sm font-medium text-purple-800">Cliente</label>
                <input type="text" id="edit-cliente" class="w-full rounded-md border border-purple-200 px-3 py-2 bg-gray-100" readonly>
                </div>
                <div>
                <label class="block text-sm font-medium text-purple-800">Fecha</label>
                <input type="date" id="edit-fecha" name="fecha" class="w-full rounded-md border border-purple-200 px-3 py-2">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-purple-800 mb-1">Productos</label>
                <table class="w-full border border-purple-100 rounded-md" id="edit-productos-table">
                <thead class="bg-purple-50">
                    <tr>
                    <th class="p-2 text-left text-sm">Producto</th>
                    <th class="p-2 text-left text-sm">Precio</th>
                    <th class="p-2 text-left text-sm">Cantidad</th>
                    <th class="p-2 text-left text-sm">Total</th>
                    <th class="p-2"></th>
                    </tr>
                </thead>
                <tbody id="edit-productos-body">
                    <!-- Filas se cargan dinámicamente -->
                </tbody>
                <tr id="edit-product-template" class="hidden">
                <td class="p-2">
                    <select name="edit-product[]" class="product-select w-full rounded-md border px-2 py-1 text-sm">
                    <option value="">Seleccionar</option>
                    <?php
                        $sql = "SELECT idProducto, nombre, stock, precioVenta FROM Producto";
                        $result = $connection->query($sql);
                        while ($row = $result->fetch_assoc()) {
                        echo "<option value=\"{$row['idProducto']}\" data-price=\"{$row['precioVenta']}\" data-stock=\"{$row['stock']}\">{$row['nombre']}</option>";
                        }
                    ?>
                    </select>
                    <input type="hidden" name="edit-stock[]" value="">
                </td>
                <td class="p-2">
                    <input type="text" name="edit-price[]" class="item-price w-full rounded-md border px-2 py-1 text-sm" readonly>
                </td>
                <td class="p-2">
                    <input type="number" name="edit-quantity[]" value="1" class="item-quantity w-full rounded-md border px-2 py-1 text-sm">
                </td>
                <td class="p-2">
                    <input type="text" name="edit-total[]" class="item-total w-full rounded-md border px-2 py-1 text-sm" readonly>
                </td>
                <td class="p-2">
                    <button type="button" class="remove-item text-red-500">✖</button>
                </td>
                </tr>

                </table>
                <button type="button" id="btn-agregar-producto-edit" class="mt-2 px-3 py-1 border border-purple-300 rounded-md text-sm hover:bg-purple-50">
                Agregar producto
                </button>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                <label class="block text-sm font-medium text-purple-800">Método de Pago</label>
                <select id="edit-metodo" name="metodo" class="w-full rounded-md border border-purple-300 px-3 py-2">
                    <option value="Cash">Efectivo</option>
                    <option value="Credit Card">Crédito</option>
                    <option value="Debit Card">Débito</option>
                    <option value="Other">Otro</option>
                </select>
                </div>
                <div>
                <label class="block text-sm font-medium text-purple-800">Estado</label>
                <select id="edit-estado" name="estado" class="w-full rounded-md border border-purple-300 px-3 py-2">
                    <option value="Pending">Pendiente</option>
                    <option value="Completed">Completado</option>
                </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-purple-800">Comentarios</label>
                <textarea id="edit-comentarios" name="comentarios" rows="3" class="w-full rounded-md border border-purple-300 px-3 py-2"></textarea>
            </div>

            <div class="flex justify-between items-center">
                <div class="font-bold text-purple-800 text-lg">
                Total: $<span id="edit-total">0.00</span>
                </div>
                <button type="submit" class="bg-purple-700 text-white font-semibold px-4 py-2 rounded-md hover:bg-purple-600">
                Guardar cambios
                </button>
            </div>
            </form>
        </div>
        </div>




        
    </div>
</div>

<script src="assets/js/sales/sales.js"></script>
<script src="assets/js/sales/recent_sales.js"></script>

