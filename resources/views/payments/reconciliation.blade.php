{{-- resources/views/payments/reconciliation.blade.php --}}
{{-- Phase 4 — P5: Payments → Reconciliation --}}
@extends('layouts.app')
@section('title', 'Reconciliation — Payments')

@section('content')

@include('partials._retention_banner')

<div class="kt-container-fixed">
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
    <div class="flex items-center gap-3 flex-wrap">
        <h1 class="text-xl font-semibold text-foreground">Reconciliation</h1>
        @php $exceptionsCount = collect($exceptions ?? [])->where('resolved', false)->count(); @endphp
        @if ($exceptionsCount > 0)
            <span class="kt-badge kt-badge-destructive kt-badge-sm">
                {{ $exceptionsCount }} unresolved exception{{ $exceptionsCount !== 1 ? 's' : '' }}
            </span>
        @endif
    </div>
</div>

{{-- Upload + auto-match flow --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">

    {{-- Step 1: Upload --}}
    <div class="card border border-border rounded-xl p-5 space-y-3">
        <div class="flex items-center gap-2 mb-1">
            <span class="w-6 h-6 rounded-full bg-primary text-primary-foreground text-xs font-bold
                         flex items-center justify-center shrink-0">1</span>
            <h3 class="text-sm font-semibold">Upload settlement file</h3>
        </div>
        <p class="text-xs text-muted-foreground">
            Upload the CSV settlement file from your payment service provider.
            Columns expected: date, reference, amount, type, description.
        </p>
        <div id="drop-zone"
             class="border-2 border-dashed border-border rounded-xl p-6 text-center
                    hover:border-primary/50 transition-colors cursor-pointer bg-muted/10"
             ondragover="event.preventDefault()"
             ondrop="handleDrop(event)">
            <i data-lucide="upload-cloud" class="w-8 h-8 mx-auto mb-2 text-muted-foreground opacity-50"></i>
            <p class="text-xs text-muted-foreground">Drag &amp; drop CSV here or</p>
            <label class="mt-2 inline-block">
                <span class="kt-btn kt-btn-outline kt-btn-sm cursor-pointer">Browse file</span>
                <input type="file" id="settlement-file" accept=".csv" class="hidden"
                       onchange="handleFileSelect(this)" />
            </label>
        </div>
        <div id="file-selected" class="hidden text-xs text-muted-foreground flex items-center gap-2">
            <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
            <span id="file-name"></span>
        </div>
        <button id="btn-run-match" class="kt-btn kt-btn-mono w-full" disabled>
            <i data-lucide="zap" class="w-4 h-4 mr-1"></i>Auto-match to ledger
        </button>
    </div>

    {{-- Step 2: Match summary --}}
    <div class="card border border-border rounded-xl p-5 space-y-3">
        <div class="flex items-center gap-2 mb-1">
            <span class="w-6 h-6 rounded-full bg-primary text-primary-foreground text-xs font-bold
                         flex items-center justify-center shrink-0">2</span>
            <h3 class="text-sm font-semibold">Match summary</h3>
        </div>
        @if ($lastRun ?? null)
            <div class="space-y-2 text-sm">
                <div class="flex justify-between py-2 border-b border-border">
                    <span class="text-muted-foreground text-xs">Run date</span>
                    <strong>{{ $lastRun['run_at'] ?? '—' }}</strong>
                </div>
                <div class="flex justify-between py-2 border-b border-border">
                    <span class="text-muted-foreground text-xs">Total items</span>
                    <strong>{{ $lastRun['total'] ?? 0 }}</strong>
                </div>
                <div class="flex justify-between py-2 border-b border-border text-green-600 dark:text-green-400">
                    <span class="text-xs">Matched</span>
                    <strong>{{ $lastRun['matched'] ?? 0 }}</strong>
                </div>
                <div class="flex justify-between py-2 {{ ($lastRun['exceptions'] ?? 0) > 0 ? 'text-destructive' : 'text-muted-foreground' }}">
                    <span class="text-xs">Exceptions</span>
                    <strong>{{ $lastRun['exceptions'] ?? 0 }}</strong>
                </div>
            </div>
            @if (($lastRun['matched'] ?? 0) > 0)
                <div class="h-2 rounded-full bg-muted overflow-hidden">
                    @php $pct = $lastRun['total'] > 0 ? round(($lastRun['matched'] / $lastRun['total']) * 100) : 0; @endphp
                    <div class="h-full bg-green-500 rounded-full" style="width: {{ $pct }}%"></div>
                </div>
                <div class="text-xs text-muted-foreground text-right">{{ $pct }}% auto-matched</div>
            @endif
        @else
            <p class="text-sm text-muted-foreground">No reconciliation run yet.</p>
        @endif
    </div>

    {{-- Step 3: Actions --}}
    <div class="card border border-border rounded-xl p-5 space-y-3">
        <div class="flex items-center gap-2 mb-1">
            <span class="w-6 h-6 rounded-full bg-primary text-primary-foreground text-xs font-bold
                         flex items-center justify-center shrink-0">3</span>
            <h3 class="text-sm font-semibold">Resolve exceptions</h3>
        </div>
        <p class="text-xs text-muted-foreground">
            Review unmatched or mismatched items below. Resolve by matching manually,
            writing off small differences, or raising a query.
        </p>
        <div class="space-y-2">
            <div class="flex justify-between items-center text-xs">
                <span class="text-muted-foreground">Unresolved</span>
                <span class="font-semibold text-destructive">{{ $exceptionsCount }}</span>
            </div>
            <div class="flex justify-between items-center text-xs">
                <span class="text-muted-foreground">Resolved this run</span>
                <span class="font-semibold text-green-600 dark:text-green-400">
                    {{ collect($exceptions ?? [])->where('resolved', true)->count() }}
                </span>
            </div>
        </div>
        <button id="btn-export-recon" class="kt-btn kt-btn-outline w-full">
            <i data-lucide="download" class="w-4 h-4 mr-1"></i>Export exceptions CSV
        </button>
    </div>
</div>

{{-- Exceptions table --}}
<div class="card border border-border rounded-xl overflow-hidden">
    <div class="px-4 py-3 border-b border-border bg-muted/20 flex items-center justify-between">
        <h3 class="text-sm font-semibold">Exceptions queue</h3>
        <div class="flex gap-2">
            <select id="exception-filter" class="kt-input text-xs">
                <option value="all">All exceptions</option>
                <option value="unresolved">Unresolved only</option>
                <option value="resolved">Resolved only</option>
            </select>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full min-w-[900px] text-sm" id="exceptions-table">
            <thead class="bg-muted/40 sticky top-0 z-10">
                <tr>
                    @foreach(['Item / Ref','Amount (PSP)','Expected (ledger)','Difference','Reason','Status','Actions'] as $col)
                        <th class="p-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wide">{{ $col }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-border bg-background" id="exceptions-tbody">
                @forelse ($exceptions ?? [] as $exc)
                    @php
                        $diff    = ($exc['amount'] ?? 0) - ($exc['expected'] ?? 0);
                        $excCls  = $exc['resolved'] ? 'kt-badge-success' : 'kt-badge-destructive';
                        $excLbl  = $exc['resolved'] ? 'Resolved' : 'Unresolved';
                    @endphp
                    <tr class="hover:bg-muted/30 transition-colors exception-row"
                        data-resolved="{{ $exc['resolved'] ? '1' : '0' }}">
                        <td class="p-3">
                            <div class="font-medium text-sm">{{ $exc['item'] ?? '—' }}</div>
                            <div class="font-mono text-xs text-muted-foreground">{{ $exc['ref'] ?? '' }}</div>
                        </td>
                        <td class="p-3 font-semibold">£{{ number_format($exc['amount'] ?? 0) }}</td>
                        <td class="p-3">£{{ number_format($exc['expected'] ?? 0) }}</td>
                        <td class="p-3 font-semibold {{ $diff > 0 ? 'text-green-600 dark:text-green-400' : ($diff < 0 ? 'text-destructive' : 'text-muted-foreground') }}">
                            {{ $diff >= 0 ? '+' : '' }}£{{ number_format($diff) }}
                        </td>
                        <td class="p-3 text-xs">{{ $exc['reason'] ?? '—' }}</td>
                        <td class="p-3">
                            <span class="kt-badge {{ $excCls }} kt-badge-sm">{{ $excLbl }}</span>
                        </td>
                        <td class="p-3">
                            @if (!$exc['resolved'])
                                <div class="flex gap-1.5">
                                    <button data-action="resolve-exception" data-id="{{ $exc['id'] }}"
                                            class="kt-btn kt-btn-outline kt-btn-sm">Resolve</button>
                                    <button data-action="writeoff-exception" data-id="{{ $exc['id'] }}"
                                            class="kt-btn kt-btn-ghost kt-btn-sm">Write-off</button>
                                </div>
                            @else
                                <span class="text-xs text-muted-foreground">{{ $exc['resolved_at'] ?? '' }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-12 text-center text-muted-foreground text-sm">
                            <i data-lucide="check-circle" class="w-8 h-8 mx-auto mb-2 opacity-30"></i>
                            No exceptions — books are balanced.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div id="toast-container" class="fixed bottom-5 right-5 z-[200] flex flex-col gap-2 pointer-events-none"></div>
@include('partials._phase4_js')

</div>
<script>
(function () {
    const { toast, auditEvent } = window.CS4;

    function handleFileSelect(input) {
        const file = input.files[0];
        if (!file) return;
        document.getElementById('file-selected').classList.remove('hidden');
        document.getElementById('file-name').textContent = file.name;
        document.getElementById('btn-run-match').disabled = false;
        document.getElementById('drop-zone').querySelector('p').textContent = 'File selected.';
    }
    window.handleFileSelect = handleFileSelect;

    function handleDrop(e) {
        e.preventDefault();
        const file = e.dataTransfer.files[0];
        if (!file || !file.name.endsWith('.csv')) { toast('Please drop a CSV file.', 'warning'); return; }
        document.getElementById('file-selected').classList.remove('hidden');
        document.getElementById('file-name').textContent = file.name;
        document.getElementById('btn-run-match').disabled = false;
    }
    window.handleDrop = handleDrop;

    document.getElementById('btn-run-match')?.addEventListener('click', () => {
        toast('Running auto-match…', 'info');
        setTimeout(() => toast('Auto-match complete. Check exceptions queue.', 'success'), 2000);
    });

    document.getElementById('btn-export-recon')?.addEventListener('click', () =>
        toast('Exceptions CSV queued.', 'info')
    );

    /* Exception filter */
    document.getElementById('exception-filter')?.addEventListener('change', function () {
        const rows = document.querySelectorAll('.exception-row');
        rows.forEach(row => {
            const resolved = row.dataset.resolved === '1';
            if (this.value === 'all') row.classList.remove('hidden');
            else if (this.value === 'resolved') row.classList.toggle('hidden', !resolved);
            else row.classList.toggle('hidden', resolved);
        });
    });

    document.addEventListener('click', e => {
        const btn = e.target.closest('[data-action]');
        if (!btn) return;
        const action = btn.dataset.action, id = btn.dataset.id;
        if (action === 'resolve-exception') { auditEvent('recon_exception_resolved', { id }); toast('Exception resolved.', 'success'); btn.closest('tr').dataset.resolved = '1'; }
        if (action === 'writeoff-exception') { auditEvent('recon_exception_writtenoff', { id }); toast('Exception written off.', 'success'); }
    });
})();
</script>

@endsection
