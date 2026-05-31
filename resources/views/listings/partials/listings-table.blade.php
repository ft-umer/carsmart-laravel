<div class="space-y-3">

    {{-- BULK ACTION BAR --}}
    <div id="bulk-bar" class="hidden flex items-center justify-between p-3 card border border-border bg-muted/10">
        <div class="text-sm">
            <span id="selected-count">0</span> selected
        </div>
        <div class="flex flex-wrap gap-2">
            <button class="kt-btn kt-btn-sm kt-btn-outline" data-bulk="pull-valuations">Pull Valuations</button>
            <button class="kt-btn kt-btn-sm kt-btn-outline" data-bulk="mark-qa">Mark for QA</button>
            <button class="kt-btn kt-btn-sm kt-btn-outline" data-bulk="assign-owner">Assign Owner</button>
            <button class="kt-btn kt-btn-sm kt-btn-outline" data-bulk="enable-bin-offer">Enable BIN/Offer</button>
            <button class="kt-btn kt-btn-sm kt-btn-outline" data-bulk="publication-queue">Publication Queue</button>
            <button class="kt-btn kt-btn-sm kt-btn-outline" data-bulk="create-auction">Create Auction</button>
            <button class="kt-btn kt-btn-sm kt-btn-ghost text-danger" data-bulk="archive">Archive</button>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="card rounded-lg border border-border overflow-hidden">
       <div class="kt-scrollable-x">
    <table class="kt-table w-full">
                <thead class="bg-muted/40 sticky top-0 z-10">
                    <tr>
                        <th class="p-3 w-10"><input type="checkbox" id="select-all" class="kt-checkbox"></th>
                        <th class="p-3 text-left">Listing</th>
                        <th class="p-3 text-left">Vehicle</th>
                        <th class="p-3 text-left">VRM</th>
                        <th class="p-3 text-right">Mileage</th>
                        <th class="p-3 text-right">Valuation</th>
                        <th class="p-3 text-center">Reserve?</th>
                        <th class="p-3 text-center">BIN / Offer</th>
                        <th class="p-3 text-center">QA</th>
                        <th class="p-3 text-left">Auction</th>
                        <th class="p-3 text-left">State</th>
                        <th class="p-3 text-left">User Name</th>
                        <th class="p-3 text-left">Owner</th>
                        <th class="p-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($listings as $listing)
                        <tr class="hover:bg-muted/5 transition" data-listing-id="{{ $listing['id'] }}">

                            {{-- Checkbox --}}
                            <td class="p-3">
                                <input type="checkbox" class="row-check kt-checkbox" data-id="{{ $listing['id'] }}">
                            </td>

                            {{-- Listing ID --}}
                            <td class="p-3 font-medium">{{ $listing['id'] }}</td>

                            {{-- Vehicle --}}
                            <td class="p-3">{{ $listing['vehicle'] }}</td>

                            {{-- VRM --}}
                            <td class="p-3 text-muted-foreground font-mono">{{ $listing['vrm'] }}</td>

                            {{-- Mileage --}}
                            <td class="p-3 text-right">{{ number_format($listing['mileage']) }}</td>

                            {{-- Valuation --}}
                            <td class="p-3 text-right font-medium">
                                £{{ number_format($listing['valuation'] ?? 0) }}
                                @if(isset($listing['valuation_source']))
                                    <div class="text-xs text-muted-foreground font-normal">{{ $listing['valuation_source'] }}</div>
                                @endif
                            </td>

                            {{-- Reserve? --}}
                            <td class="p-3 text-center">
                                @if($listing['reserve'])
                                    <span class="text-green-600 font-bold">✔</span>
                                @else
                                    <span class="text-red-500 font-bold">✖</span>
                                @endif
                            </td>

                            {{-- BIN / Offer --}}
                            <td class="p-3 text-center">
                                @if($listing['bin'] ?? false)
                                    <span class="kt-badge kt-badge-success">BIN</span>
                                @elseif($listing['offer_enabled'] ?? false)
                                    <span class="kt-badge bg-blue-100 text-blue-700">Offer</span>
                                @else
                                    <span class="text-muted-foreground text-xs">Off</span>
                                @endif
                            </td>

                            {{-- QA --}}
                            <td class="p-3 text-center">
                                <span class="kt-badge
                                    @if(str_contains(strtolower($listing['qa'] ?? ''), 'pass')) kt-badge-success
                                    @elseif(str_contains(strtolower($listing['qa'] ?? ''), 'fail')) kt-badge-danger
                                    @else kt-badge-warning @endif">
                                    {{ $listing['qa'] ?? 'Needs' }}
                                </span>
                            </td>

                            {{-- Auction --}}
                            <td class="p-3">{{ $listing['auction_code'] ?? '—' }}</td>

                            {{-- State --}}
                            <td class="p-3">
                                <span class="kt-badge kt-badge-outline">{{ $listing['state'] }}</span>
                            </td>

                            {{-- User Name --}}
                            <td class="p-3">{{ $listing['user_name'] ?? '—' }}</td>

                            {{-- Owner --}}
                            <td class="p-3">{{ $listing['owner'] }}</td>

                            {{-- Actions --}}
                            <td class="p-3">
                                <div class="flex justify-end gap-1 flex-wrap">
                                    {{-- Bulk valuation status pill (L7) --}}
                                    <span class="bulk-status-pill hidden kt-badge kt-badge-outline text-xs" data-id="{{ $listing['id'] }}"></span>

                                    <button class="kt-btn kt-btn-xs kt-btn-mono open-detail" data-id="{{ $listing['id'] }}">Open</button>
                                    <button class="kt-btn kt-btn-xs kt-btn-outline quick-view" data-id="{{ $listing['id'] }}">Quick View</button>
                                    <select class="kt-input text-xs py-1 w-auto"
                                        onchange="handleListingRowAction(this.value, '{{ $listing['id'] }}'); this.value='';">
                                        <option value="">More…</option>
                                        <option value="assign-owner">Assign Owner</option>
                                        <option value="mark-qa">Mark for QA</option>
                                        <option value="create-auction">Create Auction</option>
                                        <option value="enable-bin">Enable BIN</option>
                                        <option value="pull-valuation">Pull Valuation</option>
                                        <option value="archive">Archive</option>
                                    </select>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="14" class="p-8 text-center text-muted-foreground">
                                No listings found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer --}}
        <div class="p-3 border-t border-border flex flex-wrap items-center justify-between gap-3">
            <div class="flex gap-2">
                <button  id="btn-create-listings" class="kt-btn kt-btn-mono">
                    <i class="ki-filled ki-plus"></i> Create Listing
                </button>
            </div>
            <div class="flex items-center gap-1">
                <button class="kt-btn kt-btn-ghost kt-btn-sm">1</button>
                <button class="kt-btn kt-btn-ghost kt-btn-sm">2</button>
                <button class="kt-btn kt-btn-ghost kt-btn-sm">3</button>
                <span class="px-2 text-muted-foreground">…</span>
            </div>
        </div>
    </div>

