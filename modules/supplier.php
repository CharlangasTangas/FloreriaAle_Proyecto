<?php
include 'config\connection.php';

$mensaje = '';
$mensaje_tipo = 'success';
if (isset($_GET['msg'])) {
    $mensaje = $_GET['msg'];
    $mensaje_tipo = isset($_GET['type']) && $_GET['type'] === 'error' ? 'error' : 'success';
}
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
}

// Obtener todos los proveedores
$result = $connection->query("SELECT * FROM Proveedor");
$proveedores = $result->fetch_all(MYSQLI_ASSOC);
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
<?php endif; ?>

<div class="container mx-auto">
    <h1 class="text-2xl font-bold mb-4">Gestión de Proveedores</h1>

    <!-- Botón para registrar proveedor -->
    <button onclick="openRegisterModal()"
        class="mb-6 px-4 py-2 bg-purple-700 hover:bg-purple-500 text-white font-semibold rounded-lg text-lg transition hover:-translate-y-1 hover:shadow-lg">
        Registrar proveedor
    </button>

    <!-- Tabla de proveedores -->
    <h2 class="text-xl font-semibold mb-2">Proveedores</h2>
    <table class="table-auto w-full bg-white rounded shadow overflow-hidden">
        <thead>
            <tr class="border-b bg-purple-50">
                <th class="px-4 py-2 text-left font-medium text-purple-800">ID</th>
                <th class="px-4 py-2 text-left font-medium text-purple-800">Nombre</th>
                <th class="px-4 py-2 text-left font-medium text-purple-800">Dirección</th>
                <th class="px-4 py-2 text-left font-medium text-purple-800">RFC</th>
                <th class="px-4 py-2 text-left font-medium text-purple-800">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($proveedores as $proveedor): ?>
                <tr>
                    <td class="border px-4 py-2"><?php echo htmlspecialchars($proveedor['idProveedor']); ?></td>
                    <td class="border px-4 py-2"><?php echo htmlspecialchars($proveedor['nombre']); ?></td>
                    <td class="border px-4 py-2">
                        <?php
                        echo htmlspecialchars($proveedor['calle']) . ' No. ' . htmlspecialchars($proveedor['noCasa']) . ', ' . htmlspecialchars($proveedor['colonia']) . ', CP ' . htmlspecialchars($proveedor['CP']);
                        ?>
                    </td>
                    <td class="border px-4 py-2"><?php echo htmlspecialchars($proveedor['RFC']); ?></td>
                    <td class="border px-4 py-2">
                        <button class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded"
                            onclick='openDetailsModal(<?php echo json_encode($proveedor); ?>)'>
                            Ver detalles
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (count($proveedores) === 0): ?>
                <tr>
                    <td colspan="5" class="text-center py-4">No hay proveedores registrados.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Modal: Detalles del proveedor -->
    <div id="detailsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden"
        onclick="closeDetailsModal()">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative" onclick="event.stopPropagation()">
            <button onclick="closeDetailsModal()"
                class="absolute top-2 right-2 text-gray-500 hover:text-red-600 text-2xl font-bold">&times;</button>
            <h2 class="text-xl font-semibold mb-4">Detalles del proveedor</h2>
            <div id="detailsContent"></div>
            <div class="flex gap-2 mt-4">
                <button id="editBtn"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded">Editar</button>
            </div>
        </div>
    </div>

    <!-- Modal: Editar proveedor -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden"
        onclick="closeEditModal()">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative" onclick="event.stopPropagation()">
            <button onclick="closeEditModal()"
                class="absolute top-2 right-2 text-gray-500 hover:text-red-600 text-2xl font-bold">&times;</button>
            <h2 class="text-xl font-semibold mb-4">Editar proveedor</h2>
            <form id="editForm" method="POST" autocomplete="off"></form>
        </div>
    </div>

    <!-- Modal: Registrar proveedor -->
    <div id="registerModal"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden"
        onclick="closeRegisterModal()">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative" onclick="event.stopPropagation()">
            <button onclick="closeRegisterModal()"
                class="absolute top-2 right-2 text-gray-500 hover:text-red-600 text-2xl font-bold">&times;</button>
            <h2 class="text-xl font-semibold mb-4">Registrar proveedor</h2>
            <form id="registerForm" method="POST" autocomplete="off"></form>
        </div>
    </div>
