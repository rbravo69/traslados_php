<?php 
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $base_url= "/traslados";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sistema Traslados</title>
    <link rel="icon" href="<?= $base_url ?>/assets/img/paloma_afavor.png" type="image/png">

    <!-- <link rel="stylesheet" href="/../assets/css/styles.css"> -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Tailwind CSS -->
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <!--HTMX-->
    <script src="https://unpkg.com/htmx.org@2.0.4" integrity="sha384-HGfztofotfshcF7+8n44JQL2oJmowVChPTg48S+jvZoztPfvwD79OC/LTtG6dMp+" 
    crossorigin="anonymous"></script>
    <!-- Toastr -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  
</head>
<body class="bg-gray-100">
    
    <!-- Navbar -->
    <nav class="bg-gradient-to-r from-green-200 to-green-600 border-b border-slate-100 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <!-- Logo -->
                    <div class="shrink-0 flex items-center">
                        <a href="<?=$base_url?>/index.php">
                            <img src="<?=$base_url?>/assets/img/afavor_transparent.png" alt="Logo" class="h-9 w-auto"> <!-- Agrega tu logo aquí -->
                        </a>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <a href="<?=$base_url?>/index.php" class="text-gray-800 dark:text-gray-200 hover:text-gray-400">Inicio</a>
                    </div>
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <a href="<?=$base_url?>/templates/traslado.php" 
                        class="text-gray-800 dark:text-gray-200 hover:text-gray-400">Traslados</a>
                    </div>
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <a href="<?=$base_url?>/templates/consultaTraslados.php" 
                        class="text-gray-800 dark:text-gray-200 hover:text-gray-400">Consultas</a>
                    </div>
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <a href="<?=$base_url?>/templates/dashboard.php" 
                        class="text-gray-800 dark:text-gray-200 hover:text-gray-400">Dashboard</a>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <!-- Si el usuario está autenticado -->
                    <?php if (isset($_SESSION['usuario'])){ ?>
                        <div class="flex items-center space-x-2 text-lime-300">
                            <i class="fas fa-user-circle text-xl"></i>
                            <span class="font-semibold"><?= htmlspecialchars($_SESSION['nombre'] ?? ''); ?></span>
                        </div>
                        <a href="<?=$base_url?>/logout.php" class="text-gray-800 dark:text-gray-200 hover:text-gray-400">Cerrar sesión</a>
                    <?php }else{?>
                        <!-- Si el usuario no está autenticado -->
                        <a href="<?=$base_url?>/login.php" class="text-gray-800 dark:text-gray-200 hover:text-gray-400">Iniciar sesión</a>
                    <?php }; ?>
                </div>
            </div>
        </div>
    </nav>
    <!-- End Navbar -->
