@extends('layouts.app')

@section('title', ($listing['id'] ?? 'Listing') . ' — Carsmart CRM')

@section('content')

    <div class="kt-container-fixed py-6">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-1.5 text-xs text-muted-foreground mb-4">
            <a href="{{ route('listings.index') }}" class="hover:text-foreground">Listings</a>
            <i data-lucide="chevron-right" class="w-3 h-3"></i>
            <span class="text-foreground font-medium">{{ $listing['id'] }}</span>
        </nav>

        <div class="flex flex-col">

            {{-- ===== HEADER ===== --}}
            <div
                class="p-4 border-b border-border flex flex-col lg:flex-row lg:items-start lg:justify-between gap-3 shrink-0">
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-base font-semibold">{{ $listing['id'] }}</h2>
                        <span class="kt-badge kt-badge-outline">{{ $listing['state'] }}</span>
                        <span class="kt-badge kt-badge-outline text-xs">{{ $listing['sale_type'] ?? 'CST1' }}</span>
                    </div>
                    <div class="text-sm mt-0.5 text-muted-foreground">{{ $listing['vehicle'] }}</div>
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

            {{-- ===== ACTION BAR ===== --}}
            <div class="px-4 py-2.5 border-b border-border shrink-0">
                <div class="flex flex-wrap gap-1.5">
                    <button class="kt-btn kt-btn-sm kt-btn-mono" data-listing-action="submit-qa">
                        <i class="ki-filled ki-send"></i> Submit for QA
                    </button>
                    <button class="kt-btn kt-btn-sm kt-btn-outline" data-listing-action="pull-valuation">
                        <i class="ki-filled ki-chart-line-up"></i> Pull Latest Valuation
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
            </div>

            {{-- ===== BODY: TABS + RIGHT PANEL ===== --}}
            <div class="flex flex-col xl:flex-row flex-1 overflow-hidden min-w-0">

                {{-- Main content --}}
                <div class="flex-1 min-w-0 overflow-y-auto">

                    {{-- Tab nav --}}
                    <div class="kt-scrollable-x flex gap-0.5 px-4 pt-3 border-b border-border whitespace-nowrap shrink-0">
                        @foreach (['Status', 'About You', 'Specifications', 'Documents', 'MOT', 'Interior', 'Exterior', 'Damage & Wear', 'Technical Health', 'Valuations', 'Media', 'Pricing', 'QA', 'Auction', 'Notes', 'Activity', 'History'] as $tab)
                            @php
                                $tabId = strtolower(str_replace([' & ', ' '], ['-', '-'], $tab));
                            @endphp
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

                        {{-- ===== STATUS ===== --}}
                        <div data-tab-pane="status">

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
                                    <input class="kt-input w-full" type="text" value="{{ $listing['vrm'] ?? '' }}"
                                        placeholder="e.g., AB19 CDE">
                                </div>
                            </div>

                            {{-- Valuation with Dropdown --}}
                            <div class="mt-4 p-4 border border-border rounded-xl bg-muted/5">
                                <div class="flex items-center justify-between mb-3">
                                    <label class="text-sm font-semibold">Valuation (Price) *</label>
                                    <button class="kt-btn kt-btn-xs kt-btn-outline" data-listing-action="pull-valuation">
                                        <i class="ki-filled ki-refresh"></i> Get Valuation
                                    </button>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-xs text-muted-foreground mb-1 block">Valuation Source</label>
                                        <select class="kt-input w-full text-sm" id="valuation-source">
                                            <option value="">-- Select Source --</option>
                                            <option value="carsmart">Carsmart</option>
                                            <option value="hpi">HPI</option>
                                            <option value="autotrader">AutoTrader</option>
                                            <option value="webuyanycar">WebuyAnyCar</option>
                                            <option value="motorway">Motorway</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="text-xs text-muted-foreground mb-1 block">Valuation Amount £</label>
                                        <input class="kt-input w-full text-sm font-semibold" type="number"
                                            placeholder="e.g., 14200" value="{{ $listing['valuation'] ?? '' }}">
                                    </div>
                                </div>
                            </div>

                            <button class="kt-btn kt-btn-sm kt-btn-mono mt-4">Save Status</button>

                        </div>

                        {{-- ===== ABOUT YOU ===== --}}
                        <div data-tab-pane="about-you" class="hidden space-y-4">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs text-muted-foreground mb-1 block">Name *</label>
                                    <input class="kt-input w-full" type="text" placeholder="Seller/Vendor name"
                                        value="John Reynolds">
                                </div>
                                <div>
                                    <label class="text-xs text-muted-foreground mb-1 block">Email *</label>
                                    <input class="kt-input w-full" type="email" placeholder="Email address"
                                        value="j.reynolds@example.com">
                                </div>
                                <div>
                                    <label class="text-xs text-muted-foreground mb-1 block">Telephone Number *</label>
                                    <input class="kt-input w-full" type="tel" placeholder="Phone number"
                                        value="+44 7911 123456">
                                </div>
                                <div>
                                    <label class="text-xs text-muted-foreground mb-1 block">Mileage *</label>
                                    <input class="kt-input w-full" type="number" placeholder="Mileage"
                                        value="{{ $listing['mileage'] ?? 42000 }}">
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
                            </div>

                            <div>
                                <label class="text-xs text-muted-foreground mb-1 block">If Yes, Where? (Name)</label>
                                <input class="kt-input w-full" type="text"
                                    placeholder="e.g., AutoTrader, eBay Motors, Gumtree">
                            </div>

                            <div>
                                <label class="text-xs text-muted-foreground mb-1 block">Valuation Figure £ *</label>
                                <input class="kt-input w-full text-lg font-semibold" type="number" placeholder="0.00"
                                    value="{{ $listing['valuation'] ?? '' }}">
                            </div>

                            <div>
                                <label class="text-xs text-muted-foreground mb-1 block">Original VRM (Notes)</label>
                                <textarea class="kt-input w-full" rows="3"
                                    placeholder="Any notes about the original VRM or registration history…"></textarea>
                            </div>

                            <button class="kt-btn kt-btn-sm kt-btn-mono">Save Changes</button>

                        </div>

                        {{-- ===== SPECIFICATIONS ===== --}}
                        <div data-tab-pane="specifications" class="hidden space-y-4">

                            <div>
                                <label class="text-xs text-muted-foreground mb-1 block">Vehicle Specifications
                                    (Notes)</label>
                                <textarea class="kt-input w-full" rows="3"
                                    placeholder="e.g., Full electric windows, Climate control, Panoramic roof, Heated seats, etc."></textarea>
                            </div>

                            <div>
                                <label class="text-xs text-muted-foreground mb-1 block">Vehicle Seat Type (Notes)</label>
                                <textarea class="kt-input w-full" rows="3"
                                    placeholder="e.g., Leather, Cloth, Suede, Leather/Cloth combo, etc."></textarea>
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
                                <textarea class="kt-input w-full" rows="2"
                                    placeholder="e.g., EV charging cables included, cable management, etc."></textarea>
                            </div>

                            <div>
                                <label class="text-xs text-muted-foreground mb-1 block">Vehicle Smoking Status *</label>
                                <select class="kt-input w-full">
                                    <option>Unknown</option>
                                    <option>Non-smoking</option>
                                    <option>Smoking environment</option>
                                </select>
                            </div>

                            <button class="kt-btn kt-btn-sm kt-btn-mono">Save Changes</button>

                        </div>

                        {{-- ===== DOCUMENTS ===== --}}
                        <div data-tab-pane="documents" class="hidden space-y-4">

                            <div class="grid grid-cols-1 gap-3">
                                @foreach ([['V5C Logbook', 'v5c-logbook'], ['V5C Owner Document', 'v5c-owner'], ['V5C Print Vehicle Address', 'v5c-address'], ['Proof Of Purchase', 'proof-purchase'], ['Service Records', 'service-records'], ['Manuals', 'manuals']] as [$label, $id])
                                    <div class="card border border-border p-4 rounded-xl">
                                        <div class="flex items-center justify-between mb-3">
                                            <div class="font-semibold text-sm">{{ $label }}</div>
                                        </div>
                                        <div class="space-y-3">
                                            <div>
                                                <label class="text-xs text-muted-foreground block mb-2">Upload
                                                    Document</label>
                                                <div
                                                    class="border-2 border-dashed border-border rounded-lg p-4 text-center cursor-pointer hover:bg-muted/5">
                                                    <i
                                                        class="ki-filled ki-file-up text-xl text-muted-foreground mb-1 block"></i>
                                                    <span class="text-xs text-muted-foreground">Click to upload
                                                        PDF/Image</span>
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

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
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

                            <button class="kt-btn kt-btn-sm kt-btn-mono mt-4">Save Changes</button>

                        </div>

                        <div data-tab-pane="mot" class="hidden space-y-4">

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="card p-4 text-center">
                                    <div class="text-sm text-gray-500">MOT Status</div>
                                    <div class="text-lg font-semibold text-green-600">
                                        {{ $listing['mot_status'] ?? 'PASS' }}
                                    </div>
                                </div>

                                <div class="card p-4 text-center">
                                    <div class="text-sm text-gray-500">Last MOT Date</div>
                                    <div class="font-medium">
                                        {{ $listing['last_mot_date'] ?? 'N/A' }}
                                    </div>
                                </div>

                                <div class="card p-4 text-center">
                                    <div class="text-sm text-gray-500">MOT Expiry</div>
                                    <div class="font-medium">
                                        {{ $listing['mot_expiry'] ?? 'N/A' }}
                                    </div>
                                </div>

                                <div class="card p-4 text-center">
                                    <div class="text-sm text-gray-500">Advisories</div>
                                    <div class="font-medium">
                                        {{ $listing['mot_advisories'] ?? 'All Clear' }}
                                    </div>
                                </div>
                            </div>

                            <div class="card p-5">
                                <h3 class="text-lg font-semibold mb-4">
                                    Service Records, Manuals & Keys
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                                    <div class="border rounded-lg p-4">
                                        <div class="flex items-center justify-between mb-3">
                                            <span class="font-medium">Service Records</span>
                                            <span class="badge badge-success">
                                                {{ $listing['service_records'] ?? false ? 'YES' : 'NO' }}
                                            </span>
                                        </div>

                                        <div class="text-sm text-gray-500">
                                            Service history and supporting documents.
                                        </div>
                                    </div>

                                    <div class="border rounded-lg p-4">
                                        <div class="flex items-center justify-between mb-3">
                                            <span class="font-medium">Manuals</span>
                                            <span class="badge badge-success">
                                                {{ $listing['manuals'] ?? false ? 'YES' : 'NO' }}
                                            </span>
                                        </div>

                                        <div class="text-sm text-gray-500">
                                            Vehicle manuals available.
                                        </div>
                                    </div>

                                    <div class="border rounded-lg p-4">
                                        <div class="flex items-center justify-between mb-3">
                                            <span class="font-medium">Keys</span>
                                            <span class="badge badge-success">
                                                {{ $listing['keys'] ?? false ? 'YES' : 'NO' }}
                                            </span>
                                        </div>

                                        <div class="text-sm text-gray-500">
                                            Vehicle keys supplied.
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>

                        {{-- ===== INTERIOR INSPECTION ===== --}}
                        <div data-tab-pane="interior" class="hidden space-y-4">

                            @foreach (['Front Drivers Side', 'Front Passengers Side', 'Rear Passenger Side', 'Rear Drivers Side', 'Front Seats', 'Rear Seats', 'Dashboard', 'Boot'] as $area)
                                <div class="card border border-border p-4 rounded-xl">
                                    <div class="font-semibold text-sm mb-3">{{ $area }}</div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="text-xs text-muted-foreground block mb-2">Image</label>
                                            <div
                                                class="border-2 border-dashed border-border rounded-lg p-6 text-center cursor-pointer hover:bg-muted/5 transition">
                                                <i
                                                    class="ki-filled ki-add-image text-2xl text-muted-foreground mb-2 block"></i>
                                                <span class="text-xs text-muted-foreground">Click to upload image</span>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="text-xs text-muted-foreground mb-1 block">Notes</label>
                                            <textarea class="kt-input w-full" rows="4" placeholder="Add observations and details…"></textarea>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <button class="kt-btn kt-btn-sm kt-btn-mono">Save All Changes</button>

                        </div>

                        {{-- ===== EXTERIOR INSPECTION ===== --}}
                        <div data-tab-pane="exterior" class="hidden space-y-4">

                            @foreach ([['Front Drivers Wheel', 'Wheel'], ['Front Passengers Wheel', 'Wheel'], ['Rear Drivers Wheel', 'Wheel'], ['Rear Passengers Wheel', 'Wheel'], ['Front Drivers Tyre', 'Tyre'], ['Front Passengers Tyre', 'Tyre'], ['Rear Drivers Tyre', 'Tyre'], ['Rear Passengers Tyre', 'Tyre']] as [$area, $type])
                                <div class="card border border-border p-4 rounded-xl">
                                    <div class="font-semibold text-sm mb-3">{{ $area }} ({{ $type }})
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="text-xs text-muted-foreground block mb-2">Image</label>
                                            <div
                                                class="border-2 border-dashed border-border rounded-lg p-6 text-center cursor-pointer hover:bg-muted/5 transition">
                                                <i
                                                    class="ki-filled ki-add-image text-2xl text-muted-foreground mb-2 block"></i>
                                                <span class="text-xs text-muted-foreground">Click to upload image</span>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="text-xs text-muted-foreground mb-1 block">Notes</label>
                                            <textarea class="kt-input w-full" rows="4" placeholder="Condition, wear, damage, tread depth, etc."></textarea>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <button class="kt-btn kt-btn-sm kt-btn-mono">Save All Changes</button>

                        </div>

                        {{-- ===== DAMAGE & WEAR ===== --}}
                        <div data-tab-pane="damage-wear" class="hidden space-y-4">

                            @foreach (['Surface Marks', 'Panel Damage', 'Exterior Wear & Tear', 'Glass Health', 'Damage/Absent Fixtures', 'Dashboard & Lights'] as $area)
                                <div class="card border border-border p-4 rounded-xl">
                                    <div class="font-semibold text-sm mb-3">{{ $area }}</div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="space-y-3">
                                            <div>
                                                <label class="text-xs text-muted-foreground block mb-2">Diagram
                                                    Image</label>
                                                <div
                                                    class="border-2 border-dashed border-border rounded-lg p-4 text-center cursor-pointer hover:bg-muted/5 transition">
                                                    <i
                                                        class="ki-filled ki-add-image text-xl text-muted-foreground mb-1 block"></i>
                                                    <span class="text-xs text-muted-foreground">Click to upload
                                                        diagram</span>
                                                </div>
                                            </div>
                                            <div>
                                                <label class="text-xs text-muted-foreground block mb-2">Photo</label>
                                                <div
                                                    class="border-2 border-dashed border-border rounded-lg p-4 text-center cursor-pointer hover:bg-muted/5 transition">
                                                    <i
                                                        class="ki-filled ki-camera text-xl text-muted-foreground mb-1 block"></i>
                                                    <span class="text-xs text-muted-foreground">Click to upload
                                                        photo</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="space-y-2">
                                            <div>
                                                <label class="text-xs text-muted-foreground mb-1 block">Size (Text
                                                    Area)</label>
                                                <input class="kt-input w-full text-xs" type="text"
                                                    placeholder="e.g., 10cm, small scratch, 5mm chip">
                                            </div>
                                            <div>
                                                <label class="text-xs text-muted-foreground mb-1 block">Vehicle Side
                                                    (Location)</label>
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
                                                <label class="text-xs text-muted-foreground mb-1 block">Detailed
                                                    Notes</label>
                                                <textarea class="kt-input w-full text-xs" rows="2" placeholder="Detailed description of damage/wear…"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <button class="kt-btn kt-btn-sm kt-btn-mono">Save All Changes</button>

                        </div>

                        {{-- ===== TECHNICAL HEALTH ===== --}}
                        <div data-tab-pane="technical-health" class="hidden space-y-4">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs text-muted-foreground mb-1 block">Mechanical & Electrical Issues?
                                        *</label>
                                    <select class="kt-input w-full">
                                        <option value="no">No</option>
                                        <option value="yes">Yes</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-xs text-muted-foreground mb-1 block">Issue Details (Notes)</label>
                                    <textarea class="kt-input w-full text-xs" rows="2" placeholder="Describe any mechanical or electrical issues…"></textarea>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs text-muted-foreground mb-1 block">Service Records Present?
                                        *</label>
                                    <select class="kt-input w-full">
                                        <option value="no">No</option>
                                        <option value="partial">Yes - Partial</option>
                                        <option value="full">Yes - Full</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-xs text-muted-foreground block mb-2">Upload Service Records
                                        (Photos)</label>
                                    <div
                                        class="border-2 border-dashed border-border rounded-lg p-3 text-center cursor-pointer hover:bg-muted/5">
                                        <i class="ki-filled ki-file-up text-lg text-muted-foreground mb-1 block"></i>
                                        <span class="text-xs text-muted-foreground">Click to upload photos</span>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs text-muted-foreground mb-1 block">Manuals Present? *</label>
                                    <select class="kt-input w-full">
                                        <option value="no">No</option>
                                        <option value="yes">Yes</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-xs text-muted-foreground block mb-2">Upload Manuals (Photos)</label>
                                    <div
                                        class="border-2 border-dashed border-border rounded-lg p-3 text-center cursor-pointer hover:bg-muted/5">
                                        <i class="ki-filled ki-file-up text-lg text-muted-foreground mb-1 block"></i>
                                        <span class="text-xs text-muted-foreground">Click to upload photos</span>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs text-muted-foreground mb-1 block">Keys Present? *</label>
                                    <select class="kt-input w-full">
                                        <option value="no">No</option>
                                        <option value="yes">Yes</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-xs text-muted-foreground block mb-2">Upload Key Photos</label>
                                    <div
                                        class="border-2 border-dashed border-border rounded-lg p-3 text-center cursor-pointer hover:bg-muted/5">
                                        <i class="ki-filled ki-camera text-lg text-muted-foreground mb-1 block"></i>
                                        <span class="text-xs text-muted-foreground">Click to upload photos</span>
                                    </div>
                                </div>
                            </div>

                            <button class="kt-btn kt-btn-sm kt-btn-mono">Save Changes</button>

                        </div>

                        {{-- ===== VALUATIONS ===== --}}
                        <div data-tab-pane="valuations" class="hidden space-y-4">

                            <div class="flex justify-between items-center mb-4">
                                <h3 class="font-semibold">Valuation Comparison - Multiple Sources</h3>
                                <button class="kt-btn kt-btn-xs kt-btn-outline" data-listing-action="pull-valuation">
                                    <i class="ki-filled ki-refresh"></i> Refresh All
                                </button>
                            </div>

                            {{-- Carsmart --}}
                            <div class="card border border-border p-4 rounded-xl">
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

                            {{-- Motorway --}}
                            <div class="card border border-border p-4 rounded-xl">
                                <div class="font-semibold text-sm mb-3">Motorway (1 Type)</div>
                                <div class="bg-muted/5 p-3 rounded-lg">
                                    <div class="text-xs text-muted-foreground">Valuation</div>
                                    <div class="text-xl font-bold">£14,450</div>
                                </div>
                            </div>

                            {{-- HPI --}}
                            <div class="card border border-border p-4 rounded-xl">
                                <div class="font-semibold text-sm mb-3">HPI (11 Types)</div>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 text-xs">
                                    <div class="bg-muted/5 p-2.5 rounded">
                                        <div class="text-muted-foreground mb-1">Private Sale Valuation LOW</div>
                                        <div class="font-semibold text-sm">£19,050</div>
                                    </div>
                                    <div class="bg-muted/5 p-2.5 rounded">
                                        <div class="text-muted-foreground mb-1">Private Sale Valuation HIGH</div>
                                        <div class="font-semibold text-sm">£19,900</div>
                                    </div>
                                    <div class="bg-muted/5 p-2.5 rounded">
                                        <div class="text-muted-foreground mb-1">Forecourt Valuation LOW</div>
                                        <div class="font-semibold text-sm">£19,710</div>
                                    </div>
                                    <div class="bg-muted/5 p-2.5 rounded">
                                        <div class="text-muted-foreground mb-1">Forecourt Valuation HIGH</div>
                                        <div class="font-semibold text-sm">£21,490</div>
                                    </div>
                                    <div class="bg-muted/5 p-2.5 rounded">
                                        <div class="text-muted-foreground mb-1">Trade In Poor LOW</div>
                                        <div class="font-semibold text-sm">£14,260</div>
                                    </div>
                                    <div class="bg-muted/5 p-2.5 rounded">
                                        <div class="text-muted-foreground mb-1">Trade In Poor HIGH</div>
                                        <div class="font-semibold text-sm">£15,600</div>
                                    </div>
                                    <div class="bg-muted/5 p-2.5 rounded">
                                        <div class="text-muted-foreground mb-1">Trade In Good LOW</div>
                                        <div class="font-semibold text-sm">£15,390</div>
                                    </div>
                                    <div class="bg-muted/5 p-2.5 rounded">
                                        <div class="text-muted-foreground mb-1">Trade In Good HIGH</div>
                                        <div class="font-semibold text-sm">£16,830</div>
                                    </div>
                                    <div class="bg-muted/5 p-2.5 rounded">
                                        <div class="text-muted-foreground mb-1">Trade In Excellent LOW</div>
                                        <div class="font-semibold text-sm">£16,470</div>
                                    </div>
                                    <div class="bg-muted/5 p-2.5 rounded">
                                        <div class="text-muted-foreground mb-1">Trade In Excellent HIGH</div>
                                        <div class="font-semibold text-sm">£18,010</div>
                                    </div>
                                    <div class="bg-muted/5 p-2.5 rounded">
                                        <div class="text-muted-foreground mb-1">Average Valuation</div>
                                        <div class="font-semibold text-sm">£17,475</div>
                                    </div>
                                </div>
                            </div>

                            {{-- AutoTrader --}}
                            <div class="card border border-border p-4 rounded-xl">
                                <div class="font-semibold text-sm mb-3">AutoTrader (7 Types)</div>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 text-xs">
                                    <div class="bg-muted/5 p-2.5 rounded">
                                        <div class="text-muted-foreground mb-1">Recommended Selling Price</div>
                                        <div class="font-semibold text-sm">£18,950</div>
                                    </div>
                                    <div class="bg-muted/5 p-2.5 rounded">
                                        <div class="text-muted-foreground mb-1">How much more than Part Exchange</div>
                                        <div class="font-semibold text-sm">+£3,500</div>
                                    </div>
                                    <div class="bg-muted/5 p-2.5 rounded">
                                        <div class="text-muted-foreground mb-1">Current Valuation</div>
                                        <div class="font-semibold text-sm">£18,200</div>
                                    </div>
                                    <div class="bg-muted/5 p-2.5 rounded">
                                        <div class="text-muted-foreground mb-1">Sell Privately LOW</div>
                                        <div class="font-semibold text-sm">£17,900</div>
                                    </div>
                                    <div class="bg-muted/5 p-2.5 rounded">
                                        <div class="text-muted-foreground mb-1">Sell Privately HIGH</div>
                                        <div class="font-semibold text-sm">£19,400</div>
                                    </div>
                                    <div class="bg-muted/5 p-2.5 rounded">
                                        <div class="text-muted-foreground mb-1">Part Exchange LOW</div>
                                        <div class="font-semibold text-sm">£14,500</div>
                                    </div>
                                    <div class="bg-muted/5 p-2.5 rounded">
                                        <div class="text-muted-foreground mb-1">Part Exchange HIGH</div>
                                        <div class="font-semibold text-sm">£15,800</div>
                                    </div>
                                </div>
                            </div>

                            {{-- WebuyAnyCar --}}
                            <div class="card border border-border p-4 rounded-xl">
                                <div class="font-semibold text-sm mb-3">WebuyAnyCar (1 Type)</div>
                                <div class="bg-muted/5 p-3 rounded-lg">
                                    <div class="text-xs text-muted-foreground">Valuation</div>
                                    <div class="text-xl font-bold">£13,800</div>
                                </div>
                            </div>

                            <div class="mt-2 p-3 bg-info/10 border border-info/30 rounded-lg text-sm text-info">
                                <i class="ki-filled ki-information-4 mr-1"></i>
                                All valuations from multiple sources are displayed above.
                            </div>

                        </div>

                        {{-- ===== MEDIA ===== --}}
                        <div data-tab-pane="media" class="hidden space-y-4">

                            <div>
                                <label class="text-xs text-muted-foreground mb-2 block">Video Display/Upload</label>
                                <div
                                    class="border-2 border-dashed border-border rounded-lg p-8 text-center cursor-pointer hover:bg-muted/5">
                                    <i class="ki-filled ki-video text-3xl text-muted-foreground mb-2 block"></i>
                                    <span class="text-sm text-muted-foreground">Click to upload or embed video</span>
                                    <div class="text-xs text-muted-foreground mt-2">Supports MP4, WebM, MOV formats</div>
                                </div>
                            </div>

                            <div>
                                <label class="text-xs text-muted-foreground mb-1 block">Video URL (if embedded)</label>
                                <input class="kt-input w-full text-xs" type="text"
                                    placeholder="e.g., https://youtube.com/watch?v=...">
                            </div>

                            <button class="kt-btn kt-btn-sm kt-btn-mono">Save Video</button>

                        </div>

                        {{-- ===== PRICING ===== --}}
                        <div data-tab-pane="pricing" class="hidden space-y-4">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                @foreach ([['Guide Price', '£' . number_format($listing['guide'] ?? 0)], ['Reserve', $listing['reserve'] ?? null ? '£' . number_format($listing['reserve']) : 'Not set'], ['BIN Price', $listing['bin'] ?? false ? '£' . number_format($listing['bin_price'] ?? 0) : 'Off'], ['Make Offer', $listing['offer_enabled'] ?? false ? 'Enabled' : 'Off']] as [$l, $v])
                                    <div class="card border border-border p-3 rounded-xl">
                                        <div class="text-xs text-muted-foreground">{{ $l }}</div>
                                        <div class="font-medium mt-0.5">{{ $v }}</div>
                                    </div>
                                @endforeach
                            </div>
                            <div
                                class="mt-3 rounded-xl border border-warning/30 bg-warning/10 px-4 py-3 text-sm text-warning-foreground">
                                <i class="ki-filled ki-information-4 mr-1"></i>
                                BIN cannot be active simultaneously with a Reserve price.
                            </div>
                            <div class="flex gap-2 mt-3">
                                <button class="kt-btn kt-btn-sm kt-btn-outline" data-listing-action="apply-pricing">Apply
                                    Valuation to Pricing</button>
                                <button class="kt-btn kt-btn-sm kt-btn-outline" data-listing-action="enable-bin">Toggle
                                    BIN</button>
                            </div>
                        </div>

                        {{-- ===== QA ===== --}}
                        <div data-tab-pane="qa" class="hidden space-y-4">
                            <div class="space-y-2">
                                @foreach ([['Required Photos (6)', 'Incomplete', 'danger'], ['V5C Document', 'Missing', 'danger'], ['MOT Certificate', 'Present', 'success'], ['Pricing Set', 'Complete', 'success'], ['KYC Verified', 'Pending', 'warning'], ['Compliance Confirmed', 'Done', 'success']] as [$item, $status, $badge])
                                    <div
                                        class="card border border-border p-3 flex items-center justify-between text-sm rounded-xl">
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
                                    <div
                                        class="card border border-border p-3 flex items-start justify-between gap-2 rounded-xl">
                                        <div>
                                            <span
                                                class="text-xs font-mono text-muted-foreground bg-muted/30 px-1.5 py-0.5 rounded">{{ $event }}</span>
                                            <div class="mt-1">{{ $msg }}</div>
                                        </div>
                                        <div class="text-xs text-muted-foreground whitespace-nowrap shrink-0">
                                            {{ $time }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- ===== HISTORY ===== --}}
                        <div data-tab-pane="history" class="hidden space-y-4">

                            <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                                <h3 class="font-semibold text-sm">Full History Timeline</h3>
                                <div class="flex gap-1.5 flex-wrap">
                                    <button class="kt-btn kt-btn-xs kt-btn-outline history-filter-btn is-active"
                                        data-history-filter="all">All</button>
                                    <button class="kt-btn kt-btn-xs kt-btn-outline history-filter-btn"
                                        data-history-filter="state">State</button>
                                    <button class="kt-btn kt-btn-xs kt-btn-outline history-filter-btn"
                                        data-history-filter="valuation">Valuations</button>
                                    <button class="kt-btn kt-btn-xs kt-btn-outline history-filter-btn"
                                        data-history-filter="pricing">Pricing</button>
                                    <button class="kt-btn kt-btn-xs kt-btn-outline history-filter-btn"
                                        data-history-filter="qa">QA</button>
                                    <button class="kt-btn kt-btn-xs kt-btn-outline history-filter-btn"
                                        data-history-filter="media">Media/Docs</button>
                                    <button class="kt-btn kt-btn-xs kt-btn-outline history-filter-btn"
                                        data-history-filter="comms">Comms</button>
                                    <button class="kt-btn kt-btn-xs kt-btn-outline history-filter-btn"
                                        data-history-filter="notes">Notes</button>
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
                                    <div class="card border border-border p-3 flex items-start gap-3 rounded-xl history-entry"
                                        data-history-type="{{ $h['type'] }}">
                                        <div class="mt-0.5">
                                            <i class="ki-filled {{ $h['icon'] }} text-muted-foreground"></i>
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="font-medium text-sm">{{ $h['title'] }}</span>
                                                <span
                                                    class="kt-badge {{ $h['badge'] }} text-xs">{{ ucfirst($h['type']) }}</span>
                                            </div>
                                            <div class="text-sm text-muted-foreground mt-0.5">{{ $h['detail'] }}</div>
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
                        <div class="text-xs font-semibold text-muted-foreground uppercase tracking-wide mb-1.5 mt-1">
                            Listing</div>
                        <div class="space-y-1.5 text-xs mb-3">
                            @foreach ([['State', $listing['state']], ['Vehicle', $listing['vehicle']], ['VRM', $listing['vrm'] ?? '—'], ['Mileage', number_format($listing['mileage'] ?? 0) . ' mi'], ['Sale Type', $listing['sale_type'] ?? 'CST1'], ['Owner', $listing['owner']]] as [$k, $v])
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-muted-foreground">{{ $k }}</span>
                                    <span class="font-medium text-right truncate max-w-[60%]">{{ $v }}</span>
                                </div>
                            @endforeach
                        </div>

                        {{-- Compliance --}}
                        <div class="text-xs font-semibold text-muted-foreground uppercase tracking-wide mb-1.5">Compliance
                        </div>
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
                        <div class="text-xs font-semibold text-muted-foreground uppercase tracking-wide mb-1.5">Valuations
                        </div>
                        <div class="space-y-1.5 text-xs mb-3">
                            @foreach ([['Carsmart', '£14,200'], ['Motorway', '£14,450'], ['HPI Average', '£17,475'], ['AutoTrader Current', '£18,200'], ['WeBuyAnyCar', '£13,800']] as [$src, $val])
                                <div class="flex items-center justify-between">
                                    <span class="text-muted-foreground">{{ $src }}</span>
                                    <span class="font-medium">{{ $val }}</span>
                                </div>
                            @endforeach
                            <button class="kt-btn kt-btn-xs kt-btn-outline w-full mt-1"
                                data-listing-action="pull-valuation">
                                View All Valuations
                            </button>
                        </div>

                        {{-- Pricing --}}
                        <div class="text-xs font-semibold text-muted-foreground uppercase tracking-wide mb-1.5">Pricing
                        </div>
                        <div class="space-y-1.5 text-xs mb-3">
                            @foreach ([['Guide Price', '£' . number_format($listing['guide'] ?? 0)], ['Reserve', $listing['reserve'] ?? null ? '£' . number_format($listing['reserve']) : 'Not set'], ['BIN Price', $listing['bin'] ?? false ? '£' . number_format($listing['bin_price'] ?? 0) : 'Off'], ['Make Offer', $listing['offer_enabled'] ?? false ? 'Enabled' : 'Off']] as [$k, $v])
                                <div class="flex items-center justify-between">
                                    <span class="text-muted-foreground">{{ $k }}</span>
                                    <span class="font-medium">{{ $v }}</span>
                                </div>
                            @endforeach
                        </div>

                        {{-- Media / Documents --}}
                        <div class="text-xs font-semibold text-muted-foreground uppercase tracking-wide mb-1.5">Media &amp;
                            Documents</div>
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
                        <div class="text-xs font-semibold text-muted-foreground uppercase tracking-wide mb-1.5">Auction
                        </div>
                        <div class="space-y-1.5 text-xs mb-3">
                            @foreach ([['Auction Code', $listing['auction_code'] ?? '—'], ['Status', $listing['auction_status'] ?? '—'], ['Sniper Protection', 'Active (5 min)']] as [$k, $v])
                                <div class="flex items-center justify-between">
                                    <span class="text-muted-foreground">{{ $k }}</span>
                                    <span class="font-medium">{{ $v }}</span>
                                </div>
                            @endforeach
                        </div>

                        {{-- Last Activity --}}
                        <div class="text-xs font-semibold text-muted-foreground uppercase tracking-wide mb-1.5">Last
                            Activity</div>
                        <div class="card border border-border p-2 rounded-lg text-xs">
                            <div class="font-medium">State change: QA → Published</div>
                            <div class="text-muted-foreground mt-0.5">Published by JR · 2026-06-05 14:22</div>
                        </div>
                        <button class="kt-btn kt-btn-xs kt-btn-outline w-full mt-1.5"
                            onclick="document.querySelector('.detail-tab[data-tab=\'history\']')?.click()">
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

            {{-- ===== PULL VALUATION MODAL (Read-only display of all sources) ===== --}}
            <div id="pull-valuation-modal" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/50 p-4">
                <div class="bg-background border border-border rounded-xl w-full max-w-3xl max-h-[90vh] overflow-y-auto">

                    <div
                        class="flex items-center justify-between p-4 border-b border-border sticky top-0 bg-background z-10">
                        <div>
                            <h3 class="font-semibold text-base">Latest Valuations</h3>
                            <p class="text-xs text-muted-foreground mt-0.5">All sources · {{ $listing['vrm'] ?? 'VRM' }} ·
                                {{ $listing['vehicle'] ?? '' }}</p>
                        </div>
                        <button class="kt-btn kt-btn-sm kt-btn-icon kt-btn-outline"
                            data-modal-close="pull-valuation-modal">
                            <i class="ki-filled ki-cross"></i>
                        </button>
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

                    <div
                        class="flex items-center justify-between gap-2 p-4 border-t border-border sticky bottom-0 bg-background">
                        <p class="text-xs text-muted-foreground">
                            <i class="ki-filled ki-information-4 mr-1"></i>
                            Values shown are the most recently fetched from each provider.
                        </p>
                        <div class="flex gap-2 shrink-0">
                            <button class="kt-btn kt-btn-sm kt-btn-outline" data-listing-action="add-valuation">
                                <i class="ki-filled ki-add-item"></i> Edit / Add Values
                            </button>
                            <button class="kt-btn kt-btn-sm kt-btn-outline"
                                data-modal-close="pull-valuation-modal">Close</button>
                        </div>
                    </div>

                </div>
            </div>

            {{-- ===== ADD VALUATION MODAL (Editable inputs) ===== --}}
            <div id="add-valuation-modal" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/50 p-4">
                <div class="bg-background border border-border rounded-xl w-full max-w-3xl max-h-[85vh] overflow-y-auto">

                    <div
                        class="flex items-center justify-between p-4 border-b border-border sticky top-0 bg-background z-10">
                        <h3 class="font-semibold text-base">Add / Edit Valuation</h3>
                        <button class="kt-btn kt-btn-sm kt-btn-icon kt-btn-outline"
                            data-modal-close="add-valuation-modal">
                            <i class="ki-filled ki-cross"></i>
                        </button>
                    </div>

                    <div class="p-4 space-y-5">

                        {{-- Carsmart --}}
                        <div>
                            <div class="font-semibold text-sm mb-2">Carsmart</div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="text-xs text-muted-foreground mb-1 block">Carsmart Valuation £</label>
                                    <input class="kt-input w-full text-sm" type="number" name="carsmart_valuation"
                                        placeholder="e.g., 14200">
                                </div>
                                <div>
                                    <label class="text-xs text-muted-foreground mb-1 block">Carsmart Quick Sale Valuation
                                        £</label>
                                    <input class="kt-input w-full text-sm" type="number"
                                        name="carsmart_quick_sale_valuation" placeholder="e.g., 13500">
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
                                    <input class="kt-input w-full text-sm" type="number" name="motorway_valuation"
                                        placeholder="e.g., 14450">
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-border"></div>

                        {{-- HPI --}}
                        <div>
                            <div class="font-semibold text-sm mb-2">HPI</div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                <div>
                                    <label class="text-xs text-muted-foreground mb-1 block">Private Sale Valuation LOW
                                        £</label>
                                    <input class="kt-input w-full text-sm" type="number" name="hpi_private_sale_low"
                                        placeholder="19050">
                                </div>
                                <div>
                                    <label class="text-xs text-muted-foreground mb-1 block">Private Sale Valuation HIGH
                                        £</label>
                                    <input class="kt-input w-full text-sm" type="number" name="hpi_private_sale_high"
                                        placeholder="19900">
                                </div>
                                <div>
                                    <label class="text-xs text-muted-foreground mb-1 block">Forecourt Valuation LOW
                                        £</label>
                                    <input class="kt-input w-full text-sm" type="number" name="hpi_forecourt_low"
                                        placeholder="19710">
                                </div>
                                <div>
                                    <label class="text-xs text-muted-foreground mb-1 block">Forecourt Valuation HIGH
                                        £</label>
                                    <input class="kt-input w-full text-sm" type="number" name="hpi_forecourt_high"
                                        placeholder="21490">
                                </div>
                                <div>
                                    <label class="text-xs text-muted-foreground mb-1 block">Trade In Valuation Poor LOW
                                        £</label>
                                    <input class="kt-input w-full text-sm" type="number" name="hpi_trade_in_poor_low"
                                        placeholder="14260">
                                </div>
                                <div>
                                    <label class="text-xs text-muted-foreground mb-1 block">Trade In Valuation Poor HIGH
                                        £</label>
                                    <input class="kt-input w-full text-sm" type="number" name="hpi_trade_in_poor_high"
                                        placeholder="15600">
                                </div>
                                <div>
                                    <label class="text-xs text-muted-foreground mb-1 block">Trade In Valuation Good LOW
                                        £</label>
                                    <input class="kt-input w-full text-sm" type="number" name="hpi_trade_in_good_low"
                                        placeholder="15390">
                                </div>
                                <div>
                                    <label class="text-xs text-muted-foreground mb-1 block">Trade In Valuation Good HIGH
                                        £</label>
                                    <input class="kt-input w-full text-sm" type="number" name="hpi_trade_in_good_high"
                                        placeholder="16830">
                                </div>
                                <div>
                                    <label class="text-xs text-muted-foreground mb-1 block">Trade In Valuation Excellent
                                        LOW £</label>
                                    <input class="kt-input w-full text-sm" type="number"
                                        name="hpi_trade_in_excellent_low" placeholder="16470">
                                </div>
                                <div>
                                    <label class="text-xs text-muted-foreground mb-1 block">Trade In Valuation Excellent
                                        HIGH £</label>
                                    <input class="kt-input w-full text-sm" type="number"
                                        name="hpi_trade_in_excellent_high" placeholder="18010">
                                </div>
                                <div>
                                    <label class="text-xs text-muted-foreground mb-1 block">Average Valuation £</label>
                                    <input class="kt-input w-full text-sm" type="number" name="hpi_average_valuation"
                                        placeholder="19475">
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-border"></div>

                        {{-- AutoTrader --}}
                        <div>
                            <div class="font-semibold text-sm mb-2">AutoTrader</div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                <div>
                                    <label class="text-xs text-muted-foreground mb-1 block">Recommended Selling Price
                                        £</label>
                                    <input class="kt-input w-full text-sm" type="number"
                                        name="autotrader_recommended_price" placeholder="18950">
                                </div>
                                <div>
                                    <label class="text-xs text-muted-foreground mb-1 block">More Than Part Exchange
                                        £</label>
                                    <input class="kt-input w-full text-sm" type="number" name="autotrader_more_than_pex"
                                        placeholder="3500">
                                </div>
                                <div>
                                    <label class="text-xs text-muted-foreground mb-1 block">Current Valuation £</label>
                                    <input class="kt-input w-full text-sm" type="number"
                                        name="autotrader_current_valuation" placeholder="18200">
                                </div>
                                <div>
                                    <label class="text-xs text-muted-foreground mb-1 block">Sell Privately LOW £</label>
                                    <input class="kt-input w-full text-sm" type="number"
                                        name="autotrader_sell_privately_low" placeholder="17900">
                                </div>
                                <div>
                                    <label class="text-xs text-muted-foreground mb-1 block">Sell Privately HIGH £</label>
                                    <input class="kt-input w-full text-sm" type="number"
                                        name="autotrader_sell_privately_high" placeholder="19400">
                                </div>
                                <div>
                                    <label class="text-xs text-muted-foreground mb-1 block">Part Exchange LOW £</label>
                                    <input class="kt-input w-full text-sm" type="number"
                                        name="autotrader_part_exchange_low" placeholder="14500">
                                </div>
                                <div>
                                    <label class="text-xs text-muted-foreground mb-1 block">Part Exchange HIGH £</label>
                                    <input class="kt-input w-full text-sm" type="number"
                                        name="autotrader_part_exchange_high" placeholder="15800">
                                </div>
                                <div>
                                    <label class="text-xs text-muted-foreground mb-1 block">Sell to Dealer LOW £</label>
                                    <input class="kt-input w-full text-sm" type="number"
                                        name="autotrader_sell_to_dealer_low" placeholder="14000">
                                </div>
                                <div>
                                    <label class="text-xs text-muted-foreground mb-1 block">Sell to Dealer HIGH £</label>
                                    <input class="kt-input w-full text-sm" type="number"
                                        name="autotrader_sell_to_dealer_high" placeholder="15500">
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
                                    <input class="kt-input w-full text-sm" type="number" name="webuyanycar_valuation"
                                        placeholder="e.g., 13800">
                                </div>
                            </div>
                        </div>

                    </div>

                    <div
                        class="flex items-center justify-end gap-2 p-4 border-t border-border sticky bottom-0 bg-background">
                        <button class="kt-btn kt-btn-sm kt-btn-outline"
                            data-modal-close="add-valuation-modal">Cancel</button>
                        <button class="kt-btn kt-btn-sm kt-btn-mono" id="save-add-valuation-btn">
                            <i class="ki-filled ki-save-2"></i> Save Valuations
                        </button>
                    </div>

                </div>
            </div>

            <script>
                (function() {

                    const container = document.currentScript.closest('.kt-container-fixed') || document;

                    // ===== TAB SWITCHING =====
                    const panes = container.querySelectorAll('[data-tab-pane]');
                    const tabs = container.querySelectorAll('.detail-tab');

                    function activateTab(target) {
                        panes.forEach(pane => pane.classList.add('hidden'));
                        tabs.forEach(tab => {
                            tab.classList.remove('bg-background', 'border', 'border-b-0', 'border-border',
                                'text-foreground');
                            tab.classList.add('text-muted-foreground');
                        });
                        container.querySelector(`[data-tab-pane="${target}"]`)?.classList.remove('hidden');
                        const activeTab = container.querySelector(`.detail-tab[data-tab="${target}"]`);
                        activeTab?.classList.add('bg-background', 'border', 'border-b-0', 'border-border', 'text-foreground');
                        activeTab?.classList.remove('text-muted-foreground');
                    }

                    tabs.forEach(tab => tab.addEventListener('click', () => activateTab(tab.dataset.tab)));
                    activateTab('status');

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
                    ['pull-valuation-modal', 'add-valuation-modal'].forEach(id => {
                        document.getElementById(id)?.addEventListener('click', e => {
                            if (e.target.id === id) closeModal(id);
                        });
                    });

                    // ===== PULL VALUATION → opens pull-valuation-modal (read-only display) =====
                    document.querySelectorAll('[data-listing-action="pull-valuation"]').forEach(btn => {
                        btn.addEventListener('click', () => openModal('pull-valuation-modal'));
                    });

                    // ===== ADD VALUATION → opens add-valuation-modal (editable inputs) =====
                    document.querySelectorAll('[data-listing-action="add-valuation"]').forEach(btn => {
                        btn.addEventListener('click', () => openModal('add-valuation-modal'));
                    });

                    // "Edit / Add Values" button inside pull-valuation-modal switches to add modal
                    document.querySelector('#pull-valuation-modal [data-listing-action="add-valuation"]')?.addEventListener(
                        'click', () => {
                            closeModal('pull-valuation-modal');
                            openModal('add-valuation-modal');
                        });

                    // ===== APPLY PRICING =====
                    document.querySelectorAll('[data-listing-action="apply-pricing"]').forEach(btn => {
                        btn.addEventListener('click', () => {
                            const modal = document.getElementById('apply-pricing-modal');
                            modal?.classList.remove('hidden');
                            modal?.classList.add('flex');
                        });
                    });

                    // ===== SAVE ADD VALUATION =====
                    document.querySelector('#save-add-valuation-btn')?.addEventListener('click', () => {
                        const modal = document.getElementById('add-valuation-modal');
                        const data = {};
                        modal?.querySelectorAll('input[name]').forEach(input => {
                            data[input.name] = input.value ? Number(input.value) : null;
                        });
                        /*
                        Replace with real API call:
                        await fetch(`/listings/{id}/valuations`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify(data)
                        });
                        */
                        console.log('Valuation data:', data);
                        closeModal('add-valuation-modal');
                    });

                    // ===== VIEW VALUATION HISTORY (sidebar link) =====
                    document.querySelectorAll('[data-listing-action="view-valuation-history"]').forEach(btn => {
                        btn.addEventListener('click', () => activateTab('valuations'));
                    });

                    // ===== REMOVE VALUATION ROWS =====
                    document.querySelectorAll('[data-listing-action="remove-valuation"]').forEach(btn => {
                        btn.addEventListener('click', () => {
                            if (!confirm('Remove this valuation record?')) return;
                            btn.closest('tr')?.remove();
                            const tbody = btn.closest('tbody');
                            if (tbody && tbody.querySelectorAll('tr').length === 0) {
                                tbody.innerHTML =
                                    `<tr><td colspan="9" class="p-6 text-center text-muted-foreground text-sm">No valuations recorded yet.</td></tr>`;
                            }
                        });
                    });

                    // ===== HISTORY FILTERS =====
                    const historyFilterBtns = document.querySelectorAll('.history-filter-btn');
                    const historyEntries = document.querySelectorAll('.history-entry');

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

                })();
            </script>

        </div>

    @endsection
