<?php
include 'config\connection.php';

$mensaje = '';
$mensaje_tipo = 'success';
$condicion = '1'; // Valor por defecto para nuevo cliente

// Leer mensaje por GET (para mostrar después de redirección)
if (isset($_GET['msg'])) {
    $mensaje = $_GET['msg'];
    $mensaje_tipo = isset($_GET['type']) && $_GET['type'] === 'error' ? 'error' : 'success';
}

// Variables para el formulario de edición
$cliente_edit = null;

// Obtener el próximo ID AUTO_INCREMENT para mostrarlo en el formulario de registro
$next_id = '';
$res_next_id = $connection->query("SHOW TABLE STATUS LIKE 'Cliente'");
if ($res_next_id && $row_next_id = $res_next_id->fetch_assoc()) {
    $next_id = $row_next_id['Auto_increment'];
}


$accion = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $accion = $_POST['action'];
} elseif (isset($_GET['action'])) {
    $accion = $_GET['action'];
}


switch ($accion) {
    case 'add_cliente':
        $condicion = $_POST['condicion'];
        $sql_check = "SELECT COUNT(*) as total FROM Cliente WHERE RFC = ? OR email = ?";
        $stmt_check = $connection->prepare($sql_check);
        $stmt_check->bind_param("ss", $_POST['rfc'], $_POST['email']);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        $row_check = $result_check->fetch_assoc();

        if ($row_check['total'] > 0) {
            echo "<script>window.location.href='?page=customer&msg=Error: El RFC o el email ya están registrados&type=error';</script>";
            exit;
        } else {
            $sql = "INSERT INTO Cliente (nombre, apellidoPaterno, apellidoMaterno, RFC, telefono, email, calle, noCasa, colonia, CP, ciudad, estado, condicion)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param(
                "sssssssssssss",
                $_POST['nombre'],
                $_POST['apellidoPaterno'],
                $_POST['apellidoMaterno'],
                $_POST['rfc'],
                $_POST['telefono'],
                $_POST['email'],
                $_POST['calle'],
                $_POST['noCasa'],
                $_POST['colonia'],
                $_POST['cp'],
                $_POST['ciudad'],
                $_POST['estado'],
                $condicion
            );
            $stmt->execute();
            $new_id = $connection->insert_id;
            echo "<script>window.location.href='?page=customer&msg=Cliente registrado correctamente. ID: $new_id&type=success';</script>";
            exit;
        }
        break;

    case 'edit_cliente':
        // Validar que RFC y email no existan en otro cliente
        $sql_check = "SELECT COUNT(*) as total FROM Cliente WHERE (RFC = ? OR email = ?) AND idCliente != ?";
        $stmt_check = $connection->prepare($sql_check);
        $stmt_check->bind_param("ssi", $_POST['rfc'], $_POST['email'], $_POST['idCliente']);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        $row_check = $result_check->fetch_assoc();

        if ($row_check['total'] > 0) {
            echo "<script>window.location.href='?page=customer&msg=Error: El RFC o el email ya están registrados en otro cliente&type=error';</script>";
            exit;
        }

        $sql = "UPDATE Cliente SET nombre=?, apellidoPaterno=?, apellidoMaterno=?, RFC=?, telefono=?, email=?, calle=?, noCasa=?, colonia=?, CP=?, ciudad=?, estado=?, condicion=?
                WHERE idCliente=?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param(
            "sssssssssssssi",
            $_POST['nombre'],
            $_POST['apellidoPaterno'],
            $_POST['apellidoMaterno'],
            $_POST['rfc'],
            $_POST['telefono'],
            $_POST['email'],
            $_POST['calle'],
            $_POST['noCasa'],
            $_POST['colonia'],
            $_POST['cp'],
            $_POST['ciudad'],
            $_POST['estado'],
            $_POST['condicion'],
            $_POST['idCliente']
        );
        $stmt->execute();
        echo "<script>window.location.href='?page=customer&msg=Cliente actualizado correctamente. ID: {$_POST['idCliente']}&type=success';</script>";
        exit;
        break;

    case 'deactivate':
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $sql = "UPDATE Cliente SET condicion='0' WHERE idCliente=?";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            echo "<script>window.location.href='?page=customer&msg=Cliente desactivado correctamente&type=success';</script>";
            exit;
        }
        break;

    case 'activate':
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $sql = "UPDATE Cliente SET condicion='1' WHERE idCliente=?";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            echo "<script>window.location.href='?page=customer&msg=Cliente activado correctamente&type=success';</script>";
            exit;
        }
        break;

    case 'edit':
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $sql = "SELECT * FROM Cliente WHERE idCliente=?";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $cliente_edit = $result->fetch_assoc();
        }
        break;

    default:

        break;
}

