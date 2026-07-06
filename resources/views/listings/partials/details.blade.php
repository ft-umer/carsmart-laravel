@extends('layouts.app')

@section('title', ($listing['id'] ?? 'Listing') . ' — Carsmart CRM')

@section('content')

@php
    // ---- Vehicle check data (HPI / Owners / MOT advisories / Exterior grading) ----
    // Falls back to sensible demo defaults if the listing doesn't provide real data yet.
    $vcVehicleMeta = collect([
        $listing['vehicle_year'] ?? null,
        $listing['vehicle_colour'] ?? null,
        $listing['vehicle_body_type'] ?? null,
        $listing['vehicle_fuel_type'] ?? null,
    ])->filter()->implode(' | ');

    $vehicleChecksData = [
        'hpi' => [
            'title' => 'HPI History Check',
            'items' => $listing['hpi_checks'] ?? [
                'Not imported from outside of the EU',
                'Not been salvaged',
                'Not been used as a taxi',
                'Has valid MOT certificate',
                'Not been stolen',
                'Not on finance',
                'Not on a security watch register',
                'Not a write-off for damage',
                'Not a write-off for theft',
                'No plate changes',
            ],
            'checked_at' => $listing['hpi_checked_at'] ?? now()->format('d/m/Y'),
            'source' => $listing['hpi_source'] ?? 'Experian & Total Car Check',
        ],
        'owners' => [
            'title' => 'Previous Owners',
            'count' => $listing['previous_owners'] ?? 2,
            'rows' => $listing['ownership_history'] ?? [
                ['owner' => '1', 'length' => '3 years, 4 months and 24 days'],
                ['owner' => 'Current', 'length' => '3 years, 4 months and 24 days'],
            ],
        ],
        'mot-advisories' => [
            'title' => 'MOT Advisories',
            'count' => $listing['mot_advisories_count'] ?? 1,
            'advisories' => $listing['mot_advisories'] ?? [
                'Nearside front tyre wearing close to the legal limit.',
            ],
            'notes' => $listing['mot_advisory_notes'] ?? '',
        ],
        'exterior-grading' => [
            'title' => 'Exterior Grading',
            'grade' => $listing['exterior_grade'] ?? 'A',
            'columns' => ['A', 'B', 'C'],
            'rows' => $listing['exterior_grading_rows'] ?? [
                ['label' => 'Panel condition', 'A' => 'Minimal wear', 'B' => 'Light scuffs', 'C' => 'Visible damage'],
                ['label' => 'Paintwork', 'A' => 'Excellent finish', 'B' => 'Minor fading', 'C' => 'Significant fading'],
                ['label' => 'Alloys / trims', 'A' => 'Like new', 'B' => 'Light kerbing', 'C' => 'Heavy kerbing'],
            ],
        ],
    ];
@endphp

<div class="kt-container-fixed py-6 overflow-x-hidden">
{{-- Breadcrumb --}}
<nav class="flex items-center gap-1.5 text-xs text-muted-foreground mb-4">
    <a href="{{ route('listings.index') }}" class="hover:text-foreground">Listings</a>
    <i data-lucide="chevron-right" class="w-3 h-3"></i>
    <span class="text-foreground font-medium">{{ $listing['id'] }}</span>
