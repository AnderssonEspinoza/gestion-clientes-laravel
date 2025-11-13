<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\HistorialAtencion;
use App\Models\Asignacion;




class ClienteController extends Controller
{
    //
    // Proteger para que solo usuarios logueados puedan acceder
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Mostrar clientes disponibles (sin asignar)
    public function index()
    {
        // Solo mostrar clientes sin asignar
        $clientesDisponibles = Cliente::whereDoesntHave('asignacion')
            ->where('estado', 'pendiente') // solo pendientes
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        return view('clientes.index', compact('clientesDisponibles'));
    }

    // Mostrar detalle de un cliente
    public function show($id)
    {
        $cliente = Cliente::findOrFail($id);
        return view('clientes.show', compact('cliente'));
    }


    // Asignar cliente al usuario logueado y actualizar estado
    public function assign($id)
    {
        $cliente = Cliente::findOrFail($id);
        // Crear registro en asignacions
        $cliente->asignacion()->create([
            'user_id' => auth()->id()
        ]);

        // Respuesta JSON si lo piden (AJAX)
        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('mis-clientes')
        ->with('success', 'Cliente asignado correctamente.');
        
    }



    // Mostrar mis clientes (los asignados al usuario logueado)

    public function misClientes()
    {
        $misClientes = Cliente::whereHas('asignacion', function($query) {
            $query->where('user_id', auth()->id())
                ->whereIn('estado', ['asignado']);  // Solo clientes activos
        })
        ->orderBy('updated_at', 'desc')
        ->paginate(12);


        return view('clientes.mis', compact('misClientes'));
    }




    public function asignarCliente(Cliente $cliente)
    {
        try {
            // Usar transacción para evitar condiciones de carrera
            DB::transaction(function () use ($cliente) {
                
                // Verificar nuevamente que el cliente esté disponible (CRÍTICO)
                $clienteActual = Cliente::lockForUpdate()->find($cliente->id);
                
                if ($clienteActual->user_id !== null) {
                    throw new \Exception('Este cliente ya fue asignado a otro asesor.');
                }
                
                if ($clienteActual->estado !== 'sin_asignar') {
                    throw new \Exception('Este cliente ya no está disponible.');
                }
                
                // Asignar el cliente
                $clienteActual->update([
                    'user_id' => auth()->id(),
                    'estado' => 'pendiente'
                ]);
                
            }, 3); // 3 intentos máximo
            
            return back()->with('success', "Cliente {$cliente->nombre} asignado correctamente.");
            
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
    

      // Nuevo método para actualizar estado
    public function updateEstado(Request $request, $id)
    {
        try {
            $cliente = Cliente::findOrFail($id);
            
            // DEBUG: Log para verificar IDs
            \Log::info("DEBUG - Cliente ID: {$id}, asesor_id: {$cliente->asesor_id}, auth()->id(): " . auth()->id());
            
            // Verificar si existe la columna asesor_id y si está asignada correctamente
            if (isset($cliente->asesor_id) && $cliente->asesor_id !== null) {
                // Solo validar si hay asesor_id asignado
                if ($cliente->asesor_id !== auth()->id()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No tienes permiso para actualizar este cliente'
                    ], 403);
                }
            } else {
                // Si no hay asesor_id, permitir la actualización por ahora (para testing)
                \Log::warning("Cliente {$id} no tiene asesor_id asignado, permitiendo actualización");
            }
            
            // Validar estados usando las claves definidas en el modelo
            $estadosValidos = implode(',', array_keys(Cliente::ESTADOS));
            $request->validate([
                'estado' => 'required|string|in:' . $estadosValidos
            ]);
            
            // Guardar el estado anterior para logs
            $estadoAnterior = $cliente->estado;
            
            // Actualizar el estado
            $cliente->estado = $request->estado;
            $cliente->updated_at = now(); // Forzar actualización del timestamp
            
            // Guardar en la base de datos
            $saved = $cliente->save();
            
            // Log para debugging
            \Log::info("Estado actualizado - Cliente ID: {$id}, Estado anterior: {$estadoAnterior}, Nuevo estado: {$request->estado}, Guardado: " . ($saved ? 'Sí' : 'No'));
            
            if (!$saved) {
                throw new \Exception('No se pudo guardar el cambio en la base de datos');
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado correctamente',
                'nuevo_estado' => $cliente->estado,
                'estado_anterior' => $estadoAnterior,
                'timestamp' => $cliente->updated_at->format('Y-m-d H:i:s')
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos: ' . implode(', ', $e->errors()['estado'] ?? ['Estado no válido'])
            ], 422);
            
        } catch (\Exception $e) {
            \Log::error("Error al actualizar estado del cliente {$id}: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado: ' . $e->getMessage()
            ], 500);
        }
    }
    //Metodo para ver mis clientes
    public function showMisClientes(Cliente $cliente)
    {
        // Aquí puedes agregar cualquier lógica específica para "mis clientes"
        // Por ejemplo, verificar que el cliente pertenezca al usuario actual
        
        return view('clientes.show-mis', compact('cliente'));
    }

    /**
     * Finalizar un cliente (cambiar estado a Finalizado)
     */
public function finalizar(Request $request, $id)
    {
        try {
            // 1. Verificar asignación válida
            $asignacion = Asignacion::where('cliente_id', $id)
                ->where('user_id', auth()->id())
                ->first();

            if (!$asignacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para finalizar este cliente'
                ], 403);
            }

            // 2. Obtener cliente
            $cliente = Cliente::findOrFail($id);

            // 3. Validar que su estado esté en la lista de finalizables
            $estadosFinalizables = ['venta_concretada', 'no_califica', 'inactivo'];

            if (!in_array($cliente->estado, $estadosFinalizables)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes finalizar un cliente con estado: ' . $cliente->estado
                ], 422);
            }

            // 4. Registrar en historial_atencion con fecha_finalizacion
            HistorialAtencion::create([
                'asignacion_id'     => $asignacion->id,
                'resultado'         => $cliente->estado,
                'observaciones'     => $request->input('observaciones', ''),
                'fecha_finalizacion'=> now(),
            ]);

            // 5. Registrar en la tabla reportes
            DB::table('reportes')->insert([
                'cliente_id' => $cliente->id,
                'user_id' => auth()->id(),
                'estado' => $cliente->estado,
                'fecha_finalizacion' => now(),
                'observaciones' => $request->input('observaciones', ''),
            ]);

            // 6. Eliminar la asignación de la tabla asignacions
            //$asignacion->delete();
            //6. Actualizar el estado de la asiganacion a finalizado
           // $asignacion->update(['estado' => 'finalizado']);

            // 7. Actualizar el estado de la asignación
            $asignacion->update(['estado' => 'finalizado']); // Cambiar estado a finalizado

            return response()->json([
                'success' => true,
                'message' => 'Cliente finalizado y registrado en historial',
                'estado_final' => $cliente->estado
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al finalizar: ' . $e->getMessage()
            ], 500);
        }
    }



}