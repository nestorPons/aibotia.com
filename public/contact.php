<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

// Ocultar errores visibles en producción
ini_set('display_errors', 0);

// Cargar variables de entorno
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Honeypot (campo invisible para bots)
    if (!empty($_POST['telefono'])) {
        die("⛔ Acceso no permitido.");
    }

    // Recoger y sanitizar campos
    $nombre = trim(htmlspecialchars($_POST["nombre"] ?? ""));
    $email = trim($_POST["email"] ?? "");
    $mensaje = trim(htmlspecialchars($_POST["mensaje"] ?? ""));

    // Validaciones básicas
    if (empty($nombre) || empty($email) || empty($mensaje)) {
        die("❌ Todos los campos son obligatorios.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("❌ Correo no válido.");
    }

    // Rate-limiting por IP (máx. 1 envío por minuto)
    $ip = $_SERVER['REMOTE_ADDR'];
    $ipFile = sys_get_temp_dir() . "/form_" . md5($ip);
    if (file_exists($ipFile) && time() - filemtime($ipFile) < 60) {
        die("⏳ Espera 1 minuto antes de volver a enviar.");
    }
    touch($ipFile); // actualiza el tiempo

    // Preparar PHPMailer
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['SMTP_USERNAME'];
        $mail->Password   = $_ENV['SMTP_PASSWORD'];
        $mail->SMTPSecure = $_ENV['SMTP_SECURE'];
        $mail->Port       = $_ENV['SMTP_PORT'];

        $mail->setFrom($_ENV['SMTP_USERNAME'], 'Formulario de Contacto');
        $mail->addAddress($_ENV['SMTP_USERNAME']);

        $mail->isHTML(true);
        $mail->Subject = "Nuevo mensaje de contacto";
        $mail->Body    = "<strong>Nombre:</strong> $nombre<br>
                          <strong>Correo:</strong> $email<br>
                          <strong>Mensaje:</strong><br>$mensaje";

        $mail->send();
        echo "✅ Mensaje enviado correctamente.";
    } catch (Exception $e) {
        // Log del error, pero sin mostrar al usuario
        file_put_contents(__DIR__ . '/../logs/contact_error.log', date('c') . " - {$mail->ErrorInfo}\n", FILE_APPEND);
        echo "❌ Error al enviar el mensaje. Inténtalo más tarde.";
    }
}
?>
