{{-- resources/views/deals/partials/_commercials_tab.blade.php --}}
<div class="card border border-border rounded-xl overflow-hidden">
    <div class="px-4 py-3 border-b border-border bg-muted/20">
        <h3 class="text-sm font-semibold">Commercial breakdown</h3>
    </div>
    <div class="p-4 space-y-0 divide-y divide-border text-sm">
        @php
            $rows = [
                ['Agreed price',    '£'.number_format($deal['price'] ?? 0)],
                ['Source',          $deal['source'] ?? '—'],
                ['Reserve price',   isset($deal['reserve']) ? '£'.number_format($deal['reserve']) : '—'],
                ['BIN / Offer',     isset($deal['bin_price']) ? '£'.number_format($deal['bin_price']) : '—'],
                ['Platform fee',    isset($deal['platform_fee']) ? '£'.number_format($deal['platform_fee']) : '—'],
                ['Buyer premium',   isset($deal['buyer_premium']) ? '£'.number_format($deal['buyer_premium']) : '—'],
                ['Tax',             isset($deal['tax']) ? '£'.number_format($deal['tax']) : '—'],
                ['Adjustments',     isset($deal['adjustments']) ? '£'.number_format($deal['adjustments']) : '—'],
            ];
        @endphp
        @foreach ($rows as [$label, $value])
            <div class="flex justify-between py-2.5">
                <span class="text-muted-foreground text-xs">{{ $label }}</span>
                <span class="font-medium">{{ $value }}</span>
            </div>
        @endforeach
        <div class="flex justify-between py-2.5 font-semibold text-base">
            <span>Net payout (est.)</span>
            <span>£{{ number_format(($deal['price'] ?? 0) - ($deal['platform_fee'] ?? 0)) }}</span>
        </div>
    </div>
</div>

@if (!empty($deal['price_adjustments']))
    <div class="card border border-border rounded-xl overflow-hidden">
        <div class="px-4 py-3 border-b border-border bg-muted/20">
            <h3 class="text-sm font-semibold">Price adjustment log</h3>
        </div>
        <table class="w-full text-xs">
            <thead class="bg-muted/40">
                <tr>
                    <th class="p-3 text-left font-medium text-muted-foreground">Date</th>
                    <th class="p-3 text-left font-medium text-muted-foreground">From</th>
                    <th class="p-3 text-left font-medium text-muted-foreground">To</th>
                    <th class="p-3 text-left font-medium text-muted-foreground">Reason</th>
                    <th class="p-3 text-left font-medium text-muted-foreground">By</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border bg-background">
                @foreach ($deal['price_adjustments'] as $adj)
                    <tr>
                        <td class="p-3">{{ $adj['date'] ?? '—' }}</td>
                        <td class="p-3">£{{ number_format($adj['from'] ?? 0) }}</td>
                        <td class="p-3 font-semibold">£{{ number_format($adj['to'] ?? 0) }}</td>
                        <td class="p-3">{{ $adj['reason'] ?? '—' }}</td>
                        <td class="p-3 text-muted-foreground">{{ $adj['by'] ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
