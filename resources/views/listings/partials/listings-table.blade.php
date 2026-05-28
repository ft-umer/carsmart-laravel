<div id="view-listings" class="space-y-3">

    <!-- BULK ACTION BAR (Phase 1 required foundation) -->
    <div id="bulk-bar"
         class="hidden flex items-center justify-between p-3 card border border-border bg-muted/10">

        <div class="text-sm">
            <span id="selected-count">0</span> selected
        </div>

        <div class="flex gap-2">

            <button class="kt-btn kt-btn-sm kt-btn-outline">
                Pull Valuations
            </button>

            <button class="kt-btn kt-btn-sm kt-btn-outline">
                Mark QA
            </button>

            <button class="kt-btn kt-btn-sm kt-btn-outline">
                Assign Owner
            </button>

            <button class="kt-btn kt-btn-sm kt-btn-mono">
                Bulk Actions
            </button>

        </div>
    </div>

    <!-- TABLE -->
    <div class="card rounded-lg border border-border overflow-hidden">

        <div class="overflow-auto">

            <table class="w-full min-w-[1200px] text-sm">

                <thead class="bg-muted/40 sticky top-0 z-10">
                    <tr>
                        <th class="p-3 w-10"></th>

                        <th class="p-3 text-left">Listing</th>
                        <th class="p-3 text-left">Vehicle</th>
                        <th class="p-3 text-left">VRM</th>
                        <th class="p-3 text-left">Mileage</th>

                        <!-- Phase 1 CORE -->
                        <th class="p-3 text-right">Valuation</th>
                        <th class="p-3 text-right">Δ</th>
                        <th class="p-3 text-left">Source</th>

                        <th class="p-3 text-left">Reserve</th>
                        <th class="p-3 text-left">BIN</th>
                        <th class="p-3 text-left">QA</th>
                        <th class="p-3 text-left">State</th>
                        <th class="p-3 text-left">Owner</th>

                        <th class="p-3 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-border">

                    @forelse($listings as $listing)

                        <tr class="hover:bg-muted/5 transition"
                            data-listing-id="{{ $listing['id'] }}"
                            data-state="{{ $listing['state'] }}"
                            data-owner="{{ $listing['owner'] }}"
                            data-qa="{{ $listing['qa'] }}">

                            <!-- checkbox -->
                            <td class="p-3">
                                <input type="checkbox"
                                       class="row-check kt-checkbox"
                                       data-id="{{ $listing['id'] }}">
                            </td>

                            <!-- listing -->
                            <td class="p-3 font-medium">
                                {{ $listing['id'] }}
                            </td>

                            <!-- vehicle -->
                            <td class="p-3">
                                {{ $listing['vehicle'] }}
                            </td>

                            <!-- VRM -->
                            <td class="p-3 text-muted-foreground">
                                {{ $listing['vrm'] }}
                            </td>

                            <!-- mileage -->
                            <td class="p-3">
                                {{ number_format($listing['mileage']) }}
                            </td>

                            <!-- VALUATION (Phase 1 CORE) -->
                            <td class="p-3 text-right">
                                <div class="font-semibold">
                                    £{{ number_format($listing['valuation'] ?? 0) }}
                                </div>
                                <div class="text-xs text-muted-foreground">
                                    {{ $listing['valuation_source'] ?? '—' }}
                                </div>
                            </td>

                            <!-- DELTA -->
                            <td class="p-3 text-right">
                                <span class="text-green-600 text-xs">
                                    +£{{ $listing['delta'] ?? 0 }}
                                </span>
                            </td>

                            <!-- SOURCE -->
                            <td class="p-3 text-xs text-muted-foreground">
                                {{ $listing['valuation_source'] ?? 'Manual' }}
                            </td>

                            <!-- RESERVE -->
                            <td class="p-3">
                                <span class="{{ $listing['reserve'] ? 'text-green-600' : 'text-red-500' }}">
                                    {{ $listing['reserve'] ? 'SET' : 'NONE' }}
                                </span>
                            </td>

                            <!-- BIN -->
                            <td class="p-3">
                                <span class="badge {{ $listing['bin'] ? 'bg-green-500/20 text-green-600' : 'bg-muted text-muted-foreground' }}">
                                    {{ $listing['bin'] ? 'ON' : 'OFF' }}
                                </span>
                            </td>

                            <!-- QA -->
                            <td class="p-3">
                                <span class="badge
                                    @if($listing['qa'] === 'Passed') bg-green-500/20 text-green-600
                                    @elseif($listing['qa'] === 'Failed') bg-red-500/20 text-red-500
                                    @else bg-yellow-500/20 text-yellow-600 @endif">

                                    {{ $listing['qa'] }}
                                </span>
                            </td>

                            <!-- STATE -->
                            <td class="p-3">
                                <span class="badge bg-muted text-foreground">
                                    {{ $listing['state'] }}
                                </span>
                            </td>

                            <!-- OWNER -->
                            <td class="p-3">
                                {{ $listing['owner'] }}
                            </td>

                            <!-- ACTIONS -->
                            <td class="p-3">

                                <div class="flex gap-2 justify-end">

                                    <button class="kt-btn kt-btn-sm kt-btn-ghost open-detail"
                                            data-id="{{ $listing['id'] }}">
                                        Open
                                    </button>

                                    <button class="kt-btn kt-btn-sm kt-btn-outline quick-view"
                                            data-id="{{ $listing['id'] }}">
                                        Quick
                                    </button>

                                    <!-- Phase 1 CORE ACTION -->
                                    <button class="kt-btn kt-btn-sm kt-btn-outline"
                                            data-action="pull-valuation"
                                            data-id="{{ $listing['id'] }}">
                                        Pull
                                    </button>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="14" class="p-6 text-center text-muted-foreground">
                                No listings found
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>