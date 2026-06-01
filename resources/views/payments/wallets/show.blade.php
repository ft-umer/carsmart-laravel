{{-- resources/views/payments/wallets/show.blade.php --}}
{{-- Phase 4 — P2: Wallet Detail --}}
@extends('layouts.app')
@section('title', ($wallet['vendor_name'] ?? 'Wallet') . ' — Wallet')

@section('content')

@php
    $statusCls = match ($wallet['status'] ?? '') {
        'Clear'   => 'kt-badge-success',
        'Flagged' => 'kt-badge-warning',
        'Frozen'  => 'kt-badge-destructive',
        default   => 'kt-badge-outline',
    };
@endphp
<div class="kt-container-fixed">


<nav class="flex items-center gap-1.5 text-xs text-muted-foreground mb-4">
    <a href="{{ route('payments.wallets.index') }}" class="hover:text-foreground">Wallets</a>
    <i data-lucide="chevron-right" class="w-3 h-3"></i>
    <span class="text-foreground font-medium">{{ $wallet['vendor_name'] }}</span>
</nav>

{{-- Header --}}
<div class="card border border-border rounded-xl px-5 py-4 mb-5">
    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 flex-wrap mb-1">
                <h1 class="text-xl font-semibold">{{ $wallet['vendor_name'] }}</h1>
                <span class="kt-badge {{ $statusCls }}">{{ $wallet['status'] ?? 'Clear' }}</span>
            </div>
            <div class="flex gap-6 text-sm mt-2 flex-wrap">
                <div>
                    <span class="text-muted-foreground text-xs">Balance</span>
                    <div class="text-2xl font-bold text-foreground">£{{ number_format($wallet['balance'] ?? 0) }}</div>
                </div>
                <div>
                    <span class="text-muted-foreground text-xs">Active holds</span>
                    <div class="text-2xl font-bold text-amber-500">£{{ number_format($wallet['holds'] ?? 0) }}</div>
                </div>
                <div>
                    <span class="text-muted-foreground text-xs">Available</span>
                    <div class="text-2xl font-bold text-green-500">
                        £{{ number_format(max(0, ($wallet['balance'] ?? 0) - ($wallet['holds'] ?? 0))) }}
                    </div>
                </div>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <button id="btn-create-hold" class="kt-btn kt-btn-outline">
                <i data-lucide="lock" class="w-4 h-4 mr-1"></i>Create hold
            </button>
            <button id="btn-request-payout" class="kt-btn kt-btn-mono">
                <i data-lucide="banknote" class="w-4 h-4 mr-1"></i>Request payout
            </button>
        </div>
    </div>
</div>

{{-- Tabs --}}
<div class="border-b border-border mb-5 overflow-x-auto">
    <div class="flex gap-1 min-w-max px-1 pt-1">
        @foreach (['Movements','Holds','Payouts','Funding','Statements','History'] as $tab)
            <button data-wallet-tab="{{ Str::slug($tab) }}"
                    class="wallet-tab-btn kt-btn kt-btn-sm {{ $loop->first ? 'kt-btn-mono' : 'kt-btn-ghost' }}">
                {{ $tab }}
            </button>
        @endforeach
    </div>
</div>

