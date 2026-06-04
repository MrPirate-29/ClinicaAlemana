<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Especialidad extends Model
{
    protected $table      = 'especialidad';
    protected $primaryKey = 'id_esp';
    public    $timestamps = false;

    protected $guarded = ['id_esp'];

    protected $casts = ['activo_esp' => 'boolean'];

    public function medicos()
    {
        return $this->belongsToMany(Medico::class, 'medico_especialidad', 'id_esp', 'id_med');
    }
}
