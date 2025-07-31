<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class ReporteMaquinaria extends Model
{
    use HasFactory, AsSource, Filterable, Attachable;

    protected $table = 'reportes_maquinaria';

    protected $fillable = [
        'reporte_frente_id',
        'concepto_id',
        'tipo_maquinaria_id',
        'maquinaria_id',
        'operador_id',
        'horometro_inicial',
        'horometro_final',
        'motivo_inactividad_id',
        'horas_efectivas'
    ];

    public function reporteFrente()
    {
        return $this->belongsTo(ReporteJefeFrente::class, 'reporte_frente_id');
    }

    public function concepto()
    {
        return $this->belongsTo(Conceptos::class, 'concepto_id');
    }

    public function tiposMaquinaria()
    {
        return $this->belongsTo(TipoMaquinaria::class, 'tipo_maquinaria_id');
    }

    public function maquinaria()
    {
        return $this->belongsTo(Maquinaria::class, 'maquinaria_id');
    }

    public function operador()
    {
        return $this->belongsTo(Operador::class, 'operador_id');
    }

    public function motivo_inactividad()
    {
        return $this->belongsTo(MotivoInactividad::class, 'motivo_inactividad_id');
    }

    // En ReporteMaquinaria.php
    public function scopeByObra($query, $obraId)
    {
        return $query->whereHas('reporteFrente', function ($q) use ($obraId) {
            $q->where('obra_id', $obraId);
        });
    }
}
