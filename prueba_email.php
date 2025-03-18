<?php
    require_once 'vendor/autoload.php';

    // Configuración del transporte SMTP
    $transport = (new Swift_SmtpTransport('smtp.gmail.com', 587, 'tls'))
        ->setUsername('rbravo69@gmail.com')
        ->setPassword('ffke czcr arhj ypbz'); // Usa una App Password si tienes 2FA
    
    // Crear el Mailer usando el transporte
    $mailer = new Swift_Mailer($transport);
    
    // Crear mensaje
    $message = (new Swift_Message('prueba de correo'))
        ->setFrom(['rbravo69@gmail.com' => 'Sistema de Traslados'])
        ->setTo(['sedem@gruposedem.com.mx' => 'Destinatario'])
        ->setBody('Este es el cuerpo del mensaje.')
        ->attach(Swift_Attachment::fromPath('./documents/traslado_almoneda-Uman-20250317.pdf'));
    
    // Enviar correo
    $result = $mailer->send($message);
    
    if ($result) {
        echo "Correo enviado correctamente.";
    } else {
        echo "Error al enviar el correo.";
    }
    


?>