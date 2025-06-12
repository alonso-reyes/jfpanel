<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\MaterialUso;
use Illuminate\Http\Request;

class MaterialApiController extends Controller
{
    public function get_materiales(Request $request)
    {
        $materiales = Material::where('obra_id', $request->obra_id)->get();

        if ($materiales->isEmpty()) {
            return response()->json([
                'success' => false,
                'messages' => 'No hay materiales',
                'material' => []
            ]);
        }

        return response()->json([
            'success' => true,
            'messages' => 'Materiales cargados',
            'material' => $materiales->map(function ($material) {
                return [
                    'id' => $material->id,
                    'material' => $material->material,
                ];
            })
        ]);
    }

    public function get_usos_materiales(Request $request)
    {
        $usos = MaterialUso::where('obra_id', $request->obra_id)->get();

        if ($usos->isEmpty()) {
            return response()->json([
                'success' => false,
                'messages' => 'No hay usos de materiales',
                'usos_material' => []
            ]);
        }

        return response()->json([
            'success' => true,
            'messages' => 'Usos de materiales cargados',
            'usos_material' => $usos->map(function ($uso) {
                return [
                    'id' => $uso->id,
                    'uso' => $uso->uso,
                ];
            })
        ]);
    }
}
