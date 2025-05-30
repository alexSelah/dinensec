<?php
// send_email.php

// --- Configuración ---
$recipient_email = "info@dinensec.com"; // Email donde recibirás los mensajes
$subject_prefix = "[Dinensec Contacto]"; // Prefijo para el asunto del email

// --- Procesamiento del Formulario ---

// 1. Verificar si se envió el formulario usando el método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 2. Recoger y sanear los datos del formulario
    // htmlspecialchars() previene ataques XSS básicos.
    $name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $company = isset($_POST['company']) ? htmlspecialchars(trim($_POST['company'])) : ''; // Campo opcional
    $message = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message'])) : '';

    // 3. Validación básica
    $errors = [];
    if (empty($name)) {
        $errors[] = "El nombre es obligatorio.";
    }
    if (empty($email)) {
        $errors[] = "El correo electrónico es obligatorio.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Validar formato del email
        $errors[] = "El formato del correo electrónico no es válido.";
    }
    if (empty($message)) {
        $errors[] = "El mensaje es obligatorio.";
    }

    // 4. Si no hay errores de validación, intentar enviar el email
    if (empty($errors)) {
        // Construir el asunto del email
        $subject = "$subject_prefix Nuevo mensaje de $name";

        // Construir el cuerpo del email
        $body = "Has recibido un nuevo mensaje desde el formulario de contacto de Dinensec:\n\n";
        $body .= "Nombre: " . $name . "\n";
        $body .= "Email: " . $email . "\n";
        if (!empty($company)) { // Incluir empresa solo si se proporcionó
            $body .= "Empresa: " . $company . "\n";
        }
        $body .= "Mensaje:\n" . $message . "\n";

        // Construir las cabeceras del email
        // Es crucial sanear $email antes de usarlo en cabeceras para evitar Email Header Injection
        $sanitized_email = filter_var($email, FILTER_SANITIZE_EMAIL);
        if ($sanitized_email === $email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $headers = "From: " . $name . " <" . $sanitized_email . ">\r\n";
            $headers .= "Reply-To: " . $sanitized_email . "\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion();
        } else {
            // Si el email no es válido después de sanear, no se puede usar en cabeceras.
            // Podrías poner un email por defecto o manejar el error.
            // Por seguridad, si el email no es válido, no enviamos o usamos un 'From' genérico.
            // Aquí optamos por no enviar y redirigir con error.
            header("Location: index.html?status=error_invalid_email#contacto");
            exit;
            // Alternativa: Usar un 'From' genérico (menos ideal para responder)
            // $headers = "From: noreply@dinensec.com\r\n";
            // $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
            // $headers .= "X-Mailer: PHP/" . phpversion();
        }


        // Enviar el email usando la función mail() de PHP
        // ¡¡¡ ADVERTENCIA: mail() puede no ser fiable en todos los hostings !!!
        if (mail($recipient_email, $subject, $body, $headers)) {
            // Éxito: Redirigir a la página de origen con parámetro de éxito
            header("Location: index.html?status=success#contacto");
            exit;
        } else {
            // Error al enviar: Redirigir con parámetro de error
            header("Location: index.html?status=error_send#contacto");
            exit;
        }

    } else {
        // Errores de validación: Redirigir con parámetro de error
        // Podrías pasar los errores específicos si quisieras mostrarlos,
        // pero por simplicidad, solo indicamos un error general de validación.
        header("Location: index.html?status=error_validation#contacto");
        exit;
    }

} else {
    // Si alguien intenta acceder al script directamente sin método POST
    echo "Acceso no permitido.";
    exit;
}
?>
