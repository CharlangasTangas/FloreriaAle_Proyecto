<?php
include 'config/connection.php';
//Agregar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'add_product') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $precioCompra = floatval($_POST['precioCompra']);
    $precioVenta = floatval($_POST['precioVenta']);
    $stock = intval($_POST['stock']);
    $category = trim($_POST['category']);

    $stmt = $connection->prepare("INSERT INTO Producto (nombre, descripcion, categoria, stock, precioCompra, precioVenta) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssidd", $name, $description, $category, $stock, $precioCompra, $precioVenta);

    if ($stmt->execute()) {
     echo "<script>
            localStorage.setItem('productAdded', '1');
            window.location.href='index.php?page=products';
        </script>";
    exit;
} else {
    echo "<script>alert('Error al agregar producto: " . $stmt->error . "');</script>";
}}
//Editar
elseif($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'edit_product') {
    $id = intval($_POST['id']); // el input oculto en el formulario es "id"
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $precioCompra = floatval($_POST['precioCompra']);
    $precioVenta = floatval($_POST['precioVenta']);
    $stock = intval($_POST['stock']);
    $category = trim($_POST['category']);

    $stmt = $connection->prepare("UPDATE Producto SET nombre = ?, descripcion = ?, categoria = ?, stock = ?, precioCompra = ?, precioVenta = ? WHERE idProducto = ?");
    $stmt->bind_param("sssiddi", $name, $description, $category, $stock, $precioCompra, $precioVenta, $id);

    if ($stmt->execute()) {
       echo "<script>
            localStorage.setItem('productEdited', '1');
            window.location.href='index.php?page=products';
        </script>";
    } else {
        echo "<script>alert('Error al actualizar producto: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}

// Eliminar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_product') {
    $delete_id = intval($_POST['product_id']);

    $stmt = $connection->prepare("DELETE FROM Producto WHERE idProducto = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        echo "<script>
            localStorage.setItem('deleteSuccess', '1');
            window.location.href ='index.php?page=products.php';
        </script>";
        exit;
    } else {
        echo "<script>alert('Error al eliminar el producto.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Administrador de Productos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background:   #764ba2 ;
        min-height: 100vh;
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        position: center;
    }

    .tab-button {
        border-bottom: 2px solid transparent;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .tab-button.active {
        background:  rgb(123, 31, 162);
        color: white;
        font-weight: 600;
        border-radius: 8px 8px 0 0;
    }

    .tab-button:not(.active):hover {
        background-color: #f8fafc;
        color: #4f46e5;
    }

    /* Modern Form Styles */
    .form-section {
        background: #ffffff;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
    }

    .form-section h3 {
        font-size: 16px;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-group {
        margin-bottom: 16px;
    }

    .form-label {
        display: block;
        font-size: 14px;
        font-weight: 500;
        color: #374151;
        margin-bottom: 6px;
    }

    .form-input {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s ease;
        background-color: #f9fafb;
    }

    .form-input:focus {
        outline: none;
        border-color: #667eea;
        background-color: white;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .form-textarea {
        min-height: 80px;
        resize: vertical;
    }

    .form-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    /* Modern Buttons */
    .btn-primary {
        background: rgb(123, 31, 162) ;
        color: white;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        border: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
    }

    .btn-secondary {
        background: white;
        color: #6b7280;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 500;
        border: 2px solid #e5e7eb;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-secondary:hover {
        border-color: #d1d5db;
        background-color: #f9fafb;
    }

    /* Table Styles */
    table {
        border-collapse: collapse;
        width: 100%;
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    th,
    td {
        text-align: left;
        padding: 16px;
        border-bottom: 1px solid #e5e7eb;
    }

    th {
        background:  #764ba2 ;
        color: white;
        font-weight: 600;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    tr:hover {
        background-color: #f8fafc;
    }

    /* Search Input */
    .search-input {
        position: relative;
        margin-bottom: 20px;
    }

    .search-input input {
        padding-left: 48px;
        padding-right: 16px;
        padding-top: 12px;
        padding-bottom: 12px;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        width: 100%;
        font-size: 14px;
        background-color: white;
        transition: all 0.3s ease;
    }

    .search-input input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .search-input i {
        position: center;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
    }

    /* Action Buttons */
    .action-btn {
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .btn-edit {
        background-color: #ede9fe;
        color: #6b21a8;
        position: center;
    }

    .btn-edit:hover {
        background-color: #ddd6fe;
        transform: translateY(-1px);
    }

    .btn-delete {
        background-color: #fee2e2;
        color: #dc2626;
        margin-left: 8px;
    }

    .btn-delete:hover {
        background-color: #fecaca;
        transform: translateY(-1px);
    }

    /* Category Pills */
    .categoria-1 { background: linear-gradient(135deg, #0ea5e9, #0284c7); color: white; }
    .categoria-2 { background: linear-gradient(135deg, #10b981, #059669); color: white; }
    .categoria-3 { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; }
    .categoria-4 { background: linear-gradient(135deg, #a855f7, #9333ea); color: white; }
    .categoria-5 { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; }

    .category-pill {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        display: inline-block;
    }

    /* Animations */
    .tab-content {
        animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Success message */
    .success-message {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        padding: 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
</style>

<body>
    <div class="min-h-screen py-8 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="glass-card rounded-2xl p-8">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-box text-white text-xl"></i>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-800">Administrador de Productos</h1>
                </div>

                <!-- Tabs -->
                <div class="flex space-x-1 bg-gray-100 p-1 rounded-xl mb-8">
                    <button class="tab-button flex-1 py-3 px-6 rounded-lg font-medium transition-all duration-300 border-blue-500 text-blue-600 font-semibold active" data-tab="list">
                        <i class="fas fa-list mr-2"></i>Listado
                    </button>
                    <button class="tab-button flex-1 py-3 px-6 rounded-lg font-medium transition-all duration-300 text-gray-600" data-tab="add">
                        <i class="fas fa-plus mr-2"></i>Agregar
                    </button>
                    <button id="edit-tab" class="tab-button flex-1 py-3 px-6 rounded-lg font-medium transition-all duration-300 text-gray-600 hidden" data-tab="edit">
                        <i class="fas fa-edit mr-2"></i>Editar
                    </button>
                </div>

                <!-- Listado de Productos -->
                <div id="list-tab-content" class="tab-content">
                    <div class="search-input">
                        <i class="fas fa-search"></i>
                        <input type="text" id="product-search" placeholder="Buscar producto por nombre, categoría o descripción..." />
                    </div>

                    <div class="overflow-x-auto">
                        <table>
                            <thead>
                                <tr>
                                    <th><i class="fas fa-hashtag mr-2"></i>ID</th>
                                    <th><i class="fas fa-tag mr-2"></i>Nombre</th>
                                    <th><i class="fas fa-align-left mr-2"></i>Descripción</th>
                                    <th><i class="fas fa-dollar-sign mr-2"></i>P. Compra</th>
                                    <th><i class="fas fa-dollar-sign mr-2"></i>P. Venta</th>
                                    <th><i class="fas fa-boxes mr-2"></i>Stock</th>
                                    <th><i class="fas fa-folder mr-2"></i>Categoría</th>
                                    <th><i class="fas fa-cogs mr-2"></i>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tablaProductos">
                                <?php
                                $result = $connection->query("SELECT * FROM Producto");
                                while ($product = $result->fetch_assoc()):
                                ?>
                                <tr class="product-row" data-id="<?php echo $product['idProducto']; ?>">
                                    <td class="font-semibold"><?php echo htmlspecialchars($product['idProducto']); ?></td>
                                    <td class="font-medium"><?php echo htmlspecialchars($product['nombre']); ?></td>
                                    <td class="text-gray-600"><?php echo htmlspecialchars($product['descripcion']); ?></td>
                                    <td class="font-semibold text-green-600">$<?php echo number_format($product['precioCompra'], 2); ?></td>
                                    <td class="font-semibold text-blue-600">$<?php echo number_format($product['precioVenta'], 2); ?></td>
                                    <td class="font-semibold"><?php echo htmlspecialchars($product['stock']); ?></td>
                                    <td>
                                        <span class="category-pill categoria-<?php echo intval($product['categoria']); ?>">
                                            <?php echo htmlspecialchars($product['categoria']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="action-btn btn-edit edit-product-btn"
                                            data-id="<?php echo $product['idProducto']; ?>"
                                            data-name="<?php echo htmlspecialchars($product['nombre']); ?>"
                                            data-description="<?php echo htmlspecialchars($product['descripcion']); ?>"
                                            data-preciocompra="<?php echo htmlspecialchars($product['precioCompra']); ?>"
                                            data-precioventa="<?php echo htmlspecialchars($product['precioVenta']); ?>"
                                            data-stock="<?php echo htmlspecialchars($product['stock']); ?>"
                                            data-category="<?php echo htmlspecialchars($product['categoria']); ?>"
                                        >
                                            <i class="fas fa-edit"></i>Editar
                                        </button>
                                        <button class="action-btn btn-delete delete-product-btn" data-id="<?php echo $product['idProducto']; ?>">
                                            <i class="fas fa-trash"></i>Eliminar
                                        </button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Formulario Agregar Producto -->
                <div id="add-tab-content" class="tab-content hidden">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-plus text-white"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800">Agregar Nuevo Producto</h2>
                    </div>

                    <form action="" method="post" class="space-y-6">
                        <input type="hidden" name="action" value="add_product" />
                        
                        <div class="form-section">
                            <h3><i class="fas fa-info-circle text-blue-500"></i>Información General</h3>
                            
                            <div class="form-group">
                                <label class="form-label">Nombre del Producto</label>
                                <input type="text" name="name" class="form-input" placeholder="Nombre de la flor" required />
                            </div>

                            <div class="form-group">
                                <label class="form-label">Descripción del Producto</label>
                                <textarea name="description" class="form-input form-textarea" placeholder="¿Qué color es? ¿Cuántas piezas son?" required></textarea>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Categoría</label>
                                <input type="text" name="category" class="form-input" placeholder="1. Invernadero 2. Extrangero" required />
                            </div>
                        </div>

                        <div class="form-section">
                            <h3><i class="fas fa-dollar-sign text-green-500"></i>Precios y Stock</h3>
                            
                            <div class="form-grid-2">
                                <div class="form-group">
                                    <label class="form-label">Precio Base (Compra)</label>
                                    <input type="number" name="precioCompra" step="0.01" class="form-input" placeholder="$470.55" required />
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Precio de Venta</label>
                                    <input type="number" name="precioVenta" step="0.01" class="form-input" placeholder="$856.00" required />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Stock Inicial</label>
                                <input type="number" name="stock" class="form-input" placeholder="" required />
                            </div>
                        </div>

                        <div class="flex gap-4 pt-6 border-t">
                            <button type="submit" class="btn-primary">
                                <i class="fas fa-save"></i>Agregar Producto
                            </button>
                            <button type="button" class="btn-secondary cancel-btn">
                                <i class="fas fa-times"></i>Cancelar
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Formulario Editar Producto -->
                <div id="edit-tab-content" class="tab-content hidden">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-indigo-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-edit text-white"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800">Editar Producto</h2>
                    </div>

                    <form action="" method="post" class="space-y-6">
                        <input type="hidden" name="action" value="edit_product" />
                        <input type="hidden" name="id" id="edit_id" />
                        
                        <div class="form-section">
                            <h3><i class="fas fa-info-circle text-blue-500"></i>Información General</h3>
                            
                            <div class="form-group">
                                <label class="form-label">Nombre del Producto</label>
                                <input type="text" name="name" id="edit_name" class="form-input" required />
                            </div>

                            <div class="form-group">
                                <label class="form-label">Descripción del Producto</label>
                                <textarea name="description" id="edit_description" class="form-input form-textarea" required></textarea>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Categoría</label>
                                <input type="text" name="category" id="edit_category" class="form-input" required />
                            </div>
                        </div>

                        <div class="form-section">
                            <h3><i class="fas fa-dollar-sign text-green-500"></i>Precios y Stock</h3>
                            
                            <div class="form-grid-2">
                                <div class="form-group">
                                    <label class="form-label">Precio Base (Compra)</label>
                                    <input type="number" name="precioCompra" id="edit_precioCompra" step="0.01" class="form-input" required />
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Precio de Venta</label>
                                    <input type="number" name="precioVenta" id="edit_precioVenta" step="0.01" class="form-input" required />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Stock</label>
                                <input type="number" name="stock" id="edit_stock" class="form-input" required />
                            </div>
                        </div>

                        <div class="flex gap-4 pt-6 border-t">
                            <button type="submit" class="btn-primary">
                                <i class="fas fa-save"></i>Guardar Cambios
                            </button>
                            <button type="button" class="btn-secondary cancel-btn">
                                <i class="fas fa-times"></i>Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Tabs
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.tab-button').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                const tab = btn.dataset.tab;
                document.querySelectorAll('.tab-content').forEach(tc => tc.classList.add('hidden'));
                document.getElementById(`${tab}-tab-content`).classList.remove('hidden');
            });
        });

        // Buscar productos
        document.getElementById('product-search').addEventListener('input', function() {
            const term = this.value.toLowerCase();
            document.querySelectorAll('.product-row').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
            });
        });

        // Editar producto
        document.querySelectorAll('.edit-product-btn').forEach(button => {
            button.addEventListener('click', () => {
                document.getElementById('edit-tab').classList.remove('hidden');
                document.getElementById('edit-tab').click();

                document.getElementById('edit_id').value = button.dataset.id;
                document.getElementById('edit_name').value = button.dataset.name;
                document.getElementById('edit_description').value = button.dataset.description;
                document.getElementById('edit_precioCompra').value = button.dataset.preciocompra;
                document.getElementById('edit_precioVenta').value = button.dataset.precioventa;
                document.getElementById('edit_stock').value = button.dataset.stock;
                document.getElementById('edit_category').value = button.dataset.category;
            });
        });

        // Cancelar formulario
        document.querySelectorAll('.cancel-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.tab-content').forEach(tc => tc.classList.add('hidden'));
                document.getElementById('list-tab-content').classList.remove('hidden');

                document.querySelectorAll('.tab-button').forEach(b => b.classList.remove('active'));
                document.querySelector('[data-tab="list"]').classList.add('active');
            });
        });

        // Eliminar producto
        document.querySelectorAll('.delete-product-btn').forEach(button => {
            button.addEventListener('click', () => {
                const productId = button.dataset.id;

                if (confirm('¿Estás seguro de que deseas eliminar este producto?')) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = window.location.href;

                    const actionInput = document.createElement('input');
                    actionInput.type = 'hidden';
                    actionInput.name = 'action';
                    actionInput.value = 'delete_product';

                    const idInput = document.createElement('input');
                    idInput.type = 'hidden';
                    idInput.name = 'product_id';
                    idInput.value = productId;

                    form.appendChild(actionInput);
                    form.appendChild(idInput);

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    </script>
</body>
</html>