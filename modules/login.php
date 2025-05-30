<?php
include '..\config\connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $clave = $_POST['clave'];

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    $sql = $connection->prepare("SELECT * FROM Empleado WHERE usuario = ?");
    $sql->bind_param("s", $usuario);
    $sql->execute();
    $result = $sql->get_result();
    $usuarioData = $result->fetch_assoc();

    if ($usuarioData) {
        if ($clave === $usuarioData['clave']) {
            $_SESSION['idEmpleado'] = $usuarioData['idEmpleado'];
            $_SESSION['usuario'] = $usuarioData['usuario'];
            $_SESSION['nombre'] = $usuarioData['nombre'];
            $_SESSION['rol'] = $usuarioData['rol'];
            header('Location: ..\index.php');
            exit();
        } else {
            $error = "Usuario o contraseña incorrectos";
        }
    } else {
        $error = "Usuario o contraseña incorrectos";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login | Florería Ale</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-r from-purple-800 via-purple-600 to-purple-400 font-sans">
    <div class="w-full max-w-5xl bg-white rounded-2xl shadow-2xl overflow-hidden grid grid-cols-1 md:grid-cols-2">
        <div class="relative flex flex-col justify-center items-center p-10 bg-gradient-to-br from-purple-700 to-purple-900 text-white">
            <h2 class="text-4xl font-bold mb-4 z-10">Bienvenido</h2>
            <p class="text-center text-lg z-10">Inicia sesión para acceder a tu cuenta de Florería Ale.</p>

            <div class="absolute top-5 left-1/3 w-36 h-36 bg-white/10 rounded-full blur-2xl z-0 animate-pulse"></div>
            <div class="absolute bottom-1/4 left-1/4 w-48 h-48 bg-white/10 rounded-full blur-2xl z-0 animate-pulse"></div>
            <div class="absolute top-10 right-1/4 w-20 h-20 bg-white/10 rounded-full blur-2xl z-0 animate-pulse"></div>
            <div class="absolute bottom-10 right-1/3 w-24 h-24 bg-white/10 rounded-full blur-2xl z-0 animate-pulse"></div>

            <div class="absolute bottom-0 left-0 w-full overflow-hidden leading-none rotate-180">
                <svg viewBox="0 0 1440 320" class="w-full h-24"><path fill="#FFFFFF" fill-opacity="1" d="M0,160L48,170.7C96,181,192,203,288,197.3C384,192,480,160,576,144C672,128,768,128,864,138.7C960,149,1056,171,1152,170.7C1248,171,1344,149,1392,138.7L1440,128L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z"></path></svg>
            </div>
        </div>

        <form method="post" class="bg-white p-10 flex flex-col justify-center items-center">
            <h1 class="text-4xl font-bold text-center mb-6 bg-gradient-to-r from-purple-400 to-purple-800 bg-clip-text text-transparent">Florería Ale</h1>
            <h2 class="text-lg font-semibold text-gray-700 text-center mb-4">
                <i class="fas fa-user-circle text-2xl text-purple-600 mr-2"></i> Iniciar Sesión
            </h2>

            <input type="text" name="usuario" placeholder="Usuario" required
                class="w-full px-4 py-2 mb-6 border-b-2 border-transparent focus:outline-none focus:border-gradient-to-r focus:from-purple-600 focus:to-purple-300 bg-transparent transition" />

            <input type="password" name="clave" placeholder="Contraseña" required
                class="w-full px-4 py-2 mb-8 border-b-2 border-transparent focus:outline-none focus:border-gradient-to-r focus:from-purple-600 focus:to-purple-300 bg-transparent transition" />

            <button type="submit"
                class="w-full bg-gradient-to-r from-purple-700 to-purple-500 hover:from-purple-600 hover:to-purple-400 text-white font-semibold py-3 rounded-lg text-lg transition hover:-translate-y-1 hover:shadow-lg">
                Ingresar
            </button>

            <?php if (isset($error)): ?>
                <p class="mt-4 text-center text-red-600 font-medium"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>
