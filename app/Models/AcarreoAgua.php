<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class AcarreoAgua extends Model
{
    use HasFactory, AsSource, Filterable, Attachable;
    // Nombre de la tabla (opcional si sigue la convención de nombres de Laravel)
    protected $table = 'acarreos_agua';

    // Campos asignables masivamente
    protected $fillable = [
        'reporte_frente_id',
        'maquinaria_id',
        'origen_id',
        'destino_id',
        'viajes',
        'observaciones',
    ];

    // Relación con el modelo ReporteJefeFrente
    public function reporteFrente()
    {
        return $this->belongsTo(ReporteJefeFrente::class, 'reporte_frente_id');
    }

    // Relación con el modelo Maquinaria
    public function maquinaria()
    {
        return $this->belongsTo(Maquinaria::class);
    }

    // Relación con el modelo Origen
    public function origen()
    {
        return $this->belongsTo(Origen::class);
    }

    // Relación con el modelo Destino
    public function destino()
    {
        return $this->belongsTo(Destino::class);
    }
}
