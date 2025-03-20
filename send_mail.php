<?php
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

require 'vendor/autoload.php';

function enviarCorreo($archivo)
{
    $ruta_archivo = __DIR__ . "/documents/" . $archivo; // Ruta absoluta

    // Verificar si el archivo existe antes de enviarlo
    if (!file_exists($ruta_archivo)) {
        return "Error: El archivo no existe en la ruta: $ruta_archivo";
    }

    // Configurar el transporte SMTP de Gmail
    $transport = Transport::fromDsn('smtp://trasladosalmonedas7@gmail.com:rchmnuwyjjnckqgn@smtp.gmail.com:587');

    // Crear el objeto Mailer
    $mailer = new Mailer($transport);

    // Crear el correo
    $email = (new Email())
        ->from('trasladosalmonedas7@gmail.com')
        ->to('rabraso@outlook.com')
        ->subject('TRASLADO DE ALMONEDA')
        ->html('<p>Se ha enviado un correo desde el sistema de traslados,\n enviando el PDF para la generacion de la carta porte.</p>')
        ->attachFromPath($ruta_archivo);

    // Enviar el correo
    try {
        $mailer->send($email);
        return "Correo enviado correctamente.";
    } catch (TransportExceptionInterface $e) {
        return "Error al enviar el correo: " . $e->getMessage();
    }
}
?>