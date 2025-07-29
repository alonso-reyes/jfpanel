<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MotivoInactividad;
use Illuminate\Http\Request;

class CatalogoMotivosInactividadMaquinariaApiController extends Controller
{
    public function get_cat_motivos_inactividad(Request $request)
    {
        $motivos_inactividad = MotivoInactividad::where('obra_id', $request->obra_id)->get();

        if ($motivos_inactividad->isEmpty()) {
            return response()->json([
                'success' => false,
                'messages' => 'No hay catalogo de inactividad cargado',
                'motivos_inactividad_maquinaria' => []
            ]);
        }

        return response()->json([
            'success' => true,
            'messages' => 'Catalogo de motivos de inactividad cargados',
            'motivos_inactividad_maquinaria' => $motivos_inactividad->map(function ($motivos) {
                return [
                    'id' => $motivos->id,
                    'motivo_inactividad' => $motivos->motivo_inactividad,
                ];
            })
        ]);
    }
}
