<?php
// Incluir lógica de autenticación
require_once __DIR__ . '/includes/auth.php';
requireLogin();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';
//incluir los archivos necesarios para enviar el correo
 require __DIR__ . '/vendor/phpmailer/phpmailer/src/PHPMailer.php';
 require __DIR__ . '/vendor/phpmailer/phpmailer/src/Exception.php';
 require __DIR__ . '/vendor/phpmailer/phpmailer/src/SMTP.php';
function enviarCorreo($nombre_pdf) {
    $mail = new PHPMailer(true);

    try {
        // Habilitar depuración de PHPMailer
        $mail->SMTPDebug = 2;  // 0 = Off, 1 = Mensajes, 2 = Detalles
        $mail->Debugoutput = 'html'; // Muestra la salida en formato HTML

        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.office365.com'; // Outlook
        $mail->SMTPAuth = true;
        $mail->Username = 'traslado_almonedas@outlook.com';
        $mail->Password = 'traslados'; // Asegúrate de que es correcto
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // TLS obligatorio en Outlook
        $mail->Port = 587;

        // Configuración del correo
        $mail->setFrom('traslado_almonedas@outlook.com', 'Sistema de Traslados');
        $mail->addAddress('rbravo69@gmail.com');

        // Adjuntar PDF
        $ruta_pdf = __DIR__ . '/../documentos/' . $nombre_pdf;
        if (file_exists($ruta_pdf)) {
            $mail->addAttachment($ruta_pdf);
        } else {
            echo "Error: No se encontró el archivo PDF en $ruta_pdf";
            return;
        }

        $mail->isHTML(true);
        $mail->Subject = 'Reporte de Traslado';
        $mail->Body = '<p>Se adjunta el reporte de traslado.</p>';

        // Enviar el correo
        if ($mail->send()) {
            echo 'Correo enviado correctamente.';
        } else {
            echo 'Error al enviar correo: ' . $mail->ErrorInfo;
        }
    } catch (Exception $e) {
        echo "Excepción atrapada: {$mail->ErrorInfo}";
    }
}

?>