</nav>

    <div class="flex flex-col">

    {{-- ===== HEADER ===== --}}
    <div class="p-4 border-b border-border flex flex-col lg:flex-row lg:items-start lg:justify-between gap-3 shrink-0">
        <div>
            <div class="flex items-center gap-2 flex-wrap">
                <h2 class="text-base font-semibold">{{ $listing['id'] }}</h2>
                <span class="kt-badge kt-badge-outline">{{ $listing['state'] }}</span>
                <span class="kt-badge kt-badge-outline text-xs">{{ $listing['sale_type'] ?? 'CST1' }}</span>
            </div>
            <div class="text-sm mt-0.5 text-muted-foreground">{{ $listing['vehicle'] }}</div>
            <div class="flex items-center gap-1.5 flex-wrap mt-2">
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
    </div>

    {{-- ===== ACTION BAR ===== --}}
    {{-- Spec L2: Submit for QA | Create auction | Enable BIN | Add valuation | Pull latest valuation | Apply to pricing | Send message | More -}}
    <div class="px-4 py-2.5 border-b border-border shrink-0">
        <div class="flex flex-wrap gap-1.5">
            <button class="kt-btn kt-btn-sm kt-btn-mono" data-listing-action="submit-qa">
                <i class="ki-filled ki-send"></i> Submit for QA
            </button>
            <button class="kt-btn kt-btn-sm kt-btn-outline" data-listing-action="create-auction">
                <i class="ki-filled ki-flag"></i> Create Auction
            </button>
            <button class="kt-btn kt-btn-sm kt-btn-outline" data-listing-action="enable-bin">
                <i class="ki-filled ki-shop"></i> Enable BIN
            </button>
            <button class="kt-btn kt-btn-sm kt-btn-outline" data-listing-action="add-valuation">
                <i class="ki-filled ki-add-item"></i> Add Valuation
            </button>
            <button class="kt-btn kt-btn-sm kt-btn-outline" data-listing-action="pull-valuation">
                <i class="ki-filled ki-chart-line-up"></i> Pull Latest Valuation
            </button>
            <button class="kt-btn kt-btn-sm kt-btn-outline" data-listing-action="apply-pricing">
                <i class="ki-filled ki-price-tag"></i> Apply to Pricing
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
    </div>

    {{-- ===== BODY: TABS + RIGHT PANEL ===== --}}
    <div class="flex flex-col xl:flex-row flex-1 overflow-hidden min-w-0">

        {{-- Main content --}}
        <div class="flex-1 min-w-0 overflow-y-auto">

            {{-- Tab nav — canonical order per Phase 1 spec (L2) --}}
            <div class="kt-scrollable-x flex gap-0.5 px-4 pt-3 border-b border-border whitespace-nowrap shrink-0">
                @foreach (['Overview', 'Vehicle', 'Seller', 'Media', 'Documents', 'Pricing', 'QA', 'Valuations', 'Auction', 'Notes', 'Activity', 'History'] as $tab)
                    @php $tabId = strtolower($tab); @endphp
                    <button
                        class="detail-tab px-3 py-2 text-sm rounded-t-lg whitespace-nowrap font-medium transition-colors
                                @if ($loop->first) bg-background border border-b-0 border-border text-foreground
                                @else text-muted-foreground hover:text-foreground @endif"
                        data-tab="{{ $tabId }}">
                        {{ $tab }}
                    </button>
                @endforeach
            </div>

            {{-- Tab panes --}}
            <div class="p-4 space-y-4">

                {{-- ===== OVERVIEW ===== --}}
                {{-- Spec L2 "Overview (updated)": Valuation card always visible with latest value, source,
                     timestamp, delta vs Guide/Reserve, and buttons: View history / Pull latest valuation / Apply to pricing --}}
                <div data-tab-pane="overview" class="space-y-4">

                    {{-- Valuation card (mandatory, always shown) --}}
                    <div class="card border border-border rounded-xl p-4" id="valuation-card">
                        <div class="flex items-center justify-between mb-3">
                            <div class="font-semibold text-sm">Latest Valuation</div>
                            <span class="kt-badge kt-badge-outline text-xs" id="valuation-fetch-status">Idle</span>
                        </div>

                        <div class="flex flex-wrap items-end gap-6">
                            <div>
                                <div class="text-xs text-muted-foreground mb-1">Amount</div>
                                <div class="text-2xl font-bold tracking-tight" id="ov-valuation-amount">
                                    £{{ number_format($listing['valuation'] ?? 0) }}
                                </div>
                            </div>
                            <div>
                                <div class="text-xs text-muted-foreground mb-1">Source</div>
                                <div class="font-medium text-sm" id="ov-valuation-source">{{ $listing['valuation_source'] ?? 'Internal (Carsmart)' }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-muted-foreground mb-1">Last fetched</div>
                                <div class="font-medium text-sm" id="ov-valuation-timestamp">{{ $listing['valuation_timestamp'] ?? '2026-05-31 09:03' }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-muted-foreground mb-1">Delta vs Guide</div>
                                @php
                                    $delta = $listing['valuation_delta_guide'] ?? 200;
                                @endphp
                                <span class="kt-badge {{ $delta >= 0 ? 'kt-badge-success' : 'kt-badge-danger' }} text-xs" id="ov-delta-guide">
                                    {{ $delta >= 0 ? '+' : '' }}£{{ number_format($delta) }}
                                </span>
                            </div>
                            <div>
                                <div class="text-xs text-muted-foreground mb-1">Delta vs Reserve</div>
                                @php
                                    $deltaR = $listing['valuation_delta_reserve'] ?? -50;
                                @endphp
                                <span class="kt-badge {{ $deltaR >= 0 ? 'kt-badge-success' : 'kt-badge-danger' }} text-xs" id="ov-delta-reserve">
                                    {{ $deltaR >= 0 ? '+' : '' }}£{{ number_format($deltaR) }}
                                </span>
                            </div>
                        </div>

                        <div class="flex gap-2 mt-4">
                            <button class="kt-btn kt-btn-xs kt-btn-outline" onclick="document.querySelector('.detail-tab[data-tab=\'valuations\']')?.click()">
                                <i class="ki-filled ki-time"></i> View History
                            </button>
                            <button class="kt-btn kt-btn-xs kt-btn-outline" data-listing-action="pull-valuation">
                                <i class="ki-filled ki-refresh"></i> Pull Latest Valuation
                            </button>
                            <button class="kt-btn kt-btn-xs kt-btn-mono" data-listing-action="apply-pricing">
                                <i class="ki-filled ki-price-tag"></i> Apply to Pricing
                            </button>
                        </div>
                    </div>

                    {{-- Snapshot grid --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @foreach ([
                            ['Vehicle', $listing['vehicle'] ?? '—'],
                            ['VRM', $listing['vrm'] ?? '—'],
                            ['Mileage', number_format($listing['mileage'] ?? 0) . ' mi'],
                            ['State', $listing['state'] ?? '—'],
                            ['Sale Type', $listing['sale_type'] ?? 'CST1'],
                            ['QA Status', $listing['qa'] ?? '—'],
                            ['KYC', $listing['kyc_status'] ?? 'Required'],
                            ['Owner', $listing['owner'] ?? '—'],
                        ] as [$l, $v])
                            <div class="card border border-border p-3 rounded-xl">
                                <div class="text-xs text-muted-foreground">{{ $l }}</div>
                                <div class="font-medium mt-0.5 text-sm">{{ $v }}</div>
                            </div>
                        @endforeach
                    </div>

                    @if (($listing['missing_items'] ?? 0) > 0)
                    <div class="rounded-xl border border-warning/30 bg-warning/10 px-4 py-3 text-sm text-warning-foreground">
                        <i class="ki-filled ki-information-4 mr-1"></i>
                        {{ $listing['missing_items'] }} item(s) missing before this listing can move to Publication Queue. Check QA tab.
                    </div>
                    @endif

                </div>

                {{-- ===== VEHICLE ===== --}}
                {{-- Spec L2 "Vehicle": VRM/source/status fields + spec/seat/keys/tools/lug-nut/charging/smoking notes
                     (folded in from the old Status + Specifications tabs), plus Features / Specifications /
                     Vehicle Details / Vehicle History & Checks (HPI, Previous Owners, MOT Advisories, Exterior Grading) --}}
                <div data-tab-pane="vehicle" class="hidden space-y-4">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs text-muted-foreground mb-1 block">Source *</label>
                            <select class="kt-input w-full">
                                <option value="source1">Source 1 (Valuation Enquiry)</option>
                                <option value="source2">Source 2 (Vendor Dashboard)</option>
                                <option value="vendor">Vendor Upload</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs text-muted-foreground mb-1 block">User Type *</label>
                            <select class="kt-input w-full">
                                <option value="customer">Customer</option>
                                <option value="vendor">Vendor</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs text-muted-foreground mb-1 block">Status *</label>
                            <select class="kt-input w-full">
                                <option value="complete">Complete</option>
                                <option value="partial">Partial</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs text-muted-foreground mb-1 block">Car ID (VRM) *</label>
                            <input class="kt-input w-full" type="text" value="{{ $listing['vrm'] ?? '' }}" placeholder="e.g., AB19 CDE">
                        </div>
                        <div>
                            <label class="text-xs text-muted-foreground mb-1 block">Mileage *</label>
                            <input class="kt-input w-full" type="number" placeholder="Mileage" value="{{ $listing['mileage'] ?? 42000 }}">
                        </div>
                        <div>
                            <label class="text-xs text-muted-foreground mb-1 block">Sale Type *</label>
                            <select class="kt-input w-full">
                                <option value="cs-online">CS Online Sale</option>
                                <option value="quick">Quick Sale</option>
                                <option value="bin">Buy It Now</option>
                                <option value="editions">Editions</option>
                                <option value="norush">No Rush Sale</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs text-muted-foreground mb-1 block">Advertised Recently? *</label>
                            <select class="kt-input w-full">
                                <option value="no">No</option>
                                <option value="yes">Yes</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs text-muted-foreground mb-1 block">Vehicle Smoking Status *</label>
                            <select class="kt-input w-full">
                                <option>Unknown</option>
                                <option>Non-smoking</option>
                                <option>Smoking environment</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs text-muted-foreground mb-1 block">If Advertised, Where? (Name)</label>
                        <input class="kt-input w-full" type="text" placeholder="e.g., AutoTrader, eBay Motors, Gumtree">
                    </div>

                    {{-- ===== FEATURES ===== --}}
                    <div class="border-t border-border pt-4">
                        <div class="flex items-center justify-between mb-3">
                            <div class="font-semibold text-sm">Features</div>
                            <button type="button" class="kt-btn kt-btn-xs kt-btn-outline" data-add-feature-btn>
                                <i class="ki-filled ki-plus"></i> Add Feature
                            </button>
                        </div>
                        <div class="flex flex-wrap gap-2" id="vehicle-features-list">
                            @forelse (($listing['features'] ?? ['Navigation', 'Heated Seats', 'Parking Sensors', 'Cruise Control']) as $feature)
                                <span class="kt-badge kt-badge-outline text-xs px-3 py-1.5 flex items-center gap-1.5">
                                    <i class="ki-filled ki-check-circle text-success text-xs"></i> {{ $feature }}
                                </span>
                            @empty
                                <span class="text-xs text-muted-foreground">No features recorded yet.</span>
                            @endforelse
                        </div>
                    </div>

                    {{-- ===== SPECIFICATIONS ===== --}}
                    <div class="border-t border-border pt-4">
                        <div class="font-semibold text-sm mb-3">Specifications</div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            @foreach ([
                                ['Exterior Color', $listing['exterior_colour'] ?? $listing['vehicle_colour'] ?? '—'],
                                ['Seats Material', $listing['seats_material'] ?? '—'],
                                ['Body Type', $listing['vehicle_body_type'] ?? '—'],
                                ['Gearbox', $listing['gearbox'] ?? '—'],
                                ['Fuel Type', $listing['vehicle_fuel_type'] ?? '—'],
                                ['Engine Size', $listing['engine_size'] ?? '—'],
                            ] as [$l, $v])
                                <div class="card border border-border p-3 rounded-xl">
                                    <div class="text-xs text-muted-foreground">{{ $l }}</div>
                                    <div class="font-medium mt-0.5 text-sm">{{ $v }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- ===== VEHICLE DETAILS (+ history/check links) ===== --}}
                    <div class="border-t border-border pt-4">
                        <div class="font-semibold text-sm mb-3">Vehicle Details</div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">

                            {{-- HPI history — opens HPI History Check modal --}}
                            <button type="button" class="card border border-border rounded-xl p-3 text-left hover:bg-muted/5 transition" data-history-check="hpi">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs text-muted-foreground">HPI History</span>
                                    <i class="ki-filled ki-shield-tick text-success text-sm"></i>
                                </div>
                                <div class="font-medium text-sm">{{ $listing['hpi_summary'] ?? 'All Clear' }}</div>
                            </button>

                            <div class="card border border-border p-3 rounded-xl">
                                <div class="text-xs text-muted-foreground">Registration</div>
                                <div class="font-medium mt-0.5 text-sm">{{ $listing['vrm'] ?? '—' }}</div>
                            </div>
                            <div class="card border border-border p-3 rounded-xl">
                                <div class="text-xs text-muted-foreground">VIN</div>
                                <div class="font-medium mt-0.5 text-sm">{{ $listing['vin'] ?? '—' }}</div>
                            </div>
                            <div class="card border border-border p-3 rounded-xl">
                                <div class="text-xs text-muted-foreground">First Registered</div>
                                <div class="font-medium mt-0.5 text-sm">{{ $listing['first_registered'] ?? '—' }}</div>
                            </div>
                            <div class="card border border-border p-3 rounded-xl">
                                <div class="text-xs text-muted-foreground">Keeper Start Date</div>
                                <div class="font-medium mt-0.5 text-sm">{{ $listing['keeper_start_date'] ?? '—' }}</div>
                            </div>

                            {{-- Previous owners — opens Previous Owners modal --}}
                            <button type="button" class="card border border-border rounded-xl p-3 text-left hover:bg-muted/5 transition" data-history-check="owners">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs text-muted-foreground">Previous Owners</span>
                                    <i class="ki-filled ki-user text-muted-foreground text-sm"></i>
                                </div>
                                <div class="font-medium text-sm">{{ $listing['previous_owners'] ?? 2 }} owners</div>
                            </button>

                            <div class="card border border-border p-3 rounded-xl">
                                <div class="text-xs text-muted-foreground">No. of Keys</div>
                                <div class="font-medium mt-0.5 text-sm">{{ $listing['no_of_keys'] ?? '—' }}</div>
                            </div>
                            <div class="card border border-border p-3 rounded-xl">
                                <div class="text-xs text-muted-foreground">On Finance</div>
                                <div class="font-medium mt-0.5 text-sm">{{ ($listing['on_finance'] ?? false) ? 'Yes' : 'No' }}</div>
                            </div>
                            <div class="card border border-border p-3 rounded-xl">
                                <div class="text-xs text-muted-foreground">Private Plate</div>
                                <div class="font-medium mt-0.5 text-sm">{{ ($listing['private_plate'] ?? false) ? 'Yes' : 'No' }}</div>
                            </div>
                            <div class="card border border-border p-3 rounded-xl">
                                <div class="text-xs text-muted-foreground">Seller Keeping Plate</div>
                                <div class="font-medium mt-0.5 text-sm">{{ ($listing['seller_keeping_plate'] ?? false) ? 'Yes' : 'No' }}</div>
                            </div>
                            <div class="card border border-border p-3 rounded-xl">
                                <div class="text-xs text-muted-foreground">Original Plate</div>
                                <div class="font-medium mt-0.5 text-sm">{{ $listing['original_plate'] ?? '—' }}</div>
                            </div>

                            {{-- Exterior grading — opens Exterior Grading modal --}}
                            <button type="button" class="card border border-border rounded-xl p-3 text-left hover:bg-muted/5 transition" data-history-check="exterior-grading">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs text-muted-foreground">Exterior Grading</span>
                                    <i class="ki-filled ki-star text-muted-foreground text-sm"></i>
                                </div>
                                <div class="font-medium text-sm">Grade {{ $listing['exterior_grade'] ?? 'A' }}</div>
                            </button>

                        </div>
                    </div>

                    <div class="border-t border-border pt-4">
                        <div class="font-semibold text-sm mb-3">Vehicle Specifications</div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs text-muted-foreground mb-1 block">Vehicle Specifications (Notes)</label>
                                <textarea class="kt-input w-full" rows="3" placeholder="e.g., Full electric windows, Climate control, Panoramic roof, Heated seats, etc."></textarea>
                            </div>
                            <div>
                                <label class="text-xs text-muted-foreground mb-1 block">Vehicle Seat Type (Notes)</label>
                                <textarea class="kt-input w-full" rows="3" placeholder="e.g., Leather, Cloth, Suede, Leather/Cloth combo, etc."></textarea>
                            </div>
                            <div>
                                <label class="text-xs text-muted-foreground mb-1 block">Keys (Notes)</label>
                                <textarea class="kt-input w-full" rows="2" placeholder="e.g., 2 remote keys, 1 spare key, original fobs, etc."></textarea>
                            </div>
                            <div>
                                <label class="text-xs text-muted-foreground mb-1 block">Tool Pack Status (Notes)</label>
                                <textarea class="kt-input w-full" rows="2" placeholder="e.g., Complete with all tools, Missing toolkit, etc."></textarea>
                            </div>
                            <div>
                                <label class="text-xs text-muted-foreground mb-1 block">Lug Nut (Notes)</label>
                                <textarea class="kt-input w-full" rows="2" placeholder="Notes about lug nuts/wheel nuts…"></textarea>
                            </div>
                            <div>
                                <label class="text-xs text-muted-foreground mb-1 block">Charging Cables (Notes)</label>
                                <textarea class="kt-input w-full" rows="2" placeholder="e.g., EV charging cables included, cable management, etc."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-border pt-4">
                        <div class="flex items-center justify-between mb-3">
                            <div class="font-semibold text-sm">MOT</div>
                            <button type="button" class="kt-btn kt-btn-xs kt-btn-outline" data-history-check="mot-advisories">
                                <i class="ki-filled ki-information-4"></i> View MOT Advisories
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs text-muted-foreground mb-1 block">MOT Status *</label>
                                <select class="kt-input w-full" id="mot-status-select">
                                    <option value="valid">Valid</option>
                                    <option value="expired">Expired</option>
                                    <option value="none">No MOT (vehicle exempt / new)</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs text-muted-foreground mb-1 block">MOT Expiry Date *</label>
                                <input class="kt-input w-full" type="date" id="mot-expiry-input">
                            </div>
                            <div>
                                <label class="text-xs text-muted-foreground mb-1 block">Mileage at Last MOT</label>
                                <input class="kt-input w-full" type="number" placeholder="e.g., 41,200">
                            </div>
                            <div>
                                <label class="text-xs text-muted-foreground mb-1 block">MOT Advisories? *</label>
                                <select class="kt-input w-full">
                                    <option value="no">No</option>
                                    <option value="yes">Yes</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="text-xs text-muted-foreground mb-1 block">MOT Advisory Notes</label>
                            <textarea class="kt-input w-full text-xs" rows="2" placeholder="e.g., Nearside front tyre wearing close to legal limit, etc."></textarea>
                        </div>
                    </div>

                    <div class="border-t border-border pt-4">
                        <div class="font-semibold text-sm mb-3">Technical Health</div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs text-muted-foreground mb-1 block">Mechanical & Electrical Issues? *</label>
                                <select class="kt-input w-full">
                                    <option value="no">No</option>
                                    <option value="yes">Yes</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs text-muted-foreground mb-1 block">Issue Details (Notes)</label>
                                <textarea class="kt-input w-full text-xs" rows="2" placeholder="Describe any mechanical or electrical issues…"></textarea>
                            </div>
                            <div>
                                <label class="text-xs text-muted-foreground mb-1 block">Further Issues? *</label>
                                <select class="kt-input w-full">
                                    <option value="no">No</option>
                                    <option value="yes">Yes</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs text-muted-foreground mb-1 block">Issue Details (Notes)</label>
                                <textarea class="kt-input w-full text-xs" rows="2" placeholder="Any other issues to declare…"></textarea>
                            </div>
                            <div>
                                <label class="text-xs text-muted-foreground mb-1 block">Service Records Present? *</label>
                                <select class="kt-input w-full">
                                    <option value="no">No</option>
                                    <option value="partial">Yes - Partial</option>
                                    <option value="full">Yes - Full</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs text-muted-foreground mb-1 block">Manuals Present? *</label>
                                <select class="kt-input w-full">
                                    <option value="no">No</option>
                                    <option value="yes">Yes</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-border pt-4">
                        <div class="font-semibold text-sm mb-3">Exterior & Interior Condition</div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach ([
                                'Front Drivers Side', 'Front Passengers Side', 'Rear Passenger Side', 'Rear Drivers Side',
                                'Front Seats', 'Rear Seats', 'Dashboard', 'Boot',
                                'Front Drivers Wheel/Tyre', 'Front Passengers Wheel/Tyre', 'Rear Drivers Wheel/Tyre', 'Rear Passengers Wheel/Tyre',
                            ] as $area)
                                <div class="card border border-border p-3 rounded-xl">
                                    <div class="font-medium text-sm mb-2">{{ $area }}</div>
                                    <div class="border-2 border-dashed border-border rounded-lg p-3 text-center cursor-pointer hover:bg-muted/5 mb-2">
                                        <i class="ki-filled ki-add-image text-lg text-muted-foreground mb-1 block"></i>
                                        <span class="text-xs text-muted-foreground">Click to upload image</span>
                                    </div>
                                    <textarea class="kt-input w-full text-xs" rows="2" placeholder="Notes…"></textarea>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="border-t border-border pt-4">
                        <div class="font-semibold text-sm mb-3">Damage & Wear</div>
                        <div class="grid grid-cols-1 gap-3">
                            @foreach (['Surface Marks', 'Panel Damage', 'Exterior Wear & Tear', 'Glass Health', 'Damage/Absent Fixtures', 'Dashboard & Lights'] as $area)
                                <div class="card border border-border p-4 rounded-xl">
                                    <div class="font-semibold text-sm mb-3">{{ $area }}</div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="space-y-3">
                                            <div>
                                                <label class="text-xs text-muted-foreground block mb-2">Diagram Image</label>
                                                <div class="border-2 border-dashed border-border rounded-lg p-4 text-center cursor-pointer hover:bg-muted/5 transition">
                                                    <i class="ki-filled ki-add-image text-xl text-muted-foreground mb-1 block"></i>
                                                    <span class="text-xs text-muted-foreground">Click to upload diagram</span>
                                                </div>
                                            </div>
                                            <div>
                                                <label class="text-xs text-muted-foreground block mb-2">Photo</label>
                                                <div class="border-2 border-dashed border-border rounded-lg p-4 text-center cursor-pointer hover:bg-muted/5 transition">
                                                    <i class="ki-filled ki-camera text-xl text-muted-foreground mb-1 block"></i>
                                                    <span class="text-xs text-muted-foreground">Click to upload photo</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="space-y-2">
                                            <div>
                                                <label class="text-xs text-muted-foreground mb-1 block">Size (Text Area)</label>
                                                <input class="kt-input w-full text-xs" type="text" placeholder="e.g., 10cm, small scratch, 5mm chip">
                                            </div>
                                            <div>
                                                <label class="text-xs text-muted-foreground mb-1 block">Vehicle Side (Location)</label>
                                                <select class="kt-input w-full text-xs">
                                                    <option>Front Drivers Side</option>
                                                    <option>Front Passengers Side</option>
                                                    <option>Rear Drivers Side</option>
                                                    <option>Rear Passengers Side</option>
                                                    <option>Top/Roof</option>
                                                    <option>Bottom/Underside</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="text-xs text-muted-foreground mb-1 block">Detailed Notes</label>
                                                <textarea class="kt-input w-full text-xs" rows="2" placeholder="Detailed description of damage/wear…"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <button class="kt-btn kt-btn-sm kt-btn-mono">Save Vehicle Details</button>

                </div>

                {{-- ===== SELLER ===== --}}
                {{-- Spec L2 "Seller": seller identity + KYC + finance/plate disclosures
                     (folded in from the old "About You" + part of "Documents") --}}
                <div data-tab-pane="seller" class="hidden space-y-4">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs text-muted-foreground mb-1 block">Name *</label>
                            <input class="kt-input w-full" type="text" placeholder="Seller/Vendor name" value="John Reynolds">
                        </div>
                        <div>
                            <label class="text-xs text-muted-foreground mb-1 block">Email *</label>
                            <input class="kt-input w-full" type="email" placeholder="Email address" value="j.reynolds@example.com">
                        </div>
                        <div>
                            <label class="text-xs text-muted-foreground mb-1 block">Telephone Number *</label>
                            <input class="kt-input w-full" type="tel" placeholder="Phone number" value="+44 7911 123456">
                        </div>
                        <div>
                            <label class="text-xs text-muted-foreground mb-1 block">KYC Status *</label>
                            @if (($listing['kyc_status'] ?? '') === 'Verified')
                                <div><span class="kt-badge kt-badge-success">Verified</span></div>
                            @else
                                <div class="flex items-center gap-2">
                                    <span class="kt-badge kt-badge-danger">Required</span>
                                    <button class="kt-btn kt-btn-xs kt-btn-outline">Start KYC</button>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="text-xs text-muted-foreground mb-1 block">Original VRM (Notes)</label>
                        <textarea class="kt-input w-full" rows="3" placeholder="Any notes about the original VRM or registration history…"></textarea>
                    </div>

                    <div class="border-t border-border pt-4">
                        <div class="font-semibold text-sm mb-3">Disclosures</div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs text-muted-foreground mb-1 block">Private Plate? *</label>
                                <select class="kt-input w-full">
                                    <option value="no">No</option>
                                    <option value="yes">Yes</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs text-muted-foreground mb-1 block">Plate Status (Notes)</label>
                                <textarea class="kt-input w-full text-xs" rows="2" placeholder="Notes about private plate…"></textarea>
                            </div>
                            <div>
                                <label class="text-xs text-muted-foreground mb-1 block">Finance? *</label>
                                <select class="kt-input w-full">
                                    <option value="no">No</option>
                                    <option value="yes">Yes</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs text-muted-foreground mb-1 block">Finance Details (Notes)</label>
                                <textarea class="kt-input w-full text-xs" rows="2" placeholder="Finance company, amount, terms, etc."></textarea>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs text-muted-foreground mb-1 block">Valuation Figure £ *</label>
                        <input class="kt-input w-full text-lg font-semibold" type="number" placeholder="0.00" value="{{ $listing['valuation'] ?? '' }}">
                    </div>

                    <button class="kt-btn kt-btn-sm kt-btn-mono">Save Seller Details</button>

                </div>

                {{-- ===== MEDIA ===== --}}
                <div data-tab-pane="media" class="hidden space-y-4">

                    <div>
                        <label class="text-xs text-muted-foreground mb-2 block">Photos</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            @foreach (['Front 3/4', 'Rear 3/4', 'Side Profile', 'Interior Front', 'Interior Rear', 'Dashboard', 'Boot', 'Engine Bay'] as $shot)
                                <div class="border-2 border-dashed border-border rounded-lg p-4 text-center cursor-pointer hover:bg-muted/5">
                                    <i class="ki-filled ki-add-image text-xl text-muted-foreground mb-1 block"></i>
                                    <span class="text-xs text-muted-foreground">{{ $shot }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="border-t border-border pt-4">
                        <label class="text-xs text-muted-foreground mb-2 block">Video Display/Upload</label>
                        <div class="border-2 border-dashed border-border rounded-lg p-8 text-center cursor-pointer hover:bg-muted/5">
                            <i class="ki-filled ki-video text-3xl text-muted-foreground mb-2 block"></i>
                            <span class="text-sm text-muted-foreground">Click to upload or embed video</span>
                            <div class="text-xs text-muted-foreground mt-2">Supports MP4, WebM, MOV formats</div>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs text-muted-foreground mb-1 block">Video URL (if embedded)</label>
                        <input class="kt-input w-full text-xs" type="text" placeholder="e.g., https://youtube.com/watch?v=...">
                    </div>

                    <div class="flex items-center justify-between rounded-lg bg-muted/5 border border-border p-3 text-xs">
                        <span class="text-muted-foreground">Required Photos</span>
                        <span class="kt-badge kt-badge-danger text-xs">0/6 uploaded</span>
                    </div>

                    <button class="kt-btn kt-btn-sm kt-btn-mono">Save Media</button>

                </div>

                {{-- ===== DOCUMENTS ===== --}}
                <div data-tab-pane="documents" class="hidden space-y-4">

                    <div class="grid grid-cols-1 gap-3">
                        @foreach ([
                            ['V5C Logbook', 'v5c-logbook'],
                            ['V5C Owner Document', 'v5c-owner'],
                            ['V5C Print Vehicle Address', 'v5c-address'],
                            ['Proof Of Purchase', 'proof-purchase'],
                            ['Service Records', 'service-records'],
                            ['Manuals', 'manuals'],
                            ['MOT Certificate', 'mot-certificate'],
                        ] as [$label, $id])
                            <div class="card border border-border p-4 rounded-xl">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="font-semibold text-sm">{{ $label }}</div>
                                    @if ($id === 'v5c-logbook')
                                        <span class="kt-badge kt-badge-danger text-xs">Missing</span>
                                    @elseif ($id === 'mot-certificate')
                                        <span class="kt-badge kt-badge-success text-xs">Present</span>
                                    @else
                                        <span class="kt-badge kt-badge-outline text-xs">Not Uploaded</span>
                                    @endif
                                </div>
                                <div class="space-y-3">
                                    <div>
                                        <label class="text-xs text-muted-foreground block mb-2">Upload Document</label>
                                        <div class="border-2 border-dashed border-border rounded-lg p-4 text-center cursor-pointer hover:bg-muted/5">
                                            <i class="ki-filled ki-file-up text-xl text-muted-foreground mb-1 block"></i>
                                            <span class="text-xs text-muted-foreground">Click to upload PDF/Image</span>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="text-xs text-muted-foreground mb-1 block">Notes</label>
                                        <textarea class="kt-input w-full text-xs" rows="2" placeholder="e.g., Document status, page count, etc."></textarea>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <button class="kt-btn kt-btn-sm kt-btn-mono mt-4">Save Documents</button>

                </div>

                {{-- ===== PRICING ===== --}}
                {{-- Spec L2: Guide/Reserve/BIN/Make-Offer, mutual-exclusivity rule, and Apply-to-Pricing flow with delta preview --}}
                <div data-tab-pane="pricing" class="hidden space-y-4">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @foreach ([['Guide Price', '£' . number_format($listing['guide'] ?? 0)], ['Reserve', $listing['reserve'] ?? null ? '£' . number_format($listing['reserve']) : 'Not set'], ['BIN Price', $listing['bin'] ?? false ? '£' . number_format($listing['bin_price'] ?? 0) : 'Off'], ['Make Offer', $listing['offer_enabled'] ?? false ? 'Enabled' : 'Off']] as [$l, $v])
                            <div class="card border border-border p-3 rounded-xl">
                                <div class="text-xs text-muted-foreground">{{ $l }}</div>
                                <div class="font-medium mt-0.5">{{ $v }}</div>
                            </div>
                        @endforeach
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs text-muted-foreground mb-1 block">Guide Price £</label>
                            <input class="kt-input w-full" type="number" value="{{ $listing['guide'] ?? '' }}" id="pricing-guide-input">
                        </div>
                        <div>
                            <label class="text-xs text-muted-foreground mb-1 block">Reserve £</label>
                            <input class="kt-input w-full" type="number" value="{{ $listing['reserve'] ?? '' }}" id="pricing-reserve-input">
                        </div>
                        <div>
                            <label class="text-xs text-muted-foreground mb-1 block">BIN Price £</label>
                            <input class="kt-input w-full" type="number" value="{{ $listing['bin_price'] ?? '' }}" placeholder="Leave blank if BIN off">
                        </div>
                        <div>
                            <label class="text-xs text-muted-foreground mb-1 block">Make Offer</label>
                            <select class="kt-input w-full">
                                <option value="off">Off</option>
                                <option value="on">Enabled</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-3 rounded-xl border border-warning/30 bg-warning/10 px-4 py-3 text-sm text-warning-foreground">
                        <i class="ki-filled ki-information-4 mr-1"></i>
                        BIN cannot be active simultaneously with a Reserve price.
                    </div>

                    <div class="flex gap-2 mt-3">
                        <button class="kt-btn kt-btn-sm kt-btn-mono">Save Pricing</button>
                        <button class="kt-btn kt-btn-sm kt-btn-outline" data-listing-action="apply-pricing">
                            <i class="ki-filled ki-price-tag"></i> Apply Valuation to Pricing
                        </button>
                        <button class="kt-btn kt-btn-sm kt-btn-outline" data-listing-action="enable-bin">Toggle BIN</button>
                    </div>
                </div>

                {{-- ===== QA ===== --}}
                <div data-tab-pane="qa" class="hidden space-y-4">
                    <div class="space-y-2">
                        @foreach ([['Required Photos (6)', 'Incomplete', 'danger'], ['V5C Document', 'Missing', 'danger'], ['MOT Certificate', 'Present', 'success'], ['Pricing Set', 'Complete', 'success'], ['KYC Verified', 'Pending', 'warning'], ['Compliance Confirmed', 'Done', 'success']] as [$item, $status, $badge])
                            <div class="card border border-border p-3 flex items-center justify-between text-sm rounded-xl">
                                <span>{{ $item }}</span>
                                <span class="kt-badge kt-badge-{{ $badge }}">{{ $status }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div>
                        <label class="text-xs text-muted-foreground mb-1 block">QA Notes</label>
                        <textarea class="kt-input w-full" rows="3" placeholder="Reviewer notes on outstanding items…"></textarea>
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
                    </div>
                </div>

                {{-- ===== VALUATIONS ===== --}}
                {{-- Spec L2 Valuations module: structured log table (Date/Source/Valuer/Amount/Deviation/Notes/Comps/Used-in-rec/Actions)
                     + recommendation panel (Guide £ / Reserve band £–£ / Apply to listing) + per-provider snapshot --}}
                <div data-tab-pane="valuations" class="hidden space-y-4">

                    <div class="flex justify-between items-center mb-2">
                        <h3 class="font-semibold text-sm">Valuation Log</h3>
                        <div class="flex gap-2">
                            <button class="kt-btn kt-btn-xs kt-btn-outline" data-listing-action="pull-valuation">
                                <i class="ki-filled ki-refresh"></i> Pull Latest
                            </button>
                            <button class="kt-btn kt-btn-xs kt-btn-mono" data-listing-action="add-valuation">
                                <i class="ki-filled ki-add-item"></i> Add Valuation
                            </button>
                        </div>
                    </div>

                    {{-- Structured log table --}}
                    <div class="overflow-x-auto rounded-xl border border-border">
                        <table class="w-full text-xs">
                            <thead class="bg-muted/10">
                                <tr class="text-left text-muted-foreground">
                                    <th class="p-2.5 font-medium">Date</th>
                                    <th class="p-2.5 font-medium">Source</th>
                                    <th class="p-2.5 font-medium">Valuer</th>
                                    <th class="p-2.5 font-medium">Amount</th>
                                    <th class="p-2.5 font-medium">Deviation vs Guide</th>
                                    <th class="p-2.5 font-medium">Notes</th>
                                    <th class="p-2.5 font-medium text-center">Comps</th>
                                    <th class="p-2.5 font-medium text-center">Used in Rec.</th>
                                    <th class="p-2.5 font-medium text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border" id="valuation-log-body">
                                <tr>
                                    <td class="p-2.5 whitespace-nowrap">2026-05-31 09:03</td>
                                    <td class="p-2.5">Carsmart</td>
                                    <td class="p-2.5">System</td>
                                    <td class="p-2.5 font-semibold">£14,200</td>
                                    <td class="p-2.5"><span class="kt-badge kt-badge-success text-xs">+£200</span></td>
                                    <td class="p-2.5 text-muted-foreground">Auto-fetched</td>
                                    <td class="p-2.5 text-center"><i class="ki-filled ki-check text-success"></i></td>
                                    <td class="p-2.5 text-center"><i class="ki-filled ki-check text-success"></i></td>
                                    <td class="p-2.5 text-right whitespace-nowrap">
                                        <button class="kt-btn kt-btn-xs kt-btn-outline" data-listing-action="apply-pricing">Apply</button>
                                        <button class="kt-btn kt-btn-xs kt-btn-outline text-danger" data-listing-action="remove-valuation">Remove</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="p-2.5 whitespace-nowrap">2026-05-30 16:40</td>
                                    <td class="p-2.5">AutoTrader</td>
                                    <td class="p-2.5">System</td>
                                    <td class="p-2.5 font-semibold">£18,200</td>
                                    <td class="p-2.5"><span class="kt-badge kt-badge-success text-xs">+£4,200</span></td>
                                    <td class="p-2.5 text-muted-foreground">Current valuation</td>
                                    <td class="p-2.5 text-center"><i class="ki-filled ki-check text-success"></i></td>
                                    <td class="p-2.5 text-center text-muted-foreground">—</td>
                                    <td class="p-2.5 text-right whitespace-nowrap">
                                        <button class="kt-btn kt-btn-xs kt-btn-outline" data-listing-action="apply-pricing">Apply</button>
                                        <button class="kt-btn kt-btn-xs kt-btn-outline text-danger" data-listing-action="remove-valuation">Remove</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="p-2.5 whitespace-nowrap">2026-05-30 16:38</td>
                                    <td class="p-2.5">WeBuyAnyCar</td>
                                    <td class="p-2.5">System</td>
                                    <td class="p-2.5 font-semibold">£13,800</td>
                                    <td class="p-2.5"><span class="kt-badge kt-badge-danger text-xs">−£200</span></td>
                                    <td class="p-2.5 text-muted-foreground">Trade-in offer</td>
                                    <td class="p-2.5 text-center text-muted-foreground">—</td>
                                    <td class="p-2.5 text-center text-muted-foreground">—</td>
                                    <td class="p-2.5 text-right whitespace-nowrap">
                                        <button class="kt-btn kt-btn-xs kt-btn-outline" data-listing-action="apply-pricing">Apply</button>
                                        <button class="kt-btn kt-btn-xs kt-btn-outline text-danger" data-listing-action="remove-valuation">Remove</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Recommendation panel --}}
                    <div class="card border border-border rounded-xl p-4 bg-muted/5">
                        <div class="font-semibold text-sm mb-3">Pricing Recommendation</div>
                        <div class="flex flex-wrap items-end gap-6">
                            <div>
                                <div class="text-xs text-muted-foreground mb-1">Recommended Guide</div>
                                <div class="text-xl font-bold">£14,200</div>
                            </div>
                            <div>
                                <div class="text-xs text-muted-foreground mb-1">Recommended Reserve Band</div>
                                <div class="text-xl font-bold">£13,500 – £14,500</div>
                            </div>
                            <button class="kt-btn kt-btn-sm kt-btn-mono" data-listing-action="apply-pricing">
                                <i class="ki-filled ki-check"></i> Apply to Listing
                            </button>
                        </div>
                        <p class="text-xs text-muted-foreground mt-3">
                            <i class="ki-filled ki-information-4 mr-1"></i>
                            Based on 2 comp-backed valuations marked "Used in Rec." above.
                        </p>
                    </div>

                    {{-- Per-provider snapshot (kept from original — informational reference) --}}
                    <div class="border-t border-border pt-4">
                        <div class="font-semibold text-sm mb-3 text-muted-foreground">All Provider Snapshots</div>

                        <div class="card border border-border p-4 rounded-xl mb-3">
                            <div class="font-semibold text-sm mb-3">Carsmart (2 Types)</div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div class="bg-muted/5 p-3 rounded-lg">
                                    <div class="text-xs text-muted-foreground">Carsmart Valuation</div>
                                    <div class="text-xl font-bold">£14,200</div>
                                </div>
                                <div class="bg-muted/5 p-3 rounded-lg">
                                    <div class="text-xs text-muted-foreground">Carsmart Quick Sale Valuation</div>
                                    <div class="text-xl font-bold">£13,500</div>
                                </div>
                            </div>
                        </div>

                        <div class="card border border-border p-4 rounded-xl mb-3">
                            <div class="font-semibold text-sm mb-3">Motorway (1 Type)</div>
                            <div class="bg-muted/5 p-3 rounded-lg">
                                <div class="text-xs text-muted-foreground">Valuation</div>
                                <div class="text-xl font-bold">£14,450</div>
                            </div>
                        </div>

                        <div class="card border border-border p-4 rounded-xl mb-3">
                            <div class="font-semibold text-sm mb-3">HPI (11 Types)</div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 text-xs">
                                <div class="bg-muted/5 p-2.5 rounded"><div class="text-muted-foreground mb-1">Private Sale Valuation LOW</div><div class="font-semibold text-sm">£19,050</div></div>
                                <div class="bg-muted/5 p-2.5 rounded"><div class="text-muted-foreground mb-1">Private Sale Valuation HIGH</div><div class="font-semibold text-sm">£19,900</div></div>
                                <div class="bg-muted/5 p-2.5 rounded"><div class="text-muted-foreground mb-1">Forecourt Valuation LOW</div><div class="font-semibold text-sm">£19,710</div></div>
                                <div class="bg-muted/5 p-2.5 rounded"><div class="text-muted-foreground mb-1">Forecourt Valuation HIGH</div><div class="font-semibold text-sm">£21,490</div></div>
                                <div class="bg-muted/5 p-2.5 rounded"><div class="text-muted-foreground mb-1">Trade In Poor LOW</div><div class="font-semibold text-sm">£14,260</div></div>
                                <div class="bg-muted/5 p-2.5 rounded"><div class="text-muted-foreground mb-1">Trade In Poor HIGH</div><div class="font-semibold text-sm">£15,600</div></div>
                                <div class="bg-muted/5 p-2.5 rounded"><div class="text-muted-foreground mb-1">Trade In Good LOW</div><div class="font-semibold text-sm">£15,390</div></div>
                                <div class="bg-muted/5 p-2.5 rounded"><div class="text-muted-foreground mb-1">Trade In Good HIGH</div><div class="font-semibold text-sm">£16,830</div></div>
                                <div class="bg-muted/5 p-2.5 rounded"><div class="text-muted-foreground mb-1">Trade In Excellent LOW</div><div class="font-semibold text-sm">£16,470</div></div>
                                <div class="bg-muted/5 p-2.5 rounded"><div class="text-muted-foreground mb-1">Trade In Excellent HIGH</div><div class="font-semibold text-sm">£18,010</div></div>
                                <div class="bg-muted/5 p-2.5 rounded"><div class="text-muted-foreground mb-1">Average Valuation</div><div class="font-semibold text-sm">£17,475</div></div>
                            </div>
                        </div>

                        <div class="card border border-border p-4 rounded-xl mb-3">
                            <div class="font-semibold text-sm mb-3">AutoTrader (7 Types)</div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 text-xs">
                                <div class="bg-muted/5 p-2.5 rounded"><div class="text-muted-foreground mb-1">Recommended Selling Price</div><div class="font-semibold text-sm">£18,950</div></div>
                                <div class="bg-muted/5 p-2.5 rounded"><div class="text-muted-foreground mb-1">How much more than Part Exchange</div><div class="font-semibold text-sm">+£3,500</div></div>
                                <div class="bg-muted/5 p-2.5 rounded"><div class="text-muted-foreground mb-1">Current Valuation</div><div class="font-semibold text-sm">£18,200</div></div>
                                <div class="bg-muted/5 p-2.5 rounded"><div class="text-muted-foreground mb-1">Sell Privately LOW</div><div class="font-semibold text-sm">£17,900</div></div>
                                <div class="bg-muted/5 p-2.5 rounded"><div class="text-muted-foreground mb-1">Sell Privately HIGH</div><div class="font-semibold text-sm">£19,400</div></div>
                                <div class="bg-muted/5 p-2.5 rounded"><div class="text-muted-foreground mb-1">Part Exchange LOW</div><div class="font-semibold text-sm">£14,500</div></div>
                                <div class="bg-muted/5 p-2.5 rounded"><div class="text-muted-foreground mb-1">Part Exchange HIGH</div><div class="font-semibold text-sm">£15,800</div></div>
                            </div>
                        </div>

                        <div class="card border border-border p-4 rounded-xl">
                            <div class="font-semibold text-sm mb-3">WebuyAnyCar (1 Type)</div>
                            <div class="bg-muted/5 p-3 rounded-lg">
                                <div class="text-xs text-muted-foreground">Valuation</div>
                                <div class="text-xl font-bold">£13,800</div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-2 p-3 bg-info/10 border border-info/30 rounded-lg text-sm text-info">
                        <i class="ki-filled ki-information-4 mr-1"></i>
                        All valuations from multiple sources are displayed above.
                    </div>

                </div>

                {{-- ===== AUCTION ===== --}}
                <div data-tab-pane="auction" class="hidden">
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm">
                        @foreach ([['Auction Code', $listing['auction_code'] ?? '—'], ['Status', $listing['auction_status'] ?? '—'], ['Sniper Protection', 'Active (5 min)']] as [$l, $v])
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
                        @foreach ([['listing_created', 'Listing created by JR', '2026-05-31 09:01'], ['valuation_fetched', 'Valuation pulled from Carsmart — £14,200 (+£200)', '2026-05-31 09:03'], ['media_uploaded', 'Photo uploaded: Front 3/4', '2026-05-31 09:10'], ['valuation_applied', 'Guide price updated £14,250 → £14,200 (−£50)', '2026-05-31 09:15'], ['listing_state_changed', 'State changed: Draft → QA', '2026-05-31 10:00']] as [$event, $msg, $time])
                            <div class="card border border-border p-3 flex items-start justify-between gap-2 rounded-xl">
                                <div class="min-w-0">
                                    <span class="text-xs font-mono text-muted-foreground bg-muted/30 px-1.5 py-0.5 rounded">{{ $event }}</span>
                                    <div class="mt-1 break-words">{{ $msg }}</div>
                                </div>
                                <div class="text-xs text-muted-foreground whitespace-nowrap shrink-0">{{ $time }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- ===== HISTORY ===== --}}
                <div data-tab-pane="history" class="hidden space-y-4">

                    <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                        <h3 class="font-semibold text-sm">Full History Timeline</h3>
                        <div class="flex gap-1.5 flex-wrap">
                            <button class="kt-btn kt-btn-xs kt-btn-outline history-filter-btn is-active" data-history-filter="all">All</button>
                            <button class="kt-btn kt-btn-xs kt-btn-outline history-filter-btn" data-history-filter="state">State</button>
                            <button class="kt-btn kt-btn-xs kt-btn-outline history-filter-btn" data-history-filter="valuation">Valuations</button>
                            <button class="kt-btn kt-btn-xs kt-btn-outline history-filter-btn" data-history-filter="pricing">Pricing</button>
                            <button class="kt-btn kt-btn-xs kt-btn-outline history-filter-btn" data-history-filter="qa">QA</button>
                            <button class="kt-btn kt-btn-xs kt-btn-outline history-filter-btn" data-history-filter="media">Media/Docs</button>
                            <button class="kt-btn kt-btn-xs kt-btn-outline history-filter-btn" data-history-filter="comms">Comms</button>
                            <button class="kt-btn kt-btn-xs kt-btn-outline history-filter-btn" data-history-filter="notes">Notes</button>
                        </div>
                    </div>

                    <div class="space-y-2 text-sm" id="history-timeline">

                        @foreach ([
                            ['type' => 'state', 'badge' => 'kt-badge-success', 'icon' => 'ki-check-circle', 'title' => 'State change', 'detail' => 'QA → Published', 'meta' => 'Published by JR · 2026-06-05 14:22'],
                            ['type' => 'qa', 'badge' => 'kt-badge-success', 'icon' => 'ki-check', 'title' => 'QA Passed', 'detail' => 'All checks passed — listing approved for publication', 'meta' => 'Approved by SK · 2026-06-05 14:10'],
                            ['type' => 'pricing', 'badge' => 'kt-badge-outline', 'icon' => 'ki-price-tag', 'title' => 'BIN enabled', 'detail' => 'Buy It Now price set to £14,500', 'meta' => 'Updated by JR · 2026-06-04 11:32'],
                            ['type' => 'comms', 'badge' => 'kt-badge-outline', 'icon' => 'ki-message-text', 'title' => 'Message sent', 'detail' => 'Reminder sent to seller re: outstanding V5C document', 'meta' => 'Sent by JR · 2026-06-04 09:45'],
                            ['type' => 'media', 'badge' => 'kt-badge-outline', 'icon' => 'ki-file-up', 'title' => 'Document uploaded', 'detail' => 'V5C Logbook uploaded', 'meta' => 'Uploaded by Seller · 2026-06-03 16:08'],
                            ['type' => 'notes', 'badge' => 'kt-badge-outline', 'icon' => 'ki-save-2', 'title' => 'Note added', 'detail' => 'Vendor confirmed mileage is correct, awaiting service history scan.', 'meta' => 'Added by JR · 2026-06-03 10:15'],
                            ['type' => 'qa', 'badge' => 'kt-badge-warning', 'icon' => 'ki-message-text', 'title' => 'QA changes requested', 'detail' => 'Missing required photos (6) and V5C document', 'meta' => 'Requested by SK · 2026-06-02 17:00'],
                            ['type' => 'state', 'badge' => 'kt-badge-success', 'icon' => 'ki-check-circle', 'title' => 'State change', 'detail' => 'Draft → QA', 'meta' => 'Submitted by JR · 2026-06-01 13:20'],
                            ['type' => 'pricing', 'badge' => 'kt-badge-outline', 'icon' => 'ki-price-tag', 'title' => 'Reserve updated', 'detail' => 'Reserve price set to £13,950', 'meta' => 'Updated by JR · 2026-06-01 13:15'],
                            ['type' => 'valuation', 'badge' => 'kt-badge-info', 'icon' => 'ki-chart-line-up', 'title' => 'Valuation applied to pricing', 'detail' => 'Guide price updated £14,250 → £14,200 (−£50 / −0.4%)', 'meta' => 'Applied by System · 2026-05-31 09:15'],
                            ['type' => 'media', 'badge' => 'kt-badge-outline', 'icon' => 'ki-add-image', 'title' => 'Media uploaded', 'detail' => 'Photo uploaded: Front 3/4 exterior', 'meta' => 'Uploaded by JR · 2026-05-31 09:10'],
                            ['type' => 'valuation', 'badge' => 'kt-badge-info', 'icon' => 'ki-chart-line-up', 'title' => 'Valuation fetched', 'detail' => 'Valuation pulled from Carsmart — £14,200 (+£200 vs previous)', 'meta' => 'Fetched by System · 2026-05-31 09:03'],
                            ['type' => 'state', 'badge' => 'kt-badge-success', 'icon' => 'ki-check-circle', 'title' => 'Listing created', 'detail' => 'LST-1023 created', 'meta' => 'Created by JR · 2026-05-31 09:01'],
                        ] as $h)
                            <div class="card border border-border p-3 flex items-start gap-3 rounded-xl history-entry" data-history-type="{{ $h['type'] }}">
                                <div class="mt-0.5">
                                    <i class="ki-filled {{ $h['icon'] }} text-muted-foreground"></i>
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-medium text-sm">{{ $h['title'] }}</span>
                                        <span class="kt-badge {{ $h['badge'] }} text-xs">{{ ucfirst($h['type']) }}</span>
                                    </div>
                                    <div class="text-sm text-muted-foreground mt-0.5 break-words">{{ $h['detail'] }}</div>
                                    <div class="text-xs text-muted-foreground mt-1">{{ $h['meta'] }}</div>
                                </div>
                            </div>
                        @endforeach

                    </div>

                </div>

            </div>{{-- /tab panes --}}

        </div>{{-- /main content --}}

        {{-- Right summary sidebar --}}
        <div id="summary-sidebar"
             class="hidden xl:flex flex-col w-72 shrink-0 border-l border-border p-4 gap-5 overflow-y-auto">

            {{-- Summary --}}
            <div>
                <div class="font-semibold text-sm mb-2">Summary</div>

                {{-- Listing / Status --}}
                <div class="text-xs font-semibold text-muted-foreground uppercase tracking-wide mb-1.5 mt-1">Listing</div>
                <div class="space-y-1.5 text-xs mb-3">
                    @foreach ([
                        ['State', $listing['state']],
                        ['Vehicle', $listing['vehicle']],
                        ['VRM', $listing['vrm'] ?? '—'],
                        ['Mileage', number_format($listing['mileage'] ?? 0) . ' mi'],
                        ['Sale Type', $listing['sale_type'] ?? 'CST1'],
                        ['Owner', $listing['owner']],
                    ] as [$k, $v])
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-muted-foreground">{{ $k }}</span>
                            <span class="font-medium text-right truncate max-w-[60%]">{{ $v }}</span>
                        </div>
                    @endforeach
                </div>

                {{-- Compliance --}}
                <div class="text-xs font-semibold text-muted-foreground uppercase tracking-wide mb-1.5">Compliance</div>
                <div class="space-y-1.5 text-xs mb-3">
                    <div class="flex items-center justify-between">
                        <span class="text-muted-foreground">QA Status</span>
                        @if (($listing['qa'] ?? '') === 'Pass')
                            <span class="kt-badge kt-badge-success text-xs">Pass</span>
                        @elseif (($listing['qa'] ?? '') === 'Needs Review')
                            <span class="kt-badge kt-badge-warning text-xs">Needs Review</span>
                        @else
                            <span class="kt-badge kt-badge-outline text-xs">{{ $listing['qa'] ?? '—' }}</span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-muted-foreground">KYC</span>
                        @if (($listing['kyc_status'] ?? '') === 'Verified')
                            <span class="kt-badge kt-badge-success text-xs">Verified</span>
                        @else
                            <span class="kt-badge kt-badge-danger text-xs">Required</span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-muted-foreground">Missing Items</span>
                        <span class="font-medium">{{ $listing['missing_items'] ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-muted-foreground">Finance Outstanding</span>
                        <span class="font-medium">{{ $listing['finance'] ?? 'No' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-muted-foreground">V5C Logbook</span>
                        <span class="kt-badge kt-badge-danger text-xs">Missing</span>
                    </div>
                </div>

                {{-- Valuations snapshot --}}
                <div class="text-xs font-semibold text-muted-foreground uppercase tracking-wide mb-1.5">Valuations</div>
                <div class="space-y-1.5 text-xs mb-3">
                    @foreach ([
                        ['Carsmart', '£14,200'],
                        ['Motorway', '£14,450'],
                        ['HPI Average', '£17,475'],
                        ['AutoTrader Current', '£18,200'],
                        ['WeBuyAnyCar', '£13,800'],
                    ] as [$src, $val])
                        <div class="flex items-center justify-between">
                            <span class="text-muted-foreground">{{ $src }}</span>
                            <span class="font-medium">{{ $val }}</span>
                        </div>
                    @endforeach
                    <button class="kt-btn kt-btn-xs kt-btn-outline w-full mt-1" onclick="document.querySelector('.detail-tab[data-tab=\'valuations\']')?.click()">
                        View All Valuations
                    </button>
                </div>

                {{-- Pricing --}}
                <div class="text-xs font-semibold text-muted-foreground uppercase tracking-wide mb-1.5">Pricing</div>
                <div class="space-y-1.5 text-xs mb-3">
                    @foreach ([
                        ['Guide Price', '£' . number_format($listing['guide'] ?? 0)],
                        ['Reserve', $listing['reserve'] ?? null ? '£' . number_format($listing['reserve']) : 'Not set'],
                        ['BIN Price', $listing['bin'] ?? false ? '£' . number_format($listing['bin_price'] ?? 0) : 'Off'],
                        ['Make Offer', $listing['offer_enabled'] ?? false ? 'Enabled' : 'Off'],
                    ] as [$k, $v])
                        <div class="flex items-center justify-between">
                            <span class="text-muted-foreground">{{ $k }}</span>
                            <span class="font-medium">{{ $v }}</span>
                        </div>
                    @endforeach
                </div>

                {{-- Media / Documents --}}
                <div class="text-xs font-semibold text-muted-foreground uppercase tracking-wide mb-1.5">Media &amp; Documents</div>
                <div class="space-y-1.5 text-xs mb-3">
                    <div class="flex items-center justify-between">
                        <span class="text-muted-foreground">Required Photos</span>
                        <span class="kt-badge kt-badge-danger text-xs">0/6</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-muted-foreground">MOT Certificate</span>
                        <span class="kt-badge kt-badge-success text-xs">Present</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-muted-foreground">Service Records</span>
                        <span class="kt-badge kt-badge-outline text-xs">Not Uploaded</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-muted-foreground">Video/Media</span>
                        <span class="kt-badge kt-badge-outline text-xs">None</span>
                    </div>
                </div>

                {{-- Auction --}}
                <div class="text-xs font-semibold text-muted-foreground uppercase tracking-wide mb-1.5">Auction</div>
                <div class="space-y-1.5 text-xs mb-3">
                    @foreach ([
                        ['Auction Code', $listing['auction_code'] ?? '—'],
                        ['Status', $listing['auction_status'] ?? '—'],
                        ['Sniper Protection', 'Active (5 min)'],
                    ] as [$k, $v])
                        <div class="flex items-center justify-between">
                            <span class="text-muted-foreground">{{ $k }}</span>
                            <span class="font-medium">{{ $v }}</span>
                        </div>
                    @endforeach
                </div>

                {{-- Last Activity --}}
                <div class="text-xs font-semibold text-muted-foreground uppercase tracking-wide mb-1.5">Last Activity</div>
                <div class="card border border-border p-2 rounded-lg text-xs">
                    <div class="font-medium">State change: QA → Published</div>
                    <div class="text-muted-foreground mt-0.5">Published by JR · 2026-06-05 14:22</div>
                </div>
                <button class="kt-btn kt-btn-xs kt-btn-outline w-full mt-1.5" onclick="document.querySelector('.detail-tab[data-tab=\'history\']')?.click()">
                    View Full History
                </button>
            </div>

            <div class="border-t border-border"></div>

            {{-- Edit Essentials --}}
            <div>
                <div class="font-semibold text-sm mb-2">Edit Essentials</div>
                <div class="space-y-2">
                    <div>
                        <label class="text-xs text-muted-foreground mb-1 block">Guide Price £</label>
                        <input class="kt-input w-full text-xs" type="number" placeholder="Guide price"
                            value="{{ $listing['guide'] ?? '' }}">
                    </div>
                    <div>
                        <label class="text-xs text-muted-foreground mb-1 block">Reserve £</label>
                        <input class="kt-input w-full text-xs" type="number" placeholder="Reserve"
                            value="{{ $listing['reserve'] ?? '' }}">
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

    {{-- ===== APPLY TO PRICING MODAL (NEW — spec requires delta preview + confirmation) ===== --}}
    <div id="apply-pricing-modal" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/50 p-4">
        <div class="bg-background border border-border rounded-xl w-full max-w-lg">

            <div class="flex items-center justify-between p-4 border-b border-border">
                <h3 class="font-semibold text-base">Apply Valuation to Pricing</h3>
                <button class="kt-btn kt-btn-sm kt-btn-icon kt-btn-outline" data-modal-close="apply-pricing-modal">
                    <i class="ki-filled ki-cross"></i>
                </button>
            </div>

            <div class="p-4 space-y-4">
                <p class="text-sm text-muted-foreground">
                    Review the change before it's applied to this listing's pricing. This action is logged to Activity & History.
                </p>

                <div class="rounded-xl border border-border overflow-x-auto">
                    <table class="w-full text-sm min-w-[480px]">
                        <thead class="bg-muted/10">
                            <tr class="text-left text-muted-foreground text-xs">
                                <th class="p-2.5 font-medium">Field</th>
                                <th class="p-2.5 font-medium">Current</th>
                                <th class="p-2.5 font-medium">New (from valuation)</th>
                                <th class="p-2.5 font-medium">Delta</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            <tr>
                                <td class="p-2.5 font-medium">Guide Price</td>
                                <td class="p-2.5 text-muted-foreground" id="apply-guide-current">£{{ number_format($listing['guide'] ?? 14250) }}</td>
                                <td class="p-2.5 font-semibold" id="apply-guide-new">£{{ number_format($listing['valuation'] ?? 14200) }}</td>
                                <td class="p-2.5" id="apply-guide-delta">
                                    <span class="kt-badge kt-badge-danger text-xs">−£50</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2.5 font-medium">Reserve</td>
                                <td class="p-2.5 text-muted-foreground" id="apply-reserve-current">£{{ number_format($listing['reserve'] ?? 13950) }}</td>
                                <td class="p-2.5 font-semibold" id="apply-reserve-new">£13,500</td>
                                <td class="p-2.5" id="apply-reserve-delta">
                                    <span class="kt-badge kt-badge-danger text-xs">−£450</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="rounded-lg border border-info/30 bg-info/10 px-3 py-2 text-xs text-info">
                    <i class="ki-filled ki-information-4 mr-1"></i>
                    Source: Carsmart valuation fetched 2026-05-31 09:03.
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 p-4 border-t border-border">
                <button class="kt-btn kt-btn-sm kt-btn-outline" data-modal-close="apply-pricing-modal">Cancel</button>
                <button class="kt-btn kt-btn-sm kt-btn-mono" id="confirm-apply-pricing-btn">
                    <i class="ki-filled ki-check"></i> Confirm & Apply
                </button>
            </div>

        </div>
    </div>

    {{-- ===== PULL VALUATION MODAL (Read-only display of all sources) ===== --}}
    <div id="pull-valuation-modal" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/50 p-4">
        <div class="bg-background border border-border rounded-xl w-full max-w-3xl max-h-[90vh] overflow-y-auto">

            <div class="flex items-center justify-between p-4 border-b border-border sticky top-0 bg-background z-10">
                <div>
                    <h3 class="font-semibold text-base">Latest Valuations</h3>
                    <p class="text-xs text-muted-foreground mt-0.5">All sources · {{ $listing['vrm'] ?? 'VRM' }} · {{ $listing['vehicle'] ?? '' }}</p>
                </div>
                <button class="kt-btn kt-btn-sm kt-btn-icon kt-btn-outline" data-modal-close="pull-valuation-modal">
                    <i class="ki-filled ki-cross"></i>
                </button>
            </div>

            {{-- Fetch state banner (spec: in-progress / succeeded / failed with retry) --}}
            <div class="p-4 pb-0" id="pull-valuation-fetch-state">
                <div class="hidden rounded-lg border border-info/30 bg-info/10 px-3 py-2 text-xs text-info flex items-center gap-2" data-fetch-state="in-progress">
                    <i class="ki-filled ki-loading animate-spin"></i> Fetching latest valuations from all providers…
                </div>
                <div class="hidden rounded-lg border border-success/30 bg-success/10 px-3 py-2 text-xs text-success flex items-center gap-2" data-fetch-state="succeeded">
                    <i class="ki-filled ki-check-circle"></i> All valuations refreshed successfully.
                </div>
                <div class="hidden rounded-lg border border-danger/30 bg-danger/10 px-3 py-2 text-xs text-danger flex items-center justify-between gap-2" data-fetch-state="failed">
                    <span><i class="ki-filled ki-cross-circle mr-1"></i> Some providers failed to respond (rate limited). Showing last known values.</span>
                    <button class="kt-btn kt-btn-xs kt-btn-outline" id="retry-fetch-btn">Retry</button>
                </div>
            </div>

            <div class="p-4 space-y-5">

                {{-- CARSMART --}}
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <span class="font-semibold text-sm">Carsmart</span>
                        <span class="kt-badge kt-badge-outline text-xs">2 valuations</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="rounded-xl border border-border bg-muted/5 p-4">
                            <div class="text-xs text-muted-foreground mb-1">Carsmart Valuation</div>
                            <div class="text-2xl font-bold tracking-tight">£14,200</div>
                        </div>
                        <div class="rounded-xl border border-border bg-muted/5 p-4">
                            <div class="text-xs text-muted-foreground mb-1">Carsmart Quick Sale Valuation</div>
                            <div class="text-2xl font-bold tracking-tight">£13,500</div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-border"></div>

                {{-- MOTORWAY --}}
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <span class="font-semibold text-sm">Motorway</span>
                        <span class="kt-badge kt-badge-outline text-xs">1 valuation</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="rounded-xl border border-border bg-muted/5 p-4">
                            <div class="text-xs text-muted-foreground mb-1">Valuation</div>
                            <div class="text-2xl font-bold tracking-tight">£14,450</div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-border"></div>

                {{-- HPI --}}
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <span class="font-semibold text-sm">HPI</span>
                        <span class="kt-badge kt-badge-outline text-xs">11 valuations</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        <div class="rounded-xl border border-border bg-muted/5 p-3">
                            <div class="text-xs text-muted-foreground mb-1">Private Sale Valuation LOW</div>
                            <div class="text-lg font-bold">£19,050</div>
                        </div>
                        <div class="rounded-xl border border-border bg-muted/5 p-3">
                            <div class="text-xs text-muted-foreground mb-1">Private Sale Valuation HIGH</div>
                            <div class="text-lg font-bold">£19,900</div>
                        </div>
                        <div class="rounded-xl border border-border bg-muted/5 p-3">
                            <div class="text-xs text-muted-foreground mb-1">Forecourt Valuation LOW</div>
                            <div class="text-lg font-bold">£19,710</div>
                        </div>
                        <div class="rounded-xl border border-border bg-muted/5 p-3">
                            <div class="text-xs text-muted-foreground mb-1">Forecourt Valuation HIGH</div>
                            <div class="text-lg font-bold">£21,490</div>
                        </div>
                        <div class="rounded-xl border border-border bg-muted/5 p-3">
                            <div class="text-xs text-muted-foreground mb-1">Trade In Valuation Poor LOW</div>
                            <div class="text-lg font-bold">£14,260</div>
                        </div>
                        <div class="rounded-xl border border-border bg-muted/5 p-3">
                            <div class="text-xs text-muted-foreground mb-1">Trade In Valuation Poor HIGH</div>
                            <div class="text-lg font-bold">£15,600</div>
                        </div>
                        <div class="rounded-xl border border-border bg-muted/5 p-3">
                            <div class="text-xs text-muted-foreground mb-1">Trade In Valuation Good LOW</div>
                            <div class="text-lg font-bold">£15,390</div>
                        </div>
                        <div class="rounded-xl border border-border bg-muted/5 p-3">
                            <div class="text-xs text-muted-foreground mb-1">Trade In Valuation Good HIGH</div>
                            <div class="text-lg font-bold">£16,830</div>
                        </div>
                        <div class="rounded-xl border border-border bg-muted/5 p-3">
                            <div class="text-xs text-muted-foreground mb-1">Trade In Valuation Excellent LOW</div>
                            <div class="text-lg font-bold">£16,470</div>
                        </div>
                        <div class="rounded-xl border border-border bg-muted/5 p-3">
                            <div class="text-xs text-muted-foreground mb-1">Trade In Valuation Excellent HIGH</div>
                            <div class="text-lg font-bold">£18,010</div>
                        </div>
                        <div class="rounded-xl border border-border bg-muted/5 p-3 sm:col-span-2 lg:col-span-1">
                            <div class="text-xs text-muted-foreground mb-1">Average Valuation</div>
                            <div class="text-lg font-bold">£19,475</div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-border"></div>

                {{-- AUTOTRADER --}}
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <span class="font-semibold text-sm">AutoTrader</span>
                        <span class="kt-badge kt-badge-outline text-xs">7 valuations</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        <div class="rounded-xl border border-border bg-muted/5 p-3">
                            <div class="text-xs text-muted-foreground mb-1">Recommended Selling Price</div>
                            <div class="text-lg font-bold">£18,950</div>
                        </div>
                        <div class="rounded-xl border border-border bg-muted/5 p-3">
                            <div class="text-xs text-muted-foreground mb-1">How Much More Than Part Exchange</div>
                            <div class="text-lg font-bold text-success">+£3,500</div>
                        </div>
                        <div class="rounded-xl border border-border bg-muted/5 p-3">
                            <div class="text-xs text-muted-foreground mb-1">Our Current Valuation</div>
                            <div class="text-lg font-bold">£18,200</div>
                        </div>
                        <div class="rounded-xl border border-border bg-muted/5 p-3">
                            <div class="text-xs text-muted-foreground mb-1">Sell Privately LOW</div>
                            <div class="text-lg font-bold">£17,900</div>
                        </div>
                        <div class="rounded-xl border border-border bg-muted/5 p-3">
                            <div class="text-xs text-muted-foreground mb-1">Sell Privately HIGH</div>
                            <div class="text-lg font-bold">£19,400</div>
                        </div>
                        <div class="rounded-xl border border-border bg-muted/5 p-3">
                            <div class="text-xs text-muted-foreground mb-1">Part Exchange LOW</div>
                            <div class="text-lg font-bold">£14,500</div>
                        </div>
                        <div class="rounded-xl border border-border bg-muted/5 p-3">
                            <div class="text-xs text-muted-foreground mb-1">Part Exchange HIGH</div>
                            <div class="text-lg font-bold">£15,800</div>
                        </div>
                        <div class="rounded-xl border border-border bg-muted/5 p-3">
                            <div class="text-xs text-muted-foreground mb-1">Sell to Dealer LOW</div>
                            <div class="text-lg font-bold">£14,000</div>
                        </div>
                        <div class="rounded-xl border border-border bg-muted/5 p-3">
                            <div class="text-xs text-muted-foreground mb-1">Sell to Dealer HIGH</div>
                            <div class="text-lg font-bold">£15,500</div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-border"></div>

                {{-- WEBUYANYCAR --}}
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <span class="font-semibold text-sm">WeBuyAnyCar</span>
                        <span class="kt-badge kt-badge-outline text-xs">1 valuation</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="rounded-xl border border-border bg-muted/5 p-4">
                            <div class="text-xs text-muted-foreground mb-1">Valuation</div>
                            <div class="text-2xl font-bold tracking-tight">£13,800</div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="flex items-center justify-between gap-2 p-4 border-t border-border sticky bottom-0 bg-background">
                <p class="text-xs text-muted-foreground">
                    <i class="ki-filled ki-information-4 mr-1"></i>
                    Values shown are the most recently fetched from each provider.
                </p>
                <div class="flex gap-2 shrink-0">
                    <button class="kt-btn kt-btn-sm kt-btn-outline" data-listing-action="add-valuation">
                        <i class="ki-filled ki-add-item"></i> Edit / Add Values
                    </button>
                    <button class="kt-btn kt-btn-sm kt-btn-outline" data-modal-close="pull-valuation-modal">Close</button>
                </div>
            </div>

        </div>
    </div>

    {{-- ===== ADD VALUATION MODAL (Editable inputs) ===== --}}
    <div id="add-valuation-modal" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/50 p-4">
        <div class="bg-background border border-border rounded-xl w-full max-w-3xl max-h-[85vh] overflow-y-auto">

            <div class="flex items-center justify-between p-4 border-b border-border sticky top-0 bg-background z-10">
                <h3 class="font-semibold text-base">Add / Edit Valuation</h3>
                <button class="kt-btn kt-btn-sm kt-btn-icon kt-btn-outline" data-modal-close="add-valuation-modal">
                    <i class="ki-filled ki-cross"></i>
                </button>
            </div>

            <div class="p-4 pb-0">
                <div class="rounded-xl border border-primary/30 bg-primary/5 p-4">
                    <label class="text-xs font-semibold text-foreground mb-1 block">Set as Official Valuation *</label>
                    <p class="text-xs text-muted-foreground mb-2">
                        Choose which figure below becomes this listing's official valuation on save. It will update the Overview card, recalculate deltas vs Guide/Reserve, and add an entry to the Valuations log.
                    </p>
                    <select class="kt-input w-full" id="official-valuation-select">
                        <option value="">— Select a value to use as official valuation —</option>
                        <optgroup label="Carsmart">
                            <option value="carsmart_valuation">Carsmart Valuation</option>
                            <option value="carsmart_quick_sale_valuation">Carsmart Quick Sale Valuation</option>
                        </optgroup>
                        <optgroup label="Motorway">
                            <option value="motorway_valuation">Motorway Valuation</option>
                        </optgroup>
                        <optgroup label="HPI">
                            <option value="hpi_private_sale_low">HPI Private Sale Valuation LOW</option>
                            <option value="hpi_private_sale_high">HPI Private Sale Valuation HIGH</option>
                            <option value="hpi_forecourt_low">HPI Forecourt Valuation LOW</option>
                            <option value="hpi_forecourt_high">HPI Forecourt Valuation HIGH</option>
                            <option value="hpi_trade_in_poor_low">HPI Trade In Poor LOW</option>
                            <option value="hpi_trade_in_poor_high">HPI Trade In Poor HIGH</option>
                            <option value="hpi_trade_in_good_low">HPI Trade In Good LOW</option>
                            <option value="hpi_trade_in_good_high">HPI Trade In Good HIGH</option>
                            <option value="hpi_trade_in_excellent_low">HPI Trade In Excellent LOW</option>
                            <option value="hpi_trade_in_excellent_high">HPI Trade In Excellent HIGH</option>
                            <option value="hpi_average_valuation">HPI Average Valuation</option>
                        </optgroup>
                        <optgroup label="AutoTrader">
                            <option value="autotrader_recommended_price">AutoTrader Recommended Selling Price</option>
                            <option value="autotrader_more_than_pex">AutoTrader More Than Part Exchange</option>
                            <option value="autotrader_current_valuation">AutoTrader Current Valuation</option>
                            <option value="autotrader_sell_privately_low">AutoTrader Sell Privately LOW</option>
                            <option value="autotrader_sell_privately_high">AutoTrader Sell Privately HIGH</option>
                            <option value="autotrader_part_exchange_low">AutoTrader Part Exchange LOW</option>
                            <option value="autotrader_part_exchange_high">AutoTrader Part Exchange HIGH</option>
                            <option value="autotrader_sell_to_dealer_low">AutoTrader Sell to Dealer LOW</option>
                            <option value="autotrader_sell_to_dealer_high">AutoTrader Sell to Dealer HIGH</option>
                        </optgroup>
                        <optgroup label="WeBuyAnyCar">
                            <option value="webuyanycar_valuation">WeBuyAnyCar Valuation</option>
                        </optgroup>
                    </select>
                    <p class="text-xs text-danger mt-1.5 hidden" id="official-valuation-error">
                        Please select a value above, or enter an amount in the matching field below, before saving.
                    </p>
                </div>
            </div>

            <div class="p-4 space-y-5">

                {{-- Carsmart --}}
                <div>
                    <div class="font-semibold text-sm mb-2">Carsmart</div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs text-muted-foreground mb-1 block">Carsmart Valuation £</label>
                            <input class="kt-input w-full text-sm" type="number" name="carsmart_valuation" placeholder="e.g., 14200">
                        </div>
                        <div>
                            <label class="text-xs text-muted-foreground mb-1 block">Carsmart Quick Sale Valuation £</label>
                            <input class="kt-input w-full text-sm" type="number" name="carsmart_quick_sale_valuation" placeholder="e.g., 13500">
                        </div>
                    </div>
                </div>

                <div class="border-t border-border"></div>

                {{-- Motorway --}}
                <div>
                    <div class="font-semibold text-sm mb-2">Motorway</div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs text-muted-foreground mb-1 block">Valuation £</label>
                            <input class="kt-input w-full text-sm" type="number" name="motorway_valuation" placeholder="e.g., 14450">
                        </div>
                    </div>
                </div>

                <div class="border-t border-border"></div>

                {{-- HPI --}}
                <div>
                    <div class="font-semibold text-sm mb-2">HPI</div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        <div>
                            <label class="text-xs text-muted-foreground mb-1 block">Private Sale Valuation LOW £</label>
                            <input class="kt-input w-full text-sm" type="number" name="hpi_private_sale_low" placeholder="19050">
                        </div>
                        <div>
                            <label class="text-xs text-muted-foreground mb-1 block">Private Sale Valuation HIGH £</label>
                            <input class="kt-input w-full text-sm" type="number" name="hpi_private_sale_high" placeholder="19900">
                        </div>
                        <div>
                            <label class="text-xs text-muted-foreground mb-1 block">Forecourt Valuation LOW £</label>
                            <input class="kt-input w-full text-sm" type="number" name="hpi_forecourt_low" placeholder="19710">
                        </div>
                        <div>
                            <label class="text-xs text-muted-foreground mb-1 block">Forecourt Valuation HIGH £</label>
                            <input class="kt-input w-full text-sm" type="number" name="hpi_forecourt_high" placeholder="21490">
                        </div>
                        <div>
                            <label class="text-xs text-muted-foreground mb-1 block">Trade In Valuation Poor LOW £</label>
                            <input class="kt-input w-full text-sm" type="number" name="hpi_trade_in_poor_low" placeholder="14260">
                        </div>
                        <div>
                            <label class="text-xs text-muted-foreground mb-1 block">Trade In Valuation Poor HIGH £</label>
                            <input class="kt-input w-full text-sm" type="number" name="hpi_trade_in_poor_high" placeholder="15600">
                        </div>
                        <div>
                            <label class="text-xs text-muted-foreground mb-1 block">Trade In Valuation Good LOW £</label>
                            <input class="kt-input w-full text-sm" type="number" name="hpi_trade_in_good_low" placeholder="15390">
                        </div>
                        <div>
                            <label class="text-xs text-muted-foreground mb-1 block">Trade In Valuation Good HIGH £</label>
                            <input class="kt-input w-full text-sm" type="number" name="hpi_trade_in_good_high" placeholder="16830">
                        </div>
                        <div>
                            <label class="text-xs text-muted-foreground mb-1 block">Trade In Valuation Excellent LOW £</label>
                            <input class="kt-input w-full text-sm" type="number" name="hpi_trade_in_excellent_low" placeholder="16470">
                        </div>
                        <div>
                            <label class="text-xs text-muted-foreground mb-1 block">Trade In Valuation Excellent HIGH £</label>
                            <input class="kt-input w-full text-sm" type="number" name="hpi_trade_in_excellent_high" placeholder="18010">
                        </div>
                        <div>
                            <label class="text-xs text-muted-foreground mb-1 block">Average Valuation £</label>
                            <input class="kt-input w-full text-sm" type="number" name="hpi_average_valuation" placeholder="19475">
                        </div>
                    </div>
                </div>

                <div class="border-t border-border"></div>

                {{-- AutoTrader --}}
                <div>
                    <div class="font-semibold text-sm mb-2">AutoTrader</div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        <div>
                            <label class="text-xs text-muted-foreground mb-1 block">Recommended Selling Price £</label>
                            <input class="kt-input w-full text-sm" type="number" name="autotrader_recommended_price" placeholder="18950">
                        </div>
                        <div>
                            <label class="text-xs text-muted-foreground mb-1 block">More Than Part Exchange £</label>
                            <input class="kt-input w-full text-sm" type="number" name="autotrader_more_than_pex" placeholder="3500">
                        </div>
                        <div>
                            <label class="text-xs text-muted-foreground mb-1 block">Current Valuation £</label>
                            <input class="kt-input w-full text-sm" type="number" name="autotrader_current_valuation" placeholder="18200">
                        </div>
                        <div>
                            <label class="text-xs text-muted-foreground mb-1 block">Sell Privately LOW £</label>
                            <input class="kt-input w-full text-sm" type="number" name="autotrader_sell_privately_low" placeholder="17900">
                        </div>
                        <div>
                            <label class="text-xs text-muted-foreground mb-1 block">Sell Privately HIGH £</label>
                            <input class="kt-input w-full text-sm" type="number" name="autotrader_sell_privately_high" placeholder="19400">
                        </div>
                        <div>
                            <label class="text-xs text-muted-foreground mb-1 block">Part Exchange LOW £</label>
                            <input class="kt-input w-full text-sm" type="number" name="autotrader_part_exchange_low" placeholder="14500">
                        </div>
                        <div>
                            <label class="text-xs text-muted-foreground mb-1 block">Part Exchange HIGH £</label>
                            <input class="kt-input w-full text-sm" type="number" name="autotrader_part_exchange_high" placeholder="15800">
                        </div>
                        <div>
                            <label class="text-xs text-muted-foreground mb-1 block">Sell to Dealer LOW £</label>
                            <input class="kt-input w-full text-sm" type="number" name="autotrader_sell_to_dealer_low" placeholder="14000">
                        </div>
                        <div>
                            <label class="text-xs text-muted-foreground mb-1 block">Sell to Dealer HIGH £</label>
                            <input class="kt-input w-full text-sm" type="number" name="autotrader_sell_to_dealer_high" placeholder="15500">
                        </div>
                    </div>
                </div>

                <div class="border-t border-border"></div>

                {{-- WebuyAnyCar --}}
                <div>
                    <div class="font-semibold text-sm mb-2">WeBuyAnyCar</div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs text-muted-foreground mb-1 block">Valuation £</label>
                            <input class="kt-input w-full text-sm" type="number" name="webuyanycar_valuation" placeholder="e.g., 13800">
                        </div>
                    </div>
                </div>

            </div>

            <div class="flex items-center justify-end gap-2 p-4 border-t border-border sticky bottom-0 bg-background">
                <button class="kt-btn kt-btn-sm kt-btn-outline" data-modal-close="add-valuation-modal">Cancel</button>
                <button class="kt-btn kt-btn-sm kt-btn-mono" id="save-add-valuation-btn">
                    <i class="ki-filled ki-save-2"></i> Save Valuations
                </button>
            </div>

        </div>
    </div>

    {{-- ===== VEHICLE CHECK MODAL (HPI History / Previous Owners / MOT Advisories / Exterior Grading) =====
         Single modal, content rendered per data-history-check type. Styling follows the reference design:
         dark gradient card, VRM plate badge, vehicle avatar + name/spec line, content panel below. --}}
    <div id="vehicle-check-modal" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/60 p-4">
        <div class="rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl text-white max-h-[85vh] flex flex-col">

            <div class="p-5 relative overflow-y-auto">
                <button class="absolute top-4 right-4 w-7 h-7 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center" data-modal-close="vehicle-check-modal">
                    <i class="ki-filled ki-cross text-xs"></i>
                </button>

                <h3 class="text-lg font-semibold mb-4" id="vc-modal-title">Vehicle Check</h3>

                <div class="flex items-center gap-2 mb-4">
                    <span class="flex items-center bg-white text-slate-900 rounded-md overflow-hidden text-sm font-bold">
                        <span class="bg-blue-600 w-2 h-full block"></span>
                        <span class="px-3 py-1.5 tracking-wide" id="vc-modal-vrm">{{ $listing['vrm'] ?? 'AB19 CDE' }}</span>
                    </span>
                    <span class="text-xs text-white/70" id="vc-modal-subcount"></span>
                </div>

                <div class="flex items-center gap-3 mb-4">
                    <div class="w-11 h-11 rounded-full bg-white/10 flex items-center justify-center shrink-0">
                        <i class="ki-filled ki-car text-xl"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="font-bold text-base leading-tight truncate" id="vc-modal-vehicle-name">{{ $listing['vehicle'] ?? 'Vehicle' }}</div>
                        <div class="text-xs text-white/70 mt-0.5" id="vc-modal-vehicle-meta">{{ $vcVehicleMeta ?: ($listing['vehicle'] ?? '') }}</div>
                    </div>
                </div>

                <div class="bg-white/10 rounded-xl p-4" id="vc-modal-body">
                    {{-- populated dynamically per check type --}}
                </div>

                <p class="text-[11px] text-white/50 mt-4" id="vc-modal-footnote"></p>
            </div>
        </div>
    </div>

    <script>
    const vehicleChecksData = @json($vehicleChecksData);
    const vcVehicleMeta = @json($vcVehicleMeta);
    </script>

    <script>
    (function () {

        const container = document.currentScript.closest('.kt-container-fixed') || document;

        // ===== TAB SWITCHING =====
        const panes = container.querySelectorAll('[data-tab-pane]');
        const tabs  = container.querySelectorAll('.detail-tab');

        function activateTab(target) {
            panes.forEach(pane => pane.classList.add('hidden'));
            tabs.forEach(tab => {
                tab.classList.remove('bg-background', 'border', 'border-b-0', 'border-border', 'text-foreground');
                tab.classList.add('text-muted-foreground');
            });
            container.querySelector(`[data-tab-pane="${target}"]`)?.classList.remove('hidden');
            const activeTab = container.querySelector(`.detail-tab[data-tab="${target}"]`);
            activeTab?.classList.add('bg-background', 'border', 'border-b-0', 'border-border', 'text-foreground');
            activeTab?.classList.remove('text-muted-foreground');
        }

        tabs.forEach(tab => tab.addEventListener('click', () => activateTab(tab.dataset.tab)));
        activateTab('overview');

        // ===== MODAL HELPERS =====
        function openModal(id) {
            const modal = document.getElementById(id);
            modal?.classList.remove('hidden');
            modal?.classList.add('flex');
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            modal?.classList.add('hidden');
            modal?.classList.remove('flex');
        }

        // Close on [data-modal-close] buttons
        document.querySelectorAll('[data-modal-close]').forEach(btn => {
            btn.addEventListener('click', () => closeModal(btn.dataset.modalClose));
        });

        // Close on backdrop click
        ['pull-valuation-modal', 'add-valuation-modal', 'apply-pricing-modal', 'vehicle-check-modal'].forEach(id => {
            document.getElementById(id)?.addEventListener('click', e => {
                if (e.target.id === id) closeModal(id);
            });
        });

        // ===== PULL VALUATION → opens pull-valuation-modal (read-only display) =====
        // Spec: show fetch states (in-progress / succeeded / failed + retry) on open
        function simulateFetch() {
            const states = ['in-progress', 'succeeded', 'failed'];
            const banners = {};
            states.forEach(s => {
                banners[s] = document.querySelector(`#pull-valuation-fetch-state [data-fetch-state="${s}"]`);
                banners[s]?.classList.add('hidden');
            });
            banners['in-progress']?.classList.remove('hidden');
            const statusBadge = document.getElementById('valuation-fetch-status');
            if (statusBadge) statusBadge.textContent = 'Fetching…';

            setTimeout(() => {
                banners['in-progress']?.classList.add('hidden');
                // Demo: succeed most of the time
                const ok = true;
                banners[ok ? 'succeeded' : 'failed']?.classList.remove('hidden');
                if (statusBadge) statusBadge.textContent = ok ? 'Up to date' : 'Fetch failed';
            }, 900);
        }

        document.querySelectorAll('[data-listing-action="pull-valuation"]').forEach(btn => {
            btn.addEventListener('click', () => {
                openModal('pull-valuation-modal');
                simulateFetch();
            });
        });

        document.getElementById('retry-fetch-btn')?.addEventListener('click', simulateFetch);

        // ===== ADD VALUATION → opens add-valuation-modal (editable inputs) =====
        document.querySelectorAll('[data-listing-action="add-valuation"]').forEach(btn => {
            btn.addEventListener('click', () => openModal('add-valuation-modal'));
        });

        // "Edit / Add Values" button inside pull-valuation-modal switches to add modal
        document.querySelector('#pull-valuation-modal [data-listing-action="add-valuation"]')?.addEventListener('click', () => {
            closeModal('pull-valuation-modal');
            openModal('add-valuation-modal');
        });

        // ===== APPLY TO PRICING — opens confirmation modal with delta preview =====
        document.querySelectorAll('[data-listing-action="apply-pricing"]').forEach(btn => {
            btn.addEventListener('click', () => openModal('apply-pricing-modal'));
        });

        document.getElementById('confirm-apply-pricing-btn')?.addEventListener('click', () => {
            /*
            Replace with real API call:
            await fetch(`/listings/{id}/apply-pricing`, { method: 'POST', ... });
            */
            closeModal('apply-pricing-modal');
            // Reflect the change in the Overview valuation card badges (demo only)
            const statusBadge = document.getElementById('valuation-fetch-status');
            if (statusBadge) statusBadge.textContent = 'Applied to pricing';
        });

        document.getElementById('official-valuation-select')?.addEventListener('change', (e) => {
            const modal = document.getElementById('add-valuation-modal');
            modal?.querySelectorAll('input[name]').forEach(input => {
                input.classList.remove('ring-2', 'ring-primary');
            });
            const field = e.target.value;
            if (field) {
                const input = modal?.querySelector(`input[name="${field}"]`);
                input?.classList.add('ring-2', 'ring-primary');
                input?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                input?.focus();
            }
            document.getElementById('official-valuation-error')?.classList.add('hidden');
        });

        // ===== SAVE ADD VALUATION — sets the chosen field as the official valuation =====
        const officialValuationLabels = {
            carsmart_valuation: ['Carsmart Valuation', 'Carsmart'],
            carsmart_quick_sale_valuation: ['Carsmart Quick Sale Valuation', 'Carsmart'],
            motorway_valuation: ['Motorway Valuation', 'Motorway'],
            hpi_private_sale_low: ['HPI Private Sale Valuation LOW', 'HPI'],
            hpi_private_sale_high: ['HPI Private Sale Valuation HIGH', 'HPI'],
            hpi_forecourt_low: ['HPI Forecourt Valuation LOW', 'HPI'],
            hpi_forecourt_high: ['HPI Forecourt Valuation HIGH', 'HPI'],
            hpi_trade_in_poor_low: ['HPI Trade In Poor LOW', 'HPI'],
            hpi_trade_in_poor_high: ['HPI Trade In Poor HIGH', 'HPI'],
            hpi_trade_in_good_low: ['HPI Trade In Good LOW', 'HPI'],
            hpi_trade_in_good_high: ['HPI Trade In Good HIGH', 'HPI'],
            hpi_trade_in_excellent_low: ['HPI Trade In Excellent LOW', 'HPI'],
            hpi_trade_in_excellent_high: ['HPI Trade In Excellent HIGH', 'HPI'],
            hpi_average_valuation: ['HPI Average Valuation', 'HPI'],
            autotrader_recommended_price: ['AutoTrader Recommended Selling Price', 'AutoTrader'],
            autotrader_more_than_pex: ['AutoTrader More Than Part Exchange', 'AutoTrader'],
            autotrader_current_valuation: ['AutoTrader Current Valuation', 'AutoTrader'],
            autotrader_sell_privately_low: ['AutoTrader Sell Privately LOW', 'AutoTrader'],
            autotrader_sell_privately_high: ['AutoTrader Sell Privately HIGH', 'AutoTrader'],
            autotrader_part_exchange_low: ['AutoTrader Part Exchange LOW', 'AutoTrader'],
            autotrader_part_exchange_high: ['AutoTrader Part Exchange HIGH', 'AutoTrader'],
            autotrader_sell_to_dealer_low: ['AutoTrader Sell to Dealer LOW', 'AutoTrader'],
            autotrader_sell_to_dealer_high: ['AutoTrader Sell to Dealer HIGH', 'AutoTrader'],
            webuyanycar_valuation: ['WeBuyAnyCar Valuation', 'WeBuyAnyCar'],
        };

        function formatGBP(n) {
            return '£' + Number(n).toLocaleString('en-GB', { maximumFractionDigits: 0 });
        }

        function formatDelta(n) {
            const sign = n >= 0 ? '+' : '';
            return `${sign}£${Math.abs(n).toLocaleString('en-GB', { maximumFractionDigits: 0 })}`;
        }

        document.querySelector('#save-add-valuation-btn')?.addEventListener('click', () => {
            const modal = document.getElementById('add-valuation-modal');
            const data  = {};
            modal?.querySelectorAll('input[name]').forEach(input => {
                data[input.name] = input.value ? Number(input.value) : null;
            });

            const select = document.getElementById('official-valuation-select');
            const chosenField = select?.value;
            const errorMsg = document.getElementById('official-valuation-error');
            const chosenAmount = chosenField ? data[chosenField] : null;

            // Require a selected field AND a value entered for it
            if (!chosenField || chosenAmount === null || chosenAmount === undefined || isNaN(chosenAmount)) {
                errorMsg?.classList.remove('hidden');
                select?.focus();
                return;
            }
            errorMsg?.classList.add('hidden');

            const [label, providerName] = officialValuationLabels[chosenField] || [chosenField, 'Manual'];
            const now = new Date();
            const timestamp = now.toISOString().slice(0, 10) + ' ' + now.toTimeString().slice(0, 5);

            // 1. Update Overview valuation card
            document.getElementById('ov-valuation-amount').textContent = formatGBP(chosenAmount);
            document.getElementById('ov-valuation-source').textContent = `${providerName} (${label})`;
            document.getElementById('ov-valuation-timestamp').textContent = timestamp;

            // 2. Recalculate deltas vs current Guide / Reserve
            const guideVal   = Number(document.getElementById('pricing-guide-input')?.value) || 0;
            const reserveVal = Number(document.getElementById('pricing-reserve-input')?.value) || 0;
            const deltaGuide   = chosenAmount - guideVal;
            const deltaReserve = chosenAmount - reserveVal;

            const deltaGuideEl = document.getElementById('ov-delta-guide');
            if (deltaGuideEl) {
                deltaGuideEl.textContent = formatDelta(deltaGuide);
                deltaGuideEl.classList.toggle('kt-badge-success', deltaGuide >= 0);
                deltaGuideEl.classList.toggle('kt-badge-danger', deltaGuide < 0);
            }
            const deltaReserveEl = document.getElementById('ov-delta-reserve');
            if (deltaReserveEl) {
                deltaReserveEl.textContent = formatDelta(deltaReserve);
                deltaReserveEl.classList.toggle('kt-badge-success', deltaReserve >= 0);
                deltaReserveEl.classList.toggle('kt-badge-danger', deltaReserve < 0);
            }

            const statusBadge = document.getElementById('valuation-fetch-status');
            if (statusBadge) statusBadge.textContent = 'Updated';

            // 3. Append a row to the Valuations log table
            const tbody = document.getElementById('valuation-log-body');
            if (tbody) {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="p-2.5 whitespace-nowrap">${timestamp}</td>
                    <td class="p-2.5">${providerName}</td>
                    <td class="p-2.5">Manual entry</td>
                    <td class="p-2.5 font-semibold">${formatGBP(chosenAmount)}</td>
                    <td class="p-2.5"><span class="kt-badge ${deltaGuide >= 0 ? 'kt-badge-success' : 'kt-badge-danger'} text-xs">${formatDelta(deltaGuide)}</span></td>
                    <td class="p-2.5 text-muted-foreground">${label} — set as official valuation</td>
                    <td class="p-2.5 text-center text-muted-foreground">—</td>
                    <td class="p-2.5 text-center"><i class="ki-filled ki-check text-success"></i></td>
                    <td class="p-2.5 text-right whitespace-nowrap">
                        <button class="kt-btn kt-btn-xs kt-btn-outline" data-listing-action="apply-pricing">Apply</button>
                        <button class="kt-btn kt-btn-xs kt-btn-outline text-danger" data-listing-action="remove-valuation">Remove</button>
                    </td>
                `;
                tbody.prepend(row);
                // Re-bind the new row's action buttons
                row.querySelector('[data-listing-action="remove-valuation"]')?.addEventListener('click', (e) => {
                    if (!confirm('Remove this valuation record?')) return;
                    e.target.closest('tr')?.remove();
                });
                row.querySelector('[data-listing-action="apply-pricing"]')?.addEventListener('click', () => openModal('apply-pricing-modal'));
            }

            /*
            Replace with real API call:
            await fetch(`/listings/{id}/valuations`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ ...data, official_field: chosenField })
            });
            */
            console.log('Valuation data:', data, 'Official field:', chosenField);
            closeModal('add-valuation-modal');
        });

        // ===== VIEW VALUATION HISTORY (sidebar link) =====
        document.querySelectorAll('[data-listing-action="view-valuation-history"]').forEach(btn => {
            btn.addEventListener('click', () => activateTab('valuations'));
        });

        // ===== REMOVE VALUATION ROWS (log table) =====
        document.querySelectorAll('[data-listing-action="remove-valuation"]').forEach(btn => {
            btn.addEventListener('click', () => {
                if (!confirm('Remove this valuation record?')) return;
                btn.closest('tr')?.remove();
                const tbody = document.getElementById('valuation-log-body');
                if (tbody && tbody.querySelectorAll('tr').length === 0) {
                    tbody.innerHTML = `<tr><td colspan="9" class="p-6 text-center text-muted-foreground text-sm">No valuations recorded yet.</td></tr>`;
                }
            });
        });

        // ===== HISTORY FILTERS =====
        const historyFilterBtns = document.querySelectorAll('.history-filter-btn');
        const historyEntries    = document.querySelectorAll('.history-entry');

        historyFilterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                historyFilterBtns.forEach(b => b.classList.remove('is-active', 'kt-btn-mono'));
                btn.classList.add('is-active', 'kt-btn-mono');
                const filter = btn.dataset.historyFilter;
                historyEntries.forEach(entry => {
                    if (filter === 'all' || entry.dataset.historyType === filter) {
                        entry.classList.remove('hidden');
                    } else {
                        entry.classList.add('hidden');
                    }
                });
            });
        });

        // ===== VEHICLE CHECK MODAL (HPI / Previous Owners / MOT Advisories / Exterior Grading) =====
        function renderVehicleCheck(type) {
            const data = vehicleChecksData[type];
            if (!data) return;

            document.getElementById('vc-modal-title').textContent = data.title;
            document.getElementById('vc-modal-vehicle-meta').textContent = vcVehicleMeta;

            const body     = document.getElementById('vc-modal-body');
            const subcount = document.getElementById('vc-modal-subcount');
            const footnote = document.getElementById('vc-modal-footnote');
            subcount.textContent = '';
            footnote.textContent = '';

            if (type === 'hpi') {
                body.innerHTML = `<ul class="space-y-2.5 text-sm">${data.items.map(i =>
                    `<li class="flex items-start gap-2"><i class="ki-filled ki-check-circle text-lime-400 mt-0.5"></i><span>${i}</span></li>`
                ).join('')}</ul>`;
                footnote.textContent = `Vehicle history check completed on ${data.checked_at} using data from ${data.source}. Please note that all warranties are disclaimed — we recommend carrying out your own checks to ensure you're fully informed.`;
            }

            else if (type === 'owners') {
                subcount.textContent = `${data.count} owner${data.count === 1 ? '' : 's'}`;
                body.innerHTML = `<table class="w-full text-sm">
                    <thead><tr class="text-left text-white/60 text-xs">
                        <th class="pb-2 font-medium">Owner</th>
                        <th class="pb-2 font-medium text-right">Length of ownership</th>
                    </tr></thead>
                    <tbody class="divide-y divide-white/10">
                        ${data.rows.map(r => `<tr><td class="py-2">${r.owner}</td><td class="py-2 text-right">${r.length}</td></tr>`).join('')}
                    </tbody>
                </table>`;
            }

            else if (type === 'mot-advisories') {
                subcount.textContent = `${data.count} advisor${data.count === 1 ? 'y' : 'ies'}`;
                body.innerHTML = `<div class="space-y-2 text-sm">
                    ${data.advisories.map(a => `<div class="bg-white/5 rounded-lg px-3 py-2">${a}</div>`).join('')}
                </div>`;
                if (data.notes) footnote.textContent = data.notes;
            }

            else if (type === 'exterior-grading') {
                subcount.textContent = `Grade ${data.grade}`;
                body.innerHTML = `<div class="overflow-x-auto"><table class="w-full text-sm min-w-[360px]">
                    <thead><tr class="text-left text-white/60 text-xs">
                        <th class="pb-2 pr-3 font-medium">Area</th>
                        ${data.columns.map(c => `<th class="pb-2 pr-3 font-medium">${c}</th>`).join('')}
                    </tr></thead>
                    <tbody class="divide-y divide-white/10">
                        ${data.rows.map(r => `<tr><td class="py-2 pr-3 text-white/70">${r.label}</td>${data.columns.map(c => `<td class="py-2 pr-3">${r[c] ?? '—'}</td>`).join('')}</tr>`).join('')}
                    </tbody>
                </table></div>`;
            }
        }

        document.querySelectorAll('[data-history-check]').forEach(btn => {
            btn.addEventListener('click', () => {
                renderVehicleCheck(btn.dataset.historyCheck);
                openModal('vehicle-check-modal');
            });
        });

    })();
    </script>

</div>

@endsection