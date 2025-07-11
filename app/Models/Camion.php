<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Camion extends Model
{
    use HasFactory, AsSource, Filterable, Attachable;

    protected $table = 'camiones';

    protected $fillable = [
        'clave',
        'tipo',
        'largo',
        'ancho',
        'altura',
        'capacidad',
        'inspeccion_mecanica',
        'propietario',
        'obra_id'
    ];
}
