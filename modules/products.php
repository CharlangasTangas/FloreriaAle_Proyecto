<?php
// Sample product data - in a real application, this would come from a database
$products = [
    ['id' => 1, 'name' => 'Laptop', 'sku' => 'LPT-001', 'price' => 999.99, 'stock' => 25, 'category' => 'Electronics'],
    ['id' => 2, 'name' => 'Wireless Mouse', 'sku' => 'WMS-002', 'price' => 29.99, 'stock' => 50, 'category' => 'Accessories'],
    ['id' => 3, 'name' => 'USB-C Cable', 'sku' => 'USB-003', 'price' => 12.99, 'stock' => 100, 'category' => 'Accessories'],
    ['id' => 4, 'name' => 'Printer', 'sku' => 'PRT-004', 'price' => 199.99, 'stock' => 15, 'category' => 'Electronics'],
    ['id' => 5, 'name' => 'Printer Paper', 'sku' => 'PPR-005', 'price' => 9.99, 'stock' => 200, 'category' => 'Office Supplies'],
];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add_product' && isset($_POST['name']) && isset($_POST['sku'])) {
            // In a real application, you would add the product to the database
            // For this example, we'll just show a success message
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    showToast('Product added successfully');
                });
            </script>";
        } elseif ($_POST['action'] === 'delete_product' && isset($_POST['product_id'])) {
            // In a real application, you would delete the product from the database
            // For this example, we'll just show a success message
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    showToast('Product deleted successfully');
                });
            </script>";
        }
    }
}
?>

<div class="flex flex-col gap-6">
    <div>
        <h2 class="text-2xl font-bold tracking-tight">Administrador de Productos</h2>
        <p class="text-gray-500">Administra tu inventario, precio y stock.</p>
    </div>

    <div class="rounded-lg border bg-white shadow">
        <div class="flex flex-row items-center justify-between p-4 pb-2">
            <div>
                <h3 class="font-medium">Productos</h3>
                <p class="text-sm text-gray-500">Administra tu inventario de productos.<p>
            </div>

            <!-- Botón par abrir el modal de Agregar Productos -->
            <button type="button" class="inline-flex items-center rounded-md bg-blue-500 px-3 py-2 text-sm font-medium text-white hover:bg-blue-600" onclick="document.getElementById('add-product-modal').classList.remove('hidden')">
                <i class="fas fa-plus-circle mr-2"></i>
                Agregar Producto
            </button>

        </div>
        <div class="p-4">
            <div class="mb-4 flex items-center gap-2">
                <i class="fas fa-search text-gray-500"></i>
                <input type="text" id="product-search" placeholder="Search products..." class="w-full max-w-sm rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none">
            </div>
            <div class="rounded-md border">
                <table class="w-full">
                    <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="p-3 text-left font-medium">Name</th>
                            <th class="p-3 text-left font-medium">SKU</th>
                            <th class="p-3 text-left font-medium">Price</th>
                            <th class="p-3 text-left font-medium">Stock</th>
                            <th class="p-3 text-left font-medium">Category</th>
                            <th class="p-3 text-right font-medium">Actions</th>
                        </tr>
                    </thead>

                    <!-- El foreach se encarga de cargar los productos del arreglo "$products" uno por uno y colocarlos en forma de tabla-->
                    <tbody>
                        <?php foreach ($products as $product): ?>
                        <tr class="border-b">
                            <td class="p-3 font-medium"><?php echo $product['name']; ?></td>
                            <td class="p-3"><?php echo $product['sku']; ?></td>
                            <td class="p-3">$<?php echo number_format($product['price'], 2); ?></td>
                            <td class="p-3 <?php echo $product['stock'] < 10 ? 'text-red-500 font-medium' : ''; ?>">
                                <?php echo $product['stock']; ?>
                            </td>
                            <td class="p-3"><?php echo $product['category']; ?></td>
                            <td class="p-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <button type="button" class="rounded-md p-1 text-gray-500 hover:bg-gray-100">
                                        <i class="fas fa-edit"></i>
                                        <span class="sr-only">Edit</span>
                                    </button>
                                    <form method="POST" class="inline">
                                        <input type="hidden" name="action" value="delete_product">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                        <button type="submit" class="rounded-md p-1 text-gray-500 hover:bg-gray-100">
                                            <i class="fas fa-trash"></i>
                                            <span class="sr-only">Delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Product Modal -->
<div id="add-product-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="w-full max-w-md rounded-lg bg-white p-6">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-lg font-medium">Add New Product</h3>
            <button type="button" class="text-gray-500 hover:text-gray-700" onclick="document.getElementById('add-product-modal').classList.add('hidden')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="add_product">
            <div class="mb-4">
                <label for="name" class="mb-1 block text-sm font-medium">Product Name</label>
                <input type="text" id="name" name="name" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none" placeholder="Wireless Keyboard" required>
            </div>
            <div class="mb-4">
                <label for="sku" class="mb-1 block text-sm font-medium">SKU</label>
                <input type="text" id="sku" name="sku" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none" placeholder="WKB-001" required>
            </div>
            <div class="mb-4 grid grid-cols-2 gap-4">
                <div>
                    <label for="price" class="mb-1 block text-sm font-medium">Price ($)</label>
                    <input type="number" id="price" name="price" step="0.01" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none" placeholder="49.99" required>
                </div>
                <div>
                    <label for="stock" class="mb-1 block text-sm font-medium">Stock</label>
                    <input type="number" id="stock" name="stock" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none" placeholder="100" required>
                </div>
            </div>
            <div class="mb-4">
                <label for="category" class="mb-1 block text-sm font-medium">Category</label>
                <input type="text" id="category" name="category" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none" placeholder="Accessories">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" class="rounded-md border border-gray-300 px-4 py-2 hover:bg-gray-50" onclick="document.getElementById('add-product-modal').classList.add('hidden')">
                    Cancel
                </button>
                <button type="submit" class="rounded-md bg-blue-500 px-4 py-2 text-white hover:bg-blue-600">
                    Save Product
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Search functionality
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('product-search');
        const tableRows = document.querySelectorAll('tbody tr');
        
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            tableRows.forEach(row => {
                const name = row.cells[0].textContent.toLowerCase();
                const sku = row.cells[1].textContent.toLowerCase();
                const category = row.cells[4].textContent.toLowerCase();
                
                if (name.includes(searchTerm) || sku.includes(searchTerm) || category.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
</script>