<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeguimientoPaciente extends Model
{
    protected $table      = 'seguimiento_paciente';
    protected $primaryKey = 'id_seg';
    public    $timestamps = false;

    protected $guarded = ['id_seg'];

    protected $casts = [
        'fec_proxima_esp'  => 'date',
        'fec_alerta'       => 'date',
        'fec_alerta_admin' => 'datetime',
        'alerta_enviada'   => 'boolean',
        'alerta_admin'     => 'boolean',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'id_pac', 'id_pac');
    }

    public function medico()
    {
        return $this->belongsTo(Medico::class, 'id_med', 'id_med');
    }

    public function reporte()
    {
        return $this->belongsTo(ReporteConsulta::class, 'id_rep', 'id_rep');
    }
}
