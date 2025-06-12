<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class AcarreoMetroLineal extends Model
{
    use HasFactory, AsSource, Filterable, Attachable;
    // Nombre de la tabla (opcional si sigue la convención de nombres de Laravel)
    protected $table = 'acarreos_metro_lineal';

    // Campos asignables masivamente
    protected $fillable = [
        'reporte_frente_id',
        'viajes',
        'largo',
        'observaciones',
    ];

    // Relación con el modelo ReporteJefeFrente
    public function reporteFrente()
    {
        return $this->belongsTo(ReporteJefeFrente::class, 'reporte_frente_id');
    }
}
