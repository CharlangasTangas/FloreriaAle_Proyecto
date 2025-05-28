<?php

session_start();
include 'config\connection.php'; 

if (!isset($_SESSION['usuario'])) {
    include 'modules/login.php';
    exit; 
}
include 'includes/header.php';
include 'includes/navbar.php';
include 'includes/sidebar.php';

// Determine which page to load
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
$valid_pages = ['dashboard', 'sales', 'invoices', 'purchases', 'products', 'users', 'customer','supplier', 'test'];

if (!in_array($page, $valid_pages)) {
    $page = 'dashboard';
}

// Load the appropriate module
$module_path = 'modules/' . $page . '.php';
?>

<div class="flex overflow-hidden bg-purple-50 pt-16">
    <!-- Sidebar is included above -->
    
    <!-- Content area -->
    <div id="main-content" class="relative w-full h-full overflow-y-auto bg-purple-50 lg:ml-64 transition-margin duration-300 ">
        <main class="p-4 md:p-6 bg-purple-50">
            <?php
            if (file_exists($module_path)) {
                include $module_path;
            } else {
                echo '<div class="p-4 bg-white rounded-lg shadow">Page not found.</div>';
            }
            ?>
        </main>
    </div>
</div>

<?php 
    //include 'includes/footer.php'; 
?>