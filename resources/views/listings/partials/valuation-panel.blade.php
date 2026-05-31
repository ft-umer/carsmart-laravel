<div id="view-valuations" class="space-y-4">

    <!-- ACTION BAR -->
    <div class="card border border-border">

        <div class="p-4 flex flex-wrap gap-2">

            <button
                class="kt-btn kt-btn-mono"
                data-action="add-valuation">

                Add Valuation

            </button>

            <button
                class="kt-btn kt-btn-outline"
                data-action="pull-latest-valuation">

                Pull Latest Valuation

            </button>

            <button
                class="kt-btn kt-btn-outline"
                data-action="apply-pricing">

                Apply To Pricing

            </button>

        </div>

    </div>

    <!-- FETCH STATUS -->
    <div class="card border border-border">

        <div class="p-3 flex items-center justify-between">

            <div>

                <div class="font-medium">
                    Valuation Fetch Status
                </div>

                <div class="text-sm text-muted-foreground">

                    @if($valuationStatus === 'fetching')
                        Fetching latest valuation...
                    @elseif($valuationStatus === 'success')
                        Latest valuation received successfully.
                    @elseif($valuationStatus === 'failed')
                        Provider unavailable or vehicle not found.
                    @else
                        Ready
                    @endif

                </div>

            </div>

            @if($valuationStatus === 'fetching')

                <span class="kt-badge kt-badge-warning">
                    In Progress
                </span>

            @elseif($valuationStatus === 'success')

                <span class="kt-badge kt-badge-success">
                    Succeeded
                </span>

            @elseif($valuationStatus === 'failed')

                <div class="flex gap-2">

                    <span class="kt-badge kt-badge-danger">
                        Failed
                    </span>

                    <button class="kt-btn kt-btn-sm kt-btn-outline">
                        Retry
                    </button>

                </div>

            @endif

        </div>

    </div>

    <!-- RECOMMENDATION PANEL -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        <div class="card border border-border p-4">

            <div class="text-xs text-muted-foreground">
                Recommended Guide Price
            </div>

            <div class="text-2xl font-semibold">
                £{{ number_format($recommendedGuide ?? 0) }}
            </div>

        </div>

        <div class="card border border-border p-4">

            <div class="text-xs text-muted-foreground">
                Recommended Reserve Band
            </div>

            <div class="text-xl font-semibold">
                £{{ number_format($reserveLow ?? 0) }}
                -
                £{{ number_format($reserveHigh ?? 0) }}
            </div>

        </div>

        <div class="card border border-border p-4 flex items-center">

            <button
                class="kt-btn kt-btn-mono w-full"
                data-action="apply-to-listing">

                Apply To Listing

            </button>

        </div>

    </div>

    <!-- VALUATIONS TABLE -->
    <div class="card border border-border overflow-hidden">

        <div class="p-4 border-b border-border">

            <h3 class="font-semibold">
                Valuation History
            </h3>

        </div>

        <div class="overflow-auto">

            <table class="w-full min-w-[1200px] text-sm">

                <thead class="bg-muted/40">

                    <tr>

                        <th class="p-3 text-left">Date</th>
                        <th class="p-3 text-left">Source</th>
                        <th class="p-3 text-left">Valuer</th>
                        <th class="p-3 text-right">Amount</th>
                        <th class="p-3 text-right">Δ vs Guide</th>
                        <th class="p-3 text-left">Notes</th>
                        <th class="p-3 text-left">Comps</th>
                        <th class="p-3 text-center">Used</th>
                        <th class="p-3 text-right">Actions</th>

                    </tr>

                </thead>

                <tbody>

                    @foreach($valuations as $valuation)

                        <tr class="border-t border-border">

                            <td class="p-3">
                                {{ $valuation['date'] }}
                            </td>

                            <td class="p-3">
                                {{ $valuation['source'] }}
                            </td>

                            <td class="p-3">
                                {{ $valuation['valuer'] ?? 'System' }}
                            </td>

                            <td class="p-3 text-right font-medium">
                                £{{ number_format($valuation['amount']) }}
                            </td>

                            <td class="p-3 text-right">

                                {{ $valuation['delta'] ?? '—' }}

                            </td>

                            <td class="p-3">

                                {{ $valuation['notes'] ?? '—' }}

                            </td>

                            <td class="p-3">

                                {{ $valuation['comps'] ?? '—' }}

                            </td>

                            <td class="p-3 text-center">

                                @if($valuation['used'] ?? false)
                                    ✔
                                @endif

                            </td>

                            <td class="p-3 text-right">

                                <div class="flex justify-end gap-2">

                                    <button
                                        class="kt-btn kt-btn-xs kt-btn-outline">
                                        Apply
                                    </button>

                                    <button
                                        class="kt-btn kt-btn-xs kt-btn-ghost">
                                        Remove
                                    </button>

                                </div>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    </div>

</div>