@extends('layouts.app')

@section('content')
<div class="cm-page cm-client-detail cm-assigned-detail">
    <div class="cm-detail-card">
        <!-- Identidad del cliente -->
        <div class="cm-detail-banner">
            <div class="cm-detail-portrait">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                    <circle cx="12" cy="8" r="3.25"/>
                    <path d="M5 20v-1.5a5 5 0 0 1 5-5h4a5 5 0 0 1 5 5V20" stroke-linecap="round"/>
                </svg>
                <img
                    src="https://ui-avatars.com/api/?name={{ urlencode($cliente->nombre) }}&size=150&background=random"
                    alt="Foto de {{ $cliente->nombre }}"
                    class="cm-detail-avatar"
                    onload="this.classList.add('cm-avatar-loaded')"
                >
            </div>
        </div>

        <!-- Información del cliente -->
        <div class="cm-detail-content">
            <h1 class="cm-title">{{ $cliente->nombre }}</h1>

            <div class="cm-detail-fields">
                <div class="cm-detail-field">
                    <span class="cm-detail-label">Email</span>
                    <span class="text-gray-700 break-all">{{ $cliente->email }}</span>
                </div>
                <div class="cm-detail-field">
                    <span class="cm-detail-label">Teléfono</span>
                    <span class="text-gray-700">{{ $cliente->telefono ?? '-' }}</span>
                </div>
                <div class="cm-detail-field">
                    <span class="cm-detail-label">Fecha de registro</span>
                    <span class="text-gray-700">{{ $cliente->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="mt-6 flex flex-col sm:flex-row gap-3">
                <!-- AQUÍ CAMBIAS LA RUTA DE VOLVER -->
                <a href="{{ route('mis-clientes') }}"
                    class="cm-button cm-button-secondary w-full">
                   Volver a Mis Clientes
                </a>

                <!-- Como es "mis clientes", probablemente no necesites el botón de asignar -->
                <!-- Si lo necesitas, manténlo -->
                @unless (config('demo.enabled'))
                    <form method="POST" action="{{ route('clientes.assign', $cliente->id) }}" class="w-full">
                        @csrf
                        <button type="submit"
                                 class="cm-button cm-button-primary w-full">
                            Asignarme cliente
                        </button>
                    </form>
                @endunless
            </div>
        </div>
    </div>
</div>
@endsection