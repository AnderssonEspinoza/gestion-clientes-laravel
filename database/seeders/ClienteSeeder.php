<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cliente;

class ClienteSeeder extends Seeder
{
    public function run()
    {
        // Array de clientes de ejemplo
        $clientes = [
            ['nombre' => 'Juan Pérez', 'email' => 'juan.perez@example.com', 'telefono' => '987654321', 'estado' => 'pendiente'],
            ['nombre' => 'María López', 'email' => 'maria.lopez@example.com', 'telefono' => '987654322', 'estado' => 'pendiente'],
            ['nombre' => 'Carlos García', 'email' => 'carlos.garcia@example.com', 'telefono' => '987654323', 'estado' => 'pendiente'],
            ['nombre' => 'Ana Torres', 'email' => 'ana.torres@example.com', 'telefono' => '987654324', 'estado' => 'pendiente'],
            ['nombre' => 'Luis Fernández', 'email' => 'luis.fernandez@example.com', 'telefono' => '987654325', 'estado' => 'pendiente'],
            ['nombre' => 'Sofía Martínez', 'email' => 'sofia.martinez@example.com', 'telefono' => '987654326', 'estado' => 'pendiente'],
            ['nombre' => 'Pedro Sánchez', 'email' => 'pedro.sanchez@example.com', 'telefono' => '987654327', 'estado' => 'pendiente'],
            ['nombre' => 'Lucía Romero', 'email' => 'lucia.romero@example.com', 'telefono' => '987654328', 'estado' => 'pendiente'],
            ['nombre' => 'Diego Castro', 'email' => 'diego.castro@example.com', 'telefono' => '987654329', 'estado' => 'pendiente'],
            ['nombre' => 'Valentina Gómez', 'email' => 'valentina.gomez@example.com', 'telefono' => '987654330', 'estado' => 'pendiente'],
            ['nombre' => 'Jorge Ramírez', 'email' => 'jorge.ramirez@example.com', 'telefono' => '987654331', 'estado' => 'pendiente'],
            ['nombre' => 'Camila Herrera', 'email' => 'camila.herrera@example.com', 'telefono' => '987654332', 'estado' => 'pendiente'],
            ['nombre' => 'Ricardo Molina', 'email' => 'ricardo.molina@example.com', 'telefono' => '987654333', 'estado' => 'pendiente'],
            ['nombre' => 'Paula Vargas', 'email' => 'paula.vargas@example.com', 'telefono' => '987654334', 'estado' => 'pendiente'],
            ['nombre' => 'Fernando Ruiz', 'email' => 'fernando.ruiz@example.com', 'telefono' => '987654335', 'estado' => 'pendiente'],
            ['nombre' => 'Elliot Alderson', 'email' => 'elliot.alderson@example.com', 'telefono' => '972680667', 'estado' => 'pendiente'],
        ];

        // Insertar todos los clientes en la base de datos
        foreach ($clientes as $cliente) {
            Cliente::create($cliente);
        }
    }
}
