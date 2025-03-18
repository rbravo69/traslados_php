<?php
    require_once 'vendor/autoload.php';
    function enviarCorreo($archivo)
    {
        $ruta_archivo = __DIR__ . "/documents/" . $archivo; // Ruta absoluta
    
        // Verificar si el archivo existe antes de enviarlo
        if (!file_exists($ruta_archivo)) {
            die("Error: El archivo no existe en la ruta: $ruta_archivo");
        }
    
        // Configuración del transporte SMTP
        $transport = (new Swift_SmtpTransport('smtp.gmail.com', 587, 'tls'))
            ->setUsername('rbravo69@gmail.com')
            ->setPassword('ffke czcr arhj ypbz'); // Usa una App Password si tienes 2FA
    
        // Crear el Mailer usando el transporte
        $mailer = new Swift_Mailer($transport);
    
        // Crear mensaje
        $message = (new Swift_Message('TRASLADO DE ALMONEDA'))
            ->setFrom(['rbravo69@gmail.com' => 'Sistema de Traslados'])
            ->setTo(['rabraso@outlook.com' => 'Destinatario'])
            ->setBody('Se ha enviado un correo desde el sistema de traslados.')
            ->attach(Swift_Attachment::fromPath($ruta_archivo)); // Usar ruta absoluta
    
        // Enviar correo
        try {
            $result = $mailer->send($message);
            echo "Correo enviado correctamente.";
        } catch (Exception $e) {
            echo "Error al enviar el correo: " . $e->getMessage();
        }
    }
    
?>