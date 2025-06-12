<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Destino;
use App\Models\Maquinaria;
use App\Models\Origen;
use Illuminate\Http\Request;

class AcarreosAguaApiController extends Controller
{
    public function get_pipas(Request $request)
    {
        // Validamos que se envíe el parámetro 'obra_id'
        $request->validate([
            'obra_id' => 'required|integer',
        ]);

        $obraId = $request->obra_id;

        $pipas = Maquinaria::join('tipos_maquinaria', 'maquinarias.tipo_maquinaria_id', '=', 'tipos_maquinaria.id')
            ->where('tipos_maquinaria.acarreo_agua', 1)
            ->where('maquinarias.obra_id', $obraId)
            ->select('maquinarias.*', 'tipos_maquinaria.nombre as tipo_nombre')
            ->get()
            ->map(function ($pipa) {
                return [
                    'id'                     => $pipa->id,
                    'numero_economico'       => $pipa->numero_economico,
                    'modelo'                 => $pipa->modelo,
                    'tipo'                   => $pipa->tipo_nombre,
                    'capacidad'              => $pipa->capacidad,
                    'estado'                 => $pipa->estado,
                    'inactividad'            => $pipa->inactividad,
                    'observaciones'          => $pipa->observaciones,
                    'observaciones_inactividad' => $pipa->observaciones_inactividad,
                ];
            });

        // Retornamos la respuesta en el mismo formato
        return response()->json([
            'success'  => true,
            'messages' => 'Catálogo de pipas cargado',
            'catalogo_pipas' => [
                'pipas' => $pipas,
            ]
        ]);
    }

    public function get_catalogo_acarreos_agua(Request $request)
    {
        // Validamos que se envíe el parámetro 'obra_id'
        $request->validate([
            'obra_id' => 'required|integer',
        ]);

        $obraId = $request->obra_id;

        $pipas = Maquinaria::join('tipos_maquinaria', 'maquinarias.tipo_maquinaria_id', '=', 'tipos_maquinaria.id')
            ->where('tipos_maquinaria.acarreo_agua', 1)
            ->where('maquinarias.obra_id', $obraId)
            ->select('maquinarias.*', 'tipos_maquinaria.nombre as tipo_nombre')
            ->get()
            ->map(function ($pipa) {
                return [
                    'id'                     => $pipa->id,
                    'numero_economico'       => $pipa->numero_economico,
                    'modelo'                 => $pipa->modelo,
                    'tipo'                   => $pipa->tipo_nombre,
                    'capacidad'              => $pipa->capacidad,
                    'estado'                 => $pipa->estado,
                    'inactividad'            => $pipa->inactividad,
                ];
            });

        $origenes = Origen::where('obra_id', $obraId)->get()->map(function ($origen) {
            return [
                'id'      => $origen->id,
                'origen'  => $origen->origen,
            ];
        });

        $destinos = Destino::where('obra_id', $obraId)->get()->map(function ($destino) {
            return [
                'id'       => $destino->id,
                'destino'  => $destino->destino,
            ];
        });

        return response()->json([
            'success'  => true,
            'messages' => 'Catálogos cargados',
            'catalogo' => [
                'origenes'      => $origenes,
                'destinos'      => $destinos,
                'pipas'         => $pipas,
            ]
        ]);
    }
}
