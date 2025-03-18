<?php
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

require 'vendor/autoload.php';

// Configurar el transporte SMTP de Gmail
$transport = Transport::fromDsn('smtp://trasladosalmonedas7@gmail.com:rchmnuwyjjnckqgn@smtp.gmail.com:587');

// Crear el objeto Mailer
$mailer = new Mailer($transport);

// Crear el correo
$email = (new Email())
    ->from('trasladosalmonedas7@gmail.com')
    ->to('rabraso@outlook.com')
    ->subject('Correo con Symfony Mailer')
    ->html('<h1>Hola, este es un correo de prueba</h1>');

// Enviar el correo
$mailer->send($email);

echo 'Correo enviado correctamente con Symfony Mailer.';

?>