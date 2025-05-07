<?php
// Sample purchases data - in a real application, this would come from a database
$purchases = [
    ['id' => 1, 'reference' => 'PO-001', 'supplier' => 'Tech Supplies Inc.', 'date' => '2023-04-10', 'total' => 1250.00, 'status' => 'Received'],
    ['id' => 2, 'reference' => 'PO-002', 'supplier' => 'Office Depot', 'date' => '2023-04-12', 'total' => 450.75, 'status' => 'Received'],
    ['id' => 3, 'reference' => 'PO-003', 'supplier' => 'Electronics Wholesale', 'date' => '2023-04-15', 'total' => 2100.50, 'status' => 'Pending'],
    ['id' => 4, 'reference' => 'PO-004', 'supplier' => 'Tech Supplies Inc.', 'date' => '2023-04-18', 'total' => 875.25, 'status' => 'Ordered'],
    ['id' => 5, 'reference' => 'PO-005', 'supplier' => 'Office Depot', 'date' => '2023-04-20', 'total' => 320.00, 'status' => 'Received'],
];

// Sample products for new purchase
$available_products = [
    ['id' => 1, 'name' => 'Laptop', 'sku' => 'LPT-001', 'cost' => 800.00],
    ['id' => 2, 'name' => 'Wireless Mouse', 'sku' => 'WMS-002', 'cost' => 15.00],
    ['id' => 3, 'name' => 'USB-C Cable', 'sku' => 'USB-003', 'cost' => 5.00],
    ['id' => 4, 'name' => 'Printer', 'sku' => 'PRT-004', 'cost' => 150.00],
    ['id' => 5, 'name' => 'Printer Paper', 'sku' => 'PPR-005', 'cost' => 5.00],
];

// Sample suppliers
$suppliers = [
    ['id' => 1, 'name' => 'Tech Supplies Inc.'],
    ['id' => 2, 'name' => 'Office Depot'],
    ['id' => 3, 'name' => 'Electronics Wholesale'],
    ['id' => 4, 'name' => 'Computer Parts Ltd.'],
    ['id' => 5, 'name' => 'Stationery World'],
];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'create_purchase') {
            // In a real application, you would add the purchase to the database
            // For this example, we'll just show a success message
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    showToast('Purchase order created successfully');
                });
            </script>";
        }
    }
}
?>

