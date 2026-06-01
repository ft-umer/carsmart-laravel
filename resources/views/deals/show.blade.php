{{-- resources/views/deals/show.blade.php --}}
{{-- Phase 4 — D2: Deal Detail (full workspace) --}}
@extends('layouts.app')
@section('title', ($deal['ref'] ?? 'Deal') . ' — Carsmart')

@section('content')

@php
    $objDays  = $deal['objection_days_left'] ?? null;
    $isUrgent = $objDays !== null && $objDays <= 2 && $objDays >= 0;
    $stateCls = match ($deal['state'] ?? '') {
        'Pending'              => 'kt-badge-outline',
        'Collection scheduled' => 'kt-badge-info',
        'Handover complete'    => 'kt-badge-primary',
        'Awaiting payout'      => 'kt-badge-warning',
        'Closed'               => 'kt-badge-success',
        'Cancelled'            => 'kt-badge-destructive',
        default                => 'kt-badge-outline',
    };
@endphp
<div class="kt-container-fixed">


{{-- ── Breadcrumb ───────────────────────────────────────────────────────── --}}
<nav class="flex items-center gap-1.5 text-xs text-muted-foreground mb-4">
    <a href="{{ route('deals.index') }}" class="hover:text-foreground">Deals</a>
    <i data-lucide="chevron-right" class="w-3 h-3"></i>
    <span class="text-foreground font-medium">{{ $deal['ref'] }}</span>
</nav>

