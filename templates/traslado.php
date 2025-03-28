<?php
    require_once __DIR__ . '/../includes/auth.php';
    requireLogin();
    require_once __DIR__ . '/../includes/functions.php'; 
    require_once __DIR__ . '/../includes/create_logic.php'; 
    require_once __DIR__ . '/../includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="flex justify-center">
        <div class="w-full max-w-4xl">
            <div class="bg-white shadow-2xl rounded-xl overflow-hidden border-t-4 border-indigo-600">
                <div class="bg-indigo-600 text-white py-4 px-6">
                    <h2 class="text-3xl font-extrabold text-center tracking-wide">Crear Nuevo Traslado</h2>
                </div>
                <?= $_SESSION['ip_servidor'] . ' - ' . $_SESSION['sucursal']; ?>

                <!-- Alpine unificado -->
                <div x-data="foliosAlmonedas()">
                    <form method="POST" action="traslado.php" class="p-6 md:p-8 space-y-6" id="trasladoForm" >
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
                                    <?php if($_SESSION["sucursal_id"] == 1) echo '<option value="">Seleccione una sucursal</option>'; ?>
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

                        <!-- Folios -->
                        <div class="form-group mt-6" >
                            <label for="folios_almonedas" class="block text-sm font-medium text-gray-700 mb-2">Folios de Almonedas</label>
                            <div class="flex gap-2 mb-2">
                                <input 
                                    type="text" 
                                    name="folios_almonedas" 
                                    id="folios_almonedas"
                                    x-model="folioInput"
                                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg"
                                    placeholder="Ej: 123, 456, 789"
                                    autocomplete="off"
                                    @keydown.enter.prevent="buscarFolios"
                                >
                                <button type="button" @click="buscarFolios" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                                    Buscar Folios
                                </button>
                            </div>

                            <template x-if="cargando">
                                <div class="text-center text-indigo-600 mb-4">
                                    <span class="animate-pulse">Cargando folios...</span>
                                </div>
                            </template>

                            <template x-if="error">
                                <div class="text-red-600 font-medium mb-2" x-text="error"></div>
                            </template>

                            <div class="overflow-x-auto" x-show="folios.length">
                                <table class="min-w-full table-auto text-sm text-left text-gray-700">
                                    <thead>
                                        <tr class="bg-indigo-600 text-white">
                                            <th class="px-4 py-2">Folio</th>
                                            <th class="px-4 py-2">Cantidad</th>
                                            <th class="px-4 py-2">Precio Unitario</th>
                                            <th class="px-4 py-2">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(folio, index) in folios" :key="index">
                                            <tr class="border-b">
                                                <td class="px-4 py-2" x-text="folio.folio"></td>
                                                <td class="px-4 py-2" x-text="formatNumber(folio.cantidad)"></td>
                                                <td class="px-4 py-2" x-text="formatCurrency(folio.precio_unitario)"></td>
                                                <td class="px-4 py-2" x-text="formatCurrency(folio.total)"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Botón para guardar -->
                        <div class="mt-4 text-center">
                            <button type="submit" class="w-full px-4 py-2 rounded transition-colors duration-200" :class="cargando ? 'bg-gray-400 cursor-not-allowed' : 'bg-blue-500 hover:bg-blue-600 text-white'" :disabled="cargando">
                                <span x-show="!cargando">Guardar Traslado</span>
                                <span x-show="cargando">Procesando...</span>
                            </button>
                            <svg x-show="cargando" class="animate-spin h-5 w-5 text-white inline-block ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                            </svg>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alpine y Toastr -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.4.10/dist/cdn.min.js" defer></script>

<script>
function foliosAlmonedas() {
    
    return {
        folioInput: '',
        folios: [],
        cargandoFolios: false,
        cargando: false,
        error: '',
        buscarFolios() {
            this.cargando = true;
            this.error = '';
            this.folios = [];

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
                if (data.error) {
                    this.error = data.error;
                    this.folios = [];
                } else {
                    this.folios = data.folios || [];
                }
            })
            .catch(err => {
                this.error = 'Error al consultar los folios';
                console.error(err);
            })
            .finally(() => {
                this.cargando = false;
            });
        },

        formatNumber(value) {
            return Number(value).toLocaleString('es-MX');
        },

        formatCurrency(value) {
            return Number(value).toLocaleString('es-MX', {
                style: 'currency',
                currency: 'MXN'
            });
        }
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
