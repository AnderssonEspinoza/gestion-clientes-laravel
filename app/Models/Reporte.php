<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reporte extends Model
{
    use HasFactory;

    protected $table = 'reportes';  // Indicar el nombre de la tabla

    protected $fillable = [
        'cliente_id',
        'user_id',
        'estado',
        'fecha_finalizacion',
        'observaciones',
    ];

    public $timestamps = true; // Si usas created_at y updated_at
}
