<?php
require_once __DIR__ .'/../config.php';
require_once __DIR__ .'/functions.php'; // Agregamos las funciones de seguridad

require_once __DIR__ .'/auth.php'; // Verifica si el usuario está logueado
requireLogin(); // Si no está autenticado, redirige al login
include  __DIR__ .'/../generate_pdf.php'; // incluir el generate_pdf.php
require_once __DIR__. '/../send_mail.php'; // incluir el send_mail.php

// Obtener datos necesarios
$empresas = $sqlite->query("SELECT * FROM empresas")->fetchAll(PDO::FETCH_ASSOC);
$vehiculos = $sqlite->query("SELECT * FROM vehiculos")->fetchAll(PDO::FETCH_ASSOC);
$personal = $sqlite->query("SELECT * FROM personal_traslados")->fetchAll(PDO::FETCH_ASSOC);


// Obtener la sucursal del usuario
$sucursal_usuario = $_SESSION['sucursal_id'];

// 🔹 Obtener la sucursal de origen (Solo su sucursal si no es Admin)
if ($sucursal_usuario != 1) {
    $stmt = $sqlite->prepare("SELECT * FROM sucursales WHERE id = ?");
    $stmt->execute([$sucursal_usuario]);
} else {
    // Si pertenece a la sucursal 1 (Admin), puede ver todas
    $stmt = $sqlite->query("SELECT * FROM sucursales");
}
$sucursales_origen = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 🔹 Obtener las sucursales de destino (Todas menos la de origen)
if($sucursal_usuario == 1){
    $stmt = $sqlite->query("SELECT * FROM sucursales");
} else {
    $stmt = $sqlite->prepare("SELECT * FROM sucursales WHERE id != ?");
    $stmt->execute([$sucursal_usuario]);
}
$sucursales_destino = $stmt->fetchAll(PDO::FETCH_ASSOC);


$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Verificar el token CSRF antes de procesar la solicitud
    if (!verifyCSRFToken($_POST['csrf_token'])) {
        die("Error: Token CSRF inválido.");
    }

    // Obtener valores y sanitizar entradas
    $empresa_id = sanitizeInput($_POST['empresa_id'] ?? '');
    $fecha_traslado = sanitizeInput($_POST['fecha_traslado'] ?? '');
    $codigo_seguridad = sanitizeInput($_POST['codigo_seguridad'] ?? '');
    $vehiculo_id = sanitizeInput($_POST['vehiculo_id'] ?? '');
    $personal_id = sanitizeInput($_POST['personal_id'] ?? '');
    $sucursal_origen_id = sanitizeInput($_POST['sucursal_origen_id'] ?? '');
    $sucursal_destino_id = sanitizeInput($_POST['sucursal_destino_id'] ?? '');
    $folios_almonedas = sanitizeInput($_POST['folios_almonedas'] ?? '');
    $total_cantidad = sanitizeInput($_POST['total_cantidad'] ?? '');
    $total_precio = sanitizeInput($_POST['total_precio'] ?? '');
    $total_total = sanitizeInput($_POST['total_total'] ?? '');

    // Validar campos obligatorios
    if (empty($empresa_id) || empty($fecha_traslado) || empty($vehiculo_id) || empty($personal_id) || empty($sucursal_origen_id) || empty($sucursal_destino_id)) {
        $errors[] = "Todos los campos son obligatorios.";
    }

    // Si no hay errores, guardar en la base de datos
    if (empty($errors)) {
        $stmt = $sqlite->prepare("INSERT INTO traslados (empresa_id, fecha_traslado, codigo_seguridad, vehiculo_id, personal_id, 
        sucursal_origen_id, sucursal_destino_id, folios_almonedas, cantidad_almonedas, precio_unitario_almonedas, total_almonedas) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        if ($stmt->execute([$empresa_id, $fecha_traslado, $codigo_seguridad, 
        $vehiculo_id, $personal_id, $sucursal_origen_id, $sucursal_destino_id, 
        $folios_almonedas, $total_cantidad, $total_precio, $total_total])) {

            // Obtener el ID del traslado recién insertado
            $traslado_id = $sqlite->lastInsertId();

            // Generar el PDF después de guardar el traslado
            $nombre_pdf = generarYGuardarPDF($traslado_id, $empresa_id, 
            $fecha_traslado, $sucursal_origen_id, $sucursal_destino_id, 
            $folios_almonedas, $total_cantidad, $total_total);


            $registro = "Traslado registrado correctamente.\n PDF generado: $nombre_pdf";
         
            // Enviar correo electrónico con el PDF adjunto
           $resultado =  enviarCorreo( $nombre_pdf);
           $success = $registro."\n" . $resultado;
           $_SESSION['mensaje'] = $success;
           $_SESSION['tipo_mensaje'] = "success"; // 'success' o 'error'
            //🔹 Redirigir para limpiar los campos del formulario
            header("Location: ".$_SERVER['PHP_SELF']);
            exit;
        } else {
            $_SESSION["mensaje"] = "Error al guardar el traslado.";
            $_SESSION["tipo_mensaje"] = "error"; // 'success' o 'error'
            $errors[] = "Error al guardar el traslado.";
        }

    }
}
?>
