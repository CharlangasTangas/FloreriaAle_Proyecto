<?php
// Sample sales data - in a real application, this would come from a database
$sales = [
    ['id' => 1, 'invoice' => 'INV-001', 'customer' => 'John Smith', 'date' => '2023-04-15', 'total' => 129.99, 'status' => 'Completed'],
    ['id' => 2, 'invoice' => 'INV-002', 'customer' => 'Sarah Johnson', 'date' => '2023-04-16', 'total' => 89.50, 'status' => 'Completed'],
    ['id' => 3, 'invoice' => 'INV-003', 'customer' => 'Michael Brown', 'date' => '2023-04-18', 'total' => 249.99, 'status' => 'Completed'],
    ['id' => 4, 'invoice' => 'INV-004', 'customer' => 'Emily Davis', 'date' => '2023-04-20', 'total' => 45.75, 'status' => 'Pending'],
    ['id' => 5, 'invoice' => 'INV-005', 'customer' => 'Robert Wilson', 'date' => '2023-04-22', 'total' => 199.99, 'status' => 'Completed'],
];

// Sample products for new sale
$available_products = [
    ['id' => 1, 'name' => 'Laptop', 'sku' => 'LPT-001', 'price' => 999.99, 'stock' => 25],
    ['id' => 2, 'name' => 'Wireless Mouse', 'sku' => 'WMS-002', 'price' => 29.99, 'stock' => 50],
    ['id' => 3, 'name' => 'USB-C Cable', 'sku' => 'USB-003', 'price' => 12.99, 'stock' => 100],
    ['id' => 4, 'name' => 'Printer', 'sku' => 'PRT-004', 'price' => 199.99, 'stock' => 15],
    ['id' => 5, 'name' => 'Printer Paper', 'sku' => 'PPR-005', 'price' => 9.99, 'stock' => 200],
];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'create_sale') {
            // In a real application, you would add the sale to the database
            // For this example, we'll just show a success message
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    showToast('Sale completed successfully');
                });
            </script>";
        }
    }
}
?>

