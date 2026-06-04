@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    // =========================================================================
    // DETAIL MODAL — open via AJAX
    // =========================================================================
    document.addEventListener('click', (e) => {

        const detailBtn = e.target.closest('.open-detail');
        if (detailBtn) {
            const id = detailBtn.dataset.id;
            if (id && window.openListingDetail) {
                window.openListingDetail(id);
            }
            return;
        }

        // Quick view modal
        const quickBtn = e.target.closest('.quick-view');
        if (quickBtn) {
            const modal = document.getElementById('quick-view-modal');
            modal?.classList.remove('hidden');
            modal?.classList.add('flex');
        }
    });

    // Close quick-view
    document.addEventListener('click', (e) => {
        if (e.target.closest('.close-modal')) {
            document.querySelectorAll('#quick-view-modal').forEach(modal => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            });
        }
    });

    // =========================================================================
    // TOP-NAV TABS (Listings / QA / Publication / Valuations / Exchange / Lifecycle)
    // =========================================================================
    const allViews = ['listings', 'qa', 'publication', 'valuations', 'exchange', 'lifecycle'];

    function switchView(target) {
        allViews.forEach(v => {
            document.getElementById(`view-${v}`)?.classList.add('hidden');
        });
        document.getElementById(`view-${target}`)?.classList.remove('hidden');

        // Update tab active states
        document.querySelectorAll('[data-view-tab]').forEach(tab => {
            const isActive = tab.dataset.viewTab === target;
            tab.classList.toggle('text-foreground', isActive);
            tab.classList.toggle('border-b-2', isActive);
            tab.classList.toggle('border-primary', isActive);
            tab.classList.toggle('text-muted-foreground', !isActive);
        });
    }

    document.querySelectorAll('[data-view-tab]').forEach(tab => {
        tab.addEventListener('click', () => switchView(tab.dataset.viewTab));
    });

    // =========================================================================
    // FILTERS (row filter toggle)
    // =========================================================================
    document.getElementById('toggle-filters')?.addEventListener('click', () => {
        document.getElementById('listings-filters')?.classList.toggle('hidden');
    });

    // =========================================================================
    // BULK ACTIONS — checkbox management + action dispatch
    // =========================================================================
    const selectAll = document.getElementById('select-all');
    const bulkBar   = document.getElementById('bulk-bar');

    function updateBulkBar() {
        const checked = document.querySelectorAll('.row-check:checked');
        if (bulkBar) {
            bulkBar.classList.toggle('hidden', checked.length === 0);
            const countEl = bulkBar.querySelector('.bulk-count');
            if (countEl) countEl.textContent = checked.length;
        }
    }

    selectAll?.addEventListener('change', () => {
        document.querySelectorAll('.row-check').forEach(cb => {
            cb.checked = selectAll.checked;
        });
        updateBulkBar();
    });

    document.addEventListener('change', (e) => {
        if (e.target.classList.contains('row-check')) updateBulkBar();
    });

    // Bulk action handler
    document.querySelectorAll('[data-bulk]').forEach(btn => {
        btn.addEventListener('click', async function () {
            const action = this.dataset.bulk;
            const checked = [...document.querySelectorAll('.row-check:checked')];
            const ids     = checked.map(cb => cb.dataset.id).filter(Boolean);

            if (ids.length === 0) return;

            if (action === 'pull-valuations') {
                // Per-row status pills
                ids.forEach(id => {
                    const pill = document.querySelector(`.bulk-status-pill[data-id="${id}"]`);
                    if (pill) {
                        pill.classList.remove('hidden');
                        pill.textContent = 'In Queue';
                        pill.className = 'bulk-status-pill kt-badge kt-badge-outline text-xs';
                    }
                });

                // Stagger fetches for visual feedback
                for (const [idx, id] of ids.entries()) {
                    const pill = document.querySelector(`.bulk-status-pill[data-id="${id}"]`);

                    await new Promise(r => setTimeout(r, idx * 400));

                    if (pill) {
                        pill.textContent = 'Fetching';
                        pill.className = 'bulk-status-pill kt-badge kt-badge-warning text-xs';
                    }

                    await new Promise(r => setTimeout(r, 1200));

                    // TODO: real POST /listings/bulk { action, ids }
                    const success = Math.random() > 0.15;

                    if (pill) {
                        if (success) {
                            pill.textContent = 'Done +£150';
                            pill.className = 'bulk-status-pill kt-badge kt-badge-success text-xs';
                        } else {
                            pill.textContent = 'Failed';
                            pill.className = 'bulk-status-pill kt-badge kt-badge-danger text-xs';
                            pill.title = 'Provider unavailable. Hover to retry.';
                        }
                    }
                }

                return;
            }

            // All other bulk actions — single POST
            // TODO: POST /listings/bulk { action, ids }
            console.log('Bulk action:', action, ids);
        });
    });

    // =========================================================================
    // ADD LISTING button → navigate to create page
    // =========================================================================
    document.querySelector('[data-action="create-listing"]')?.addEventListener('click', () => {
        window.location.href = '{{ route("listings.create") }}';
    });

    // =========================================================================
    // ADD VALUATION modal (from index-level Valuations tab)
    // =========================================================================
    document.querySelector('[data-action="add-valuation"]')?.addEventListener('click', () => {
        const modal = document.getElementById('add-valuation-modal');
        modal?.classList.remove('hidden');
        modal?.classList.add('flex');
    });

    // =========================================================================
    // PULL LATEST VALUATION (from index-level Valuations tab)
    // =========================================================================
    document.querySelector('[data-action="pull-latest-valuation"]')?.addEventListener('click', () => {
        // TODO: trigger a standalone pull (no listing ID in this context — prompt if needed)
        alert('Pull valuation: select a listing first, or use bulk action from the Listings tab.');
    });

    // =========================================================================
    // APPLY TO LISTING (Valuations panel recommendation)
    // =========================================================================
    document.querySelector('[data-action="apply-to-listing"]')?.addEventListener('click', () => {
        const modal = document.getElementById('apply-pricing-modal');
        modal?.classList.remove('hidden');
        modal?.classList.add('flex');
    });

});
</script>
@endpush