// Obtener clientes activos
$result = $connection->query("SELECT * FROM Cliente WHERE condicion='1'");
$clientes = $result->fetch_all(MYSQLI_ASSOC);

// Obtener clientes desactivados
$result_inactivos = $connection->query("SELECT * FROM Cliente WHERE condicion='0'");
$clientes_inactivos = $result_inactivos->fetch_all(MYSQLI_ASSOC);
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestión de Clientes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-gray-100 p-6">
    <?php if ($mensaje): ?>
        <script>
            Swal.fire({
                icon: "<?php echo ($mensaje_tipo === 'error') ? 'error' : 'success'; ?>",
                title: "<?php echo addslashes($mensaje); ?>",
                confirmButtonText: 'Aceptar'
            });
            // Limpiar la URL para que no se repita el mensaje al recargar
            if (window.location.search.includes('msg=')) {
                const url = new URL(window.location.href);
                url.searchParams.delete('msg');
                url.searchParams.delete('type');
                window.history.replaceState({}, document.title, url.pathname + url.search);
            }
        </script>
    <?php endif; ?>
    <div class="container mx-auto">
        <h1 class="text-2xl font-bold mb-4">Gestión de Clientes</h1>
        <!-- Botón para mostrar formulario agregar cliente -->
        <?php if (!$cliente_edit): ?>
            <button onclick="document.getElementById('add-client-form').classList.toggle('hidden')"
                class="mb-4 bg-blue-500 text-white px-4 py-2 rounded">Agregar Cliente</button>
        <?php endif; ?>

        <!-- Formulario para agregar cliente -->
        <div id="add-client-form" class="<?php echo $cliente_edit ? 'hidden' : ''; ?> mb-6 bg-white p-4 rounded shadow">
            <form id="form-add-cliente" method="POST" autocomplete="off">
                <input type="hidden" name="action" value="add_cliente">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block">ID:</label>
                        <input type="text" class="w-full border px-2 py-1 bg-gray-100"
                            value="<?php echo htmlspecialchars($next_id); ?>" disabled>
                    </div>
                    <div><label class="block">Nombre:</label><input type="text" name="nombre"
                            class="w-full border px-2 py-1" required></div>
                    <div><label class="block">Apellido Paterno:</label><input type="text" name="apellidoPaterno"
                            class="w-full border px-2 py-1" required></div>
                    <div><label class="block">Apellido Materno:</label><input type="text" name="apellidoMaterno"
                            class="w-full border px-2 py-1" required></div>
                    <div><label class="block">RFC:</label><input type="text" name="rfc" maxlength="13"
                            class="w-full border px-2 py-1">
                    </div>
                    <div><label class="block">Teléfono:</label><input type="text" name="telefono" maxlength="10"
                            class="w-full border px-2 py-1"></div>
                    <div><label class="block">Email:</label><input type="email" name="email"
                            class="w-full border px-2 py-1"></div>
                    <div><label class="block">Calle:</label><input type="text" name="calle"
                            class="w-full border px-2 py-1"></div>
                    <div><label class="block">No. Casa:</label><input type="text" name="noCasa"
                            class="w-full border px-2 py-1"></div>
                    <div><label class="block">Colonia:</label><input type="text" name="colonia"
                            class="w-full border px-2 py-1"></div>
                    <div><label class="block">CP:</label><input type="text" name="cp" maxlength="5"
                            class="w-full border px-2 py-1">
                    </div>
                    <div><label class="block">Ciudad:</label><input type="text" name="ciudad"
                            class="w-full border px-2 py-1"></div>
                    <div><label class="block">Estado:</label><input type="text" name="estado"
                            class="w-full border px-2 py-1"></div>
                    <div>
                        <label class="block">Condición:</label>
                        <select name="condicion" id="condicion" class="border border-gray-300 rounded px-2 py-1">
                            <option value="1" <?php echo ($condicion === '1') ? 'selected' : ''; ?>>Activo</option>
                            <option value="0" <?php echo ($condicion === '0') ? 'selected' : ''; ?>>Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Guardar</button>
                </div>
            </form>
        </div>

        <!-- Formulario para editar cliente -->
        <?php if ($cliente_edit): ?>
            <div class="mb-6 bg-white p-4 rounded shadow">
                <form method="POST" autocomplete="off">
                    <input type="hidden" name="action" value="edit_cliente">
                    <input type="hidden" name="idCliente" value="<?php echo $cliente_edit['idCliente']; ?>">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block">ID:</label>
                            <input type="text" class="w-full border px-2 py-1 bg-gray-100"
                                value="<?php echo htmlspecialchars($cliente_edit['idCliente']); ?>" disabled>
                        </div>
                        <div><label class="block">Nombre:</label><input type="text" name="nombre"
                                class="w-full border px-2 py-1"
                                value="<?php echo htmlspecialchars($cliente_edit['nombre']); ?>" required></div>
                        <div><label class="block">Apellido Paterno:</label><input type="text" name="apellidoPaterno"
                                class="w-full border px-2 py-1"
                                value="<?php echo htmlspecialchars($cliente_edit['apellidoPaterno']); ?>" required></div>
                        <div><label class="block">Apellido Materno:</label><input type="text" name="apellidoMaterno"
                                class="w-full border px-2 py-1"
                                value="<?php echo htmlspecialchars($cliente_edit['apellidoMaterno']); ?>" required></div>
                        <div><label class="block">RFC:</label><input type="text" name="rfc" maxlength="13"
                                class="w-full border px-2 py-1"
                                value="<?php echo htmlspecialchars($cliente_edit['RFC']); ?>"></div>
                        <div><label class="block">Teléfono:</label><input type="text" name="telefono" maxlength="10"
                                class="w-full border px-2 py-1"
                                value="<?php echo htmlspecialchars($cliente_edit['telefono']); ?>"></div>
                        <div><label class="block">Email:</label><input type="email" name="email"
                                class="w-full border px-2 py-1"
                                value="<?php echo htmlspecialchars($cliente_edit['email']); ?>"></div>
                        <div><label class="block">Calle:</label><input type="text" name="calle"
                                class="w-full border px-2 py-1"
                                value="<?php echo htmlspecialchars($cliente_edit['calle']); ?>"></div>
                        <div><label class="block">No. Casa:</label><input type="text" name="noCasa"
                                class="w-full border px-2 py-1"
                                value="<?php echo htmlspecialchars($cliente_edit['noCasa']); ?>"></div>
                        <div><label class="block">Colonia:</label><input type="text" name="colonia"
                                class="w-full border px-2 py-1"
                                value="<?php echo htmlspecialchars($cliente_edit['colonia']); ?>"></div>
                        <div><label class="block">CP:</label><input type="text" name="cp" maxlength="5"
                                class="w-full border px-2 py-1"
                                value="<?php echo htmlspecialchars($cliente_edit['CP']); ?>"></div>
                        <div><label class="block">Ciudad:</label><input type="text" name="ciudad"
                                class="w-full border px-2 py-1"
                                value="<?php echo htmlspecialchars($cliente_edit['ciudad']); ?>"></div>
                        <div><label class="block">Estado:</label><input type="text" name="estado"
                                class="w-full border px-2 py-1"
                                value="<?php echo htmlspecialchars($cliente_edit['estado']); ?>"></div>
                        <div>
                            <label class="block">Condición:</label>
                            <select name="condicion" id="condicion" class="border border-gray-300 rounded px-2 py-1">
                                <option value="1" <?php echo ($condicion === '1') ? 'selected' : ''; ?>>Activo</option>
                                <option value="0" <?php echo ($condicion === '0') ? 'selected' : ''; ?>>Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center">
                        <button type="submit"
                            class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded">Actualizar</button>
                        <a href="?page=customer"
                            class="ml-4 px-4 py-2 rounded bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold transition-colors duration-200">Cancelar</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <!-- Tabla de clientes activos -->
        <h2 class="text-xl font-semibold mt-8 mb-2">Clientes activos</h2>
        <table class="table-auto w-full bg-white rounded shadow overflow-hidden">
            <thead>
                <tr class="bg-gray-200 text-left">
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">Nombre</th>
                    <th class="px-4 py-2">Apellido Paterno</th>
                    <th class="px-4 py-2">Apellido Materno</th>
                    <th class="px-4 py-2">RFC</th>
                    <th class="px-4 py-2">Teléfono</th>
                    <th class="px-4 py-2">Email</th>
                    <th class="px-4 py-2">Dirección</th>
                    <th class="px-4 py-2">Ciudad</th>
                    <th class="px-4 py-2">Estado</th>
                    <th class="px-4 py-2">Condición</th>
                    <th class="px-4 py-2">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clientes as $cliente): ?>
                    <tr>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($cliente['idCliente']); ?></td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($cliente['nombre']); ?></td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($cliente['apellidoPaterno']); ?></td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($cliente['apellidoMaterno']); ?></td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($cliente['RFC']); ?></td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($cliente['telefono']); ?></td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($cliente['email']); ?></td>
                        <td class="border px-4 py-2">
                            <?php
                            echo htmlspecialchars($cliente['calle']) . ' No. ' . htmlspecialchars($cliente['noCasa']) . ', ' . htmlspecialchars($cliente['colonia']) . ', CP ' . htmlspecialchars($cliente['CP']);
                            ?>
                        </td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($cliente['ciudad']); ?></td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($cliente['estado']); ?></td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($cliente['condicion']); ?></td>
                        <td class="border px-4 py-2">
                            <a href="?page=customer&action=edit&id=<?php echo $cliente['idCliente']; ?>"
                                class="inline-block bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded mr-2">Editar</a>
                            <a href="?page=customer&action=deactivate&id=<?php echo $cliente['idCliente']; ?>"
                                class="inline-block bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded"
                                onclick="return confirm('¿Seguro que deseas desactivar este cliente?');">Desactivar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (count($clientes) === 0): ?>
                    <tr>
                        <td colspan="12" class="text-center py-4">No hay clientes activos.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Tabla de clientes desactivados -->
        <!-- Botón para abrir el modal -->
        <!-- Botón para abrir el modal -->
