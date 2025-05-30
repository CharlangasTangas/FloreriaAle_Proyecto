<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar Morado Elegante</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Mejoras visuales para el sidebar */
        .sidebar-enhanced {
            background: rgba(88, 28, 135, 0.85);
            backdrop-filter: blur(15px);
            border-right: 1px solid rgba(147, 51, 234, 0.3);
            box-shadow: 
                0 0 0 1px rgba(255, 255, 255, 0.1),
                0 25px 50px rgba(0, 0, 0, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.15);
        }
        
        /* Efectos de hover mejorados */
        .menu-item-enhanced {
            position: relative;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .menu-item-enhanced::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, 
                transparent, 
                rgba(255, 255, 255, 0.1), 
                transparent
            );
            transition: left 0.6s ease;
        }
        
        .menu-item-enhanced:hover::before {
            left: 100%;
        }
        
        .menu-item-enhanced:hover {
            background: rgba(147, 51, 234, 0.4);
            transform: translateX(6px);
            border-left: 3px solid rgba(168, 85, 247, 0.8);
            box-shadow: 
                0 8px 25px rgba(147, 51, 234, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }
        
        /* Iconos con efecto glow sutil */
        .icon-enhanced {
            filter: drop-shadow(0 0 6px rgba(168, 85, 247, 0.3));
            transition: all 0.3s ease;
        }
        
        .menu-item-enhanced:hover .icon-enhanced {
            filter: drop-shadow(0 0 10px rgba(168, 85, 247, 0.6));
            transform: scale(1.05);
        }
        
        /* Divider elegante */
        .divider-enhanced {
            background: linear-gradient(90deg, 
                transparent 0%, 
                rgba(147, 51, 234, 0.3) 20%,
                rgba(168, 85, 247, 0.4) 50%,
                rgba(147, 51, 234, 0.3) 80%,
                transparent 100%
            );
            height: 1px;
            box-shadow: 0 1px 2px rgba(168, 85, 247, 0.2);
        }
        
        /* Botón de logout mejorado pero conservando estructura */
        .logout-enhanced {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            box-shadow: 
                0 0 0 1px rgba(255, 255, 255, 0.1),
                0 10px 20px rgba(239, 68, 68, 0.3);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .logout-enhanced:hover {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            box-shadow: 
                0 0 0 1px rgba(255, 255, 255, 0.2),
                0 15px 30px rgba(239, 68, 68, 0.4);
            transform: translateY(-2px) translateX(6px);
        }
        
        /* Modal mejorado manteniendo funcionalidad */
        .modal-enhanced {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 
                0 25px 50px rgba(0, 0, 0, 0.25),
                0 0 0 1px rgba(255, 255, 255, 0.1);
        }
        
        /* Backdrop mejorado */
        .backdrop-enhanced {
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }
        
        /* Texto con sombra sutil */
        .text-enhanced {
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
        }
        
        /* Efecto de profundidad en elementos activos */
        .active-item {
            background: rgba(168, 85, 247, 0.35);
            border-left: 3px solid rgba(168, 85, 247, 1);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Sidebar con efecto translúcido y desenfoque MEJORADO -->
    <aside id="sidebar"
        class="fixed top-0 left-0 z-20 flex flex-col flex-shrink-0 w-64 h-full pt-16 font-normal sidebar-enhanced transition-transform duration-300 ease-in-out transform -translate-x-full lg:translate-x-0">
        
        <div class="relative flex flex-col flex-1 min-h-0 pt-0">
            <div class="flex flex-col flex-1 pt-5 pb-4 overflow-y-auto">
                <div class="flex-1 px-3 space-y-1 divide-y divide-purple-700">
                    <ul class="pb-2 space-y-2">
                        
                        <!-- Elementos del menú de navegación MEJORADOS -->
                        
                        <!-- Estadísticas -->
                        <li>
                            <a href="?page=dashboard"
                                class="menu-item-enhanced flex items-center p-2 text-base text-white text-enhanced rounded-lg group">
                                <i class="fas fa-tachometer-alt w-6 h-6 text-purple-300 icon-enhanced transition duration-75 group-hover:text-white"></i>
                                <span class="ml-3">Estadísticas</span>
                            </a>
                        </li>

                        <!-- Ventas -->
                        <li>
                            <a href="?page=sales"
                                class="menu-item-enhanced flex items-center p-2 text-base text-white text-enhanced rounded-lg group">
                                <i class="fas fa-shopping-cart w-6 h-6 text-purple-300 icon-enhanced transition duration-75 group-hover:text-white"></i>
                                <span class="ml-3">Ventas</span>
                            </a>
                        </li>

                        <!-- Facturas -->
                        <li>
                            <a href="?page=invoices"
                                class="menu-item-enhanced flex items-center p-2 text-base text-white text-enhanced rounded-lg group">
                                <i class="fas fa-file-invoice w-6 h-6 text-purple-300 icon-enhanced transition duration-75 group-hover:text-white"></i>
                                <span class="ml-3">Facturas</span>
                            </a>
                        </li>

                        <!-- Compras -->
                        <li>
                            <a href="?page=purchases"
                                class="menu-item-enhanced flex items-center p-2 text-base text-white text-enhanced rounded-lg group">
                                <i class="fas fa-truck w-6 h-6 text-purple-300 icon-enhanced transition duration-75 group-hover:text-white"></i>
                                <span class="ml-3">Compras</span>
                            </a>
                        </li>

                        <!-- Productos -->
                        <li>
                            <a href="?page=products"
                                class="menu-item-enhanced flex items-center p-2 text-base text-white text-enhanced rounded-lg group">
                                <i class="fas fa-box w-6 h-6 text-purple-300 icon-enhanced transition duration-75 group-hover:text-white"></i>
                                <span class="ml-3">Productos</span>
                            </a>
                        </li>

                        <!-- Empleados -->
                        <li>
                            <a href="?page=users"
                                class="menu-item-enhanced flex items-center p-2 text-base text-white text-enhanced rounded-lg group">
                                <i class="fas fa-users w-6 h-6 text-purple-300 icon-enhanced transition duration-75 group-hover:text-white"></i>
                                <span class="ml-3">Empleados</span>
                            </a>
                        </li>

                        <!-- Clientes -->
                        <li>
                            <a href="?page=customer"
                                class="menu-item-enhanced flex items-center p-2 text-base text-white text-enhanced rounded-lg group">
                                <i class="fa-solid fa-address-card w-6 h-6 text-purple-300 icon-enhanced transition duration-75 group-hover:text-white"></i>
                                <span class="ml-3">Clientes</span>
                            </a>
                        </li>

                        <!-- Proveedor -->
                        <li>
                            <a href="?page=supplier"
                                class="menu-item-enhanced flex items-center p-2 text-base text-white text-enhanced rounded-lg group">
                                <i class="fa-solid fa-boxes-packing w-6 h-6 text-purple-300 icon-enhanced transition duration-75 group-hover:text-white"></i>
                                <span class="ml-3">Proveedor</span>
                            </a>
                        </li>

                        <!-- Pérdidas -->
                        <li>
                            <a href="?page=perdida"
                                class="menu-item-enhanced flex items-center p-2 text-base text-white text-enhanced rounded-lg group">
                                <i class="fa-solid fa-square-minus w-6 h-6 text-purple-300 icon-enhanced transition duration-75 group-hover:text-white"></i>
                                <span class="ml-3">Pérdidas</span>
                            </a>
                        </li>

                    </ul>

                    <!-- Sección inferior con opción de cerrar sesión -->
                    <ul class="pt-4 mt-4 space-y-2">
                        
                        <!-- Divider mejorado -->
                        <div class="divider-enhanced mb-4"></div>
                        
                        <!-- Cerrar Sesión MEJORADO -->
                        <li>
                            <a href="modules/logout.php"
                                class="logout-enhanced flex items-center p-2 text-base text-white text-enhanced rounded-lg group">
                                <i class="fas fa-sign-out-alt w-6 h-6 text-white transition duration-75"></i>
                                <span class="ml-3">Cerrar Sesión</span>
                            </a>
                        </li>

                    </ul>
                </div>
            </div>
        </div>
    </aside>

    <!-- Backdrop mejorado para la animación de apertura -->
    <div id="sidebarBackdrop"
        class="backdrop-enhanced fixed inset-0 z-10 hidden transition-opacity duration-300 ease-in-out opacity-0">
    </div>

    <!-- Modal de Cerrar Sesión MEJORADO -->
    <div id="close-session-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="modal-enhanced w-full max-w-md rounded-xl p-6">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-800">Cerrar Sesión</h3>
                <button type="button" class="text-gray-500 hover:text-gray-700 transition-colors duration-200 p-2 rounded-lg hover:bg-gray-100"
                    onclick="document.getElementById('close-session-modal').classList.add('hidden')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST">
                <div class="mb-4">
                    <label for="name" class="mb-1 block text-sm font-medium text-gray-700">
                        Estás a punto de cerrar sesión, ¿estás seguro?
                    </label>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" class="rounded-lg border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50 transition-all duration-200"
                        onclick="document.getElementById('close-session-modal').classList.add('hidden')">
                        Cancelar
                    </button>
                    <button type="submit" class="rounded-lg bg-red-500 px-4 py-2 text-white hover:bg-red-600 transition-all duration-200 shadow-lg hover:shadow-xl">
                        Cerrar Sesión
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Script para demostrar el toggle del sidebar en móvil
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            
            sidebar.classList.toggle('-translate-x-full');
            
            if (sidebar.classList.contains('-translate-x-full')) {
                backdrop.classList.add('hidden');
                backdrop.classList.add('opacity-0');
            } else {
                backdrop.classList.remove('hidden');
                setTimeout(() => backdrop.classList.remove('opacity-0'), 10);
            }
        }

        // Cerrar sidebar al hacer click en backdrop
        document.getElementById('sidebarBackdrop').addEventListener('click', function() {
            toggleSidebar();
        });
        
        // Agregar clase active al elemento actual (ejemplo)
        document.addEventListener('DOMContentLoaded', function() {
            // Aquí puedes agregar lógica para marcar el elemento activo
            // basado en la página actual
        });
    </script>
</body>
</html>