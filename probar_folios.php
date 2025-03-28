<?php
// probar_folios.php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Probar Folios</title>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-7xl mx-auto bg-white shadow-md rounded-lg p-6" x-data="folioTester()">
        <h1 class="text-2xl font-bold text-center text-indigo-700 mb-6">Probar Consulta de Folios</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Panel Izquierdo: Consulta y errores -->
            <div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Folios separados por coma:</label>
                    <input type="text" x-model="folioInput" class="w-full border border-gray-300 rounded-lg px-4 py-2" placeholder="Ej: 123,456,789" @keydown.enter.prevent="buscarFolios">
                </div>

                <div class="mb-4 text-center">
                    <button @click="buscarFolios" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition-all duration-200">
                        Buscar
                    </button>
                </div>

                <template x-if="cargando">
                    <div class="text-center text-indigo-600 mb-4">
                        <span class="animate-pulse">Cargando folios...</span>
                    </div>
                </template>

                <div class="bg-gray-100 border border-gray-300 rounded p-4 text-sm text-gray-700 whitespace-pre-wrap">
                    <h2 class="text-lg font-semibold text-indigo-700 mb-2">Consulta Generada</h2>
                    <template x-if="consulta">
                        <code x-text="consulta"></code>
                    </template>
                    <template x-if="!consulta">
                        <p class="text-gray-400 italic">La consulta aparecerá aquí después de buscar.</p>
                    </template>
                </div>

                <template x-if="error">
                    <div class="mt-4 text-red-600 font-medium" x-text="error"></div>
                </template>
            </div>

            <!-- Panel Derecho: Tabla -->
            <div>
                <template x-if="folios.length > 0">
                    <div class="overflow-x-auto">
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
                </template>

                <template x-if="folios.length === 0 && !cargando && !error">
                    <p class="text-center text-gray-500">No hay resultados que mostrar.</p>
                </template>
            </div>
        </div>
    </div>

    <script>
        function folioTester() {
            return {
                folioInput: '',
                folios: [],
                cargando: false,
                error: '',
                consulta: '',

                buscarFolios() {
                    this.cargando = true;
                    this.error = '';
                    this.folios = [];
                    this.consulta = '';

                    fetch('./includes/buscar_folios_mysql.php', {
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
                            this.consulta = data.query || '';
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
</body>
</html>
