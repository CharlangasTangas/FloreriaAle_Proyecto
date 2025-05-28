<?php
include 'config\connection.php';

$mensaje = '';
$mensaje_tipo = 'success';
$condicion = '1';

if (isset($_GET['msg'])) {
    $mensaje = $_GET['msg'];
    $mensaje_tipo = isset($_GET['type']) && $_GET['type'] === 'error' ? 'error' : 'success';
}

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
}

// Obtener clientes activos
$result = $connection->query("SELECT * FROM Cliente WHERE condicion='1'");
$clientes = $result->fetch_all(MYSQLI_ASSOC);

// Obtener clientes desactivados
$result_inactivos = $connection->query("SELECT * FROM Cliente WHERE condicion='0'");
$clientes_inactivos = $result_inactivos->fetch_all(MYSQLI_ASSOC);
?>

<?php if ($mensaje): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            alert("<?php echo addslashes($mensaje); ?>");
            if (window.location.search.includes('msg=')) {
                const url = new URL(window.location.href);
                url.searchParams.delete('msg');
                url.searchParams.delete('type');
                window.history.replaceState({}, document.title, url.pathname + url.search);
            }
        });
    </script>
<?php endif; ?>>

<div class="container mx-auto">
    <h1 class="text-2xl font-bold mb-4">Gestión de Clientes</h1>

    <!-- Botón para registrar cliente -->
    <button onclick="openRegisterModal()"
        class="mb-6 px-4 py-2 bg-purple-700 hover:bg-purple-500 text-white font-semibold rounded-lg text-lg transition hover:-translate-y-1 hover:shadow-lg">
        Registrar cliente
    </button>

    <!-- Tabla de clientes activos (simplificada) -->
    <h2 class="text-xl font-semibold mb-2">Clientes activos</h2>
    <table class="table-auto w-full bg-white rounded shadow overflow-hidden">
        <thead>
            <tr class="border-b bg-purple-50">
                <th class="px-4 py-2 text-left font-medium text-purple-800">ID</th>
                <th class="px-4 py-2 text-left font-medium text-purple-800">Nombre</th>
                <th class="px-4 py-2 text-left font-medium text-purple-800">Apellido Paterno</th>
                <th class="px-4 py-2 text-left font-medium text-purple-800">RFC</th>
                <th class="px-4 py-2 text-left font-medium text-purple-800">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clientes as $cliente): ?>
                <tr>
                    <td class="border px-4 py-2"><?php echo htmlspecialchars($cliente['idCliente']); ?></td>
                    <td class="border px-4 py-2"><?php echo htmlspecialchars($cliente['nombre']); ?></td>
                    <td class="border px-4 py-2"><?php echo htmlspecialchars($cliente['apellidoPaterno']); ?></td>
                    <td class="border px-4 py-2"><?php echo htmlspecialchars($cliente['RFC']); ?></td>
                    <td class="border px-4 py-2">
                        <button class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded"
                            onclick='openDetailsModal(<?php echo json_encode($cliente); ?>)'>
                            Ver detalles
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (count($clientes) === 0): ?>
                <tr>
                    <td colspan="5" class="text-center py-4">No hay clientes activos.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Botón y modal de clientes desactivados -->
    <button onclick="document.getElementById('modal-inactivos').classList.remove('hidden')"
        class="mt-8 px-3 py-1 bg-gray-700 hover:bg-gray-500 text-white font-semibold py-3 rounded-lg text-lg transition hover:-translate-y-1 hover:shadow-lg">
        Ver clientes desactivados
    </button>

    <div id="modal-inactivos"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden"
        onclick="this.classList.add('hidden')">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-6xl max-h-[90vh] p-6 relative overflow-y-auto"
            onclick="event.stopPropagation()">
            <button onclick="document.getElementById('modal-inactivos').classList.add('hidden')"
                class="absolute top-2 right-2 text-gray-500 hover:text-red-600 text-2xl font-bold">&times;</button>
            <h2 class="text-xl font-semibold mb-4">Clientes desactivados</h2>
            <div class="overflow-x-auto">
                <table class="table-auto w-full bg-white rounded shadow overflow-hidden">
                    <thead>
                        <tr class="border-b bg-purple-50">
                            <th class="px-2 py-1 text-left font-medium text-purple-800">ID</th>
                            <th class="px-2 py-1 text-left font-medium text-purple-800">Nombre</th>
                            <th class="px-2 py-1 text-left font-medium text-purple-800">Apellido Paterno</th>
                            <th class="px-2 py-1 text-left font-medium text-purple-800">Apellido Materno</th>
                            <th class="px-2 py-1 text-left font-medium text-purple-800">RFC</th>
                            <th class="px-2 py-1 text-left font-medium text-purple-800">Teléfono</th>
                            <th class="px-2 py-1 text-left font-medium text-purple-800">Email</th>
                            <th class="px-2 py-1 text-left font-medium text-purple-800">Dirección</th>
                            <th class="px-2 py-1 text-left font-medium text-purple-800">Ciudad</th>
                            <th class="px-2 py-1 text-left font-medium text-purple-800">Estado</th>
                            <th class="px-2 py-1 text-left font-medium text-purple-800">Condición</th>
                            <th class="px-2 py-1 text-left font-medium text-purple-800">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clientes_inactivos as $cliente): ?>
                            <tr>
                                <td class="border px-2 py-1"><?php echo htmlspecialchars($cliente['idCliente']); ?></td>
                                <td class="border px-2 py-1"><?php echo htmlspecialchars($cliente['nombre']); ?></td>
                                <td class="border px-2 py-1"><?php echo htmlspecialchars($cliente['apellidoPaterno']); ?></td>
                                <td class="border px-2 py-1"><?php echo htmlspecialchars($cliente['apellidoMaterno']); ?></td>
                                <td class="border px-2 py-1"><?php echo htmlspecialchars($cliente['RFC']); ?></td>
                                <td class="border px-2 py-1"><?php echo htmlspecialchars($cliente['telefono']); ?></td>
                                <td class="border px-2 py-1"><?php echo htmlspecialchars($cliente['email']); ?></td>
                                <td class="border px-2 py-1">
                                    <?php
                                    echo htmlspecialchars($cliente['calle']) . ' No. ' . htmlspecialchars($cliente['noCasa']) . ', ' . htmlspecialchars($cliente['colonia']) . ', CP ' . htmlspecialchars($cliente['CP']);
                                    ?>
                                </td>
                                <td class="border px-2 py-1"><?php echo htmlspecialchars($cliente['ciudad']); ?></td>
                                <td class="border px-2 py-1"><?php echo htmlspecialchars($cliente['estado']); ?></td>
                                <td class="border px-2 py-1"><?php echo htmlspecialchars($cliente['condicion']); ?></td>
                                <td class="border px-2 py-1">
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

    <!-- Modal: Detalles del cliente -->
    <div id="detailsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden"
        onclick="closeDetailsModal()">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative" onclick="event.stopPropagation()">
            <button onclick="closeDetailsModal()"
                class="absolute top-2 right-2 text-gray-500 hover:text-red-600 text-2xl font-bold">&times;</button>
            <h2 class="text-xl font-semibold mb-4">Detalles del cliente</h2>
            <div id="detailsContent"></div>
            <div class="flex gap-2 mt-4">
                <button id="editBtn"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded">Editar</button>
                <button id="deactivateBtn"
                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">Desactivar</button>
            </div>
        </div>
    </div>

    <!-- Modal: Editar cliente -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden"
        onclick="closeEditModal()">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative" onclick="event.stopPropagation()">
            <button onclick="closeEditModal()"
                class="absolute top-2 right-2 text-gray-500 hover:text-red-600 text-2xl font-bold">&times;</button>
            <h2 class="text-xl font-semibold mb-4">Editar cliente</h2>
            <form id="editForm" method="POST" autocomplete="off"></form>
        </div>
    </div>

    <!-- Modal: Registrar cliente -->
    <div id="registerModal"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden"
        onclick="closeRegisterModal()">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative" onclick="event.stopPropagation()">
            <button onclick="closeRegisterModal()"
                class="absolute top-2 right-2 text-gray-500 hover:text-red-600 text-2xl font-bold">&times;</button>
            <h2 class="text-xl font-semibold mb-4">Registrar cliente</h2>
            <form id="registerForm" method="POST" autocomplete="off"></form>
        </div>
    </div>
