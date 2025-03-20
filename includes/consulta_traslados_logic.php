<?php
require_once __DIR__ . '/auth.php';
// Verifica si el usuario está logueado
requireLogin();
require_once __DIR__ . '/../config.php';

if(isset($_SESSION['sucursal_id'])){
    $sucursal_usuario = $_SESSION['sucursal_id'];
} else {
    $sucursal_usuario = 1;
}
// Obtener filtros si existen
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : null;
$sucursal_origen_id = isset($_GET['sucursal_origen_id']) ? $_GET['sucursal_origen_id'] : $sucursal_usuario;

// Construir la consulta SQL con filtros dinámicos
$query = "SELECT 
    t.fecha_traslado, 
    so.nombre AS sucursal_origen, 
    sd.nombre AS sucursal_destino, 
    p.nombre AS personal, 
    e.nombre AS empresa, 
    v.modelo, v.color
FROM traslados t
JOIN sucursales so ON t.sucursal_origen_id = so.id
JOIN sucursales sd ON t.sucursal_destino_id = sd.id
JOIN personal_traslados p ON t.personal_id = p.id
JOIN empresas e ON t.empresa_id = e.id
JOIN vehiculos v ON t.vehiculo_id = v.id
WHERE 1=1";

// Aplicar filtros dinámicos
$params = [];
if ($fecha) {
    $query .= " AND t.fecha_traslado = ?";
    $params[] = $fecha;
}
if ($sucursal_origen_id) {
    $query .= " AND t.sucursal_origen_id = ?";
    $params[] = $sucursal_origen_id;
}

// Ordenar por fecha descendente
$query .= " ORDER BY t.fecha_traslado DESC";

// Ejecutar la consulta
$stmt = $sqlite->prepare($query);
$stmt->execute($params);
$traslados = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->closeCursor();

// Obtener sucursales para los filtros
$sucursales = $sqlite->query("SELECT * FROM sucursales")->fetchAll(PDO::FETCH_ASSOC);
$sqlite=null;
?>
