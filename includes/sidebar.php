<!-- Sidebar -->
<aside id="sidebar" class="fixed top-0 left-0 z-20 flex flex-col flex-shrink-0 w-64 h-full pt-16 font-normal bg-white border-r border-gray-200 transition-transform duration-300 ease-in-out transform -translate-x-full lg:translate-x-0">
    <div class="relative flex flex-col flex-1 min-h-0 pt-0 bg-white">
        <div class="flex flex-col flex-1 pt-5 pb-4 overflow-y-auto">
            <div class="flex-1 px-3 space-y-1 bg-white divide-y divide-gray-200">
                <ul class="pb-2 space-y-2">

                    <!-- E S T A D Í S T I C A S -->
                    <li>
                        <a href="?page=dashboard" class="flex items-center p-2 text-base text-gray-900 rounded-lg hover:bg-gray-100 group <?php echo (!isset($_GET['page']) || $_GET['page'] === 'dashboard') ? 'bg-gray-100' : ''; ?>">
                            <i class="fas fa-tachometer-alt w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900"></i>
                            <span class="ml-3">Estadísticas</span>
                        </a>
                    </li>

                    <!-- V E N T A S -->
                    <li>
                        <a href="?page=sales" class="flex items-center p-2 text-base text-gray-900 rounded-lg hover:bg-gray-100 group <?php echo (isset($_GET['page']) && $_GET['page'] === 'sales') ? 'bg-gray-100' : ''; ?>">
                            <i class="fas fa-shopping-cart w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900"></i>
                            <span class="ml-3">Ventas</span>
                        </a>
                    </li>

                    <!-- F A C T U R A S -->
                    <li>
                        <a href="?page=invoices" class="flex items-center p-2 text-base text-gray-900 rounded-lg hover:bg-gray-100 group <?php echo (isset($_GET['page']) && $_GET['page'] === 'invoices') ? 'bg-gray-100' : ''; ?>">
                            <i class="fas fa-file-invoice w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900"></i>
                            <span class="ml-3">Facturas</span>
                        </a>
                    </li>

                    <!-- C O M P R A S -->
                    <li>
                        <a href="?page=purchases" class="flex items-center p-2 text-base text-gray-900 rounded-lg hover:bg-gray-100 group <?php echo (isset($_GET['page']) && $_GET['page'] === 'purchases') ? 'bg-gray-100' : ''; ?>">
                            <i class="fas fa-truck w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900"></i>
                            <span class="ml-3">Compras</span>
                        </a>
                    </li>

                    <!-- P R O D U C T O S -->
                    <li>
                        <a href="?page=products" class="flex items-center p-2 text-base text-gray-900 rounded-lg hover:bg-gray-100 group <?php echo (isset($_GET['page']) && $_GET['page'] === 'products') ? 'bg-gray-100' : ''; ?>">
                            <i class="fas fa-box w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900"></i>
                            <span class="ml-3">Productos</span>
                        </a>
                    </li>

                    <!-- E M P L E A D O S -->
                    <li>
                        <a href="?page=users" class="flex items-center p-2 text-base text-gray-900 rounded-lg hover:bg-gray-100 group <?php echo (isset($_GET['page']) && $_GET['page'] === 'users') ? 'bg-gray-100' : ''; ?>">
                            <i class="fas fa-users w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900"></i>
                            <span class="ml-3">Empleados</span>
                        </a>
                    </li>
                </ul>
                <ul class="pt-4 mt-4 space-y-2">

                    <!-- A J U S T E S -->
                    <li>
                        <a href="?page=test" class="flex items-center p-2 text-base text-gray-900 rounded-lg hover:bg-gray-100 group">
                            <i class="fas fa-cog w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900"></i>
                            <span class="ml-3">Ajustes</span>
                        </a>
                    </li>

                    <!-- C E R R A R  S E S I Ó N -->
                    <li>
                        <button type="button" class="flex items-center p-2 text-base bg-red-500 text-white rounded-lg hover:bg-red-800 group" onclick="document.getElementById('close-session-modal').classList.remove('hidden')">
                            <i class="fas fa-sign-out-alt w-6 h-6 text-white transition duration-75 group-hover:text-white"></i>
                            <span class="ml-3">Cerrar Sesión</span> 
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</aside>

<!-- Backdrop -->
<div id="sidebarBackdrop" class="fixed inset-0 z-10 hidden bg-gray-900 bg-opacity-50 transition-opacity duration-300 ease-in-out opacity-0"></div>

<!-- Cerrar Sesión Modal -->
<div id="close-session-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="w-full max-w-md rounded-lg bg-white p-6">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-lg font-medium">Cerrar Sesión</h3>
            <button type="button" class="text-gray-500 hover:text-gray-700" onclick="document.getElementById('close-session-modal').classList.add('hidden')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="add_product">
            <div class="mb-4">
                <label for="name" class="mb-1 block text-sm font-medium">Estás a punto de cerrar sesión, ¿estás seguro?</label>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" class="rounded-md border border-gray-300 px-4 py-2 hover:bg-gray-50" onclick="document.getElementById('close-session-modal').classList.add('hidden')">
                    Cancelar
                </button>
                <button type="submit" class="rounded-md bg-red-500 px-4 py-2 text-white hover:bg-red-800">
                    Cerrar Sesión
                </button>
            </div>
        </form>
    </div>
</div>