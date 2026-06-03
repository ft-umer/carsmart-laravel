{{-- resources/views/reports/custom.blade.php --}}
{{-- Phase 5 — R2: Custom Report Builder --}}
@extends('layouts.app')
@section('title', 'Custom Report Builder — Carsmart')

@section('content')

@include('partials._retention_banner')

<div class="kt-container-fixed">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
        <div class="flex items-center gap-3">
            <a href="{{ route('reports.index') }}" class="text-muted-foreground hover:text-foreground">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
            </a>
            <div>
                <h1 class="text-xl font-semibold text-foreground">Custom Report Builder</h1>
                <p class="text-sm text-muted-foreground mt-0.5">Ad-hoc analysis with field selection and grouping</p>
            </div>
        </div>
        @if(count($savedReports ?? []) > 0)
            <div class="flex gap-2">
                <select class="kt-input text-sm" id="load-saved-report">
                    <option value="">Load saved report…</option>
                    @foreach($savedReports ?? [] as $saved)
                        <option value="{{ $saved['id'] }}">{{ $saved['name'] }}</option>
                    @endforeach
                </select>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-[320px,1fr] gap-5">

        {{-- ── Builder Panel ── --}}
        <div class="space-y-4">

            {{-- Dataset --}}
            <div class="card border border-border rounded-xl p-4">
                <label class="block text-xs text-muted-foreground mb-2 font-semibold uppercase tracking-wide">Dataset</label>
                <select class="kt-input w-full" id="dataset-select">
                    <option value="">Choose dataset…</option>
                    @foreach(['Listings','Auctions','Leads','Vendors','Deals','Payments','Logistics','Disputes','Communications'] as $ds)
                        <option value="{{ strtolower($ds) }}">{{ $ds }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Fields --}}
            <div class="card border border-border rounded-xl p-4">
                <label class="block text-xs text-muted-foreground mb-2 font-semibold uppercase tracking-wide">Fields</label>
                <div id="fields-container" class="space-y-1.5 text-sm text-muted-foreground">
                    <p class="text-xs italic">Select a dataset first.</p>
                </div>
            </div>

            {{-- Group by --}}
            <div class="card border border-border rounded-xl p-4">
                <label class="block text-xs text-muted-foreground mb-2 font-semibold uppercase tracking-wide">Group by</label>
                <div id="groupby-container" class="space-y-1.5 text-sm text-muted-foreground">
                    <p class="text-xs italic">Select fields first.</p>
                </div>
            </div>

            {{-- Filters builder --}}
            <div class="card border border-border rounded-xl p-4">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs text-muted-foreground font-semibold uppercase tracking-wide">Filters</label>
                    <button id="add-filter" class="kt-btn kt-btn-ghost kt-btn-xs">
                        <i data-lucide="plus" class="w-3 h-3 mr-1"></i> Add
                    </button>
                </div>
                <div id="filters-container" class="space-y-2"></div>
            </div>

            {{-- Date range --}}
            <div class="card border border-border rounded-xl p-4">
                <label class="block text-xs text-muted-foreground mb-2 font-semibold uppercase tracking-wide">Date range</label>
                <select class="kt-input w-full mb-2">
                    <option value="30d">Last 30 days</option>
                    <option value="7d">Last 7 days</option>
                    <option value="90d">Last 90 days</option>
                    <option value="ytd">Year to date</option>
                    <option value="custom">Custom…</option>
                </select>
                <div id="custom-dates" class="hidden grid-cols-2 gap-2">
                    <input type="date" class="kt-input w-full" />
                    <input type="date" class="kt-input w-full" />
                </div>
            </div>

            <button id="btn-run-report" class="kt-btn kt-btn-mono w-full">
                <i data-lucide="play" class="w-4 h-4 mr-2"></i> Run report
            </button>
        </div>

        {{-- ── Results Panel ── --}}
        <div class="space-y-4">

            {{-- Empty state --}}
            <div id="results-empty" class="card border border-border rounded-xl p-16 text-center">
                <i data-lucide="bar-chart-2" class="w-12 h-12 mx-auto mb-3 text-muted-foreground/30"></i>
                <p class="text-sm font-medium text-foreground">Configure your report</p>
                <p class="text-xs text-muted-foreground mt-1">Select a dataset, choose fields, and click Run.</p>
            </div>

            {{-- Results (shown after run) --}}
            <div id="results-content" class="hidden space-y-4">

                {{-- Result actions --}}
                <div class="flex items-center justify-between">
                    <span class="text-sm text-muted-foreground" id="results-count">0 rows</span>
                    <div class="flex gap-2">
                        <button class="kt-btn kt-btn-ghost kt-btn-sm" id="btn-save-report">
                            <i data-lucide="bookmark" class="w-4 h-4 mr-1"></i> Save report
                        </button>
                        <button class="kt-btn kt-btn-ghost kt-btn-sm" id="btn-export-csv-custom">
                            <i data-lucide="download" class="w-4 h-4 mr-1"></i> Export CSV
                        </button>
                        <button class="kt-btn kt-btn-ghost kt-btn-sm" id="btn-schedule-custom">
                            <i data-lucide="calendar" class="w-4 h-4 mr-1"></i> Schedule email
                        </button>
                    </div>
                </div>

                {{-- Chart area --}}
                <div class="card border border-border rounded-xl p-4">
                    <div id="results-chart" class="h-40 flex items-end gap-1">
                        {{-- Rendered by JS --}}
                    </div>
                </div>

                {{-- Table --}}
                <div class="card border border-border rounded-xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm" id="results-table">
                            <thead class="bg-muted/40">
                                <tr id="results-thead"></tr>
                            </thead>
                            <tbody id="results-tbody" class="divide-y divide-border bg-background"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Save report modal --}}
