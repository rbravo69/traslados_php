<?php
// Verifica si el usuario está logueado
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
require_once __DIR__ .  '/../config.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/consulta_traslados_logic.php';

?>


<div class="container mx-auto p-5">
    <h1 class="text-2xl font-bold mb-5">Consulta de Traslados</h1>

    <!-- Formulario de Filtro -->
    <form method="GET" action="consultaTraslados.php" class="mb-5">
        <div class="flex space-x-4">
            <div>
                <label for="fecha" class="block text-gray-700">Fecha:</label>
                <input type="date" name="fecha" id="fecha" class="border rounded p-2" value="<?=$fecha ?>">
            </div>
            <div>
                <label for="sucursal_origen_id" class="block text-gray-700">Sucursal de Origen:</label>
                <select name="sucursal_origen_id" id="sucursal_origen_id" class="border rounded p-2">
                    <option value="">Todas</option>
                    <?php foreach ($sucursales as $sucursal): ?>
                        <option value="<?= $sucursal['id'] ?>" <?= ($sucursal['id'] == $sucursal_origen_id) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($sucursal['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
           
            <div class="self-end">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Filtrar</button>
            </div>
        </div>
    </form>

    <!-- Tabla de Resultados -->
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-lg">
            <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">Fecha de Traslado</th>
                    <th class="py-3 px-6 text-left">Sucursal de Origen</th>
                    <th class="py-3 px-6 text-left">Sucursal de Destino</th>
                    <th class="py-3 px-6 text-left">Personal</th>
                    <th class="py-3 px-6 text-left">Empresa</th>
                    <th class="py-3 px-6 text-left">Vehículo</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
                <?php foreach ($traslados as $traslado): ?>
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6"><?= htmlspecialchars($traslado['fecha_traslado']) ?></td>
                        <td class="py-3 px-6"><?= htmlspecialchars($traslado['sucursal_origen']) ?></td>
                        <td class="py-3 px-6"><?= htmlspecialchars($traslado['sucursal_destino']) ?></td>
                        <td class="py-3 px-6"><?= htmlspecialchars($traslado['personal']) ?></td>
                        <td class="py-3 px-6"><?= htmlspecialchars($traslado['empresa']) ?></td>
                        <td class="py-3 px-6"><?= htmlspecialchars($traslado['modelo']) ?> - <?= htmlspecialchars($traslado['color']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

