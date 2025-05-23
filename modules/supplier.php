<?php
include 'config\connection.php';

$mensaje = '';
$mensaje_tipo = 'success';
if (isset($_GET['msg'])) {
    $mensaje = $_GET['msg'];
    $mensaje_tipo = isset($_GET['type']) && $_GET['type'] === 'error' ? 'error' : 'success';
}
// Variables para el formulario de edición
$proveedor_edit = null;

// Obtener el próximo ID AUTO_INCREMENT para mostrarlo en el formulario de registro
$next_id = '';
$res_next_id = $connection->query("SHOW TABLE STATUS LIKE 'Proveedor'");
if ($res_next_id && $row_next_id = $res_next_id->fetch_assoc()) {
    $next_id = $row_next_id['Auto_increment'];
}

// Determinar acción
$accion = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $accion = $_POST['action'];
} elseif (isset($_GET['action'])) {
    $accion = $_GET['action'];
}

// Switch para manejar acciones
switch ($accion) {
    case 'add_proveedor':
        $sql_check = "SELECT COUNT(*) as total FROM Proveedor WHERE RFC = ? OR email = ?";
        $stmt_check = $connection->prepare($sql_check);
        $stmt_check->bind_param("ss", $_POST['rfc'], $_POST['email']);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        $row_check = $result_check->fetch_assoc();

        if ($row_check['total'] > 0) {
            echo "<script>window.location.href='?page=supplier&msg=Error: El RFC o el email ya están registrados&type=error';</script>";
            exit;
        } else {
            $sql = "INSERT INTO Proveedor (nombre, apellidoPaterno, apellidoMaterno, RFC, telefono, email, calle, noCasa, colonia, CP, ciudad, estado)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param(
                "ssssssssssss",
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
                $_POST['estado']
            );
            $stmt->execute();
            $new_id = $connection->insert_id;
            echo "<script>window.location.href='?page=supplier&msg=Proveedor registrado correctamente. ID: $new_id&type=success';</script>";
            exit;
        }
        break;

    case 'edit_proveedor':
        // Validar que RFC y email no existan en otro proveedor
        $sql_check = "SELECT COUNT(*) as total FROM Proveedor WHERE (RFC = ? OR email = ?) AND idProveedor != ?";
        $stmt_check = $connection->prepare($sql_check);
        $stmt_check->bind_param("ssi", $_POST['rfc'], $_POST['email'], $_POST['idProveedor']);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        $row_check = $result_check->fetch_assoc();

        if ($row_check['total'] > 0) {
            echo "<script>window.location.href='?page=supplier&msg=Error: El RFC o el email ya están registrados en otro proveedor&type=error';</script>";
            exit;
        }

        $sql = "UPDATE Proveedor SET nombre=?, apellidoPaterno=?, apellidoMaterno=?, RFC=?, telefono=?, email=?, calle=?, noCasa=?, colonia=?, CP=?, ciudad=?, estado=?
                WHERE idProveedor=?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param(
            "ssssssssssssi",
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
            $_POST['idProveedor']
        );
        $stmt->execute();
        echo "<script>window.location.href='?page=supplier&msg=Proveedor actualizado correctamente. ID: {$_POST['idProveedor']}&type=success';</script>";
        exit;
        break;

    /*case 'delete':
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $sql = "DELETE FROM Proveedor WHERE idProveedor=?";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            echo "<script>window.location.href='?page=supplier&msg=Proveedor eliminado correctamente&type=success';</script>";
            exit;
        }
        break;*/

    case 'edit':
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $sql = "SELECT * FROM Proveedor WHERE idProveedor=?";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $proveedor_edit = $result->fetch_assoc();
        }
        break;

    default:
        // No hacer nada
        break;
}

