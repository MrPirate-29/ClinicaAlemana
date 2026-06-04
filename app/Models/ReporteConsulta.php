<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReporteConsulta extends Model
{
    protected $table      = 'reporte_consulta';
    protected $primaryKey = 'id_rep';
    public    $timestamps = false;

    protected $guarded = ['id_rep'];

    protected $casts = [
        'paciente_asistio' => 'boolean',
        'fec_atencion'     => 'date',
    ];

    public function cita()
    {
        return $this->belongsTo(Cita::class, 'id_cit', 'id_cit');
    }

    public function medico()
    {
        return $this->belongsTo(Medico::class, 'id_med', 'id_med');
    }

    public function seguimiento()
    {
        return $this->hasOne(SeguimientoPaciente::class, 'id_rep', 'id_rep');
    }
}
