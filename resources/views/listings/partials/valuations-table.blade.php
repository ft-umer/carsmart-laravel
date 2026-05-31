<div>

    <div class="flex items-center justify-between mb-4">

        <div>

            <h2 class="text-lg font-semibold">
                Valuations
            </h2>

            <div class="text-sm text-muted-foreground">
                Internal and external valuation history
            </div>

        </div>

        <div class="flex gap-2">

            <button
                class="kt-btn kt-btn-mono">
                Add Valuation
            </button>

            <button
                class="kt-btn kt-btn-outline">
                Pull Latest Valuation
            </button>

            <button
                class="kt-btn kt-btn-outline">
                Apply To Pricing
            </button>

        </div>

    </div>

    <div class="card overflow-auto">

        <table class="w-full text-sm">

            <thead>

            <tr>

                <th class="p-3">Date</th>
                <th class="p-3">Listing</th>
                <th class="p-3">Source</th>
                <th class="p-3">Valuer</th>
                <th class="p-3">Amount</th>
                <th class="p-3">Delta</th>
                <th class="p-3">Notes</th>
                <th class="p-3">Comps</th>
                <th class="p-3">Used</th>
                <th class="p-3">Actions</th>

            </tr>

            </thead>

          <tbody>
@php
$valuations = [
    [
        'date' => '2026-05-30',
        'listing' => 'BMW 320d M Sport',
        'source' => 'Carsmart',
        'valuer' => 'System',
        'amount' => 14000,
        'delta' => '+2.5%',
        'notes' => 'Strong market demand',
        'comps' => 12,
        'used' => true,
    ],
    [
        'date' => '2026-05-28',
        'listing' => 'BMW 320d M Sport',
        'source' => 'HPI',
        'valuer' => 'HPI Feed',
        'amount' => 15000,
        'delta' => '+9.8%',
        'notes' => 'Above guide price',
        'comps' => 15,
        'used' => false,
    ],
    [
        'date' => '2026-05-25',
        'listing' => 'BMW 320d M Sport',
        'source' => 'CAP',
        'valuer' => 'CAP Expert',
        'amount' => 14250,
        'delta' => '+4.3%',
        'notes' => 'Recommended valuation',
        'comps' => 10,
        'used' => true,
    ],
];
@endphp

@forelse($valuations as $valuation)
    <tr class="border-b">

        <td class="p-3">
            {{ $valuation['date'] }}
        </td>

        <td class="p-3">
            {{ $valuation['listing'] }}
        </td>

        <td class="p-3">
            {{ $valuation['source'] }}
        </td>

        <td class="p-3">
            {{ $valuation['valuer'] }}
        </td>

        <td class="p-3 font-medium">
            £{{ number_format($valuation['amount']) }}
        </td>

        <td class="p-3">
            <span class="{{ str_contains($valuation['delta'], '+') ? 'text-green-600' : 'text-red-600' }}">
                {{ $valuation['delta'] }}
            </span>
        </td>

        <td class="p-3">
            {{ $valuation['notes'] }}
        </td>

        <td class="p-3">
            {{ $valuation['comps'] }}
        </td>

        <td class="p-3">
            @if($valuation['used'])
                <span class="kt-badge kt-badge-success">Yes</span>
            @else
                <span class="kt-badge kt-badge-outline">No</span>
            @endif
        </td>

        <td class="p-3">
            <div class="flex gap-2">

                <button class="kt-btn kt-btn-sm kt-btn-outline">
                    View
                </button>

                <button class="kt-btn kt-btn-sm kt-btn-mono">
                    Apply
                </button>

            </div>
        </td>

    </tr>
@empty
    <tr>
        <td colspan="10" class="p-6 text-center text-muted-foreground">
            No valuations found.
        </td>
    </tr>
@endforelse

</tbody>

        </table>

    </div>

    <div class="card mt-4 p-4">

        <h3 class="font-semibold mb-2">
            Pricing Recommendation
        </h3>

        <div class="grid md:grid-cols-3 gap-4">

            <div>

                <div class="text-xs text-muted-foreground">
                    Guide Price
                </div>

                <div class="font-semibold">
                    £14,250
                </div>

            </div>

            <div>

                <div class="text-xs text-muted-foreground">
                    Reserve Band
                </div>

                <div class="font-semibold">
                    £13,500 - £14,000
                </div>

            </div>

            <div>

                <button class="kt-btn kt-btn-mono">
                    Apply To Listing
                </button>

            </div>

        </div>

    </div>

</div>