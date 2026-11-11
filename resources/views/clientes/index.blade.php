@extends('layouts.app')

@section('content')

<div class="container cm-page cm-client-list cm-available" data-cm-refresh-page>
    <header class="cm-page-heading">
        <h1 class="cm-title">Clientes disponibles</h1>
        <p class="cm-page-summary">Revisa los registros y asígnate los clientes que vas a atender.</p>
    </header>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="status" id="success-message">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert" id="error-message">
            {{ session('error') }}
        </div>
    @endif

    <div class="cm-workbar">
        @if(!$clientesDisponibles->isEmpty())
            <p class="cm-result-count">
                <span data-cm-result-count>{{ $clientesDisponibles->total() }}</span> clientes disponibles
            </p>
        @endif
        <div class="cm-refresh">
            <span id="ultimo-refresh" aria-live="off"></span>
            <label class="cm-auto-refresh">
                <input type="checkbox" id="auto-refresh" checked data-cm-focus="auto-refresh">
                <span>Auto-actualizar (10 s)</span>
            </label>
            <button onclick="refreshClientes()" class="btn-refresh" data-cm-focus="refresh">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 11a8 8 0 1 0-2.4 5.7M20 5v6h-6"/></svg>
                Actualizar
            </button>
        </div>
    </div>

    @if($clientesDisponibles->isEmpty())
        <div class="cm-empty">

            <h2 class="text-lg font-medium mb-2">No hay clientes disponibles</h2>
            <p class="text-gray-600">Todos los clientes han sido asignados o no hay clientes nuevos.</p>
            <button onclick="refreshClientes()" class=" mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition"> Verificar nuevamente </button>
        </div>
    @else
        <div class="cm-table-scroll table-to-card" role="region" aria-label="Tabla de clientes disponibles" tabindex="0">
            <table role="table" class="min-w-full text-sm text-left text-gray-700">
                <thead role="rowgroup" class="bg-gray-50 border-b">
                    <tr role="row">
                        <th role="columnheader" scope="col" class="px-6 py-3">Cliente</th>
                        <th role="columnheader" scope="col" class="px-6 py-3">Teléfono</th>
                        <th role="columnheader" scope="col" class="px-6 py-3">Estado</th>
                        <th role="columnheader" scope="col" class="px-6 py-3">Registro</th>
                        <th role="columnheader" scope="col" class="px-6 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody role="rowgroup" class="bg-white" id="clientes-tbody">
                    @foreach($clientesDisponibles as $cliente)
                        <tr role="row" id="cliente-{{ $cliente->id }}" class="cliente-row">
                            <td role="cell" class="cm-record-identity">
                                <div class="cm-record-person">
                                    <span class="cm-record-initial" aria-hidden="true">{{ mb_substr($cliente->nombre, 0, 1) }}</span>
                                    <div>
                                        <strong>{{ $cliente->nombre }}</strong>
                                        <span class="cm-record-email">{{ $cliente->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td role="cell" class="cm-record-phone"><span class="cm-field-label" aria-hidden="true">Teléfono</span>{{ $cliente->telefono ?? '-' }}</td>
                            <td role="cell" class="cm-record-status">
                                <span class="cm-field-label" aria-hidden="true">Estado</span>
                                @php
                                    $estilos = [
                                        'sin_asignar' => 'bg-blue-100 text-blue-800',
                                        'pendiente' => 'bg-yellow-100 text-yellow-800',
                                        'aprobado' => 'bg-green-100 text-green-800',
                                        'rechazado' => 'bg-red-100 text-red-800',
                                        'activo' => 'bg-green-100 text-green-800',
                                        'inactivo' => 'bg-gray-100 text-gray-800',
                                    ];
                                @endphp
                                <span class="cm-record-badge {{ $estilos[$cliente->estado] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($cliente->estado) }}
                                </span>
                            </td>
                            <td role="cell" class="cm-record-date"><span class="cm-field-label" aria-hidden="true">Registro</span>
                                {{ $cliente->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td role="cell" class="cm-row-actions">
                                <!-- Botón Ver -->
                                <a href="{{ route('clientes.show', $cliente->id) }}" class="cm-record-view" data-cm-focus="view-{{ $cliente->id }}">Ver <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 8h10m-4-4 4 4-4 4"/></svg></a>
                                <!-- Botón Asignarme mejorado -->
                                <button onclick="asignarCliente({{ $cliente->id }}, '{{ $cliente->nombre }}')" data-cm-focus="assign-{{ $cliente->id }}" class="asignar-btn cm-record-assign" data-cliente-id="{{ $cliente->id }}"> Asignarme </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $clientesDisponibles->links() }}
        </div>
    @endif
</div>

<!-- Modal de Confirmación -->
<div id="confirmarModal" class="cm-dialog hidden" role="dialog" aria-modal="true" aria-labelledby="index-dialog-title" data-cm-confirmation>
    <div class="modal cm-dialog-panel">
        <h3 id="index-dialog-title">Asignar cliente</h3>
        <p class="text-sm text-gray-700 mb-4" id="modalMensaje"></p>
        <div class="container-actions cm-dialog-actions">
            <button id="cancelarFinalizar" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">Cancelar</button>
            <button id="confirmarFinalizar" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Asignarme</button>
        </div>
    </div>
</div>


<!-- Contenedor de los toasts -->
<!-- <div id="toast-container" class="cm-toasts" aria-live="polite"></div> -->



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
            boton.innerHTML = 'Asignando…';

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
                        fila.style.transition = 'opacity 160ms var(--cm-ease)';
                        fila.style.backgroundColor = '#dcfce7'; // Verde claro
                        setTimeout(() => {
                            fila.style.opacity = '0';
                            setTimeout(() => {
                                fila.dispatchEvent(new Event('cm:before-record-removal', { bubbles: true }));
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
                }
            } catch (error) {
                console.error('Error:', error);
                mostrarMensaje('Error de conexión. Intenta nuevamente.', 'error');
                
                // Restaurar botón en caso de error
                boton.disabled = false;
                boton.innerHTML = textoOriginal;
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
        mensaje.setAttribute('role', tipo === 'success' ? 'status' : 'alert');
        mensaje.textContent = texto;

        // Insertar al inicio del container
        const container = document.querySelector('.container');
        const titulo = container.querySelector('h1');
        titulo.insertAdjacentElement('afterend', mensaje);

        // Auto-remover después de 5 segundos
        setTimeout(() => {
            if (mensaje.parentNode) {
                mensaje.style.transition = 'opacity 150ms var(--cm-ease)';
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
                <tr role="row">
                    <td role="cell" colspan="5" class="cm-empty-cell">
                        <h3 class="text-lg font-medium text-gray-800 mb-2">¡No hay más clientes disponibles!</h3>
                        <p class="text-gray-600">Todos los clientes han sido asignados.</p>
                        <button onclick="refreshClientes()" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition"> Verificar nuevamente </button>
                    </td>
                </tr>
            `;
        }
    }

    function actualizarContador() {
        const contador = document.querySelector('[data-cm-result-count]');
        if (contador) contador.textContent = Math.max(0, Number(contador.textContent) - 1);
    }

    async function refreshClientes() {
        try {
            window.dispatchEvent(new Event('cm:save-refresh-focus'));
            const botonRefresh = document.querySelector('button[onclick="refreshClientes()"]');
            const textoOriginal = botonRefresh.innerHTML;
            botonRefresh.disabled = true;
            botonRefresh.textContent = 'Actualizando…'; // Simple reload para obtener datos frescos
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