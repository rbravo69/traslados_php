<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Conexión a SQLite (para autenticación y gestión de sucursales)
define('DB_SQLITE', __DIR__ . '/db/database.sqlite'); // Corrige la ruta aquí
try {
    $sqlite = new PDO("sqlite:" . DB_SQLITE);
    $sqlite->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error al conectar con SQLite: " . $e->getMessage());
}


    ?>
