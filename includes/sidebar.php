<!-- Sidebar -->
<aside id="sidebar" class="fixed top-0 left-0 z-20 flex flex-col flex-shrink-0 w-64 h-full pt-16 font-normal bg-white border-r border-gray-200 transition-transform duration-300 ease-in-out transform -translate-x-full lg:translate-x-0">
    <div class="relative flex flex-col flex-1 min-h-0 pt-0 bg-white">
        <div class="flex flex-col flex-1 pt-5 pb-4 overflow-y-auto">
            <div class="flex-1 px-3 space-y-1 bg-white divide-y divide-gray-200">
                <ul class="pb-2 space-y-2">

                    <!-- D A S H B O A R D -->
                    <li>
                        <a href="?page=dashboard" class="flex items-center p-2 text-base text-gray-900 rounded-lg hover:bg-gray-100 group <?php echo (!isset($_GET['page']) || $_GET['page'] === 'dashboard') ? 'bg-gray-100' : ''; ?>">
                            <i class="fas fa-tachometer-alt w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900"></i>
                            <span class="ml-3">Dashboard</span>
                        </a>
                    </li>

                    <!-- S A L E S -->
                    <li>
                        <a href="?page=sales" class="flex items-center p-2 text-base text-gray-900 rounded-lg hover:bg-gray-100 group <?php echo (isset($_GET['page']) && $_GET['page'] === 'sales') ? 'bg-gray-100' : ''; ?>">
                            <i class="fas fa-shopping-cart w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900"></i>
                            <span class="ml-3">Sales</span>
                        </a>
                    </li>
                    <li>
                        <a href="?page=invoices" class="flex items-center p-2 text-base text-gray-900 rounded-lg hover:bg-gray-100 group <?php echo (isset($_GET['page']) && $_GET['page'] === 'invoices') ? 'bg-gray-100' : ''; ?>">
                            <i class="fas fa-file-invoice w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900"></i>
                            <span class="ml-3">Invoices</span>
                        </a>
                    </li>
                    <li>
                        <a href="?page=purchases" class="flex items-center p-2 text-base text-gray-900 rounded-lg hover:bg-gray-100 group <?php echo (isset($_GET['page']) && $_GET['page'] === 'purchases') ? 'bg-gray-100' : ''; ?>">
                            <i class="fas fa-truck w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900"></i>
                            <span class="ml-3">Purchases</span>
                        </a>
                    </li>
                    <li>
                        <a href="?page=products" class="flex items-center p-2 text-base text-gray-900 rounded-lg hover:bg-gray-100 group <?php echo (isset($_GET['page']) && $_GET['page'] === 'products') ? 'bg-gray-100' : ''; ?>">
                            <i class="fas fa-box w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900"></i>
                            <span class="ml-3">Products</span>
                        </a>
                    </li>
                    <li>
                        <a href="?page=users" class="flex items-center p-2 text-base text-gray-900 rounded-lg hover:bg-gray-100 group <?php echo (isset($_GET['page']) && $_GET['page'] === 'users') ? 'bg-gray-100' : ''; ?>">
                            <i class="fas fa-users w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900"></i>
                            <span class="ml-3">Users</span>
                        </a>
                    </li>
                </ul>
                <ul class="pt-4 mt-4 space-y-2">
                    <li>
                        <a href="?page=test" class="flex items-center p-2 text-base text-gray-900 rounded-lg hover:bg-gray-100 group">
                            <i class="fas fa-cog w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900"></i>
                            <span class="ml-3">Settings</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center p-2 text-base text-gray-900 rounded-lg hover:bg-gray-100 group">
                            <i class="fas fa-sign-out-alt w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900"></i>
                            <span class="ml-3">Logout</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</aside>

<!-- Backdrop -->
<div id="sidebarBackdrop" class="fixed inset-0 z-10 hidden bg-gray-900 bg-opacity-50 transition-opacity duration-300 ease-in-out opacity-0"></div>