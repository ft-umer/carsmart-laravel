{{-- resources/views/disputes/index.blade.php --}}
{{-- Phase 4 — S1: Disputes → Queue --}}
@extends('layouts.app')
@section('title', 'Disputes Queue — Carsmart')

@section('content')

@include('partials._retention_banner')

<div class="kt-container-fixed">
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
    <div class="flex items-center gap-3 flex-wrap">
        <h1 class="text-xl font-semibold text-foreground">Disputes</h1>
        <span class="text-sm text-muted-foreground">
            {{ count($disputes) }} case{{ count($disputes) !== 1 ? 's' : '' }}
        </span>
        @php
            $overdueAck = collect($disputes)
                ->where('state', 'New')
                ->filter(fn($d) => ($d['ack_hours_left'] ?? 99) < 0)
                ->count();
            $overdueDecision = collect($disputes)
                ->whereIn('state', ['Ack sent','Investigating'])
                ->filter(fn($d) => ($d['decision_days_left'] ?? 99) < 0)
                ->count();
        @endphp
        @if ($overdueAck > 0)
            <span class="kt-badge kt-badge-destructive kt-badge-sm">
                {{ $overdueAck }} ack overdue
            </span>
        @endif
        @if ($overdueDecision > 0)
            <span class="kt-badge kt-badge-warning kt-badge-sm">
                {{ $overdueDecision }} decision overdue
            </span>
        @endif
    </div>
    <div class="flex gap-2">
        <button id="btn-export-disputes" class="kt-btn kt-btn-ghost">
            <i data-lucide="download" class="w-4 h-4 mr-1"></i>Export
        </button>
        <a href="{{ route('disputes.create') }}" class="kt-btn kt-btn-mono">
            + Open case
        </a>
    </div>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('disputes.index') }}"
      class="card border border-border rounded-lg p-3 mb-5">
    <input type="hidden" name="include_archived" value="{{ request('include_archived') }}">
    <div class="flex flex-wrap gap-2 items-end">

        <div class="flex-1 min-w-[180px]">
            <label class="block text-xs text-muted-foreground mb-1">Search</label>
            <input name="search" value="{{ $search ?? '' }}" type="search"
                   class="kt-input w-full" placeholder="Case / deal / party…" />
        </div>

        <div class="min-w-[150px]">
            <label class="block text-xs text-muted-foreground mb-1">Source</label>
            <select name="source" class="kt-input w-full">
                <option value="">All sources</option>
                <option value="Seller objection" @selected(($source ?? '') === 'Seller objection')>Seller objection</option>
                <option value="Post-handover"    @selected(($source ?? '') === 'Post-handover')>Post-handover</option>
            </select>
        </div>

        <div class="min-w-[140px]">
            <label class="block text-xs text-muted-foreground mb-1">SLA</label>
            <select name="sla" class="kt-input w-full">
                <option value="">Any</option>
                <option value="ack_due"      @selected(($sla ?? '') === 'ack_due')>Ack due</option>
                <option value="decision_due" @selected(($sla ?? '') === 'decision_due')>Decision due</option>
            </select>
        </div>

        <div class="min-w-[130px]">
            <label class="block text-xs text-muted-foreground mb-1">State</label>
            <select name="state" class="kt-input w-full">
                <option value="">All states</option>
                @foreach(['New','Ack sent','Investigating','Decision pending','Resolved','Escalated'] as $s)
                    <option value="{{ $s }}" @selected(($state ?? '') === $s)>{{ $s }}</option>
                @endforeach
            </select>
        </div>

        <div class="min-w-[130px]">
            <label class="block text-xs text-muted-foreground mb-1">Owner</label>
            <select name="owner" class="kt-input w-full">
                <option value="">All owners</option>
                @foreach ($owners ?? [] as $o)
                    <option value="{{ $o }}" @selected(($owner ?? '') === $o)>{{ $o }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="kt-btn kt-btn-mono self-end">Apply</button>
        <a href="{{ route('disputes.index') }}" class="kt-btn kt-btn-ghost self-end">Reset</a>
    </div>
</form>

{{-- Table + QV --}}
<div class="grid grid-cols-1 xl:grid-cols-[1fr,380px] gap-5">

    <div class="card border border-border rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1000px] text-sm">
                <thead class="bg-muted/40 sticky top-0 z-10">
                    <tr>
                        @foreach(['#','Case','Deal','Reason','SLA: Ack by','SLA: Decision by','State','Owner','Actions'] as $col)
                            <th class="p-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wide
                                       {{ $col === 'Actions' ? 'w-32' : '' }}">
                                {{ $col }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-border bg-background">
                    @forelse ($disputes as $d)
                        @php
                            $ackLeft      = $d['ack_hours_left']    ?? null;
                            $decLeft      = $d['decision_days_left'] ?? null;
                            $ackOverdue   = $ackLeft !== null && $ackLeft < 0;
                            $decOverdue   = $decLeft !== null && $decLeft < 0;

                            $stateCls = match ($d['state'] ?? '') {
                                'New'               => 'kt-badge-outline',
                                'Ack sent'          => 'kt-badge-info',
                                'Investigating'     => 'kt-badge-warning',
                                'Decision pending'  => 'kt-badge-primary',
                                'Resolved'          => 'kt-badge-success',
                                'Escalated'         => 'kt-badge-destructive',
                                default             => 'kt-badge-outline',
                            };
                        @endphp
                        <tr class="hover:bg-muted/30 transition-colors" data-dispute-id="{{ $d['id'] }}">

                            <td class="p-3 text-xs text-muted-foreground">{{ $loop->iteration }}</td>

                            {{-- Case --}}
                            <td class="p-3">
                                <button data-action="preview-dispute" data-id="{{ $d['id'] }}"
                                        class="font-medium text-foreground hover:text-primary text-left">
                                    {{ $d['ref'] ?? 'DSP-' . $d['id'] }}
                                </button>
                                <div class="text-xs text-muted-foreground mt-0.5">
                                    {{ $d['source'] ?? '—' }}
                                </div>
                            </td>

                            {{-- Deal --}}
                            <td class="p-3">
                                @if ($d['deal_ref'] ?? null)
                                    <a href="{{ route('deals.show', $d['deal_id']) }}"
                                       class="font-mono text-xs text-primary hover:underline">
                                        {{ $d['deal_ref'] }}
                                    </a>
                                @else
                                    <span class="text-xs text-muted-foreground">—</span>
                                @endif
                            </td>

                            {{-- Reason --}}
                            <td class="p-3 text-xs max-w-[140px]">
                                <span class="truncate block" title="{{ $d['reason'] ?? '' }}">
                                    {{ $d['reason'] ?? '—' }}
                                </span>
                            </td>

                            {{-- SLA: Ack --}}
                            <td class="p-3 whitespace-nowrap">
                                @if ($ackLeft === null)
                                    <span class="text-xs text-muted-foreground">—</span>
                                @elseif ($ackOverdue)
                                    <span class="text-xs text-destructive font-semibold">⚠ Overdue</span>
                                @elseif ($ackLeft <= 6)
                                    <span class="text-xs text-warning font-medium">{{ $ackLeft }}h left</span>
                                @else
                                    <span class="text-xs text-muted-foreground">{{ $ackLeft }}h</span>
                                @endif
                            </td>

                            {{-- SLA: Decision --}}
                            <td class="p-3 whitespace-nowrap">
                                @if ($decLeft === null)
                                    <span class="text-xs text-muted-foreground">—</span>
                                @elseif ($decOverdue)
                                    <span class="text-xs text-destructive font-semibold">⚠ Overdue</span>
                                @elseif ($decLeft <= 1)
                                    <span class="text-xs text-warning font-medium">{{ $decLeft }}d left</span>
                                @else
                                    <span class="text-xs text-muted-foreground">{{ $decLeft }}d</span>
                                @endif
                            </td>

                            {{-- State --}}
                            <td class="p-3">
                                <span class="kt-badge {{ $stateCls }} kt-badge-sm">{{ $d['state'] ?? '—' }}</span>
                            </td>

                            {{-- Owner --}}
                            <td class="p-3">
                                @if ($d['owner'] ?? null)
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full
                                                 bg-primary/10 text-primary text-xs font-bold"
                                          title="{{ $d['owner'] }}">
                                        {{ $d['owner'] }}
                                    </span>
                                @else
                                    <span class="text-xs text-muted-foreground">—</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="p-3">
                                <div class="flex items-center gap-1.5">
                                    <button data-action="preview-dispute" data-id="{{ $d['id'] }}"
                                            class="kt-btn kt-btn-ghost kt-btn-sm">Preview</button>
                                    <a href="{{ route('disputes.show', $d['id']) }}"
                                       class="kt-btn kt-btn-outline kt-btn-sm">Open</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-12 text-center text-muted-foreground text-sm">
                                <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 opacity-30"></i>
                                No disputes in queue.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 border-t border-border flex items-center justify-between
                    text-xs text-muted-foreground bg-muted/10">
            <span>{{ count($disputes) }} of {{ $total ?? count($disputes) }}</span>
            <div class="flex gap-2">
                <button class="kt-btn kt-btn-ghost kt-btn-sm" disabled>
                    <i data-lucide="chevron-left" class="w-3.5 h-3.5 mr-1"></i>Prev
                </button>
                <button class="kt-btn kt-btn-ghost kt-btn-sm" disabled>
                    Next<i data-lucide="chevron-right" class="w-3.5 h-3.5 ml-1"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Quick View --}}
    <aside class="card border border-border rounded-xl overflow-hidden
                  sticky top-[86px] h-[calc(100vh-120px)] flex flex-col">

        <div class="px-4 py-3 border-b border-border bg-muted/20 shrink-0 flex items-start justify-between gap-3">
            <div class="min-w-0">
                <div id="qv-title" class="text-sm font-semibold text-foreground">Select a case</div>
                <div id="qv-meta"  class="text-xs text-muted-foreground mt-0.5">Preview will appear here</div>
            </div>
            <a id="qv-open-link" href="#" class="kt-btn kt-btn-outline kt-btn-sm hidden shrink-0">Open</a>
        </div>

        <div class="border-b border-border px-4 pt-2 shrink-0 overflow-x-auto">
            <div class="flex gap-1 min-w-max">
                @foreach(['Overview','Parties','SLA','Activity'] as $tab)
                    <button data-qv-tab="{{ Str::slug($tab) }}"
                            class="qv-tab-btn kt-btn kt-btn-sm
                                   {{ $loop->first ? 'kt-btn-mono' : 'kt-btn-ghost' }}">
                        {{ $tab }}
                    </button>
                @endforeach
            </div>
        </div>

        <div class="flex-1 overflow-auto p-4 text-sm" id="qv-body">
            @foreach(['overview','parties','sla','activity'] as $tab)
                <div id="qv-tab-{{ $tab }}"
                     class="qv-tab-content {{ $loop->first ? '' : 'hidden' }} space-y-3">
                    <p class="text-xs text-muted-foreground">Select a case to preview.</p>
                </div>
            @endforeach
        </div>

        <div id="qv-footer" class="border-t border-border px-4 py-3 bg-muted/10 shrink-0 hidden">
            <div id="qv-footer-actions" class="flex gap-2 flex-wrap"></div>
        </div>
    </aside>
