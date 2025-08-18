<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;

class ExportarMaquinariaInactivaApiController extends Controller
{

    public function exportar_maquinaria_inactiva($obraId, Request $request)
    {
        $spreadsheet = new Spreadsheet();
        // === HOJA 1: Maquinaria INACTIVA ===
        $datos_inactivas = [];
        // Subconsulta para obtener el último horómetro de cada maquinaria
        $ultimoHorometro = DB::table('horometros as h1')
            ->select('h1.id')
            ->whereColumn('h1.maquinaria_id', 'maquinarias.id')
            ->orderBy('h1.id', 'desc')
            ->limit(1);

        // === HOJA 1: Volumen Total por Concepto ===
        $query = DB::table('maquinarias')
            ->join('tipos_maquinaria', 'maquinarias.tipo_maquinaria_id', '=', 'tipos_maquinaria.id')
            ->join('horometros', function ($join) use ($ultimoHorometro) {
                $join->on('horometros.id', '=', DB::raw("({$ultimoHorometro->toSql()})"))
                    ->addBinding($ultimoHorometro->getBindings());
            })
            ->join('catalogo_motivos_inactividad_maquinaria', 'maquinarias.motivo_inactividad_id', '=', 'catalogo_motivos_inactividad_maquinaria.id')
            ->select(
                'maquinarias.numero_economico as numero_economico',
                'tipos_maquinaria.nombre as tipo_maquinaria',
                'horometros.horometro_inicial as horometro_inicial',
                'horometros.horometro_final as horometro_final',
                'catalogo_motivos_inactividad_maquinaria.motivo_inactividad as motivo_inactividad'
            )
            ->where('maquinarias.obra_id', $obraId)
            ->where('maquinarias.estado', 'inactivo');

        // Filtrar por rango de fechas si se envió
        $resultados = $query->get();

        // dd($resultados);
        // exit;

        foreach ($resultados as $r) {
            $datos_inactivas[] = [
                'numero_economico'    => $r->numero_economico,
                'tipo_maquinaria'    => $r->tipo_maquinaria,
                'horometro_inicial'   => $r->horometro_inicial,
                'horometro_final'     => $r->horometro_final,
                'motivo_inactividad'  => $r->motivo_inactividad,
            ];
        }



        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Resumen de maquinaria inactiva');

        // Encabezados
        $sheet->setCellValue('A1', 'Numero economico');
        $sheet->setCellValue('B1', 'Tipo de maquinaria');
        $sheet->setCellValue('C1', 'Horometro inicial');
        $sheet->setCellValue('D1', 'Horometro final');
        $sheet->setCellValue('E1', 'Motivos de inactividad');

        // Datos
        $row = 2;
        foreach ($datos_inactivas as $dato) {
            $sheet->setCellValue("A{$row}", $dato['numero_economico']);
            $sheet->setCellValue("B{$row}", $dato['tipo_maquinaria']);
            $sheet->setCellValue("C{$row}", $dato['horometro_inicial']);
            $sheet->setCellValue("D{$row}", $dato['horometro_final']);
            $sheet->setCellValue("E{$row}", $dato['motivo_inactividad']);
            $row++;
        }


        // === HOJA 2: Maquinaria ACTIVA ===
        $datos_activa = [];
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Resumen de maquinaria activa');
        // Subconsulta para obtener el último horómetro de cada maquinaria
        $ultimoHorometro = DB::table('horometros as h1')
            ->select('h1.id')
            ->whereColumn('h1.maquinaria_id', 'maquinarias.id')
            ->orderBy('h1.id', 'desc')
            ->limit(1);

        // === HOJA 1: Volumen Total por Concepto ===
        $query = DB::table('maquinarias')
            ->join('tipos_maquinaria', 'maquinarias.tipo_maquinaria_id', '=', 'tipos_maquinaria.id')
            ->join('horometros', function ($join) use ($ultimoHorometro) {
                $join->on('horometros.id', '=', DB::raw("({$ultimoHorometro->toSql()})"))
                    ->addBinding($ultimoHorometro->getBindings());
            })
            ->select(
                'maquinarias.numero_economico as numero_economico',
                'tipos_maquinaria.nombre as tipo_maquinaria',
                'horometros.horometro_inicial as horometro_inicial',
                'horometros.horometro_final as horometro_final'
            )
            ->where('maquinarias.obra_id', $obraId)
            ->where('maquinarias.estado', 'activo');

        // Filtrar por rango de fechas si se envió
        $resultados_activas = $query->get();

        // dd($resultados);
        // exit;

        foreach ($resultados_activas as $r) {
            $datos_activa[] = [
                'numero_economico'    => $r->numero_economico,
                'tipo_maquinaria'    => $r->tipo_maquinaria,
                'horometro_inicial'   => $r->horometro_inicial,
                'horometro_final'     => $r->horometro_final,
            ];
        }

        // Encabezados
        $sheet2->setCellValue('A1', 'Numero economico');
        $sheet2->setCellValue('B1', 'Tipo de maquinaria');
        $sheet2->setCellValue('C1', 'Horometro inicial');
        $sheet2->setCellValue('D1', 'Horometro final');

        // Datos
        $row = 2;
        foreach ($datos_activa as $dato) {
            $sheet2->setCellValue("A{$row}", $dato['numero_economico']);
            $sheet2->setCellValue("B{$row}", $dato['tipo_maquinaria']);
            $sheet2->setCellValue("C{$row}", $dato['horometro_inicial']);
            $sheet2->setCellValue("D{$row}", $dato['horometro_final']);
            $row++;
        }

        // === EXPORTAMOS ===
        $fileName = 'resumen_maquinaria.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->setIncludeCharts(true);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"$fileName\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
