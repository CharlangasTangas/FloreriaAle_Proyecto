<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar Morado Elegante</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Mejoras visuales para el navbar */
        .navbar-enhanced {
            background: rgba(88, 28, 135, 0.85);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(147, 51, 234, 0.3);
            box-shadow:
                0 0 0 1px rgba(255, 255, 255, 0.1),
                0 4px 20px rgba(0, 0, 0, 0.15),
                inset 0 1px 0 rgba(255, 255, 255, 0.15);
        }

        /* Botones del navbar mejorados */
        .navbar-btn-enhanced {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .navbar-btn-enhanced::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg,
                    transparent,
                    rgba(255, 255, 255, 0.1),
                    transparent);
            transition: left 0.6s ease;
        }

        .navbar-btn-enhanced:hover::before {
            left: 100%;
        }

        .navbar-btn-enhanced:hover {
            background: rgba(147, 51, 234, 0.4);
            box-shadow:
                0 4px 12px rgba(147, 51, 234, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
            transform: translateY(-1px);
        }

        /* Avatar mejorado */
        .avatar-enhanced {
            border: 2px solid rgba(168, 85, 247, 0.6);
            box-shadow:
                0 0 0 2px rgba(255, 255, 255, 0.1),
                0 4px 12px rgba(147, 51, 234, 0.3);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .avatar-enhanced:hover {
            border-color: rgba(168, 85, 247, 1);
            box-shadow:
                0 0 0 2px rgba(255, 255, 255, 0.2),
                0 6px 20px rgba(147, 51, 234, 0.4),
                0 0 20px rgba(168, 85, 247, 0.3);
            transform: scale(1.05);
        }

        /* Dropdown mejorado */
        .dropdown-enhanced {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow:
                0 25px 50px rgba(0, 0, 0, 0.25),
                0 0 0 1px rgba(255, 255, 255, 0.1);
        }

        /* Texto del navbar con sombra */
        .navbar-text-enhanced {
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
        }

        /* Logo mejorado */
        .logo-enhanced {
            filter: drop-shadow(0 2px 8px rgba(168, 85, 247, 0.3));
            transition: all 0.3s ease;
        }

        .logo-enhanced:hover {
            filter: drop-shadow(0 4px 12px rgba(168, 85, 247, 0.5));
            transform: scale(1.02);
        }

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
                    transparent);
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
                    transparent 100%);
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
    <!-- Navbar con efecto translúcido y desenfoque MEJORADO -->
    <nav id="navbar" class="navbar-enhanced fixed w-full z-30 transition-all duration-300 ease-in-out">
        <div class="px-3 py-3 lg:px-5 lg:pl-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center justify-start">
                    <!-- Botón para abrir/cerrar la sidebar -->
                    <button id="sidebarToggle" aria-expanded="true" aria-controls="sidebar"
                        class="navbar-btn-enhanced lg:hidden mr-2 text-purple-300 hover:text-white cursor-pointer p-2 rounded-lg">
                        <svg id="sidebarToggleOpen" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </button>
                    <a href="index.php"
                        class="logo-enhanced text-xl font-bold flex items-center lg:ml-2.5 text-white navbar-text-enhanced">
                        <span class="self-center whitespace-nowrap">Florería "Ale"</span>
                    </a>
                </div>
                <div class="flex items-center">
                    <!-- Botón de notificaciones -->
                    <button id="toggleNotifications"
                        class="navbar-btn-enhanced p-2 mr-1 text-purple-300 rounded-lg hover:text-white relative">
                        <span class="sr-only">Ver notificaciones</span>
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z">
                            </path>
                        </svg>
                        <!-- Indicador de notificaciones -->
                        <span
                            class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center shadow-lg">3</span>
                    </button>


                    <!-- Nombre del usuario -->
                    <?php if (isset($_SESSION['nombre'])): ?>
                        <span class="mx-2 text-purple-200 font-semibold navbar-text-enhanced">
                            <?= htmlspecialchars($_SESSION['nombre']) ?>
                        </span>
                    <?php else: ?>
                        <span class="mx-2 text-purple-200 font-semibold navbar-text-enhanced">
                            Invitado
                        </span>
                    <?php endif; ?>

                    <div class="flex items-center ml-3">
                        <!-- Menú de usuario -->
                        <button type="button" class="avatar-enhanced flex text-sm rounded-full" id="user-menu-button"
                            onclick="toggleUserDropdown()">
                            <span class="sr-only">Abrir menú de usuario</span>
                            <img class="w-8 h-8 rounded-full"
                                src="https://ui-avatars.com/api/?name=Admin+User&background=9333ea&color=fff"
                                alt="Foto de usuario">
                        </button>


                        
<!-- Dropdown del usuario -->
<div class="dropdown-enhanced z-50 hidden absolute right-4 top-16 text-base list-none divide-y divide-gray-100 rounded-xl shadow-2xl"
    id="dropdown-user" style="min-width: 200px;">
    <div class="px-4 py-3">
        <p class="text-sm text-gray-900 font-semibold">
            <?= isset($_SESSION['nombre']) ? htmlspecialchars($_SESSION['nombre']) : 'Invitado' ?>
        </p>
        <p class="text-sm font-medium text-gray-600 truncate">
            <?= isset($_SESSION['rol']) && !empty($_SESSION['rol']) ? htmlspecialchars($_SESSION['rol']) : 'Default' ?>
        </p>
    </div>
    <ul class="py-1">
        <li>
            <!-- Redirige al index.php -->
            <a href="index.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-purple-50 transition-colors duration-200">
                Dashboard
            </a>
        </li>
        <li>
            <!-- Botón para cerrar sesión -->
            <form method="POST" action="modules/logout.php" class="block px-4 py-2">
                <button type="submit" class="w-full text-left text-sm text-red-600 hover:bg-red-50 transition-colors duration-200">
                    Cerrar sesión
                </button>
            </form>
        </li>
    </ul>
