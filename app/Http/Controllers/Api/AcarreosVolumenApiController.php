<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Origen;
use App\Models\Destino;
use App\Models\Material;
use App\Models\MaterialUso;
use App\Models\Camion;
use App\Models\CatalogoCamionAcarreo;
use Illuminate\Http\Request;

class AcarreosVolumenApiController extends Controller
{
    public function get_catalogos_volumen(Request $request)
    {
        // Validamos que se envíe el parámetro 'obra_id'
        $request->validate([
            'obra_id' => 'required|integer',
        ]);

        $obraId = $request->obra_id;

        // Consultamos cada catálogo y aplicamos el mismo formato que usabas en los otros controladores
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

        $materiales = Material::where('obra_id', $obraId)->get()->map(function ($material) {
            return [
                'id'       => $material->id,
                'material' => $material->material,
            ];
        });

        $usos_material = MaterialUso::where('obra_id', $obraId)->get()->map(function ($uso) {
            return [
                'id'  => $uso->id,
                'uso' => $uso->uso,
            ];
        });

        $camiones = Camion::where('obra_id', $obraId)->get()->map(function ($camion) {
            return [
                'id'                    => $camion->id,
                'clave'                 => $camion->clave,
                'tipo'                  => $camion->tipo,
                'largo'                 => $camion->largo,
                'ancho'                 => $camion->ancho,
                'altura'                => $camion->altura,
                'capacidad'             => $camion->capacidad,
                'inspeccion_mecanica'   => $camion->inspeccion_mecanica,
                'propietario'           => $camion->propietario,
            ];
        });

        $tipos_camion = CatalogoCamionAcarreo::where('obra_id', $obraId)->get()->map(function ($tipos_camion) {
            return [
                'id'                    => $tipos_camion->id,
                'nombre'                 => $tipos_camion->nombre,
            ];
        });

        // Retornamos la respuesta unificada
        return response()->json([
            'success'  => true,
            'messages' => 'Catálogos cargados',
            'catalogo' => [
                'origenes'      => $origenes,
                'destinos'      => $destinos,
                'materiales'    => $materiales,
                'usos_material' => $usos_material,
                'camiones'      => $camiones,
                'tipos_camion'      => $tipos_camion,
            ]
        ]);
    }
}
