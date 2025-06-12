<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class ReporteFotografia extends Model
{
    use HasFactory, AsSource, Filterable, Attachable;

    protected $table = 'reportes_fotografias';

    protected $fillable = [
        'reporte_frente_id',
        'url',
        'descripcion'
    ];

    public function reporteFrente()
    {
        return $this->belongsTo(ReporteJefeFrente::class, 'reporte_frente_id');
    }
}
