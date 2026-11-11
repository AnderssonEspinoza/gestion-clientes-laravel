import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

function focusOutsideRecord(record) {
    const nearbyAction = record?.nextElementSibling?.querySelector('a[href]')
        ?? record?.previousElementSibling?.querySelector('a[href]');
    const fallback = document.querySelector('.btn-refresh') ?? document.querySelector('#cm-main');
    (nearbyAction ?? fallback)?.focus({ preventScroll: true });
}

document.addEventListener('cm:before-record-removal', event => {
    if (event.target.contains(document.activeElement)) focusOutsideRecord(event.target);
});

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
            if (previousFocus.isConnected && !previousFocus.disabled) {
                previousFocus.focus({ preventScroll: true });
            } else {
                focusOutsideRecord(previousFocus.closest('.cliente-row'));
            }
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

const refreshPage = document.querySelector('[data-cm-refresh-page]');

if (refreshPage) {
    // A scheduled reload retains keyboard orientation without changing the refresh cycle.
    const focusKey = 'clientmanager:refresh-focus:' + window.location.pathname + window.location.search;
    let lastRecordControl;

    refreshPage.addEventListener('focusin', event => {
        if (event.target.dataset.cmFocus) lastRecordControl = event.target;
    });

    try {
        const previousFocus = sessionStorage.getItem(focusKey);
        sessionStorage.removeItem(focusKey);

        if (previousFocus) {
            const control = refreshPage.querySelector('[data-cm-focus="' + CSS.escape(previousFocus) + '"]');
            (control ?? refreshPage.querySelector('#auto-refresh')).focus({ preventScroll: true });
        }

        window.addEventListener('cm:save-refresh-focus', () => {
            const activeControl = document.activeElement;
            const control = activeControl.closest('[data-cm-confirmation]') ? lastRecordControl : activeControl;
            if (refreshPage.contains(control) && control.dataset.cmFocus) {
                sessionStorage.setItem(focusKey, control.dataset.cmFocus);
            }
        });
    } catch {
        // Refresh still works if the browser blocks session storage.
    }
}
