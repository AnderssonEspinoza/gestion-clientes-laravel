<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HistorialAtencion;
use App\Models\Cliente;
use App\Models\User;
use App\Models\Asignacion;
use App\Models\Sucursal;
use App\Models\Reporte; // Modelo para la tabla reportes
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function index()
    {
        // 1. Datos historial de atenciones (clientes que han sido atendidos)
        $data = HistorialAtencion::selectRaw('DATE(created_at) as fecha, COUNT(*) as total')
            ->groupBy('fecha')
            ->orderBy('fecha', 'asc')
            ->get();

        // 2. Datos ventas por sucursal (usando reportes en lugar de asignaciones)
        $datos = Sucursal::leftJoin('users', 'sucursales.id', '=', 'users.sucursal_id')
            ->leftJoin('reportes', 'users.id', '=', 'reportes.user_id') // Usar la tabla 'reportes' en lugar de 'asignacions'
            ->leftJoin('clientes', 'reportes.cliente_id', '=', 'clientes.id') // Relacionar con clientes
            ->whereIn('clientes.estado', ['venta_concretada']) // Filtrar por estados de clientes finalizados
            ->selectRaw('sucursales.nombre, COUNT(reportes.cliente_id) as ventas') // Contar las ventas
            ->groupBy('sucursales.nombre') // Agrupar por sucursal
            ->get();


        // 3. Clientes por estado
        $estados = ['pendiente','en_evaluacion','califica','no_califica','venta_concretada','inactivo'];
        $clientesPorEstado = Cliente::select('estado', DB::raw('COUNT(*) as total'))
            ->groupBy('estado')
            ->pluck('total', 'estado')
            ->toArray();

        // Asegúrate de que todos los estados estén presentes en el gráfico
        foreach ($estados as $estado) {
            if (!isset($clientesPorEstado[$estado])) {
                $clientesPorEstado[$estado] = 0;
            }
        }

        // 4. Clientes por asesor (opcional)
        $clientesPorAsesor = DB::table('reportes')
            ->join('users', 'reportes.user_id', '=', 'users.id')
            ->select('users.name', DB::raw('COUNT(reportes.cliente_id) as total'))
            ->groupBy('users.name')
            ->whereNotNull('reportes.cliente_id')  // Asegura que haya cliente asignado
            ->get();


        // 5. Traer todas las sucursales para el dropdown
        $sucursales = Sucursal::all();

        // 6. Traer todos los usuarios para la tabla de asesores
        $users = User::with('sucursal')->get();

        // KPIs para el Dashboard
        // 1. Total Clientes y Delta
        $totalClientes = Cliente::count();
        $totalClientesPrev = Cliente::where('created_at', '<', now()->subMonth())->count();
        $totalClientesDelta = $totalClientesPrev > 0 ? (($totalClientes - $totalClientesPrev) / $totalClientesPrev) * 100 : 0;

        // 2. Ventas Concretadas y Delta
        $ventasConcretadas = Reporte::where('estado', 'venta_concretada')->count();
        $ventasConcretadasPrev = Reporte::where('estado', 'venta_concretada')
            ->where('fecha_finalizacion', '<', now()->subMonth())->count();
        $ventasConcretadasDelta = $ventasConcretadasPrev > 0 ? (($ventasConcretadas - $ventasConcretadasPrev) / $ventasConcretadasPrev) * 100 : 0;

        // 3. Total Asesores y Delta
        $totalAsesores = User::where('role', 'user')->count();
        $totalAsesoresPrev = User::where('role', 'user')->where('created_at', '<', now()->subMonth())->count();
        $totalAsesoresDelta = $totalAsesoresPrev > 0 ? (($totalAsesores - $totalAsesoresPrev) / $totalAsesoresPrev) * 100 : 0;

        // 4. Sucursales y Delta
        $totalSucursales = Sucursal::count();
        $totalSucursalesPrev = Sucursal::where('created_at', '<', now()->subMonth())->count();
        $totalSucursalesDelta = $totalSucursalesPrev > 0 ? (($totalSucursales - $totalSucursalesPrev) / $totalSucursalesPrev) * 100 : 0;

        // 5. Tiempo promedio de atención
        // Obtener el tiempo promedio entre la creación del cliente y la finalización del cliente
        $tiempoPromedioAtencion = Reporte::join('clientes', 'reportes.cliente_id', '=', 'clientes.id')
            ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, clientes.created_at, reportes.fecha_finalizacion)) as tiempo_promedio')
            ->first();

        // Pasando los datos a la vista
        $kpis = [
            'totalClientes' => $totalClientes,
            'totalClientesDelta' => $totalClientesDelta,
            'ventasConcretadas' => $ventasConcretadas,
            'ventasConcretadasDelta' => $ventasConcretadasDelta,
            'totalAsesores' => $totalAsesores,
            'totalAsesoresDelta' => $totalAsesoresDelta,
            'totalSucursales' => $totalSucursales,
            'totalSucursalesDelta' => $totalSucursalesDelta,
            'tiempoPromedioAtencion' => $tiempoPromedioAtencion->tiempo_promedio ?? 0, // Tiempo promedio de atención
        ];

        return view('reporte.index', compact(
            'data', 'datos', 'estados', 'clientesPorEstado', 'clientesPorAsesor', 
            'sucursales', 'users', 'kpis'
        ));
    }

    public function detalleKpi($tipo)
    {
        // Lógica para obtener los detalles del KPI
        // Aquí puedes cargar más datos o hacer cálculos adicionales según el tipo
        return view('reporte.detalle', compact('tipo'));
    }
}
