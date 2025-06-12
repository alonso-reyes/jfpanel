<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Personal extends Model
{
    use HasFactory, AsSource, Filterable, Attachable;

    protected $table = 'personal';

    protected $fillable = [
        'nombre',
        'puesto_id',
        'obra_id'
    ];

    public function puesto()
    {
        return $this->belongsTo(Puesto::class, 'puesto_id');  // Relación inversa con 'puesto_id'
    }
}
