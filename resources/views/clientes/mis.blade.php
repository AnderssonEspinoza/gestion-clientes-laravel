@extends('layouts.app')

@section('content')

<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Mis Clientes Asignados</h1>
    
    @if(session('success'))
        <div class="bg-green-200 text-green-800 p-2 mb-4 rounded">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="bg-red-200 text-red-800 p-2 mb-4 rounded">
            {{ session('error') }}
        </div>
    @endif
    
    @if($misClientes->isEmpty())
        <p>No tienes clientes asignados.</p>
    @else
        <div class="overflow-x-auto table-responsive">
            <table class="min-w-full border text-sm md:text-base">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border px-4 py-2">Nombre</th>
                        <th class="border px-4 py-2">Email</th>
                        <th class="border px-4 py-2">Teléfono</th>
                        <th class="border px-4 py-2">Estado</th>
                        <th class="border px-4 py-2">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($misClientes as $cliente)
                    <tr id="cliente-{{ $cliente->id }}" class="cliente-row border-b hover:bg-gray-50 transition">
                        <td class="border px-4 py-2">{{ $cliente->nombre }}</td>
                        <td class="border px-4 py-2">{{ $cliente->email }}</td>
                        <td class="border px-4 py-2">{{ $cliente->telefono ?? '-' }}</td>
                        
                        <td>
                            <select class="form-select form-select-sm estado-select" 
                                    data-cliente-id="{{ $cliente->id }}"
                                    style="min-width: 160px;">
                                @foreach(App\Models\Cliente::ESTADOS as $key => $label)
                                    <option value="{{ $key }}" 
                                            {{ $cliente->estado == $key ? 'selected' : '' }} >
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </td>

                        <td class="border px-4 py-2 space-x-2">
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

        <!-- Vista de Tarjetas en móviles -->
        <div class="client-card hidden md:none">
            @foreach($misClientes as $cliente)
                <div class="client-card-item">
                    <h2>{{ $cliente->nombre }}</h2>
                    <p><strong>📧:</strong> {{ $cliente->email }}</p>
                    <p><strong>📞:</strong> {{ $cliente->telefono ?? '-' }}</p>
                    <p><strong>Estado:</strong> <select class="form-select form-select-sm estado-select" 
                                    data-cliente-id="{{ $cliente->id }}"
                                    style="min-width: 160px;">
                                @foreach(App\Models\Cliente::ESTADOS as $key => $label)
                                    <option value="{{ $key }}" 
                                            {{ $cliente->estado == $key ? 'selected' : '' }} >
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                    </p>

                    <div class="actions">
                        <a href="{{ route('mis-clientes.show', $cliente->id) }}" class="action-btn bg-blue-500 hover:bg-blue-600 text-white">Ver</a>
                        <form id="finalizarForm" data-id="{{ $cliente->id }}" method="POST" class="inline-block">
                            @csrf
                            <button type="button" class="action-btn bg-orange-500 hover:bg-red-600 text-white finalizarBtn" data-id="{{ $cliente->id }}">Finalizar</button>
                        </form>
                        @if($cliente->telefono)
                            <a href="#" onclick="abrirWhatsApp('{{ $cliente->telefono }}', '{{ $cliente->nombre }}')" class="action-btn bg-green-500 hover:bg-green-600 text-white">WhatsApp</a>
                        @else
                            <span class="action-btn bg-gray-400 text-white cursor-not-allowed">Sin teléfono</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

    @endif
</div>

<!-- Contenedor de notificaciones -->
<div id="toast-container" class="fixed bottom-4 right-4 z-50"></div>

<!-- Modal de Confirmación -->
<div id="finalizarModal" class="fixed inset-0 bg-gray-500 bg-opacity-50 flex items-center justify-center hidden">
    <div class="modal bg-white p-6 rounded-lg shadow-lg w-96">
        <h3 class="text-lg font-semibold">Confirmar Acción</h3>
        <p class="text-sm text-gray-700 mb-4">¿Estás seguro de que deseas finalizar a este cliente?</p>
        <div class="container-actions flex justify-end space-x-4">
            <button id="cancelarFinalizar" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">Cancelar</button>
            <button id="confirmarFinalizar" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Confirmar</button>
        </div>
    </div>
