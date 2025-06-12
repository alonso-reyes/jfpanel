<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Obra;
use App\Models\UsuariosJefeFrente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;


class ObraApiController extends Controller
{
    public function getObra(Request $request)
    {
        $obra = Obra::find($request->obra_id);

        if (!$obra) {
            return response()->json([
                'success' => false,
                'messages' => 'No se encontro la información de la obra',
            ]);
        }

        return response()->json([
            'success' => true,
            'messages' => 'Cargado',
            'clave' => $obra->clave,
            'nombre' => $obra->nombre,
            'contrato' => $obra->contrato,
            'ubicacion' => $obra->ubicacion,
            'descripcion' => $obra->descripcion,
        ]);
    }
}
