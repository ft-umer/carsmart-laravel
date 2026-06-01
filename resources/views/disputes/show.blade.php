{{-- resources/views/disputes/show.blade.php --}}
{{-- Phase 4 — S2: Dispute Case Detail (full workspace) --}}
@extends('layouts.app')
@section('title', ($dispute['ref'] ?? 'Case') . ' — Disputes')

@section('content')

@php
    $ackLeft = $dispute['ack_hours_left']    ?? null;
    $decLeft = $dispute['decision_days_left'] ?? null;
    $ackOver = $ackLeft !== null && $ackLeft < 0;
    $decOver = $decLeft !== null && $decLeft < 0;

    $stateCls = match ($dispute['state'] ?? '') {
        'New'              => 'kt-badge-outline',
        'Ack sent'         => 'kt-badge-info',
        'Investigating'    => 'kt-badge-warning',
        'Decision pending' => 'kt-badge-primary',
        'Resolved'         => 'kt-badge-success',
        'Escalated'        => 'kt-badge-destructive',
        default            => 'kt-badge-outline',
    };
@endphp

{{-- Breadcrumb --}}

<div class="kt-container-fixed">
<nav class="flex items-center gap-1.5 text-xs text-muted-foreground mb-4">
    <a href="{{ route('disputes.index') }}" class="hover:text-foreground">Disputes</a>
    <i data-lucide="chevron-right" class="w-3 h-3"></i>
    <span class="text-foreground font-medium">{{ $dispute['ref'] ?? 'Case' }}</span>
</nav>

