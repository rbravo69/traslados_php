<?php
// Verifica si el usuario está logueado
require_once __DIR__ . '/../includes/auth.php';
requireLogin();
require_once __Dir__ . '/../includes/dashboard_logic.php';
require_once __DIR__ .'/../includes/header.php';
?>

<div class="p-6">
    <h2 class="text-2xl font-semibold">Dashboard de Traslados</h2>

    <div class="flex space-x-4 items-center mb-6">
        <!-- Card del Total de Traslados -->
        <div class="mt-6 w-40">
            <div class="bg-white shadow-lg rounded-lg p-6 text-center">
                <h3 class="text-lg font-semibold">Traslados Realizados</h3>
                <p class="text-4xl font-bold text-blue-500"><?= htmlspecialchars($totalTraslados) ?></p>
            </div>
        </div>

        <!-- Card del Total de Gramos -->
        <div class="mt-6 w-40">
            <div class="bg-white shadow-lg rounded-lg p-6 text-center">
                <h3 class="text-lg font-semibold">Gramos Transferidos</h3>
                <p class="text-4xl font-bold text-blue-500"><?= htmlspecialchars($totalAlmonedas) ?></p>
            </div>
        </div>
    </div>

    <!-- Gráfico de Traslados por Mes -->
    <div class="mt-6">
        <h3 class="text-xl font-semibold">Traslados por Mes en General (<?= date('Y') ?>)</h3>
        <div class="bg-white shadow-lg rounded-lg p-6">
            <canvas id="trasladosPorMesChart" class="w-full" style="height: 200px;"></canvas>
        </div>
    </div>

    <!-- Gráfico de Almonedas por Mes -->
    <div class="mt-6">
        <h3 class="text-xl font-semibold">Total de Almonedas por Mes (<?= date('Y') ?>)</h3>
        <div class="bg-white shadow-lg rounded-lg p-6">
            <canvas id="almonedasPorMesChart" class="w-full" style="height: 200px;"></canvas>
        </div>
    </div>

    <!-- Detalle de Traslados -->
    <table class="min-w-full border-collapse border border-gray-200 mt-5">
        <thead>
            <tr>
                <th class="border border-gray-300 px-4 py-2 text-center bg-slate-300">Sucursal Origen</th>
                <th class="border border-gray-300 px-4 py-2 text-center bg-slate-300">Sucursal Destino</th>
                <th class="border border-gray-300 px-4 py-2 text-center bg-slate-300">Cantidad Gramos</th>
                <th class="border border-gray-300 px-4 py-2 text-center bg-slate-300">Fecha de Traslado</th>
                <th class="border border-gray-300 px-4 py-2 text-center bg-slate-300">Código de Seguridad</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($detalleTraslados as $traslado): ?>
                <tr>
                    <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($traslado['sucursal_origen']) ?></td>
                    <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($traslado['sucursal_destino']) ?></td>
                    <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($traslado['cantidad_almonedas']) ?></td>
                    <td class="border border-gray-300 px-4 py-2"><?= date('d/m/Y', strtotime($traslado['fecha_traslado'])) ?></td>
                    <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($traslado['codigo_seguridad']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Cargar el gráfico con Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Datos de traslados por mes
    const trasladosPorMes = <?= json_encode(array_values($trasladosPorMes)) ?>;
    const almonedasPorMes = <?= json_encode(array_values($totalAlmonedasPorMes)) ?>;

    const meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

    // Configuración del gráfico de traslados
    const ctx = document.getElementById('trasladosPorMesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: meses,
            datasets: [{
                label: 'Traslados por Mes',
                data: trasladosPorMes,
                backgroundColor: 'rgba(75, 192, 192, 0.4)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true } }
        }
    });

    // Configuración del gráfico de almonedas
    const ctxAlmonedas = document.getElementById('almonedasPorMesChart').getContext('2d');
    new Chart(ctxAlmonedas, {
        type: 'line',
        data: {
            labels: meses,
            datasets: [{
                label: 'Total de Almonedas por Mes',
                data: almonedasPorMes,
                backgroundColor: 'rgba(153, 102, 255, 0.6)',
                borderColor: 'rgba(153, 102, 255, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true } }
        }
    });
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
