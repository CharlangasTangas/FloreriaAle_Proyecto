
    <?php
    
include 'config\connection.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $usuario = $_POST['usuario'];
        $clave = $_POST['clave'];

        // Iniciar la sesión si no está iniciada (puede ser necesario para el $_SESSION)
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $sql = $connection->prepare("SELECT * FROM Empleado WHERE usuario = ?");
        $sql->bind_param("s", $usuario);
        $sql->execute();
        $result = $sql->get_result();
        $usuarioData = $result->fetch_assoc();

        if ($usuarioData) {
            // Comparación de contraseña sin hashing (se recomienda hashing para producción)
            if ($clave === $usuarioData['clave']) {
                $_SESSION['usuario'] = $usuarioData['usuario'];
                $_SESSION['nombre'] = $usuarioData['nombre'];
                $_SESSION['rol'] = $usuarioData['rol'];
                header('Location: index.php');
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
    <style>
        /* Estilos personalizados para el degradado del fondo izquierdo y las líneas de input */
        .gradient-bg-left {
            background: linear-gradient(to bottom right, #6D28D9, #4C1D95); /* Degradado de morado más claro a más oscuro */
            position: relative;
            overflow: hidden; /* Asegura que las formas no se salgan */
        }
        .gradient-bg-left::before,
        .gradient-bg-left::after,
        .shape-circle-1,
        .shape-circle-2,
        .shape-abstract-1,
        .shape-abstract-2 {
            content: '';
            position: absolute;
            background: rgba(255, 255, 255, 0.1); /* Blanco translúcido para las formas */
            filter: blur(20px); /* Suaviza las formas */
            border-radius: 50%; /* Por defecto circular, algunas se cambiarán */
            z-index: 1; /* Detrás del contenido de texto */
        }

        /* Formas circulares existentes */
        .gradient-bg-left::before { /* Círculo grande arriba izquierda */
            width: 300px;
            height: 300px;
            top: -50px;
            left: -50px;
            transform: rotate(45deg);
        }
        .gradient-bg-left::after { /* Círculo grande abajo derecha */
            width: 200px;
            height: 200px;
            bottom: -80px;
            right: -80px;
            transform: rotate(-30deg);
        }
        .shape-circle-1 { /* Círculo mediano arriba derecha */
            width: 80px;
            height: 80px;
            top: 20%;
            right: 15%;
        }
        .shape-circle-2 { /* Círculo mediano abajo izquierda */
            width: 120px;
            height: 120px;
            bottom: 25%;
            left: 10%;
        }

        /* Nuevas formas abstractas */
        .shape-abstract-1 { /* Forma irregular / blob */
            width: 150px;
            height: 150px;
            background: rgba(255, 255, 255, 0.08); /* Ligeramente menos opaco */
            border-radius: 40% 60% 70% 30% / 50% 60% 40% 50%; /* Forma más orgánica */
            top: 5%;
            left: 30%;
            filter: blur(25px);
            animation: float 8s ease-in-out infinite; /* Animación de flotación */
        }
        .shape-abstract-2 { /* Otra forma irregular */
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.06);
            border-radius: 60% 40% 30% 70% / 70% 30% 60% 40%;
            bottom: 10%;
            right: 30%;
            filter: blur(20px);
            animation: float 10s ease-in-out infinite reverse; /* Animación inversa */
        }

        /* Keyframes para la animación de flotación */
        @keyframes float {
            0% { transform: translate(0, 0) rotate(0deg); }
            25% { transform: translate(10px, -10px) rotate(5deg); }
            50% { transform: translate(0, 10px) rotate(0deg); }
            75% { transform: translate(-10px, -5px) rotate(-5deg); }
            100% { transform: translate(0, 0) rotate(0deg); }
        }


        /* Estilo para las líneas de los inputs con degradado */
        .input-underline-gradient {
            border: none;
            border-bottom: 2px solid;
            border-image: linear-gradient(to right, #6D28D9, #8B5CF6); /* Degradado morado-azul */
            border-image-slice: 1;
            background: transparent; /* Asegura que el fondo del input sea transparente */
            transition: border-image 0.3s ease-in-out; /* Transición para el enfoque */
        }
        .input-underline-gradient:focus {
            outline: none; /* Elimina el contorno de enfoque predeterminado */
            border-image: linear-gradient(to right, #8B5CF6, #C084FC); /* Cambia el degradado al enfocar */
            border-image-slice: 1;
        }

        /* Estilo para el botón SUBMIT con degradado */
        .submit-button-gradient {
            background: linear-gradient(to right, #6D28D9, #8B5CF6); /* Degradado del botón */
            transition: all 0.3s ease-in-out;
        }
        .submit-button-gradient:hover {
            background: linear-gradient(to right, #8B5CF6, #C084FC); /* Degradado al pasar el ratón */
            transform: translateY(-2px); /* Pequeño efecto al pasar el ratón */
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }

        /* Estilo para el saludo "Good Morning" */
        .greeting-gradient-text {
            background: linear-gradient(to right, #8B5CF6, #6D28D9);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 600; /* Semibold */
        }

        /* Olas en la parte inferior izquierda (ajustado para que la ola "suba" un poco menos) */
        .wave-bottom {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100px; /* Altura de la sección de la ola */
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23FFFFFF" fill-opacity="1" d="M0,160L48,170.7C96,181,192,203,288,197.3C384,192,480,160,576,144C672,128,768,128,864,138.7C960,149,1056,171,1152,170.7C1248,171,1344,149,1392,138.7L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-repeat: no-repeat;
            background-size: cover;
            background-position: bottom;
            transform: translateY(40%); /* Ajusta para mostrar la parte superior de la ola */
            z-index: 10; /* Asegura que esté por encima de otros elementos */
        }

    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-r from-purple-800 via-purple-600 to-purple-400 font-sans">
    <div class="w-full max-w-5xl bg-white rounded-2xl shadow-2xl overflow-hidden grid grid-cols-1 md:grid-cols-2">
        <div class="gradient-bg-left text-white flex flex-col justify-center items-center p-10 relative">
            <h2 class="text-4xl font-bold mb-4 z-20">Bienvenido</h2>
            <p class="text-center text-lg z-20">Inicia sesión para acceder a tu cuenta de Florería Ale.</p>
            <div class="shape-circle-1 z-10"></div>
            <div class="shape-circle-2 z-10"></div>
            <div class="shape-abstract-1 z-10"></div>
            <div class="shape-abstract-2 z-10"></div>
            <div class="wave-bottom z-20"></div> </div>

        <form method="post" class="bg-white p-10 flex flex-col justify-center items-center">
            <h1 class="text-4xl font-bold text-purple-700 text-center mb-6 greeting-gradient-text">Florería Ale</h1>
            <h2 class="text-lg font-semibold text-gray-700 text-center mb-4">
                <i class="fas fa-user-circle text-2xl text-purple-600 mr-2"></i> Iniciar Sesión
            </h2>

            <input type="text" name="usuario" placeholder="Usuario" required
                    class="w-full px-4 py-2 mb-6 input-underline-gradient" />

            <input type="password" name="clave" placeholder="Contraseña" required
                    class="w-full px-4 py-2 mb-8 input-underline-gradient" />

            <button type="submit"
                    class="w-full submit-button-gradient text-white font-semibold py-3 rounded-lg text-lg">
                Ingresar
            </button>

            <?php if (isset($error)): ?>
                <p class="mt-4 text-center text-red-600 font-medium"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
        </form>
    </div>

</body>
</html>