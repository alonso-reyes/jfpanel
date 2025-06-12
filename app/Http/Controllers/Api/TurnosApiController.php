<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Turno;
use Illuminate\Http\Request;

class TurnosApiController extends Controller
{
    public function getTurnos(Request $request)
    {
        $turnos = Turno::where('obra_id', $request->obra_id)->get();

        if ($turnos->isEmpty()) {
            return response()->json([
                'success' => false,
                'messages' => 'No hay turnos cargados',
                'turnos' => []
            ]);
        }

        return response()->json([
            'success' => true,
            'messages' => 'Turnos cargados',
            'turnos' => $turnos->map(function ($turno) {
                return [
                    'id' => $turno->id,
                    'turno' => $turno->nombre_turno,
                    'hora_entrada' => $turno->hora_entrada,
                    'hora_salida' => $turno->hora_salida,
                ];
            })
        ]);
    }
}
