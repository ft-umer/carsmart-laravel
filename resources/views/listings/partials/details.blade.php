<div class="flex flex-col h-screen max-h-[90vh] overflow-hidden">

<div class="
    p-4
    border-b border-border
    flex
    flex-col
    lg:flex-row
    lg:items-start
    lg:justify-between
    gap-3
">
        <div>
            <div class="flex items-center gap-2 flex-wrap">
                <h2 class="text-lg font-semibold">{{ $listing['id'] }}</h2>
                <span class="kt-badge kt-badge-outline">{{ $listing['state'] }}</span>
                <span class="kt-badge kt-badge-outline text-xs">{{ $listing['sale_type'] ?? 'CST1' }}</span>
            </div>
            <div class="text-sm mt-0.5 text-muted-foreground">{{ $listing['vehicle'] }}</div>
            <div class="flex flex-wrap gap-2 mt-2">
                <span class="kt-badge kt-badge-warning">QA: {{ $listing['qa'] }}</span>
                @if (($listing['kyc_status'] ?? '') !== 'Verified')
                    <span class="kt-badge kt-badge-danger">KYC Required</span>
                @endif
                @if (($listing['missing_items'] ?? 0) > 0)
                    <span class="kt-badge kt-badge-warning">{{ $listing['missing_items'] }} Missing Items</span>
                @endif
                <span class="kt-badge kt-badge-outline">Owner: {{ $listing['owner'] }}</span>
            </div>
        </div>
        <button class="kt-btn kt-btn-sm kt-btn-ghost close-modal">✕</button>
    </div>

    {{-- ===== ACTION BAR ===== --}}
    <div class="p-3 border-b border-border">
        <div class="kt-toolbar flex flex-wrap gap-2 overflow-x-auto">
            <button class="kt-btn kt-btn-sm kt-btn-mono" data-listing-action="submit-qa">Submit for QA</button>
            <button class="kt-btn kt-btn-sm kt-btn-outline" data-listing-action="pull-valuation"
                id="pull-valuation-btn">
                <span class="pull-label">Pull Latest Valuation</span>
                <span class="pull-spinner hidden">⟳ Fetching…</span>
            </button>
            <button class="kt-btn kt-btn-sm kt-btn-outline" data-listing-action="apply-pricing">Apply to
                Pricing</button>
            <button class="kt-btn kt-btn-sm kt-btn-outline" data-listing-action="create-auction">Create Auction</button>
            <button class="kt-btn kt-btn-sm kt-btn-outline" data-listing-action="enable-bin">Enable BIN</button>
            <button class="kt-btn kt-btn-sm kt-btn-outline" data-listing-action="send-message">Send Message</button>
            <div class="kt-menu" data-kt-menu="true">
                <button class="kt-btn kt-btn-sm kt-btn-outline">More ▾</button>
                <div class="kt-menu-dropdown w-52">
                    <div class="kt-menu-item"><button class="kt-menu-link">Generate Preview</button></div>
                    <div class="kt-menu-item"><button class="kt-menu-link">Move to Publication Queue</button></div>
                    <div class="kt-menu-item"><button class="kt-menu-link">Duplicate</button></div>
                    <div class="kt-menu-separator"></div>
                    <div class="kt-menu-item"><button class="kt-menu-link text-danger">Archive</button></div>
                </div>
            </div>
        </div>
        {{-- Fetch status toast --}}
        <div id="valuation-fetch-status" class="hidden mt-2 text-xs rounded-lg px-3 py-2 border"></div>
    </div>

    <div class="flex flex-col xl:flex-row flex-1 overflow-hidden min-w-0">

        <div class="flex-1 min-w-0 overflow-y-auto">

          <div class="
    kt-scrollable-x
    flex
    gap-1
    px-4
    pt-3
    border-b border-border
    whitespace-nowrap
