<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HorarioMedico extends Model
{
    protected $table      = 'horario_medico';
    protected $primaryKey = 'id_hor';
    public    $timestamps = false;

    protected $guarded = ['id_hor'];

    protected $casts = ['activo_hor' => 'boolean'];

    public function medico()
    {
        return $this->belongsTo(Medico::class, 'id_med', 'id_med');
    }
}
