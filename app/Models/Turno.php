<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Turno extends Model
{
    use HasFactory, AsSource, Filterable, Attachable;

    protected $table = 'turnos';

    protected $fillable = [
        'nombre_turno',
        'hora_entrada',
        'hora_salida',
        'obra_id'
    ];
}
