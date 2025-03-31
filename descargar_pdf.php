<?php
session_start();

// Validar que se haya generado el archivo
if (!isset($_SESSION['archivo_pdf'])) {
    die("No se encontró el archivo para descargar.");
}

$nombreArchivo = $_SESSION['archivo_pdf'];
$rutaArchivo = __DIR__ . "/documents/" . $nombreArchivo;
if (!file_exists($rutaArchivo)) {
    die("El archivo no existe.");
}

unset($_SESSION['archivo_pdf']); // Limpiar la variable de sesión después de usarla

// Forzar descarga
header("Content-Type: application/pdf");
header("Content-Disposition: attachment; filename=\"$nombreArchivo\"");
header("Content-Length: " . filesize($rutaArchivo));
readfile($rutaArchivo);
exit;


?>