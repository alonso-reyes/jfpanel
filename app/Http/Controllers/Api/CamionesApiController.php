<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Camion;
use Illuminate\Http\Request;

class CamionesApiController extends Controller
{
    public function get_camiones(Request $request)
    {
        $camiones = Camion::where('obra_id', $request->obra_id)->get();

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
                    'clave' => $camion->clave,
                    'tipo' => $camion->tipo,
                    'largo' => $camion->largo,
                    'ancho' => $camion->ancho,
                    'altura' => $camion->altura,
                    'capacidad' => $camion->capacidad,
                    'inspeccion_mecanica' => $camion->inspeccion_mecanica,
                    'propietario' => $camion->propietario,
                ];
            })
        ]);
    }

}
