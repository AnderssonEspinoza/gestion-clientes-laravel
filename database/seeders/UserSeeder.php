<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Sucursal;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'andersson@akana.com'],
            [
                'name' => 'Andersson',
                'role' => 'admin',
                'password' => Hash::make('1321'),
                'sucursal_id' => null // el admin puede no tener sucursal
            ]
        );

        // Traer todas las sucursales
        $sucursales = Sucursal::all();

        for ($i = 1; $i <= 5; $i++) {
            User::updateOrCreate(
                ['email' => "asesor$i@akana.com"], // busca por email
                [
                    'name' => "Asesor $i",
                    'role' => 'user',
                    'password' => Hash::make('123456'),
                    'sucursal_id' => $sucursales->random()->id,// asigna la sucursal correspondiente
                ]
            );

        }
    }
}