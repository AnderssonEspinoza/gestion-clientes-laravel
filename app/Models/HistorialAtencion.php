<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialAtencion extends Model
{
    use HasFactory;

    // Nombre de la tabla
    protected $table = 'historial_atencion';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'asignacion_id',
        'resultado',
        'observaciones',
        'fecha_atencion', // 👈 lo añadí porque existe en tu tabla
    ];

    // Casts automáticos
    protected $casts = [
        'fecha_atencion' => 'datetime',
    ];

    // Relación con Asignacion
    public function asignacion()
    {
        return $this->belongsTo(Asignacion::class, 'asignacion_id');
    }
}