{{-- Header --}}
<div class="card border border-border rounded-xl px-5 py-4 mb-5">
    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 flex-wrap mb-2">
                <h1 class="text-xl font-semibold">{{ $dispute['ref'] ?? 'DSP-' . $dispute['id'] }}</h1>
                <span class="kt-badge {{ $stateCls }}">{{ $dispute['state'] }}</span>
                <a href="{{ route('deals.show', $dispute['deal_id']) }}"
                   class="kt-badge kt-badge-outline hover:bg-muted transition-colors text-xs font-mono">
                    {{ $dispute['deal_ref'] ?? '—' }}
                </a>
                @if ($ackOver)
                    <span class="kt-badge kt-badge-destructive">⚠ Ack overdue</span>
                @endif
                @if ($decOver)
                    <span class="kt-badge kt-badge-destructive">⚠ Decision overdue</span>
                @endif
            </div>
            <p class="text-sm text-muted-foreground">
                <strong class="text-foreground">{{ $dispute['source'] ?? '—' }}</strong>
                <span class="mx-2">·</span>
                Reason: <strong class="text-foreground">{{ $dispute['reason'] ?? '—' }}</strong>
                <span class="mx-2">·</span>
                Opened: {{ $dispute['created_at'] ?? '—' }}
            </p>
        </div>
        <div class="flex flex-wrap gap-2 items-start">
            @if (($dispute['state'] ?? '') === 'New')
                <button id="btn-send-ack" class="kt-btn kt-btn-outline">
                    <i data-lucide="send" class="w-4 h-4 mr-1"></i>Send acknowledgement
                </button>
            @endif
            <button id="btn-request-evidence" class="kt-btn kt-btn-outline">
                <i data-lucide="paperclip" class="w-4 h-4 mr-1"></i>Request evidence
            </button>
            <button id="btn-decide-outcome" class="kt-btn kt-btn-mono">
                <i data-lucide="gavel" class="w-4 h-4 mr-1"></i>Decide outcome
            </button>
            <div class="relative" x-data="{ open: false }">
                <button @click="open=!open" class="kt-btn kt-btn-ghost">
                    More <i data-lucide="chevron-down" class="w-3.5 h-3.5 ml-1"></i>
                </button>
                <div x-show="open" @click.outside="open=false"
                     class="absolute right-0 mt-1 w-48 bg-background border border-border
                            rounded-lg shadow-xl z-30 py-1 text-sm">
                    <button id="btn-apply-charge" class="w-full text-left flex items-center gap-2 px-3 py-2 hover:bg-muted">
                        <i data-lucide="credit-card" class="w-3.5 h-3.5 opacity-60"></i> Apply charge / refund
                    </button>
                    <button id="btn-escalate" class="w-full text-left flex items-center gap-2 px-3 py-2 hover:bg-muted">
                        <i data-lucide="arrow-up-circle" class="w-3.5 h-3.5 opacity-60"></i> Escalate
                    </button>
                    <div class="border-t border-border my-1"></div>
                    <button id="btn-close-case" class="w-full text-left flex items-center gap-2 px-3 py-2
                                                       hover:bg-muted text-muted-foreground">
                        <i data-lucide="x-circle" class="w-3.5 h-3.5"></i> Close case
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- SLA bar --}}
    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div class="flex items-center gap-3 px-3 py-2 rounded-lg border
                    {{ $ackOver ? 'border-destructive bg-red-50 dark:bg-red-900/20' : 'border-border bg-muted/10' }}">
            <i data-lucide="clock" class="w-4 h-4 shrink-0 {{ $ackOver ? 'text-destructive' : 'text-muted-foreground' }}"></i>
            <div class="flex-1 min-w-0">
                <div class="text-xs text-muted-foreground">Ack SLA (+24h)</div>
                <div class="text-sm font-semibold {{ $ackOver ? 'text-destructive' : '' }}">
                    @if ($ackLeft === null) Not started
                    @elseif ($ackOver) Overdue
                    @else {{ $ackLeft }}h remaining
                    @endif
                </div>
            </div>
            @if (!$ackOver && $ackLeft !== null)
                <div class="w-16 h-1.5 rounded-full bg-muted overflow-hidden shrink-0">
                    <div class="h-full rounded-full bg-primary"
                         style="width: {{ min(100, max(0, (24 - $ackLeft) / 24 * 100)) }}%"></div>
                </div>
            @endif
        </div>
        <div class="flex items-center gap-3 px-3 py-2 rounded-lg border
                    {{ $decOver ? 'border-destructive bg-red-50 dark:bg-red-900/20' : 'border-border bg-muted/10' }}">
            <i data-lucide="gavel" class="w-4 h-4 shrink-0 {{ $decOver ? 'text-destructive' : 'text-muted-foreground' }}"></i>
            <div class="flex-1 min-w-0">
                <div class="text-xs text-muted-foreground">Decision SLA (+5 business days)</div>
                <div class="text-sm font-semibold {{ $decOver ? 'text-destructive' : '' }}">
                    @if ($decLeft === null) Not started
                    @elseif ($decOver) Overdue
                    @else {{ $decLeft }}d remaining
                    @endif
                </div>
            </div>
            @if (!$decOver && $decLeft !== null)
                <div class="w-16 h-1.5 rounded-full bg-muted overflow-hidden shrink-0">
                    <div class="h-full rounded-full bg-primary"
                         style="width: {{ min(100, max(0, (5 - $decLeft) / 5 * 100)) }}%"></div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Tabs --}}
