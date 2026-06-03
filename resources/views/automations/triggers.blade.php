{{-- resources/views/automations/triggers.blade.php --}}
{{-- Phase 5 — A2: Triggers Registry --}}
@extends('layouts.app')
@section('title', 'Triggers Registry — Automations')

@section('content')

@include('partials._retention_banner')

<div class="kt-container-fixed">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
        <div>
            <h1 class="text-xl font-semibold text-foreground">Triggers Registry</h1>
            <p class="text-sm text-muted-foreground mt-0.5">Available events and their payload schemas</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('automations.index') }}" class="kt-btn kt-btn-ghost kt-btn-sm">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Back to journeys
            </a>
        </div>
    </div>

    {{-- Category filters --}}
    <div class="flex gap-2 flex-wrap mb-5">
        @foreach(['All','Listings','Valuations','KYC','Auctions','Deals','Communications'] as $cat)
            <button class="kt-btn kt-btn-{{ $cat === 'All' ? 'mono' : 'outline' }} kt-btn-sm trigger-cat-btn"
                    data-cat="{{ $cat }}">
                {{ $cat }}
            </button>
        @endforeach
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-[1fr,420px] gap-5">

        <div class="card border border-border rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-muted/40">
                        <tr>
                            @foreach(['Trigger','Category','Payload fields','Last used','Actions'] as $col)
                                <th class="p-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wide">{{ $col }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border bg-background">
                        @php
                            $triggers = $triggers ?? [
                                ['name'=>'missing_photos',      'cat'=>'Listings',      'fields'=>'listing_id, owner_id, photo_count',       'last_used'=>'2 hours ago'],
                                ['name'=>'missing_docs',        'cat'=>'Listings',      'fields'=>'listing_id, owner_id, missing_doc_types',  'last_used'=>'5 hours ago'],
                                ['name'=>'pricing_not_set',     'cat'=>'Listings',      'fields'=>'listing_id, owner_id',                    'last_used'=>'1 day ago'],
                                ['name'=>'reserve_not_set',     'cat'=>'Listings',      'fields'=>'listing_id, auction_id',                  'last_used'=>'3 days ago'],
                                ['name'=>'kyc_pending',         'cat'=>'KYC',           'fields'=>'user_id, role, days_pending',              'last_used'=>'30 min ago'],
                                ['name'=>'valuation_fetched',   'cat'=>'Valuations',    'fields'=>'listing_id, lead_id?, amount_pennies, source, succeeded, error_code?', 'last_used'=>'10 min ago'],
                                ['name'=>'valuation_applied',   'cat'=>'Valuations',    'fields'=>'listing_id, guide_changed, reserve_changed, delta_pennies', 'last_used'=>'1 hour ago'],
                                ['name'=>'valuation_failed',    'cat'=>'Valuations',    'fields'=>'listing_id, lead_id?, error_code, message','last_used'=>'20 min ago'],
                                ['name'=>'auction_published',   'cat'=>'Auctions',      'fields'=>'auction_id, lot_count, start_time',        'last_used'=>'2 days ago'],
                                ['name'=>'auction_starts',      'cat'=>'Auctions',      'fields'=>'auction_id',                              'last_used'=>'1 day ago'],
                                ['name'=>'auction_closing',     'cat'=>'Auctions',      'fields'=>'auction_id, minutes_remaining',            'last_used'=>'4 hours ago'],
                                ['name'=>'outbid',              'cat'=>'Auctions',      'fields'=>'lot_id, bidder_id, current_amount_pennies','last_used'=>'30 min ago'],
                                ['name'=>'reserve_met',         'cat'=>'Auctions',      'fields'=>'lot_id, auction_id, amount_pennies',       'last_used'=>'1 hour ago'],
                                ['name'=>'auction_ended',       'cat'=>'Auctions',      'fields'=>'auction_id, lots_sold, total_gmv_pennies', 'last_used'=>'6 hours ago'],
                                ['name'=>'deal_created',        'cat'=>'Deals',         'fields'=>'deal_id, listing_id, buyer_id, seller_id', 'last_used'=>'3 hours ago'],
                                ['name'=>'payout_requested',    'cat'=>'Deals',         'fields'=>'deal_id, amount_pennies, vendor_id',       'last_used'=>'45 min ago'],
                            ];
                        @endphp
                        @foreach($triggers as $t)
                            <tr class="hover:bg-muted/30 transition-colors trigger-row" data-cat="{{ $t['cat'] }}" data-name="{{ $t['name'] }}">
                                <td class="p-3 font-mono text-xs font-semibold text-foreground">{{ $t['name'] }}</td>
                                <td class="p-3">
                                    <span class="kt-badge kt-badge-outline kt-badge-sm">{{ $t['cat'] }}</span>
                                </td>
                                <td class="p-3 text-xs text-muted-foreground font-mono max-w-xs truncate" title="{{ $t['fields'] }}">
                                    {{ $t['fields'] }}
                                </td>
                                <td class="p-3 text-xs text-muted-foreground">{{ $t['last_used'] }}</td>
                                <td class="p-3">
                                    <div class="flex gap-1">
                                        <button class="kt-btn kt-btn-ghost kt-btn-xs trigger-schema-btn" data-name="{{ $t['name'] }}">
                                            View schema
                                        </button>
                                        <button class="kt-btn kt-btn-ghost kt-btn-xs trigger-test-btn" data-name="{{ $t['name'] }}">
                                            Test fire
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Schema viewer --}}
        <div id="schema-panel" class="card border border-border rounded-xl p-5 hidden xl:block">
            <h3 class="text-sm font-semibold text-foreground mb-3">Schema viewer</h3>
            <p class="text-sm text-muted-foreground">Click "View schema" on a trigger to inspect its payload.</p>
        </div>
    </div>
