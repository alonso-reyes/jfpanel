<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conceptos;
use App\Models\Destino;
use App\Models\Origen;
use App\Models\Turno;
use Illuminate\Http\Request;

class ConceptosApiController extends Controller
{
    public function get_conceptos(Request $request)
    {
        $conceptos = Conceptos::where('obra_id', $request->obra_id)->get();

        if ($conceptos->isEmpty()) {
            return response()->json([
                'success' => false,
                'messages' => 'No hay conceptos cargados',
                'conceptos' => []
            ]);
        }

        return response()->json([
            'success' => true,
            'messages' => 'Conceptos cargados',
            'conceptos' => $conceptos->map(function ($concepto) {
                return [
                    'id' => $concepto->id,
                    'concepto' => $concepto->nombre,
                    'descripcion' => $concepto->descripcion,
                ];
            })
        ]);
    }
}
