<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class MaterialUso extends Model
{
    use HasFactory, AsSource, Filterable, Attachable;

    protected $table = 'materiales_uso';

    protected $fillable = [
        'uso',
        'obra_id'
    ];
}
