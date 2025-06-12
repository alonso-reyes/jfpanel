<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class AcarreoArea extends Model
{
    use HasFactory, AsSource, Filterable, Attachable;

    protected $table = 'acarreos_area';

    // Campos asignables masivamente
    protected $fillable = [
        'reporte_frente_id',
        'viajes',
        'largo',
        'ancho',
        'area',
        'observaciones',
    ];

    // Relación con el modelo ReporteJefeFrente
    public function reporteFrente()
    {
        return $this->belongsTo(ReporteJefeFrente::class, 'reporte_frente_id');
    }
}
