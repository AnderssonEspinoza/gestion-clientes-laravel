@extends('layouts.app')

@section('content')

<div class="container mx-auto p-4 bg-white min-h-screen">
    <div class="block-1 flex justify-between items-center mb-6">
        <h1 class="title text-3xl font-bold text-gray-800"> 📋 Clientes Disponibles </h1>
        <div class="block-refresh flex items-center space-x-4">
            <span id="ultimo-refresh" class=" text-sm text-gray-500"></span>
            <button onclick="refreshClientes()" class="btn-refresh bg-blue-500 text-white px-3 py-2 rounded text-sm hover:bg-blue-600 transition"> 🔄 Actualizar </button>
            <label class="txt-refresh flex items-center">
                <input type="checkbox" id="auto-refresh" checked class="mr-2">
                <span class="text-sm text-gray-600">Auto-actualizar (10s)</span>
            </label>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" id="success-message">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" id="error-message">
            {{ session('error') }}
        </div>
    @endif

    @if($clientesDisponibles->isEmpty())
        <div class="text-center py-12">
            <div class="text-gray-400 text-6xl mb-4">📋</div>
            <h3 class="text-lg font-medium text-gray-800 mb-2">No hay clientes disponibles</h3>
            <p class="text-gray-600">Todos los clientes han sido asignados o no hay clientes nuevos.</p>
            <button onclick="refreshClientes()" class=" mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition"> 🔄 Verificar nuevamente </button>
        </div>
    @else
        <div class="mb-4">
            <p class="text-gray-700">
                <strong>Total disponibles:</strong>
                <span class="text-blue-600 font-semibold">{{ $clientesDisponibles->total() }}</span> clientes
            </p>
        </div>

        <div class="overflow-x-auto bg-white rounded-lg shadow-md table-to-card">
            <table class="min-w-full text-sm text-left text-gray-700">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3">Nombre</th>
                        <th class="px-6 py-3 hidden-mobile">Email</th>
                        <th class="px-6 py-3">Teléfono</th>
                        <th class="px-6 py-3">Estado</th>
                        <th class="px-6 py-3">Creado</th>
                        <th class="px-6 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white" id="clientes-tbody">
                    @foreach($clientesDisponibles as $cliente)
                        <tr id="cliente-{{ $cliente->id }}" class="border-b hover:bg-gray-50 transition cliente-row">
                            <td class="px-6 py-4 font-medium">{{ $cliente->nombre }}</td>
                            <td class="px-6 py-4 hidden-mobile">{{ $cliente->email }}</td>
                            <td class="px-6 py-4">{{ $cliente->telefono ?? '-' }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $estilos = [
                                        'sin_asignar' => 'bg-blue-100 text-blue-800',
                                        'pendiente' => 'bg-yellow-100 text-yellow-800',
                                        'aprobado' => 'bg-green-100 text-green-800',
                                        'rechazado' => 'bg-red-100 text-red-800',
                                        'activo' => 'bg-green-100 text-green-800',
                                        'inactivo' => 'bg-gray-100 text-gray-800',
                                    ];
                                    $iconos = [
                                        'sin_asignar' => '📋',
                                        'pendiente' => '⏳',
                                        'aprobado' => '✅',
                                        'rechazado' => '❌',
                                        'activo' => '✅',
                                        'inactivo' => '⚫',
                                    ];
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-semibold flex items-center gap-1 w-fit {{ $estilos[$cliente->estado] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $iconos[$cliente->estado] ?? 'ℹ️' }} {{ ucfirst($cliente->estado) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-500">
                                {{ $cliente->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 flex flex-wrap gap-3 justify-center">
                                <!-- Botón Ver -->
                                <a href="{{ route('clientes.show', $cliente->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-md transition"> 🔍 Ver </a>
                                <!-- Botón Asignarme mejorado -->
                                <button onclick="asignarCliente({{ $cliente->id }}, '{{ $cliente->nombre }}')" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg shadow-md transition asignar-btn" data-cliente-id="{{ $cliente->id }}"> ✋ Asignarme </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- Vista móvil como tarjetas -->
        <div class="client-card hidden md:none">
            @foreach($clientesDisponibles as $cliente)
                <div class="client-card-item">
                    <h2 class="font-medium">{{ $cliente->nombre }}</h2>
                    <!-- <p class="hiddem-mobile"><strong>Email:</strong> {{ $cliente->email }}</p> -->
                    <p><strong>Teléfono:</strong> {{ $cliente->telefono ?? '-' }}</p>
                    <p><strong>Estado:</strong> {{ $iconos[$cliente->estado] ?? 'ℹ️' }} {{ ucfirst($cliente->estado) }}</p>
                    <p><strong>Creado:</strong> {{ $cliente->created_at->format('d/m/Y H:i') }}</p>

                    <div class="actions">
                        <a href="{{ route('clientes.show', $cliente->id) }}" class="action-btn bg-blue-600 hover:bg-blue-700 text-white">🔍 Ver</a>
                        <button onclick="asignarCliente({{ $cliente->id }}, '{{ $cliente->nombre }}')" class="action-btn bg-green-600 hover:bg-green-700 text-white">✋ Asignarme</button>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $clientesDisponibles->links() }}
        </div>
    @endif
</div>

<!-- Modal de Confirmación -->
<div id="confirmarModal" class="fixed inset-0 bg-gray-500 bg-opacity-50 flex items-center justify-center hidden">
    <div class="modal bg-white p-6 rounded-lg shadow-lg w-96">
        <h3 class="text-lg font-semibold">Confirmar Acción</h3>
        <p class="text-sm text-gray-700 mb-4" id="modalMensaje"></p>
        <div class="container-actions flex justify-end space-x-4">
            <button id="cancelarFinalizar" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">Cancelar</button>
            <button id="confirmarFinalizar" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Confirmar</button>
        </div>
    </div>
</div>


<!-- Contenedor de los toasts -->
<!-- <div id="toast-container" class="fixed bottom-4 right-4 z-50"></div> -->

<style>
    @media (max-width: 745px) {
        .title{
            width: 100%;
        }
        .block-1, .block-refresh{
            flex-direction: column;
            /* gap: 10px; */
        }
        .block-refresh{
            width: 100%;
            align-items: start;
            gap: 10px;
        }
        .txt-refresh, .btn-refresh{
            margin: 0 !important;
        }

        .hidden-mobile{
            display: none;
        }

        /* Cambiar el diseño de la tabla a tarjetas */
        .table-to-card {
            display: none; /* Ocultar la tabla */
        }

        .client-card {
            display: block; /* Mostrar las tarjetas */
            display: flex;
            flex-direction: column;
            gap: 10px;
            background-color: white;
            padding: 16px;
            margin: 8px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .client-card h2 {
            font-size: 1.125rem; /* Tamaño de texto de nombre */
        }

        .client-card .actions {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .client-card .action-btn {
            padding: 8px 16px;
            font-size: 0.875rem;
            border-radius: 8px;
            cursor: pointer;
            width: 50%;
            text-align: center;
        }

        .client-card-item{
            background-color: #D9D2D2;
            padding: 16px;
            margin-bottom: 16px; /* Espacio entre las tarjetas */
            border-radius: 8px; /* Bordes redondeados */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Sombra suave */
            border: 1px solid #e5e7eb; /* Borde gris claro */

        }

    }

    @media (max-width: 450px){
        .modal{
            width: 80vw;
        }
        .actions{
            margin-top: 6px;
        }
        .container-actions{
            justify-content: center;
        }
        #cancelarFinalizar, #confirmarFinalizar{
            font-size: 13px;
            width: 50%;
        }
        .client-card-item{
            font-size: 13px;
        }
        .client-card .action-btn{
            padding: 5px 12px;
            font-size: 12px;
        }

    }
</style>


<!--  -->

<script>
    let autoRefreshInterval;
    let isAssigning = false; // Flag para evitar múltiples asignaciones

    function actualizarUltimoRefresh() {
        const ahora = new Date();
        document.getElementById('ultimo-refresh').textContent = `Actualizado: ${ahora.toLocaleTimeString()}`;
    }

    // Definir la función como 'async' para poder usar await
    async function asignarCliente(clienteId, nombreCliente) {
        console.log('Asignando cliente:', clienteId, nombreCliente); // Verificar en consola

        if (isAssigning) {
            mostrarToast('Ya hay una asignación en proceso, espera un momento.');
            return;
        }

        // Mostrar el modal de confirmación en lugar de un toast
        mostrarModalConfirmacion(`¿Estás seguro de que quieres asignarte el cliente "${nombreCliente}"?`, async () => {
            isAssigning = true;
            const boton = document.querySelector(`[data-cliente-id="${clienteId}"]`);
            const textoOriginal = boton.innerHTML;

            // Deshabilitar el botón y cambiar su texto
            boton.disabled = true;
            boton.innerHTML = '⏳ Asignando...';
            boton.classList.remove('hover:bg-green-700');
            boton.classList.add('bg-gray-400', 'cursor-not-allowed');

            try {
                const response = await fetch(`/clientes/${clienteId}/assign`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({}) // Vacío ya que no necesitamos datos adicionales
                });

                const data = await response.json();

                if (data.success) {
                    // Mostrar mensaje de éxito con notificación verde
                    mostrarMensaje('Cliente asignado correctamente.', 'success');
                    
                    // Quitar la fila del cliente asignado con animación
                    const fila = document.getElementById(`cliente-${clienteId}`);
                    if (fila) {
                        fila.style.transition = 'all 0.3s ease-out';
                        fila.style.backgroundColor = '#dcfce7'; // Verde claro
                        setTimeout(() => {
                            fila.style.opacity = '0';
                            fila.style.transform = 'translateX(-100%)';
                            setTimeout(() => {
                                fila.remove();
                                verificarTablaVacia();
                            }, 300);
                        }, 500);
                    }

                    // Actualizar contador de clientes
                    actualizarContador();
                    
                } else {
                    // Mostrar error específico
                    mostrarMensaje(data.message || 'Este cliente ya fue asignado a otro asesor.', 'error');
                    
                    // Restaurar el botón
                    boton.disabled = false;
                    boton.innerHTML = textoOriginal;
                    boton.classList.add('hover:bg-green-700');
                    boton.classList.remove('bg-gray-400', 'cursor-not-allowed');
                }
            } catch (error) {
                console.error('Error:', error);
                mostrarMensaje('Error de conexión. Intenta nuevamente.', 'error');
                
                // Restaurar botón en caso de error
                boton.disabled = false;
                boton.innerHTML = textoOriginal;
                boton.classList.add('hover:bg-green-700');
                boton.classList.remove('bg-gray-400', 'cursor-not-allowed');
            } finally {
                isAssigning = false;
            }
        });
    }

    // Función para mostrar el Modal de Confirmación
    function mostrarModalConfirmacion(mensaje, callback) {
        // Mostrar el modal y establecer el mensaje
        const modal = document.getElementById('confirmarModal');
        const modalMensaje = document.getElementById('modalMensaje');
        modalMensaje.textContent = mensaje;

        // Mostrar el modal
        modal.classList.remove('hidden');

        // Configurar los botones de confirmar y cancelar
        document.getElementById('confirmarFinalizar').onclick = function() {
            modal.classList.add('hidden'); // Cerrar el modal
            if (callback) callback(); // Ejecutar el callback (asignar el cliente)
        };

        document.getElementById('cancelarFinalizar').onclick = function() {
            modal.classList.add('hidden'); // Cerrar el modal sin hacer nada
        };
    }


    function mostrarMensaje(texto, tipo) {
        // Remover mensajes anteriores
        const mensajesAnteriores = document.querySelectorAll('#success-message, #error-message, .mensaje-dinamico');
        mensajesAnteriores.forEach(msg => msg.remove());

        // Crear nuevo mensaje
        const mensaje = document.createElement('div');
        mensaje.className = `px-4 py-3 rounded mb-4 mensaje-dinamico ${ tipo === 'success' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700' }`;
        mensaje.textContent = texto;

        // Insertar al inicio del container
        const container = document.querySelector('.container');
        const titulo = container.querySelector('h1');
        titulo.insertAdjacentElement('afterend', mensaje);

        // Auto-remover después de 5 segundos
        setTimeout(() => {
            if (mensaje.parentNode) {
                mensaje.style.transition = 'opacity 0.3s ease-out';
                mensaje.style.opacity = '0';
                setTimeout(() => mensaje.remove(), 300);
            }
        }, 5000);
    }

    function verificarTablaVacia() {
        const filas = document.querySelectorAll('.cliente-row');
        if (filas.length === 0) {
            const tbody = document.getElementById('clientes-tbody');
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="text-gray-400 text-4xl mb-4">🎉</div>
                        <h3 class="text-lg font-medium text-gray-800 mb-2">¡No hay más clientes disponibles!</h3>
                        <p class="text-gray-600">Todos los clientes han sido asignados.</p>
                        <button onclick="refreshClientes()" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition"> 🔄 Verificar nuevamente </button>
                    </td>
                </tr>
            `;
        }
    }

    function actualizarContador() {
        // Actualizar contador de clientes disponibles si existe
        const contador = document.querySelector('.text-blue-600');
        if (contador) {
            const filas = document.querySelectorAll('.cliente-row');
            contador.textContent = filas.length;
        }
    }

    async function refreshClientes() {
        try {
            const botonRefresh = document.querySelector('button[onclick="refreshClientes()"]');
            const textoOriginal = botonRefresh.innerHTML;
            botonRefresh.disabled = true;
            botonRefresh.innerHTML = '⏳ Actualizando...'; // Simple reload para obtener datos frescos
            window.location.reload();
        } catch (error) {
            console.error('Error al actualizar:', error);
            mostrarMensaje('Error al actualizar la lista', 'error');

            // Restaurar botón
            botonRefresh.disabled = false;
            botonRefresh.innerHTML = textoOriginal;
        }
    }

    function configurarAutoRefresh() {
        const checkbox = document.getElementById('auto-refresh');

        // Limpiar intervalo anterior
        if (autoRefreshInterval) {
            clearInterval(autoRefreshInterval);
        }

        if (checkbox.checked) {
            autoRefreshInterval = setInterval(() => {
                // Solo refrescar si no hay asignación en proceso
                if (!isAssigning) {
                    refreshClientes();
                }
            }, 10000); // 10 segundos
        }
    }

    // Configurar al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        actualizarUltimoRefresh();
        configurarAutoRefresh();

        // Escuchar cambios en el checkbox
        document.getElementById('auto-refresh').addEventListener('change', configurarAutoRefresh);

        // Deshabilitar auto-refresh cuando el usuario está inactivo
        let inactivityTimer;

        function resetInactivityTimer() {
            clearTimeout(inactivityTimer);
            inactivityTimer = setTimeout(() => {
                const checkbox = document.getElementById('auto-refresh');
                if (checkbox.checked) {
                    checkbox.checked = false;
                    configurarAutoRefresh();
                    mostrarMensaje('Auto-actualización pausada por inactividad', 'info');
                }
            }, 300000); // 5 minutos de inactividad
        }

        // Detectar actividad del usuario
        document.addEventListener('click', resetInactivityTimer);
        document.addEventListener('keypress', resetInactivityTimer);
        resetInactivityTimer();
    });

    // Limpiar interval al salir de la página
    window.addEventListener('beforeunload', function() {
        if (autoRefreshInterval) {
            clearInterval(autoRefreshInterval);
        }
    });

    // Detectar cuando la página pierde/gana foco
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            // Página no visible - pausar auto-refresh
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
            }
        } else {
            // Página visible - reanudar auto-refresh si está activado
            const checkbox = document.getElementById('auto-refresh');
            if (checkbox.checked) {
                configurarAutoRefresh();
                // Refrescar inmediatamente al volver a la pestaña
                setTimeout(refreshClientes, 1000);
            }
        }
    });
</script>


@endsection