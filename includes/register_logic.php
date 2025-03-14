<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/functions.php';

// Obtener sucursales
$sucursales = $sqlite->query("SELECT * FROM sucursales")->fetchAll(PDO::FETCH_ASSOC);

$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = sanitizeInput($_POST['nombre']);
    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = sanitizeInput($_POST['confirm_password']);
    $sucursal_id = sanitizeInput($_POST['sucursal']);

    if ($password !== $confirm_password) {
        $errors[] = "Las contraseñas no coinciden.";
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $sqlite->prepare("INSERT INTO users (name, username, password, sucursal_id) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$nombre, $username, $hashed_password, $sucursal_id])) {
            $success = "Registro exitoso.";
            header("Location: ../login.php");
            exit;
        } else {
            $errors[] = "Error al registrar usuario.";
        }
    }
}
?>