</div>

<div id="toast-container" class="fixed bottom-5 right-5 z-[200] flex flex-col gap-2 pointer-events-none"></div>
@include('partials._phase4_js')
</div>

<script>
(function () {
    const { toast, auditEvent, fmt } = window.CS4;
    const DISPUTES = @json($disputes->values()->all());

    function renderQV(id) {
        const d = DISPUTES.find(x => String(x.id) === String(id));
        if (!d) return;

        const qvTitle = document.getElementById('qv-title');
        const qvMeta  = document.getElementById('qv-meta');
        const link    = document.getElementById('qv-open-link');
        if (qvTitle) qvTitle.textContent = d.ref ?? ('DSP-' + d.id);
        if (qvMeta)  qvMeta.textContent  = d.state + ' · ' + (d.source ?? '');
        if (link)    { link.href = '/disputes/' + id; link.classList.remove('hidden'); }

        // Overview
        document.getElementById('qv-tab-overview').innerHTML = `
            <div class="grid grid-cols-2 gap-3 text-xs">
                <div><span class="text-muted-foreground">Case ref</span><br><strong>${d.ref ?? '—'}</strong></div>
                <div><span class="text-muted-foreground">Source</span><br><strong>${d.source ?? '—'}</strong></div>
                <div><span class="text-muted-foreground">Deal</span><br><strong class="font-mono">${d.deal_ref ?? '—'}</strong></div>
                <div><span class="text-muted-foreground">State</span><br><strong>${d.state ?? '—'}</strong></div>
                <div class="col-span-2"><span class="text-muted-foreground">Reason</span><br><strong>${d.reason ?? '—'}</strong></div>
            </div>`;

        // Parties
        document.getElementById('qv-tab-parties').innerHTML = `
            <div class="space-y-3 text-xs">
                <div class="p-3 rounded-lg border border-border bg-muted/10">
                    <div class="font-semibold mb-1">Raised by</div>
                    <div>${d.raised_by ?? '—'}</div>
                    <div class="text-muted-foreground">${d.raised_by_role ?? ''}</div>
                </div>
                <div class="p-3 rounded-lg border border-border bg-muted/10">
                    <div class="font-semibold mb-1">Assigned to</div>
                    <div>${d.owner ?? 'Unassigned'}</div>
                </div>
            </div>`;

        // SLA
        const ackLeft = d.ack_hours_left;
        const decLeft = d.decision_days_left;
        document.getElementById('qv-tab-sla').innerHTML = `
            <div class="space-y-4 text-xs">
                <div class="p-3 rounded-xl border ${ackLeft !== null && ackLeft < 0 ? 'border-destructive bg-red-50 dark:bg-red-900/20' : 'border-border bg-muted/10'}">
                    <div class="font-semibold mb-1 flex items-center gap-2">
                        <i data-lucide="clock" class="w-3.5 h-3.5"></i> Acknowledgement SLA
                    </div>
                    <div class="text-xl font-bold ${ackLeft !== null && ackLeft < 0 ? 'text-destructive' : ''}">
                        ${ackLeft === null ? '—' : ackLeft < 0 ? '⚠ Overdue' : ackLeft + 'h remaining'}
                    </div>
                    <div class="text-muted-foreground mt-0.5">Target: within 24 hours of opening</div>
                </div>
                <div class="p-3 rounded-xl border ${decLeft !== null && decLeft < 0 ? 'border-destructive bg-red-50 dark:bg-red-900/20' : 'border-border bg-muted/10'}">
                    <div class="font-semibold mb-1 flex items-center gap-2">
                        <i data-lucide="gavel" class="w-3.5 h-3.5"></i> Decision SLA
                    </div>
                    <div class="text-xl font-bold ${decLeft !== null && decLeft < 0 ? 'text-destructive' : ''}">
                        ${decLeft === null ? '—' : decLeft < 0 ? '⚠ Overdue' : decLeft + 'd remaining'}
                    </div>
                    <div class="text-muted-foreground mt-0.5">Target: within 5 business days</div>
                </div>
            </div>`;

        // Activity
        const acts = d.activity ?? [];
        document.getElementById('qv-tab-activity').innerHTML = acts.length
            ? acts.slice().reverse().map(a => `
                <div class="flex gap-2 text-xs">
                    <div class="w-2 h-2 rounded-full bg-primary mt-1.5 shrink-0"></div>
                    <div><div class="font-medium">${a.description}</div>
                    <div class="text-muted-foreground">${a.date}</div></div>
                </div>`).join('')
            : '<p class="text-xs text-muted-foreground">No activity.</p>';

        // Footer
        const footer = document.getElementById('qv-footer');
        const fa = document.getElementById('qv-footer-actions');
        if (footer && fa) {
            footer.classList.remove('hidden');
            fa.innerHTML = `
                ${d.state === 'New'
                    ? `<button onclick="sendAck('${id}')" class="kt-btn kt-btn-mono kt-btn-sm">Send ack</button>` : ''}
                <a href="/disputes/${id}" class="kt-btn kt-btn-outline kt-btn-sm">Open case</a>`;
        }

        window.CS4.switchQvTab('overview');
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function sendAck(id) {
        auditEvent('dispute_ack_sent', { id });
        toast('Acknowledgement sent.', 'success');
        renderQV(id);
    }
    window.sendAck = sendAck;

    document.addEventListener('click', e => {
        const btn = e.target.closest('[data-action]');
        if (!btn) return;
        if (btn.dataset.action === 'preview-dispute') renderQV(btn.dataset.id);
    });

    document.getElementById('btn-export-disputes')?.addEventListener('click', () =>
        toast('Export queued.', 'info')
    );
})();
</script>

@endsection
