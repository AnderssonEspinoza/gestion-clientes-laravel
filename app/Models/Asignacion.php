<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asignacion extends Model
{
    protected $fillable = ['cliente_id', 'user_id', 'estado'];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function historiales()
    {
        return $this->hasMany(HistorialAtencion::class, 'asignacion_id');
    }
}
