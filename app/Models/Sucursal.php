<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    use HasFactory;

    protected $table = 'sucursales';

    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
    ];

    // Relación: una sucursal tiene muchos usuarios
    public function usuarios()
    {
        return $this->hasMany(User::class, 'sucursal_id');
    }

    // Solo asesores (role = 'user')
    public function asesores()
    {
        return $this->hasMany(User::class, 'sucursal_id')->where('role', 'user');
    }

    // (Opcional) Solo admins de esa sucursal
    public function admins()
    {
        return $this->hasMany(User::class, 'sucursal_id')->where('role', 'admin');
    }

}