</div>

<script>
function openDetailsModal(cliente) {
    let html = `
        <div class="grid grid-cols-2 gap-2">
            <div><b>ID:</b> ${cliente.idCliente}</div>
            <div><b>Nombre:</b> ${cliente.nombre}</div>
            <div><b>Apellido Paterno:</b> ${cliente.apellidoPaterno}</div>
            <div><b>Apellido Materno:</b> ${cliente.apellidoMaterno}</div>
            <div><b>RFC:</b> ${cliente.RFC}</div>
            <div><b>Teléfono:</b> ${cliente.telefono}</div>
            <div><b>Email:</b> ${cliente.email}</div>
            <div><b>Calle:</b> ${cliente.calle}</div>
            <div><b>No. Casa:</b> ${cliente.noCasa}</div>
            <div><b>Colonia:</b> ${cliente.colonia}</div>
            <div><b>CP:</b> ${cliente.CP}</div>
            <div><b>Ciudad:</b> ${cliente.ciudad}</div>
            <div><b>Estado:</b> ${cliente.estado}</div>
            <div><b>Condición:</b> ${cliente.condicion == 1 ? 'Activo' : 'Inactivo'}</div>
        </div>
    `;
    document.getElementById('detailsContent').innerHTML = html;
    document.getElementById('detailsModal').classList.remove('hidden');
    document.getElementById('editBtn').onclick = function () {
        openEditModal(cliente);
    };
    document.getElementById('deactivateBtn').onclick = function () {
        if (confirm('¿Seguro que deseas desactivar este cliente?')) {
            window.location.href = '?page=customer&action=deactivate&id=' + cliente.idCliente;
        }
    };
}
function closeDetailsModal() {
    document.getElementById('detailsModal').classList.add('hidden');
}

