<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ejecutar los seeders que necesitas
        $this->call([
            SucursalSeeder::class,
            UserSeeder::class,
            ClienteSeeder::class,
            // Agrega aquí otros seeders si los tienes
        ]);
    }
}
