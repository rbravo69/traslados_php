<?php
    require_once 'auth.php';
    requireLogin();
    require_once __DIR__ . '/../config.php';

    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['error' => 'Método no permitido']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $folioTexto = $input['folios'] ?? '';

    if (empty($folioTexto)) {
        echo json_encode(['error' => 'No se recibieron folios']);
        exit;
    }

    try {
        $folioNumeros = explode(',', $folioTexto);
        $folioNumeros = array_map('trim', $folioNumeros);
        $folioNumeros = array_map('intval', $folioNumeros); // Evita inyecciones SQL

        if (empty($folioNumeros)) {
            echo json_encode(['error' => 'Folios vacíos']);
            exit;
        }

        $placeholders = implode(',', array_fill(0, count($folioNumeros), '?'));

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
            WHERE ei.Folio IN ($placeholders)
            AND di.tipoSalida = 2
            GROUP BY ei.Folio
        ";

        $stmt = $mysql->prepare($query);
        $stmt->execute($folioNumeros);
        $folios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        if (empty($folios)) {
            echo json_encode(['error' => 'No se encontraron folios']);
            exit;
        }

        // Agregar totales generales
        $total_cantidad = 0;
        $total_precio = 0;
        $total_total = 0;
        $folios_comas = [];

        foreach ($folios as $f) {
            $total_cantidad += $f['Cantidad'];
            $total_precio += $f['PrecioUnitario'];
            $total_total += $f['Total'];
            $folios_comas[] = $f['Folio'];
        }

        echo json_encode([
            'folios' => $folios,
            'resumen' => [
                'folios_almonedas' => implode(',', $folios_comas),
                'total_cantidad' => $total_cantidad,
                'total_precio' => $total_precio,
                'total_total' => $total_total,
            ]
        ]);
    } catch (Exception $e) {
        error_log("Error al buscar folios: " . $e->getMessage());
        echo json_encode(['error' => 'Error al procesar los folios']);
    } finally {
        $mysql = null; // Cierra la conexión a la base de datos
    }
 ?> 