function openEditModal(cliente) {
    let html = `
        <input type="hidden" name="action" value="edit_cliente">
        <input type="hidden" name="idCliente" value="${cliente.idCliente}">
        <div class="grid grid-cols-2 gap-2">
            <div><label>Nombre:</label><input type="text" name="nombre" value="${cliente.nombre}" class="w-full border px-2 py-1" required></div>
            <div><label>Apellido Paterno:</label><input type="text" name="apellidoPaterno" value="${cliente.apellidoPaterno}" class="w-full border px-2 py-1" required></div>
            <div><label>Apellido Materno:</label><input type="text" name="apellidoMaterno" value="${cliente.apellidoMaterno}" class="w-full border px-2 py-1" required></div>
            <div><label>RFC:</label><input type="text" name="rfc" value="${cliente.RFC}" class="w-full border px-2 py-1"></div>
            <div><label>Teléfono:</label><input type="text" name="telefono" value="${cliente.telefono}" class="w-full border px-2 py-1"></div>
            <div><label>Email:</label><input type="email" name="email" value="${cliente.email}" class="w-full border px-2 py-1"></div>
            <div><label>Calle:</label><input type="text" name="calle" value="${cliente.calle}" class="w-full border px-2 py-1"></div>
            <div><label>No. Casa:</label><input type="text" name="noCasa" value="${cliente.noCasa}" class="w-full border px-2 py-1"></div>
            <div><label>Colonia:</label><input type="text" name="colonia" value="${cliente.colonia}" class="w-full border px-2 py-1"></div>
            <div><label>CP:</label><input type="text" name="cp" value="${cliente.CP}" class="w-full border px-2 py-1"></div>
            <div><label>Ciudad:</label><input type="text" name="ciudad" value="${cliente.ciudad}" class="w-full border px-2 py-1"></div>
            <div><label>Estado:</label><input type="text" name="estado" value="${cliente.estado}" class="w-full border px-2 py-1"></div>
            <div>
                <label>Condición:</label>
                <select name="condicion" class="w-full border px-2 py-1">
                    <option value="1" ${cliente.condicion == 1 ? 'selected' : ''}>Activo</option>
                    <option value="0" ${cliente.condicion == 0 ? 'selected' : ''}>Inactivo</option>
                </select>
            </div>
        </div>
        <div class="mt-4 flex gap-3 items-center">
            <button type="submit" class="px-4 py-2 bg-yellow-600 hover:bg-gray-500 text-white font-semibold rounded-lg">Actualizar</button>
            <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-700 hover:bg-gray-500 text-white font-semibold rounded-lg">Cancelar</button>
        </div>
    `;
    document.getElementById('editForm').innerHTML = html;
    document.getElementById('editModal').classList.remove('hidden');
    closeDetailsModal();
}
function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

function openRegisterModal() {
    let html = `
        <input type="hidden" name="action" value="add_cliente">
        <div class="grid grid-cols-2 gap-2">
            <div><label>Nombre:</label><input type="text" name="nombre" class="w-full border px-2 py-1" required></div>
            <div><label>Apellido Paterno:</label><input type="text" name="apellidoPaterno" class="w-full border px-2 py-1" required></div>
            <div><label>Apellido Materno:</label><input type="text" name="apellidoMaterno" class="w-full border px-2 py-1" required></div>
            <div><label>RFC:</label><input type="text" name="rfc" class="w-full border px-2 py-1"></div>
            <div><label>Teléfono:</label><input type="text" name="telefono" class="w-full border px-2 py-1"></div>
            <div><label>Email:</label><input type="email" name="email" class="w-full border px-2 py-1"></div>
            <div><label>Calle:</label><input type="text" name="calle" class="w-full border px-2 py-1"></div>
            <div><label>No. Casa:</label><input type="text" name="noCasa" class="w-full border px-2 py-1"></div>
            <div><label>Colonia:</label><input type="text" name="colonia" class="w-full border px-2 py-1"></div>
            <div><label>CP:</label><input type="text" name="cp" class="w-full border px-2 py-1"></div>
            <div><label>Ciudad:</label><input type="text" name="ciudad" class="w-full border px-2 py-1"></div>
            <div><label>Estado:</label><input type="text" name="estado" class="w-full border px-2 py-1"></div>
            <div>
                <label>Condición:</label>
                <select name="condicion" class="w-full border px-2 py-1">
                    <option value="1">Activo</option>
                    <option value="0">Inactivo</option>
                </select>
            </div>
        </div>
        <div class="mt-4 flex gap-3 items-center">
            <button type="submit" class="px-4 py-2 bg-purple-700 hover:bg-purple-500 text-white font-semibold rounded-lg">Registrar</button>
            <button type="button" onclick="closeRegisterModal()" class="px-4 py-2 bg-gray-700 hover:bg-gray-500 text-white font-semibold rounded-lg">Cancelar</button>
        </div>
    `;
    document.getElementById('registerForm').innerHTML = html;
    document.getElementById('registerModal').classList.remove('hidden');
}
function closeRegisterModal() {
    document.getElementById('registerModal').classList.add('hidden');
}
</script>