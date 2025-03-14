<?php
// Incluir lógica de autenticación
require_once __DIR__ . '/includes/auth.php';
requireLogin();

require_once __DIR__ . '/config.php'; // Conexión a la base de datos
require_once __DIR__ . '/vendor/fpdf.php'; // Asegúrate de que FPDF está en la carpeta correcta
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
    $stmt->execute([$traslado_id,$sucursal_origen_id, 
    $sucursal_destino_id, $empresa_id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        return null;
    }

    // Crear PDF
    $pdf = new FPDF('P', 'mm', 'Letter');
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Reporte de Traslado', 0, 1, 'C');
    $pdf->Ln(5);

    // Datos del traslado
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(50, 10, 'Fecha: ' . $fecha_traslado, 0, 1);
    $pdf->Cell(50, 10, 'Codigo Seguridad: ' . $data['codigo_seguridad'], 0, 1);
    // Datos de la empresa
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Datos de la Empresa', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(50, 10, 'Empresa: ' . $data['empresa'], 0, 1);
    $pdf->Cell(50, 10, 'RFC: ' . $data['rfc'], 0, 1);
    $pdf->Cell(50, 10, 'Direccion: ' . $data['direccion'], 0, 1);
    $pdf->Ln(5);
    // Datos del traslado
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(50, 10, 'Sucursal Origen: ' . $data['sucursal_origen'], 0, 1);
    $pdf->Cell(50, 10, 'Sucursal Destino: ' . $data['sucursal_destino'], 0, 1);
    $pdf->Ln(5);
    // Detalles del personal
    $personal = $sqlite->query("SELECT * FROM personal_traslados 
    WHERE id = " . $data['personal_id'])->fetch(PDO::FETCH_ASSOC);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Personal que Traslada', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(50, 10, 'Nombre: ' . $personal["nombre"], 0, 1);
    $pdf->Cell(50, 10, 'Num. Licencia: ' .  $personal["licencia"], 0, 1);
    $pdf->Cell(50, 10, 'Mum. Empleado: ' .$personal["num_empleado"] , 0, 1);
    $pdf->Cell(50, 10, 'Seguro Social(IMSS): ' .$personal["nss"] , 0, 1);
    $pdf->Ln(5);

    // Detalles de los vehículos
    $vehiculo_id = $data['vehiculo_id'];
    $vehiculos = $sqlite->query("SELECT * FROM vehiculos WHERE id = $vehiculo_id")->fetch(PDO::FETCH_ASSOC);
    
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Vehiculo de Traslado', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(50, 10, 'Marca: ' . $vehiculos["marca"], 0, 1);
    $pdf->Cell(50, 10, 'Modelo: ' . $vehiculos["modelo"], 0, 1);
    $pdf->Cell(50, 10, 'Color: ' .  $vehiculos["color"], 0, 1);
    $pdf->Cell(50, 10, 'Placas: ' .$vehiculos["placas"] , 0, 1);
    $pdf->Cell(50, 10, 'Num. Serie: ' .$vehiculos["num_serie"] , 0, 1);
    $pdf->Cell(50, 10, 'Num. Motor: ' .$vehiculos["num_motor"] , 0, 1);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Datos del Seguro', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(50, 10, 'Compañia: ' . $vehiculos["aseguradora"], 0, 1);
    $pdf->Cell(50, 10, 'Poliza: ' . $vehiculos["num_poliza"], 0, 1);
    $pdf->Cell(50, 10, 'Vigencia: ' . $vehiculos["fecha_vencimiento"], 0, 1);
    $pdf->Ln(10);
    // Detalles de los folios de almoneda
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Datos de Almoneda', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(50, 10, 'Folios: ' . $data["folio"], 0, 1);
    $pdf->Cell(50, 10, 'Cantidad: ' . number_format($data["cantidad_almonedas"],1,'.',','), 0, 1);
    $pdf->Cell(50, 10, 'Precio Unitario: ' . number_format($data["color"],2,'.',','), 0, 1);
    $pdf->Cell(50, 10, 'Total: ' . number_format($data["total_almonedas"],2,'.',',') , 0, 1);


    // Crear directorio si no existe
    $ruta_pdf = __DIR__ . 'documentos/';
    if (!file_exists($ruta_pdf)) {
        mkdir($ruta_pdf, 0777, true);
    }

    // Guardar el PDF con nombre único
    $nombre_pdf = "traslado_almoneda-".$data['sucursal_origen']."-" .date("Ymmd") . ".pdf";
    $pdf->Output($ruta_pdf . $nombre_pdf, 'F'); // 'F' = Guardar en archivo

     // Forzar la descarga del PDF
     header('Content-Type: application/pdf');
     header('Content-Disposition: attachment; filename="' . $nombre_pdf . '"');
     readfile($ruta_completa);
 
     exit; // Terminar la ejecución después de enviar el PDF
}
?>
