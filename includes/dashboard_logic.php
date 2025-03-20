<?php
// Incluir lógica de autenticación
require_once __DIR__ . '/auth.php';
requireLogin();
require_once __DIR__ . '/../config.php';

if(isset($_SESSION["sucursal_id"])){
    $sucursal_id = $_SESSION["sucursal_id"];
}

// Obtener el total de traslados
$filtro = $sucursal_id !=1 ? "WHERE sucursal_origen_id = $sucursal_id" : "";
$query = "SELECT COUNT(*) AS totalTraslados FROM traslados $filtro";
$totalTraslados = $sqlite->query($query)->fetchColumn();

// Obtener el total de gramos transferidos
$query = "SELECT SUM(cantidad_almonedas) AS totalAlmonedas FROM traslados $filtro";
$totalAlmonedas = $sqlite->query($query)->fetchColumn();

// Obtener datos para los gráficos
$query = "SELECT strftime('%m', fecha_traslado) AS mes, COUNT(*) AS total FROM traslados $filtro GROUP BY mes ORDER BY mes";
$trasladosPorMes = $sqlite->query($query)->fetchAll(PDO::FETCH_KEY_PAIR);

$query = "SELECT strftime('%m', fecha_traslado) AS mes, SUM(cantidad_almonedas) AS total FROM traslados $filtro GROUP BY mes ORDER BY mes";
$totalAlmonedasPorMes = $sqlite->query($query)->fetchAll(PDO::FETCH_KEY_PAIR);

// Obtener detalles de traslados
$query = "SELECT 
    so.nombre AS sucursal_origen, 
    sd.nombre AS sucursal_destino, 
    t.cantidad_almonedas, 
    t.fecha_traslado, 
    t.codigo_seguridad
FROM traslados t
JOIN sucursales so ON t.sucursal_origen_id = so.id
JOIN sucursales sd ON t.sucursal_destino_id = sd.id
$filtro
ORDER BY t.fecha_traslado DESC";
$detalleTraslados = $sqlite->query($query)->fetchAll(PDO::FETCH_ASSOC);

// Obtener sucursales para filtros
$filtro = $sucursal_id != 1 ? "WHERE id = $sucursal_id" : "";
$query = "SELECT id, nombre FROM sucursales $filtro ";
$sucursales = $sqlite->query($query)->fetchAll(PDO::FETCH_ASSOC);
$sqlite=null;

?>
