<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    protected $table      = 'notificacion';
    protected $primaryKey = 'id_not';
    public    $timestamps = false;

    protected $guarded = ['id_not'];

    protected $casts = [
        'leido_not' => 'boolean',
        'fec_not'   => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usu', 'id_usu');
    }
}
