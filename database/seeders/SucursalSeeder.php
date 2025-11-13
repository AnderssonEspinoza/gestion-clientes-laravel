<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sucursal;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SucursalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Crear 9 sucursales
        Sucursal::insert([
            ['nombre' => 'Sucursal 1', 'direccion' => 'Av. Principal 123', 'telefono' => '123456789'],
            ['nombre' => 'Sucursal 2', 'direccion' => 'Calle Secundaria 456', 'telefono' => '987654321'],
            ['nombre' => 'Sucursal 3', 'direccion' => 'Av. El Sol 789', 'telefono' => '555555555'],
            ['nombre' => 'Sucursal 4', 'direccion' => 'Calle Luna 101', 'telefono' => '111222333'],
            ['nombre' => 'Sucursal 5', 'direccion' => 'Calle Estrella 202', 'telefono' => '222333444'],
            ['nombre' => 'Sucursal 6', 'direccion' => 'Av. Mar 303', 'telefono' => '333444555'],
            ['nombre' => 'Sucursal 7', 'direccion' => 'Calle Sol 404', 'telefono' => '444555666'],
            ['nombre' => 'Sucursal 8', 'direccion' => 'Av. Norte 505', 'telefono' => '555666777'],
            ['nombre' => 'Sucursal 9', 'direccion' => 'Calle Este 606', 'telefono' => '666777888'],
        ]);
    }
}
