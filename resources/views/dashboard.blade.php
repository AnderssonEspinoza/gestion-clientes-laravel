<x-app-layout>
    <x-slot name="header">
        <h1 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Inicio') }}
        </h1>
    </x-slot>

    <div class="cm-page cm-dashboard-home">
        <div class="cm-dashboard-body">
            <div class="cm-detail-card">
                <div class="p-6 text-gray-900">
                    {{ __("Has iniciado sesión.") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
