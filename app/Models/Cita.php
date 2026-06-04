<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    protected $table      = 'cita';
    protected $primaryKey = 'id_cit';
    public    $timestamps = false;

    protected $guarded = ['id_cit'];

    protected $casts = [
        'fec_cit'    => 'date',
        'fecreg_cit' => 'datetime',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'id_pac', 'id_pac');
    }

    public function medico()
    {
        return $this->belongsTo(Medico::class, 'id_med', 'id_med');
    }

    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class, 'id_esp', 'id_esp');
    }

    public function gestora()
    {
        return $this->belongsTo(Usuario::class, 'id_usu_gestora', 'id_usu');
    }

    public function reporteConsulta()
    {
        return $this->hasOne(ReporteConsulta::class, 'id_cit', 'id_cit');
    }
}
