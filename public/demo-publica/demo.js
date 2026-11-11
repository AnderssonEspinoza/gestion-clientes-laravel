const menuToggle = document.querySelector('#menu-toggle');
const mobileMenu = document.querySelector('#mobile-menu');

menuToggle.addEventListener('click', () => {
    const closed = mobileMenu.classList.toggle('hidden');
    menuToggle.setAttribute('aria-expanded', String(!closed));
});

document.querySelectorAll('.cm-nav-link').forEach(link => {
    link.removeAttribute('aria-current');
    if (new URL(link.href).pathname === window.location.pathname) {
        link.setAttribute('aria-current', 'page');
    }
});

const refreshButton = document.querySelector('[data-demo-refresh]');
const autoRefresh = document.querySelector('#auto-refresh');

if (refreshButton) {
    refreshButton.addEventListener('click', () => {
        document.querySelector('#ultimo-refresh').textContent = 'Datos de ejemplo actualizados';
    });
    window.setInterval(() => {
        if (autoRefresh.checked && !document.hidden) {
            document.querySelector('#ultimo-refresh').textContent = 'Datos de ejemplo actualizados';
        }
    }, 10000);
}

const filters = document.querySelector('.filter-form');
if (filters) {
    const params = new URLSearchParams(window.location.search);
    for (const select of filters.querySelectorAll('select')) {
        if (params.has(select.name)) select.value = params.get(select.name);
    }
}