{{-- ── TAB: Movements ─────────────────────────────────────────────────────── --}}
<div id="wallet-tab-movements" class="wallet-tab-content">
    <div class="card border border-border rounded-xl overflow-hidden">
        <div class="px-4 py-3 border-b border-border bg-muted/20 flex items-center justify-between">
            <h3 class="text-sm font-semibold">Ledger movements</h3>
            <button onclick="window.CS4.toast('Movements exported.','info')" class="kt-btn kt-btn-outline kt-btn-sm">
                Export CSV
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-muted/40">
                    <tr>
                        @foreach (['Date','Ref','Type','Description','Amount','Balance after'] as $col)
                            <th class="p-3 text-left font-semibold text-muted-foreground uppercase tracking-wide">{{ $col }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-border bg-background">
                    @forelse ($wallet['movements'] ?? [] as $mv)
                        @php
                            $typeCls = match ($mv['type'] ?? '') {
                                'hold'       => 'kt-badge-warning',
                                'capture'    => 'kt-badge-success',
                                'refund'     => 'kt-badge-info',
                                'fee'        => 'kt-badge-outline',
                                'payout'     => 'kt-badge-primary',
                                'adjustment' => 'kt-badge-outline',
                                default      => 'kt-badge-outline',
                            };
                            $isDebit = in_array($mv['type'] ?? '', ['hold','fee','payout']);
                        @endphp
                        <tr class="hover:bg-muted/20 transition-colors">
                            <td class="p-3 text-muted-foreground whitespace-nowrap">{{ $mv['date'] ?? '—' }}</td>
                            <td class="p-3 font-mono">{{ $mv['ref'] ?? '—' }}</td>
                            <td class="p-3"><span class="kt-badge {{ $typeCls }} kt-badge-sm">{{ $mv['type'] ?? '—' }}</span></td>
                            <td class="p-3">{{ $mv['description'] ?? '—' }}</td>
                            <td class="p-3 font-semibold {{ $isDebit ? 'text-destructive' : 'text-green-600 dark:text-green-400' }}">
                                {{ $isDebit ? '-' : '+' }}£{{ number_format(abs($mv['amount'] ?? 0)) }}
                            </td>
                            <td class="p-3">£{{ number_format($mv['balance_after'] ?? 0) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-8 text-center text-muted-foreground">No movements.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ── TAB: Holds ─────────────────────────────────────────────────────────── --}}
<div id="wallet-tab-holds" class="wallet-tab-content hidden">
    <div class="card border border-border rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-muted/40">
                    <tr>
                        @foreach (['Deal ref','Amount','Reason','Expiry','Status','Actions'] as $col)
                            <th class="p-3 text-left font-semibold text-muted-foreground uppercase tracking-wide">{{ $col }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-border bg-background">
                    @forelse ($wallet['holds_list'] ?? [] as $hold)
                        <tr class="hover:bg-muted/20 transition-colors">
                            <td class="p-3 font-mono">{{ $hold['deal_ref'] ?? '—' }}</td>
                            <td class="p-3 font-semibold">£{{ number_format($hold['amount'] ?? 0) }}</td>
                            <td class="p-3">{{ $hold['reason'] ?? '—' }}</td>
                            <td class="p-3 text-muted-foreground">{{ $hold['expiry'] ?? '—' }}</td>
                            <td class="p-3"><span class="kt-badge kt-badge-warning kt-badge-sm">{{ $hold['status'] ?? 'Active' }}</span></td>
                            <td class="p-3">
                                <div class="flex gap-1.5">
                                    <button data-action="release-hold" data-id="{{ $hold['id'] }}"
                                            class="kt-btn kt-btn-outline kt-btn-sm">Release</button>
                                    <button data-action="capture-hold" data-id="{{ $hold['id'] }}"
                                            class="kt-btn kt-btn-mono kt-btn-sm">Capture</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-8 text-center text-muted-foreground">No active holds.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ── TAB: Payouts ───────────────────────────────────────────────────────── --}}
<div id="wallet-tab-payouts" class="wallet-tab-content hidden">
    @include('payments.partials._wallet_payouts', ['wallet' => $wallet])
</div>

{{-- ── TAB: Funding ───────────────────────────────────────────────────────── --}}
<div id="wallet-tab-funding" class="wallet-tab-content hidden">
    @include('payments.partials._wallet_funding', ['wallet' => $wallet])
</div>

{{-- ── TAB: Statements ────────────────────────────────────────────────────── --}}
<div id="wallet-tab-statements" class="wallet-tab-content hidden">
    <div class="space-y-2">
        @forelse ($wallet['statements'] ?? [] as $stmt)
            <div class="card border border-border rounded-xl px-4 py-3 flex items-center gap-4">
                <i data-lucide="file-text" class="w-5 h-5 text-muted-foreground shrink-0"></i>
                <div class="flex-1">
                    <div class="text-sm font-medium">{{ $stmt['label'] ?? 'Statement' }}</div>
                    <div class="text-xs text-muted-foreground">{{ $stmt['period'] ?? '' }}</div>
                </div>
                <a href="{{ $stmt['pdf_url'] ?? '#' }}" class="kt-btn kt-btn-outline kt-btn-sm">PDF</a>
                <a href="{{ $stmt['csv_url'] ?? '#' }}" class="kt-btn kt-btn-ghost kt-btn-sm">CSV</a>
            </div>
        @empty
            <div class="card border border-border rounded-xl p-8 text-center text-sm text-muted-foreground">
                No statements generated yet.
            </div>
        @endforelse
    </div>
</div>

{{-- ── TAB: History ───────────────────────────────────────────────────────── --}}
<div id="wallet-tab-history" class="wallet-tab-content hidden">
    @include('deals.partials._history_tab', ['deal' => ['audit_log' => $wallet['audit_log'] ?? []]])
</div>

{{-- ═══════════════════════════════════════════════════════════════════════ --}}
{{-- MODALS                                                                  --}}
{{-- ═══════════════════════════════════════════════════════════════════════ --}}

{{-- Create hold --}}
<div id="modal-create-hold" class="modal-overlay fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="modal-backdrop fixed inset-0 bg-black/50 backdrop-blur-sm"></div>
    <div class="modal-box relative w-full max-w-md mx-auto card border border-border rounded-2xl shadow-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-border flex items-center justify-between bg-muted/20">
            <h2 class="text-base font-semibold">Create hold</h2>
            <button class="modal-close kt-btn kt-btn-icon kt-btn-ghost"><i data-lucide="x" class="w-4 h-4"></i></button>
        </div>
        <div class="px-6 py-4 space-y-4">
            <div>
                <label class="block text-xs font-medium mb-1">Deal ref</label>
                <input id="hold-deal-ref" class="kt-input w-full" placeholder="DEL-3112" />
            </div>
            <div>
                <label class="block text-xs font-medium mb-1">Amount (£) <span class="text-destructive">*</span></label>
                <input id="hold-amount" type="number" step="1" class="kt-input w-full" placeholder="500" />
            </div>
            <div>
                <label class="block text-xs font-medium mb-1">Reason <span class="text-destructive">*</span></label>
                <input id="hold-reason" class="kt-input w-full" placeholder="Deposit for DEL-3112" />
            </div>
            <div>
                <label class="block text-xs font-medium mb-1">Expiry date</label>
                <input id="hold-expiry" type="date" class="kt-input w-full" />
            </div>
            <div class="flex gap-2 justify-end pt-2 border-t border-border">
                <button class="modal-close kt-btn kt-btn-ghost">Cancel</button>
                <button id="btn-confirm-hold" class="kt-btn kt-btn-mono">Create hold</button>
            </div>
        </div>
    </div>
</div>

{{-- Request payout --}}
<div id="modal-wallet-payout" class="modal-overlay fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="modal-backdrop fixed inset-0 bg-black/50 backdrop-blur-sm"></div>
    <div class="modal-box relative w-full max-w-md mx-auto card border border-border rounded-2xl shadow-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-border flex items-center justify-between bg-muted/20">
            <h2 class="text-base font-semibold">Request payout</h2>
            <button class="modal-close kt-btn kt-btn-icon kt-btn-ghost"><i data-lucide="x" class="w-4 h-4"></i></button>
        </div>
        <div class="px-6 py-4 space-y-4">
            <div>
                <label class="block text-xs font-medium mb-1">Amount (£) <span class="text-destructive">*</span></label>
                <input id="payout-amount" type="number" step="1" class="kt-input w-full"
                       max="{{ ($wallet['balance'] ?? 0) - ($wallet['holds'] ?? 0) }}"
                       placeholder="{{ max(0, ($wallet['balance'] ?? 0) - ($wallet['holds'] ?? 0)) }}" />
                <p class="text-xs text-muted-foreground mt-1">
                    Available: £{{ number_format(max(0, ($wallet['balance'] ?? 0) - ($wallet['holds'] ?? 0))) }}
                </p>
            </div>
            <div>
                <label class="block text-xs font-medium mb-1">Destination <span class="text-destructive">*</span></label>
                <select id="payout-destination" class="kt-input w-full">
                    <option value="">Select bank / wallet</option>
                    @foreach ($wallet['payout_destinations'] ?? [] as $dest)
                        <option value="{{ $dest['id'] }}">{{ $dest['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium mb-1">Note <span class="text-destructive">*</span></label>
                <textarea id="payout-note-wallet" class="kt-input w-full" rows="3"
                          placeholder="Provide context for the approver…"></textarea>
            </div>
            <div class="rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 p-3 text-xs text-amber-800 dark:text-amber-300">
                <i data-lucide="info" class="w-3.5 h-3.5 inline mr-1"></i>
                Payout requests require Admin approval before funds are released.
            </div>
            <div class="flex gap-2 justify-end pt-2 border-t border-border">
                <button class="modal-close kt-btn kt-btn-ghost">Cancel</button>
                <button id="btn-confirm-wallet-payout" class="kt-btn kt-btn-mono">Submit request</button>
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
        const btn = e.target.closest('.wallet-tab-btn');
        if (!btn) return;
        const tab = btn.dataset.walletTab;
        document.querySelectorAll('.wallet-tab-btn').forEach(b => {
            b.classList.toggle('kt-btn-mono', b.dataset.walletTab === tab);
            b.classList.toggle('kt-btn-ghost', b.dataset.walletTab !== tab);
        });
        document.querySelectorAll('.wallet-tab-content').forEach(c =>
            c.classList.toggle('hidden', c.id !== 'wallet-tab-' + tab)
        );
    });

    /* Modal triggers */
    document.getElementById('btn-create-hold')?.addEventListener('click', () => openModal('modal-create-hold'));
    document.getElementById('btn-request-payout')?.addEventListener('click', () => openModal('modal-wallet-payout'));

    /* Create hold */
    document.getElementById('btn-confirm-hold')?.addEventListener('click', () => {
        const amount = document.getElementById('hold-amount')?.value;
        const reason = document.getElementById('hold-reason')?.value?.trim();
        if (!amount || !reason) { toast('Amount and reason required.', 'warning'); return; }
        auditEvent('wallet_hold_created', { amount, reason });
        toast('Hold of £' + Number(amount).toLocaleString() + ' created.', 'success');
        closeModal('modal-create-hold');
    });

    /* Request payout */
    document.getElementById('btn-confirm-wallet-payout')?.addEventListener('click', () => {
        const amount = document.getElementById('payout-amount')?.value;
        const dest   = document.getElementById('payout-destination')?.value;
        const note   = document.getElementById('payout-note-wallet')?.value?.trim();
        if (!amount || !dest || !note) { toast('All fields required.', 'warning'); return; }
        auditEvent('wallet_payout_requested', { amount, dest });
        toast('Payout request submitted. Awaiting Admin approval.', 'success');
        closeModal('modal-wallet-payout');
    });

    /* Hold actions */
    document.addEventListener('click', e => {
        const btn = e.target.closest('[data-action]');
        if (!btn) return;
        if (btn.dataset.action === 'release-hold') {
            auditEvent('wallet_hold_released', { id: btn.dataset.id });
            toast('Hold released.', 'success');
        }
        if (btn.dataset.action === 'capture-hold') {
            auditEvent('wallet_hold_captured', { id: btn.dataset.id });
            toast('Hold captured.', 'success');
        }
    });
})();
</script>

@endsection
