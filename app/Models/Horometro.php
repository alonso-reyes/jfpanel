<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horometro extends Model
{
    use HasFactory;

    protected $table = 'horometros';

    protected $fillable = [
        'maquinaria_id',
        'horometro_inicial',
        'horometro_final',
        'parcialidad_turno',
    ];

    /**
     * Relación con Maquinaria (muchos a uno).
     */
    public function maquinaria()
    {
        return $this->belongsTo(Maquinaria::class, 'maquinaria_id');
    }
}
