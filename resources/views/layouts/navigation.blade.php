<!-- resources/views/layouts/navigation.blade.php -->
<nav class="cm-nav" aria-label="Navegación principal">
    <div class="cm-nav-frame">
        <div class="cm-nav-layout">
            
            <!-- LOGO -->
            <div class="cm-brand">
                <span class="cm-brand-mark" aria-hidden="true"></span>
                ClientManager
            </div>

            <!-- Menú Desktop -->
            <div class="cm-nav-links">
                <span class="cm-nav-caption">Espacio de trabajo</span>
                <a href="{{ route('clientes.index') }}" class="cm-nav-link cm-nav-disponibles">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h10"/></svg>
                    Clientes Disponibles
                </a>

                <a href="{{ route('mis-clientes') }}" class="cm-nav-link cm-nav-asignados">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 5h14v14H5z M8 12l2.5 2.5L16 9"/></svg>
                    Mis Clientes
                </a>

                <!-- Mostrar Reportes solo si el usuario es administrador -->
                @if(Auth::user()->role == 'admin')
                    <a href="{{ route('reporte.index') }}" class="cm-nav-link cm-nav-reportes">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 4v16h16M9 15v-3m5 3V9m5 6V5"/></svg>
                        Reportes
                    </a>
                @endif
            </div>

            <!-- USUARIO + LOGOUT (DESKTOP) -->
            <div class="cm-nav-account">
                <span class="cm-account-name">
                    <span class="cm-account-initial" aria-hidden="true">{{ mb_substr(Auth::user()->name, 0, 1) }}</span>
                    <span><strong>{{ Auth::user()->name }}</strong><small>{{ Auth::user()->getRoleDisplayName() }}</small></span>
                </span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="cm-signout">
                        Salir
                    </button>
                </form>
            </div>

            <!-- BOTÓN HAMBURGUESA (SOLO MÓVIL) -->
            <div class="cm-menu-control">
                <button id="menu-toggle" type="button" class="cm-menu-toggle" aria-label="Menú de navegación" aria-controls="mobile-menu" aria-expanded="false">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16" stroke-linecap="round"/></svg>
                </button>
            </div>
        </div>
    </div>

    <!-- MENÚ MÓVIL -->
    <div id="mobile-menu" class="hidden cm-mobile-menu">
        <a href="{{ route('clientes.index') }}" class="cm-nav-link cm-nav-disponibles">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h10"/></svg>
            Clientes Disponibles
        </a>
        <a href="{{ route('mis-clientes') }}" class="cm-nav-link cm-nav-asignados">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 5h14v14H5z M8 12l2.5 2.5L16 9"/></svg>
            Mis Clientes
        </a>

        <!-- Mostrar Reportes solo si el usuario es administrador en el menú móvil -->
        @if(Auth::user()->role == 'admin')
            <a href="{{ route('reporte.index') }}" class="cm-nav-link cm-nav-reportes">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 4v16h16M9 15v-3m5 3V9m5 6V5"/></svg>
                Reportes
            </a>
        @endif

        <div class="cm-nav-divider"></div>
        <div class="cm-mobile-account">
            <span class="cm-account-name">{{ Auth::user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="cm-signout">
                    Salir
                </button>
            </form>
        </div>
    </div>

    <script>
        document.querySelectorAll('.cm-nav-link').forEach(link => {
            if (link.pathname === window.location.pathname) {
                link.setAttribute('aria-current', 'page');
            } else if (window.location.pathname.startsWith(link.pathname + '/')) {
                link.setAttribute('aria-current', 'location');
            }
        });
        document.getElementById('menu-toggle').addEventListener('click', function () {
            const closed = document.getElementById('mobile-menu').classList.toggle('hidden');
            this.setAttribute('aria-expanded', String(!closed));
        });
    </script>
</nav>