<div class="border-b border-border mb-5 overflow-x-auto">
    <div class="flex gap-1 min-w-max px-1 pt-1">
        @foreach(['Overview','Evidence','Communications','Financials','Activity','History'] as $tab)
            <button data-dispute-tab="{{ Str::slug($tab) }}"
                    class="dispute-tab-btn kt-btn kt-btn-sm
                           {{ $loop->first ? 'kt-btn-mono' : 'kt-btn-ghost' }}">
                {{ $tab }}
            </button>
        @endforeach
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-[1fr,300px] gap-5">

    {{-- Left: tabs --}}
    <div>

        {{-- ── Overview ──────────────────────────────────────────────────── --}}
        <div id="dispute-tab-overview" class="dispute-tab-content space-y-4">

            {{-- Timeline + vehicle --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="card border border-border rounded-xl p-4 space-y-3">
                    <h4 class="text-xs font-semibold text-muted-foreground uppercase tracking-wide">Parties</h4>
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Raised by</span>
                            <strong>{{ $dispute['raised_by'] ?? '—' }}</strong>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Role</span>
                            <span>{{ $dispute['raised_by_role'] ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Against</span>
                            <strong>{{ $dispute['against_party'] ?? '—' }}</strong>
                        </div>
                    </div>
                </div>
                <div class="card border border-border rounded-xl p-4 space-y-3">
                    <h4 class="text-xs font-semibold text-muted-foreground uppercase tracking-wide">Vehicle</h4>
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Vehicle</span>
                            <strong>{{ $dispute['vehicle_title'] ?? '—' }}</strong>
                        </div>
                        @if ($dispute['vrm'] ?? null)
                            <div class="flex justify-between">
                                <span class="text-muted-foreground">VRM</span>
                                <span class="font-mono bg-muted px-1.5 py-0.5 rounded">{{ $dispute['vrm'] }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Agreed price</span>
                            <strong>{{ $dispute['deal_price'] ? '£'.number_format($dispute['deal_price']) : '—' }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Inspection reports --}}
            @if (!empty($dispute['inspection_reports']))
                <div class="card border border-border rounded-xl p-4">
                    <h4 class="text-xs font-semibold text-muted-foreground uppercase tracking-wide mb-3">
                        Inspection reports
                    </h4>
                    <div class="space-y-2">
                        @foreach ($dispute['inspection_reports'] as $rpt)
                            <div class="flex items-center gap-3 px-3 py-2.5 rounded-lg border border-border bg-muted/10">
                                <i data-lucide="clipboard-list" class="w-4 h-4 text-muted-foreground shrink-0"></i>
                                <div class="flex-1 text-sm">{{ $rpt['name'] ?? 'Report' }}</div>
                                <span class="text-xs text-muted-foreground">{{ $rpt['date'] ?? '' }}</span>
                                <a href="{{ $rpt['url'] ?? '#' }}" target="_blank"
                                   class="kt-btn kt-btn-ghost kt-btn-sm">View</a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- ── Evidence ──────────────────────────────────────────────────── --}}
        <div id="dispute-tab-evidence" class="dispute-tab-content hidden space-y-4">
            <div class="card border border-border rounded-xl overflow-hidden">
                <div class="px-4 py-3 border-b border-border bg-muted/20 flex items-center justify-between">
                    <h4 class="text-sm font-semibold">Evidence files</h4>
                    <button id="btn-upload-evidence" class="kt-btn kt-btn-mono kt-btn-sm">
                        <i data-lucide="upload" class="w-3.5 h-3.5 mr-1"></i>Upload
                    </button>
                </div>
                <div class="p-4">
                    @if (!empty($dispute['evidence']))
                        <div class="space-y-2">
                            @foreach ($dispute['evidence'] as $ev)
                                <div class="flex items-center gap-3 px-3 py-2.5 rounded-lg border border-border bg-muted/10
                                            hover:bg-muted/30 transition-colors">
                                    <i data-lucide="{{ str_contains(strtolower($ev['name'] ?? ''), 'photo') ? 'image' : 'file-text' }}"
                                       class="w-4 h-4 text-muted-foreground shrink-0"></i>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-medium truncate">{{ $ev['name'] ?? 'File' }}</div>
                                        <div class="text-xs text-muted-foreground">
                                            Added by {{ $ev['added_by'] ?? '—' }} · {{ $ev['added_at'] ?? '' }}
                                        </div>
                                    </div>
                                    @if ($ev['notes'] ?? null)
                                        <span class="text-xs text-muted-foreground max-w-[120px] truncate"
                                              title="{{ $ev['notes'] }}">
                                            {{ $ev['notes'] }}
                                        </span>
                                    @endif
                                    <a href="{{ $ev['url'] ?? '#' }}" target="_blank"
                                       class="kt-btn kt-btn-ghost kt-btn-sm shrink-0">
                                        <i data-lucide="download" class="w-3.5 h-3.5"></i>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-sm text-muted-foreground">
                            <i data-lucide="folder-open" class="w-8 h-8 mx-auto mb-2 opacity-30"></i>
                            No evidence uploaded yet.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── Communications ────────────────────────────────────────────── --}}
        <div id="dispute-tab-communications" class="dispute-tab-content hidden">
            @include('deals.partials._comms_tab', ['deal' => $dispute])
        </div>

        {{-- ── Financials ────────────────────────────────────────────────── --}}
        <div id="dispute-tab-financials" class="dispute-tab-content hidden space-y-4">

            {{-- Proposed outcome --}}
            @if ($dispute['outcome'] ?? null)
                <div class="card border border-border rounded-xl p-4 space-y-3">
                    <h4 class="text-sm font-semibold flex items-center gap-2">
                        <i data-lucide="gavel" class="w-4 h-4 opacity-60"></i>Outcome decision
                    </h4>
                    @php
                        $outcomeCls = match ($dispute['outcome']['type'] ?? '') {
                            'price_adjustment' => 'kt-badge-warning',
                            'cancel_rerun'     => 'kt-badge-destructive',
                            'vendor_charge'    => 'kt-badge-info',
                            'partial_refund'   => 'kt-badge-primary',
                            'note_only'        => 'kt-badge-outline',
                            default            => 'kt-badge-outline',
                        };
                        $outcomeLabels = [
                            'price_adjustment' => 'Price adjustment',
                            'cancel_rerun'     => 'Cancel & re-run',
                            'vendor_charge'    => 'Vendor charge',
                            'partial_refund'   => 'Partial refund',
                            'note_only'        => 'Note only',
                        ];
                    @endphp
                    <div class="flex items-center gap-3 flex-wrap">
                        <span class="kt-badge {{ $outcomeCls }}">
                            {{ $outcomeLabels[$dispute['outcome']['type'] ?? ''] ?? $dispute['outcome']['type'] ?? '—' }}
                        </span>
                        @if ($dispute['outcome']['amount'] ?? null)
                            <span class="text-lg font-bold">
                                £{{ number_format($dispute['outcome']['amount']) }}
                            </span>
                        @endif
                    </div>
                    @if ($dispute['outcome']['notes'] ?? null)
                        <div class="text-sm text-muted-foreground bg-muted/30 border border-border
                                    rounded-lg px-3 py-2">
                            {{ $dispute['outcome']['notes'] }}
                        </div>
                    @endif
                    <div class="text-xs text-muted-foreground">
                        Decided by {{ $dispute['outcome']['decided_by'] ?? '—' }}
                        · {{ $dispute['outcome']['decided_at'] ?? '' }}
                    </div>
                </div>
            @else
                <div class="card border border-border rounded-xl p-6 text-center text-sm text-muted-foreground">
                    <i data-lucide="gavel" class="w-8 h-8 mx-auto mb-2 opacity-30"></i>
                    No outcome decided yet. Use <strong>Decide outcome</strong> above.
                </div>
            @endif

            {{-- Wallet / charge impact --}}
            @if (!empty($dispute['financial_postings']))
                <div class="card border border-border rounded-xl overflow-hidden">
                    <div class="px-4 py-3 border-b border-border bg-muted/20">
                        <h4 class="text-sm font-semibold">Financial postings</h4>
                    </div>
                    <table class="w-full text-xs">
                        <thead class="bg-muted/40">
                            <tr>
                                @foreach(['Date','Type','Party','Amount','Ref','Status'] as $col)
                                    <th class="p-3 text-left font-semibold text-muted-foreground uppercase tracking-wide">{{ $col }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border bg-background">
                            @foreach ($dispute['financial_postings'] as $fp)
                                <tr class="hover:bg-muted/20 transition-colors">
                                    <td class="p-3 text-muted-foreground">{{ $fp['date'] ?? '—' }}</td>
                                    <td class="p-3">
                                        <span class="kt-badge kt-badge-outline kt-badge-sm">{{ $fp['type'] ?? '—' }}</span>
                                    </td>
                                    <td class="p-3">{{ $fp['party'] ?? '—' }}</td>
                                    <td class="p-3 font-semibold">£{{ number_format($fp['amount'] ?? 0) }}</td>
                                    <td class="p-3 font-mono">{{ $fp['ref'] ?? '—' }}</td>
                                    <td class="p-3">
                                        <span class="kt-badge {{ ($fp['status'] ?? '') === 'Applied' ? 'kt-badge-success' : 'kt-badge-warning' }} kt-badge-sm">
                                            {{ $fp['status'] ?? '—' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- ── Activity ──────────────────────────────────────────────────── --}}
        <div id="dispute-tab-activity" class="dispute-tab-content hidden">
            @include('deals.partials._activity_tab', ['deal' => ['activity' => $dispute['activity'] ?? []]])
        </div>

        {{-- ── History (audit) ──────────────────────────────────────────── --}}
        <div id="dispute-tab-history" class="dispute-tab-content hidden">
            @include('deals.partials._history_tab', ['deal' => ['audit_log' => $dispute['audit_log'] ?? []]])
        </div>
    </div>

    {{-- Right panel --}}
    <aside class="space-y-4">

        {{-- Summary --}}
        <div class="card border border-border rounded-xl p-4 space-y-3">
            <h3 class="text-xs font-semibold text-muted-foreground uppercase tracking-wide">Summary</h3>
            <div class="space-y-1.5 text-xs">
                @php
                    $rows = [
                        'Case ref'     => $dispute['ref'] ?? '—',
                        'Deal'         => $dispute['deal_ref'] ?? '—',
                        'Source'       => $dispute['source'] ?? '—',
                        'Opened'       => $dispute['created_at'] ?? '—',
                        'Owner'        => $dispute['owner'] ?? 'Unassigned',
                    ];
                @endphp
                @foreach ($rows as $label => $value)
                    <div class="flex justify-between gap-2">
                        <span class="text-muted-foreground shrink-0">{{ $label }}</span>
                        <span class="font-medium text-right truncate">{{ $value }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Edit essentials --}}
        <div class="card border border-border rounded-xl p-4">
            <h3 class="text-xs font-semibold text-muted-foreground uppercase tracking-wide mb-3">
                Edit essentials
            </h3>
            <form method="POST" action="{{ route('disputes.update', $dispute['id']) }}" class="space-y-3">
                @csrf @method('PATCH')
                <div>
                    <label class="block text-xs font-medium mb-1">Owner</label>
                    <select name="owner" class="kt-input w-full">
                        <option value="">Unassigned</option>
                        @foreach ($owners ?? [] as $o)
                            <option @selected(($dispute['owner'] ?? '') === $o)>{{ $o }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1">State</label>
                    <select name="state" class="kt-input w-full">
                        @foreach(['New','Ack sent','Investigating','Decision pending','Resolved','Escalated'] as $s)
                            <option @selected(($dispute['state'] ?? '') === $s)>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="kt-btn kt-btn-mono w-full">Save</button>
            </form>
        </div>

        {{-- Notes --}}
        <div class="card border border-border rounded-xl p-4">
            <h3 class="text-xs font-semibold text-muted-foreground uppercase tracking-wide mb-2">
                Internal notes
            </h3>
            <textarea id="dispute-notes" class="kt-input w-full text-xs" rows="4"
                      placeholder="Notes visible to the team only…">{{ $dispute['notes'] ?? '' }}</textarea>
            <button id="btn-save-notes" class="kt-btn kt-btn-outline kt-btn-sm mt-2 w-full">
                Save notes
            </button>
        </div>
    </aside>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════ --}}
{{-- MODALS                                                                  --}}
{{-- ═══════════════════════════════════════════════════════════════════════ --}}

