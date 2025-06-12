<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;


class Material extends Model
{
    use HasFactory, AsSource, Filterable, Attachable;

    protected $table = 'materiales';

    protected $fillable = [
        'material',
        'obra_id'
    ];
}
