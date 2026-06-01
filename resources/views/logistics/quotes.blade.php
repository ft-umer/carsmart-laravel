{{-- resources/views/logistics/quotes.blade.php --}}
{{-- Phase 4 — L1: Logistics → Quotes --}}
@extends('layouts.app')
@section('title', 'Transport Quotes — Logistics')

@section('content')

<div class="kt-container-fixed">
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
    <h1 class="text-xl font-semibold text-foreground">Transport Quotes</h1>
    <span class="text-xs text-muted-foreground">Get smart-drop quotes for collection &amp; delivery</span>
</div>

<div class="grid grid-cols-1 lg:grid-cols-[420px,1fr] gap-5">

    {{-- Request form --}}
    <div class="card border border-border rounded-xl p-5 space-y-4 h-fit">
        <h3 class="text-sm font-semibold flex items-center gap-2">
            <i data-lucide="map-pin" class="w-4 h-4 opacity-60"></i> New quote request
        </h3>

        <div class="space-y-3">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium mb-1">
                        Pickup postcode <span class="text-destructive">*</span>
                    </label>
                    <input id="q-pickup" class="kt-input w-full" placeholder="SW1A 1AA" />
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1">
                        Drop postcode <span class="text-destructive">*</span>
                    </label>
                    <input id="q-drop" class="kt-input w-full" placeholder="M1 1AE" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium mb-1">Preferred date</label>
                    <input id="q-date" type="date" class="kt-input w-full" />
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1">Window</label>
                    <select id="q-window" class="kt-input w-full">
                        <option value="AM">AM (08:00–12:00)</option>
                        <option value="PM">PM (12:00–18:00)</option>
                        <option value="Any">Any time</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium mb-1">Vehicle size</label>
                <select id="q-vehicle-size" class="kt-input w-full">
                    <option value="small">Small (hatchback / city car)</option>
                    <option value="medium">Medium (saloon / estate)</option>
                    <option value="large">Large (SUV / van)</option>
                    <option value="oversized">Oversized / specialist</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium mb-1">Deal ref (optional)</label>
                <input id="q-deal-ref" class="kt-input w-full" placeholder="DEL-3112" />
            </div>

            <div>
                <label class="block text-xs font-medium mb-1">Notes</label>
                <textarea id="q-notes" class="kt-input w-full" rows="2"
                          placeholder="Special requirements, access restrictions…"></textarea>
            </div>

            <button id="btn-get-quotes" class="kt-btn kt-btn-mono w-full">
                <i data-lucide="search" class="w-4 h-4 mr-1"></i>Get quotes
            </button>
        </div>
    </div>

    {{-- Results --}}
    <div>
        <div id="quotes-loading" class="hidden card border border-border rounded-xl p-8 text-center">
            <div class="animate-spin w-8 h-8 border-2 border-primary border-t-transparent rounded-full mx-auto mb-3"></div>
            <p class="text-sm text-muted-foreground">Contacting transport providers…</p>
        </div>

        <div id="quotes-empty" class="card border border-border rounded-xl p-8 text-center text-sm text-muted-foreground">
            <i data-lucide="truck" class="w-10 h-10 mx-auto mb-3 opacity-20"></i>
            <p>Fill in the form and click <strong>Get quotes</strong> to see available providers.</p>
        </div>

        <div id="quotes-results" class="hidden space-y-3"></div>
    </div>
</div>

