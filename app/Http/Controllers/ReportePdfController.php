<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Maquinaria;
use App\Models\ReporteJefeFrente;
use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReportePdfController extends Controller
{
    public function generate($id)
    {
        $reporte = ReporteJefeFrente::with([
            'usuario_jefe_frente',
            'turno',
            'zonaTrabajo',
            'dibujosZonaTrabajo',
            'obra',
            'acarreosVolumen',
            'acarreosArea',
            'acarreosMetroLineal',
            'acarreosAgua',
            'reporteMaquinaria.concepto', // Asegúrate de cargar estas relaciones
            'reporteMaquinaria.operador',
            'reporteMaquinaria.maquinaria',
            'reportePersonal',
            'fotografias'
        ])->findOrFail($id);

        $maquinas = Maquinaria::with('tiposMaquinaria')->get();

        // Ruta del logo (ajusta según donde lo hayas guardado)
        $logoPath = storage_path('app/public/logos/logo_rod.jpg');

        // Genera la imagen combinada
        //$imageController = new ZonaTrabajoImageController();
        //$combinedImage = $imageController->generateCombinedImage($id);

        // Verificar si existe el logo
        if (!file_exists($logoPath)) {
            $logoPath = null; // O una ruta alternativa
        }

        // $imagenesDibujos = [];
        // foreach ($reporte->dibujosZonaTrabajo as $dibujo) {
        //     if ($dibujo->tieneImagen()) {
        //         $imagenesDibujos[] = asset('images/zona_trabajo/' . basename($dibujo->ruta_imagen));
        //     }
        // }

        // $imagenesDibujos = null;
        // foreach ($reporte->dibujosZonaTrabajo as $dibujo) {
        //     if ($dibujo->tieneImagen()) {
        //         $imagenesDibujos = asset('images/zona_trabajo/' . basename($dibujo->ruta_imagen));
        //         break; // Detenerse después de encontrar la primera imagen válida
        //     }
        // }

        /// CODIGO PARA CARGAR IMAGENES DE DIBUJOS DE ZONA DE TRABAJO DE FORM LOCAL
        // $imageSrc = null;
        // foreach ($reporte->dibujosZonaTrabajo as $dibujo) {
        //     if (!empty($dibujo->ruta_imagen)) {
        //         try {
        //             $filename = basename($dibujo->ruta_imagen);
        //             $publicPath = public_path('images/zona_trabajo/' . $filename);

        //             if (file_exists($publicPath)) {
        //                 $imageData = file_get_contents($publicPath);
        //                 $imageSrc = 'data:image/jpeg;base64,' . base64_encode($imageData);
        //                 break; // Tomamos la primera imagen válida y salimos del bucle
        //             }
        //         } catch (\Exception $e) {
        //             Log::error("Error cargando imagen: " . $e->getMessage());
        //         }
        //     }
        // }

        $imageSrc = null;
        foreach ($reporte->dibujosZonaTrabajo as $dibujo) {
            if (!empty($dibujo->ruta_imagen)) {
                $imageSrc = $dibujo->ruta_imagen;
                break; // Tomamos la primera imagen válida y salimos del bucle
            }
        }

        $pdf = PDF::loadView('pdf.reporte', [
            'reporte' => $reporte,
            'maquinas' => $maquinas,
            'logoPath' => $logoPath,
            'imageSrc' => $imageSrc
        ])
            ->setPaper('a4', 'portrait')
            // ->setOptions([
            //     'isRemoteEnabled' => true,
            //     'isHtml5ParserEnabled' => true,
            //     'isPhpEnabled' => true // Necesario para algunas operaciones
            // ]);
            ->setOption('isRemoteEnabled', true)
            ->setOption('isPhpEnabled', true)
            ->setOption('isHtml5ParserEnabled', true);

        return $pdf->stream("reporte-{$reporte->id}.pdf"); // Ver en el navegador
        //return $pdf->download("reporte-{$reporte->id}.pdf");
    }
}
