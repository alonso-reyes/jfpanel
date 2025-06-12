<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class ReportePersonal extends Model
{
    use HasFactory, AsSource, Filterable, Attachable;

    protected $table = 'reportes_personal';

    protected $fillable = [
        'reporte_frente_id',
        'personal_id',
    ];

    public function reporteFrente()
    {
        return $this->belongsTo(ReporteJefeFrente::class, 'reporte_frente_id');
    }

    public function personal()
    {
        return $this->belongsTo(Personal::class, 'personal_id');
    }
}
