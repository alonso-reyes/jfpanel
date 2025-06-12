<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Destino;
use App\Models\Origen;
use App\Models\Turno;
use Illuminate\Http\Request;

class OrigenesApiController extends Controller
{
    public function get_origenes(Request $request)
    {
        $origenes = Origen::where('obra_id', $request->obra_id)->get();

        if ($origenes->isEmpty()) {
            return response()->json([
                'success' => false,
                'messages' => 'No hay origenes cargados',
                'origenes' => []
            ]);
        }

        return response()->json([
            'success' => true,
            'messages' => 'origenes cargados',
            'origenes' => $origenes->map(function ($origen) {
                return [
                    'id' => $origen->id,
                    'origen' => $origen->origen,
                ];
            })
        ]);
    }

    public function get_destinos(Request $request)
    {
        $destinos = Destino::where('obra_id', $request->obra_id)->get();

        if ($destinos->isEmpty()) {
            return response()->json([
                'success' => false,
                'messages' => 'No hay destinos cargados',
                'destinos' => []
            ]);
        }

        return response()->json([
            'success' => true,
            'messages' => 'destinos cargados',
            'destinos' => $destinos->map(function ($destino) {
                return [
                    'id' => $destino->id,
                    'destino' => $destino->destino,
                ];
            })
        ]);
    }
}
