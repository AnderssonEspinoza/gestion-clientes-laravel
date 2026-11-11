<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    @stack('scripts')
    <body class="cm-app antialiased">
        <div class="cm-shell">
            <a class="cm-skip" href="#cm-main">Ir al contenido</a>
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="cm-slot-header">
                    <div class="cm-page">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main id="cm-main" class="cm-main" tabindex="-1">
                @if (config('demo.enabled'))
                    <p class="cm-demo-notice"><strong>Demo pública</strong> · Solo lectura con datos ficticios</p>
                @endif
                @yield('content')
                {{ $slot ?? '' }}
            </main>
        </div>
    </body>
</html>
