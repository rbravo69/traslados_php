<?php
require_once __DIR__ . '/includes/register_logic.php';



?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="./assets/img/paloma_afavor.png" type="jpg/png" sizes="32x32">
    <title>Registro</title>
      <!-- Tailwind CSS -->
   <script src="https://unpkg.com/@tailwindcss/browser@4"></script>

</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-lg shadow-lg">
        <div class="flex justify-center">
            <img src="./assets/img/afavor_transparent.png" alt="Logo" class="w-40 mx-auto rounded-md shadow-md">
        </div>
        <h2 class="text-2xl font-semibold text-center text-gray-700">Registro</h2>
        <form action="./includes/register_logic.php" method="POST" class="space-y-4">
            <div class="m-3">
                <label class="block text-sm font-medium text-gray-700">Usuario</label>
                <input type="text" name="username" required 
                class="w-full px-4 py-2 mt-1 border-2 border-gray-400 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div class="m-3">
                <label class="block text-sm font-medium text-gray-700">Nombre completo</label>
                <input type="text" name="nombre" required 
                class="w-full px-4 py-2 mt-1 border-2 border-gray-400 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
            <div class="m-3">
                <label class="block text-sm font-medium text-gray-700">Contraseña</label>
                <input type="password" name="password" required class="w-full px-4 py-2 mt-1 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div class="m-3">
                <label class="block text-sm font-medium text-gray-700">Confirmar Contraseña</label>
                <input type="password" name="confirm_password" required 
                class="w-full px-4 py-2 mt-1 border-2 border-gray-400 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div class="m-3">
                <label class="block text-sm font-medium text-gray-700">Sucursal</label>
                <select name="sucursal" required class="w-full px-4 py-2 mt-1 border-2 border-gray-400  rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">Seleccione una sucursal</option>
                   <?php foreach ($sucursales as $sucursal): ?>
                        <option value="<?= $sucursal['id'] ?>"><?= $sucursal['nombre'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="w-full py-2 text-white bg-blue-500 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400">
                Registrarse
            </button>
        </form>
    </div>
</body>
</html>
