<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Obra;
use App\Models\Turno;
use App\Models\ZonaTrabajo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GeneralesApiController extends Controller
{
    public function get_catalogo_generales(Request $request)
    {
        // Obtener la obra
        $obra = Obra::find($request->obra_id);

        if (!$obra) {
            return response()->json([
                'success' => false,
                'messages' => 'No se encontró la información de la obra',
                'catalogo_generales' => []
            ]);
        }

        // Obtener los turnos
        $turnos = Turno::where('obra_id', $request->obra_id)->get();

        // Obtener las zonas de trabajo
        $zonas = ZonaTrabajo::where('obra_id', $request->obra_id)->get();

        // Estructurar la respuesta
        $catalogo_generales = [
            'obra' => [
                'clave' => $obra->clave,
                'nombre' => $obra->nombre,
                'contrato' => $obra->contrato,
                'ubicacion' => $obra->ubicacion,
                'descripcion' => $obra->descripcion,
            ],
            'turnos' => $turnos->isEmpty() ? [] : $turnos->map(function ($turno) {
                return [
                    'id' => $turno->id,
                    'turno' => $turno->nombre_turno,
                    'hora_entrada' => $turno->hora_entrada,
                    'hora_salida' => $turno->hora_salida,
                ];
            })->values()->toArray(),
            'zonas' => $zonas->isEmpty() ? [] : $zonas->map(function ($zona) {
                return [
                    'id' => $zona->id,
                    'clave' => $zona->clave,
                    'nombre' => $zona->nombre,
                    'descripcion' => $zona->descripcion,
                    'obra_id' => $zona->obra_id,
                    'imagen_url' => $zona->getImagenUrlAttribute(),
                    //'imagen_url' => $zona->imagen_url,
                    //'imagen_url' => asset('storage/' . str_replace('\\', '/', $zona->imagen)),
                ];
            })->values()->toArray(),
        ];

        // Verificar si hay datos
        if (empty($catalogo_generales['turnos']) && empty($catalogo_generales['zonas'])) {
            return response()->json([
                'success' => false,
                'messages' => 'No hay datos generales cargados',
                'catalogo_generales' => []
            ]);
        }

        return response()->json([
            'success' => true,
            'messages' => 'Datos generales cargados',
            'catalogo_generales' => $catalogo_generales
        ]);
    }
}