{{-- Decide outcome --}}
<div id="modal-decide-outcome" class="modal-overlay fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="modal-backdrop fixed inset-0 bg-black/50 backdrop-blur-sm"></div>
    <div role="dialog" aria-modal="true"
         class="modal-box relative w-full max-w-lg mx-auto card border border-border rounded-2xl shadow-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-border flex items-center justify-between bg-muted/20">
            <h2 class="text-base font-semibold flex items-center gap-2">
                <i data-lucide="gavel" class="w-4 h-4"></i> Decide outcome
            </h2>
            <button class="modal-close kt-btn kt-btn-icon kt-btn-ghost">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <div class="px-6 py-4 space-y-4">
            <div>
                <label class="block text-xs font-medium mb-2">
                    Outcome type <span class="text-destructive">*</span>
                </label>
                <div class="grid grid-cols-1 gap-2" id="outcome-type-list">
                    @php
                        $outcomeOptions = [
                            ['value' => 'price_adjustment', 'label' => 'Price adjustment',
                             'desc'  => 'Adjust the agreed deal price up or down'],
                            ['value' => 'cancel_rerun',     'label' => 'Cancel & re-run',
                             'desc'  => 'Cancel the deal and re-list the vehicle'],
                            ['value' => 'vendor_charge',    'label' => 'Vendor charge',
                             'desc'  => 'Charge the vendor wallet for a breach'],
                            ['value' => 'partial_refund',   'label' => 'Partial refund',
                             'desc'  => 'Issue a partial refund to the relevant party'],
                            ['value' => 'note_only',        'label' => 'Note only',
                             'desc'  => 'Record a note; no financial action taken'],
                        ];
                    @endphp
                    @foreach ($outcomeOptions as $opt)
                        <label class="flex items-start gap-3 px-3 py-2.5 rounded-lg border border-border
                                      cursor-pointer hover:bg-muted/30 transition-colors
                                      has-[:checked]:border-primary has-[:checked]:bg-primary/5">
                            <input type="radio" name="outcome_type" value="{{ $opt['value'] }}"
                                   class="form-radio mt-0.5 shrink-0"
                                   onchange="toggleOutcomeAmount(this.value)" />
                            <div>
                                <div class="text-sm font-medium">{{ $opt['label'] }}</div>
                                <div class="text-xs text-muted-foreground">{{ $opt['desc'] }}</div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Amount (shown for financial outcomes) --}}
            <div id="outcome-amount-wrap" class="hidden">
                <label class="block text-xs font-medium mb-1">
                    Amount (£) <span class="text-destructive">*</span>
                </label>
                <input id="outcome-amount" type="number" step="1" class="kt-input w-full"
                       placeholder="e.g. 500" />
            </div>

            <div>
                <label class="block text-xs font-medium mb-1">
                    Decision notes <span class="text-destructive">*</span>
                </label>
                <textarea id="outcome-notes" class="kt-input w-full" rows="3"
                          placeholder="Provide full reasoning for the outcome…"></textarea>
            </div>

            <div class="rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200
                        dark:border-amber-700 p-3 text-xs text-amber-800 dark:text-amber-300">
                <i data-lucide="info" class="w-3.5 h-3.5 inline mr-1"></i>
                Financial outcomes (charge / refund / price adjustment) will post entries to
                Wallet and Charges, and update the linked Deal.
            </div>

            <div class="flex gap-2 justify-end pt-2 border-t border-border">
                <button class="modal-close kt-btn kt-btn-ghost">Cancel</button>
                <button id="btn-confirm-outcome" class="kt-btn kt-btn-mono">Apply outcome</button>
            </div>
        </div>
    </div>
