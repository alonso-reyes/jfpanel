<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Puesto extends Model
{
    use HasFactory, AsSource, Filterable, Attachable;

    protected $table = 'puestos';

    // Campos asignables masivamente
    protected $fillable = [
        'puesto',
        'obra_id',
    ];

    public function personal()
    {
        return $this->hasMany(Personal::class, 'puesto_id');
    }
}
