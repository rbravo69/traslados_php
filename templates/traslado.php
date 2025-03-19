<?php
    require_once __DIR__ . '/../includes/auth.php'; // Verifica si el usuario está logueado
    requireLogin(); // Si no está autenticado, redirige al login


    require_once __DIR__ . '/../includes/functions.php'; 
    require_once __DIR__ .  '/../includes/create_logic.php'; 
    require_once __DIR__ . '/../includes/header.php';

?>

<div class="container mx-auto px-4 py-8">
    <div class="flex justify-center">
        <div class="w-full max-w-4xl">
            <div class="bg-white shadow-2xl rounded-xl overflow-hidden border-t-4 border-indigo-600">
                <div class="bg-indigo-600 text-white py-4 px-6">
                    <h2 class="text-3xl font-extrabold text-center tracking-wide">Crear Nuevo Traslado</h2>
                </div>

                <!-- Sección de notificaciones -->
                <?php if (!empty($success)): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <strong class="font-bold">Éxito!</strong>
                        <span class="block sm:inline"><?php echo nl2br(htmlspecialchars($success)); ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <strong class="font-bold">Error!</strong>
                        <span class="block sm:inline">
                            <?php foreach ($errors as $error): ?>
                                <p><?php echo htmlspecialchars($error); ?></p>
                            <?php endforeach; ?>
                        </span>
                    </div>
                <?php endif; ?>

                <form method="POST" action="traslado.php" class="p-6 md:p-8 space-y-6" id="trasladoForm">
                    <!-- Token CSRF -->
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken(); ?>">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Empresa -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Empresa</label>
                            <select name="empresa_id" class="w-full border rounded p-2" required>
                                <option value="">Seleccione una empresa</option>
                                <?php foreach ($empresas as $empresa): ?>
                                    <option value="<?= $empresa['id'] ?>"><?= htmlspecialchars($empresa['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Fecha de Traslado -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Fecha del Traslado</label>
                            <input type="date" name="fecha_traslado" class="w-full border rounded p-2" required>
                        </div>

                        <!-- Código de Seguridad -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Código de Seguridad</label>
                            <input type="text" name="codigo_seguridad" class="w-full border rounded p-2">
                        </div>

                        <!-- Vehículo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Vehículo</label>
                            <select name="vehiculo_id" class="w-full border rounded p-2" required>
                                <option value="">Seleccione un vehículo</option>
                                <?php foreach ($vehiculos as $vehiculo): ?>
                                    <option value="<?= $vehiculo['id'] ?>"><?= htmlspecialchars($vehiculo['modelo'] . " - " . $vehiculo['color']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Personal -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Personal de Traslado</label>
                            <select name="personal_id" class="w-full border rounded p-2" required>
                                <option value="">Seleccione un personal</option>
                                <?php foreach ($personal as $persona): ?>
                                    <option value="<?= $persona['id'] ?>"><?= htmlspecialchars($persona['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Sucursal de Origen -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Sucursal de Origen</label>
                            <select name="sucursal_origen_id" class="w-full border rounded p-2" required>
                                <?php if($_SESSION["sucursal_id"]==1){
                                   echo '<option value="">Seleccione una sucursal</option>';
                                }
                                ?>
                                <?php foreach ($sucursales_origen as $sucursal): ?>
                                    <option value="<?= $sucursal['id'] ?>"><?= htmlspecialchars($sucursal['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Sucursal de Destino -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Sucursal de Destino</label>
                            <select name="sucursal_destino_id" class="w-full border rounded p-2" required>
                                <option value="">Seleccione una sucursal</option>
                                <?php foreach ($sucursales_destino as $sucursal): ?>
                                    <option value="<?= $sucursal['id'] ?>"><?= htmlspecialchars($sucursal['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <!-- Sección de Folios usando HTMX con MySQL -->
                        <div class="form-group mt-6">
                            <label for="folios_almonedas" class="block text-sm font-medium text-gray-700 mb-2">Folios de Almonedas</label>
                            
                            <!-- Campo para ingresar los folios -->
                            <input type="text" name="folios_almonedas" id="folios_almonedas"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                                placeholder="Ingrese los folios separados por coma"
                                hx-get="/../includes/buscar_folios_mysql.php"
                                hx-trigger="keyup changed delay:500ms"
                                hx-target="#tablaFolios"
                                hx-indicator="#loadingIndicator"
                                hx-include="[name='folios_almonedas']"
                                autocomplete="off">


                            
                            <!-- Indicador de carga -->
                            <div id="loadingIndicator" class="text-center mt-2" style="display: none;">
                                <span class="text-gray-600">Cargando desde MySQL...</span>
                            </div>

                            <!-- Tabla donde se mostrarán los folios -->
                            <div class="overflow-x-auto mt-2">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase bg-indigo-600">Folio</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase bg-indigo-600">Cantidad</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase bg-indigo-600">Precio Unitario</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase bg-indigo-600">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tablaFolios">
                                        <!-- Aquí HTMX insertará los datos dinámicamente con PHP -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    <div class="mt-4">
                        <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded">
                            Guardar Traslado
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function limpiarCampos() {
    if (<?php if (!empty($success))  ?>) {
        document.getElementById('trasladoForm').reset();
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
