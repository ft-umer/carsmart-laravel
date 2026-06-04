{{--
    L2 — Listing Detail (Record view)
    Loaded as AJAX partial into #modal-content inside listing-detail-modal.
    Fixes:
      - Overview valuation card: amount + source + timestamp + delta vs Guide AND Reserve
        + View History / Pull Latest Valuation / Apply to Pricing buttons
      - Full 12-tab structure retained
      - All action buttons wired with data-listing-action attributes
--}}
<div class="flex flex-col h-screen max-h-[92vh] overflow-hidden">

    {{-- ===== HEADER ===== --}}
    <div class="p-4 border-b border-border flex flex-col lg:flex-row lg:items-start lg:justify-between gap-3 shrink-0">
        <div>
            <div class="flex items-center gap-2 flex-wrap">
                <h2 class="text-base font-semibold">{{ $listing['id'] }}</h2>
                <span class="kt-badge kt-badge-outline">{{ $listing['state'] }}</span>
                <span class="kt-badge kt-badge-outline text-xs">{{ $listing['sale_type'] ?? 'CST1' }}</span>
            </div>
            <div class="text-sm mt-0.5 text-muted-foreground">{{ $listing['vehicle'] }}</div>
            <div class="flex flex-wrap gap-1.5 mt-2">
                @if (($listing['qa'] ?? '') === 'Needs Review')
                    <span class="kt-badge kt-badge-warning">QA: {{ $listing['qa'] }}</span>
                @elseif (($listing['qa'] ?? '') === 'Pass')
                    <span class="kt-badge kt-badge-success">QA: Pass</span>
                @else
                    <span class="kt-badge kt-badge-outline">QA: {{ $listing['qa'] ?? '—' }}</span>
                @endif
                @if (($listing['kyc_status'] ?? '') !== 'Verified')
                    <span class="kt-badge kt-badge-danger">KYC Required</span>
                @else
                    <span class="kt-badge kt-badge-success">KYC Verified</span>
                @endif
                @if (($listing['missing_items'] ?? 0) > 0)
                    <span class="kt-badge kt-badge-warning">{{ $listing['missing_items'] }} Missing Items</span>
                @endif
                <span class="kt-badge kt-badge-outline">Owner: {{ $listing['owner'] }}</span>
            </div>
        </div>
        <button class="kt-btn kt-btn-sm kt-btn-ghost close-modal self-start lg:self-center">
            <i class="ki-filled ki-cross"></i>
        </button>
    </div>

    {{-- ===== ACTION BAR ===== --}}
    <div class="px-4 py-2.5 border-b border-border shrink-0">
        <div class="flex flex-wrap gap-1.5">
            <button class="kt-btn kt-btn-sm kt-btn-mono" data-listing-action="submit-qa">
                <i class="ki-filled ki-send"></i> Submit for QA
            </button>
            <button class="kt-btn kt-btn-sm kt-btn-outline" data-listing-action="pull-valuation" id="pull-valuation-btn">
                <i class="ki-filled ki-chart-line-up pull-icon"></i>
                <span class="ki-filled ki-loading pull-spinner hidden animate-spin"></span>
                <span class="pull-label">Pull Latest Valuation</span>
            </button>
            <button class="kt-btn kt-btn-sm kt-btn-outline" data-listing-action="apply-pricing">
                <i class="ki-filled ki-price-tag"></i> Apply to Pricing
            </button>
            <button class="kt-btn kt-btn-sm kt-btn-outline" data-listing-action="add-valuation">
                <i class="ki-filled ki-add-item"></i> Add Valuation
            </button>
            <button class="kt-btn kt-btn-sm kt-btn-outline" data-listing-action="create-auction">
                <i class="ki-filled ki-flag"></i> Create Auction
            </button>
            <button class="kt-btn kt-btn-sm kt-btn-outline" data-listing-action="enable-bin">
                <i class="ki-filled ki-shop"></i> Enable BIN
            </button>
            <button class="kt-btn kt-btn-sm kt-btn-outline" data-listing-action="send-message">
                <i class="ki-filled ki-message-text"></i> Send Message
            </button>
            <div class="kt-menu" data-kt-menu="true">
                <button class="kt-btn kt-btn-sm kt-btn-outline">
                    More <i class="ki-filled ki-down text-xs"></i>
                </button>
                <div class="kt-menu-dropdown w-56">
                    <div class="kt-menu-item">
                        <button class="kt-menu-link" data-listing-action="generate-preview">
                            <i class="ki-filled ki-eye kt-menu-icon"></i> Generate Preview
                        </button>
                    </div>
                    <div class="kt-menu-item">
                        <button class="kt-menu-link" data-listing-action="publication-queue">
                            <i class="ki-filled ki-check-circle kt-menu-icon"></i> Move to Publication Queue
                        </button>
                    </div>
                    <div class="kt-menu-item">
                        <button class="kt-menu-link" data-listing-action="duplicate">
                            <i class="ki-filled ki-copy kt-menu-icon"></i> Duplicate
                        </button>
                    </div>
                    <div class="kt-menu-separator"></div>
                    <div class="kt-menu-item">
                        <button class="kt-menu-link text-danger" data-listing-action="archive">
                            <i class="ki-filled ki-trash kt-menu-icon"></i> Archive
                        </button>
                    </div>
                </div>
            </div>
        </div>
        {{-- Fetch status toast --}}
        <div id="valuation-fetch-status" class="hidden mt-2 text-xs rounded-lg px-3 py-2 border"></div>
    </div>

    {{-- ===== BODY: TABS + RIGHT PANEL ===== --}}
    <div class="flex flex-col xl:flex-row flex-1 overflow-hidden min-w-0">

        {{-- Main content --}}
        <div class="flex-1 min-w-0 overflow-y-auto">

            {{-- Tab nav --}}
            <div class="kt-scrollable-x flex gap-0.5 px-4 pt-3 border-b border-border whitespace-nowrap shrink-0">
                @foreach (['Overview', 'Vehicle', 'Seller', 'Media', 'Documents', 'Pricing', 'QA', 'Valuations', 'Auction', 'Notes', 'Activity', 'History'] as $tab)
                    <button
                        class="detail-tab px-3 py-2 text-sm rounded-t-lg whitespace-nowrap font-medium transition-colors
                            @if ($loop->first) bg-background border border-b-0 border-border text-foreground
                            @else text-muted-foreground hover:text-foreground @endif"
                        data-tab="{{ strtolower($tab) }}">
                        {{ $tab }}
                    </button>
                @endforeach
            </div>

            {{-- Tab panes --}}
            <div class="p-4 space-y-4">

                {{-- ===== OVERVIEW ===== --}}
                <div data-tab-pane="overview">

                    {{-- Valuation card — always visible on Overview (L2 spec) --}}
                    <div class="rounded-2xl border border-border bg-card p-4 mb-4 shadow-sm">

                        {{-- Card header --}}
                        <div class="flex items-center justify-between flex-wrap gap-3 mb-4">
                            <div>
                                <div class="font-semibold text-sm">Latest Valuation</div>
                                <div class="text-xs text-muted-foreground mt-0.5">
                                    {{ $listing['valuation_source'] ?? '—' }}
                                    @if (!empty($listing['valuation_date']))
                                        &middot; {{ $listing['valuation_date'] }}
                                    @endif
                                </div>
                            </div>
                            <div class="flex gap-2 flex-wrap">
                                {{-- View History — scrolls/switches to Valuations tab --}}
                                <button
                                    class="kt-btn kt-btn-xs kt-btn-ghost"
                                    data-listing-action="view-valuation-history">
                                    <i class="ki-filled ki-time text-xs"></i> View History
                                </button>
                                {{-- Pull Latest Valuation --}}
                                <button
                                    class="kt-btn kt-btn-xs kt-btn-outline"
                                    data-listing-action="pull-valuation">
                                    <i class="ki-filled ki-chart-line-up text-xs"></i> Pull Latest
                                </button>
                                {{-- Apply to Pricing --}}
                                <button
                                    class="kt-btn kt-btn-xs kt-btn-mono"
                                    data-listing-action="apply-pricing">
                                    <i class="ki-filled ki-price-tag text-xs"></i> Apply to Pricing
                                </button>
                            </div>
                        </div>

                        {{-- Valuation figures grid --}}
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">

                            {{-- Amount --}}
                            <div class="rounded-xl border border-border bg-muted/10 p-3">
                                <div class="text-xs text-muted-foreground mb-1">Amount</div>
                                <div class="text-xl font-bold">
                                    £{{ number_format($listing['valuation'] ?? 0) }}
                                </div>
                                <div class="text-xs text-muted-foreground mt-0.5">
                                    {{ $listing['valuation_source'] ?? '—' }}
                                </div>
                            </div>

                            {{-- Delta vs Guide --}}
                            @php
                                $dg = ($listing['valuation'] ?? 0) - ($listing['guide'] ?? 0);
                                $dgPct = ($listing['guide'] ?? 0) > 0
                                    ? round(($dg / $listing['guide']) * 100, 1)
                                    : 0;
                            @endphp
                            <div class="rounded-xl border border-border bg-muted/10 p-3">
                                <div class="text-xs text-muted-foreground mb-1">Delta vs Guide</div>
                                <div class="text-lg font-semibold {{ $dg >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $dg >= 0 ? '+' : '−' }}£{{ number_format(abs($dg)) }}
                                </div>
                                <div class="text-xs {{ $dg >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $dg >= 0 ? '+' : '' }}{{ $dgPct }}%
                                </div>
                            </div>

                            {{-- Delta vs Reserve --}}
                            @php
                                $dr = ($listing['valuation'] ?? 0) - ($listing['reserve'] ?? 0);
                                $drPct = ($listing['reserve'] ?? 0) > 0
                                    ? round(($dr / $listing['reserve']) * 100, 1)
                                    : 0;
                            @endphp
                            <div class="rounded-xl border border-border bg-muted/10 p-3">
                                <div class="text-xs text-muted-foreground mb-1">Delta vs Reserve</div>
                                @if (($listing['reserve'] ?? null))
                                    <div class="text-lg font-semibold {{ $dr >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $dr >= 0 ? '+' : '−' }}£{{ number_format(abs($dr)) }}
                                    </div>
                                    <div class="text-xs {{ $dr >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $dr >= 0 ? '+' : '' }}{{ $drPct }}%
                                    </div>
                                @else
                                    <div class="text-sm text-muted-foreground mt-1">Reserve not set</div>
                                @endif
                            </div>

                            {{-- Source --}}
                            <div class="rounded-xl border border-border bg-muted/10 p-3">
                                <div class="text-xs text-muted-foreground mb-1">Source</div>
                                <div class="font-semibold text-sm">{{ $listing['valuation_source'] ?? '—' }}</div>
                                <div class="text-xs text-muted-foreground mt-0.5">
                                    {{ $listing['valuation_date'] ?? '' }}
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- Summary grid --}}
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach ([
                            ['VRM',          $listing['vrm'] ?? '—'],
                            ['VIN',          $listing['vin'] ?? '—'],
                            ['Mileage',      number_format($listing['mileage'] ?? 0) . ' mi'],
                            ['Fuel',         $listing['fuel'] ?? '—'],
                            ['Transmission', $listing['transmission'] ?? '—'],
                            ['Colour',       $listing['colour'] ?? '—'],
                            ['Guide Price',  '£' . number_format($listing['guide'] ?? 0)],
                            ['Reserve',      ($listing['reserve'] ?? null) ? '£' . number_format($listing['reserve']) : 'Not set'],
                            ['BIN',          ($listing['bin'] ?? false) ? '£' . number_format($listing['bin_price'] ?? 0) : 'Off'],
                            ['Make Offer',   ($listing['offer_enabled'] ?? false) ? 'Enabled' : 'Off'],
                            ['Auction',      $listing['auction_code'] ?? '—'],
                            ['KYC',          $listing['kyc_status'] ?? '—'],
                        ] as [$label, $value])
                            <div class="card border border-border p-3 rounded-xl">
                                <div class="text-xs text-muted-foreground">{{ $label }}</div>
                                <div class="font-medium mt-0.5 text-sm">{{ $value }}</div>
                            </div>
                        @endforeach
                    </div>

                </div>

                {{-- ===== VEHICLE ===== --}}
                <div data-tab-pane="vehicle" class="hidden">
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm">
                        @foreach ([
                            ['VRM',          $listing['vrm'] ?? '—'],
                            ['VIN',          $listing['vin'] ?? '—'],
                            ['Make',         'BMW'],
                            ['Model',        '3 Series'],
                            ['Derivative',   'M Sport'],
                            ['Year',         '2019'],
                            ['Mileage',      number_format($listing['mileage'] ?? 0)],
                            ['Colour',       $listing['colour'] ?? '—'],
                            ['Fuel',         $listing['fuel'] ?? '—'],
                            ['Transmission', $listing['transmission'] ?? '—'],
                            ['Body',         'Saloon'],
                            ['Doors',        '4'],
                            ['Seats',        '5'],
                            ['ULEZ',         'Yes'],
                            ['MOT Expiry',   '2027-04-12'],
                            ['Keys',         '2'],
                            ['Service Hist.','Full'],
                        ] as [$l, $v])
                            <div class="card border border-border p-3 rounded-xl">
                                <div class="text-xs text-muted-foreground">{{ $l }}</div>
                                <div class="font-medium">{{ $v }}</div>
                            </div>
                        @endforeach
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                        <div class="card border border-border p-3 rounded-xl">
                            <div class="text-xs text-muted-foreground mb-1">Condition Notes</div>
                            <div class="text-sm">Light alloy scuff rear-right. Interior clean.</div>
                        </div>
                        <div class="card border border-border p-3 rounded-xl">
                            <div class="text-xs text-muted-foreground mb-1">Known Faults</div>
                            <div class="text-sm">None declared.</div>
                        </div>
                    </div>
                </div>

                {{-- ===== SELLER ===== --}}
                <div data-tab-pane="seller" class="hidden">
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3 text-sm">
                        @foreach ([
                            ['Seller',             'John Reynolds'],
                            ['Type',               'Private'],
                            ['Sale Type',          $listing['sale_type'] ?? 'CST1'],
                            ['Email',              'j.reynolds@example.com'],
                            ['Phone',              '+44 7911 123456'],
                            ['Preferred Channel',  'Phone'],
                            ['KYC / KYB Status',   $listing['kyc_status'] ?? 'Pending'],
                            ['Business Number',    '—'],
                            ['Address',            '12 Park Lane, London, W1K 1AB'],
                        ] as [$l, $v])
                            <div class="card border border-border p-3 rounded-xl @if($l === 'Address') md:col-span-2 @endif">
                                <div class="text-xs text-muted-foreground">{{ $l }}</div>
                                <div class="font-medium">{{ $v }}</div>
                            </div>
                        @endforeach
                    </div>
                    @if (($listing['kyc_status'] ?? '') !== 'Verified')
                        <div class="mt-3 rounded-xl border border-danger/30 bg-danger/5 px-4 py-3 text-sm text-danger">
                            <i class="ki-filled ki-shield-cross mr-1"></i>
                            KYC not verified — listing cannot move to Publication Queue until resolved.
                        </div>
                    @endif
                </div>

                {{-- ===== MEDIA ===== --}}
                <div data-tab-pane="media" class="hidden">
                    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3">
                        @foreach (['Front 3/4', 'Rear 3/4', 'Odometer', 'VIN Plate', 'Engine Bay', 'Interior'] as $photo)
                            <div class="card border border-border rounded-xl p-3 flex flex-col items-center justify-center text-center gap-2 h-36 bg-muted/10">
                                <i class="ki-filled ki-picture text-3xl text-muted-foreground"></i>
                                <div class="text-xs font-medium">{{ $photo }}</div>
                                <span class="kt-badge kt-badge-warning text-xs">Missing</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-3 text-sm text-muted-foreground">
                        Video URL: <span class="text-foreground">—</span>
                    </div>
                    <div class="mt-3">
                        <button class="kt-btn kt-btn-sm kt-btn-outline">
                            <i class="ki-filled ki-add-files"></i> Upload Photos
                        </button>
                    </div>
                </div>

                {{-- ===== DOCUMENTS ===== --}}
                <div data-tab-pane="documents" class="hidden">
                    <div class="space-y-2 text-sm">
                        @foreach ([
                            ['V5C Front',      'Missing', 'danger'],
                            ['V5C Back',       'Missing', 'danger'],
                            ['MOT Certificate','Present', 'success'],
                            ['Service Receipts','Missing','danger'],
                            ['Other Proofs',   'Missing', 'warning'],
                        ] as [$doc, $status, $badge])
                            <div class="card border border-border p-3 flex items-center justify-between rounded-xl">
                                <div class="flex items-center gap-2">
                                    <i class="ki-filled ki-file-up text-muted-foreground"></i>
                                    <span>{{ $doc }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="kt-badge kt-badge-{{ $badge }}">{{ $status }}</span>
                                    <button class="kt-btn kt-btn-xs kt-btn-outline">Upload</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- ===== PRICING ===== --}}
                <div data-tab-pane="pricing" class="hidden">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @foreach ([
                            ['Guide Price', '£' . number_format($listing['guide'] ?? 0)],
                            ['Reserve',     ($listing['reserve'] ?? null) ? '£' . number_format($listing['reserve']) : 'Not set'],
                            ['BIN Price',   ($listing['bin'] ?? false) ? '£' . number_format($listing['bin_price'] ?? 0) : 'Off'],
                            ['Make Offer',  ($listing['offer_enabled'] ?? false) ? 'Enabled (≥98% / <90%)' : 'Off'],
                        ] as [$l, $v])
                            <div class="card border border-border p-3 rounded-xl">
                                <div class="text-xs text-muted-foreground">{{ $l }}</div>
                                <div class="font-medium mt-0.5">{{ $v }}</div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-3 rounded-xl border border-warning/30 bg-warning/10 px-4 py-3 text-sm text-warning-foreground">
                        <i class="ki-filled ki-information-4 mr-1"></i>
                        BIN cannot be active simultaneously with a Reserve price. If BIN is enabled, Reserve must be blank.
                    </div>
                    <div class="flex gap-2 mt-3">
                        <button class="kt-btn kt-btn-sm kt-btn-outline" data-listing-action="apply-pricing">Apply Valuation to Pricing</button>
                        <button class="kt-btn kt-btn-sm kt-btn-outline" data-listing-action="enable-bin">Toggle BIN</button>
                    </div>
                </div>

                {{-- ===== QA ===== --}}
                <div data-tab-pane="qa" class="hidden">
                    <div class="space-y-2">
                        @foreach ([
                            ['Required Photos (6)',   'Incomplete', 'danger'],
                            ['V5C Document',          'Missing',    'danger'],
                            ['MOT Certificate',       'Present',    'success'],
                            ['Pricing Set',           'Complete',   'success'],
                            ['KYC Verified',          'Pending',    'warning'],
                            ['Compliance Confirmed',  'Done',       'success'],
                        ] as [$item, $status, $badge])
                            <div class="card border border-border p-3 flex items-center justify-between text-sm rounded-xl">
                                <span>{{ $item }}</span>
                                <span class="kt-badge kt-badge-{{ $badge }}">{{ $status }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex gap-2 mt-4 flex-wrap">
                        <button class="kt-btn kt-btn-sm kt-btn-mono" data-listing-action="approve-qa">
                            <i class="ki-filled ki-check"></i> Pass QA
                        </button>
                        <button class="kt-btn kt-btn-sm kt-btn-outline text-danger" data-listing-action="fail-qa">
                            <i class="ki-filled ki-cross"></i> Fail with Reasons
                        </button>
                        <button class="kt-btn kt-btn-sm kt-btn-outline" data-listing-action="request-changes">
                            <i class="ki-filled ki-message-text"></i> Request Changes
                        </button>
                        <button class="kt-btn kt-btn-sm kt-btn-ghost" data-listing-action="assign-reviewer">
                            <i class="ki-filled ki-user"></i> Assign Reviewer
                        </button>
                    </div>
                </div>

                {{-- ===== VALUATIONS ===== --}}
                <div data-tab-pane="valuations" class="hidden">

                    {{-- Action bar --}}
                    <div class="flex gap-2 mb-4 flex-wrap">
                        <button class="kt-btn kt-btn-sm kt-btn-mono" data-listing-action="add-valuation">
                            <i class="ki-filled ki-add-item"></i> Add Valuation
                        </button>
                        <button class="kt-btn kt-btn-sm kt-btn-outline" data-listing-action="pull-valuation">
                            <i class="ki-filled ki-chart-line-up"></i> Pull Latest Valuation
                        </button>
                        <button class="kt-btn kt-btn-sm kt-btn-outline" data-listing-action="apply-pricing">
                            <i class="ki-filled ki-price-tag"></i> Apply to Pricing
                        </button>
                    </div>

                    {{-- Fetch status --}}
                    <div id="val-tab-fetch-status" class="hidden mb-3 text-xs rounded-lg px-3 py-2 border"></div>

                    {{-- Valuations table --}}
                    <div class="card border border-border rounded-xl overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="kt-table min-w-[960px]">
                                <thead class="bg-muted/40">
                                    <tr>
                                        <th class="p-3 text-left text-xs">Date</th>
                                        <th class="p-3 text-left text-xs">Source</th>
                                        <th class="p-3 text-left text-xs">Valuer</th>
                                        <th class="p-3 text-right text-xs">Amount</th>
                                        <th class="p-3 text-right text-xs">Δ vs Guide</th>
                                        <th class="p-3 text-left text-xs">Notes</th>
                                        <th class="p-3 text-center text-xs">Comps</th>
                                        <th class="p-3 text-center text-xs">Used</th>
                                        <th class="p-3 text-right text-xs">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($listing['valuations'] ?? [] as $v)
                                        <tr class="border-t border-border hover:bg-muted/5 transition-colors">
                                            <td class="p-3 text-sm">{{ $v['date'] }}</td>
                                            <td class="p-3 text-sm">{{ $v['source'] }}</td>
                                            <td class="p-3 text-sm">{{ $v['valuer'] ?? 'System' }}</td>
                                            <td class="p-3 text-right text-sm font-medium">£{{ number_format($v['amount']) }}</td>
                                            <td class="p-3 text-right text-sm {{ str_contains($v['delta'] ?? '', '+') ? 'text-success' : 'text-danger' }}">
                                                {{ $v['delta'] ?? '—' }}
                                            </td>
                                            <td class="p-3 text-sm text-muted-foreground">{{ $v['notes'] ?? '—' }}</td>
                                            <td class="p-3 text-center text-sm">{{ $v['comps'] ?? 0 }}</td>
                                            <td class="p-3 text-center">
                                                @if ($v['used'] ?? false)
                                                    <i class="ki-filled ki-check-circle text-success"></i>
                                                @else
                                                    <span class="text-muted-foreground">—</span>
                                                @endif
                                            </td>
                                            <td class="p-3 text-right">
                                                <div class="flex justify-end gap-1">
                                                    <button
                                                        class="kt-btn kt-btn-xs kt-btn-outline"
                                                        data-valuation-id="{{ $v['id'] }}"
                                                        data-listing-action="apply-single-valuation">
                                                        Apply
                                                    </button>
                                                    <button
                                                        class="kt-btn kt-btn-xs kt-btn-ghost text-danger"
                                                        data-valuation-id="{{ $v['id'] }}"
                                                        data-listing-action="remove-valuation">
                                                        Remove
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="p-6 text-center text-muted-foreground text-sm">
                                                No valuations recorded yet.
                                                <button class="kt-btn kt-btn-xs kt-btn-outline ml-2" data-listing-action="pull-valuation">Pull now</button>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                {{-- ===== AUCTION ===== --}}
                <div data-tab-pane="auction" class="hidden">
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm">
                        @foreach ([
                            ['Auction Code',      $listing['auction_code'] ?? '—'],
                            ['Status',            $listing['auction_status'] ?? '—'],
                            ['Sniper Protection', 'Active (5 min)'],
                        ] as [$l, $v])
                            <div class="card border border-border p-3 rounded-xl">
                                <div class="text-xs text-muted-foreground">{{ $l }}</div>
                                <div class="font-medium">{{ $v }}</div>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex gap-2 mt-4">
                        <button class="kt-btn kt-btn-sm kt-btn-mono" data-listing-action="assign-auction">
                            <i class="ki-filled ki-flag"></i> Assign to Auction
                        </button>
                        <button class="kt-btn kt-btn-sm kt-btn-outline" data-listing-action="create-auction">
                            <i class="ki-filled ki-add-folder"></i> Create New Auction
                        </button>
                    </div>
                </div>

                {{-- ===== NOTES ===== --}}
                <div data-tab-pane="notes" class="hidden">
                    <textarea class="kt-input w-full" rows="6" placeholder="Add internal notes…"></textarea>
                    <button class="kt-btn kt-btn-sm kt-btn-mono mt-2">
                        <i class="ki-filled ki-save-2"></i> Save Note
                    </button>
                </div>

                {{-- ===== ACTIVITY ===== --}}
                <div data-tab-pane="activity" class="hidden">
                    <div class="space-y-2 text-sm">
                        @foreach ([
                            ['listing_created',    'Listing created by JR',                               '2026-05-31 09:01'],
                            ['valuation_fetched',  'Valuation pulled from Carsmart — £14,200 (+£200)',    '2026-05-31 09:03'],
                            ['media_uploaded',     'Photo uploaded: Front 3/4',                           '2026-05-31 09:10'],
                            ['valuation_applied',  'Guide price updated £14,250 → £14,200 (−£50)',        '2026-05-31 09:15'],
                            ['listing_state_changed','State changed: Draft → QA',                         '2026-05-31 10:00'],
                        ] as [$event, $msg, $time])
                            <div class="card border border-border p-3 flex items-start justify-between gap-2 rounded-xl">
                                <div>
                                    <span class="text-xs font-mono text-muted-foreground bg-muted/30 px-1.5 py-0.5 rounded">{{ $event }}</span>
                                    <div class="mt-1">{{ $msg }}</div>
                                </div>
                                <div class="text-xs text-muted-foreground whitespace-nowrap shrink-0">{{ $time }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- ===== HISTORY ===== --}}
                <div data-tab-pane="history" class="hidden">
                    <div class="space-y-2 text-sm">
                        <div class="card border border-border p-3 rounded-xl">
                            <div class="text-xs text-muted-foreground">State change</div>
                            <div class="font-medium">Draft → QA</div>
                            <div class="text-xs text-muted-foreground mt-1">Submitted by JR · 2026-05-31</div>
                        </div>
                        <div class="card border border-border p-3 rounded-xl">
                            <div class="text-xs text-muted-foreground">Valuation applied</div>
                            <div class="font-medium">Guide £14,250 → £14,200 (−£50 / −0.4%)</div>
                            <div class="text-xs text-muted-foreground mt-1">Updated by System · 2026-05-31</div>
                        </div>
                        <div class="card border border-border p-3 rounded-xl">
                            <div class="text-xs text-muted-foreground">Listing created</div>
                            <div class="font-medium">LST-1023 created</div>
                            <div class="text-xs text-muted-foreground mt-1">By JR · 2026-05-31</div>
                        </div>
                    </div>
                </div>

            </div>{{-- /tab panes --}}

        </div>{{-- /main content --}}

        {{-- ===== RIGHT PANEL ===== --}}
        <div class="hidden xl:flex flex-col w-72 shrink-0 border-l border-border p-4 gap-5 overflow-y-auto">

            {{-- Summary --}}
            <div>
                <div class="font-semibold text-sm mb-2">Summary</div>
                <div class="space-y-1.5 text-xs">
                    @foreach ([
                        ['State',   $listing['state']],
                        ['Owner',   $listing['owner']],
                        ['QA',      $listing['qa']],
                        ['KYC',     $listing['kyc_status'] ?? 'Pending'],
                        ['Missing', ($listing['missing_items'] ?? 0) . ' items'],
                    ] as [$k, $v])
                        <div class="flex items-center justify-between">
                            <span class="text-muted-foreground">{{ $k }}</span>
                            <span class="font-medium">{{ $v }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="border-t border-border"></div>

            {{-- Edit Essentials --}}
            <div>
                <div class="font-semibold text-sm mb-2">Edit Essentials</div>
                <div class="space-y-2">
                    <div>
                        <label class="text-xs text-muted-foreground mb-1 block">Guide Price £</label>
                        <input class="kt-input w-full text-xs" type="number" placeholder="Guide price" value="{{ $listing['guide'] ?? '' }}">
                    </div>
                    <div>
                        <label class="text-xs text-muted-foreground mb-1 block">Reserve £</label>
                        <input class="kt-input w-full text-xs" type="number" placeholder="Reserve" value="{{ $listing['reserve'] ?? '' }}">
                    </div>
                    <button class="kt-btn kt-btn-xs kt-btn-mono w-full">Save</button>
                </div>
            </div>

            <div class="border-t border-border"></div>

            {{-- Notes quick-add --}}
            <div>
                <div class="font-semibold text-sm mb-2">Quick Note</div>
                <textarea class="kt-input w-full text-xs" rows="3" placeholder="Add a note…"></textarea>
                <button class="kt-btn kt-btn-xs kt-btn-outline w-full mt-1.5">Save Note</button>
            </div>

            <div class="border-t border-border"></div>

            {{-- Communications --}}
            <div>
                <div class="font-semibold text-sm mb-2">Communications</div>
                <div class="space-y-2 text-xs">
                    <div class="card border border-border p-2 rounded-lg">
                        <div class="text-muted-foreground">Email sent to seller</div>
                        <div class="font-medium mt-0.5">2026-05-30 · JR</div>
                    </div>
                    <button class="kt-btn kt-btn-xs kt-btn-outline w-full" data-listing-action="send-message">
                        <i class="ki-filled ki-message-text"></i> Send Message
                    </button>
                </div>
            </div>

        </div>

    </div>{{-- /body --}}

</div>

<script>
(function () {
    // Tab switching
    const panes = document.querySelectorAll('[data-tab-pane]');
    const tabs  = document.querySelectorAll('.detail-tab');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const target = tab.dataset.tab;

            panes.forEach(p => p.classList.add('hidden'));
            tabs.forEach(t => {
                t.classList.remove('bg-background', 'border', 'border-b-0', 'border-border', 'text-foreground');
                t.classList.add('text-muted-foreground');
            });

            document.querySelector(`[data-tab-pane="${target}"]`)?.classList.remove('hidden');
            tab.classList.add('bg-background', 'border', 'border-b-0', 'border-border', 'text-foreground');
            tab.classList.remove('text-muted-foreground');
        });
    });

    // "View History" button — switch to Valuations tab
    document.querySelectorAll('[data-listing-action="view-valuation-history"]').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelector('.detail-tab[data-tab="valuations"]')?.click();
        });
    });

    // Pull Latest Valuation — UI states
    const pullBtns = document.querySelectorAll('[data-listing-action="pull-valuation"]');
    const statusEl = document.getElementById('valuation-fetch-status');
    const tabStatusEl = document.getElementById('val-tab-fetch-status');

    pullBtns.forEach(btn => {
        btn.addEventListener('click', async () => {
            // In progress state
            pullBtns.forEach(b => b.disabled = true);
            document.querySelector('.pull-label')?.classList.add('hidden');
            document.querySelector('.pull-icon')?.classList.add('hidden');
            document.querySelector('.pull-spinner')?.classList.remove('hidden');

            const showStatus = (el, msg, type) => {
                if (!el) return;
                el.classList.remove('hidden', 'border-success', 'border-danger', 'border-warning',
                    'bg-success/10', 'bg-danger/10', 'bg-warning/10',
                    'text-success', 'text-danger', 'text-warning-foreground');
                const map = {
                    success: ['border-success', 'bg-success/10', 'text-success'],
                    error:   ['border-danger',  'bg-danger/10',  'text-danger'],
                    warning: ['border-warning', 'bg-warning/10', 'text-warning-foreground'],
                };
                el.classList.add(...(map[type] || map.warning));
                el.textContent = msg;
            };

            showStatus(statusEl,    'Fetching latest valuation…', 'warning');
            showStatus(tabStatusEl, 'Fetching latest valuation…', 'warning');

            // TODO: replace with real fetch POST
            await new Promise(r => setTimeout(r, 1800));

            // Simulate success / failure
            const success = Math.random() > 0.2;

            if (success) {
                showStatus(statusEl,    'Valuation fetched — £14,350 (Carsmart). Delta vs Guide: +£100.', 'success');
                showStatus(tabStatusEl, 'Valuation fetched — £14,350 (Carsmart). Delta vs Guide: +£100.', 'success');
            } else {
                showStatus(statusEl,    'Valuation fetch failed — provider unavailable. Please retry.', 'error');
                showStatus(tabStatusEl, 'Valuation fetch failed — provider unavailable. Please retry.', 'error');
            }

            // Restore buttons
            pullBtns.forEach(b => b.disabled = false);
            document.querySelector('.pull-label')?.classList.remove('hidden');
            document.querySelector('.pull-icon')?.classList.remove('hidden');
            document.querySelector('.pull-spinner')?.classList.add('hidden');

            // Auto-hide status after 6 s
            setTimeout(() => {
                statusEl?.classList.add('hidden');
                tabStatusEl?.classList.add('hidden');
            }, 6000);
        });
    });

    // Apply Pricing — open modal
    document.querySelectorAll('[data-listing-action="apply-pricing"]').forEach(btn => {
        btn.addEventListener('click', () => {
            const modal = document.getElementById('apply-pricing-modal');
            modal?.classList.remove('hidden');
            modal?.classList.add('flex');
        });
    });

    // Add Valuation — open modal
    document.querySelectorAll('[data-listing-action="add-valuation"]').forEach(btn => {
        btn.addEventListener('click', () => {
            const modal = document.getElementById('add-valuation-modal');
            modal?.classList.remove('hidden');
            modal?.classList.add('flex');
        });
    });

    // Remove valuation (row action)
    document.querySelectorAll('[data-listing-action="remove-valuation"]').forEach(btn => {
        btn.addEventListener('click', () => {
            if (confirm('Remove this valuation record?')) {
                // TODO: DELETE /listings/{id}/valuations/{valuationId}
                btn.closest('tr')?.remove();
            }
        });
    });

})();
</script>