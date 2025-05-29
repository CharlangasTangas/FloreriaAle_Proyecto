<?php 
include 'config/connection.php';

// Procesar formulario de nueva pérdida
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'add_perdida') {
    $empleado = trim($_POST['empleado'] ?? '');
    $producto = trim($_POST['producto'] ?? '');
    $cantidad = intval($_POST['cantidad'] ?? '');
    $razon = trim($_POST['razon'] ?? '');
    $fecha = trim($_POST['fecha'] ?? '');

    if (!$empleado || !$producto || !$cantidad || !$razon || !$fecha) {
        echo "<script>
            alert('Todos los campos son obligatorios y deben tener valores válidos.');
            window.history.back();
        </script>";
        exit;
    } else {
        // Obtener precio y stock actual del producto
        $stmtProducto = $connection->prepare("SELECT precioCompra, stock FROM Producto WHERE idProducto = ?");
        $stmtProducto->bind_param("s", $producto);
        $stmtProducto->execute();
        $resultadoProducto = $stmtProducto->get_result();

        if ($row = $resultadoProducto->fetch_assoc()) {
            $precioProducto = floatval($row['precioCompra']);
            $stockActual = intval($row['stock']);

            if ($cantidad > $stockActual) {
                echo "<script>
                    alert('No hay suficiente stock para registrar esta pérdida.');
                    window.history.back();
                </script>";
                exit;
            }

            $valorTotal = $precioProducto * $cantidad;

            // Iniciar transacción
            $connection->begin_transaction();

            try {
                // Insertar la pérdida
                $stmt = $connection->prepare("INSERT INTO Perdida (idEmpleado, idProducto, cantidad, total, razon, fecha) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssidss", $empleado, $producto, $cantidad, $valorTotal, $razon, $fecha);
                $stmt->execute();

                // Actualizar stock del producto
                $nuevoStock = $stockActual - $cantidad;
                $stmtStock = $connection->prepare("UPDATE Producto SET stock = ? WHERE idProducto = ?");
                $stmtStock->bind_param("is", $nuevoStock, $producto);
                $stmtStock->execute();

                // Confirmar transacción
                $connection->commit();

                echo "<script>
                    localStorage.setItem('perdidaRegistrada', '1');
                    window.location.href='index.php?page=perdida';
                </script>";
                exit;

            } catch (Exception $e) {
                $connection->rollback();
                $msg = json_encode("Error al registrar la pérdida: " . $connection->error);
                echo "<script>
                    alert($msg);
                    window.history.back();
                </script>";
                exit;
            }
        } else {
            echo "<script>
                alert('No se encontró el producto con el ID proporcionado.');
                window.history.back();
            </script>";
            exit;
        }
        $stmtProducto->close();
    }
}

