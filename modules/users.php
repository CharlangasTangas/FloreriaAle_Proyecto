<?php
require_once '<config\connection.php';
// --- Obtener empleados de la base de datos ---
$users = [];
$resultado = $connection->query("SELECT * FROM Empleado");
while ($fila = $resultado->fetch_assoc()) {
    $users[] = $fila;
}

// --- Procesar formularios POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'add_user') {
        $nombre = $_POST['nombre'] ?? '';
        $apellidoPaterno = $_POST['apellidoPaterno'] ?? '';
        $apellidoMaterno = $_POST['apellidoMaterno'] ?? '';
        $usuario = $_POST['usuario'] ?? '';
        $clave = $_POST['clave'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        $RFC = $_POST['RFC'] ?? '';
        $calle = $_POST['calle'] ?? '';
        $noCasa = $_POST['noCasa'] ?? '';
        $colonia = $_POST['colonia'] ?? '';
        $cp = $_POST['cp'] ?? '';
        $ciudad = $_POST['ciudad'] ?? '';
        $estado = $_POST['estado'] ?? '';
        $fechaNacimiento = $_POST['fechaNacimiento'] ?? '';
        $rol = $_POST['rol'] ?? '';
        $turno = $_POST['turno'] ?? '';
        $sueldo = $_POST['sueldo'] ?? 0;
        $estatus = isset($_POST['estatus']) ? (int)$_POST['estatus'] : 1;

        $checkRFC = $connection->prepare("SELECT idEmpleado FROM Empleado WHERE RFC = ?");
        $checkRFC->bind_param("s", $RFC);
        $checkRFC->execute();
        $checkRFC->store_result();

        if ($checkRFC->num_rows > 0) {
            $mensaje = "Ya existe un empleado con ese RFC";
        } else {
            $stmt = $connection->prepare("INSERT INTO Empleado (nombre, apellidoPaterno, apellidoMaterno, usuario, clave, telefono, RFC, calle, noCasa, colonia, cp, ciudad, estado, fechaNacimiento, rol, turno, sueldo, estatus) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssssssssssssdi", $nombre, $apellidoPaterno, $apellidoMaterno, $usuario, $clave, $telefono, $RFC, $calle, $noCasa, $colonia, $cp, $ciudad, $estado, $fechaNacimiento, $rol, $turno, $sueldo, $estatus);

            if ($stmt->execute()) {
                $mensaje = "Empleado agregado correctamente";
            } else {
                $mensaje = "Error al agregar empleado: " . $stmt->error;
            }
        }

        echo "<script>alert('" . addslashes($mensaje) . "'); window.location.href = '" . $_SERVER['PHP_SELF'] . "';</script>";
        exit;
    }

    elseif ($action === 'edit_user' && isset($_POST['idEmpleado'])) {
        $idEmpleado = (int) $_POST['idEmpleado'];
        $nombre = $_POST['nombre'] ?? '';
        $apellidoPaterno = $_POST['apellidoPaterno'] ?? '';
        $apellidoMaterno = $_POST['apellidoMaterno'] ?? '';
        $usuario = $_POST['usuario'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        $RFC = $_POST['RFC'] ?? '';
        $calle = $_POST['calle'] ?? '';
        $noCasa = $_POST['noCasa'] ?? '';
        $colonia = $_POST['colonia'] ?? '';
        $CP = $_POST['CP'] ?? '';
        $ciudad = $_POST['ciudad'] ?? '';
        $estado = $_POST['estado'] ?? '';
        $fechaNacimiento = $_POST['fechaNacimiento'] ?? '';
        $rol = $_POST['rol'] ?? '';
        $turno = $_POST['turno'] ?? '';
        $sueldo = $_POST['sueldo'] ?? 0;
        $estatus = isset($_POST['estatus']) ? (int)$_POST['estatus'] : 1;


        $checkRFC = $connection->prepare("SELECT idEmpleado FROM Empleado WHERE RFC = ? AND idEmpleado != ?");
        $checkRFC->bind_param("si", $RFC, $idEmpleado);
        $checkRFC->execute();
        $checkRFC->store_result();

        if ($checkRFC->num_rows > 0) {
            $mensaje = "Ya existe otro empleado con ese RFC";
        } else {
            $stmt = $connection->prepare("UPDATE Empleado SET nombre=?, apellidoPaterno=?, apellidoMaterno=?, usuario=?, telefono=?, RFC=?, calle=?, noCasa=?, colonia=?, CP=?, ciudad=?, estado=?, fechaNacimiento=?, rol=?, turno=?, sueldo=?, estatus=? WHERE idEmpleado=?");
            $stmt->bind_param("ssssssssssssssssii", $nombre, $apellidoPaterno, $apellidoMaterno, $usuario, $telefono, $RFC, $calle, $noCasa, $colonia, $CP, $ciudad, $estado, $fechaNacimiento, $rol, $turno, $sueldo, $estatus,$idEmpleado);

            if ($stmt->execute()) {
                $mensaje = "Empleado actualizado correctamente";
            } else {
                $mensaje = "Error al editar empleado: " . $stmt->error;
            }
        }

        echo "<script>alert('" . addslashes($mensaje) . "'); window.location.href = '" . $_SERVER['PHP_SELF'] . "';</script>";
        exit;
    }

    elseif ($action === 'delete_user' && isset($_POST['user_id'])) {
        $id = (int) $_POST['user_id'];
        $stmt = $connection->prepare("UPDATE Empleado SET estatus = 0 WHERE idEmpleado = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $mensaje = "Empleado eliminado correctamente";
        echo "<script>alert('" . addslashes($mensaje) . "'); window.location.href = '" . $_SERVER['PHP_SELF'] . "';</script>";
        exit;
    }
}

?>
<<<<<<< HEAD
<<<<<<< Updated upstream

<div class="flex flex-col gap-6">
    <div>
        <h2 class="text-2xl font-bold tracking-tight">User Management</h2>
        <p class="text-gray-500">Create, view, update, and delete system users.</p>
    </div>
=======
<!DOCTYPE html>
<html lang="es">
=======
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <title>Gestión de Clientes</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- FontAwesome para iconos (asegúrate de incluirlo) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
</head>
<<<<<<< HEAD
>>>>>>> Stashed changes
=======
>>>>>>> compras

<body class="bg-gray-100 p-6">

    <?php if (!empty($mensaje)): ?>
        <script>
            Swal.fire({
                icon: "<?php echo ($mensaje_tipo === 'error') ? 'error' : 'success'; ?>",
                title: "<?php echo addslashes($mensaje); ?>",
                confirmButtonText: 'Aceptar'
            });
        </script>
    <?php endif; ?>

    <div class="flex flex-col gap-6">
        <div>
            <h2 class="text-2xl font-bold tracking-tight">Floreria Ale</h2>
            <p class="text-gray-500">Lista de empleados.</p>
        </div>

        <div class="rounded-lg border bg-white shadow">
            <div class="flex flex-row items-center justify-between p-4 pb-2">
                <div>
                    <h3 class="font-medium">Empleados</h3>
                    <p class="text-sm text-gray-500">Bienvenidos.</p>
                </div>
                <button type="button" class="inline-flex items-center rounded-md bg-blue-500 px-3 py-2 text-sm font-medium text-white hover:bg-blue-600" onclick="document.getElementById('add-user-modal').classList.remove('hidden')">
                    <i class="fas fa-plus-circle mr-2"></i>
                    Agregar empleado
                </button>
            </div>
            <div class="p-4">
                <div class="mb-4 flex items-center gap-2 max-w-sm">
                    <i class="fas fa-search text-gray-500"></i>
                    <input type="text" id="user-search" placeholder="Search Empleados..." class="w-full rounded-md border border-gray-300 px-3 py-2 focus:border-blue-500 focus:outline-none" />
                </div>
                <div class="rounded-md border">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b bg-gray-50">
                                <th class="p-3 text-left font-medium">Nombre</th>
                                <th class="p-3 text-left font-medium">Usuario</th>
                                <th class="p-3 text-left font-medium">Rol</th>
                                <th class="p-3 text-left font-medium">Estatus</th>
                                <th class="p-3 text-right font-medium">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr class="border-b">
                                <td class="p-3 font-medium"><?php echo htmlspecialchars($user['nombre']); ?></td>
                                <td class="p-3"><?php echo htmlspecialchars($user['usuario']); ?></td>
                                <td class="p-3"><?php echo htmlspecialchars($user['rol']); ?></td>
                                <td class="p-3">
                                    <span class="rounded-full px-2 py-1 text-xs font-medium <?php echo ($user['estatus'] == 1) ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                        <?php echo ($user['estatus'] == 1) ? 'Activo' : 'Inactivo'; ?>
                                    </span>
                                </td>
                                <td class="p-3 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button
                                            type="button"
                                            class="rounded-md p-1 text-gray-500 hover:bg-gray-100"
                                            onclick="openEditModal(this)"
                                            data-id="<?php echo $user['idEmpleado']; ?>"
                                            data-nombre="<?php echo htmlspecialchars($user['nombre'] ?? ''); ?>"
                                            data-apellido-paterno="<?php echo htmlspecialchars($user['apellidoPaterno'] ?? ''); ?>"
                                            data-apellido-materno="<?php echo htmlspecialchars($user['apellidoMaterno'] ?? ''); ?>"
                                            data-usuario="<?php echo htmlspecialchars($user['usuario'] ?? ''); ?>"
                                            data-rfc="<?php echo htmlspecialchars($user['RFC'] ?? ''); ?>"
                                            data-telefono="<?php echo htmlspecialchars($user['telefono'] ?? ''); ?>"
                                            data-calle="<?php echo htmlspecialchars($user['calle'] ?? ''); ?>"
                                            data-nocasa="<?php echo htmlspecialchars($user['noCasa'] ?? ''); ?>"
                                            data-colonia="<?php echo htmlspecialchars($user['colonia'] ?? ''); ?>"
                                            data-CP="<?php echo htmlspecialchars($user['CP'] ?? ''); ?>"
                                            data-ciudad="<?php echo htmlspecialchars($user['ciudad'] ?? ''); ?>"
                                            data-estado="<?php echo htmlspecialchars($user['estado'] ?? ''); ?>"
                                            data-fecha-nacimiento="<?php echo htmlspecialchars($user['fechaNacimiento'] ?? ''); ?>"
                                            data-rol="<?php echo htmlspecialchars($user['rol'] ?? ''); ?>"
                                            data-turno="<?php echo htmlspecialchars($user['turno'] ?? ''); ?>"
                                            data-sueldo="<?php echo htmlspecialchars($user['sueldo'] ?? ''); ?>"
                                        >
                                            <i class="fas fa-user-cog"></i>
                                            <span class="sr-only">Editar</span>
                                        </button>

                                        <form method="POST" class="inline" onsubmit="return confirm('¿Seguro que quieres eliminar este empleado?');">
                                            <input type="hidden" name="action" value="delete_user" />
                                            <input type="hidden" name="user_id" value="<?php echo (int)$user['idEmpleado']; ?>" />
                                            <button type="submit" class="rounded-md p-1 text-gray-500 hover:bg-gray-100">
                                                <i class="fas fa-trash"></i>
                                                <span class="sr-only">Eliminar</span>
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
<div id="add-user-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-purple-100 bg-opacity-60 hidden">
    <div class="w-full max-w-lg h-[90vh] rounded-2xl bg-purple-100 p-6 mx-4 my-6 shadow-lg overflow-hidden fade-in-up">

        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-xl font-semibold text-purple-950">Añadir empleado</h3>
            <button type="button" class="text-purple-500 hover:text-purple-700 transition" onclick="document.getElementById('add-user-modal').classList.add('hidden')">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        <div class="px-6">
            <form method="POST" action="" class="grid grid-cols-2 gap-4 overflow-y-auto pr-2" style="max-height: 70vh;">
                <input type="hidden" name="action" value="add_user">

                <div><label class="block text-sm text-purple-800">Nombre:</label><input type="text" name="nombre" class="w-full border border-purple-200 rounded px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-purple-300" required></div>
                <div><label class="block text-sm text-purple-800">Apellido Paterno:</label><input type="text" name="apellidoPaterno" class="w-full border border-purple-200 rounded px-3 py-2 bg-white" required></div>
                <div><label class="block text-sm text-purple-800">Apellido Materno:</label><input type="text" name="apellidoMaterno" class="w-full border border-purple-200 rounded px-3 py-2 bg-white" required></div>
                <div><label class="block text-sm text-purple-800">RFC:</label><input type="text" name="RFC" maxlength="13" class="w-full border border-purple-200 rounded px-3 py-2 bg-white"></div>
                <div><label class="block text-sm text-purple-800">Teléfono:</label><input type="text" name="telefono" maxlength="10" class="w-full border border-purple-200 rounded px-3 py-2 bg-white"></div>
                <div><label class="block text-sm text-purple-800">Salario:</label><input type="number" name="sueldo" class="w-full border border-purple-200 rounded px-3 py-2 bg-white"></div>
                <div><label class="block text-sm text-purple-800">Calle:</label><input type="text" name="calle" class="w-full border border-purple-200 rounded px-3 py-2 bg-white"></div>
                <div><label class="block text-sm text-purple-800">No. Casa:</label><input type="text" name="noCasa" class="w-full border border-purple-200 rounded px-3 py-2 bg-white"></div>
                <div><label class="block text-sm text-purple-800">Colonia:</label><input type="text" name="colonia" class="w-full border border-purple-200 rounded px-3 py-2 bg-white"></div>
                <div><label class="block text-sm text-purple-800">CP:</label><input type="text" name="cp" maxlength="5" class="w-full border border-purple-200 rounded px-3 py-2 bg-white"></div>
                <div><label class="block text-sm text-purple-800">Ciudad:</label><input type="text" name="ciudad" class="w-full border border-purple-200 rounded px-3 py-2 bg-white"></div>
                <div><label class="block text-sm text-purple-800">Estado:</label><input type="text" name="estado" class="w-full border border-purple-200 rounded px-3 py-2 bg-white"></div>

                <div>
                    <label for="fechaNacimiento" class="block text-sm text-purple-800">Fecha de nacimiento</label>
                    <input type="date" name="fechaNacimiento" id="fechaNacimiento" required class="w-full border border-purple-200 rounded px-3 py-2 bg-white" />
                </div>

                <div>
                    <label for="usuario" class="block text-sm text-purple-800">Usuario</label>
                    <input type="text" name="usuario" id="usuario" required class="w-full border border-purple-200 rounded px-3 py-2 bg-white" />
                </div>

                <div>
                    <label for="clave" class="block text-sm text-purple-800">Contraseña</label>
                    <input type="password" name="clave" id="clave" required class="w-full border border-purple-200 rounded px-3 py-2 bg-white" />
                </div>

                <div>
                    <label for="rol" class="block text-sm text-purple-800">Rol</label>
                    <select name="rol" id="rol" required class="w-full border border-purple-200 rounded px-3 py-2 bg-white">
                        <option value="" disabled selected>Selecciona un rol</option>
                        <option value="admin">Admin</option>
                        <option value="general">General</option>
                        <option value="florista">Florista</option>
                    </select>
                </div>

                <div>
                    <label for="turno" class="block text-sm text-purple-800">Turno</label>
                    <select name="turno" id="turno" required class="w-full border border-purple-200 rounded px-3 py-2 bg-white">
                        <option value="" disabled selected>Selecciona un turno</option>
                        <option value="matutino">Matutino</option>
                        <option value="vespertino">Vespertino</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-pink-800">Estatus:</label>
                    <select name="estatus" id="add-estatus" class="w-full border border-pink-200 rounded px-3 py-2 bg-white" required>
                        <option value="1" selected>Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>

                <!-- BOTÓN GUARDAR -->
                <div class="col-span-2 flex justify-center mt-4">
                    <button type="submit" class="bg-gradient-to-r from-purple-700 to-purple-500 hover:from-purple-600 hover:to-purple-400 text-white font-semibold py-2 rounded-lg text-lg transition hover:-translate-y-1 hover:shadow-lg px-4">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



<!-- Modal Editar Empleado -->
<div id="edit-user-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-pink-100 bg-opacity-60 hidden">
    <div class="w-full max-w-lg h-[90vh] rounded-2xl bg-pink-100 p-6 mx-4 my-6 shadow-lg overflow-hidden fade-in-up">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-xl font-semibold text-pink-950">Editar empleado</h3>
            <button type="button" class="text-pink-500 hover:text-pink-700 transition" onclick="document.getElementById('edit-user-modal').classList.add('hidden')">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <div class="px-6">
            <form method="POST" action="" class="grid grid-cols-2 gap-4 overflow-y-auto pr-2" style="max-height: 70vh;">
                <input type="hidden" name="action" value="edit_user">
                <input type="hidden" name="idEmpleado" id="edit-id">

                <div><label class="block text-sm text-pink-800">Nombre:</label><input type="text" name="nombre" id="edit-nombre" class="w-full border border-pink-200 rounded px-3 py-2 bg-white" required></div>
                <div><label class="block text-sm text-pink-800">Apellido Paterno:</label><input type="text" name="apellidoPaterno" id="edit-apellidoPaterno" class="w-full border border-pink-200 rounded px-3 py-2 bg-white" required></div>
                <div><label class="block text-sm text-pink-800">Apellido Materno:</label><input type="text" name="apellidoMaterno" id="edit-apellidoMaterno" class="w-full border border-pink-200 rounded px-3 py-2 bg-white" required></div>
                <div><label class="block text-sm text-pink-800">RFC:</label><input type="text" name="RFC" id="edit-RFC" maxlength="13" class="w-full border border-pink-200 rounded px-3 py-2 bg-white"></div>
                <div><label class="block text-sm text-pink-800">Teléfono:</label><input type="text" name="telefono" id="edit-telefono" maxlength="10" class="w-full border border-pink-200 rounded px-3 py-2 bg-white"></div>
                <div><label class="block text-sm text-pink-800">Salario:</label><input type="number" name="sueldo" id="edit-sueldo" class="w-full border border-pink-200 rounded px-3 py-2 bg-white"></div>
                <div><label class="block text-sm text-pink-800">Calle:</label><input type="text" name="calle" id="edit-calle" class="w-full border border-pink-200 rounded px-3 py-2 bg-white"></div>
                <div><label class="block text-sm text-pink-800">No. Casa:</label><input type="text" name="noCasa" id="edit-noCasa" class="w-full border border-pink-200 rounded px-3 py-2 bg-white"></div>
                <div><label class="block text-sm text-pink-800">Colonia:</label><input type="text" name="colonia" id="edit-colonia" class="w-full border border-pink-200 rounded px-3 py-2 bg-white"></div>
                <div><label class="block text-sm text-pink-800">CP:</label><input type="text" name="CP" id="edit-CP" maxlength="5" class="w-full border border-pink-200 rounded px-3 py-2 bg-white"></div>
                <div><label class="block text-sm text-pink-800">Ciudad:</label><input type="text" name="ciudad" id="edit-ciudad" class="w-full border border-pink-200 rounded px-3 py-2 bg-white"></div>
                <div><label class="block text-sm text-pink-800">Estado:</label><input type="text" name="estado" id="edit-estado" class="w-full border border-pink-200 rounded px-3 py-2 bg-white"></div>
                <div><label class="block text-sm text-pink-800">Fecha de nacimiento:</label><input type="date" name="fechaNacimiento" id="edit-fechaNacimiento" class="w-full border border-pink-200 rounded px-3 py-2 bg-white" required></div>
                <div><label class="block text-sm text-pink-800">Usuario:</label><input type="text" name="usuario" id="edit-usuario" class="w-full border border-pink-200 rounded px-3 py-2 bg-white" required></div>

                <!-- NO se incluye clave (contraseña) en edición -->

                <div>
                    <label class="block text-sm text-pink-800">Rol:</label>
                    <select name="rol" id="edit-rol" class="w-full border border-pink-200 rounded px-3 py-2 bg-white" required>
                        <option value="" disabled>Selecciona un rol</option>
                        <option value="admin">Admin</option>
                        <option value="general">General</option>
                        <option value="florista">Florista</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm text-pink-800">Turno:</label>
                    <select name="turno" id="edit-turno" class="w-full border border-pink-200 rounded px-3 py-2 bg-white" required>
                        <option value="" disabled>Selecciona un turno</option>
                        <option value="matutino">Matutino</option>
                        <option value="vespertino">Vespertino</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-pink-800">Estatus:</label>
                    <select name="estatus" id="edit-estatus" class="w-full border border-pink-200 rounded px-3 py-2 bg-white" required>
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>

                <!-- BOTÓN GUARDAR -->
                <div class="col-span-2 flex justify-center mt-4">
                    <button type="submit" class="bg-gradient-to-r from-pink-500 to-pink-400 hover:from-pink-400 hover:to-pink-300 text-white font-semibold py-2 rounded-lg text-lg transition hover:-translate-y-1 hover:shadow-lg px-4">
                        Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    // Búsqueda por nombre y correo (usuario)
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('user-search');
        const tableRows = document.querySelectorAll('tbody tr');

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();

            tableRows.forEach(row => {
                const name = row.cells[0].textContent.toLowerCase();   // Nombre
                const user = row.cells[1].textContent.toLowerCase();   // Usuario (correo o nombre de usuario)

                if (name.includes(searchTerm) || user.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
</script>
<script>
    // Función para abrir modal de edición y llenar formulario
    function openEditModal(button) {
        const modal = document.getElementById('edit-user-modal');
        modal.classList.remove('hidden');

        document.getElementById('edit-id').value = button.dataset.id || '';
        document.getElementById('edit-nombre').value = button.dataset.nombre || '';
        document.getElementById('edit-apellidoPaterno').value = button.dataset.apellidoPaterno || '';
        document.getElementById('edit-apellidoMaterno').value = button.dataset.apellidoMaterno || '';
        document.getElementById('edit-usuario').value = button.dataset.usuario || '';
        document.getElementById('edit-RFC').value = button.dataset.rfc || '';
        document.getElementById('edit-telefono').value = button.dataset.telefono || '';
        document.getElementById('edit-calle').value = button.dataset.calle || '';
        document.getElementById('edit-noCasa').value = button.dataset.nocasa || '';
        document.getElementById('edit-colonia').value = button.dataset.colonia || '';
        document.getElementById('edit-CP').value = button.dataset.CP || '';
        document.getElementById('edit-ciudad').value = button.dataset.ciudad || '';
        document.getElementById('edit-estado').value = button.dataset.estado || '';
        document.getElementById('edit-fechaNacimiento').value = button.dataset.fechaNacimiento || '';
        document.getElementById('edit-rol').value = button.dataset.rol || '';
        document.getElementById('edit-turno').value = button.dataset.turno || '';
        document.getElementById('edit-sueldo').value = button.dataset.sueldo || '';
        document.getElementById('edit-estatus').value = button.dataset.estatus || '';
    }

    // Cerrar modal haciendo clic fuera del contenido
    document.getElementById('edit-user-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });

    // Filtro búsqueda en tabla
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('user-search');
        const tableRows = document.querySelectorAll('tbody tr');

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();

            tableRows.forEach(row => {
                const name = row.cells[0].textContent.toLowerCase();
                const user = row.cells[1].textContent.toLowerCase();

                if (name.includes(searchTerm) || user.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
</script>
<?php if (!empty($toastMessage)): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        showToast("<?= addslashes($toastMessage) ?>");
    });
</script>
<?php endif; ?>
</body>

</html>



