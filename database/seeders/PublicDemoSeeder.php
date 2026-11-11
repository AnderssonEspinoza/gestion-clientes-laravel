<?php

namespace Database\Seeders;

use App\Http\Controllers\Auth\DemoSessionController;
use App\Models\Cliente;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use LogicException;

class PublicDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (! config('demo.enabled') || DB::getDefaultConnection() !== 'public_demo') {
            throw new LogicException('El seeder requiere la base exclusiva de la demo pública.');
        }

        DB::transaction(function (): void {
            $branch = Sucursal::firstOrCreate(['nombre' => 'Sucursal Centro (demo)']);
            $visitor = User::firstOrCreate(['email' => DemoSessionController::EMAIL], [
                'name' => 'Visita de demostración',
                'password' => Str::random(64),
                'role' => 'admin',
                'sucursal_id' => $branch->id,
                'email_verified_at' => now(),
            ]);

            foreach ([
                'Camila Torres', 'Gabriel Herrera', 'Lucía Paredes', 'Mateo Silva',
                'Valentina Ruiz', 'Renato Rojas', 'Sofía Aguilar', 'Joaquín Vargas',
                'Elena Castro', 'Tomás Campos', 'Nora Castillo', 'Andrea Chávez',
                'Mariana León', 'Carolina Ramos', 'Inés Navarro', 'Diego Salazar',
            ] as $index => $name) {
                $client = Cliente::firstOrCreate(['email' => "cliente{$index}@example.test"], [
                    'nombre' => $name,
                    'estado' => $index === 2 ? 'venta_concretada' : 'pendiente',
                ]);

                if ($index > 2) {
                    continue;
                }

                $assignment = $client->asignacion()->firstOrCreate([], [
                    'user_id' => $visitor->id,
                    'estado' => $index === 2 ? 'finalizado' : 'asignado',
                ]);

                if ($index === 2) {
                    DB::table('reportes')->updateOrInsert(
                        ['cliente_id' => $client->id, 'user_id' => $visitor->id],
                        ['estado' => 'venta_concretada', 'fecha_finalizacion' => $client->created_at],
                    );
                    DB::table('historial_atencion')->updateOrInsert(
                        ['asignacion_id' => $assignment->id],
                        ['resultado' => 'venta_concretada', 'created_at' => $client->created_at],
                    );
                }
            }
        });
    }
}