<div class="flex flex-col gap-6">
    <div>
        <h2 class="text-2xl font-bold tracking-tight">Sales Management</h2>
        <p class="text-gray-500">Create and manage sales transactions.</p>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <!-- New Sale Form -->
        <div class="rounded-lg border bg-white shadow">
            <div class="p-4 border-b">
                <h3 class="font-medium">New Sale</h3>
            </div>
            <div class="p-4">
                <form method="POST" id="sale-form">
                    <input type="hidden" name="action" value="create_sale">
                    
                    <div class="mb-4 grid grid-cols-2 gap-4">
                        <div>
                            <label for="customer" class="mb-1 block text-sm font-medium">Customer</label>
                            <input type="text" id="customer" name="customer" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none" placeholder="Customer Name">
                        </div>
                        <div>
                            <label for="date" class="mb-1 block text-sm font-medium">Date</label>
                            <input type="date" id="date" name="date" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="mb-1 block text-sm font-medium">Items</label>
                        <div class="rounded-md border">
                            <table class="w-full" id="sale-items">
                                <thead>
                                    <tr class="border-b bg-gray-50">
                                        <th class="p-2 text-left text-sm font-medium">Product</th>
                                        <th class="p-2 text-left text-sm font-medium">Price</th>
                                        <th class="p-2 text-left text-sm font-medium">Quantity</th>
                                        <th class="p-2 text-left text-sm font-medium">Total</th>
                                        <th class="p-2 text-left text-sm font-medium"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="border-b" id="item-row-template">
                                        <td class="p-2">
                                            <select name="product[]" class="product-select w-full rounded-md border border-gray-300 px-2 py-1 text-sm focus:border-blue-500 focus:outline-none">
                                                <option value="">Select Product</option>
                                                <?php foreach ($available_products as $product): ?>
                                                <option value="<?php echo $product['id']; ?>" data-price="<?php echo $product['price']; ?>" data-stock="<?php echo $product['stock']; ?>">
                                                    <?php echo $product['name']; ?> (<?php echo $product['sku']; ?>)
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td class="p-2">
                                            <input type="text" name="price[]" class="item-price w-full rounded-md border border-gray-300 px-2 py-1 text-sm focus:border-blue-500 focus:outline-none" readonly>
                                        </td>
                                        <td class="p-2">
                                            <input type="number" name="quantity[]" min="1" value="1" class="item-quantity w-full rounded-md border border-gray-300 px-2 py-1 text-sm focus:border-blue-500 focus:outline-none">
                                        </td>
                                        <td class="p-2">
                                            <input type="text" name="total[]" class="item-total w-full rounded-md border border-gray-300 px-2 py-1 text-sm focus:border-blue-500 focus:outline-none" readonly>
                                        </td>
                                        <td class="p-2">
                                            <button type="button" class="remove-item rounded-md p-1 text-red-500 hover:bg-red-50">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <button type="button" id="add-item" class="mt-2 inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium hover:bg-gray-50">
                            <i class="fas fa-plus mr-2"></i> Add Item
                        </button>
                    </div>
                    
                    <div class="mb-4 grid grid-cols-2 gap-4">
                        <div>
                            <label for="payment-method" class="mb-1 block text-sm font-medium">Payment Method</label>
                            <select id="payment-method" name="payment_method" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none">
                                <option value="Cash">Cash</option>
                                <option value="Credit Card">Credit Card</option>
                                <option value="Debit Card">Debit Card</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label for="status" class="mb-1 block text-sm font-medium">Status</label>
                            <select id="status" name="status" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none">
                                <option value="Completed">Completed</option>
                                <option value="Pending">Pending</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="notes" class="mb-1 block text-sm font-medium">Notes</label>
                        <textarea id="notes" name="notes" rows="2" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none" placeholder="Additional notes..."></textarea>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="text-lg font-bold">
                            Total: $<span id="grand-total">0.00</span>
                            <input type="hidden" name="grand_total" id="grand-total-input" value="0">
                        </div>
                        <button type="submit" class="rounded-md bg-blue-500 px-4 py-2 text-white hover:bg-blue-600">
                            Complete Sale
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Recent Sales -->
        <div class="rounded-lg border bg-white shadow">
            <div class="p-4 border-b">
                <h3 class="font-medium">Recent Sales</h3>
            </div>
            <div class="p-4">
                <div class="mb-4 flex items-center gap-2">
                    <i class="fas fa-search text-gray-500"></i>
                    <input type="text" id="sale-search" placeholder="Search sales..." class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none">
                </div>
                <div class="rounded-md border">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b bg-gray-50">
                                <th class="p-3 text-left font-medium">Invoice</th>
                                <th class="p-3 text-left font-medium">Customer</th>
                                <th class="p-3 text-left font-medium">Date</th>
                                <th class="p-3 text-left font-medium">Total</th>
                                <th class="p-3 text-left font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sales as $sale): ?>
                            <tr class="border-b">
                                <td class="p-3 font-medium">
                                    <a href="?page=invoices&id=<?php echo $sale['id']; ?>" class="text-blue-500 hover:underline">
                                        <?php echo $sale['invoice']; ?>
                                    </a>
                                </td>
                                <td class="p-3"><?php echo $sale['customer']; ?></td>
                                <td class="p-3"><?php echo $sale['date']; ?></td>
                                <td class="p-3">$<?php echo number_format($sale['total'], 2); ?></td>
                                <td class="p-3">
                                    <span class="rounded-full px-2 py-1 text-xs font-medium <?php echo $sale['status'] === 'Completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                        <?php echo $sale['status']; ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 text-right">
                    <a href="?page=invoices" class="text-blue-500 hover:underline">View All Sales</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const saleForm = document.getElementById('sale-form');
        const saleItems = document.getElementById('sale-items');
        const addItemBtn = document.getElementById('add-item');
        const grandTotalSpan = document.getElementById('grand-total');
        const grandTotalInput = document.getElementById('grand-total-input');
        
        // Initialize the first row
        initializeRow(saleItems.querySelector('tbody tr'));
        
        // Add new item row
        addItemBtn.addEventListener('click', function() {
            const template = document.getElementById('item-row-template');
            const newRow = template.cloneNode(true);
            newRow.id = '';
            saleItems.querySelector('tbody').appendChild(newRow);
            initializeRow(newRow);
        });
        
        // Initialize a row's event listeners
        function initializeRow(row) {
            const productSelect = row.querySelector('.product-select');
            const priceInput = row.querySelector('.item-price');
            const quantityInput = row.querySelector('.item-quantity');
            const totalInput = row.querySelector('.item-total');
            const removeBtn = row.querySelector('.remove-item');
            
            // Product selection change
            productSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const price = selectedOption.dataset.price || 0;
                priceInput.value = price;
                updateRowTotal(row);
            });
            
            // Quantity change
            quantityInput.addEventListener('input', function() {
                updateRowTotal(row);
            });
            
            // Remove item
            removeBtn.addEventListener('click', function() {
                if (saleItems.querySelectorAll('tbody tr').length > 1) {
                    row.remove();
                    updateGrandTotal();
                } else {
                    // Reset the first row instead of removing it
                    productSelect.value = '';
                    priceInput.value = '';
                    quantityInput.value = 1;
                    totalInput.value = '';
                    updateGrandTotal();
                }
            });
        }
        
        // Update a row's total
        function updateRowTotal(row) {
            const priceInput = row.querySelector('.item-price');
            const quantityInput = row.querySelector('.item-quantity');
            const totalInput = row.querySelector('.item-total');
            
            const price = parseFloat(priceInput.value) || 0;
            const quantity = parseInt(quantityInput.value) || 0;
            const total = price * quantity;
            
            totalInput.value = total.toFixed(2);
            updateGrandTotal();
        }
        
        // Update the grand total
        function updateGrandTotal() {
            const totalInputs = document.querySelectorAll('.item-total');
            let grandTotal = 0;
            
            totalInputs.forEach(input => {
                grandTotal += parseFloat(input.value) || 0;
            });
            
            grandTotalSpan.textContent = grandTotal.toFixed(2);
            grandTotalInput.value = grandTotal.toFixed(2);
        }
        
        // Search functionality for sales
        const searchInput = document.getElementById('sale-search');
        const tableRows = document.querySelectorAll('tbody tr:not(#item-row-template)');
        
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            tableRows.forEach(row => {
                const invoice = row.cells[0].textContent.toLowerCase();
                const customer = row.cells[1].textContent.toLowerCase();
                
                if (invoice.includes(searchTerm) || customer.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
</script>