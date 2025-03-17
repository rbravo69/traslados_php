<?php
// Incluir lógica de autenticación
require_once __DIR__ . '/includes/auth.php';
requireLogin();

require_once __DIR__ . '/config.php'; // Conexión a la base de datos
require_once __DIR__ . '/fpdf/fpdf.php'; // Ajusta la ruta según corresponda

function generarYGuardarPDF($traslado_id, $empresa_id, $fecha_traslado, $sucursal_origen_id, $sucursal_destino_id, $folios, $cantidad, $total) {
    global $sqlite;

    // Obtener datos relacionados desde la base de datos
    $stmt = $sqlite->prepare("
         SELECT 
        t.folios_almonedas AS folio, 
        t.fecha_traslado, 
        s1.nombre AS sucursal_origen, 
        s2.nombre AS sucursal_destino,
        e.nombre AS empresa, 
        e.rfc, 
        e.direccion,
        t.cantidad_almonedas,
         t.precio_unitario_almonedas,
        t.total_almonedas,
        t.codigo_seguridad,
        t.vehiculo_id,
        t.personal_id
        FROM traslados t
        JOIN sucursales s1 ON t.sucursal_origen_id = s1.id
        JOIN sucursales s2 ON t.sucursal_destino_id = s2.id
        JOIN empresas e ON t.empresa_id = e.id
        WHERE t.id = ? 
        AND t.sucursal_origen_id = ? 
        AND t.sucursal_destino_id = ? 
        AND t.empresa_id = ?
    ");
    $stmt->execute([$traslado_id, $sucursal_origen_id, $sucursal_destino_id, $empresa_id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        return null;
    }

    // Crear PDF
    $pdf = new FPDF('P', 'mm', 'Letter');
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->SetFillColor(96, 176, 132);
    $pdf->Cell(0, 10, 'Reporte de Traslado', 0, 1, 'C');
    $pdf->Ln(5);

    // Datos del traslado
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(96, 176, 132);
    $pdf->Cell(50, 10, 'Fecha:', 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 10, $fecha_traslado, 1, 1, 'L');
    
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(96, 176, 132);
    $pdf->Cell(50, 10, 'Codigo Seguridad:', 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 10, $data['codigo_seguridad'], 1, 1, 'L');
    
    // Datos de la empresa
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->SetFillColor(96, 176, 132);
    $pdf->Cell(0, 10, 'Datos de la Empresa', 1, 1, 'C', true);
    $pdf->SetFillColor(96, 176, 132);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(50, 10, 'Empresa:', 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 10, $data['empresa'], 1, 1, 'L');
    
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(96, 176, 132);
    $pdf->Cell(50, 10, 'RFC:', 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 10, $data['rfc'], 1, 1, 'L');
    
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(96, 176, 132);
    $pdf->Cell(50, 10, 'Direccion:', 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 10, $data['direccion'], 1, 1, 'L');
    
    // Datos del traslado
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->SetFillColor(96, 176, 132);
    $pdf->Cell(0, 10, 'Datos del Traslado', 1, 1, 'C', true);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetFillColor(96, 176, 132);
    $pdf->Cell(50, 10, 'Sucursal Origen:', 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, $data['sucursal_origen'], 1, 1, 'L');
    
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(96, 176, 132);
    $pdf->Cell(50, 10, 'Sucursal Destino:', 1, 0, 'L', true);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 10, $data['sucursal_destino'], 1, 1, 'L');
    
    // Detalles del personal
    $personal = $sqlite->query("SELECT * FROM personal_traslados WHERE id = " . $data['personal_id'])->fetch(PDO::FETCH_ASSOC);
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->SetFillColor(96, 176, 132);
    $pdf->Cell(0, 10, 'Personal que Traslada', 1, 1, 'C', true);
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetFillColor(96, 176, 132);
    $pdf->Cell(50, 10, 'Nombre:', 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 10, $personal["nombre"], 1, 1, 'L');
    
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(96, 176, 132);
    $pdf->Cell(50, 10, 'Num. Licencia:', 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 10, $personal["num_licencia"], 1, 1, 'L');
    
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(96, 176, 132);
    $pdf->Cell(50, 10, 'Num. Empleado:', 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 10, $personal["num_empleado"], 1, 1, 'L');
    
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(96, 176, 132);
    $pdf->Cell(50, 10, 'Seguro Social (IMSS):', 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 10, $personal["nss"], 1, 1, 'L');
    
    // Detalles de los vehículos
    $vehiculo_id = $data['vehiculo_id'];
    $vehiculos = $sqlite->query("SELECT * FROM vehiculos WHERE id = $vehiculo_id")->fetch(PDO::FETCH_ASSOC);
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->SetFillColor(96, 176, 132);
    $pdf->Cell(0, 10, 'Vehiculo de Traslado', 1, 1, 'C', true);
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetFillColor(96, 176, 132);
    $pdf->Cell(50, 10, 'Marca:', 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 10, $vehiculos["marca"], 1, 1, 'L');
    
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(96, 176, 132);
    $pdf->Cell(50, 10, 'Modelo:', 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 10, $vehiculos["modelo"], 1, 1, 'L');
    
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(96, 176, 132);
    $pdf->Cell(50, 10, 'Color:', 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 10, $vehiculos["color"], 1, 1, 'L');
    
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(96, 176, 132);
    $pdf->Cell(50, 10, 'Placas:', 1, 0, 'L', true);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 10, $vehiculos["placas"], 1, 1, 'L');
    
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(96, 176, 132);
    $pdf->Cell(50, 10, 'Num. Serie:', 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 10, $vehiculos["num_serie"], 1, 1, 'L');
    
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(96, 176, 132);
    $pdf->Cell(50, 10, 'Num. Motor:', 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 10, $vehiculos["num_motor"], 1, 1, 'L');
    
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->SetFillColor(96, 176, 132);
    $pdf->Cell(0, 10, 'Datos del Seguro', 1, 1, 'C', true);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(96, 176, 132);
    $pdf->Cell(50, 10, 'Compañia:', 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 10, $vehiculos["aseguradora"], 1, 1, 'L');
    
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(96, 176, 132);
    $pdf->Cell(50, 10, 'Poliza:', 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 10, $vehiculos["num_poliza"], 1, 1, 'L');
    
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(96, 176, 132);
    $pdf->Cell(50, 10, 'Vigencia:', 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 10, $vehiculos["fecha_vencimiento"], 1, 1, 'L');
    
    // Detalles de los folios de almoneda
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->SetFillColor(96, 176, 132);
    $pdf->Cell(0, 10, 'Datos de Almoneda', 1, 1, 'C', true);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(96, 176, 132);
    $pdf->Cell(50, 10, 'Folios:', 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 10, $data["folio"], 1, 1, 'L');
    
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(96, 176, 132);
    $pdf->Cell(50, 10, 'Cantidad:', 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 10, number_format($data["cantidad_almonedas"], 1, '.', ','), 1, 1, 'L');
    
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(96, 176, 132);
    $pdf->Cell(50, 10, 'Precio Unitario:', 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 10, number_format($data["precio_unitario_almonedas"], 2, '.', ','), 1, 1, 'L');
    
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(96, 176, 132);
    $pdf->Cell(50, 10, 'Total:', 1, 0, 'L', true);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 10, number_format($data["total_almonedas"], 2, '.', ','), 1, 1, 'L');

    // Crear directorio si no existe
    $ruta_pdf = __DIR__ . '/documents';
    if (!file_exists($ruta_pdf)) {
        mkdir($ruta_pdf, 0777, true);
    }

    // Guardar el PDF con nombre único
    $nombre_pdf = "traslado_almoneda-".$data['sucursal_origen']."-" .date("Ymd") . ".pdf";
    $ruta_completa = $ruta_pdf . '/' . $nombre_pdf;
    $pdf->Output($ruta_completa, 'F'); // 'F' = Guardar en archivo

    // Actualizar la ruta del PDF en la tabla traslado
    $update_stmt = $sqlite->prepare("UPDATE traslados SET ruta_pdf = ? WHERE id = ?");
    $update_stmt->execute([$ruta_completa, $traslado_id]);

    // Forzar la descarga del PDF
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $nombre_pdf . '"');
    readfile($ruta_completa);
    return $nombre_pdf;
    exit; // Terminar la ejecución después de enviar el PDF
}
?>
