<?php
// Sample invoices data - in a real application, this would come from a database
$invoices = [
    [
        'id' => 1, 
        'invoice_number' => 'INV-001', 
        'customer' => 'John Smith', 
        'date' => '2023-04-15', 
        'due_date' => '2023-05-15', 
        'total' => 129.99, 
        'status' => 'Paid',
        'items' => [
            ['product' => 'Wireless Mouse', 'quantity' => 1, 'price' => 29.99, 'total' => 29.99],
            ['product' => 'USB-C Cable', 'quantity' => 2, 'price' => 12.99, 'total' => 25.98],
            ['product' => 'Printer Paper', 'quantity' => 1, 'price' => 9.99, 'total' => 9.99],
            ['product' => 'Laptop Cooling Pad', 'quantity' => 1, 'price' => 64.03, 'total' => 64.03],
        ]
    ],
    [
        'id' => 2, 
        'invoice_number' => 'INV-002', 
        'customer' => 'Sarah Johnson', 
        'date' => '2023-04-16', 
        'due_date' => '2023-05-16', 
        'total' => 89.50, 
        'status' => 'Paid',
        'items' => []
    ],
    [
        'id' => 3, 
        'invoice_number' => 'INV-003', 
        'customer' => 'Michael Brown', 
        'date' => '2023-04-18', 
        'due_date' => '2023-05-18', 
        'total' => 249.99, 
        'status' => 'Paid',
        'items' => []
    ],
    [
        'id' => 4, 
        'invoice_number' => 'INV-004', 
        'customer' => 'Emily Davis', 
        'date' => '2023-04-20', 
        'due_date' => '2023-05-20', 
        'total' => 45.75, 
        'status' => 'Pending',
        'items' => []
    ],
    [
        'id' => 5, 
        'invoice_number' => 'INV-005', 
        'customer' => 'Robert Wilson', 
        'date' => '2023-04-22', 
        'due_date' => '2023-05-22', 
        'total' => 199.99, 
        'status' => 'Paid',
        'items' => []
    ],
];

// Check if viewing a specific invoice
$view_invoice = false;
$current_invoice = null;

