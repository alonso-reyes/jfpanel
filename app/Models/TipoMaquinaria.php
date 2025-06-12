<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class TipoMaquinaria extends Model
{
    use HasFactory, AsSource, Filterable, Attachable;

    protected $table = 'tipos_maquinaria';

    protected $fillable = [
        'nombre',
        'obra_id',
        'acarreo_agua'
    ];

    /**
     * Relación con Maquinaria (uno a muchos).
     */
    public function maquinarias()
    {
        return $this->hasMany(Maquinaria::class, 'tipo_maquinaria_id');
    }

    /**
     * Relación con Operadores (muchos a muchos).
     */
    public function operadores()
    {
        return $this->belongsToMany(Operador::class, 'operador_tipo_maquinaria', 'tipo_maquinaria_id', 'operador_id');
    }
}
