<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class MotivoInactividad extends Model
{
    use HasFactory, AsSource, Filterable, Attachable;

    protected $table = 'catalogo_motivos_inactividad_maquinaria';

    protected $fillable = [
        'motivo_inactividad',
        'obra_id'
    ];
}
