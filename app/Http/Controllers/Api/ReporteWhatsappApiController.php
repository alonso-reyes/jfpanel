<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ReporteJefeFrente;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\DB;

class ReporteWhatsappApiController extends Controller
{
    public function reporteDiarioWhastapp(Request $request)
    {
        //Log::info('Datos recibidos:', $request->all()); 
        //Log::info('Datos recibidos:', $request['data']['acarreos_volumen']);

        $request->validate([
            'obra_id' => 'required|exists:obras,id',
        ]);

        $obraId = $request->obra_id;

        $fechas = ReporteJefeFrente::where('obra_id', $obraId)
            ->select(DB::raw('DATE(created_at) as fecha'))
            ->distinct()
            ->orderBy('fecha', 'desc')
            ->pluck('fecha');

        $resumen = [];

        foreach ($fechas as $fecha) {
            $reportes = ReporteJefeFrente::where('obra_id', $obraId)
                ->whereDate('created_at', $fecha)
                ->pluck('id');

            //$reporteId = $reportes->first(); // o el que tú decidas

            // Maquinaria
            $totalMaquinaria = DB::table('reportes_maquinaria')
                ->whereIn('reporte_frente_id', $reportes)
                ->count();

            $maquinariaPorTipo = DB::table('reportes_maquinaria as rm')
                ->join('tipos_maquinaria as tm', 'rm.tipo_maquinaria_id', '=', 'tm.id')
                ->whereIn('rm.reporte_frente_id', $reportes)
                ->select('tm.nombre as tipo_maquinaria', DB::raw('count(*) as total'))
                ->groupBy('tm.nombre')
                ->get();

            // Personal
            $totalPersonal = DB::table('reportes_personal')
                ->whereIn('reporte_frente_id', $reportes)
                ->count();

            $personalPorPuesto = DB::table('reportes_personal as rp')
                ->join('personal as p', 'rp.personal_id', '=', 'p.id')
                ->join('puestos as pt', 'p.puesto_id', '=', 'pt.id')
                ->whereIn('rp.reporte_frente_id', $reportes)
                ->select('pt.puesto as puesto', DB::raw('count(*) as total'))
                ->groupBy('pt.puesto')
                ->get();

            // Acarreos
            $detallesVolumen = DB::table('acarreos_volumen as av')
                ->join('materiales as m', 'av.material_id', '=', 'm.id')
                ->join('materiales_uso as mu', 'av.material_uso_id', '=', 'mu.id')
                ->whereIn('av.reporte_frente_id', $reportes)
                ->select(
                    'm.material as material',
                    'mu.uso as uso_material',
                    'av.volumen'
                )
                ->get();


            $volumenTotal = DB::table('acarreos_volumen')
                ->whereIn('reporte_frente_id', $reportes)
                ->selectRaw('COALESCE(SUM(viajes), 0) as total_viajes,
                    COALESCE(SUM(capacidad), 0) as total_capacidad,
                    COALESCE(SUM(volumen), 0) as total_volumen
                ')
                ->first();

            // $volumenTotal = DB::table('acarreos_volumen')
            //     ->whereIn('reporte_frente_id', $reportes)
            //     ->sum('volumen');

            $areaTotal = DB::table('acarreos_area')
                ->whereIn('reporte_frente_id', $reportes)
                ->sum('area');

            $metroLinealTotal = DB::table('acarreos_metro_lineal')
                ->whereIn('reporte_frente_id', $reportes)
                ->sum('largo');

            // Agregar al resumen
            $resumen[] = [
                'fecha' => $fecha,
                'total_maquinaria' => $totalMaquinaria,
                'maquinaria_por_tipo' => $maquinariaPorTipo,
                'total_personal' => $totalPersonal,
                'personal_por_puesto' => $personalPorPuesto,
                'acarreos' => [
                    'detalles_volumen' => $detallesVolumen,
                    'volumen' => $volumenTotal,
                    'area' => $areaTotal,
                    'metro_lineal' => $metroLinealTotal,
                ],
            ];
        }


        return response()->json([
            'success' => true,
            'messages' => 'Reportes de whatsapp generados con éxito',
            'data' => $resumen,
        ]);
    }


    public function enviarReporteDiarioWhastappPDF(Request $request)
    {
        // Validar los parámetros
        $request->validate([
            'obra_id' => 'required|exists:obras,id',
            'fecha' => 'required|date',
        ]);

        $obraId = $request->obra_id;
        $fecha = $request->fecha;

        // Obtener los datos del reporte por obra y fecha
        $resumen = $this->obtenerResumenReporte($obraId, $fecha);  // Suponiendo que tienes este método para obtener los datos

        // Generar el PDF
        $dompdf = new Dompdf();
        $dompdf->loadHtml($this->renderizarReporteHTML($resumen)); // Renderiza el HTML para el PDF

        // Opciones de configuración
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Guardar el archivo PDF
        $output = $dompdf->output();
        $fileName = "reporte_" . $obraId . "_" . $fecha . ".pdf";
        $filePath = storage_path('app/public/reportes/' . $fileName);
        file_put_contents($filePath, $output);

        // Retornar la URL para descargar el PDF
        $pdfUrl = asset('storage/reportes/' . $fileName);

        return response()->json([
            'success' => true,
            'messages' => 'Reporte generado con éxito',
            'data' => [
                'pdf_url' => $pdfUrl,
            ],
        ]);
    }

    private function renderizarReporteHTML2($resumen)
    {
        // Aquí renderizas el HTML del reporte, puede ser un simple template o una vista.
        // Supongamos que tienes un método que convierte el resumen a HTML:
        return view('reportes.reporte_pdf', compact('resumen'))->render();
    }









    private function obtenerResumenReporte($obraId, $fecha)
    {
        // Convertir la fecha si viene en otro formato
        $fechaFormateada = date('Y-m-d', strtotime($fecha));

        // Obtener los reportes de la fecha específica
        $reportes = ReporteJefeFrente::where('obra_id', $obraId)
            ->whereDate('created_at', $fechaFormateada)
            ->pluck('id');

        // Si no hay reportes para esa fecha, devolver array vacío
        if ($reportes->isEmpty()) {
            return [];
        }

        // --- MAQUINARIA ---
        // Total de maquinaria
        $totalMaquinaria = DB::table('reportes_maquinaria')
            ->whereIn('reporte_frente_id', $reportes)
            ->count();

        // Maquinaria por tipo
        $maquinariaPorTipo = DB::table('reportes_maquinaria as rm')
            ->join('tipos_maquinaria as tm', 'rm.tipo_maquinaria_id', '=', 'tm.id')
            ->whereIn('rm.reporte_frente_id', $reportes)
            ->select('tm.nombre as tipo_maquinaria', DB::raw('count(*) as total'))
            ->groupBy('tm.nombre')
            ->get();

        // --- PERSONAL ---
        // Total de personal
        $totalPersonal = DB::table('reportes_personal')
            ->whereIn('reporte_frente_id', $reportes)
            ->count();

        // Personal por puesto
        $personalPorPuesto = DB::table('reportes_personal as rp')
            ->join('personal as p', 'rp.personal_id', '=', 'p.id')
            ->join('puestos as pt', 'p.puesto_id', '=', 'pt.id')
            ->whereIn('rp.reporte_frente_id', $reportes)
            ->select('pt.puesto as puesto', DB::raw('count(*) as total'))
            ->groupBy('pt.puesto')
            ->get();

        // --- ACARREOS ---
        // Detalles de volumen
        $detallesVolumen = DB::table('acarreos_volumen as av')
            ->join('materiales as m', 'av.material_id', '=', 'm.id')
            ->join('materiales_uso as mu', 'av.material_uso_id', '=', 'mu.id')
            ->whereIn('av.reporte_frente_id', $reportes)
            ->select(
                'm.material as material',
                'mu.uso as uso_material',
                'av.volumen'
            )
            ->get();

        // Volumen total
        $volumenTotal = DB::table('acarreos_volumen')
            ->whereIn('reporte_frente_id', $reportes)
            ->selectRaw('COALESCE(SUM(viajes), 0) as total_viajes,
            COALESCE(SUM(capacidad), 0) as total_capacidad,
            COALESCE(SUM(volumen), 0) as total_volumen
        ')
            ->first();

        // Área total
        $areaTotal = DB::table('acarreos_area')
            ->whereIn('reporte_frente_id', $reportes)
            ->sum('area');

        // Metro lineal total
        $metroLinealTotal = DB::table('acarreos_metro_lineal')
            ->whereIn('reporte_frente_id', $reportes)
            ->sum('largo');

        // --- AVANCES ---
        // Aquí puedes agregar cualquier otra consulta para avances específicos de la obra

        // --- DATOS DE LA OBRA ---
        $obra = DB::table('obras')
            ->where('id', $obraId)
            ->select('nombre', 'ubicacion')
            ->first();

        // Crear el resumen
        $resumen = [
            'obra' => $obra,
            'fecha' => $fechaFormateada,
            'total_maquinaria' => $totalMaquinaria,
            'maquinaria_por_tipo' => $maquinariaPorTipo,
            'total_personal' => $totalPersonal,
            'personal_por_puesto' => $personalPorPuesto,
            'acarreos' => [
                'detalles_volumen' => $detallesVolumen,
                'volumen' => $volumenTotal,
                'area' => $areaTotal,
                'metro_lineal' => $metroLinealTotal,
            ],
        ];

        return $resumen;
    }

    /**
     * Renderiza el HTML para el PDF del reporte diario
     * 
     * @param array $resumen Datos del resumen para el reporte
     * @return string HTML renderizado
     */
    private function renderizarReporteHTML($resumen)
    {
        // Si no hay datos, mostrar mensaje de no disponible
        if (empty($resumen)) {
            return '<h1>No hay datos disponibles para esta fecha</h1>';
        }

        $logoPath = storage_path('app/public/logos/logo_rod.jpg');

        if (file_exists($logoPath)) {
            $logoData = base64_encode(file_get_contents($logoPath));
            $logoPath = 'data:image/jpeg;base64,' . $logoData;
        } else {
            $logoPath = null;
        }

        //dd($logoPath);

        // Formatear la fecha para mostrar
        $fechaMostrar = date('d/m/Y', strtotime($resumen['fecha']));

        // Convertir los objetos a arrays para facilitar el acceso en la vista
        $maquinariaPorTipo = [];
        foreach ($resumen['maquinaria_por_tipo'] as $item) {
            $maquinariaPorTipo[] = [
                'tipo' => $item->tipo_maquinaria,
                'total' => $item->total
            ];
        }

        $personalPorPuesto = [];
        foreach ($resumen['personal_por_puesto'] as $item) {
            $personalPorPuesto[] = [
                'puesto' => $item->puesto,
                'total' => $item->total
            ];
        }

        $detallesVolumen = [];
        foreach ($resumen['acarreos']['detalles_volumen'] as $item) {
            $detallesVolumen[] = [
                'material' => $item->material,
                'uso' => $item->uso_material,
                'volumen' => $item->volumen
            ];
        }

        // Crear un array con los datos formateados
        $datos = [
            'obra' => $resumen['obra'],
            'logoPath' => $logoPath,
            'fecha' => $fechaMostrar,
            'total_maquinaria' => $resumen['total_maquinaria'],
            'maquinaria_por_tipo' => $maquinariaPorTipo,
            'total_personal' => $resumen['total_personal'],
            'personal_por_puesto' => $personalPorPuesto,
            'acarreos' => [
                'detalles_volumen' => $detallesVolumen,
                'volumen' => $resumen['acarreos']['volumen'],
                'area' => $resumen['acarreos']['area'],
                'metro_lineal' => $resumen['acarreos']['metro_lineal'],
            ],
        ];

        // Usar una vista Blade para el PDF
        return view('reportes.reporte_diario_pdf', compact('datos'))->render();
    }

    /**
     * Genera y envía un PDF con el reporte diario de una obra en una fecha específica
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function reporteDiarioWhastappPDF(Request $request)
    {
        // Validar los parámetros
        $request->validate([
            'obra_id' => 'required|exists:obras,id',
            'fecha' => 'required|date',
        ]);

        $obraId = $request->obra_id;
        $fecha = $request->fecha;

        // Obtener los datos del reporte por obra y fecha
        $resumen = $this->obtenerResumenReporte($obraId, $fecha);

        // Verificar si hay datos
        if (empty($resumen)) {
            return response()->json([
                'success' => false,
                'messages' => 'No hay datos disponibles para esta fecha',
                'data' => null,
            ], 404);
        }

        // Crear una instancia de Dompdf
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new \Dompdf\Dompdf($options);

        // Cargar el HTML
        $dompdf->loadHtml($this->renderizarReporteHTML($resumen));

        // Opciones de configuración
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Guardar el archivo PDF
        $output = $dompdf->output();
        $fileName = "reporte_{$obraId}_{$fecha}.pdf";

        // Asegurar que el directorio existe
        $directorio = storage_path('app/public/reportes');
        if (!file_exists($directorio)) {
            mkdir($directorio, 0755, true);
        }

        $filePath = "{$directorio}/{$fileName}";
        file_put_contents($filePath, $output);

        // Retornar la URL para descargar el PDF
        $pdfUrl = asset("storage/reportes/{$fileName}");

        return response()->json([
            'success' => true,
            'messages' => 'Reporte generado con éxito',
            'data' => [
                'pdf_url' => $pdfUrl,
            ],
        ]);
    }
}
