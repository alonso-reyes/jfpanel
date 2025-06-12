<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class ZonaTrabajoDibujo extends Model
{
    use HasFactory, AsSource, Filterable, Attachable;

    protected $fillable = [
        'reporte_id',
        'zona_trabajo_id',
        'puntos',
        'color',
        'grosor',
        'ruta_imagen'
    ];

    protected $casts = [
        'puntos' => 'array' // Convierte automáticamente el JSON a array
    ];

    public function reporte()
    {
        return $this->belongsTo(ReporteJefeFrente::class);
    }

    public function zonaTrabajo()
    {
        return $this->belongsTo(ZonaTrabajo::class);
    }

    public function getImagenUrlAttribute()
    {
        return $this->imagen_path ? asset('storage/' . $this->imagen_path) : null;
    }

    public function tieneImagen()
    {
        return !empty($this->ruta_imagen) && file_exists(public_path('images/zona_trabajo/' . basename($this->ruta_imagen)));
    }
}
