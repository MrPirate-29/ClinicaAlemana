<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medico extends Model
{
    protected $table      = 'medico';
    protected $primaryKey = 'id_med';
    public    $timestamps = false;

    protected $guarded = ['id_med'];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usu', 'id_usu');
    }

    public function especialidades()
    {
        return $this->belongsToMany(
            Especialidad::class,
            'medico_especialidad',
            'id_med',
            'id_esp'
        );
    }

    public function horarios()
    {
        return $this->hasMany(HorarioMedico::class, 'id_med', 'id_med');
    }

    public function citas()
    {
        return $this->hasMany(Cita::class, 'id_med', 'id_med');
    }
}
