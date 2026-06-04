<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    protected $table      = 'paciente';
    protected $primaryKey = 'id_pac';
    public    $timestamps = false;

    protected $guarded = ['id_pac'];

    protected $casts = [
        'priv_pac'   => 'boolean',
        'fecreg_pac' => 'datetime',
    ];

    public function citas()
    {
        return $this->hasMany(Cita::class, 'id_pac', 'id_pac');
    }

    public function gestora()
    {
        return $this->belongsTo(Usuario::class, 'id_usu_gestora', 'id_usu');
    }
}
