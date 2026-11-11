@extends('layouts.app')

@section('content')

<div class="cm-page cm-client-list cm-assigned">
    <header class="cm-page-heading">
        <h1 class="cm-title">Mis clientes asignados</h1>
        <p class="cm-page-summary">Consulta el estado de tus clientes y continúa su atención.</p>
    </header>
    
    @if(session('success'))
        <div role="status" class="bg-green-200 text-green-800 p-2 mb-4 rounded">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div role="alert" class="bg-red-200 text-red-800 p-2 mb-4 rounded">
            {{ session('error') }}
        </div>
    @endif
    
    @if($misClientes->isEmpty())
        <p class="cm-empty">No tienes clientes asignados.</p>
    @else
        <div class="cm-table-scroll table-responsive" role="region" aria-label="Tabla de clientes asignados" tabindex="0">
            <table role="table" class="min-w-full text-sm md:text-base">
                <thead role="rowgroup">
                    <tr role="row" class="bg-gray-200">
                        <th role="columnheader" scope="col" class="border px-4 py-2">Cliente</th>
                        <th role="columnheader" scope="col" class="border px-4 py-2">Teléfono</th>
                        <th role="columnheader" scope="col" class="border px-4 py-2">Estado</th>
                        <th role="columnheader" scope="col" class="border px-4 py-2">Acciones</th>
                    </tr>
                </thead>
                <tbody role="rowgroup">
                    @foreach($misClientes as $cliente)
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
                            <select aria-label="Estado del cliente" class="form-select form-select-sm estado-select"
                                    data-cliente-id="{{ $cliente->id }}">
                                @foreach(App\Models\Cliente::ESTADOS as $key => $label)
                                    <option value="{{ $key }}"
                                            {{ $cliente->estado == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </td>

                        <td role="cell" class="cm-row-actions">
                            <a href="{{ route('mis-clientes.show', $cliente->id) }}"                                 
                               class="inline-block bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded">                                 
                                Ver                             
                            </a>

                            {{-- Finalizar --}}
                            <form id="finalizarForm" data-id="{{ $cliente->id }}" method="POST" class="inline-block">
                                @csrf
                                <button type="button" 
                                        class="bg-orange-500 hover:bg-red-600 text-white px-3 py-1 rounded finalizarBtn" 
                                        data-id="{{ $cliente->id }}">
                                    Finalizar
                                </button>
                            </form>

                            @if($cliente->telefono)
                                <a href="#"
                                   onclick="abrirWhatsApp('{{ $cliente->telefono }}', '{{ $cliente->nombre }}')"
                                   class="inline-block bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded">
                                    WhatsApp
                                </a>
                            @else
                                <span class="inline-block bg-gray-400 text-white px-3 py-1 rounded cursor-not-allowed">
                                    Sin teléfono
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    @endif
</div>

<!-- Contenedor de notificaciones -->
<div id="toast-container" class="cm-toasts" aria-live="polite"></div>

<!-- Modal de Confirmación -->
<div id="finalizarModal" class="cm-dialog hidden" role="dialog" aria-modal="true" aria-labelledby="mis-dialog-title" data-cm-confirmation>
    <div class="modal cm-dialog-panel">
        <h3 id="mis-dialog-title">Finalizar atención</h3>
        <p class="text-sm text-gray-700 mb-4">¿Estás seguro de que deseas finalizar a este cliente?</p>
        <div class="container-actions cm-dialog-actions">
            <button id="cancelarFinalizar" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">Cancelar</button>
            <button id="confirmarFinalizar" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Confirmar</button>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Agregar evento a todos los selects de estado
    document.querySelectorAll('.estado-select').forEach(function(select) {
        // Aplicar color inicial al cargar la página
        aplicarColorEstado(select, select.value);

        select.addEventListener('change', function() {
            cambiarEstado(this);
        });
    });
});

// Mostrar el modal de confirmación
$(document).on('click', '.finalizarBtn', function() {
    var clienteId = $(this).data('id'); // Obtener el ID del cliente desde el botón
    $('#finalizarModal').removeClass('hidden'); // Mostrar modal
    $('#confirmarFinalizar').data('id', clienteId); // Pasar el id al botón de confirmación
});

// Confirmar la finalización
$('#confirmarFinalizar').click(function() {
    var clienteId = $(this).data('id');
    $.ajax({
        url: '/mis-clientes/' + clienteId + '/finalizar',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
        },
        success: function(response) {
            if (response.success) {
                // Mostrar la notificación de éxito
                mostrarToast(response.message, 'success');
                
                // Animación de eliminación con transición
                var fila = $('#cliente-' + clienteId);
                fila.css('transition', 'opacity 160ms var(--cm-ease)'); // Transición suave
                fila.css('background-color', '#dcfce7'); // Color de fondo verde claro
                setTimeout(function() {
                    fila.css('opacity', '0'); // Hacerla invisible
                    setTimeout(function() {
                        fila.get(0)?.dispatchEvent(new Event('cm:before-record-removal', { bubbles: true }));
                        fila.remove(); // Eliminar la fila después de la animación
                    }, 300); // Tiempo de espera para eliminar la fila
                }, 500); // Tiempo de espera para aplicar la animación de color
            } else {
                // Mostrar la notificación de error
                mostrarToast(response.message, 'error');
            }
            $('#finalizarModal').addClass('hidden'); // Cerrar el modal
        },
        error: function() {
            // Mostrar la notificación de error
            mostrarToast('Ocurrió un error al procesar la solicitud.', 'error');
            $('#finalizarModal').addClass('hidden'); // Cerrar el modal en caso de error
        }
    });
});

