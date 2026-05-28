<div>
    <div class="p-4 border-b flex justify-between">

        <div>
            <h2 class="text-lg font-semibold">
                Listing {{ $listing['ref'] }}
            </h2>

            <div class="text-xs text-muted-foreground">
                {{ $listing['state'] }} · Owner {{ $listing['owner'] }}
            </div>
        </div>

        <button class="close-modal">✕</button>
    </div>

    <div class="p-4 grid grid-cols-2 gap-4">

        <div class="card border p-3 rounded-lg">
            <div class="text-xs text-muted-foreground">Latest Valuation</div>
            <div class="text-xl font-semibold">
                £{{ number_format($listing['valuation']) }}
            </div>
        </div>

        <div class="card border p-3 rounded-lg">
            <div class="text-xs text-muted-foreground">Delta vs Guide</div>

            @php
                $delta = $listing['valuation'] - $listing['guide'];
            @endphp

            <div class="text-xl font-semibold {{ $delta >= 0 ? 'text-green-600' : 'text-red-500' }}">
                {{ $delta >= 0 ? '+' : '' }}£{{ number_format($delta) }}
            </div>
        </div>

    </div>

    <div class="p-4 border-t text-sm">
        Vehicle: {{ $listing['vehicle'] }} <br>
        VRM: {{ $listing['vrm'] }} <br>
        Reserve: £{{ number_format($listing['reserve']) }} <br>
        BIN: £{{ number_format($listing['bin']) }}
    </div>

    <div class="p-4 border-t">
        <h3 class="font-semibold mb-2">Valuations</h3>

        <table class="w-full text-sm">
            @foreach($valuations as $v)
                <tr class="border-t">
                    <td>{{ $v['source'] }}</td>
                    <td>£{{ number_format($v['amount']) }}</td>
                    <td class="text-muted-foreground">{{ $v['time'] }}</td>
                </tr>
            @endforeach
        </table>
    </div>
</div>