">
                @foreach (['Overview', 'Vehicle', 'Seller', 'Media', 'Documents', 'Pricing', 'QA', 'Valuations', 'Auction', 'Notes', 'Activity', 'History'] as $tab)
                    <button
                        class="detail-tab px-3 py-2 rounded-t-lg whitespace-nowrap font-medium
                            @if ($loop->first) bg-background border border-b-0 border-border @else text-muted-foreground hover:text-foreground @endif"
                        data-tab="{{ strtolower($tab) }}">
                        {{ $tab }}
                    </button>
                @endforeach
            </div>

            {{-- Tab panes --}}
            <div class="p-4 space-y-4">

                {{-- OVERVIEW --}}
                <div data-tab-pane="overview">
                    {{-- Valuation card — always visible --}}
                    <div class="rounded-2xl border border-border bg-muted/10 p-4 mb-4">
                        <div class="flex items-center justify-between flex-wrap gap-3 mb-3">
                            <div class="font-semibold text-sm">Latest Valuation</div>
                            <div class="flex gap-2">
                                <button class="kt-btn kt-btn-xs kt-btn-outline"
                                    data-listing-action="pull-valuation">Pull Latest</button>
                                <button class="kt-btn kt-btn-xs kt-btn-outline"
                                    data-listing-action="apply-pricing">Apply to Pricing</button>
                                <button class="kt-btn kt-btn-xs kt-btn-ghost">View History</button>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3">
                            <div>
                                <div class="text-xs text-muted-foreground">Amount</div>
                                <div class="text-xl font-semibold">£{{ number_format($listing['valuation'] ?? 0) }}
                                </div>
                                <div class="text-xs text-muted-foreground">{{ $listing['valuation_source'] ?? '—' }} ·
                                    {{ $listing['valuation_date'] ?? '' }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-muted-foreground">Delta vs Guide</div>
                                @php $dg = ($listing['valuation'] ?? 0) - ($listing['guide'] ?? 0); @endphp
                                <div class="text-lg font-semibold {{ $dg >= 0 ? 'text-green-600' : 'text-red-500' }}">
                                    {{ $dg >= 0 ? '+' : '' }}£{{ number_format(abs($dg)) }}
                                    <span
                                        class="text-sm">({{ $dg >= 0 ? '+' : '' }}{{ $listing['guide'] ? round(($dg / $listing['guide']) * 100, 1) : 0 }}%)</span>
                                </div>
                            </div>
                            <div>
                                <div class="text-xs text-muted-foreground">Delta vs Reserve</div>
                                @php $dr = ($listing['valuation'] ?? 0) - ($listing['reserve'] ?? 0); @endphp
                                <div class="text-lg font-semibold {{ $dr >= 0 ? 'text-green-600' : 'text-red-500' }}">
                                    {{ $dr >= 0 ? '+' : '' }}£{{ number_format(abs($dr)) }}
                                </div>
                            </div>
                            <div>
                                <div class="text-xs text-muted-foreground">Source</div>
                                <div class="font-medium">{{ $listing['valuation_source'] ?? '—' }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Summary grid --}}
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach ([['VRM', $listing['vrm']], ['VIN', $listing['vin'] ?? '—'], ['Mileage', number_format($listing['mileage'] ?? 0) . ' mi'], ['Fuel', $listing['fuel'] ?? '—'], ['Transmission', $listing['transmission'] ?? '—'], ['Colour', $listing['colour'] ?? '—'], ['Guide Price', '£' . number_format($listing['guide'] ?? 0)], ['Reserve', $listing['reserve'] ? '£' . number_format($listing['reserve']) : 'Not set'], ['BIN', $listing['bin'] ?? false ? '£' . number_format($listing['bin_price'] ?? 0) : 'Off'], ['Offer', $listing['offer_enabled'] ?? false ? 'Enabled' : 'Off'], ['Auction', $listing['auction_code'] ?? '—'], ['KYC', $listing['kyc_status'] ?? '—']] as [$label, $value])
                            <div class="card border border-border p-3">
                                <div class="text-xs text-muted-foreground">{{ $label }}</div>
                                <div class="font-medium mt-0.5">{{ $value }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- VEHICLE --}}
                <div data-tab-pane="vehicle" class="hidden">
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm">
                        @foreach ([['VRM', $listing['vrm']], ['VIN', $listing['vin'] ?? '—'], ['Make', 'BMW'], ['Model', '3 Series'], ['Derivative', 'M Sport'], ['Year', '2019'], ['Mileage', number_format($listing['mileage'])], ['Colour', $listing['colour'] ?? '—'], ['Fuel', $listing['fuel'] ?? '—'], ['Transmission', $listing['transmission'] ?? '—'], ['Body', 'Saloon'], ['Doors', '4'], ['Seats', '5'], ['ULEZ', 'Yes'], ['MOT Expiry', '2027-04-12'], ['Keys', '2']] as [$l, $v])
                            <div class="card border border-border p-3">
                                <div class="text-xs text-muted-foreground">{{ $l }}</div>
                                <div class="font-medium">{{ $v }}</div>
                            </div>
                        @endforeach
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                        <div class="card border border-border p-3">
                            <div class="text-xs text-muted-foreground mb-1">Condition Notes</div>
                            <div class="text-sm">Light alloy scuff rear-right. Interior clean.</div>
                        </div>
                        <div class="card border border-border p-3">
                            <div class="text-xs text-muted-foreground mb-1">Known Faults</div>
                            <div class="text-sm">None declared.</div>
                        </div>
                    </div>
                </div>

                {{-- SELLER --}}
                <div data-tab-pane="seller" class="hidden">
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3 text-sm">
                        @foreach ([['Seller', 'John Reynolds'], ['Type', 'Private'], ['Sale Type', $listing['sale_type'] ?? 'CST1'], ['Email', 'j.reynolds@example.com'], ['Phone', '+44 7911 123456'], ['Preferred Channel', 'Phone'], ['KYC Status', $listing['kyc_status'] ?? 'Pending'], ['Address', '12 Park Lane, London, W1K 1AB']] as [$l, $v])
                            <div
                                class="card border border-border p-3 @if ($l === 'Address') md:col-span-2 @endif">
                                <div class="text-xs text-muted-foreground">{{ $l }}</div>
                                <div class="font-medium">{{ $v }}</div>
                            </div>
                        @endforeach
                    </div>
                    @if (($listing['kyc_status'] ?? '') !== 'Verified')
                        <div class="mt-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                            KYC not verified — listing cannot move to Publication Queue until resolved.
                        </div>
                    @endif
                </div>

                {{-- MEDIA --}}
                <div data-tab-pane="media" class="hidden">
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3">
                        @foreach (['Front 3/4', 'Rear 3/4', 'Odometer', 'VIN Plate', 'Engine Bay', 'Interior'] as $photo)
                            <div
                                class="card border border-border rounded-xl p-3 flex flex-col items-center justify-center text-center gap-2 h-36 bg-muted/10">
                                <div class="text-2xl">📷</div>
                                <div class="text-xs font-medium">{{ $photo }}</div>
                                <span class="kt-badge kt-badge-warning text-xs">Missing</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-3 text-sm text-muted-foreground">Video URL: <span class="text-foreground">—</span>
                    </div>
                </div>

                {{-- DOCUMENTS --}}
                <div data-tab-pane="documents" class="hidden">
                    <div class="space-y-2 text-sm">
                        @foreach (['V5C Front', 'V5C Back', 'MOT Certificate', 'Service Receipts', 'Other Proofs'] as $doc)
                            <div class="card border border-border p-3 flex items-center justify-between">
                                <span>{{ $doc }}</span>
                                <span class="kt-badge kt-badge-warning">Missing</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- PRICING --}}
                <div data-tab-pane="pricing" class="hidden">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @foreach ([['Guide Price', '£' . number_format($listing['guide'] ?? 0)], ['Reserve', '£' . number_format($listing['reserve'] ?? 0)], ['BIN Price', $listing['bin'] ?? false ? '£' . number_format($listing['bin_price']) : 'Off'], ['Make Offer', $listing['offer_enabled'] ?? false ? 'Enabled (≥98% accept / <90% decline)' : 'Off']] as [$l, $v])
                            <div class="card border border-border p-3">
                                <div class="text-xs text-muted-foreground">{{ $l }}</div>
                                <div class="font-medium mt-0.5">{{ $v }}</div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                        BIN cannot be active simultaneously with a Reserve price. If BIN is enabled, Reserve must be
                        blank.
                    </div>
                </div>

                {{-- QA --}}
                <div data-tab-pane="qa" class="hidden">
                    <div class="space-y-2">
                        @foreach ([['Required Photos', 'Incomplete', 'danger'], ['V5C Document', 'Missing', 'danger'], ['MOT Certificate', 'Present', 'success'], ['Pricing Set', 'Complete', 'success'], ['KYC Verified', 'Pending', 'warning']] as [$item, $status, $badge])
                            <div class="card border border-border p-3 flex items-center justify-between text-sm">
                                <span>{{ $item }}</span>
                                <span class="kt-badge kt-badge-{{ $badge }}">{{ $status }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex gap-2 mt-4">
                        <button class="kt-btn kt-btn-sm kt-btn-mono">Pass QA</button>
                        <button class="kt-btn kt-btn-sm kt-btn-outline text-danger">Fail with Reasons</button>
                        <button class="kt-btn kt-btn-sm kt-btn-outline">Request Changes</button>
                    </div>
                </div>

                {{-- VALUATIONS --}}
                <div data-tab-pane="valuations" class="hidden">
                    <div class="flex gap-2 mb-4">
                        <button class="kt-btn kt-btn-sm kt-btn-mono" data-listing-action="add-valuation">Add
                            Valuation</button>
                        <button class="kt-btn kt-btn-sm kt-btn-outline" data-listing-action="pull-valuation">Pull
                            Latest Valuation</button>
                        <button class="kt-btn kt-btn-sm kt-btn-outline" data-listing-action="apply-pricing">Apply to
                            Pricing</button>
                    </div>
                    <div class="card border border-border">
    <div class="overflow-x-auto">
                       <table class="kt-table min-w-[900px]">
                            <thead class="bg-muted/40">
                                <tr>
                                    <th class="p-3 text-left">Date</th>
                                    <th class="p-3 text-left">Source</th>
                                    <th class="p-3 text-left">Valuer</th>
                                    <th class="p-3 text-right">Amount</th>
                                    <th class="p-3 text-right">Δ vs Guide</th>
                                    <th class="p-3 text-left">Notes</th>
                                    <th class="p-3 text-center">Comps</th>
                                    <th class="p-3 text-center">Used</th>
                                    <th class="p-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($listing['valuations'] ?? [] as $v)
                                    <tr class="border-t border-border">
                                        <td class="p-3">{{ $v['date'] }}</td>
                                        <td class="p-3">{{ $v['source'] }}</td>
                                        <td class="p-3">{{ $v['valuer'] }}</td>
                                        <td class="p-3 text-right font-medium">£{{ number_format($v['amount']) }}</td>
                                        <td
                                            class="p-3 text-right {{ str_contains($v['delta'], '+') ? 'text-green-600' : 'text-red-500' }}">
                                            {{ $v['delta'] }}</td>
                                        <td class="p-3">{{ $v['notes'] ?? '—' }}</td>
                                        <td class="p-3 text-center">{{ $v['comps'] ?? 0 }}</td>
                                        <td class="p-3 text-center">
                                            @if ($v['used'] ?? false)
                                                ✔
                                            @endif
                                        </td>
                                        <td class="p-3 text-right">
                                            <div class="flex justify-end gap-1">
                                                <button class="kt-btn kt-btn-xs kt-btn-outline">Apply</button>
                                                <button
                                                    class="kt-btn kt-btn-xs kt-btn-ghost text-danger">Remove</button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="p-4 text-center text-muted-foreground">No valuations
                                            yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- AUCTION --}}
                <div data-tab-pane="auction" class="hidden">
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm">
                        <div class="card border border-border p-3">
                            <div class="text-xs text-muted-foreground">Auction Code</div>
                            <div class="font-medium">{{ $listing['auction_code'] ?? '—' }}</div>
                        </div>
                        <div class="card border border-border p-3">
                            <div class="text-xs text-muted-foreground">Status</div>
                            <div class="font-medium">{{ $listing['auction_status'] ?? '—' }}</div>
                        </div>
                        <div class="card border border-border p-3">
                            <div class="text-xs text-muted-foreground">Sniper Protection</div>
                            <div class="font-medium">Active (5 min)</div>
                        </div>
                    </div>
                    <div class="flex gap-2 mt-4">
                        <button class="kt-btn kt-btn-sm kt-btn-mono">Assign to Auction</button>
                        <button class="kt-btn kt-btn-sm kt-btn-outline">Create New Auction</button>
                    </div>
                </div>

                {{-- NOTES --}}
                <div data-tab-pane="notes" class="hidden">
                    <textarea class="kt-input w-full" rows="6" placeholder="Add internal notes…"></textarea>
                    <button class="kt-btn kt-btn-sm kt-btn-mono mt-2">Save Note</button>
                </div>

                {{-- ACTIVITY --}}
                <div data-tab-pane="activity" class="hidden">
                    <div class="space-y-2 text-sm">
                        @foreach ([['listing_created', 'Listing created by JR', '2026-05-31 09:01'], ['valuation_fetched', 'Valuation pulled from Carsmart — £14,200', '2026-05-31 09:03'], ['media_uploaded', 'Photo uploaded: Front 3/4', '2026-05-31 09:10']] as [$event, $msg, $time])
                            <div class="card border border-border p-3 flex items-start justify-between gap-2">
                                <div>
                                    <span class="text-xs font-mono text-muted-foreground">{{ $event }}</span>
                                    <div>{{ $msg }}</div>
                                </div>
                                <div class="text-xs text-muted-foreground whitespace-nowrap">{{ $time }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- HISTORY --}}
                <div data-tab-pane="history" class="hidden">
                    <div class="space-y-2 text-sm">
                        <div class="card border border-border p-3">
                            <div class="text-xs text-muted-foreground">State: Draft → QA (pending)</div>
                            <div>Submitted by JR · 2026-05-31</div>
                        </div>
                        <div class="card border border-border p-3">
                            <div class="text-xs text-muted-foreground">Valuation applied: Guide £14,250 → £14,200</div>
                            <div>Updated by System · 2026-05-31</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div
            class="
    hidden xl:flex
    flex-col
    w-80
    shrink-0
    border-l border-border
    p-4
    gap-4
    overflow-y-auto