if (isset($_GET['id'])) {
    $invoice_id = intval($_GET['id']);
    foreach ($invoices as $invoice) {
        if ($invoice['id'] === $invoice_id) {
            $view_invoice = true;
            $current_invoice = $invoice;
            break;
        }
    }
}
?>

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
        </div>
    </div>

    <div class="rounded-lg border bg-white shadow">
        <div class="p-6">
            <!-- Invoice Header -->
            <div class="flex justify-between mb-8">
                <div>
                    <h3 class="text-lg font-bold mb-1">POS System</h3>
                    <p class="text-sm text-gray-500">123 Business Street</p>
                    <p class="text-sm text-gray-500">City, State 12345</p>
                    <p class="text-sm text-gray-500">Phone: (123) 456-7890</p>
                    <p class="text-sm text-gray-500">Email: info@possystem.com</p>
                </div>
                <div class="text-right">
                    <h3 class="text-xl font-bold mb-1">INVOICE</h3>
                    <p class="text-sm text-gray-500">Invoice #: <?php echo $current_invoice['invoice_number']; ?></p>
                    <p class="text-sm text-gray-500">Date: <?php echo $current_invoice['date']; ?></p>
                    <p class="text-sm text-gray-500">Due Date: <?php echo $current_invoice['due_date']; ?></p>
                    <p class="text-sm font-medium mt-2">
                        <span class="rounded-full px-2 py-1 text-xs font-medium <?php echo $current_invoice['status'] === 'Paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                            <?php echo $current_invoice['status']; ?>
                        </span>
                    </p>
                </div>
            </div>
            
            <!-- Customer Info -->
            <div class="mb-8">
                <h4 class="text-sm font-medium text-gray-500 mb-2">BILL TO</h4>
                <p class="font-medium"><?php echo $current_invoice['customer']; ?></p>
                <p class="text-sm text-gray-500">123 Customer Street</p>
                <p class="text-sm text-gray-500">City, State 12345</p>
                <p class="text-sm text-gray-500">Email: customer@example.com</p>
            </div>
            
            <!-- Invoice Items -->
            <div class="mb-8">
                <table class="w-full">
                    <thead>
                        <tr class="border-b-2 border-gray-200">
                            <th class="py-2 text-left font-medium">Item</th>
                            <th class="py-2 text-right font-medium">Quantity</th>
                            <th class="py-2 text-right font-medium">Price</th>
                            <th class="py-2 text-right font-medium">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($current_invoice['items'])): ?>
                            <?php foreach ($current_invoice['items'] as $item): ?>
                            <tr class="border-b">
                                <td class="py-3"><?php echo $item['product']; ?></td>
                                <td class="py-3 text-right"><?php echo $item['quantity']; ?></td>
                                <td class="py-3 text-right">$<?php echo number_format($item['price'], 2); ?></td>
                                <td class="py-3 text-right">$<?php echo number_format($item['total'], 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr class="border-b">
                                <td class="py-3">Product 1</td>
                                <td class="py-3 text-right">1</td>
                                <td class="py-3 text-right">$<?php echo number_format($current_invoice['total'] * 0.8, 2); ?></td>
                                <td class="py-3 text-right">$<?php echo number_format($current_invoice['total'] * 0.8, 2); ?></td>
                            </tr>
                            <tr class="border-b">
                                <td class="py-3">Product 2</td>
                                <td class="py-3 text-right">1</td>
                                <td class="py-3 text-right">$<?php echo number_format($current_invoice['total'] * 0.2, 2); ?></td>
                                <td class="py-3 text-right">$<?php echo number_format($current_invoice['total'] * 0.2, 2); ?></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Invoice Summary -->
            <div class="flex justify-end">
                <div class="w-64">
                    <div class="flex justify-between py-2">
                        <span class="font-medium">Subtotal:</span>
                        <span>$<?php echo number_format($current_invoice['total'] * 0.9, 2); ?></span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="font-medium">Tax (10%):</span>
                        <span>$<?php echo number_format($current_invoice['total'] * 0.1, 2); ?></span>
                    </div>
                    <div class="flex justify-between py-2 border-t border-gray-200 font-bold">
                        <span>Total:</span>
                        <span>$<?php echo number_format($current_invoice['total'], 2); ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Notes -->
            <div class="mt-8 pt-8 border-t border-gray-200">
                <h4 class="text-sm font-medium text-gray-500 mb-2">NOTES</h4>
                <p class="text-sm text-gray-500">Thank you for your business. Payment is due within 30 days.</p>
            </div>
        </div>
    </div>
</div>

<?php else: ?>
<!-- Invoices List View -->
<div class="flex flex-col gap-6">
    <div>
        <h2 class="text-2xl font-bold tracking-tight">Invoices</h2>
        <p class="text-gray-500">View and manage customer invoices.</p>
    </div>

    <div class="rounded-lg border bg-white shadow">
        <div class="p-4">
            <div class="mb-4 flex items-center gap-2">
                <i class="fas fa-search text-gray-500"></i>
                <input type="text" id="invoice-search" placeholder="Search invoices..." class="w-full max-w-sm rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none">
            </div>
            <div class="rounded-md border">
                <table class="w-full">
                    <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="p-3 text-left font-medium">Invoice #</th>
                            <th class="p-3 text-left font-medium">Customer</th>
                            <th class="p-3 text-left font-medium">Date</th>
                            <th class="p-3 text-left font-medium">Due Date</th>
                            <th class="p-3 text-left font-medium">Total</th>
                            <th class="p-3 text-left font-medium">Status</th>
                            <th class="p-3 text-right font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($invoices as $invoice): ?>
                        <tr class="border-b">
                            <td class="p-3 font-medium">
                                <a href="?page=invoices&id=<?php echo $invoice['id']; ?>" class="text-blue-500 hover:underline">
                                    <?php echo $invoice['invoice_number']; ?>
                                </a>
                            </td>
                            <td class="p-3"><?php echo $invoice['customer']; ?></td>
                            <td class="p-3"><?php echo $invoice['date']; ?></td>
                            <td class="p-3"><?php echo $invoice['due_date']; ?></td>
                            <td class="p-3">$<?php echo number_format($invoice['total'], 2); ?></td>
                            <td class="p-3">
                                <span class="rounded-full px-2 py-1 text-xs font-medium <?php echo $invoice['status'] === 'Paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                    <?php echo $invoice['status']; ?>
                                </span>
                            </td>
                            <td class="p-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="?page=invoices&id=<?php echo $invoice['id']; ?>" class="rounded-md p-1 text-gray-500 hover:bg-gray-100">
                                        <i class="fas fa-eye"></i>
                                        <span class="sr-only">View</span>
                                    </a>
                                    <button type="button" class="rounded-md p-1 text-gray-500 hover:bg-gray-100">
                                        <i class="fas fa-print"></i>
                                        <span class="sr-only">Print</span>
                                    </button>
                                    <button type="button" class="rounded-md p-1 text-gray-500 hover:bg-gray-100">
                                        <i class="fas fa-file-pdf"></i>
                                        <span class="sr-only">PDF</span>
                                    </button>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search functionality for invoices
        const searchInput = document.getElementById('invoice-search');
        const tableRows = document.querySelectorAll('tbody tr');
        
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
<?php endif; ?>