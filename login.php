<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="./assets/img/paloma_afavor.png" type="jpg/png" sizes="16x16">
        <title>Login Traslado</title>
   <!-- Tailwind CSS -->
   <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white shadow-lg rounded-2xl p-8 max-w-md w-w-md space-y-4">
        <img src="./assets/img/afavor.png" alt="logo" 
            class="w-40 mx-auto rounded-md shadow-md">
        <h2 class="text-2xl font-semibold text-gray-700 text-center mb-6">Entrada de Usuarios</h2>
        <form action="./includes/login_logic.php" method="POST" class="space-y-4">
            <div>
                <label for="username" class="block text-gray-600 font-medium">Nombre</label>
                <input type="text" id="username" name="username" 
                class="w-full px-4 py-2 border-2  rounded-lg  border-gray-400
                focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label for="password" class="block text-gray-600 font-medium">Contraseña</label>
                <input type="password" id="password" name="password" 
                class="w-full px-4 py-2 border-2 rounded-lg border-gray-400
                focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <button type="submit" 
            class="w-full bg-blue-500 text-white py-2 rounded-lg font-semibold hover:bg-blue-600 transition duration-300">Entrar</button>
        </form>
        <p class="text-gray-500 text-center mt-4">¿Ya tienes cuenta? <a href="./register.php" 
        class="bg-orange-500 hover:bg-orange-700 text-white px-2 py-1 rounded-lg font-semibold transition duration-300">Registro</a></p>
    </div>
</body>
</html>
