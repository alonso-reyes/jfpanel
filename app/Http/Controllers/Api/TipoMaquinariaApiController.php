<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Particulares;
use App\Models\TipoMaquinaria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TipoMaquinariaApiController extends Controller
{
    public function get_tipos_maquinaria2(Request $request)
    {
        $tipos = TipoMaquinaria::where('obra_id', $request->obra_id)->get();

        if ($tipos->isEmpty()) {
            return response()->json([
                'success' => false,
                'messages' => 'No tipos de maquinaria cargados',
                'catalogo_maquinarias' => []
            ]);
        }

        return response()->json([
            'success' => true,
            'messages' => 'Tipos de maquinaria cargados',
            'catalogo_maquinarias' => $tipos->map(function ($tipo) {
                return [
                    'id' => $tipo->id,
                    'familia' => $tipo->nombre,
                ];
            })
        ]);
    }

    public function get_tipos_maquinaria(Request $request)
    {
        $tipos = TipoMaquinaria::with(['maquinarias' => function ($query) use ($request) {
            $query->where('obra_id', $request->obra_id)
                ->with(['horometros' => function ($query) {
                    $query->orderBy('id', 'desc')->limit(1);
                }]);
        }, 'operadores' => function ($query) use ($request) {
            $query->where('obra_id', $request->obra_id);
        }])
            ->where('obra_id', $request->obra_id)
            ->get();

        $tipos = $tipos->filter(function ($tipo) {
            return !$tipo->maquinarias->isEmpty();
            //&& !$tipo->operadores->isEmpty();
        });

        if ($tipos->isEmpty()) {
            return response()->json([
                'success' => false,
                'messages' => 'No tipos de maquinaria cargados',
                'catalogo_maquinarias' => []
            ]);
        }

        $catalogo_maquinarias = $tipos->map(function ($tipo) {
            return [
                'id' => $tipo->id,
                'familia' => $tipo->nombre,
                'maquinarias' => $tipo->maquinarias->map(function ($maquinaria) {
                    $ultimoHorometro = $maquinaria->horometros->first();
                    return [
                        'id' => $maquinaria->id,
                        'numero_economico' => $maquinaria->numero_economico,
                        'horometro' => [
                            'horometro_inicial' => $ultimoHorometro->horometro_inicial ?? null,
                            'horometro_final' => $ultimoHorometro->horometro_final ?? null,
                        ],
                    ];
                })->values()->toArray(),
                'operadores' => $tipo->operadores->map(function ($operador) {
                    return [
                        'id' => $operador->id,
                        'nombre' => $operador->nombre,
                    ];
                })->values()->toArray(),
            ];
        });

        //Log::info('Catalogo Maquinas:', $catalogo_maquinarias->toArray());

        $catalogo_particulares = Particulares::where('obra_id', $request->obra_id)->get();

        return response()->json([
            'success' => true,
            'messages' => 'Tipos de maquinaria cargados',
            'catalogo_maquinarias' => $catalogo_maquinarias->values()->toArray(),
            'catalogo_particulares' => $catalogo_particulares->values()->toArray()
        ]);
    }
}
