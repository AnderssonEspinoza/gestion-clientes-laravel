import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Keep keyboard focus within the existing confirmation overlays.
document.querySelectorAll('[data-cm-confirmation]').forEach(dialog => {
    const cancel = dialog.querySelector('#cancelarFinalizar');
    const confirm = dialog.querySelector('#confirmarFinalizar');
    let previousFocus;

    const syncFocus = () => {
        const open = !dialog.classList.contains('hidden');
        dialog.inert = !open;

        if (open && !dialog.contains(document.activeElement)) {
            previousFocus = document.activeElement;
            cancel.focus();
        } else if (!open && previousFocus) {
            if (previousFocus.isConnected) previousFocus.focus();
            previousFocus = null;
        }
    };

    syncFocus();
    new MutationObserver(syncFocus).observe(dialog, {
        attributes: true,
        attributeFilter: ['class'],
    });

    dialog.addEventListener('keydown', event => {
        if (event.key === 'Escape') {
            event.preventDefault();
            cancel.click();
        }

        if (event.key === 'Tab') {
            const last = confirm.disabled ? cancel : confirm;
            const boundary = event.shiftKey ? cancel : last;
            if (document.activeElement === boundary) {
                event.preventDefault();
                (event.shiftKey ? last : cancel).focus();
            }
        }
    });
});
