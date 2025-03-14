<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Verificar si el usuario está autenticado
function isLoggedIn() {
    return isset( $_SESSION['usuario']);
}

// Redirigir si no está autenticado
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: ../login.php");
        exit;
    }
}

// Cerrar sesión
function logout() {
    session_destroy();
    header("Location: ../login.php");
    exit;
}
?>
