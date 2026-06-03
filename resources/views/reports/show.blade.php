{{-- resources/views/reports/show.blade.php --}}
{{-- Phase 5 — R1: Individual Report View --}}
@extends('layouts.app')
@section('title', ($reportTitle ?? 'Report') . ' — Reports')

@section('content')

    @include('partials._retention_banner')

    <div class="kt-container-fixed">
        {{-- Breadcrumbs --}}
        <div class="flex items-center gap-2 text-sm text-muted-foreground mb-4">
            <a href="{{ route('dashboard') }}" class="hover:text-foreground transition-colors">
                Dashboard
            </a>

            <i data-lucide="chevron-right" class="w-4 h-4"></i>

            <a href="{{ route('reports.index') }}" class="hover:text-foreground transition-colors">
                Reports
            </a>

            <i data-lucide="chevron-right" class="w-4 h-4"></i>

            <span class="text-foreground font-medium">
                {{ $title ?? ($reportTitle ?? 'Report') }}
            </span>
        </div>

        {{-- Toolbar --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
            <div class="flex items-center gap-3">
                <a href="{{ route('reports.index') }}" class="text-muted-foreground hover:text-foreground">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                </a>
                <div>
                    <h1 class="text-xl font-semibold text-foreground">{{ $reportTitle ?? 'Report' }}</h1>
                    <p class="text-sm text-muted-foreground mt-0.5">{{ $reportDescription ?? '' }}</p>
                </div>
            </div>
            <div class="flex gap-2 flex-wrap">
                <button class="kt-btn kt-btn-ghost kt-btn-sm" id="btn-export-csv">
                    <i data-lucide="download" class="w-4 h-4 mr-1"></i> CSV
                </button>
                <button class="kt-btn kt-btn-ghost kt-btn-sm" id="btn-export-pdf">
                    <i data-lucide="file-text" class="w-4 h-4 mr-1"></i> PDF
                </button>
                <button class="kt-btn kt-btn-outline kt-btn-sm" id="btn-save-view">
                    <i data-lucide="bookmark" class="w-4 h-4 mr-1"></i> Save view
                </button>
                <button class="kt-btn kt-btn-mono kt-btn-sm" id="btn-schedule-this">
                    <i data-lucide="calendar" class="w-4 h-4 mr-1"></i> Schedule
                </button>
            </div>
        </div>

        {{-- Filters --}}
        <form method="GET" class="card border border-border rounded-lg p-3 mb-5">
            <div class="flex flex-wrap gap-2 items-end">
                <div class="min-w-[160px]">
                    <label class="block text-xs text-muted-foreground mb-1">Date range</label>
                    <select name="range" class="kt-input w-full">
                        <option value="7d" @selected(($range ?? '30d') === '7d')>Last 7 days</option>
                        <option value="30d" @selected(($range ?? '30d') === '30d')>Last 30 days</option>
                        <option value="90d" @selected(($range ?? '30d') === '90d')>Last 90 days</option>
                    </select>
                </div>

                @if (in_array($reportSlug ?? '', ['valuation-coverage', 'valuation-delta']))
                    <div class="min-w-[140px]">
                        <label class="block text-xs text-muted-foreground mb-1">Owner</label>
                        <select name="owner" class="kt-input w-full">
                            <option value="">All owners</option>
                            @foreach ($owners ?? [] as $o)
                                <option value="{{ $o }}" @selected(($owner ?? '') === $o)>{{ $o }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="min-w-[140px]">
                        <label class="block text-xs text-muted-foreground mb-1">Source</label>
                        <select name="source" class="kt-input w-full">
                            <option value="">All sources</option>
                            <option value="internal" @selected(($source ?? '') === 'internal')>Internal</option>
                            <option value="external" @selected(($source ?? '') === 'external')>External</option>
                            <option value="provider" @selected(($source ?? '') === 'provider')>Provider</option>
                        </select>
                    </div>
                @endif

                @if (in_array($reportSlug ?? '', ['listings-funnel']))
                    <div class="min-w-[120px]">
                        <label class="block text-xs text-muted-foreground mb-1">CST</label>
                        <select name="cst" class="kt-input w-full">
                            <option value="">All</option>
                            @foreach (['CST1', 'CST2', 'CST3', 'CST4', 'CST5'] as $cst)
                                <option value="{{ $cst }}" @selected(($cstFilter ?? '') === $cst)>{{ $cst }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="min-w-[120px]">
                        <label class="block text-xs text-muted-foreground mb-1">VLT</label>
                        <select name="vlt" class="kt-input w-full">
                            <option value="">All</option>
                            @foreach (['VLT1', 'VLT2', 'VLT3', 'VLT4', 'VLT5'] as $vlt)
                                <option value="{{ $vlt }}" @selected(($vltFilter ?? '') === $vlt)>{{ $vlt }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <button type="submit" class="kt-btn kt-btn-mono self-end">Apply</button>
                <a href="{{ request()->url() }}" class="kt-btn kt-btn-ghost self-end">Reset</a>
            </div>
        </form>

        {{-- ── Metric cards ── --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            @foreach ($metrics ?? [] as $metric)
                <div class="kt-card">
                    <div class="kt-card-content p-4">
                        <div class="text-xs text-muted-foreground mb-1">{{ $metric['label'] }}</div>
                        <div class="text-2xl font-bold text-mono">{{ $metric['value'] }}</div>
                        @if (isset($metric['delta']))
                            <div
                                class="text-xs {{ str_starts_with($metric['delta'], '+') ? 'text-success' : 'text-destructive' }} mt-0.5">
                                {{ $metric['delta'] }} vs prev period
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach

            {{-- Default placeholder metrics if none provided --}}
            @if (empty($metrics ?? []))
                @foreach ([['Total records', '—', null], ['Period change', '—', null], ['Top performer', '—', null], ['Avg value', '—', null]] as [$label, $val, $delta])
                    <div class="kt-card">
                        <div class="kt-card-content p-4">
                            <div class="text-xs text-muted-foreground mb-1">{{ $label }}</div>
                            <div class="text-2xl font-bold text-mono">{{ $val }}</div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        {{-- ── Chart placeholder ── --}}
        @if (in_array($reportSlug ?? '', ['listings-funnel', 'auction-performance', 'lead-conversion', 'valuation-delta']))
            <div class="card border border-border rounded-xl p-5 mb-5">
                <h3 class="text-sm font-semibold text-foreground mb-4">
                    @if ($reportSlug === 'listings-funnel')
                        Funnel conversion
                    @elseif($reportSlug === 'valuation-delta')
                        Delta distribution
                    @else
                        Performance over time
                    @endif
                </h3>
                <div id="report-chart" class="h-48 flex items-end gap-1 px-4">
                    @php $bars = [45,62,78,55,90,38,72,85,61,44,93,56]; @endphp
                    @foreach ($bars as $h)
                        <div class="flex-1 bg-primary/20 hover:bg-primary/40 transition-colors rounded-t cursor-pointer"
                            style="height:{{ $h }}%;" title="{{ $h }}%"></div>
                    @endforeach
                </div>
                <div class="flex justify-between text-xs text-muted-foreground mt-2 px-4">
                    <span>Start of period</span>
                    <span>End of period</span>
                </div>
            </div>
        @endif

        {{-- ── Data table ── --}}
        <div class="card border border-border rounded-xl overflow-hidden">
            <div class="p-4 border-b border-border flex items-center justify-between">
                <h3 class="text-sm font-semibold text-foreground">Data table</h3>
                <span class="text-xs text-muted-foreground">{{ count($rows ?? []) }} rows</span>
            </div>

            @if (($reportSlug ?? '') === 'valuation-coverage')
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-muted/40">
                            <tr>
                                @foreach (['Listing', 'Latest valuation', 'Source', 'Time', 'Guide', 'Δ £', 'Δ %', 'Owner', 'Status'] as $col)
                                    <th
                                        class="p-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wide">
                                        {{ $col }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border bg-background">
                            @forelse($rows ?? [] as $row)
                                <tr class="hover:bg-muted/30 transition-colors">
                                    <td class="p-3 font-medium text-foreground">{{ $row['listing'] }}</td>
                                    <td class="p-3">
                                        @if (is_numeric($row['valuation'] ?? null))
                                            £{{ number_format((float) $row['valuation'] / 100, 0) }}
                                        @else
                                            <span class="text-muted-foreground">None</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-xs">{{ $row['source'] ?? '—' }}</td>
                                    <td class="p-3 text-xs text-muted-foreground">
                                        {{ isset($row['valuation_at']) ? \Carbon\Carbon::parse($row['valuation_at'])->diffForHumans() : '—' }}
                                    </td>
                                   <td class="p-3">
    @if(is_numeric($row['guide'] ?? null))
        £{{ number_format((float)$row['guide'] / 100, 0) }}
    @else
        —
    @endif
</td>
                                    </td>
                                    <td
                                        class="p-3 {{ isset($row['delta']) && $row['delta'] > 0 ? 'text-success' : 'text-destructive' }}">
                                        {{ isset($row['delta']) ? '£' . number_format(abs($row['delta']) / 100, 0) : '—' }}
                                    </td>
                                    <td class="p-3 text-xs">{{ $row['delta_pct'] ?? '—' }}</td>
                                    <td class="p-3 text-sm">{{ $row['owner'] }}</td>
                                    <td class="p-3">
                                        <span class="kt-badge kt-badge-outline kt-badge-xs">{{ $row['status'] }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="p-10 text-center text-muted-foreground">No data for selected
                                        filters.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-muted/40">
                            <tr>
                                @foreach ($columns ?? ['Item', 'Value', 'Change', 'Status'] as $col)
                                    <th
                                        class="p-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wide">
                                        {{ $col }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border bg-background">
                            @forelse($rows ?? [] as $row)
                                <tr class="hover:bg-muted/30 transition-colors">
                                    @foreach ($row as $cell)
                                        <td class="p-3 text-sm text-foreground">{{ $cell }}</td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ count($columns ?? ['Item', 'Value', 'Change', 'Status']) }}"
                                        class="p-10 text-center text-muted-foreground">No data available for the selected
                                        period.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif

            @if (isset($rows) && method_exists($rows, 'links'))
                <div class="p-4 border-t border-border">{{ $rows->links() }}</div>
            @endif
        </div>

    </div>

    @push('scripts')
        <script>
            document.getElementById('btn-export-csv')?.addEventListener('click', () => {
                window.location = window.location.href + (window.location.href.includes('?') ? '&' : '?') +
                'export=csv';
            });
            document.getElementById('btn-export-pdf')?.addEventListener('click', () => {
                window.location = window.location.href + (window.location.href.includes('?') ? '&' : '?') +
                'export=pdf';
            });
        </script>
    @endpush

@endsection
