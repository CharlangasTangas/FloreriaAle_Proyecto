<?php
// Sample purchase data - in a real application, this would come from a database
$purchases = [
    ['id' => 1, 'invoice' => 'PUR-001', 'supplier' => 'Tech Solutions Inc.', 'date' => '2023-04-15', 'total' => 529.99, 'status' => 'Completed'],
    ['id' => 2, 'invoice' => 'PUR-002', 'supplier' => 'Office Supplies Co.', 'date' => '2023-04-16', 'total' => 189.50, 'status' => 'Completed'],
    ['id' => 3, 'invoice' => 'PUR-003', 'supplier' => 'Global Electronics', 'date' => '2023-04-18', 'total' => 749.99, 'status' => 'Completed'],
    ['id' => 4, 'invoice' => 'PUR-004', 'supplier' => 'Wholesale Goods Ltd.', 'date' => '2023-04-20', 'total' => 145.75, 'status' => 'Pending'],
    ['id' => 5, 'invoice' => 'PUR-005', 'supplier' => 'Industrial Parts S.A.', 'date' => '2023-04-22', 'total' => 399.99, 'status' => 'Completed'],
];

include 'config/connection.php';
?>

<div class="flex flex-col gap-6">
    <div>
        <h2 class="text-2xl text-purple-950 font-bold tracking-tight">Administrador de Compras</h2>
        <p class="text-gray-500">Crea y administra las transacciones de tus compras.</p>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <div class="rounded-lg border bg-white shadow">
            <div class="p-4 border-b">
                <h3 class="font-medium text-purple-950">Registrar Compra</h3>
            </div>
            <div class="p-4">
                <form method="POST" id="purchase-form">
                    <input type="hidden" name="action" value="create_purchase">
                    
                    <div class="mb-4 grid grid-cols-2 gap-4">
                        <div class="relative">
                            <label for="supplier" class="mb-1 block text-sm font-medium text-purple-800">Proveedor</label>
                            <input type="text" id="supplier" name="supplier" autocomplete="off"
                                class="w-full rounded-md border border-purple-100 px-3 py-2 focus:border-purple-500 focus:outline-none"
                                placeholder="Nombre de Proveedor">
                            <input type="hidden" id="idProveedor" name="idProveedor">

                            <div id="sugerencias-proveedores"
                                class="absolute z-50 bg-white border border-purple-200 rounded-md mt-1 w-full max-h-48 overflow-y-auto hidden shadow-lg">
                            </div>
                        </div>

                        <div>
                            <label for="date" class="mb-1 block text-sm font-medium text-purple-800 ">Fecha</label>
                            <input type="date" id="date" name="date" class="w-full rounded-md border border-purple-100 px-3 py-2 focus:border-purple-500 focus:outline-none" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="rounded-md border border-purple-100">
                            <table class="w-full" id="purchase-items">
                                <thead>
                                    <tr class="border-b bg-purple-50">
                                        <th class="p-2 text-left text-sm font-medium text-purple-800">Producto</th>
                                        <th class="p-2 text-left text-sm font-medium text-purple-800">Costo Unitario</th>
                                        <th class="p-2 text-left text-sm font-medium text-purple-800">Cantidad</th>
                                        <th class="p-2 text-left text-sm font-medium text-purple-800">Subtotal</th>
                                        <th class="p-2 text-left text-sm font-medium text-purple-800"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="border-b" id="item-row-template">
                                        <td class="p-2">
                                            <select name="product[]" class="product-select w-full rounded-md border border-purple-100 px-2 py-1 text-sm focus:border-purple-500 focus:outline-none">
                                                <option value="">Seleccionar</option>
                                                <?php
                                                    $sql = "SELECT idProducto, nombre, stock, precioCompra FROM Producto"; // Cambiado a precioCompra
                                                    $result = $connection->query($sql);

                                                    while ($row = $result->fetch_assoc()) {
                                                        $id = $row['idProducto'];
                                                        $nombre = $row['nombre'];
                                                        $stock = $row['stock'];
                                                        $precioCompra = $row['precioCompra']; // Cambiado a precioCompra
                                                        echo "<option value=\"$id\" data-cost=\"$precioCompra\" data-stock=\"$stock\">$nombre</option>"; // Cambiado a data-cost
                                                    }
                                                ?>
                                            </select>
                                            <input type="hidden" class="item-stock-hidden" name="stock[]">
                                        </td>
                                        <td class="p-2">
                                            <input type="text" name="cost[]" class="item-cost w-full rounded-md border border-purple-100 px-2 py-1 text-sm focus:border-purple-500 focus:outline-none" readonly>
                                        </td>
                                        <td class="p-2">
                                            <input type="number" name="quantity[]" min="1" value="1" class="item-quantity w-full rounded-md border border-purple-100 px-2 py-1 text-sm focus:border-purple-500 focus:outline-none">
                                        </td>
                                        <td class="p-2">
                                            <input type="text" name="subtotal[]" class="item-subtotal w-full rounded-md border border-purple-100 px-2 py-1 text-sm focus:border-purple-500 focus:outline-none" readonly>
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
                                <option value="Transfer">Transferencia</option>
                                <option value="Other">Otro</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="notes" class="mb-1 block text-sm font-medium text-purple-800">Comentarios</label>
                        <textarea id="notes" name="notes" rows="2" class="w-full rounded-md border border-purple-300 px-3 py-2 focus:border-purple-500 focus:outline-none" placeholder="Comentarios adicionales"></textarea>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="text-lg font-bold text-purple-800">
                            Total: $<span id="grand-total-purchase">0.00</span>
                            <input type="hidden" name="grand_total_purchase" id="grand-total-purchase-input" value="0">
                        </div>

                        <button type="button" onclick="realizarCompra()" class="px-4 bg-purple-700 hover:bg-purple-500 text-white font-semibold py-3 rounded-lg text-lg transition hover:-translate-y-1 hover:shadow-lg">
                            Registrar Compra
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div id="modal-compra-exitosa" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden">
            <div class="bg-white rounded-2xl p-8 shadow-xl w-full max-w-md text-center animate-fade-in">
                <h2 class="text-2xl font-bold text-purple-700 mb-4">✅ ¡Compra registrada con éxito!</h2>
                <p class="text-gray-600 mb-6">La compra ha sido registrada correctamente.</p>
                <button onclick="cerrarModalCompra()" class="bg-purple-700 hover:bg-purple-600 text-white font-semibold py-2 px-6 rounded-lg transition">
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
        
        <div class="rounded-lg border bg-white shadow">
            <div class="p-4 border-b">
                <h3 class="font-medium text-purple-950">Compras Recientes</h3>
            </div>
            <div class="p-4">
                <div class="mb-4 flex items-center gap-2">
                    <i class="fas fa-search text-purple-500"></i>
                    <input type="text" id="purchase-search" placeholder="Buscar compras" class="w-full rounded-md border border-purple-300 px-3 py-2 focus:border-purple-500 focus:outline-none">
                </div>
                <div class="rounded-md border border-purple-100">
                    <table id="tabla-compras" class="w-full">
                        <thead>
                            <tr class="border-b bg-purple-50">
                                <th class="p-3 text-left font-medium text-purple-800">Id</th>
                                <th class="p-3 text-left font-medium text-purple-800">Proveedor</th>
                                <th class="p-3 text-left font-medium text-purple-800">Fecha</th>
                                <th class="p-3 text-left font-medium text-purple-800">Total</th>
                                <th class="p-3 text-left font-medium text-purple-800">Estado</th>
                            </tr>
                        </thead>
                        <tbody id="compras-recientes-body">
                            <tr><td colspan="6" class="text-center p-4 text-gray-500">Cargando compras...</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 text-right">
                    <a href="?page=purchases" class="text-purple-500 hover:underline">Ver todas</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/purchases/purchases.js"></script>
<script src="assets/js/purchases/recent_purchases.js"></script>