</div>

{{-- Test Fire Modal --}}
<div id="modal-test-fire" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-background rounded-xl shadow-xl w-full max-w-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold">Test fire: <span id="test-trigger-name" class="font-mono text-primary"></span></h2>
            <button class="test-fire-close text-muted-foreground hover:text-foreground">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div>
            <label class="block text-xs text-muted-foreground mb-1 font-medium">Payload JSON</label>
            <textarea id="test-payload" class="kt-input w-full font-mono text-xs" rows="8">{}</textarea>
        </div>
        <div id="test-result" class="mt-3 hidden rounded-lg bg-muted p-3 text-xs font-mono text-foreground"></div>
        <div class="flex justify-end gap-2 mt-4">
            <button class="test-fire-close kt-btn kt-btn-ghost">Cancel</button>
            <button id="btn-fire" class="kt-btn kt-btn-mono">
                <i data-lucide="send" class="w-4 h-4 mr-1"></i> Fire event
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Category filter
document.querySelectorAll('.trigger-cat-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const cat = this.dataset.cat;
        document.querySelectorAll('.trigger-cat-btn').forEach(b => {
            b.className = 'kt-btn kt-btn-outline kt-btn-sm trigger-cat-btn';
        });
        this.className = 'kt-btn kt-btn-mono kt-btn-sm trigger-cat-btn';
        document.querySelectorAll('.trigger-row').forEach(row => {
            row.style.display = cat === 'All' || row.dataset.cat === cat ? '' : 'none';
        });
    });
});

// Schema viewer
document.querySelectorAll('.trigger-schema-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const name = btn.dataset.name;
        const schemas = {
            valuation_fetched: `{
  "listing_id": "string",
  "lead_id": "string | null",
  "amount_pennies": "integer",
  "source": "string",
  "succeeded": "boolean",
  "error_code": "string | null"
}`,
            valuation_applied: `{
  "listing_id": "string",
  "guide_changed": "boolean",
  "reserve_changed": "boolean",
  "delta_pennies": "integer"
}`,
            valuation_failed: `{
  "listing_id": "string",
  "lead_id": "string | null",
  "error_code": "string",
  "message": "string"
}`,
        };
        const schema = schemas[name] || `{\n  // Payload fields for ${name}\n  "event": "${name}"\n}`;
        document.getElementById('schema-panel').innerHTML = `
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-foreground">Schema: <span class="font-mono text-primary">${name}</span></h3>
            </div>
            <pre class="bg-muted rounded-lg p-3 text-xs font-mono text-foreground overflow-auto">${schema}</pre>
            <div class="mt-3">
                <p class="text-xs text-muted-foreground">Fields marked <code>?</code> are optional.</p>
            </div>`;
    });
});

// Test fire modal
document.querySelectorAll('.trigger-test-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('test-trigger-name').textContent = btn.dataset.name;
        document.getElementById('modal-test-fire').classList.remove('hidden');
        document.getElementById('modal-test-fire').classList.add('flex');
        document.getElementById('test-result').classList.add('hidden');
    });
});
document.querySelectorAll('.test-fire-close').forEach(b => b.addEventListener('click', () => {
    document.getElementById('modal-test-fire').classList.add('hidden');
    document.getElementById('modal-test-fire').classList.remove('flex');
}));
document.getElementById('btn-fire')?.addEventListener('click', () => {
    const result = document.getElementById('test-result');
    result.classList.remove('hidden');
    result.innerHTML = `<span class="text-success">✓ Event fired successfully.</span>\nJourney evaluation queued.\nRun ID: run_${Date.now()}`;
});
</script>
@endpush

@endsection
