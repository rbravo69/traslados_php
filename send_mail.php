<?php
// Incluir lógica de autenticación
require_once __DIR__ . '/includes/auth.php';
requireLogin();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/phpmailer/src/Exception.php';
require 'vendor/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/src/SMTP.php';

$mail = new PHPMailer(true);

$archivo_pdf = pathinfo($_FILES['archivo']['name'], PATHINFO_EXTENSION);

try {
    // Configuración del servidor SMTP de Outlook
    $mail->isSMTP();
    $mail->Host = 'smtp.office365.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'traslado_almonedas@outlook.com';
    $mail->Password = 'traslados';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // Configuración del correo
    $mail->setFrom('traslado_almonedas@outlook.com', 'sistema de traslados de almoneda');
    $mail->addAddress('destino@gmail.com');

    // Adjuntar archivo PDF
    $mail->addAttachment('./documents/', $archivo_pdf);

    $mail->isHTML(true);
    $mail->Subject = 'Reporte de Equipos';
    $mail->Body = 'Este es un mensaje de prueba.';

    $mail->send();
    echo 'Correo enviado';
} catch (Exception $e) {
    echo "Error al enviar correo: {$mail->ErrorInfo}";
}
?>
