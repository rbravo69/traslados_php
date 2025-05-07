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

                <div x-data="trasladoForm()" class="p-6 md:p-8 space-y-6">
                    <form method="POST" action="../includes/create_logic.php" id="trasladoForm">
                        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken(); ?>">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Empresa</label>
                                <select name="empresa_id" class="w-full border rounded p-2" required>
                                    <option value="">Seleccione una empresa</option>
                                    <?php foreach ($empresas as $empresa): ?>
                                        <option value="<?= $empresa['id'] ?>"><?= htmlspecialchars($empresa['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Fecha del Traslado</label>
                                <input type="date" name="fecha_traslado" class="w-full border rounded p-2" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Código de Seguridad</label>
                                <input type="text" name="codigo_seguridad" autocomplete="off" class="w-full border rounded p-2">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Vehículo</label>
                                <select name="vehiculo_id" class="w-full border rounded p-2" required>
                                    <option value="">Seleccione un vehículo</option>
                                    <?php foreach ($vehiculos as $vehiculo): ?>
                                        <option value="<?= $vehiculo['id'] ?>"><?= htmlspecialchars($vehiculo['modelo'] . " - " . $vehiculo['color']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Personal de Traslado</label>
                                <select name="personal_id" class="w-full border rounded p-2" required>
                                    <option value="">Seleccione un personal</option>
                                    <?php foreach ($personal as $persona): ?>
                                        <option value="<?= $persona['id'] ?>"><?= htmlspecialchars($persona['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Sucursal de Origen</label>
                                <select name="sucursal_origen_id" class="w-full border rounded p-2" required>
                                    <?php if($_SESSION["sucursal_id"] == 1) echo '<option value="">Seleccione una sucursal</option>'; ?>
                                    <?php foreach ($sucursales_origen as $sucursal): ?>
                                        <option value="<?= $sucursal['id'] ?>"><?= htmlspecialchars($sucursal['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

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

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Folios</label>
                                <input type="text" x-model="folios" name="folios" placeholder="Ej. 2256,2254,2289" class="w-full border rounded p-2" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Cantidad</label>
                                <input type="number" x-model.number="cantidad" name="cantidad" class="w-full border rounded p-2" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Precio Unitario</label>
                                <input type="number" x-model.number="precio" name="precio_unitario" class="w-full border rounded p-2" />
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700">Total</label>
                            <input type="text" :value="formatearMoneda(total)" readonly name="total" class="w-full border rounded p-2 bg-gray-100" />
                        </div>

                        <div class="mt-6 text-center">
                            <button type="button"
                                class="w-full px-4 py-2 rounded transition-colors duration-200"
                                :class="cargando ? 'bg-gray-400 cursor-not-allowed' : 'bg-blue-500 hover:bg-blue-600 text-white'"
                                :disabled="cargando"
                                @click="mostrarOverlayYEnviar()">
                                <span x-show="!cargando">Guardar Traslado</span>
                                <span x-show="cargando">Guardando...</span>
                                <svg x-show="cargando" class="animate-spin h-5 w-5 text-white inline-block ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function trasladoForm() {
    return {
        folios: '',
        cantidad: 0,
        precio: 0,
        cargando: false,

        get total() {
            return this.cantidad * this.precio;
        },

        formatearMoneda(valor) {
            return Number(valor).toLocaleString('es-MX', {
                style: 'currency',
                currency: 'MXN',
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        },

        mostrarOverlayYEnviar() {
            this.cargando = true;

            fetch('./../includes/create_logic.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    folios: this.folios,
                    totalCantidad: this.cantidad,
                    totalPrecioUnitario: this.precio,
                    totalTotal: this.total
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Toastify({
                        text: "Traslado guardado exitosamente",
                        duration: 3000,
                        gravity: "top",
                        position: 'right',
                        backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                    }).showToast();
                    this.cargando = false;
                    this.limpiarCampos();
                } else {
                    Toastify({
                        text: data.error || 'Error al guardar el traslado',
                        duration: 3000,
                        gravity: "top",
                        position: 'right',
                        backgroundColor: "linear-gradient(to right, #FF0000, #FF4500)",
                    }).showToast();
                    this.cargando = false;
                    console.error(data.error || 'Error al guardar el traslado');
                }
            })
            .catch(err => {
                Toastify({
                    text: "Error al procesar la solicitud",
                    duration: 3000,
                    gravity: "top",
                    position: 'right',
                    backgroundColor: "linear-gradient(to right, #FF0000, #FF4500)",
                }).showToast();
                this.cargando = false;
                console.error('Error al procesar la solicitud', err);
            })
            .finally(() => {
                this.cargando = false;
            });
        },

        limpiarCampos() {
            this.folios = '';
            this.cantidad = 0;
            this.precio = 0;
            document.getElementById('trasladoForm').reset();
        }
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
