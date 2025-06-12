<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class CatalogoCamionAcarreo extends Model
{
    use HasFactory, AsSource, Filterable, Attachable;

    protected $table = 'catalogo_camiones_acarreos';

    protected $fillable = [
        'nombre',
        'obra_id',
    ];

    /**
     * Relación: este camión pertenece a una obra.
     */
    public function obra()
    {
        return $this->belongsTo(Obra::class);
    }
}
