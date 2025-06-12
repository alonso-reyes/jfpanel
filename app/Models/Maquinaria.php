<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Maquinaria extends Model
{
    use HasFactory, AsSource, Filterable, Attachable;

    protected $table = 'maquinarias';

    protected $fillable = [
        'numero_economico',
        'modelo',
        'nombre',
        'tipo_maquinaria_id',
        'capacidad',
        'horometro_inicial',
        'estado',
        'inactividad',
        'observaciones_inactividad',
        'observaciones',
        'obra_id'
    ];

    /**
     * Relación con TipoMaquinaria (muchos a uno).
     */
    public function tiposMaquinaria()
    {
        return $this->belongsTo(TipoMaquinaria::class, 'tipo_maquinaria_id');
    }

    // public function tipoMaquinaria()
    // {
    //     return $this->belongsTo(TipoMaquinaria::class, 'tipo_maquinaria_id');
    // }

    /**
     * Relación con Operadores (muchos a muchos).
     */
    public function operadores()
    {
        return $this->belongsToMany(Operador::class, 'maquinaria_operador', 'maquinaria_id', 'operador_id');
    }

    /**
     * Relación con Horómetros (uno a muchos).
     */
    public function horometros()
    {
        return $this->hasMany(Horometro::class, 'maquinaria_id');
    }

    public function ultimoHorometro()
    {
        return $this->hasOne(Horometro::class)->latestOfMany();
    }

    // Luego puedes acceder así:
    // $maquinaria->ultimoHorometro->horometro_inicial;
}
