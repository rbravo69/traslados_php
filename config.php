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
$port=0;
if(isset($_SESSION['sucursal_id'])){
    if($_SESSION["sucursal_id"]==1){
        $port = 3306;
    } else {
        $port = 3307;
    }
} else {

}

if(isset($_SESSION['ip_servidor'])){
   // Si el usuario está logueado, obtener la IP del servidor MySQL según su sucursal
$mysql_host = isset($_SESSION['ip_servidor']) ? $_SESSION['ip_servidor'] : 'localhost';
$mysql_user = "root"; // Cambia si tienes otro usuario
$mysql_pass = "admin"; // Agrega la contraseña si aplica
$mysql_db = "basedatos"; // Nombre de la base de datos
$mysql_port = $port; // Puerto de MySQL

try {
    $mysql = new PDO("mysql:host=$mysql_host;port=$mysql_port;dbname=$mysql_db;charset=utf8", $mysql_user, $mysql_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Error al conectar con MySQL en $mysql_host: " . $e->getMessage());
}
} else {
    $sucursal = null;
}

?>
