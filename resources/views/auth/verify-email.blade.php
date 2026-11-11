<x-guest-layout>
    <header class="cm-auth-heading"><h1>Verificar correo electrónico</h1></header>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Abre el enlace que enviamos a tu correo para verificarlo. Puedes solicitar otro si el mensaje aún no llegó.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ __('Te enviamos un nuevo enlace de verificación al correo que registraste.') }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    {{ __('Reenviar correo de verificación') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Salir') }}
            </button>
        </form>
    </div>
</x-guest-layout>
