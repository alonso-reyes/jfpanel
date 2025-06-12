<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ReporteJefeFrente;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ZonaTrabajoImageController  extends Controller
{
    public function generateCombinedImage($reporteId)
    {
        try {
            $reporte = ReporteJefeFrente::with(['zonaTrabajo', 'dibujosZonaTrabajo'])
                ->findOrFail($reporteId);

            // Verifica que exista la imagen
            if (!$reporte->zonaTrabajo || !$reporte->zonaTrabajo->imagen) {
                throw new \Exception("No hay imagen registrada");
            }

            // Construye la ruta completa
            $imagePath = storage_path('app/public/' . $reporte->zonaTrabajo->imagen);

            // Verifica que el archivo exista físicamente
            if (!file_exists($imagePath)) {
                throw new \Exception("Archivo de imagen no encontrado: " . $imagePath);
            }

            $manager = new ImageManager(new Driver());
            $image = $manager->read($imagePath);

            // foreach ($reporte->dibujosZonaTrabajo as $dibujo) {
            //     $image->drawPolygon($dibujo->puntos, function ($draw) use ($dibujo) {
            //         $draw->border($dibujo->grosor, $this->argbToRgba($dibujo->color));
            //     });
            // }

            return $image->toPng();

            return (string) $image->toDataUri(); // Esto ya incluye "data:image/png;base64,"

        } catch (\Exception $e) {
            Log::error("Error generando imagen: " . $e->getMessage());
            return null;
        }
    }

    private function isValidImage($path)
    {
        try {
            $manager = new ImageManager(new Driver());
            $manager->read($path);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }


    private function argbToRgba($argb)
    {
        $a = hexdec(substr($argb, 0, 2)) / 255;
        $r = hexdec(substr($argb, 2, 2));
        $g = hexdec(substr($argb, 4, 2));
        $b = hexdec(substr($argb, 6, 2));

        return sprintf('rgba(%d, %d, %d, %.2f)', $r, $g, $b, $a);
    }
}
