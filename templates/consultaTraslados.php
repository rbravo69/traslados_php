<?php
// Verifica si el usuario está logueado
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
require_once __DIR__ .  '/../config.php';
require_once __DIR__ . '/../includes/header.php';

// Obtener filtros si existen
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : null;
$sucursal_origen_id = isset($_GET['sucursal_origen_id']) ? $_GET['sucursal_origen_id'] : null;
$sucursal_destino_id = isset($_GET['sucursal_destino_id']) ? $_GET['sucursal_destino_id'] : null;

// Construir la consulta SQL con filtros dinámicos
$query = "SELECT 
    t.fecha_traslado, 
    so.nombre AS sucursal_origen, 
    sd.nombre AS sucursal_destino, 
    p.nombre AS personal, 
    e.nombre AS empresa, 
    v.modelo, v.color
FROM traslados t
JOIN sucursales so ON t.sucursal_origen_id = so.id
JOIN sucursales sd ON t.sucursal_destino_id = sd.id
JOIN personal_traslados p ON t.personal_id = p.id
JOIN empresas e ON t.empresa_id = e.id
JOIN vehiculos v ON t.vehiculo_id = v.id
WHERE 1=1";

// Aplicar filtros dinámicos
$params = [];
if ($fecha) {
    $query .= " AND t.fecha_traslado = ?";
    $params[] = $fecha;
}
if ($sucursal_origen_id) {
    $query .= " AND t.sucursal_origen_id = ?";
    $params[] = $sucursal_origen_id;
}
if ($sucursal_destino_id) {
    $query .= " AND t.sucursal_destino_id = ?";
    $params[] = $sucursal_destino_id;
}

// Ordenar por fecha descendente
$query .= " ORDER BY t.fecha_traslado DESC";

// Ejecutar la consulta
$stmt = $sqlite->prepare($query);
$stmt->execute($params);
$traslados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener sucursales para los filtros
$sucursales = $sqlite->query("SELECT * FROM sucursales")->fetchAll(PDO::FETCH_ASSOC);
?>


<div class="container mx-auto p-5">
    <h1 class="text-2xl font-bold mb-5">Consulta de Traslados</h1>

    <!-- Formulario de Filtro -->
    <form method="GET" action="consultaTraslados.php" class="mb-5">
        <div class="flex space-x-4">
            <div>
                <label for="fecha" class="block text-gray-700">Fecha:</label>
                <input type="date" name="fecha" id="fecha" class="border rounded p-2" value="<?= htmlspecialchars($fecha) ?>">
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

