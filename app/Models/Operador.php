<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;


class Operador extends Model
{
    use HasFactory, AsSource, Filterable, Attachable;

    protected $table = 'operadores';

    protected $fillable = [
        'clave_trabajador', 
        'nombre',
        'obra_id'
    ];

    /**
     * Relación con Maquinarias (muchos a muchos).
     */
    public function maquinarias()
    {
        return $this->belongsToMany(Maquinaria::class, 'maquinaria_operador', 'operador_id', 'maquinaria_id');
    }

    /**
     * Relación con Tipos de Maquinaria (muchos a muchos).
     */
    public function tiposMaquinaria()
    {
        return $this->belongsToMany(TipoMaquinaria::class, 'operador_tipo_maquinaria', 'operador_id', 'tipo_maquinaria_id');
    }
}
