<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Destino;
use App\Models\Maquinaria;
use App\Models\Origen;
use App\Models\Personal;
use Illuminate\Http\Request;

class PersonalApiController extends Controller
{
    public function get_personal(Request $request)
    {
        $request->validate([
            'obra_id' => 'required|integer',
        ]);

        $obraId = $request->obra_id;

        $personal = Personal::join('puestos', 'personal.puesto_id', '=', 'puestos.id')
            ->where('personal.obra_id', $obraId)
            ->select('personal.*', 'puestos.puesto as puesto')
            ->get()
            ->map(function ($persona) {
                return [
                    'id'         => $persona->id,
                    'nombre'     => $persona->nombre,
                    'puesto'     => $persona->puesto,
                ];
            });


        return response()->json([
            'success'  => true,
            'messages' => 'Catálogo de personal y usuarios cargado',
            'personal' => $personal,
        ]);
    }
}
