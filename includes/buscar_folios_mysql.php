<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'auth.php';
requireLogin();

$mysql_host = $_SESSION['ip_servidor'] ?? 'localhost';
$mysql_user = 'root';
$mysql_pass = 'admin';
$mysql_db = 'basedatos';
$mysql_port = 3307;

try {
    $mysql = new PDO("mysql:host=$mysql_host;port=$mysql_port;dbname=$mysql_db;charset=utf8", $mysql_user, $mysql_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error de conexión: ' . $e->getMessage()]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$folioTexto = $input['folios'] ?? '';

if (empty($folioTexto)) {
    echo json_encode(['error' => 'No se recibieron folios']);
    exit;
}

try {
    $folioNumeros = array_map('intval', array_filter(array_map('trim', explode(',', $folioTexto))));

    if (empty($folioNumeros)) {
        echo json_encode(['error' => 'Folios vacíos o inválidos']);
        exit;
    }

    $placeholders = implode(',', array_fill(0, count($folioNumeros), '?'));

    $query = "
        SELECT 
            ei.Folio AS folio,
            COALESCE(SUM(di.pesoPiedras) + SUM(di.peso), 0) AS cantidad,
            COALESCE(SUM(di.avaluo) / NULLIF((SUM(di.pesoPiedras) + SUM(di.peso)), 0), 0) AS precio_unitario,
            COALESCE(SUM(di.avaluo), 0) AS total
        FROM basedatos.entradainventario ei
        LEFT JOIN basedatos.detallesentradainventario di 
            ON ei.ID = di.IDEntrada OR ei.ID = di.ContratoPrincipal
        WHERE ei.Folio IN ($placeholders)
        
        GROUP BY ei.Folio
    ";

    $stmt = $mysql->prepare($query);
    $stmt->execute($folioNumeros);
    $folios = $stmt->fetchAll();

    if (empty($folios)) {
        echo json_encode(['error' => 'No se encontraron folios']);
        exit;
    }

    echo json_encode([
        'folios' => $folios,
        'query' => $query
    ]);
} catch (Exception $e) {
    echo json_encode(['error' => 'Error en la consulta: ' . $e->getMessage()]);
} finally {
    $mysql = null;
}