</div>

<style>
    @media (max-width: 700px) {
    /* Ocultar la tabla en pantallas pequeñas */
    .table-responsive {
        display: none;
    }

    /* Mostrar las tarjetas en pantallas pequeñas */
    .client-card {
        /* display: block; */
        display: flex;
        flex-direction: column;
        gap: 16px;
        margin: 8px;
        padding: 16px;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .client-card h2 {
        font-size: 1.125rem;
        font-weight: 600;
    }

    .client-card p {
        font-size: 0.875rem;
        margin-top: 4px;
    }
    .client-card-item{
        background-color: #D9D2D2;
        padding: 16px;
        /* margin-bottom: 16px; Espacio entre las tarjetas */
        border-radius: 8px; /* Bordes redondeados */
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Sombra suave */
        border: 1px solid #e5e7eb; /* Borde gris claro */

    }

    .client-card .actions {
        display: flex;
        gap: 10px;
        margin-top: 10px;
    }

    .client-card .action-btn {
        padding: 8px 16px;
        font-size: 0.875rem;
        border-radius: 8px;
        cursor: pointer;
    }

    .client-card .action-btn.bg-blue-500 {
        background-color: #2563eb; /* Azul */
    }

    .client-card .action-btn.bg-orange-500 {
        background-color: #ea580c; /* Naranja */
    }

    .client-card .action-btn.bg-green-500 {
        background-color: #10b981; /* Verde */
    }

    .client-card .action-btn:hover {
        opacity: 0.9;
    }
    .form-select{
        padding: 3px 8px;
        font-size: 12px;
    }
}

@media (max-width: 450px){
    .action-btn{
        /* width: 30%; */
        font-size: 10px;
        text-align: center;
        /* width: 90px; */
    }
    #finalizarForm .action-btn{
        /* width: 90px; */
        /* padding: auto; */
    }
    .modal{
        width: 80vw;
    }
    #cancelarFinalizar, #confirmarFinalizar{
        font-size: 13px;
        width: 50%;
    }
    .container-actions{
        justify-content: center;
    }
}

</style>
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
                fila.css('transition', 'all 0.3s ease-out'); // Transición suave
                fila.css('background-color', '#dcfce7'); // Color de fondo verde claro
                setTimeout(function() {
                    fila.css('opacity', '0'); // Hacerla invisible
                    fila.css('transform', 'translateX(-100%)'); // Mover fuera de la vista
                    setTimeout(function() {
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
    toast.classList.add('toast', 'p-4', 'rounded-lg', 'shadow-lg', 'mb-2', 'w-80', 'text-white', 'transition', 'duration-300', 'ease-in-out');

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
    'pendiente': '#ffc107',           // Amarillo
    'en_evaluacion': '#0dcaf0',       // Azul
    'califica': '#198754',            // Verde
    'no_califica': '#dc3545',         // Rojo
    'venta_concretada': '#6f42c1'     // Morado
};

// Función para aplicar colores de acuerdo al estado
function aplicarColorEstado(selectElement, estado) {
    const color = coloresEstados[estado];
    if (color) {
        selectElement.style.backgroundColor = color;
        selectElement.style.color = '#ffffff';
        selectElement.style.fontWeight = 'bold';
        selectElement.style.borderColor = color;
        selectElement.style.borderWidth = '2px';
    } else {
        // Restablecer si no hay color definido
        selectElement.style.backgroundColor = '#f8f9fa';
        selectElement.style.color = '#212529';
        selectElement.style.fontWeight = 'normal';
        selectElement.style.borderColor = '#ced4da';
        selectElement.style.borderWidth = '1px';
    }
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
            selectElement.style.boxShadow = '0 0 15px rgba(255,255,255,0.9)';
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

        selectElement.style.boxShadow = '0 0 10px #dc3545';
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