// Obtener todos los proveedores
$result = $connection->query("SELECT * FROM Proveedor");
$proveedores = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestión de Proveedores</title>
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
        <h1 class="text-2xl font-bold mb-4">Gestión de Proveedores</h1>
        <!-- Botón para mostrar formulario agregar proveedor -->
        <?php if (!$proveedor_edit): ?>
            <button onclick="document.getElementById('add-supplier-form').classList.toggle('hidden')"
                class="mb-4 bg-blue-500 text-white px-4 py-2 rounded">Agregar Proveedor</button>
        <?php endif; ?>

        <!-- Formulario para agregar proveedor -->
        <div id="add-supplier-form" class="<?php echo $proveedor_edit ? 'hidden' : ''; ?> mb-6 bg-white p-4 rounded shadow">
            <form id="form-add-proveedor" method="POST" autocomplete="off">
                <input type="hidden" name="action" value="add_proveedor">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block">ID:</label>
                        <input type="text" class="w-full border px-2 py-1 bg-gray-100" value="<?php echo htmlspecialchars($next_id); ?>" disabled>
                    </div>
                    <div><label class="block">Nombre:</label><input type="text" name="nombre"
                            class="w-full border px-2 py-1" required></div>
                    <div><label class="block">Apellido Paterno:</label><input type="text" name="apellidoPaterno"
                            class="w-full border px-2 py-1" required></div>
                    <div><label class="block">Apellido Materno:</label><input type="text" name="apellidoMaterno"
                            class="w-full border px-2 py-1" required></div>
                    <div><label class="block">RFC:</label><input type="text" name="rfc" maxlength="13" class="w-full border px-2 py-1">
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
                    <div><label class="block">CP:</label><input type="text" name="cp" maxlength="5" class="w-full border px-2 py-1">
                    </div>
                    <div><label class="block">Ciudad:</label><input type="text" name="ciudad"
                            class="w-full border px-2 py-1"></div>
                    <div><label class="block">Estado:</label><input type="text" name="estado"
                            class="w-full border px-2 py-1"></div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Guardar</button>
                </div>
            </form>
        </div>

        <!-- Formulario para editar proveedor -->
        <?php if ($proveedor_edit): ?>
            <div class="mb-6 bg-white p-4 rounded shadow">
                <form method="POST" autocomplete="off">
                    <input type="hidden" name="action" value="edit_proveedor">
                    <input type="hidden" name="idProveedor" value="<?php echo $proveedor_edit['idProveedor']; ?>">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block">ID:</label>
                            <input type="text" class="w-full border px-2 py-1 bg-gray-100"
                                value="<?php echo htmlspecialchars($proveedor_edit['idProveedor']); ?>" disabled>
                        </div>
                        <div><label class="block">Nombre:</label><input type="text" name="nombre"
                                class="w-full border px-2 py-1"
                                value="<?php echo htmlspecialchars($proveedor_edit['nombre']); ?>" required></div>
                        <div><label class="block">Apellido Paterno:</label><input type="text" name="apellidoPaterno"
                                class="w-full border px-2 py-1"
                                value="<?php echo htmlspecialchars($proveedor_edit['apellidoPaterno']); ?>" required></div>
                        <div><label class="block">Apellido Materno:</label><input type="text" name="apellidoMaterno"
                                class="w-full border px-2 py-1"
                                value="<?php echo htmlspecialchars($proveedor_edit['apellidoMaterno']); ?>" required></div>
                        <div><label class="block">RFC:</label><input type="text" name="rfc" maxlength="13" class="w-full border px-2 py-1"
                                value="<?php echo htmlspecialchars($proveedor_edit['RFC']); ?>"></div>
                        <div><label class="block">Teléfono:</label><input type="text" name="telefono" maxlength="10"
                                class="w-full border px-2 py-1"
                                value="<?php echo htmlspecialchars($proveedor_edit['telefono']); ?>"></div>
                        <div><label class="block">Email:</label><input type="email" name="email"
                                class="w-full border px-2 py-1"
                                value="<?php echo htmlspecialchars($proveedor_edit['email']); ?>"></div>
                        <div><label class="block">Calle:</label><input type="text" name="calle"
                                class="w-full border px-2 py-1"
                                value="<?php echo htmlspecialchars($proveedor_edit['calle']); ?>"></div>
                        <div><label class="block">No. Casa:</label><input type="text" name="noCasa"
                                class="w-full border px-2 py-1"
                                value="<?php echo htmlspecialchars($proveedor_edit['noCasa']); ?>"></div>
                        <div><label class="block">Colonia:</label><input type="text" name="colonia"
                                class="w-full border px-2 py-1"
                                value="<?php echo htmlspecialchars($proveedor_edit['colonia']); ?>"></div>
                        <div><label class="block">CP:</label><input type="text" name="cp" maxlength="5" class="w-full border px-2 py-1"
                                value="<?php echo htmlspecialchars($proveedor_edit['CP']); ?>"></div>
                        <div><label class="block">Ciudad:</label><input type="text" name="ciudad"
                                class="w-full border px-2 py-1"
                                value="<?php echo htmlspecialchars($proveedor_edit['ciudad']); ?>"></div>
                        <div><label class="block">Estado:</label><input type="text" name="estado"
                                class="w-full border px-2 py-1"
                                value="<?php echo htmlspecialchars($proveedor_edit['estado']); ?>"></div>
                    </div>
                    <div class="mt-4 flex items-center">
                        <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded">Actualizar</button>
                        <a href="?page=supplier" class="ml-4 px-4 py-2 rounded bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold transition-colors duration-200">Cancelar</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <!-- Tabla de proveedores -->
        <h2 class="text-xl font-semibold mt-8 mb-2">Proveedores</h2>
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
                    <th class="px-4 py-2">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($proveedores as $proveedor): ?>
                    <tr>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($proveedor['idProveedor']); ?></td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($proveedor['nombre']); ?></td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($proveedor['apellidoPaterno']); ?></td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($proveedor['apellidoMaterno']); ?></td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($proveedor['RFC']); ?></td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($proveedor['telefono']); ?></td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($proveedor['email']); ?></td>
                        <td class="border px-4 py-2">
                            <?php
                            echo htmlspecialchars($proveedor['calle']) . ' No. ' . htmlspecialchars($proveedor['noCasa']) . ', ' . htmlspecialchars($proveedor['colonia']) . ', CP ' . htmlspecialchars($proveedor['CP']);
                            ?>
                        </td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($proveedor['ciudad']); ?></td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($proveedor['estado']); ?></td>
                        <td class="border px-4 py-2">
                            <a href="?page=supplier&action=edit&id=<?php echo $proveedor['idProveedor']; ?>"
                                class="inline-block bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded mr-2">Editar</a>
                            
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (count($proveedores) === 0): ?>
                    <tr>
                        <td colspan="11" class="text-center py-4">No hay proveedores registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>

</html>