// Procesar eliminación de pérdida
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'delete_perdida') {
    $idPerdida = intval($_POST['id_perdida'] ?? 0);

    if ($idPerdida > 0) {
        // Iniciar transacción
        $connection->begin_transaction();

        try {
            // Obtener idProducto y cantidad de la pérdida
            $stmtDatos = $connection->prepare("SELECT idProducto, cantidad FROM Perdida WHERE idPerdida = ?");
            $stmtDatos->bind_param("i", $idPerdida);
            $stmtDatos->execute();
            $resultado = $stmtDatos->get_result();

            if ($row = $resultado->fetch_assoc()) {
                $idProducto = $row['idProducto'];
                $cantidadPerdida = intval($row['cantidad']);

                // Devolver cantidad al stock
                $stmtUpdateStock = $connection->prepare("UPDATE Producto SET stock = stock + ? WHERE idProducto = ?");
                $stmtUpdateStock->bind_param("is", $cantidadPerdida, $idProducto);
                $stmtUpdateStock->execute();

                // Eliminar la pérdida
                $stmtDelete = $connection->prepare("DELETE FROM Perdida WHERE idPerdida = ?");
                $stmtDelete->bind_param("i", $idPerdida);
                $stmtDelete->execute();

                // Confirmar transacción
                $connection->commit();

                echo "<script>
                    localStorage.setItem('deletePerdida', '1');
                    window.location.href='index.php?page=perdida';
                </script>";
                exit;
            } else {
                throw new Exception("No se encontró la pérdida con ese ID.");
            }

        } catch (Exception $e) {
            $connection->rollback();
            $msg = json_encode("Error al eliminar la pérdida: " . $e->getMessage());
            echo "<script>
                alert($msg);
                window.history.back();
            </script>";
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Pérdidas - Florería Ale</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .controls {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }

        .search-container {
            display: flex;
            gap: 15px;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .search-box {
            flex: 1;
            min-width: 300px;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 15px 50px 15px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .search-box input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .search-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            color: white;
            padding: 6px 12px;
            font-size: 14px;
        }

        .btn-danger:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.3);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .stat-card h3 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .stat-card p {
            opacity: 0.9;
            font-size: 1.1rem;
        }

        .content {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #f8f9fa;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            font-weight: 600;
            color: #333;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 1px;
        }

        tr:hover {
            background-color: #f8f9fa;
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-daño, .status-dano {
            background: rgba(156, 39, 176, 0.1);
            color: #9c27b0;
        }

        .status-robo {
            background: rgba(255, 165, 0, 0.1);
            color: #ff8c00;
        }

        .status-vencimiento {
            background: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }

        .status-pérdida, .status-perdida {
            background: rgba(255, 107, 107, 0.1);
            color: #ff6b6b;
        }

        .status-otro {
            background: rgba(108, 117, 125, 0.1);
            color: #6c757d;
        }

        .no-results {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
            animation: fadeIn 0.3s ease;
        }

        .modal.show {
            display: flex !important;
            align-items: center;
            justify-content: center;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content {
            background-color: white;
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            animation: slideIn 0.3s ease;
            max-height: 90vh;
            overflow-y: auto;
        }

        @keyframes slideIn {
            from { 
                transform: translateY(-50px); 
                opacity: 0; 
            }
            to { 
                transform: translateY(0); 
                opacity: 1; 
            }
        }

        .modal h2 {
            margin-bottom: 25px;
            color: #333;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .modal-buttons {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 30px;
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @media (max-width: 768px) {
            .search-container {
                flex-direction: column;
            }
            
            .search-box {
                min-width: 100%;
            }
            
            .stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🌸 Pérdidas</h1>
            <p>Administra y controla las pérdidas de inventario</p>
        </div>

        <?php if (isset($mensaje)): ?>
            <div class="alert alert-<?php echo $tipo_mensaje; ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <div class="controls">
            <div class="search-container">
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Buscar por ID de pérdida o nombre del empleado...">
                    <span class="search-icon">🔍</span>
                </div>
                <button type="button" class="btn btn-primary" onclick="openModal()">+ Nueva Pérdida</button>
            </div>

            <div class="stats">
                <?php
                // Calcular estadísticas
                $stats_query = "SELECT 
                    COUNT(*) as total_perdidas,
                    COALESCE(SUM(total), 0) as valor_total,
                    COUNT(CASE WHEN MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE()) THEN 1 END) as mes_actual
                    FROM Perdida";
                $stats_result = $connection->query($stats_query);
                $stats = $stats_result->fetch_assoc();
                ?>
                <div class="stat-card">
                    <h3><?php echo $stats['total_perdidas']; ?></h3>
                    <p>Total Pérdidas</p>
                </div>
                <div class="stat-card">
                    <h3>$<?php echo number_format($stats['valor_total'], 2); ?></h3>
                    <p>Valor Total</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $stats['mes_actual']; ?></h3>
                    <p>Este Mes</p>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID PÉRDIDA</th>
                            <th>EMPLEADO</th>
                            <th>PRODUCTO</th>
                            <th>CANTIDAD</th>
                            <th>VALOR TOTAL</th>
                            <th>RAZÓN</th>
                            <th>FECHA</th>
                            <th>ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody id="perdidasTable">
                        <?php
                        $query = "SELECT p.*, e.nombre as nombre_empleado, pr.nombre as nombre_producto 
                                  FROM Perdida p 
                                  LEFT JOIN Empleado e ON p.idEmpleado = e.idEmpleado 
                                  LEFT JOIN Producto pr ON p.idProducto = pr.idProducto 
                                  ORDER BY p.fecha DESC";
                        $result = $connection->query($query);
                        
                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td><strong>" . $row['idPerdida'] . "</strong></td>";
                                echo "<td>" . htmlspecialchars($row['nombre_empleado'] ?? 'N/A') . "</td>";
                                echo "<td>" . htmlspecialchars($row['nombre_producto'] ?? 'N/A') . "</td>";
                                echo "<td>" . $row['cantidad'] . "</td>";
                                echo "<td><strong>$" . number_format($row['total'], 2) . "</strong></td>";
                                echo "<td><span class='status-badge status-" . strtolower($row['razon']) . "'>" . $row['razon'] . "</span></td>";
                                echo "<td>" . date('d M Y', strtotime($row['fecha'])) . "</td>";
                                echo "<td>
                                        <form method='POST' style='display: inline;' onsubmit='return confirm(\"¿Estás seguro de eliminar esta pérdida?\")'>
                                            <input type='hidden' name='action' value='delete_perdida'>
                                            <input type='hidden' name='id_perdida' value='" . $row['idPerdida'] . "'>
                                            <button type='submit' class='btn btn-danger'>Eliminar</button>
                                        </form>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8' class='no-results'>
                                    <div style='text-align: center; padding: 40px;'>
                                        <div style='font-size: 4rem; margin-bottom: 20px;'>📊</div>
                                        <h3>No hay pérdidas registradas</h3>
                                        <p>Haz clic en 'Nueva Pérdida' para agregar la primera</p>
                                    </div>
                                  </td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<!-- Modal convertido en div -->
<div id="modal" class="modal">
    <div class="modal-content">
        <h2>Registrar Nueva Pérdida</h2>
        <form method="post" action="">
            <input type="hidden" name="action" value="add_perdida">

            <div class="form-group">
                <label for="empleado">Empleado:</label>
                <input type="text" id="empleado" name="empleado" placeholder="Ingrese ID del empleado" required>
            </div>

            <div class="form-group">
                <label for="producto">Producto:</label>
                <input type="text" id="producto" name="producto" placeholder="Ingrese ID del producto" required>
            </div>

            <div class="form-group">
                <label for="cantidad">Cantidad:</label>
                <input type="number" id="cantidad" name="cantidad" min="1" placeholder="Ingrese un número" required>
            </div>

            <!-- Quitamos el campo "total" porque se calcula automáticamente -->

            <div class="form-group">
                <label for="razon">Razón:</label>
                <textarea id="razon" name="razon" rows="3" placeholder="¿Por qué es una pérdida?" required></textarea>
            </div>

            <div class="form-group">
                <label for="fecha">Fecha:</label>
                <input type="date" id="fecha" name="fecha" required>
            </div>

            <div class="modal-buttons">
                <button type="submit" class="btn btn-primary">Guardar</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal() {
        document.getElementById('modal').classList.add('show');
    }

    function closeModal() {
        document.getElementById('modal').classList.remove('show');
    }

    // Cerrar modal si se hace click fuera del contenido
    document.getElementById('modal').addEventListener('click', function(event) {
        if(event.target === this) {
            closeModal();
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
    if (localStorage.getItem('perdidaRegistrada') === '1') {
        alert('¡Pérdida registrada correctamente!');
        localStorage.removeItem('perdidaRegistrada');
    }
});
</script>
</body>
</html>