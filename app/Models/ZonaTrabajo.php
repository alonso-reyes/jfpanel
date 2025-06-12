<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class ZonaTrabajo extends Model
{
    use HasFactory, AsSource, Filterable, Attachable;

    protected $table = 'zonas_trabajo';

    protected $fillable = [
        'clave',
        'nombre',
        'descripcion',
        'imagen',
        'obra_id',
    ];

    public function getImagenUrlAttribute()
    {
        return $this->imagen ? asset('storage/' . $this->imagen) : null;
    }
}