{{-- Quote history --}}
@if (!empty($quoteHistory))
    <div class="mt-6">
        <h3 class="text-sm font-semibold mb-3">Recent quote requests</h3>
        <div class="card border border-border rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-muted/40">
                        <tr>
                            @foreach(['Date','Route','Deal','Window','Selected provider','Quote','Actions'] as $col)
                                <th class="p-3 text-left font-semibold text-muted-foreground uppercase tracking-wide">{{ $col }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border bg-background">
                        @foreach ($quoteHistory as $qh)
                            <tr class="hover:bg-muted/20 transition-colors">
                                <td class="p-3 text-muted-foreground">{{ $qh['requested_at'] ?? '—' }}</td>
                                <td class="p-3">{{ $qh['pickup'] ?? '—' }} → {{ $qh['drop'] ?? '—' }}</td>
                                <td class="p-3 font-mono">{{ $qh['deal_ref'] ?? '—' }}</td>
                                <td class="p-3">{{ $qh['window'] ?? '—' }}</td>
                                <td class="p-3 font-medium">{{ $qh['selected_provider'] ?? 'None selected' }}</td>
                                <td class="p-3 font-semibold">{{ $qh['quote'] ? '£'.number_format($qh['quote']) : '—' }}</td>
                                <td class="p-3">
                                    @if ($qh['selected_provider'] ?? null)
                                        <span class="kt-badge kt-badge-success kt-badge-sm">Selected</span>
                                    @else
                                        <button data-action="rerun-quote" data-id="{{ $qh['id'] }}"
                                                class="kt-btn kt-btn-ghost kt-btn-sm">Re-run</button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif

<div id="toast-container" class="fixed bottom-5 right-5 z-[200] flex flex-col gap-2 pointer-events-none"></div>
@include('partials._phase4_js')
</div>

<script>
(function () {
    const { toast, auditEvent } = window.CS4;

    /* Simulated providers */
    const PROVIDERS = [
        { name: 'AutoTransport Pro', sla: '1–2 business days', earliest: 'Tomorrow AM', quote: 89,  rating: 4.8, simulated: false },
        { name: 'SwiftCar Logistics', sla: '2–3 business days', earliest: 'Thu PM',       quote: 74,  rating: 4.5, simulated: false },
        { name: 'National Vehicle Move', sla: '3–5 business days', earliest: 'Fri Any',   quote: 62,  rating: 4.2, simulated: false },
        { name: 'Internal rate card',   sla: 'Estimate only',     earliest: 'Flexible',   quote: 70,  rating: null, simulated: true  },
    ];

    document.getElementById('btn-get-quotes')?.addEventListener('click', () => {
        const pickup = document.getElementById('q-pickup')?.value.trim();
        const drop   = document.getElementById('q-drop')?.value.trim();
        if (!pickup || !drop) { toast('Pickup and drop postcodes required.', 'warning'); return; }

        document.getElementById('quotes-empty').classList.add('hidden');
        document.getElementById('quotes-results').classList.add('hidden');
        document.getElementById('quotes-loading').classList.remove('hidden');
        auditEvent('logistics_quote_requested', { pickup, drop });

        setTimeout(() => {
            document.getElementById('quotes-loading').classList.add('hidden');
            const resultsDiv = document.getElementById('quotes-results');
            resultsDiv.classList.remove('hidden');
            resultsDiv.innerHTML = PROVIDERS.map((p, i) => `
                <div class="card border ${i === 0 ? 'border-primary/40 bg-primary/5' : 'border-border'} rounded-xl p-4
                            flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-lg bg-muted flex items-center justify-center shrink-0">
                            <i data-lucide="truck" class="w-5 h-5 text-muted-foreground"></i>
                        </div>
                        <div>
                            <div class="text-sm font-semibold flex items-center gap-2">
                                ${p.name}
                                ${p.simulated ? '<span class="kt-badge kt-badge-outline kt-badge-sm">Simulated</span>' : ''}
                                ${i === 0 ? '<span class="kt-badge kt-badge-primary kt-badge-sm">Best match</span>' : ''}
                            </div>
                            <div class="text-xs text-muted-foreground mt-0.5">
                                SLA: ${p.sla} · Earliest: <strong>${p.earliest}</strong>
                                ${p.rating ? ' · ★ ' + p.rating : ''}
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 sm:shrink-0">
                        <div class="text-right">
                            <div class="text-xl font-bold">£${p.quote}</div>
                            <div class="text-xs text-muted-foreground">inc. VAT</div>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="window.CS4.auditEvent('logistics_quote_selected',{provider:'${p.name}',quote:${p.quote}});window.CS4.toast('${p.name} selected — job created.','success')"
                                    class="kt-btn ${i === 0 ? 'kt-btn-mono' : 'kt-btn-outline'} kt-btn-sm">
                                Select
                            </button>
                            <button onclick="window.CS4.toast('Messaging ${p.name}…','info')"
                                    class="kt-btn kt-btn-ghost kt-btn-sm">Message</button>
                        </div>
                    </div>
                </div>`).join('');
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }, 1800);
    });
})();
</script>

@endsection
