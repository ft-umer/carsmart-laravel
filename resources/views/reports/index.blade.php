{{-- resources/views/reports/index.blade.php --}}
{{-- Phase 5 — R0: Reports → Overview --}}
@extends('layouts.app')
@section('title', 'Reports & Analytics — Carsmart')

@section('content')

@include('partials._retention_banner')

<div class="kt-container-fixed">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
        <div>
            <h1 class="text-xl font-semibold text-foreground">Reports & Analytics</h1>
            <p class="text-sm text-muted-foreground mt-0.5">Management visibility and scheduled insights</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('reports.custom') }}" class="kt-btn kt-btn-outline kt-btn-sm">
                <i data-lucide="sliders" class="w-4 h-4 mr-1"></i> Custom report
            </a>
            <button class="kt-btn kt-btn-mono kt-btn-sm" id="btn-schedule-report">
                <i data-lucide="calendar" class="w-4 h-4 mr-1"></i> Schedule email
            </button>
        </div>
    </div>

    {{-- Date range filter --}}
    <form method="GET" action="{{ route('reports.index') }}" class="card border border-border rounded-lg p-3 mb-6">
        <div class="flex flex-wrap gap-2 items-end">
            <div class="min-w-[160px]">
                <label class="block text-xs text-muted-foreground mb-1">Date range</label>
                <select name="range" class="kt-input w-full" onchange="this.form.submit()">
                    <option value="7d"  @selected(($range ?? '30d') === '7d')>Last 7 days</option>
                    <option value="30d" @selected(($range ?? '30d') === '30d')>Last 30 days</option>
                    <option value="90d" @selected(($range ?? '30d') === '90d')>Last 90 days</option>
                    <option value="ytd" @selected(($range ?? '30d') === 'ytd')>Year to date</option>
                    <option value="custom">Custom range…</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="button" class="kt-btn kt-btn-ghost kt-btn-sm" id="btn-export-all">
                    <i data-lucide="download" class="w-4 h-4 mr-1"></i> Export all
                </button>
            </div>
        </div>
    </form>

    {{-- ── Valuation widgets (Phase 5 additions) ── --}}
    <div class="mb-6">
        <h2 class="text-sm font-semibold text-muted-foreground uppercase tracking-wide mb-3">Valuations</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- Valuation Coverage --}}
            <a href="{{ route('reports.show', 'valuation-coverage') }}"
               class="kt-card hover:border-primary/40 transition-colors">
                <div class="kt-card-content p-5">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-semibold text-foreground">Valuation coverage</span>
                        <span class="flex items-center justify-center size-9 rounded-lg bg-primary/10">
                            <i data-lucide="percent" class="w-4 h-4 text-primary"></i>
                        </span>
                    </div>
                    <div class="flex items-end gap-3 mb-2">
                        <span class="text-3xl font-bold text-mono">{{ $valuationCoverage ?? '73' }}%</span>
                        <span class="text-sm text-success mb-1">+4% vs prev period</span>
                    </div>
                    <div class="w-full bg-muted rounded-full h-2 mb-3">
                        <div class="bg-primary rounded-full h-2" style="width:{{ $valuationCoverage ?? 73 }}%"></div>
                    </div>
                    <div class="grid grid-cols-3 gap-2 text-center text-xs">
                        <div class="bg-muted/50 rounded-lg p-2">
                            <div class="font-bold text-foreground">{{ $covStats['with_valuation'] ?? 146 }}</div>
                            <div class="text-muted-foreground">With valuation</div>
                        </div>
                        <div class="bg-muted/50 rounded-lg p-2">
                            <div class="font-bold text-foreground">{{ $covStats['without'] ?? 54 }}</div>
                            <div class="text-muted-foreground">Without</div>
                        </div>
                        <div class="bg-muted/50 rounded-lg p-2">
                            <div class="font-bold text-foreground">{{ $covStats['failures'] ?? 8 }}</div>
                            <div class="text-muted-foreground text-destructive">Failures</div>
                        </div>
                    </div>
                </div>
            </a>

            {{-- Valuation Delta --}}
            <a href="{{ route('reports.show', 'valuation-delta') }}"
               class="kt-card hover:border-primary/40 transition-colors">
                <div class="kt-card-content p-5">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-semibold text-foreground">Valuation delta</span>
                        <span class="flex items-center justify-center size-9 rounded-lg bg-warning/10">
                            <i data-lucide="bar-chart-2" class="w-4 h-4 text-warning"></i>
                        </span>
                    </div>
                    <div class="flex items-end gap-3 mb-3">
                        <span class="text-3xl font-bold text-mono">£{{ number_format(($deltaStats['median'] ?? 850) / 100, 0) }}</span>
                        <span class="text-sm text-muted-foreground mb-1">median |Δ|</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2 text-center text-xs">
                        <div class="bg-muted/50 rounded-lg p-2">
                            <div class="font-bold text-foreground">£{{ number_format(($deltaStats['p90'] ?? 2400) / 100, 0) }}</div>
                            <div class="text-muted-foreground">90th pctile</div>
                        </div>
                        <div class="bg-muted/50 rounded-lg p-2">
                            <div class="font-bold text-foreground">{{ $deltaStats['applied_pct'] ?? 62 }}%</div>
                            <div class="text-muted-foreground">Applied</div>
                        </div>
                        <div class="bg-muted/50 rounded-lg p-2">
                            <div class="font-bold text-foreground">{{ $deltaStats['not_applied_pct'] ?? 38 }}%</div>
                            <div class="text-muted-foreground">Not applied</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- ── Standard report cards ── --}}
    <h2 class="text-sm font-semibold text-muted-foreground uppercase tracking-wide mb-3">Standard reports</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

        @foreach([
            ['listings-funnel',    'Listings funnel',       'Created → QA → Ready → Published → Live → Closed', 'funnel',    'primary'],
            ['auction-performance','Auction performance',   'Lots, bidders, reserve-met %, avg uplift',          'gavel',     'warning'],
            ['lead-conversion',    'Lead conversion',       'First response time, qualified %, conversion',      'user-check','success'],
            ['vendor-participation','Vendor participation', 'Invited, accepted, active bidders, wins',           'store',     'info'],
            ['revenue-fees',       'Revenue & fees',        'Subscription, transaction fees, credits, net',      'dollar-sign','success'],
            ['wallet-payouts',     'Wallet & payouts',      'Balances, holds, payout approval times',            'wallet',    'primary'],
            ['logistics-sla',      'Logistics SLA',         'Quote to schedule, on-time pickups/deliveries',     'truck',     'warning'],
            ['disputes-sla',       'Disputes SLA',          'Ack within 24h, decision within 5 days, outcomes', 'shield',    'destructive'],
            ['comms-metrics',      'Communications metrics','Volume by channel, delivery/read/response rates',   'mail',      'info'],
        ] as [$slug, $title, $desc, $icon, $colour])
            <a href="{{ route('reports.show', $slug) }}"
               class="kt-card hover:border-{{ $colour }}/40 transition-colors group">
                <div class="kt-card-content p-5">
                    <div class="flex items-start gap-3">
                        <span class="flex items-center justify-center size-10 rounded-lg bg-{{ $colour }}/10 shrink-0 group-hover:bg-{{ $colour }}/20 transition-colors">
                            <i data-lucide="{{ $icon }}" class="w-5 h-5 text-{{ $colour }}"></i>
                        </span>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-semibold text-foreground mb-1">{{ $title }}</div>
                            <div class="text-xs text-muted-foreground">{{ $desc }}</div>
                        </div>
                        <i data-lucide="arrow-right" class="w-4 h-4 text-muted-foreground group-hover:text-{{ $colour }} transition-colors shrink-0 mt-0.5"></i>
                    </div>
                </div>
            </a>
        @endforeach

    </div>

