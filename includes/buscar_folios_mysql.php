<?php
require_once 'auth.php'; // Verifica si el usuario está logueado
requireLogin();
require_once __DIR__ . '/../config.php';

if (!isset($_GET['folios_almonedas']) || empty($_GET['folios_almonedas'])) {
    echo "<tr><td colspan='4' class='text-red-500 text-center'>No se recibieron folios</td></tr>";
    exit;
}

try {
    // Convertir la cadena de folios en un array y sanitizar
    $folioNumeros = explode(',', $_GET['folios_almonedas']);
    $folioNumeros = array_map('trim', $folioNumeros);
    $folioNumeros = array_map('intval', $folioNumeros); // Convertir a enteros para evitar inyecciones SQL

    // Convertir el array de folios en una cadena separada por comas
    $foliosString = implode(',', $folioNumeros);

    // Consulta SQL en PHP puro con PDO
    $query = "
        SELECT 
            ei.Folio,
            COALESCE(SUM(di.pesoPiedras) + SUM(di.peso), 0) AS Cantidad,
            COALESCE(SUM(di.avaluo) / NULLIF((SUM(di.pesoPiedras) + SUM(di.peso)), 0), 0) AS PrecioUnitario,
            COALESCE(SUM(di.avaluo) / NULLIF((SUM(di.pesoPiedras) + SUM(di.peso)), 0), 0) * 
            (COALESCE(SUM(di.pesoPiedras) + SUM(di.peso), 0)) AS Total
        FROM basedatos.entradainventario ei
        LEFT JOIN basedatos.detallesentradainventario di 
            ON ei.ID = di.IDEntrada OR ei.ID = di.ContratoPrincipal
        WHERE ei.Folio IN ($foliosString)
        GROUP BY ei.Folio
    ";

    // Preparar y ejecutar la consulta con PDO

    $stmt = $mysql->prepare($query);
    $stmt->execute();
    $folios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($folios)) {
        echo "<tr><td colspan='4' class='text-red-500 text-center'>No se encontraron folios en MySQL</td></tr>";
        exit;
    }
    $total_cantidad = 0;
    $total_precio = 0;
    $total_total = 0;
    $folios_con_comas = '';
    // Generar tabla HTML directamente
    foreach ($folios as $folio) {
        echo "<tr>
            <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-900'>". $folio['Folio'] ."</td>
            <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-900'>" . number_format($folio['Cantidad'], 1) . "</td>
            <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-900'>" . number_format($folio['PrecioUnitario'], 4) . "</td>
            <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-900'>" . number_format($folio['Total'], 2) . "</td>
        </tr>";
        //agregarle coma a los folios
        $folios_con_comas .= $folio['Folio'] . ',';
        $total_cantidad += $folio['Cantidad'];
        $total_precio += $folio['PrecioUnitario'];  
        $total_total += $folio['Total'];
    }

       // Eliminar la última coma
       $folios_con_comas = rtrim($folios_con_comas, ',');
    // Agregar los totales al formulario
    echo '<input type="hidden" name="folios_almonedas" value="'. $folios_con_comas.'" />';
    echo '<input type="hidden" name="total_cantidad" value="'. $total_cantidad.'" />';
    echo '<input type="hidden" name="total_precio" value="'. $total_precio .' "/>';
    echo '<input type="hidden" name="total_total" value="'. $total_total .' "/>';

} catch (Exception $e) {
    error_log("Error buscando folios: " . $e->getMessage());
    echo "<tr><td colspan='4' class='text-red-500 text-center'>Error al buscar folios</td></tr>";
}
?>


