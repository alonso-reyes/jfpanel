<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ZonaTrabajo;
use Illuminate\Http\Request;

class ZonasTrabajoApiController extends Controller
{
    public function get_zonas_trabajo(Request $request)
    {
        $zonas = ZonaTrabajo::where('obra_id', $request->obra_id)->get();

        if ($zonas->isEmpty()) {
            return response()->json([
                'success' => false,
                'messages' => 'No hay zonas de trabajo cargadas',
                'zonas' => []
            ]);
        }

        return response()->json([
            'success' => true,
            'messages' => 'Zonas de trabajo cargadas',
            'zonas' => $zonas->map(function ($zona) {
                return [
                    'id' => $zona->id,
                    'clave' => $zona->clave,
                    'nombre' => $zona->nombre,
                    'descripcion' => $zona->descripcion,
                    'obra_id' => $zona->obra_id,
                    //'imagen_url' => asset('storage/' . $zona->imagen), // URL pública de la imagen
                    //'imagen_url' => asset('storage/' . str_replace('\\', '/', $zona->imagen)),
                    'imagen_url' => $zona->getImagenUrlAttribute(),
                ];
            })
        ]);
    }


    public function show($id)
    {
        $zona = ZonaTrabajo::findOrFail($id);

        return response()->json([
            'id' => $zona->id,
            'clave' => $zona->clave,
            'nombre' => $zona->nombre,
            'descripcion' => $zona->descripcion,
            'obra_id' => $zona->obra_id,
            //'imagen_url' => $zona->imagen_url, // URL de la imagen
            'imagen_url' => $zona->getImagenUrlAttribute(),
        ]);
    }
}