</div>

<script>
function openDetailsModal(proveedor) {
    let html = `
        <div class="grid grid-cols-2 gap-2">
            <div><b>ID:</b> ${proveedor.idProveedor}</div>
            <div><b>Nombre:</b> ${proveedor.nombre}</div>
            <div><b>Apellido Paterno:</b> ${proveedor.apellidoPaterno}</div>
            <div><b>Apellido Materno:</b> ${proveedor.apellidoMaterno}</div>
            <div><b>RFC:</b> ${proveedor.RFC}</div>
            <div><b>Teléfono:</b> ${proveedor.telefono}</div>
            <div><b>Email:</b> ${proveedor.email}</div>
            <div><b>Calle:</b> ${proveedor.calle}</div>
            <div><b>No. Casa:</b> ${proveedor.noCasa}</div>
            <div><b>Colonia:</b> ${proveedor.colonia}</div>
            <div><b>CP:</b> ${proveedor.CP}</div>
            <div><b>Ciudad:</b> ${proveedor.ciudad}</div>
            <div><b>Estado:</b> ${proveedor.estado}</div>
        </div>
    `;
    document.getElementById('detailsContent').innerHTML = html;
    document.getElementById('detailsModal').classList.remove('hidden');
    document.getElementById('editBtn').onclick = function () {
        openEditModal(proveedor);
    };
}
function closeDetailsModal() {
    document.getElementById('detailsModal').classList.add('hidden');
}

function openEditModal(proveedor) {
    let html = `
        <input type="hidden" name="action" value="edit_proveedor">
        <input type="hidden" name="idProveedor" value="${proveedor.idProveedor}">
        <div class="grid grid-cols-2 gap-2">
            <div><label>Nombre:</label><input type="text" name="nombre" value="${proveedor.nombre}" class="w-full border px-2 py-1" required></div>
            <div><label>Apellido Paterno:</label><input type="text" name="apellidoPaterno" value="${proveedor.apellidoPaterno}" class="w-full border px-2 py-1" required></div>
            <div><label>Apellido Materno:</label><input type="text" name="apellidoMaterno" value="${proveedor.apellidoMaterno}" class="w-full border px-2 py-1" required></div>
            <div><label>RFC:</label><input type="text" name="rfc" value="${proveedor.RFC}" class="w-full border px-2 py-1"></div>
            <div><label>Teléfono:</label><input type="text" name="telefono" value="${proveedor.telefono}" class="w-full border px-2 py-1"></div>
            <div><label>Email:</label><input type="email" name="email" value="${proveedor.email}" class="w-full border px-2 py-1"></div>
            <div><label>Calle:</label><input type="text" name="calle" value="${proveedor.calle}" class="w-full border px-2 py-1"></div>
            <div><label>No. Casa:</label><input type="text" name="noCasa" value="${proveedor.noCasa}" class="w-full border px-2 py-1"></div>
            <div><label>Colonia:</label><input type="text" name="colonia" value="${proveedor.colonia}" class="w-full border px-2 py-1"></div>
            <div><label>CP:</label><input type="text" name="cp" value="${proveedor.CP}" class="w-full border px-2 py-1"></div>
            <div><label>Ciudad:</label><input type="text" name="ciudad" value="${proveedor.ciudad}" class="w-full border px-2 py-1"></div>
            <div><label>Estado:</label><input type="text" name="estado" value="${proveedor.estado}" class="w-full border px-2 py-1"></div>
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
        <input type="hidden" name="action" value="add_proveedor">
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