</div>

{{-- Schedule report modal --}}
<div id="modal-schedule-report" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-background rounded-xl shadow-xl w-full max-w-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold">Schedule email report</h2>
            <button class="sched-close text-muted-foreground hover:text-foreground">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="space-y-3">
            <div>
                <label class="block text-xs text-muted-foreground mb-1">Report</label>
                <select class="kt-input w-full">
                    <option>All reports summary</option>
                    <option>Listings funnel</option>
                    <option>Auction performance</option>
                    <option>Valuation coverage</option>
                    <option>Revenue & fees</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-muted-foreground mb-1">Frequency</label>
                <select class="kt-input w-full">
                    <option>Daily</option>
                    <option>Weekly</option>
                    <option>Monthly</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-muted-foreground mb-1">Recipients</label>
                <input type="text" class="kt-input w-full" placeholder="Email addresses, comma-separated" />
            </div>
            <div>
                <label class="block text-xs text-muted-foreground mb-1">Format</label>
                <div class="flex gap-3">
                    <label class="flex items-center gap-1.5 text-sm cursor-pointer">
                        <input type="radio" name="report-fmt" value="csv" class="kt-radio" checked /> CSV
                    </label>
                    <label class="flex items-center gap-1.5 text-sm cursor-pointer">
                        <input type="radio" name="report-fmt" value="pdf" class="kt-radio" /> PDF
                    </label>
                </div>
            </div>
        </div>
        <div class="flex justify-end gap-2 mt-5">
            <button class="sched-close kt-btn kt-btn-ghost">Cancel</button>
            <button class="kt-btn kt-btn-mono">Save schedule</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('btn-schedule-report')?.addEventListener('click', () => {
    document.getElementById('modal-schedule-report').classList.remove('hidden');
    document.getElementById('modal-schedule-report').classList.add('flex');
});
document.querySelectorAll('.sched-close').forEach(b => b.addEventListener('click', () => {
    document.getElementById('modal-schedule-report').classList.add('hidden');
    document.getElementById('modal-schedule-report').classList.remove('flex');
}));
</script>
@endpush

@endsection
