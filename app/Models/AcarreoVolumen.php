<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class AcarreoVolumen extends Model
{
    use HasFactory, AsSource, Filterable, Attachable;

    // Nombre de la tabla (opcional si sigue la convención de nombres de Laravel)
    protected $table = 'acarreos_volumen';

    // Campos asignables masivamente
    protected $fillable = [
        'reporte_frente_id',
        'material_id',
        'material_uso_id',
        'origen_id',
        'destino_id',
        'camion_id',
        'viajes',
        'capacidad',
        'volumen',
        'observaciones',
    ];

    // Relación con el modelo ReporteJefeFrente
    public function reporteFrente()
    {
        return $this->belongsTo(ReporteJefeFrente::class, 'reporte_frente_id');
    }

    // Relación con el modelo Material
    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    // Relación con el modelo MaterialUso
    public function materialUso()
    {
        return $this->belongsTo(MaterialUso::class, 'material_uso_id');
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

    // Relación con el modelo Camion
    public function camion()
    {
        return $this->belongsTo(Camion::class);
    }

    public function catalogo_camion()
    {
        return $this->belongsTo(CatalogoCamionAcarreo::class, 'camion_id');
    }
}