{{-- ── Deal header ──────────────────────────────────────────────────────── --}}
<div class="card border border-border rounded-xl px-5 py-4 mb-5">
    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">

        {{-- Left: identity --}}
        <div>
            <div class="flex items-center gap-3 flex-wrap mb-2">
                <h1 class="text-xl font-semibold text-foreground">{{ $deal['ref'] }}</h1>
                <span class="kt-badge {{ $stateCls }}">{{ $deal['state'] }}</span>
                @if ($deal['objection_active'] ?? false)
                    <span class="kt-badge kt-badge-warning">
                        Objection {{ $isUrgent ? '⚠ ' : '' }}ends in {{ $objDays }}d
                    </span>
                @endif
                @if ($deal['kyc_verified'] ?? false)
                    <span class="kt-badge kt-badge-success kt-badge-sm">KYC Verified</span>
                @endif
                @if ($deal['card_on_file'] ?? false)
                    <span class="kt-badge kt-badge-primary kt-badge-sm">Card on file ✔</span>
                @endif
            </div>
            <div class="text-sm text-muted-foreground">
                <span class="font-medium text-foreground">{{ $deal['vehicle_title'] }}</span>
                @if ($deal['vrm'] ?? null)
                    <span class="font-mono ml-2 bg-muted px-2 py-0.5 rounded text-xs">{{ $deal['vrm'] }}</span>
                @endif
                <span class="mx-2">·</span>
                Price: <strong class="text-foreground">£{{ number_format($deal['price']) }}</strong>
                <span class="mx-2">·</span>
                Owner: <strong class="text-foreground">{{ $deal['owner'] ?? 'Unassigned' }}</strong>
            </div>
        </div>

        {{-- Right: actions --}}
        <div class="flex flex-wrap gap-2 items-start">
            @if (in_array($deal['state'] ?? '', ['Pending', 'Collection scheduled']))
                <a href="{{ route('logistics.jobs.create', ['deal' => $deal['id']]) }}"
                   class="kt-btn kt-btn-outline">
                    <i data-lucide="truck" class="w-4 h-4 mr-1"></i>Book collection
                </a>
            @endif
            @if (($deal['state'] ?? '') === 'Awaiting payout')
                <button id="btn-request-payout" class="kt-btn kt-btn-primary">
                    <i data-lucide="banknote" class="w-4 h-4 mr-1"></i>Request payout
                </button>
            @endif
            <button id="btn-adjust-price" class="kt-btn kt-btn-outline">
                <i data-lucide="pencil" class="w-4 h-4 mr-1"></i>Adjust price
            </button>
            <a href="{{ route('disputes.create', ['deal' => $deal['id']]) }}"
               class="kt-btn kt-btn-outline">
                <i data-lucide="alert-octagon" class="w-4 h-4 mr-1"></i>Open dispute
            </a>
            <div class="relative" x-data="{ open: false }">
                <button @click="open=!open" class="kt-btn kt-btn-ghost">
                    More <i data-lucide="chevron-down" class="w-3.5 h-3.5 ml-1"></i>
                </button>
                <div x-show="open" @click.outside="open=false"
                     class="absolute right-0 mt-1 w-52 bg-background border border-border
                            rounded-lg shadow-xl z-30 py-1 text-sm">
                    <button id="btn-confirm-handover" class="w-full text-left flex items-center gap-2 px-3 py-2 hover:bg-muted">
                        <i data-lucide="check-circle" class="w-3.5 h-3.5 opacity-60"></i> Confirm handover
                    </button>
                    <button id="btn-gen-settlement" class="w-full text-left flex items-center gap-2 px-3 py-2 hover:bg-muted">
                        <i data-lucide="file-text" class="w-3.5 h-3.5 opacity-60"></i> Generate settlement PDF
                    </button>
                    <div class="border-t border-border my-1"></div>
                    <button id="btn-cancel-rerun" class="w-full text-left flex items-center gap-2 px-3 py-2 hover:bg-muted text-destructive">
                        <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i> Cancel &amp; re-run
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Main layout: Tabs + Right panel ─────────────────────────────────── --}}
<div class="grid grid-cols-1 xl:grid-cols-[1fr,320px] gap-5">

    {{-- ── Left: Tabs ─────────────────────────────────────────────────────── --}}
    <div>
        {{-- Tab bar --}}
        <div class="border-b border-border mb-5 overflow-x-auto">
            <div class="flex gap-1 min-w-max px-1 pt-1">
                @foreach (['Overview','Parties','Commercials','Logistics','Documents','Communications','Financials','Activity','History'] as $tab)
                    <button data-deal-tab="{{ Str::slug($tab) }}"
                            class="deal-tab-btn kt-btn kt-btn-sm
                                   {{ $loop->first ? 'kt-btn-mono' : 'kt-btn-ghost' }}">
                        {{ $tab }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- ── TAB: Overview ─────────────────────────────────────────────── --}}
        <div id="deal-tab-overview" class="deal-tab-content space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                {{-- Status card --}}
                <div class="card border border-border rounded-xl p-4 space-y-2">
                    <h3 class="text-xs font-semibold text-muted-foreground uppercase tracking-wide">Status</h3>
                    <span class="kt-badge {{ $stateCls }} text-sm">{{ $deal['state'] }}</span>
                    <div class="text-xs text-muted-foreground">
                        Created: {{ $deal['created_at'] ?? '—' }}
                    </div>
                </div>
                {{-- Objection timer --}}
                <div class="card border border-border rounded-xl p-4 space-y-2">
                    <h3 class="text-xs font-semibold text-muted-foreground uppercase tracking-wide">Objection window</h3>
                    @if ($objDays !== null && $objDays >= 0)
                        <div class="text-2xl font-bold {{ $isUrgent ? 'text-destructive' : 'text-foreground' }}">
                            {{ $objDays }}d
                        </div>
                        <div class="text-xs text-muted-foreground">
                            Ends: {{ $deal['objection_ends_at'] ?? '—' }}
                        </div>
                        <div class="h-1.5 rounded-full bg-muted overflow-hidden">
                            <div class="h-full rounded-full {{ $isUrgent ? 'bg-destructive' : 'bg-primary' }}"
                                 style="width: {{ min(100, max(0, (7 - ($objDays ?? 0)) / 7 * 100)) }}%"></div>
                        </div>
                    @elseif ($objDays !== null)
                        <div class="text-sm text-muted-foreground">Window expired</div>
                    @else
                        <div class="text-sm text-muted-foreground">No objection window</div>
                    @endif
                </div>
                {{-- Readiness checklist --}}
                <div class="card border border-border rounded-xl p-4 space-y-2">
                    <h3 class="text-xs font-semibold text-muted-foreground uppercase tracking-wide">Readiness</h3>
                    @php
                        $checks = [
                            'KYC verified'     => $deal['kyc_verified'] ?? false,
                            'Card on file'     => $deal['card_on_file'] ?? false,
                            'V5C uploaded'     => $deal['v5c_uploaded'] ?? false,
                            'Photos uploaded'  => $deal['photos_uploaded'] ?? false,
                            'Handover signed'  => $deal['handover_signed'] ?? false,
                        ];
                    @endphp
                    <div class="space-y-1">
                        @foreach ($checks as $label => $done)
                            <div class="flex items-center gap-2 text-xs">
                                <i data-lucide="{{ $done ? 'check-circle' : 'circle' }}"
                                   class="w-3.5 h-3.5 {{ $done ? 'text-green-500' : 'text-muted-foreground' }} shrink-0">
                                </i>
                                <span class="{{ $done ? 'text-foreground' : 'text-muted-foreground' }}">{{ $label }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Source summary --}}
            <div class="card border border-border rounded-xl p-4">
                <h3 class="text-xs font-semibold text-muted-foreground uppercase tracking-wide mb-3">Sale summary</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                    <div><div class="text-xs text-muted-foreground">Source</div><strong>{{ $deal['source'] }}</strong></div>
                    <div><div class="text-xs text-muted-foreground">Reserve</div><strong>{{ $deal['reserve'] ? '£'.number_format($deal['reserve']) : '—' }}</strong></div>
                    <div><div class="text-xs text-muted-foreground">BIN / Offer</div><strong>{{ $deal['bin_price'] ? '£'.number_format($deal['bin_price']) : '—' }}</strong></div>
                    <div><div class="text-xs text-muted-foreground">Agreed</div><strong>£{{ number_format($deal['price']) }}</strong></div>
                </div>
            </div>
        </div>

        {{-- ── TAB: Parties ───────────────────────────────────────────────── --}}
        <div id="deal-tab-parties" class="deal-tab-content hidden space-y-4">
            @include('deals.partials._parties_tab', ['deal' => $deal])
        </div>

        {{-- ── TAB: Commercials ───────────────────────────────────────────── --}}
        <div id="deal-tab-commercials" class="deal-tab-content hidden space-y-4">
            @include('deals.partials._commercials_tab', ['deal' => $deal])
        </div>

        {{-- ── TAB: Logistics ─────────────────────────────────────────────── --}}
        <div id="deal-tab-logistics" class="deal-tab-content hidden space-y-4">
            @include('deals.partials._logistics_tab', ['deal' => $deal])
        </div>

        {{-- ── TAB: Documents ─────────────────────────────────────────────── --}}
        <div id="deal-tab-documents" class="deal-tab-content hidden space-y-4">
            @include('deals.partials._documents_tab', ['deal' => $deal])
        </div>

        {{-- ── TAB: Communications ────────────────────────────────────────── --}}
        <div id="deal-tab-communications" class="deal-tab-content hidden space-y-4">
            @include('deals.partials._comms_tab', ['deal' => $deal])
        </div>

        {{-- ── TAB: Financials ────────────────────────────────────────────── --}}
        <div id="deal-tab-financials" class="deal-tab-content hidden space-y-4">
            @include('deals.partials._financials_tab', ['deal' => $deal])
        </div>

        {{-- ── TAB: Activity ──────────────────────────────────────────────── --}}
        <div id="deal-tab-activity" class="deal-tab-content hidden">
            @include('deals.partials._activity_tab', ['deal' => $deal])
        </div>

        {{-- ── TAB: History (audit) ───────────────────────────────────────── --}}
        <div id="deal-tab-history" class="deal-tab-content hidden">
            @include('deals.partials._history_tab', ['deal' => $deal])
        </div>
    </div>

    {{-- ── Right panel ──────────────────────────────────────────────────── --}}
    <aside class="space-y-4">

        {{-- Summary card --}}
        <div class="card border border-border rounded-xl p-4 space-y-3">
            <h3 class="text-xs font-semibold text-muted-foreground uppercase tracking-wide">Summary</h3>
            <div class="space-y-1.5 text-xs">
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Agreed price</span>
                    <strong>£{{ number_format($deal['price']) }}</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Platform fee</span>
                    <span>{{ isset($deal['platform_fee']) ? '£'.number_format($deal['platform_fee']) : '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-muted-foreground">Deposit hold</span>
                    <span>{{ isset($deal['deposit_hold']) ? '£'.number_format($deal['deposit_hold']) : '—' }}</span>
                </div>
                <div class="border-t border-border pt-1.5 flex justify-between font-semibold">
                    <span>Net payout (est.)</span>
                    @php $net = ($deal['price'] ?? 0) - ($deal['platform_fee'] ?? 0); @endphp
                    <span>£{{ number_format($net) }}</span>
                </div>
            </div>
        </div>

        {{-- Edit essentials --}}
        <div class="card border border-border rounded-xl p-4">
            <h3 class="text-xs font-semibold text-muted-foreground uppercase tracking-wide mb-3">Edit essentials</h3>
            <form method="POST" action="{{ route('deals.update', $deal['id']) }}" class="space-y-3">
                @csrf @method('PATCH')
                <div>
                    <label class="block text-xs font-medium mb-1">Owner</label>
                    <select name="owner" class="kt-input w-full">
                        <option value="">Unassigned</option>
                        @foreach ($owners ?? [] as $o)
                            <option @selected(($deal['owner'] ?? '') === $o)>{{ $o }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1">State</label>
                    <select name="state" class="kt-input w-full">
                        @foreach (['Pending','Collection scheduled','Handover complete','Awaiting payout','Closed','Cancelled'] as $s)
                            <option @selected(($deal['state'] ?? '') === $s)>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="kt-btn kt-btn-mono w-full">Save</button>
            </form>
        </div>

        {{-- Notes --}}
        <div class="card border border-border rounded-xl p-4">
            <h3 class="text-xs font-semibold text-muted-foreground uppercase tracking-wide mb-2">Internal notes</h3>
            <textarea id="deal-notes" class="kt-input w-full text-xs" rows="4"
                      placeholder="Notes visible to the team only…">{{ $deal['notes'] ?? '' }}</textarea>
            <button id="btn-save-notes" class="kt-btn kt-btn-outline kt-btn-sm mt-2 w-full">Save notes</button>
        </div>

        {{-- Transport chat link --}}
        <div class="card border border-border rounded-xl p-4">
            <h3 class="text-xs font-semibold text-muted-foreground uppercase tracking-wide mb-2">Transport chat</h3>
            @if ($deal['job_id'] ?? null)
                <a href="{{ route('logistics.jobs.show', $deal['job_id']) }}#chat"
                   class="kt-btn kt-btn-outline w-full">
                    <i data-lucide="message-circle" class="w-4 h-4 mr-1"></i>Open transport chat
                </a>
            @else
                <p class="text-xs text-muted-foreground mb-2">No logistics job yet.</p>
                <a href="{{ route('logistics.jobs.create', ['deal' => $deal['id']]) }}"
                   class="kt-btn kt-btn-ghost w-full text-xs">Book collection first</a>
            @endif
        </div>
    </aside>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════ --}}
{{-- MODALS                                                                  --}}
{{-- ═══════════════════════════════════════════════════════════════════════ --}}

{{-- Adjust price --}}
<div id="modal-adjust-price" class="modal-overlay fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="modal-backdrop fixed inset-0 bg-black/50 backdrop-blur-sm"></div>
    <div role="dialog" aria-modal="true"
         class="modal-box relative w-full max-w-md mx-auto card border border-border rounded-2xl shadow-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-border flex items-center justify-between bg-muted/20">
            <h2 class="text-base font-semibold">Adjust price</h2>
            <button class="modal-close kt-btn kt-btn-icon kt-btn-ghost"><i data-lucide="x" class="w-4 h-4"></i></button>
        </div>
        <div class="px-6 py-4 space-y-4">
            <div>
                <label class="block text-xs font-medium mb-1">Current price</label>
                <div class="kt-input w-full bg-muted/30 text-muted-foreground">
                    £{{ number_format($deal['price']) }}
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium mb-1">New price (£) <span class="text-destructive">*</span></label>
                <input id="adj-new-price" type="number" step="1" class="kt-input w-full"
                       value="{{ $deal['price'] }}" />
            </div>
            <div>
                <label class="block text-xs font-medium mb-1">Reason <span class="text-destructive">*</span></label>
                <textarea id="adj-reason" class="kt-input w-full" rows="3"
                          placeholder="Explain the price change…"></textarea>
            </div>
            <div class="flex gap-2 justify-end pt-2 border-t border-border">
                <button class="modal-close kt-btn kt-btn-ghost">Cancel</button>
                <button id="btn-confirm-adjust" class="kt-btn kt-btn-mono">Apply adjustment</button>
            </div>
        </div>
    </div>
</div>

{{-- Cancel & re-run --}}
<div id="modal-cancel-rerun" class="modal-overlay fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="modal-backdrop fixed inset-0 bg-black/50 backdrop-blur-sm"></div>
    <div role="dialog" aria-modal="true"
         class="modal-box relative w-full max-w-md mx-auto card border border-border rounded-2xl shadow-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-border flex items-center justify-between bg-muted/20">
            <h2 class="text-base font-semibold text-destructive">Cancel &amp; re-run</h2>
            <button class="modal-close kt-btn kt-btn-icon kt-btn-ghost"><i data-lucide="x" class="w-4 h-4"></i></button>
        </div>
        <div class="px-6 py-4 space-y-4">
            <p class="text-sm text-muted-foreground">This will cancel the deal and re-list the vehicle. Reason required.</p>
            <div>
                <label class="block text-xs font-medium mb-1">Reason <span class="text-destructive">*</span></label>
                <textarea id="cancel-reason" class="kt-input w-full" rows="3"
                          placeholder="e.g. Seller withdrew…"></textarea>
            </div>
            <div class="flex gap-2 justify-end pt-2 border-t border-border">
                <button class="modal-close kt-btn kt-btn-ghost">Back</button>
                <button id="btn-confirm-cancel" class="kt-btn kt-btn-destructive">Confirm cancel</button>
            </div>
        </div>
    </div>
</div>

{{-- Request payout --}}
<div id="modal-request-payout" class="modal-overlay fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="modal-backdrop fixed inset-0 bg-black/50 backdrop-blur-sm"></div>
    <div role="dialog" aria-modal="true"
         class="modal-box relative w-full max-w-lg mx-auto card border border-border rounded-2xl shadow-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-border flex items-center justify-between bg-muted/20">
            <h2 class="text-base font-semibold">Request payout</h2>
            <button class="modal-close kt-btn kt-btn-icon kt-btn-ghost"><i data-lucide="x" class="w-4 h-4"></i></button>
        </div>
        <div class="px-6 py-4 space-y-4">
            <div class="rounded-xl border border-border p-4 bg-muted/30 space-y-1">
                <div class="text-xs text-muted-foreground">Requested amount</div>
                <div class="text-2xl font-bold">£{{ number_format(($deal['price'] ?? 0) - ($deal['platform_fee'] ?? 0)) }}</div>
                <div class="text-xs text-muted-foreground">Net after platform fee</div>
            </div>
            <div>
                <label class="block text-xs font-medium mb-1">Payout destination <span class="text-destructive">*</span></label>
                <select id="payout-dest" class="kt-input w-full">
                    <option value="">Select destination</option>
                    @foreach ($payoutDestinations ?? [] as $dest)
                        <option value="{{ $dest['id'] }}">{{ $dest['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium mb-1">Note <span class="text-destructive">*</span></label>
                <textarea id="payout-note" class="kt-input w-full" rows="3"
                          placeholder="Add context for the approver…"></textarea>
            </div>
            <div class="rounded-lg bg-muted/30 border border-border p-3 text-xs text-muted-foreground">
                <i data-lucide="info" class="w-3.5 h-3.5 inline mr-1"></i>
                Payout requires handover confirmation and mandatory documents. Awaits Admin approval.
            </div>
            <div class="flex gap-2 justify-end pt-2 border-t border-border">
                <button class="modal-close kt-btn kt-btn-ghost">Cancel</button>
                <button id="btn-submit-payout-req" class="kt-btn kt-btn-mono">Submit request</button>
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
    const $ = id => document.getElementById(id) ?? document.querySelector(id);

    /* ── Tab switching ──────────────────────────────────────────────────── */
    document.addEventListener('click', e => {
        const btn = e.target.closest('.deal-tab-btn');
        if (!btn) return;
        const tab = btn.dataset.dealTab;
        document.querySelectorAll('.deal-tab-btn').forEach(b => {
            const a = b.dataset.dealTab === tab;
            b.classList.toggle('kt-btn-mono', a);
            b.classList.toggle('kt-btn-ghost', !a);
        });
        document.querySelectorAll('.deal-tab-content').forEach(c =>
            c.classList.toggle('hidden', c.id !== 'deal-tab-' + tab)
        );
    });

    /* ── Modal triggers ─────────────────────────────────────────────────── */
    $('btn-adjust-price')?.addEventListener('click', () => openModal('modal-adjust-price'));
    $('btn-request-payout')?.addEventListener('click', () => openModal('modal-request-payout'));
    $('btn-cancel-rerun')?.addEventListener('click', () => openModal('modal-cancel-rerun'));
    $('btn-confirm-handover')?.addEventListener('click', () => {
        auditEvent('handover_confirmed', { deal: '{{ $deal["id"] }}' });
        toast('Handover confirmed. Payout can now be requested.', 'success');
    });
    $('btn-gen-settlement')?.addEventListener('click', () => toast('Settlement PDF queued — check documents tab.', 'info'));

    /* ── Adjust price ───────────────────────────────────────────────────── */
    $('btn-confirm-adjust')?.addEventListener('click', () => {
        const price  = $('adj-new-price')?.value;
        const reason = $('adj-reason')?.value?.trim();
        if (!price || !reason) { toast('Price and reason are required.', 'warning'); return; }
        auditEvent('price_adjusted', { deal: '{{ $deal["id"] }}', price, reason });
        toast('Price adjusted to £' + Number(price).toLocaleString(), 'success');
        closeModal('modal-adjust-price');
    });

    /* ── Cancel & re-run ────────────────────────────────────────────────── */
    $('btn-confirm-cancel')?.addEventListener('click', () => {
        const reason = $('cancel-reason')?.value?.trim();
        if (!reason) { toast('Reason is required.', 'warning'); return; }
        auditEvent('deal_cancelled', { deal: '{{ $deal["id"] }}', reason });
        toast('Deal cancelled. Re-run queued.', 'success');
        closeModal('modal-cancel-rerun');
    });

    /* ── Request payout ─────────────────────────────────────────────────── */
    $('btn-submit-payout-req')?.addEventListener('click', () => {
        const dest = $('payout-dest')?.value;
        const note = $('payout-note')?.value?.trim();
        if (!dest || !note) { toast('Destination and note are required.', 'warning'); return; }
        auditEvent('payout_requested', { deal: '{{ $deal["id"] }}', dest, note });
        toast('Payout request submitted. Awaiting Admin approval.', 'success');
        closeModal('modal-request-payout');
    });

    /* ── Save notes ─────────────────────────────────────────────────────── */
    $('btn-save-notes')?.addEventListener('click', () => {
        auditEvent('deal_notes_updated', { deal: '{{ $deal["id"] }}' });
        toast('Notes saved.', 'success');
    });

})();
</script>

@endsection