</div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

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
                                <i
                                    class="fas fa-tachometer-alt w-6 h-6 text-purple-300 icon-enhanced transition duration-75 group-hover:text-white"></i>
                                <span class="ml-3">Estadísticas</span>
                            </a>
                        </li>

                        <!-- Ventas -->
                        <li>
                            <a href="?page=sales"
                                class="menu-item-enhanced flex items-center p-2 text-base text-white text-enhanced rounded-lg group">
                                <i
                                    class="fas fa-shopping-cart w-6 h-6 text-purple-300 icon-enhanced transition duration-75 group-hover:text-white"></i>
                                <span class="ml-3">Ventas</span>
                            </a>
                        </li>

                        <!-- Facturas -->
                        <li>
                            <a href="?page=invoices"
                                class="menu-item-enhanced flex items-center p-2 text-base text-white text-enhanced rounded-lg group">
                                <i
                                    class="fas fa-file-invoice w-6 h-6 text-purple-300 icon-enhanced transition duration-75 group-hover:text-white"></i>
                                <span class="ml-3">Facturas</span>
                            </a>
                        </li>

                        <!-- Compras -->
                        <li>
                            <a href="?page=purchases"
                                class="menu-item-enhanced flex items-center p-2 text-base text-white text-enhanced rounded-lg group">
                                <i
                                    class="fas fa-truck w-6 h-6 text-purple-300 icon-enhanced transition duration-75 group-hover:text-white"></i>
                                <span class="ml-3">Compras</span>
                            </a>
                        </li>

                        <!-- Productos -->
                        <li>
                            <a href="?page=products"
                                class="menu-item-enhanced flex items-center p-2 text-base text-white text-enhanced rounded-lg group">
                                <i
                                    class="fas fa-box w-6 h-6 text-purple-300 icon-enhanced transition duration-75 group-hover:text-white"></i>
                                <span class="ml-3">Productos</span>
                            </a>
                        </li>

                        <!-- Empleados -->
                        <li>
                            <a href="?page=users"
                                class="menu-item-enhanced flex items-center p-2 text-base text-white text-enhanced rounded-lg group">
                                <i
                                    class="fas fa-users w-6 h-6 text-purple-300 icon-enhanced transition duration-75 group-hover:text-white"></i>
                                <span class="ml-3">Empleados</span>
                            </a>
                        </li>

                        <!-- Clientes -->
                        <li>
                            <a href="?page=customer"
                                class="menu-item-enhanced flex items-center p-2 text-base text-white text-enhanced rounded-lg group">
                                <i
                                    class="fa-solid fa-address-card w-6 h-6 text-purple-300 icon-enhanced transition duration-75 group-hover:text-white"></i>
                                <span class="ml-3">Clientes</span>
                            </a>
                        </li>

                        <!-- Proveedor -->
                        <li>
                            <a href="?page=supplier"
                                class="menu-item-enhanced flex items-center p-2 text-base text-white text-enhanced rounded-lg group">
                                <i
                                    class="fa-solid fa-boxes-packing w-6 h-6 text-purple-300 icon-enhanced transition duration-75 group-hover:text-white"></i>
                                <span class="ml-3">Proveedor</span>
                            </a>
                        </li>

                        <!-- Pérdidas -->
                        <li>
                            <a href="?page=perdida"
                                class="menu-item-enhanced flex items-center p-2 text-base text-white text-enhanced rounded-lg group">
                                <i
                                    class="fa-solid fa-square-minus w-6 h-6 text-purple-300 icon-enhanced transition duration-75 group-hover:text-white"></i>
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
    <div id="close-session-modal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="modal-enhanced w-full max-w-md rounded-xl p-6">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-800">Cerrar Sesión</h3>
                <button type="button"
                    class="text-gray-500 hover:text-gray-700 transition-colors duration-200 p-2 rounded-lg hover:bg-gray-100"
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
                    <button type="button"
                        class="rounded-lg border border-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-50 transition-all duration-200"
                        onclick="document.getElementById('close-session-modal').classList.add('hidden')">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="rounded-lg bg-red-500 px-4 py-2 text-white hover:bg-red-600 transition-all duration-200 shadow-lg hover:shadow-xl">
                        Cerrar Sesión
                    </button>
                </div>
            </form>
        </div>
    </div>


    <script>
        // Script para toggle del sidebar
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

        // Script para toggle del dropdown de usuario
        function toggleUserDropdown() {
            const dropdown = document.getElementById('dropdown-user');
            dropdown.classList.toggle('hidden');
        }

        // Cerrar dropdown al hacer click fuera
        document.addEventListener('click', function (event) {
            const dropdown = document.getElementById('dropdown-user');
            const button = document.getElementById('user-menu-button');

            if (!dropdown.contains(event.target) && !button.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });

        // Conectar el botón del sidebar toggle
        document.getElementById('sidebarToggle').addEventListener('click', toggleSidebar);

        // Cerrar sidebar al hacer click en backdrop
        document.getElementById('sidebarBackdrop').addEventListener('click', function () {
            toggleSidebar();
        });

        // Agregar clase active al elemento actual (ejemplo)
        document.addEventListener('DOMContentLoaded', function () {
            // Aquí puedes agregar lógica para marcar el elemento activo
            // basado en la página actual
        });
    </script>
</body>

</html>