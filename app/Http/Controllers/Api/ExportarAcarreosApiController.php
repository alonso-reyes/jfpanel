<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Exports\VolumenPorConceptoExport;
use App\Models\Obra;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Layout;
use PhpOffice\PhpSpreadsheet\Chart\ChartSeries;
use PhpOffice\PhpSpreadsheet\Chart\ChartSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExportarAcarreosApiController extends Controller
{

    public function exportar($obraId, Request $request)
    {
        $clave = Obra::where('id', $obraId)->value('clave');

        if (!$clave) {
            abort(404, 'Obra no encontrada o no tiene clave');
        }

        $fecha_inicio = $request->query('fecha_inicio');
        $fecha_termino = $request->query('fecha_termino');
        $tipos = ['acarreos_volumen' => 'Volumen'];
        $datos = [];

        // === HOJA 1: Volumen Total por Concepto ===
        foreach ($tipos as $tabla => $etiqueta) {
            $query = DB::table($tabla)
                ->join('reportes_jefe_frente', "$tabla.reporte_frente_id", '=', 'reportes_jefe_frente.id')
                ->join('conceptos_presupuesto', "$tabla.concepto_id", '=', 'conceptos_presupuesto.id')
                ->select(
                    'conceptos_presupuesto.nombre as concepto',
                    'conceptos_presupuesto.factor_abundamiento',
                    DB::raw('SUM(volumen) as total'),
                    DB::raw("'$etiqueta' as tipo")
                )
                ->whereNotNull("$tabla.concepto_id")
                ->where('reportes_jefe_frente.obra_id', $obraId);

            // Filtro de fechas
            if (!empty($fecha_inicio) && !empty($fecha_termino)) {
                $query->whereBetween('reportes_jefe_frente.created_at', [
                    $fecha_inicio . ' 00:00:00',
                    $fecha_termino . ' 23:59:59'
                ]);
            } elseif (!empty($fecha_inicio)) {
                $query->whereBetween('reportes_jefe_frente.created_at', [
                    $fecha_inicio . ' 00:00:00',
                    now()->endOfDay()
                ]);
            }

            $resultados = $query
                ->groupBy('conceptos_presupuesto.nombre', 'conceptos_presupuesto.factor_abundamiento')
                ->get();

            foreach ($resultados as $r) {
                $datos[] = [
                    'concepto'            => $r->concepto,
                    'volumen'             => $r->total,
                    'factor_abundamiento' => $r->factor_abundamiento,
                    'volumen_suelto'      => $r->factor_abundamiento != 0
                        ? $r->total / $r->factor_abundamiento
                        : 0,
                ];
            }
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Resumen de Volumen por Concepto');

        // Encabezados y datos HOJA 1
        $sheet->fromArray(['Concepto', 'Volumen Total', 'Factor Abundamiento', 'Volumen Suelto'], null, 'A1');
        $row = 2;
        foreach ($datos as $dato) {
            $sheet->setCellValue("A{$row}", $dato['concepto']);
            $sheet->setCellValue("B{$row}", $dato['volumen']);
            $sheet->setCellValue("C{$row}", $dato['factor_abundamiento']);
            $sheet->setCellValue("D{$row}", $dato['volumen_suelto']);
            $row++;
        }

        // === AGREGAR GRÁFICA DE BARRAS ===

        if (!empty($datos)) {
            $pointCount = $row - 2;

            $dataSeriesLabels = [
                new DataSeriesValues('String', "'Resumen de Volumen por Concepto'!\$A\$2:\$A\$" . ($row - 1), null, $pointCount)
            ];

            $xAxisTickValues = [
                new DataSeriesValues('String', "'Resumen de Volumen por Concepto'!\$A\$2:\$A\$" . ($row - 1), null, $pointCount)
            ];

            // $dataSeriesValues = [
            //     new DataSeriesValues('Number', "'Resumen de Volumen por Concepto'!\$B\$2:\$B\$" . ($row - 1), null, $pointCount)
            // ];

            $dataSeriesValues = [
                new DataSeriesValues('Number', "'Resumen de Volumen por Concepto'!\$D\$2:\$D\$" . ($row - 1), null, $pointCount)
            ];


            // 3. Configurar la serie de datos
            $series = new DataSeries(
                DataSeries::TYPE_BARCHART,
                DataSeries::GROUPING_STANDARD,
                range(0, count($dataSeriesValues) - 1),
                $dataSeriesLabels,
                $xAxisTickValues,
                $dataSeriesValues
            );

            // 4. Crear el gráfico completo
            $plotArea = new PlotArea(null, [$series]);
            $chart = new Chart(
                'chart1',
                new Title('Volumen por Concepto'),
                new Legend(),
                $plotArea,
                true,
                DataSeries::EMPTY_AS_GAP,
                new Title('Conceptos'),
                new Title('Volumen (m³)')
            );

            // 5. Posicionar el gráfico
            $chart->setTopLeftPosition('F2');
            $chart->setBottomRightPosition('P20');

            // 6. Añadir estilo a las celdas de datos (opcional pero recomendado)
            foreach (range('A', 'D') as $col) {
                $sheet->getStyle($col . '1')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['argb' => 'FFD9D9D9']]
                ]);
            }
            $sheet->addChart($chart);
        }




        // === HOJA 2: Volumen Total por Material ===
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Resumen por Material');

        $materiales = DB::table('acarreos_volumen')
            ->join('reportes_jefe_frente', 'acarreos_volumen.reporte_frente_id', '=', 'reportes_jefe_frente.id')
            ->join('materiales', 'acarreos_volumen.material_id', '=', 'materiales.id')
            ->select(
                'materiales.material as material',
                'materiales.factor_abundamiento as factor_abundamiento',
                DB::raw('SUM(volumen) as total')
            )
            ->whereNotNull('acarreos_volumen.material_id')
            ->where('reportes_jefe_frente.obra_id', $obraId);

        // Filtro de fechas
        if (!empty($fecha_inicio) && !empty($fecha_termino)) {
            $materiales->whereBetween('reportes_jefe_frente.created_at', [
                $fecha_inicio . ' 00:00:00',
                $fecha_termino . ' 23:59:59'
            ]);
        } elseif (!empty($fecha_inicio)) {
            $materiales->whereBetween('reportes_jefe_frente.created_at', [
                $fecha_inicio . ' 00:00:00',
                now()->endOfDay()
            ]);
        }

        $materiales = $materiales
            ->groupBy('materiales.material', 'materiales.factor_abundamiento')
            ->get();

        $sheet2->fromArray(['Material', 'Volumen Total', 'Factor Abundamiento', 'Volumen Suelto'], null, 'A1');
        $row = 2;
        foreach ($materiales as $mat) {
            $volumenSuelto = $mat->factor_abundamiento != 0 ? $mat->total / $mat->factor_abundamiento : 0;
            $sheet2->setCellValue("A{$row}", $mat->material);
            $sheet2->setCellValue("B{$row}", $mat->total);
            $sheet2->setCellValue("C{$row}", $mat->factor_abundamiento);
            $sheet2->setCellValue("D{$row}", $volumenSuelto);
            $row++;
        }

        // === HOJA 2: Gráfica de pastel ===
        if (!empty($materiales)) {
            $lastDataRow2 = count($materiales) + 1;

            // Nombres de materiales en eje (columna A)
            $xAxisTickValues2 = [
                new DataSeriesValues('String', "'Resumen por Material'!\$A\$2:\$A\$" . $lastDataRow2)
            ];

            // Valores de volumen suelto (columna D)
            $dataSeriesValues2 = [
                new DataSeriesValues('Number', "'Resumen por Material'!\$D\$2:\$D\$" . $lastDataRow2)
            ];

            $series2 = new DataSeries(
                DataSeries::TYPE_PIECHART,     // tipo pastel
                null,                          // agrupamiento
                range(0, count($dataSeriesValues2) - 1),
                [],                            // <-- aquí NO van las etiquetas
                $xAxisTickValues2,             // <-- las etiquetas van en los ticks
                $dataSeriesValues2             // valores
            );

            // $series2->setShowValues(true);

            $plotArea2 = new PlotArea(null, [$series2]);
            $chart2 = new Chart(
                'chart2',
                new Title('Distribución de Volumen por Material'),
                new Legend(Legend::POSITION_RIGHT, null, false),
                $plotArea2,
                true,
                DataSeries::EMPTY_AS_GAP
            );

            $chart2->setTopLeftPosition('F2');
            $chart2->setBottomRightPosition('P20');

            $sheet2->addChart($chart2);
        }





        // === HOJA 3: Resumen de viajes por camión ===
        $datos_tipo_camion = [];
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setTitle('Resumen de viajes por camión');

        foreach ($tipos as $tabla => $etiqueta) {
            $resultados = DB::table("$tabla as a")
                ->join('reportes_jefe_frente as rjf', 'a.reporte_frente_id', '=', 'rjf.id')
                ->join('catalogo_camiones_acarreos as cca', 'a.camion_id', '=', 'cca.id')
                ->select(
                    'cca.nombre as tipo_camion',
                    DB::raw('SUM(a.viajes) as total_viajes'),
                    DB::raw("'$etiqueta' as tipo")
                )
                ->where('rjf.obra_id', $obraId);

            if (!empty($fecha_inicio) && !empty($fecha_termino)) {
                $resultados->whereBetween('rjf.created_at', [
                    $fecha_inicio . ' 00:00:00',
                    $fecha_termino . ' 23:59:59'
                ]);
            } elseif (!empty($fecha_inicio)) {
                $resultados->whereBetween('rjf.created_at', [
                    $fecha_inicio . ' 00:00:00',
                    now()->endOfDay()
                ]);
            }

            $resultados = $resultados
                ->groupBy('cca.nombre')
                ->get();

            foreach ($resultados as $r) {
                $datos_tipo_camion[] = [
                    'tipo_camion' => $r->tipo_camion ?? 'Desconocido',
                    'total_viajes' => $r->total_viajes,
                ];
            }
        }

        $sheet3->setCellValue('A1', 'Tipo de camión');
        $sheet3->setCellValue('B1', 'Total de viajes');
        $row = 2;
        foreach ($datos_tipo_camion as $dato) {
            $sheet3->setCellValue("A{$row}", $dato['tipo_camion']);
            $sheet3->setCellValue("B{$row}", $dato['total_viajes']);
            $row++;
        }

        // === EXPORTAMOS ===
        $fechaActual = now()->format('Y-m-d');
        $fileName = "{$clave}_reporte_avance_{$fechaActual}.xlsx";
        $writer = new Xlsx($spreadsheet);
        $writer->setIncludeCharts(true);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName);
    }


    public function exportar_old($obraId, Request $request)
    {
        $fecha_inicio = $request->query('fecha_inicio');
        $fecha_termino = $request->query('fecha_termino');
        //dd($end);
        $tipos = ['acarreos_volumen' => 'Volumen'];
        $datos = [];

        // === HOJA 1: Volumen Total por Concepto ===
        foreach ($tipos as $tabla => $etiqueta) {
            $query = DB::table($tabla)
                ->join('reportes_jefe_frente', "$tabla.reporte_frente_id", '=', 'reportes_jefe_frente.id')
                ->join('conceptos_presupuesto', "$tabla.concepto_id", '=', 'conceptos_presupuesto.id')
                ->select(
                    'conceptos_presupuesto.nombre as concepto',
                    'conceptos_presupuesto.factor_abundamiento',
                    DB::raw('SUM(volumen) as total'),
                    DB::raw("'$etiqueta' as tipo")
                )
                ->whereNotNull("$tabla.concepto_id")
                ->where('reportes_jefe_frente.obra_id', $obraId);

            // Filtrar por rango de fechas si se envió
            if (!empty($fecha_inicio) && !empty($fecha_termino)) {
                // Desde inicio hasta fin incluyendo ambos días
                $query->whereBetween('reportes_jefe_frente.created_at', [
                    $fecha_inicio . ' 00:00:00',
                    $fecha_termino . ' 23:59:59'
                ]);
            } elseif (!empty($fecha_inicio)) {
                // Desde fecha_inicio hasta hoy
                $query->whereBetween('reportes_jefe_frente.created_at', [
                    $fecha_inicio . ' 00:00:00',
                    now()->endOfDay() // Carbon helper para hoy 23:59:59
                ]);
            }

            $resultados = $query
                ->groupBy('conceptos_presupuesto.nombre', 'conceptos_presupuesto.factor_abundamiento')
                ->get();

            foreach ($resultados as $r) {
                $datos[] = [
                    'concepto'            => $r->concepto,
                    'volumen'             => $r->total,
                    'factor_abundamiento' => $r->factor_abundamiento,
                    'volumen_suelto'      => $r->factor_abundamiento != 0
                        ? $r->total / $r->factor_abundamiento
                        : 0,
                ];
            }
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Resumen de Volumen por Concepto');

        // Encabezados
        $sheet->setCellValue('A1', 'Concepto');
        $sheet->setCellValue('B1', 'Volumen Total');
        $sheet->setCellValue('C1', 'Factor Abundamiento');
        $sheet->setCellValue('D1', 'Volumen Suelto');

        // Datos
        $row = 2;
        foreach ($datos as $dato) {
            $sheet->setCellValue("A{$row}", $dato['concepto']);
            $sheet->setCellValue("B{$row}", $dato['volumen']);
            $sheet->setCellValue("C{$row}", $dato['factor_abundamiento']);
            $sheet->setCellValue("D{$row}", $dato['volumen_suelto']);
            $row++;
        }

        // === HOJA 2: Volumen Total por Material ===
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Resumen por Material');

        $materiales = DB::table('acarreos_volumen')
            ->join('reportes_jefe_frente', 'acarreos_volumen.reporte_frente_id', '=', 'reportes_jefe_frente.id')
            ->join('materiales', 'acarreos_volumen.material_id', '=', 'materiales.id')
            ->select(
                'materiales.material as material',
                'materiales.factor_abundamiento as factor_abundamiento',
                DB::raw('SUM(volumen) as total')
            )
            ->whereNotNull('acarreos_volumen.material_id')
            ->where('reportes_jefe_frente.obra_id', $obraId);

        // Aplicar filtro de fechas
        if (!empty($fecha_inicio) && !empty($fecha_termino)) {
            $materiales->whereBetween('reportes_jefe_frente.created_at', [
                $fecha_inicio . ' 00:00:00',
                $fecha_termino . ' 23:59:59'
            ]);
        } elseif (!empty($fecha_inicio)) {
            $materiales->whereBetween('reportes_jefe_frente.created_at', [
                $fecha_inicio . ' 00:00:00',
                now()->endOfDay()
            ]);
        }

        // Agrupar y obtener los resultados
        $materiales = $materiales
            ->groupBy('materiales.material', 'materiales.factor_abundamiento')
            ->get();

        // Escribir encabezados
        $sheet2->setCellValue('A1', 'Material');
        $sheet2->setCellValue('B1', 'Volumen Total');
        $sheet2->setCellValue('C1', 'Factor Abundamiento');
        $sheet2->setCellValue('D1', 'Volumen Suelto');

        // Escribir datos
        $row = 2;
        foreach ($materiales as $mat) {
            $volumenSuelto = $mat->factor_abundamiento != 0
                ? $mat->total / $mat->factor_abundamiento
                : 0;

            $sheet2->setCellValue("A{$row}", $mat->material);
            $sheet2->setCellValue("B{$row}", $mat->total);
            $sheet2->setCellValue("C{$row}", $mat->factor_abundamiento);
            $sheet2->setCellValue("D{$row}", $volumenSuelto);
            $row++;
        }


        // === HOJA 3: Numero Total de viajes por Tipo de Camion ===
        $datos_tipo_camion = [];
        // Crear hoja 3 y poner encabezados
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setTitle('Resumen de viajes por camión');

        foreach ($tipos as $tabla => $etiqueta) {
            $resultados = DB::table("$tabla as a")
                ->join('reportes_jefe_frente as rjf', 'a.reporte_frente_id', '=', 'rjf.id')
                ->join('catalogo_camiones_acarreos as cca', 'a.camion_id', '=', 'cca.id')
                ->select(
                    'cca.nombre as tipo_camion',
                    DB::raw('SUM(a.viajes) as total_viajes'),
                    DB::raw("'$etiqueta' as tipo")
                )
                ->where('rjf.obra_id', $obraId);

            // Aplicar filtro de fechas
            if (!empty($fecha_inicio) && !empty($fecha_termino)) {
                $resultados->whereBetween('rjf.created_at', [
                    $fecha_inicio . ' 00:00:00',
                    $fecha_termino . ' 23:59:59'
                ]);
            } elseif (!empty($fecha_inicio)) {
                $resultados->whereBetween('rjf.created_at', [
                    $fecha_inicio . ' 00:00:00',
                    now()->endOfDay()
                ]);
            }

            $resultados = $resultados
                ->groupBy('cca.nombre')
                ->get();

            foreach ($resultados as $r) {
                $datos_tipo_camion[] = [
                    'tipo_camion' => $r->tipo_camion ?? 'Desconocido',
                    'total_viajes' => $r->total_viajes,
                ];
            }
        }

        $sheet3->setCellValue('A1', 'Tipo de camión');
        $sheet3->setCellValue('B1', 'Total de viajes');

        // Insertar datos
        $row = 2;
        foreach ($datos_tipo_camion as $dato) {
            $sheet3->setCellValue("A{$row}", $dato['tipo_camion']);
            $sheet3->setCellValue("B{$row}", $dato['total_viajes']);
            $row++;
        }

        // === EXPORTAMOS ===
        $fileName = 'total_volumenes.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->setIncludeCharts(true);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"$fileName\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }


    public function exportar_reportejf_individual($reporteId)
    {
        $tipos = ['acarreos_volumen' => 'Volumen'];
        $datos = [];

        // === HOJA 1: Volumen Total por Concepto ===
        foreach ($tipos as $tabla => $etiqueta) {
            $resultados = DB::table($tabla)
                ->join('reportes_jefe_frente', "$tabla.reporte_frente_id", '=', 'reportes_jefe_frente.id')
                ->join('conceptos_presupuesto', "$tabla.concepto_id", '=', 'conceptos_presupuesto.id')
                ->select(
                    'conceptos_presupuesto.nombre as concepto',
                    'conceptos_presupuesto.factor_abundamiento',
                    DB::raw('SUM(volumen) as total'),
                    DB::raw("'$etiqueta' as tipo")
                )
                ->whereNotNull("$tabla.concepto_id")
                ->where('reportes_jefe_frente.id', $reporteId) // <- aquí se cambió
                ->groupBy('conceptos_presupuesto.nombre', 'conceptos_presupuesto.factor_abundamiento')
                ->get();


            foreach ($resultados as $r) {
                $datos[] = [
                    'concepto' => $r->concepto,
                    'volumen' => $r->total,
                    'factor_abundamiento' => $r->factor_abundamiento,
                    'volumen_suelto'  => $r->factor_abundamiento != 0
                        ? $r->total / $r->factor_abundamiento
                        : 0,
                ];
            }
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Resumen de Volumen por Concepto');

        // Encabezados
        $sheet->setCellValue('A1', 'Concepto');
        $sheet->setCellValue('B1', 'Volumen Total');
        $sheet->setCellValue('C1', 'Factor Abundamiento');
        $sheet->setCellValue('D1', 'Volumen Suelto');

        // Datos
        $row = 2;
        foreach ($datos as $dato) {
            $sheet->setCellValue("A{$row}", $dato['concepto']);
            $sheet->setCellValue("B{$row}", $dato['volumen']);
            $sheet->setCellValue("C{$row}", $dato['factor_abundamiento']);
            $sheet->setCellValue("D{$row}", $dato['volumen_suelto']);
            $row++;
        }


        // === HOJA 2: Volumen Total por Material ===
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Resumen por Material');

        // Consulta por material
        $materiales = DB::table('acarreos_volumen')
            ->join('reportes_jefe_frente', 'acarreos_volumen.reporte_frente_id', '=', 'reportes_jefe_frente.id')
            ->join('materiales', 'acarreos_volumen.material_id', '=', 'materiales.id')
            ->select(
                'materiales.material as material',
                'materiales.factor_abundamiento as factor_abundamiento',
                DB::raw('SUM(volumen) as total')
            )
            ->whereNotNull('acarreos_volumen.material_id')
            ->where('reportes_jefe_frente.id', $reporteId) // <- también aquí
            ->groupBy('materiales.material', 'materiales.factor_abundamiento')
            ->get();


        // Escribir encabezados
        $sheet2->setCellValue('A1', 'Material');
        $sheet2->setCellValue('B1', 'Volumen Total');
        $sheet2->setCellValue('C1', 'Factor Abundamiento');
        $sheet2->setCellValue('D1', 'Volumen Suelto');

        // Escribir datos
        $row = 2;
        foreach ($materiales as $mat) {
            $volumenSuelto = $mat->factor_abundamiento != 0
                ? $mat->total / $mat->factor_abundamiento
                : 0;

            $sheet2->setCellValue("A{$row}", $mat->material);
            $sheet2->setCellValue("B{$row}", $mat->total);
            $sheet2->setCellValue("C{$row}", $mat->factor_abundamiento);
            $sheet2->setCellValue("D{$row}", $volumenSuelto);
            $row++;
        }


        // === HOJA 3: Numero Total de viajes por Tipo de Camion ===
        $datos_tipo_camion = [];
        // Crear hoja 3 y poner encabezados
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setTitle('Resumen de viajes por camión');

        foreach ($tipos as $tabla => $etiqueta) {
            $resultados = DB::table("$tabla as a")
                ->join('reportes_jefe_frente as rjf', 'a.reporte_frente_id', '=', 'rjf.id')
                ->join('catalogo_camiones_acarreos as cca', 'a.camion_id', '=', 'cca.id')
                ->select(
                    'cca.nombre as tipo_camion',
                    DB::raw('SUM(a.viajes) as total_viajes'),
                    DB::raw("'$etiqueta' as tipo")
                )
                ->where('rjf.id', $reporteId)
                ->groupBy('cca.nombre')
                ->get();

            foreach ($resultados as $r) {
                $datos_tipo_camion[] = [
                    'tipo_camion' => $r->tipo_camion ?? 'Desconocido',
                    'total_viajes' => $r->total_viajes,
                ];
            }
        }

        $sheet3->setCellValue('A1', 'Tipo de camión');
        $sheet3->setCellValue('B1', 'Total de viajes');

        // Insertar datos
        $row = 2;
        foreach ($datos_tipo_camion as $dato) {
            $sheet3->setCellValue("A{$row}", $dato['tipo_camion']);
            $sheet3->setCellValue("B{$row}", $dato['total_viajes']);
            $row++;
        }

        // === EXPORTAMOS ===
        $fileName = 'total_volumenes.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->setIncludeCharts(true);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"$fileName\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }


    public function exportar2($obraId)
    {
        $tipos = [
            'acarreos_volumen' => 'Volumen'
        ];

        $datos = [];

        foreach ($tipos as $tabla => $etiqueta) {
            $resultados = DB::table($tabla)
                ->join('reportes_jefe_frente', "$tabla.reporte_frente_id", '=', 'reportes_jefe_frente.id')
                ->join('conceptos_presupuesto', "$tabla.concepto_id", '=', 'conceptos_presupuesto.id')
                ->select(
                    'conceptos_presupuesto.nombre as concepto',
                    DB::raw('SUM(volumen) as total'),
                    DB::raw("'$etiqueta' as tipo")
                )
                ->whereNotNull("$tabla.concepto_id")
                ->where('reportes_jefe_frente.obra_id', $obraId)
                ->groupBy('conceptos_presupuesto.nombre')
                ->get();

            foreach ($resultados as $r) {
                $datos[] = [
                    'concepto' => $r->concepto,
                    'volumen' => $r->total,

                ];
            }
        }

        // Creamos Excel usando PhpSpreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Resumen de Volumen por Concepto');

        // Encabezados
        $sheet->setCellValue('A1', 'Concepto');
        $sheet->setCellValue('B1', 'Volumen Total');

        // Datos
        $row = 2;
        foreach ($datos as $dato) {
            $sheet->setCellValue("A{$row}", $dato['concepto']);
            $sheet->setCellValue("B{$row}", $dato['volumen']);
            $row++;
        }

        // Crear la gráfica
        $lastRow = count($datos) + 1; // +1 por los encabezados

        $dataSeriesLabels = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$B$1', null, 1), // 'Volumen Total'
        ];

        $xAxisTickValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$A$2:$A$' . $lastRow, null, count($datos)), // Conceptos
        ];

        $dataSeriesValues = [
            new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$B$2:$B$' . $lastRow, null, count($datos)), // Valores
        ];

        $series = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_STANDARD,
            range(0, count($dataSeriesValues) - 1),
            $dataSeriesLabels,
            $xAxisTickValues,
            $dataSeriesValues
        );

        $plotArea = new PlotArea(null, [$series]);
        $legend = new Legend();
        $chart = new Chart(
            'chart1',
            new Title('Volumen por Concepto'),
            $legend,
            $plotArea,
            true,
            DataSeries::EMPTY_AS_GAP,
            new Title('Conceptos'),
            new Title('Volumen')
        );

        $chart->setTopLeftPosition('D2');
        $chart->setBottomRightPosition('L20');
        $sheet->addChart($chart);

        // Writer con soporte para gráficas
        $writer = new Xlsx($spreadsheet);
        $writer->setIncludeCharts(true);

        // Cabeceras para descargar el archivo
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="resumen_acarreos.xlsx"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