</div>

<script>
// Checkbox: select-all
document.getElementById('select-all')?.addEventListener('change', function () {
    document.querySelectorAll('.row-check').forEach(cb => cb.checked = this.checked);
    updateBulkBar();
});
document.addEventListener('change', function (e) {
    if (e.target.classList.contains('row-check')) updateBulkBar();
});

function updateBulkBar() {
    const checked = document.querySelectorAll('.row-check:checked');
    const bar = document.getElementById('bulk-bar');
    const countEl = document.getElementById('selected-count');
    if (countEl) countEl.textContent = checked.length;
    bar?.classList.toggle('hidden', checked.length === 0);
    bar?.classList.toggle('flex', checked.length > 0);
}

// Bulk action handler
document.querySelectorAll('[data-bulk]').forEach(btn => {
    btn.addEventListener('click', function () {
        const action = this.dataset.bulk;
        const ids = [...document.querySelectorAll('.row-check:checked')].map(cb => cb.dataset.id);
        if (!ids.length) return;

        if (action === 'pull-valuations') {
            // Show per-row status pills (L7 requirement)
            ids.forEach(id => {
                const pill = document.querySelector(`.bulk-status-pill[data-id="${id}"]`);
                if (pill) {
                    pill.classList.remove('hidden');
                    pill.textContent = 'In Queue';
                    pill.className = 'bulk-status-pill kt-badge kt-badge-outline text-xs';
                    // Simulate: In queue → Fetching → Succeeded
                    setTimeout(() => { pill.textContent = 'Fetching'; pill.className = 'bulk-status-pill kt-badge kt-badge-warning text-xs'; }, 800);
                    setTimeout(() => { pill.textContent = 'Succeeded +£200'; pill.className = 'bulk-status-pill kt-badge kt-badge-success text-xs'; }, 2200);
                }
            });
        }

        // TODO: POST /listings/bulk with { action, ids }
        console.log('Bulk action:', action, ids);
    });
});

function handleListingRowAction(action, id) {
    if (!action) return;
    console.log('Row action:', action, id);
    // TODO: wire to controller endpoints
}
</script>
