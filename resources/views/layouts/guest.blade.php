<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="cm-auth antialiased">
        <div class="cm-auth-shell">
            <aside class="cm-auth-brand">
                <a href="/" class="cm-brand">
                    <span class="cm-brand-mark" aria-hidden="true"></span>
                    ClientManager
                </a>
                <div class="cm-auth-intro">
                    <p>Gestiona tus clientes en un solo lugar.</p>
                    <span>Seguimiento de leads, evaluación crediticia y ventas por sucursal.</span>
                </div>
            </aside>
            <main class="cm-auth-main">
                <div class="cm-auth-card">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </body>
</html>
