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
        $transport = (new Swift_SmtpTransport('smtp.office365.com', 587, 'tls'))
            ->setUsername('traslado_almonedas@ooutlook.com')
            ->setPassword('UEHXC-BD7AU-3FUAT-J5LL3-VQJ7B'); // Usa una App Password si tienes 2FA
    
        // Crear el Mailer usando el transporte
        $mailer = new Swift_Mailer($transport);
    
        // Crear mensaje
        $message = (new Swift_Message('TRASLADO DE ALMONEDA'))
            ->setFrom(['traslado_almonedas@ooutlook.com' => 'Sistema de Traslados de Almoneda'])
            ->setTo(['rbravo69@gmail.com' => 'Destinatario'])
            ->setBody('Se ha enviado un correo desde el sistema de traslados, enviando el PDF para la generacion de la carta porte.')
            ->attach(Swift_Attachment::fromPath($ruta_archivo)); // Usar ruta absoluta
    
        // Enviar correo
        try {
            $result = $mailer->send($message);
            if ($result) {
                return "Correo enviado correctamente.";
            } else {
                return "Error al enviar el correo: No se pudo enviar el mensaje.";
            }
        } catch (Swift_TransportException $e) {
            return "Error de transporte al enviar el correo: " . $e->getMessage();
        } catch (Exception $e) {
            return "Error al enviar el correo: " . $e->getMessage();
        }
    }
    
?>