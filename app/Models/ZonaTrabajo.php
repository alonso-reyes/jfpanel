<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;
use Illuminate\Support\Str;

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
        if (!$this->imagen) {
            return null;
        }

        // Si la imagen ya es una URL (ej. Cloudinary), simplemente la regresamos
        if (Str::startsWith($this->imagen, ['http://', 'https://'])) {
            return $this->imagen;
        }

        // Si no es URL, se asume que está en storage local
        return asset('storage/' . str_replace('\\', '/', $this->imagen));
        //return $this->imagen ? asset('storage/' . $this->imagen) : null;
    }
}
