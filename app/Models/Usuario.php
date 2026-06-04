<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
    protected $table      = 'usuario';
    protected $primaryKey = 'id_usu';
    public    $timestamps = false;

    protected $guarded = ['id_usu'];

    protected $hidden = ['con_usu', 'two_factor_secret'];

    // Indica a Laravel qué columna es la contraseña
    public function getAuthPasswordName(): string
    {
        return 'con_usu';
    }

    public function getAuthPassword(): string
    {
        return $this->con_usu;
    }

    // Relación con la tabla rol
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id_rol');
    }
}
