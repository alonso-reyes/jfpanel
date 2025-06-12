<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Obra extends Model
{
    use HasFactory, AsSource, Filterable, Attachable;
    //
    protected $table = 'obras';

    protected $fillable = ['clave', 'nombre', 'contrato', 'ubicacion', 'descripcion'];
}
