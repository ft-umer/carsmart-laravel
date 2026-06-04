{{--
    L2 — Listing Detail Modal shell
    The modal shell is always present in the DOM.
    Clicking a listing row (or Open button) fires an AJAX GET /listings/{id}
    and injects the HTML partial (details.blade.php) into #modal-content.
--}}
<div
    id="listing-detail-modal"
    class="fixed inset-0 z-50 hidden items-center justify-center p-3 sm:p-4"
    role="dialog"
    aria-modal="true"
    aria-labelledby="detail-modal-title">

    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm close-overlay transition-opacity"></div>

    {{-- Panel --}}
    <div class="relative w-full max-w-[1100px] max-h-[92vh] bg-background border border-border rounded-2xl shadow-2xl flex flex-col overflow-hidden">

        {{-- Loading skeleton (shown while AJAX fetches) --}}
        <div id="detail-modal-loading" class="flex items-center justify-center p-12">
            <div class="flex flex-col items-center gap-3 text-muted-foreground">
                <i class="ki-filled ki-loading animate-spin text-2xl"></i>
                <span class="text-sm">Loading listing…</span>
            </div>
        </div>

        {{-- Injected content --}}
        <div id="modal-content" class="hidden flex-1 overflow-hidden flex flex-col"></div>

    </div>

</div>

<script>
(function () {
    /**
     * Open the detail modal and load the listing partial via AJAX.
     * Called by listings-scripts.blade.php when a row's "Open" / "open-detail" is clicked.
     */
    window.openListingDetail = function (listingId) {
        const modal    = document.getElementById('listing-detail-modal');
        const loading  = document.getElementById('detail-modal-loading');
        const content  = document.getElementById('modal-content');

        // Reset
        loading.classList.remove('hidden');
        content.classList.add('hidden');
        content.innerHTML = '';

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');

        fetch(`/listings/${listingId}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.text())
        .then(html => {
            content.innerHTML = html;
            loading.classList.add('hidden');
            content.classList.remove('hidden');
        })
        .catch(() => {
            content.innerHTML = `
                <div class="p-8 text-center text-danger">
                    <i class="ki-filled ki-warning text-2xl mb-2 block"></i>
                    Failed to load listing. Please try again.
                </div>`;
            loading.classList.add('hidden');
            content.classList.remove('hidden');
        });
    };

    // Close on backdrop click or .close-modal / .close-overlay
    document.addEventListener('click', (e) => {
        if (e.target.closest('.close-modal') || e.target.closest('.close-overlay')) {
            const modal = document.getElementById('listing-detail-modal');
            modal?.classList.add('hidden');
            modal?.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }
    });

    // Close on Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const modal = document.getElementById('listing-detail-modal');
            modal?.classList.add('hidden');
            modal?.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }
    });
})();
</script>