<div class="flex flex-col gap-6">
    <div>
        <h2 class="text-2xl font-bold tracking-tight">Purchase Management</h2>
        <p class="text-gray-500">Create and manage purchase orders from suppliers.</p>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <!-- New Purchase Form -->
        <div class="rounded-lg border bg-white shadow">
            <div class="p-4 border-b">
                <h3 class="font-medium">New Purchase Order</h3>
            </div>
            <div class="p-4">
                <form method="POST" id="purchase-form">
                    <input type="hidden" name="action" value="create_purchase">
                    
                    <div class="mb-4 grid grid-cols-2 gap-4">
                        <div>
                            <label for="supplier" class="mb-1 block text-sm font-medium">Supplier</label>
                            <select id="supplier" name="supplier" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none">
                                <option value="">Select Supplier</option>
                                <?php foreach ($suppliers as $supplier): ?>
                                <option value="<?php echo $supplier['id']; ?>"><?php echo $supplier['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label for="date" class="mb-1 block text-sm font-medium">Date</label>
                            <input type="date" id="date" name="date" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="mb-1 block text-sm font-medium">Items</label>
                        <div class="rounded-md border">
                            <table class="w-full" id="purchase-items">
                                <thead>
                                    <tr class="border-b bg-gray-50">
                                        <th class="p-2 text-left text-sm font-medium">Product</th>
                                        <th class="p-2 text-left text-sm font-medium">Cost</th>
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
                                                <option value="<?php echo $product['id']; ?>" data-cost="<?php echo $product['cost']; ?>">
                                                    <?php echo $product['name']; ?> (<?php echo $product['sku']; ?>)
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td class="p-2">
                                            <input type="text" name="cost[]" class="item-cost w-full rounded-md border border-gray-300 px-2 py-1 text-sm focus:border-blue-500 focus:outline-none" readonly>
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
                            <label for="reference" class="mb-1 block text-sm font-medium">Reference Number</label>
                            <input type="text" id="reference" name="reference" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none" placeholder="PO-006">
                        </div>
                        <div>
                            <label for="status" class="mb-1 block text-sm font-medium">Status</label>
                            <select id="status" name="status" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none">
                                <option value="Ordered">Ordered</option>
                                <option value="Pending">Pending</option>
                                <option value="Received">Received</option>
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
                            Create Purchase Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Recent Purchases -->
        <div class="rounded-lg border bg-white shadow">
            <div class="p-4 border-b">
                <h3 class="font-medium">Recent Purchase Orders</h3>
            </div>
            <div class="p-4">
                <div class="mb-4 flex items-center gap-2">
                    <i class="fas fa-search text-gray-500"></i>
                    <input type="text" id="purchase-search" placeholder="Search purchases..." class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none">
                </div>
                <div class="rounded-md border">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b bg-gray-50">
                                <th class="p-3 text-left font-medium">Reference</th>
                                <th class="p-3 text-left font-medium">Supplier</th>
                                <th class="p-3 text-left font-medium">Date</th>
                                <th class="p-3 text-left font-medium">Total</th>
                                <th class="p-3 text-left font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($purchases as $purchase): ?>
                            <tr class="border-b">
                                <td class="p-3 font-medium">
                                    <a href="#" class="text-blue-500 hover:underline">
                                        <?php echo $purchase['reference']; ?>
                                    </a>
                                </td>
                                <td class="p-3"><?php echo $purchase['supplier']; ?></td>
                                <td class="p-3"><?php echo $purchase['date']; ?></td>
                                <td class="p-3">$<?php echo number_format($purchase['total'], 2); ?></td>
                                <td class="p-3">
                                    <span class="rounded-full px-2 py-1 text-xs font-medium 
                                        <?php 
                                        if ($purchase['status'] === 'Received') {
                                            echo 'bg-green-100 text-green-800';
                                        } elseif ($purchase['status'] === 'Pending') {
                                            echo 'bg-yellow-100 text-yellow-800';
                                        } else {
                                            echo 'bg-blue-100 text-blue-800';
                                        }
                                        ?>">
                                        <?php echo $purchase['status']; ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 text-right">
                    <a href="#" class="text-blue-500 hover:underline">View All Purchase Orders</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const purchaseForm = document.getElementById('purchase-form');
        const purchaseItems = document.getElementById('purchase-items');
        const addItemBtn = document.getElementById('add-item');
        const grandTotalSpan = document.getElementById('grand-total');
        const grandTotalInput = document.getElementById('grand-total-input');
        
        // Initialize the first row
        initializeRow(purchaseItems.querySelector('tbody tr'));
        
        // Add new item row
        addItemBtn.addEventListener('click', function() {
            const template = document.getElementById('item-row-template');
            const newRow = template.cloneNode(true);
            newRow.id = '';
            purchaseItems.querySelector('tbody').appendChild(newRow);
            initializeRow(newRow);
        });
        
        // Initialize a row's event listeners
        function initializeRow(row) {
            const productSelect = row.querySelector('.product-select');
            const costInput = row.querySelector('.item-cost');
            const quantityInput = row.querySelector('.item-quantity');
            const totalInput = row.querySelector('.item-total');
            const removeBtn = row.querySelector('.remove-item');
            
            // Product selection change
            productSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const cost = selectedOption.dataset.cost || 0;
                costInput.value = cost;
                updateRowTotal(row);
            });
            
            // Quantity change
            quantityInput.addEventListener('input', function() {
                updateRowTotal(row);
            });
            
            // Remove item
            removeBtn.addEventListener('click', function() {
                if (purchaseItems.querySelectorAll('tbody tr').length > 1) {
                    row.remove();
                    updateGrandTotal();
                } else {
                    // Reset the first row instead of removing it
                    productSelect.value = '';
                    costInput.value = '';
                    quantityInput.value = 1;
                    totalInput.value = '';
                    updateGrandTotal();
                }
            });
        }
        
        // Update a row's total
        function updateRowTotal(row) {
            const costInput = row.querySelector('.item-cost');
            const quantityInput = row.querySelector('.item-quantity');
            const totalInput = row.querySelector('.item-total');
            
            const cost = parseFloat(costInput.value) || 0;
            const quantity = parseInt(quantityInput.value) || 0;
            const total = cost * quantity;
            
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
        
        // Search functionality for purchases
        const searchInput = document.getElementById('purchase-search');
        const tableRows = document.querySelectorAll('tbody tr:not(#item-row-template)');
        
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            tableRows.forEach(row => {
                const reference = row.cells[0].textContent.toLowerCase();
                const supplier = row.cells[1].textContent.toLowerCase();
                
                if (reference.includes(searchTerm) || supplier.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
</script>