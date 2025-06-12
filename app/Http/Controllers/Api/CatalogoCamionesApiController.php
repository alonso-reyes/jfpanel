<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Camion;
use App\Models\CatalogoCamionAcarreo;
use Illuminate\Http\Request;

class CatalogoCamionesApiController extends Controller
{
    public function get_catalogo_camiones_volumen(Request $request)
    {
        $camiones = CatalogoCamionAcarreo::where('obra_id', $request->obra_id)->get();

        if ($camiones->isEmpty()) {
            return response()->json([
                'success' => false,
                'messages' => 'No hay camiones cargados',
                'camiones' => []
            ]);
        }

        return response()->json([
            'success' => true,
            'messages' => 'Camiones cargados',
            'camiones' => $camiones->map(function ($camion) {
                return [
                    'id' => $camion->id,
                    'nombre' => $camion->nombre,
                ];
            })
        ]);
    }
}
