{{-- resources/views/partials/_phase4_js.blade.php --}}

<script>
(function () {
    'use strict';

    const $ = (selector, ctx = document) => ctx.querySelector(selector);
    const $$ = (selector, ctx = document) => [...ctx.querySelectorAll(selector)];

/* ── Modal ─────────────────────────────────────────────────────────── */

function openModal(id) {
    const modal = document.getElementById(id);

    if (!modal) {
        console.warn('Modal not found:', id);
        return;
    }

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    document.body.classList.add('overflow-hidden');
}

function closeModal(id) {
    const modal = document.getElementById(id);

    if (!modal) return;

    modal.classList.remove('flex');
    modal.classList.add('hidden');

    document.body.classList.remove('overflow-hidden');
}

/* Global modal handling */
document.addEventListener('click', function (e) {

    const closeBtn = e.target.closest('.modal-close');

    if (closeBtn) {
        const modal = closeBtn.closest('.modal-overlay');

        if (modal) {
            closeModal(modal.id);
        }

        return;
    }

    const backdrop = e.target.closest('.modal-backdrop');

    if (backdrop) {
        const modal = backdrop.closest('.modal-overlay');

        if (modal) {
            closeModal(modal.id);
        }
    }
});

/* ESC closes all open modals */
document.addEventListener('keydown', function (e) {

    if (e.key !== 'Escape') return;

    document.querySelectorAll('.modal-overlay:not(.hidden)')
        .forEach(modal => closeModal(modal.id));
});

    document.addEventListener('keydown', (e) => {
        if (e.key !== 'Escape') return;

        $$('.modal-overlay, .modal, [id$="-modal"]').forEach(modal => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        });
    });

    /* =========================
       QUICK VIEW TABS
    ========================== */

    function switchQvTab(tab) {

        $$('.qv-tab-btn').forEach(btn => {

            const active = btn.dataset.qvTab === tab;

            btn.classList.toggle('kt-btn-mono', active);
            btn.classList.toggle('kt-btn-ghost', !active);
        });

        $$('.qv-tab-content').forEach(content => {

            content.classList.toggle(
                'hidden',
                content.id !== `qv-tab-${tab}`
            );
        });
    }

    document.addEventListener('click', (e) => {

        const btn = e.target.closest('.qv-tab-btn');

        if (!btn) return;

        switchQvTab(btn.dataset.qvTab);
    });

    /* =========================
       GLOBAL
    ========================== */

    window.CS4 = {
        openModal,
        closeModal,
        switchQvTab,
        $,
        $$
    };

})();
</script>