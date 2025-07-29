<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Conceptos extends Model
{
    //
    use HasFactory, AsSource, Filterable, Attachable;

    protected $table = 'conceptos_presupuesto';

    protected $fillable = [
        'tipo',
        'nombre',
        'descripcion',
        'unidad',
        'cantidad',
        'factor_abundamiento',
        'obra_id'
    ];


    /**
     * Obtener conceptos con sus factores para un select
     */
    public static function getConceptosConFactor($obraId, $tipo = 'CAPITULO')
    {
        return static::where('tipo', $tipo)
            ->where('obra_id', $obraId)
            ->orderBy('descripcion')
            ->get()
            ->mapWithKeys(function ($concepto) {
                return [
                    $concepto->id => [
                        'descripcion' => $concepto->descripcion,
                        'factor_abundamiento' => $concepto->factor_abundamiento
                    ]
                ];
            });
    }

    /**
     * Obtener solo las opciones para el select
     */
    public static function getOpcionesSelect($obraId, $tipo = 'CAPITULO')
    {
        return static::where('tipo', $tipo)
            ->where('obra_id', $obraId)
            ->orderBy('descripcion')
            ->pluck('descripcion', 'id')
            ->toArray();
    }
}
