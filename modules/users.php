<?php
// Sample user data - in a real application, this would come from a database
//Este es una prueba pra ver cambios
$users = [
    ['id' => 1, 'name' => 'John Smith', 'email' => 'john.smith@example.com', 'role' => 'Admin', 'status' => 'Active'],
    ['id' => 2, 'name' => 'Sarah Johnson', 'email' => 'sarah.johnson@example.com', 'role' => 'Cashier', 'status' => 'Active'],
    ['id' => 3, 'name' => 'Michael Brown', 'email' => 'michael.brown@example.com', 'role' => 'Manager', 'status' => 'Active'],
    ['id' => 4, 'name' => 'Emily Davis', 'email' => 'emily.davis@example.com', 'role' => 'Cashier', 'status' => 'Inactive'],
    ['id' => 5, 'name' => 'Robert Wilson', 'email' => 'robert.wilson@example.com', 'role' => 'Inventory', 'status' => 'Active'],
];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add_user' && isset($_POST['name']) && isset($_POST['email'])) {
            // In a real application, you would add the user to the database
            // For this example, we'll just show a success message
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    showToast('User added successfully');
                });
            </script>";
        } elseif ($_POST['action'] === 'delete_user' && isset($_POST['user_id'])) {
            // In a real application, you would delete the user from the database
            // For this example, we'll just show a success message
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    showToast('User deleted successfully');
                });
            </script>";
        }
    }
}
?>

<div class="flex flex-col gap-6">
    <div>
        <h2 class="text-2xl font-bold tracking-tight">User Management</h2>
        <p class="text-gray-500">Create, view, update, and delete system users.</p>
    </div>

    <div class="rounded-lg border bg-white shadow">
        <div class="flex flex-row items-center justify-between p-4 pb-2">
            <div>
                <h3 class="font-medium">Users</h3>
                <p class="text-sm text-gray-500">Manage user accounts and permissions.</p>
            </div>
            <button type="button" class="inline-flex items-center rounded-md bg-blue-500 px-3 py-2 text-sm font-medium text-white hover:bg-blue-600" onclick="document.getElementById('add-user-modal').classList.remove('hidden')">
                <i class="fas fa-plus-circle mr-2"></i>
                Add User
            </button>
        </div>
        <div class="p-4">
            <div class="mb-4 flex items-center gap-2">
                <i class="fas fa-search text-gray-500"></i>
                <input type="text" id="user-search" placeholder="Search users..." class="w-full max-w-sm rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none">
            </div>
            <div class="rounded-md border">
                <table class="w-full">
                    <thead>
                        <tr class="border-b bg-gray-50">
                            <th class="p-3 text-left font-medium">Name</th>
                            <th class="p-3 text-left font-medium">Email</th>
                            <th class="p-3 text-left font-medium">Role</th>
                            <th class="p-3 text-left font-medium">Status</th>
                            <th class="p-3 text-right font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr class="border-b">
                            <td class="p-3 font-medium"><?php echo $user['name']; ?></td>
                            <td class="p-3"><?php echo $user['email']; ?></td>
                            <td class="p-3"><?php echo $user['role']; ?></td>
                            <td class="p-3">
                                <span class="rounded-full px-2 py-1 text-xs font-medium <?php echo $user['status'] === 'Active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                    <?php echo $user['status']; ?>
                                </span>
                            </td>
                            <td class="p-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <button type="button" class="rounded-md p-1 text-gray-500 hover:bg-gray-100">
                                        <i class="fas fa-user-cog"></i>
                                        <span class="sr-only">Edit</span>
                                    </button>
                                    <form method="POST" class="inline">
                                        <input type="hidden" name="action" value="delete_user">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
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

<!-- Add User Modal -->
<div id="add-user-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="w-full max-w-md rounded-lg bg-white p-6">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-lg font-medium">Add New User</h3>
            <button type="button" class="text-gray-500 hover:text-gray-700" onclick="document.getElementById('add-user-modal').classList.add('hidden')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="add_user">
            <div class="mb-4">
                <label for="name" class="mb-1 block text-sm font-medium">Full Name</label>
                <input type="text" id="name" name="name" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none" placeholder="John Smith" required>
            </div>
            <div class="mb-4">
                <label for="email" class="mb-1 block text-sm font-medium">Email</label>
                <input type="email" id="email" name="email" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none" placeholder="john.smith@example.com" required>
            </div>
            <div class="mb-4">
                <label for="role" class="mb-1 block text-sm font-medium">Role</label>
                <select id="role" name="role" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none">
                    <option value="Admin">Admin</option>
                    <option value="Manager">Manager</option>
                    <option value="Cashier" selected>Cashier</option>
                    <option value="Inventory">Inventory</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="status" class="mb-1 block text-sm font-medium">Status</label>
                <select id="status" name="status" class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none">
                    <option value="Active" selected>Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" class="rounded-md border border-gray-300 px-4 py-2 hover:bg-gray-50" onclick="document.getElementById('add-user-modal').classList.add('hidden')">
                    Cancel
                </button>
                <button type="submit" class="rounded-md bg-blue-500 px-4 py-2 text-white hover:bg-blue-600">
                    Save User
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Search functionality
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('user-search');
        const tableRows = document.querySelectorAll('tbody tr');
        
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            tableRows.forEach(row => {
                const name = row.cells[0].textContent.toLowerCase();
                const email = row.cells[1].textContent.toLowerCase();
                const role = row.cells[2].textContent.toLowerCase();
                
                if (name.includes(searchTerm) || email.includes(searchTerm) || role.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
</script>