<button onclick="document.getElementById('modal-inactivos').classList.remove('hidden')"
    class="mt-8 mb-2 bg-gray-500 hover:bg-gray-700 text-white px-4 py-2 rounded">
    Ver clientes desactivados
</button>

<!-- Modal de clientes desactivados -->
<div id="modal-inactivos"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden"
    onclick="this.classList.add('hidden')">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl max-h-[90vh] p-6 relative overflow-y-auto"
         onclick="event.stopPropagation()">
        <!-- Botón de cerrar -->
        <button onclick="document.getElementById('modal-inactivos').classList.add('hidden')"
            class="absolute top-2 right-2 text-gray-500 hover:text-red-600 text-2xl font-bold">&times;</button>
        <h2 class="text-xl font-semibold mb-4">Clientes desactivados</h2>
        <div class="overflow-x-auto">
            <table class="table-auto w-full bg-white rounded shadow overflow-hidden">
                <thead>
                    <tr class="bg-gray-200 text-left">
                        <th class="px-4 py-2">ID</th>
                        <th class="px-4 py-2">Nombre</th>
                        <th class="px-4 py-2">Apellido Paterno</th>
                        <th class="px-4 py-2">Apellido Materno</th>
                        <th class="px-4 py-2">RFC</th>
                        <th class="px-4 py-2">Teléfono</th>
                        <th class="px-4 py-2">Email</th>
                        <th class="px-4 py-2">Dirección</th>
                        <th class="px-4 py-2">Ciudad</th>
                        <th class="px-4 py-2">Estado</th>
                        <th class="px-4 py-2">Condición</th>
                        <th class="px-4 py-2">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clientes_inactivos as $cliente): ?>
                        <tr>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($cliente['idCliente']); ?></td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($cliente['nombre']); ?></td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($cliente['apellidoPaterno']); ?></td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($cliente['apellidoMaterno']); ?></td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($cliente['RFC']); ?></td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($cliente['telefono']); ?></td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($cliente['email']); ?></td>
                            <td class="border px-4 py-2">
                                <?php
                                echo htmlspecialchars($cliente['calle']) . ' No. ' . htmlspecialchars($cliente['noCasa']) . ', ' . htmlspecialchars($cliente['colonia']) . ', CP ' . htmlspecialchars($cliente['CP']);
                                ?>
                            </td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($cliente['ciudad']); ?></td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($cliente['estado']); ?></td>
                            <td class="border px-4 py-2"><?php echo htmlspecialchars($cliente['condicion']); ?></td>
                            <td class="border px-4 py-2">
                                <a href="?page=customer&action=activate&id=<?php echo $cliente['idCliente']; ?>"
                                    class="inline-block bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded"
                                    onclick="return confirm('¿Seguro que deseas activar este cliente?');">Activar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (count($clientes_inactivos) === 0): ?>
                        <tr>
                            <td colspan="12" class="text-center py-4">No hay clientes desactivados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>

</html>