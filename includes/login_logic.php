<?php
require_once __DIR__ . '/../config.php'; 
require_once __DIR__ . '/functions.php';

$errors = [];

//Verificar si una sesión ya está activa
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitizar entradas
    $username = sanitizeInput($_POST['username']);
    $password = sanitizeInput($_POST['password']);

    // Validar entrada (evitar XSS)
    $username = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');

    // Verificar que los campos no estén vacíos
    if (empty($username) || empty($password)) {
        $errors[] = "Usuario y contraseña son obligatorios.";
    } else {
        // Consulta segura con PDO (evita SQL Injection)
        $stmt = $sqlite->prepare("
            SELECT u.*, s.IP as ip_servidor, s.nombre AS sucursal 
            FROM users u
            JOIN sucursales s ON u.sucursal_id = s.id
            WHERE u.username = :username
        ");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar contraseña
        if ($user) {
            if (password_verify($password, $user['password'])) {
                //Iniciar sesión de manera segura
                session_regenerate_id(true);
                $_SESSION['usuario'] = $user['username'];
                $_SESSION['nombre'] = $user['name'];
                $_SESSION['sucursal'] = $user['sucursal'];
                $_SESSION['sucursal_id'] = $user['sucursal_id'];
                $_SESSION['ip_servidor'] = $user['ip_servidor']; // Guardamos la IP del servidor MySQL

                header("Location: ../index.php");
                exit;
            } else {
                $errors[] = "Contraseña incorrecta.";
            }
        } else {
            $errors[] = "Usuario no encontrado.";
        }
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: ../login.php");
        exit;
    }
}
?>

