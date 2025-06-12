<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;
use Laravel\Sanctum\HasApiTokens;

class UsuariosJefeFrente extends Model
{
    use HasFactory, AsSource, Filterable, Attachable, HasApiTokens;

    protected $table = 'usuarios_jefe_frente';

    protected $fillable = [
        'tipo_usuario',
        'nombre',
        'usuario',
        'password',
        'obra_id'
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * Asegurarse de que la contraseña se encripte automáticamente.
     */
    // public function setPasswordAttribute($value)
    // {
    //     $this->attributes['password'] = bcrypt($value);
    // }
}