<div id="modal-save-report" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-background rounded-xl shadow-xl w-full max-w-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold">Save report</h2>
            <button class="save-report-close text-muted-foreground hover:text-foreground">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="space-y-3">
            <div>
                <label class="block text-xs text-muted-foreground mb-1 font-medium">Report name</label>
                <input type="text" class="kt-input w-full" placeholder="My custom report…" />
            </div>
            <div>
                <label class="flex items-center gap-2 text-sm cursor-pointer">
                    <input type="checkbox" class="kt-checkbox" />
                    Share with team
                </label>
            </div>
        </div>
        <div class="flex justify-end gap-2 mt-5">
            <button class="save-report-close kt-btn kt-btn-ghost">Cancel</button>
            <button class="kt-btn kt-btn-mono">Save</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
const datasetFields = {
    listings: ['id','title','vrm','status','cst','vlt','owner','created_at','guide_price','reserve_price','latest_valuation','valuation_source','valuation_delta'],
    auctions: ['id','title','lots','bidders','reserve_met_pct','avg_uplift','start_time','end_time'],
    leads: ['id','name','source','status','first_response_h','qualified','converted','owner'],
    vendors: ['id','company','kyb_state','card_on_file','active_bidder','wins'],
    deals: ['id','listing','buyer','seller','state','amount','fees','created_at'],
    payments: ['id','type','amount','vendor','status','created_at'],
    logistics: ['id','deal','provider','quote_to_schedule_h','status','created_at'],
    disputes: ['id','deal','reason','state','ack_hours','decision_days','outcome'],
    communications: ['id','channel','journey','recipient','status','sent_at'],
};

const datasetSelect = document.getElementById('dataset-select');
const fieldsContainer = document.getElementById('fields-container');
const groupbyContainer = document.getElementById('groupby-container');

datasetSelect?.addEventListener('change', function() {
    const fields = datasetFields[this.value] || [];
    fieldsContainer.innerHTML = fields.length
        ? fields.map(f => `
            <label class="flex items-center gap-2 cursor-pointer py-0.5">
                <input type="checkbox" class="kt-checkbox field-cb" value="${f}" />
                <span class="text-sm text-foreground font-mono">${f}</span>
            </label>`).join('')
        : '<p class="text-xs italic text-muted-foreground">No fields available.</p>';

    groupbyContainer.innerHTML = fields.length
        ? fields.map(f => `
            <label class="flex items-center gap-2 cursor-pointer py-0.5">
                <input type="checkbox" class="kt-checkbox groupby-cb" value="${f}" />
                <span class="text-sm text-foreground font-mono">${f}</span>
            </label>`).join('')
        : '<p class="text-xs italic text-muted-foreground">Select fields first.</p>';
});

// Add filter
let filterCount = 0;
document.getElementById('add-filter')?.addEventListener('click', () => {
    filterCount++;
    const fc = document.getElementById('filters-container');
    const fields = datasetFields[datasetSelect.value] || ['field'];
    fc.innerHTML += `
        <div class="flex gap-1 items-center" id="filter-${filterCount}">
            <select class="kt-input text-xs flex-1">
                ${fields.map(f => `<option>${f}</option>`).join('')}
            </select>
            <select class="kt-input text-xs w-16">
                <option>=</option><option>!=</option><option>&gt;</option><option>&lt;</option><option>contains</option>
            </select>
            <input type="text" class="kt-input text-xs flex-1" placeholder="Value" />
            <button onclick="document.getElementById('filter-${filterCount}').remove()" class="text-muted-foreground hover:text-destructive p-1">
                <i data-lucide="x" class="w-3 h-3"></i>
            </button>
        </div>`;
    lucide.createIcons();
});

// Run report
document.getElementById('btn-run-report')?.addEventListener('click', () => {
    const selectedFields = [...document.querySelectorAll('.field-cb:checked')].map(cb => cb.value);
    if(!datasetSelect.value) { alert('Please select a dataset.'); return; }

    document.getElementById('results-empty').classList.add('hidden');
    document.getElementById('results-content').classList.remove('hidden');

    // Build table headers
    const cols = selectedFields.length ? selectedFields : Object.keys({id:1,name:2,status:3});
    const thead = document.getElementById('results-thead');
    thead.innerHTML = cols.map(c => `<th class="p-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wide">${c}</th>`).join('');

    // Mock rows
    const tbody = document.getElementById('results-tbody');
    tbody.innerHTML = Array(8).fill(null).map((_, i) =>
        `<tr class="hover:bg-muted/30 transition-colors">${cols.map(c => `<td class="p-3 text-sm text-muted-foreground">—</td>`).join('')}</tr>`
    ).join('');
    document.getElementById('results-count').textContent = '8 rows (demo)';

    // Mini chart
    const chart = document.getElementById('results-chart');
    const vals = [45,72,38,85,61,90,44,76,53,68];
    chart.innerHTML = vals.map(v => `<div class="flex-1 bg-primary/25 hover:bg-primary/50 rounded-t transition-colors" style="height:${v}%"></div>`).join('');
});

// Save report modal
document.getElementById('btn-save-report')?.addEventListener('click', () => {
    document.getElementById('modal-save-report').classList.remove('hidden');
    document.getElementById('modal-save-report').classList.add('flex');
});
document.querySelectorAll('.save-report-close').forEach(b => b.addEventListener('click', () => {
    document.getElementById('modal-save-report').classList.add('hidden');
    document.getElementById('modal-save-report').classList.remove('flex');
}));
</script>
@endpush

@endsection
