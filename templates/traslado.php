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
                <?= $_SESSION['ip_servidor'].'- '. $_SESSION['sucursal'] ; ?>

            <!--Formulario de Traslado-->
                <div x-data="{ cargando: false }">
                <form method="POST" 
                    action="traslado.php" 
                    class="p-6 md:p-8 space-y-6" 
                    id="trasladoForm"
                    @submit="cargando = true">
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
                    <!-- Sección de Folios usando Alpine con Mysql -->
                    <div x-data="foliosAlmonedas()" x-init="init()" x-id="foliosAlmonedas" >
                        <div class="form-group mt-6">
                            <label for="folios_almonedas" class="block text-sm font-medium text-gray-700 mb-2">Folios de Almonedas</label>

                            <!-- Input de búsqueda -->
                            <input 
                                type="text" 
                                name="folios_almonedas" 
                                id="folios_almonedas"
                                x-model="folioInput"
                                @input.debounce.500ms="buscarFolios()"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                                placeholder="Ingrese los folios separados por coma ejemplo: 123, 456, 789"
                                autocomplete="off">

                            <!-- 🔄 Indicador de carga (oculta la tabla mientras se cargan datos) -->
                            <div x-show="!cargandoFolios" class="text-center mt-4 text-indigo-600">
                                <div class="inline-flex items-center space-x-2">
                                    <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                                    </svg>
                                    <span>Cargando folios de almonedas...</span>
                                </div>
                            </div>

                            <!-- Tabla de resultados -->
                            <div class="overflow-x-auto mt-4" x-show="cargandoFolios">
                                <template x-if="folios.length > 0">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead>
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase bg-indigo-600">Folio</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase bg-indigo-600">Cantidad</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase bg-indigo-600">Precio Unitario</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase bg-indigo-600">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template x-for="folio in folios" :key="folio.folio">
                                                <tr>
                                                    <td class="px-6 py-2" x-text="folio.folio"></td>
                                                    <td class="px-6 py-2" x-text="folio.cantidad"></td>
                                                    <td class="px-6 py-2" x-text="folio.precio_unitario"></td>
                                                    <td class="px-6 py-2" x-text="folio.total"></td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </template>

                                <!-- Mensaje si no hay resultados -->
                                <template x-if="!folios.length && folioInput.trim()">
                                    <div class="text-center text-gray-500 mt-4">
                                        No se encontraron folios válidos.
                                    </div>
                                </template>
                            </div>
    </div>
</div>


                    <!-- Botón para guardar el traslado -->
                        <div class="mt-4">
                        <button 
                            type="submit"
                            class="w-full px-4 py-2 rounded transition-colors duration-200"
                            :class="cargando ? 'bg-gray-400 cursor-not-allowed' : 'bg-blue-500 hover:bg-blue-600 text-white'"
                            :disabled="cargando">
                            <template x-if="!cargando">Guardar Traslado</template>
                            <template x-if="cargando">Procesando...</template>
                        </button>
                        </div>
                </form>
                <div x-show="cargando"
                    x-transition
                    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">

                    <div class="text-center bg-white p-6 rounded-lg shadow-lg">
                        <svg class="w-8 h-8 mx-auto text-blue-600 animate-spin mb-4" 
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" 
                                    stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" 
                                d="M4 12a8 8 0 018-8v8z"></path>
                        </svg>
                        <p class="text-gray-800 text-sm">Guardando traslado, por favor espera...</p>
                    </div>

                </div>
            </div>
            </div>
        </div>
    </div>
</div>

<script>
  <?php
    if (isset($_SESSION['mensaje'])) {
        $mensaje = $_SESSION['mensaje'];
        $tipoMensaje = $_SESSION['tipo_mensaje'];
        echo "toastr.$tipoMensaje('$mensaje');"; // Muestra la notificación con el tipo correcto (success/error)
        unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']); // Eliminar el mensaje después de mostrarlo
    }
    ?>
</script>
<script>
function foliosAlmonedas() {
    return {
        folioInput: '',
        folios: [],
        cargandoFolios: false,
        cargando: false, // Asegúrate de inicializar cargando como false
        init() {},
        buscarFolios() {
            if (!this.folioInput.trim()) {
                this.folios = [];
                return;
            }
            console.log(this.folioInput);
            
            this.cargandoFolios = true;
            fetch('./../includes/buscar_folios_mysql.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ folios: this.folioInput })
            })
            .then(res => res.json())
            .then(data => {
                // Verifica si la respuesta es un array
                if (Array.isArray(data)) {
                    this.folios = data;
                } else {
                    console.error('La respuesta no es un array:', data);
                    this.folios = [];
                }
                console.log(data);
            })
            .catch(error => {
                console.error('Error al buscar folios:', error);
                this.folios = [];
            })
            .finally(() => {
                this.cargandoFolios = false;
            });
        }
    };
}
</script>


<?php require_once __DIR__ . '/../includes/footer.php'; ?>


