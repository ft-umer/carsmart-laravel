{{-- resources/views/automations/runs.blade.php --}}
{{-- Phase 5 — A3: Runs & Monitoring --}}
@extends('layouts.app')
@section('title', 'Automation Runs — Carsmart')

@section('content')

@include('partials._retention_banner')

<div class="kt-container-fixed">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
        <div>
            <h1 class="text-xl font-semibold text-foreground">Runs & Monitoring</h1>
            <p class="text-sm text-muted-foreground mt-0.5">Observe executions, failures, and outcomes</p>
        </div>
        <div class="flex gap-2">
            <button class="kt-btn kt-btn-ghost kt-btn-sm" id="btn-export-runs">
                <i data-lucide="download" class="w-4 h-4 mr-1"></i> Export
            </button>
        </div>
    </div>

    {{-- Summary stats --}}
    <div class="grid grid-cols-3 gap-4 mb-5">
        @foreach([
            ['Succeeded', $stats['succeeded'] ?? 0, 'check-circle', 'success'],
            ['Failed',    $stats['failed']    ?? 0, 'x-circle',     'destructive'],
            ['Skipped',   $stats['skipped']   ?? 0, 'skip-forward', 'warning'],
        ] as [$label, $val, $icon, $colour])
            <div class="kt-card">
                <div class="kt-card-content p-4 flex items-center gap-3">
                    <i data-lucide="{{ $icon }}" class="w-5 h-5 text-{{ $colour }} shrink-0"></i>
                    <div>
                        <div class="text-xl font-bold text-mono">{{ $val }}</div>
                        <div class="text-xs text-muted-foreground">{{ $label }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('automations.runs') }}" class="card border border-border rounded-lg p-3 mb-5">
        <div class="flex flex-wrap gap-2 items-end">
            <div class="min-w-[180px]">
                <label class="block text-xs text-muted-foreground mb-1">Journey</label>
                <select name="journey_id" class="kt-input w-full">
                    <option value="">All journeys</option>
                    @foreach($journeys ?? [] as $j)
                        <option value="{{ $j['id'] }}" @selected(($journeyId ?? '') == $j['id'])>{{ $j['name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[130px]">
                <label class="block text-xs text-muted-foreground mb-1">Status</label>
                <select name="status" class="kt-input w-full">
                    <option value="">All</option>
                    <option value="Succeeded" @selected(($status ?? '') === 'Succeeded')>Succeeded</option>
                    <option value="Failed"    @selected(($status ?? '') === 'Failed')>Failed</option>
                    <option value="Skipped"   @selected(($status ?? '') === 'Skipped')>Skipped</option>
                </select>
            </div>
            <div class="min-w-[160px]">
                <label class="block text-xs text-muted-foreground mb-1">Date range</label>
                <select name="date_range" class="kt-input w-full">
                    <option value="today">Today</option>
                    <option value="7d" @selected(($dateRange ?? '') === '7d')>Last 7 days</option>
                    <option value="30d" @selected(($dateRange ?? '') === '30d')>Last 30 days</option>
                </select>
            </div>
            <button type="submit" class="kt-btn kt-btn-mono self-end">Apply</button>
            <a href="{{ route('automations.runs') }}" class="kt-btn kt-btn-ghost self-end">Reset</a>
        </div>
    </form>

    {{-- Table + detail panel --}}
    <div class="grid grid-cols-1 xl:grid-cols-[1fr,420px] gap-5">

        <div class="card border border-border rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-sm">
                    <thead class="bg-muted/40">
                        <tr>
                            @foreach(['Time','Journey','Step','Recipient','Channel','Status','Reason','Actions'] as $col)
                                <th class="p-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wide">{{ $col }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border bg-background">
                        @forelse($runs ?? [] as $run)
                            <tr class="hover:bg-muted/30 transition-colors cursor-pointer run-row" data-id="{{ $run['id'] }}">
                                <td class="p-3 text-xs text-muted-foreground whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($run['time'])->format('d M H:i:s') }}
                                </td>
                                <td class="p-3 text-sm font-medium text-foreground">{{ $run['journey'] }}</td>
                                <td class="p-3 text-xs">{{ $run['step'] }}</td>
                                <td class="p-3 text-xs text-muted-foreground">{{ $run['recipient'] }}</td>
                                <td class="p-3">
                                    <span class="kt-badge kt-badge-outline kt-badge-xs">{{ $run['channel'] }}</span>
                                </td>
                                <td class="p-3">
                                    <span class="kt-badge kt-badge-{{ match($run['status']) {
                                        'Succeeded' => 'success', 'Failed' => 'destructive',
                                        'Skipped' => 'warning', default => 'secondary'
                                    } }} kt-badge-sm">{{ $run['status'] }}</span>
                                </td>
                                <td class="p-3 text-xs text-muted-foreground">{{ $run['reason'] ?? '—' }}</td>
                                <td class="p-3">
                                    <div class="flex gap-1">
                                        <button class="kt-btn kt-btn-ghost kt-btn-xs run-log-btn" data-id="{{ $run['id'] }}">
                                            Open log
                                        </button>
                                        @if($run['status'] === 'Failed')
                                            <button class="kt-btn kt-btn-ghost kt-btn-xs text-warning run-retry-btn" data-id="{{ $run['id'] }}">
                                                Retry
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-10 text-center text-muted-foreground">No runs matching your filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(isset($runs) && method_exists($runs, 'links'))
                <div class="p-4 border-t border-border">{{ $runs->links() }}</div>
            @endif
        </div>

        {{-- Run detail panel --}}
        <div id="run-detail" class="card border border-border rounded-xl p-5 hidden xl:block">
            <h3 class="text-sm font-semibold text-foreground mb-3">Run detail</h3>
            <p class="text-sm text-muted-foreground">Select a run to inspect payload, headers, and provider response.</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
const runDetail = document.getElementById('run-detail');
document.querySelectorAll('.run-log-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const row = btn.closest('tr');
        const id  = btn.dataset.id;
        const journey = row.querySelector('td:nth-child(2)')?.innerText;
        const status  = row.querySelector('.kt-badge')?.innerText;

        runDetail.innerHTML = `
            <h3 class="text-sm font-semibold text-foreground mb-3">Run #${id}</h3>
            <div class="space-y-3">
                <div class="flex items-center gap-2 text-xs">
                    <span class="text-muted-foreground w-20">Journey</span>
                    <span class="font-medium text-foreground">${journey}</span>
                </div>
                <div class="flex items-center gap-2 text-xs">
                    <span class="text-muted-foreground w-20">Status</span>
                    <span>${status}</span>
                </div>
                <div>
                    <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wide mb-1">Payload</p>
                    <pre class="bg-muted rounded p-2 text-xs font-mono overflow-auto">{"event":"example","id":"${id}"}</pre>
                </div>
                <div>
                    <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wide mb-1">Provider response</p>
                    <pre class="bg-muted rounded p-2 text-xs font-mono overflow-auto">{"status":200,"message_id":"msg_abc"}</pre>
                </div>
                <div>
                    <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wide mb-1">Suppression</p>
                    <p class="text-xs text-muted-foreground">None</p>
                </div>
            </div>`;
    });
});

document.querySelectorAll('.run-retry-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        if(confirm('Retry this failed run?')) {
            btn.textContent = 'Retrying…';
            setTimeout(() => { btn.textContent = 'Retried'; btn.disabled = true; }, 1500);
        }
    });
});
</script>
@endpush

@endsection
