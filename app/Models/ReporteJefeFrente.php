<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class ReporteJefeFrente extends Model
{
    use HasFactory, AsSource, Filterable, Attachable;

    protected $table = 'reportes_jefe_frente';

    protected $fillable = [
        'usuario_id',
        'turno_id',
        'hora_inicio_real_actividades',
        'hora_termino_real_actividades',
        'zona_trabajo_id',
        'obra_id',
        'sobrestante',
        'observaciones',
    ];

    // Relación con el modelo Jefes frente
    public function usuario_jefe_frente()
    {
        return $this->belongsTo(UsuariosJefeFrente::class, 'usuario_id');
    }

    // Relación con el modelo Turno
    public function turno()
    {
        return $this->belongsTo(Turno::class);
    }

    // Relación con el modelo ZonaTrabajo
    public function zonaTrabajo()
    {
        return $this->belongsTo(ZonaTrabajo::class);
    }

    // En el modelo ReporteJefeFrente
    public function dibujosZonaTrabajo()
    {
        return $this->hasMany(ZonaTrabajoDibujo::class, 'reporte_id');
    }

    // Relación con el modelo Obra
    public function obra()
    {
        return $this->belongsTo(Obra::class);
    }

    public function acarreosVolumen()
    {
        return $this->hasMany(AcarreoVolumen::class, 'reporte_frente_id');
    }

    // // Relación con AcarreoArea
    public function acarreosArea()
    {
        return $this->hasMany(AcarreoArea::class, 'reporte_frente_id');
    }

    // // Relación con AcarreoMetroLineal
    public function acarreosMetroLineal()
    {
        return $this->hasMany(AcarreoMetroLineal::class, 'reporte_frente_id');
    }

    // // Relación con AcarreoAgua
    public function acarreosAgua()
    {
        return $this->hasMany(AcarreoAgua::class, 'reporte_frente_id');
    }

    public function reporteMaquinaria()
    {
        return $this->hasMany(ReporteMaquinaria::class, 'reporte_frente_id');
    }

    public function reportePersonal()
    {
        return $this->hasMany(ReportePersonal::class, 'reporte_frente_id');
    }

    public function fotografias()
    {
        return $this->hasMany(ReporteFotografia::class, 'reporte_frente_id');
    }
}