// Función para cancelar la acción
$('#cancelarFinalizar').click(function() {
    $('#finalizarModal').addClass('hidden'); // Cerrar el modal sin hacer nada
});

// Función para mostrar las notificaciones tipo toast
function mostrarToast(message, type = 'success') {
    const toastContainer = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.classList.add('toast', 'p-4', 'rounded-lg', 'shadow-lg', 'mb-2', 'w-80', 'text-white', 'transition-opacity', 'duration-150');

    // Establecer los colores según el tipo de notificación (success o error)
    if (type === 'success') {
        toast.classList.add('bg-green-500');
    } else {
        toast.classList.add('bg-red-500');
    }

    // Establecer el contenido de la notificación
    toast.innerHTML = `
        <div class="flex justify-between items-center">
            <span class="text-sm">${message}</span>
            <button class="close-toast text-lg font-semibold" onclick="cerrarToast(this)">✖</button>
        </div>
    `;

    // Agregar el toast al contenedor
    toastContainer.appendChild(toast);

    // Hacer que la notificación se cierre automáticamente después de 5 segundos
    setTimeout(() => {
        toast.classList.add('opacity-0');
        setTimeout(() => toast.remove(), 500);  // Eliminar el toast del DOM después de la animación
    }, 5000);
}

// Función para cerrar la notificación manualmente
function cerrarToast(button) {
    const toast = button.closest('.toast');
    toast.classList.add('opacity-0');
    setTimeout(() => toast.remove(), 500);
}

// Definir colores para cada estado
const coloresEstados = {
    pendiente: ['#fff1ce', '#684900'],
    en_evaluacion: ['#e4eef9', '#234e75'],
    califica: ['#dff2e9', '#175747'],
    no_califica: ['#fbe8ed', '#892c46'],
    venta_concretada: ['#eee8fa', '#513781'],
};

function aplicarColorEstado(selectElement, estado) {
    const [fondo, texto] = coloresEstados[estado] ?? ['var(--cm-subtle)', 'var(--cm-ink)'];
    selectElement.style.backgroundColor = fondo;
    selectElement.style.color = texto;
    selectElement.style.fontWeight = '600';
}

// Función para cambiar el estado de un cliente
function cambiarEstado(selectElement) {
    const clienteId = selectElement.dataset.clienteId;
    const nuevoEstado = selectElement.value;
    const estadoOriginal = selectElement.dataset.original || selectElement.value;

    if (!selectElement.dataset.original) {
        selectElement.dataset.original = selectElement.value;
    }

    selectElement.disabled = true;
    selectElement.style.opacity = '0.6';

    fetch(`/mis-clientes/${clienteId}/estado`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            estado: nuevoEstado
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            selectElement.dataset.original = nuevoEstado;

            // APLICAR EL COLOR DEL NUEVO ESTADO
            aplicarColorEstado(selectElement, nuevoEstado);

            console.log('Estado actualizado a:', data.nuevo_estado);

            // Animación de éxito
            selectElement.style.boxShadow = 'inset 0 0 0 1px var(--cm-accent)';
            setTimeout(() => {
                selectElement.style.boxShadow = '';
            }, 1000);

        } else {
            throw new Error(data.message || 'Error al actualizar');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        selectElement.value = estadoOriginal;
        aplicarColorEstado(selectElement, estadoOriginal);

        selectElement.style.boxShadow = 'inset 0 0 0 1px var(--cm-danger)';
        setTimeout(() => {
            selectElement.style.boxShadow = '';
        }, 2000);

        alert('Error al actualizar el estado: ' + error.message);
    })
    .finally(() => {
        selectElement.disabled = false;
        selectElement.style.opacity = '1';
    });
}

// Función para abrir WhatsApp
function abrirWhatsApp(telefono, nombre) {
    // Limpiar el número de teléfono (remover espacios, guiones, etc.)
    let numeroLimpio = telefono.replace(/\D/g, '');

    // Si el número no empieza con código de país, agregar +51 para Perú
    if (!numeroLimpio.startsWith('51') && numeroLimpio.length === 9) {
        numeroLimpio = '51' + numeroLimpio;
    }

    // Mensaje predeterminado (opcional)
    const mensaje = `Hola ${nombre}, soy de AKANA TECHNOLOGY. Me pongo en contacto contigo para...`;

    // Crear la URL de WhatsApp
    const urlWhatsApp = `https://wa.me/${numeroLimpio}?text=${encodeURIComponent(mensaje)}`;

    // Abrir WhatsApp en una nueva ventana/pestaña
    window.open(urlWhatsApp, '_blank');
}
</script>

@endsection