</div>

{{-- Apply charge / refund --}}
<div id="modal-apply-charge" class="modal-overlay fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="modal-backdrop fixed inset-0 bg-black/50 backdrop-blur-sm"></div>
    <div class="modal-box relative w-full max-w-md mx-auto card border border-border rounded-2xl shadow-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-border flex items-center justify-between bg-muted/20">
            <h2 class="text-base font-semibold">Apply charge / refund</h2>
            <button class="modal-close kt-btn kt-btn-icon kt-btn-ghost">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <div class="px-6 py-4 space-y-4">
            <div>
                <label class="block text-xs font-medium mb-1">Type</label>
                <select id="charge-type" class="kt-input w-full">
                    <option value="vendor_charge">Vendor charge</option>
                    <option value="partial_refund">Partial refund</option>
                    <option value="adjustment">Price adjustment</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium mb-1">Amount (£) <span class="text-destructive">*</span></label>
                <input id="charge-amount" type="number" step="1" class="kt-input w-full" placeholder="500" />
            </div>
            <div>
                <label class="block text-xs font-medium mb-1">Party</label>
                <select id="charge-party" class="kt-input w-full">
                    <option>{{ $dispute['buyer_name'] ?? 'Buyer' }}</option>
                    <option>{{ $dispute['seller_name'] ?? 'Seller' }}</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium mb-1">Notes <span class="text-destructive">*</span></label>
                <textarea id="charge-notes" class="kt-input w-full" rows="2"></textarea>
            </div>
            <div class="flex gap-2 justify-end pt-2 border-t border-border">
                <button class="modal-close kt-btn kt-btn-ghost">Cancel</button>
                <button id="btn-confirm-charge" class="kt-btn kt-btn-mono">Apply</button>
            </div>
        </div>
    </div>
