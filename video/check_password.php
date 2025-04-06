<?php
// --- CONFIGURACIÓN SEGURA ---
// Define aquí tus contraseñas y los enlaces de Meet correspondientes.
// ¡¡¡ESTO NUNCA SERÁ VISIBLE EN EL NAVEGADOR DEL USUARIO!!!
$valid_access_codes = [
    // Contraseña => Enlace de Meet
    "formacion2025" => "https://meet.google.com/pmh-vcku-xhk", // Cambia esto por tu enlace real
    "hackers2025" => "https://meet.google.com/itw-bysu-rme", // Clase de hacking con los niños
    "familia2025" => "https://meet.google.com/hwp-ocwf-xmx", // Añade tantas como necesites
    // ... puedes añadir más pares clave => valor aquí
];
// --- FIN DE LA CONFIGURACIÓN ---

// Variable para guardar el resultado
$output_html = '';
$is_valid = false;
$meet_link = '';
$submitted_password = '';

// 1. Comprobar si se ha enviado el formulario por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 2. Obtener la contraseña enviada (con seguridad básica)
    $submitted_password = isset($_POST['password']) ? trim($_POST['password']) : '';

    // 3. Validar la contraseña
    if (!empty($submitted_password) && isset($valid_access_codes[$submitted_password])) {
        // ¡Contraseña correcta!
        $is_valid = true;
        $meet_link = $valid_access_codes[$submitted_password];
    } else {
        // Contraseña incorrecta o vacía
        $is_valid = false;
    }

} else {
    // Si alguien intenta acceder a check_password.php directamente sin enviar datos
    // Podemos redirigir a la página principal o mostrar un error genérico.
    header('Location: index.html'); // O index.html si no renombraste
    exit; // Detener la ejecución del script
}

// 4. Preparar la salida HTML para mostrar al usuario
// Usaremos una estructura HTML similar a la de index.html para consistencia
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_valid ? 'Acceso Concedido' : 'Acceso Denegado'; ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        /* Estilos adicionales específicos para esta página de resultado */
        .result-box {
            padding: 25px;
            margin-top: 20px;
            border-radius: 8px;
            text-align: center;
        }
        .success-box {
            background-color: #e8f5e9; /* Verde claro */
            border: 1px solid #a5d6a7; /* Borde verde */
        }
        .error-box {
            background-color: #ffebee; /* Rojo claro */
            border: 1px solid #ef9a9a; /* Borde rojo */
        }
        .meet-link {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 25px;
            background-color: #4CAF50; /* Botón verde para el éxito */
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .meet-link:hover {
            background-color: #45a049;
        }
        .back-link {
             display: inline-block;
            margin-top: 20px;
            color: #555;
            text-decoration: none;
            font-size: 0.9em;
        }
         .back-link:hover {
             text-decoration: underline;
         }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($is_valid): ?>
            <header>
                <h1>✅ ¡Acceso Concedido!</h1>
            </header>
            <main>
                <div class="result-box success-box">
                    <p>Tu código secreto "<strong><?php echo htmlspecialchars($submitted_password); ?></strong>" es correcto.</p>
                    <p>Haz clic en el botón para unirte a la reunión:</p>
                    <a href="<?php echo htmlspecialchars($meet_link); ?>" target="_blank" rel="noopener noreferrer" class="meet-link">
                        🔗 Unirse a Google Meet
                    </a>
                </div>
                 <a href="index.php" class="back-link">← Volver</a> </main>
        <?php else: ?>
            <header>
                 <h1>❌ ¡Acceso Denegado!</h1>
            </header>
             <main>
                <div class="result-box error-box">
                    <p>El código secreto que has introducido no es válido o está vacío.</p>
                    <p>Por favor, inténtalo de nuevo.</p>
                </div>
                 <a href="index.php" class="back-link">← Intentar de nuevo</a> </main>
        <?php endif; ?>
         <footer>
            <p>&copy; <?php echo date("Y"); ?> Tu Organización Secreta 😉</p>
        </footer>
    </div>
</body>
</html>