">

            {{-- Summary --}}
            <div>
                <div class="font-semibold text-sm mb-2">Summary</div>
                <div class="space-y-1 text-xs text-muted-foreground">
                    <div>State: <span class="text-foreground font-medium">{{ $listing['state'] }}</span></div>
                    <div>Owner: <span class="text-foreground font-medium">{{ $listing['owner'] }}</span></div>
                    <div>QA: <span class="text-foreground font-medium">{{ $listing['qa'] }}</span></div>
                    <div>KYC: <span
                            class="text-foreground font-medium">{{ $listing['kyc_status'] ?? 'Pending' }}</span></div>
                    <div>Missing: <span class="text-foreground font-medium">{{ $listing['missing_items'] ?? 0 }}
                            items</span></div>
                </div>
            </div>

            {{-- Edit Essentials --}}
            <div>
                <div class="font-semibold text-sm mb-2">Edit Essentials</div>
                <div class="space-y-2">
                    <input class="kt-input w-full text-xs" placeholder="Guide price £"
                        value="{{ $listing['guide'] ?? '' }}">
                    <input class="kt-input w-full text-xs" placeholder="Reserve £"
                        value="{{ $listing['reserve'] ?? '' }}">
                    <button class="kt-btn kt-btn-xs kt-btn-mono w-full">Save</button>
                </div>
            </div>

            {{-- Communications --}}
            <div>
                <div class="font-semibold text-sm mb-2">Communications</div>
                <div class="space-y-2 text-xs">
                    <div class="card border border-border p-2 rounded-lg">
                        <div class="text-muted-foreground">Email sent to seller</div>
                        <div>2026-05-30 · JR</div>
                    </div>
                    <button class="kt-btn kt-btn-xs kt-btn-outline w-full">Send Message</button>
                </div>
            </div>

        </div>

    </div>

</div>

<script>
    (function() {
        const panes = document.querySelectorAll('[data-tab-pane]');
        const tabs = document.querySelectorAll('.detail-tab');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const target = tab.dataset.tab;

                panes.forEach(p => p.classList.add('hidden'));
                tabs.forEach(t => {
                    t.classList.remove('bg-background', 'border', 'border-b-0',
                        'border-border');
                    t.classList.add('text-muted-foreground');
                });

                document.querySelector(`[data-tab-pane="${target}"]`)?.classList.remove('hidden');
                tab.classList.add('bg-background', 'border', 'border-b-0', 'border-border');
                tab.classList.remove('text-muted-foreground');
            });
        });
    })();
</script>