</div>

<div id="toast-container" class="fixed bottom-5 right-5 z-[200] flex flex-col gap-2 pointer-events-none"></div>
@include('partials._phase4_js')
</div>

<script>
(function () {
    const { toast, openModal, closeModal, auditEvent } = window.CS4;

    /* Tab switching */
    document.addEventListener('click', e => {
        const btn = e.target.closest('.dispute-tab-btn');
        if (!btn) return;
        const tab = btn.dataset.disputeTab;
        document.querySelectorAll('.dispute-tab-btn').forEach(b => {
            b.classList.toggle('kt-btn-mono', b.dataset.disputeTab === tab);
            b.classList.toggle('kt-btn-ghost', b.dataset.disputeTab !== tab);
        });
        document.querySelectorAll('.dispute-tab-content').forEach(c =>
            c.classList.toggle('hidden', c.id !== 'dispute-tab-' + tab)
        );
    });

    /* Header action buttons */
    document.getElementById('btn-send-ack')?.addEventListener('click', () => {
        auditEvent('dispute_ack_sent', { id: '{{ $dispute["id"] }}' });
        toast('Acknowledgement sent to parties.', 'success');
    });

    document.getElementById('btn-request-evidence')?.addEventListener('click', () => {
        auditEvent('dispute_evidence_requested', { id: '{{ $dispute["id"] }}' });
        toast('Evidence request sent.', 'info');
    });

    document.getElementById('btn-decide-outcome')?.addEventListener('click', () =>
        openModal('modal-decide-outcome')
    );

    document.getElementById('btn-apply-charge')?.addEventListener('click', () =>
        openModal('modal-apply-charge')
    );

    document.getElementById('btn-escalate')?.addEventListener('click', () => {
        auditEvent('dispute_escalated', { id: '{{ $dispute["id"] }}' });
        toast('Case escalated.', 'warning');
    });

    document.getElementById('btn-close-case')?.addEventListener('click', () => {
        if (window.confirm('Close this dispute case? Ensure all actions are complete.')) {
            auditEvent('dispute_closed', { id: '{{ $dispute["id"] }}' });
            toast('Case closed.', 'success');
        }
    });

    document.getElementById('btn-save-notes')?.addEventListener('click', () => {
        auditEvent('dispute_notes_updated', { id: '{{ $dispute["id"] }}' });
        toast('Notes saved.', 'success');
    });

    document.getElementById('btn-upload-evidence')?.addEventListener('click', () =>
        toast('Upload UI — wire to a real file input in production.', 'info')
    );

    /* Outcome amount visibility */
    window.toggleOutcomeAmount = function (type) {
        const wrap = document.getElementById('outcome-amount-wrap');
        const financial = ['price_adjustment','vendor_charge','partial_refund'];
        wrap?.classList.toggle('hidden', !financial.includes(type));
    };

    /* Confirm outcome */
    document.getElementById('btn-confirm-outcome')?.addEventListener('click', () => {
        const type  = document.querySelector('input[name="outcome_type"]:checked')?.value;
        const notes = document.getElementById('outcome-notes')?.value?.trim();
        if (!type)  { toast('Select an outcome type.', 'warning'); return; }
        if (!notes) { toast('Decision notes required.', 'warning'); return; }
        const amount = document.getElementById('outcome-amount')?.value;
        auditEvent('dispute_decision_recorded', { id: '{{ $dispute["id"] }}', type, amount, notes });
        toast('Outcome applied. Financial postings created.', 'success');
        closeModal('modal-decide-outcome');
    });

    /* Confirm charge */
    document.getElementById('btn-confirm-charge')?.addEventListener('click', () => {
        const amount = document.getElementById('charge-amount')?.value;
        const notes  = document.getElementById('charge-notes')?.value?.trim();
        if (!amount || !notes) { toast('Amount and notes required.', 'warning'); return; }
        auditEvent('financial_adjustment_applied', { id: '{{ $dispute["id"] }}', amount });
        toast('Charge / refund applied to wallet.', 'success');
        closeModal('modal-apply-charge');
    });
})();
</script>

@endsection
