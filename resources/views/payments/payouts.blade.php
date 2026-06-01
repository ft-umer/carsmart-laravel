{{-- resources/views/payments/payouts.blade.php --}}
{{-- Phase 4 — P4: Payments → Payouts (Approvals) --}}
@extends('layouts.app')
@section('title', 'Payout Approvals — Payments')

@section('content')

    @include('partials._retention_banner')

    <div class="kt-container-fixed">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
            <div class="flex items-center gap-3 flex-wrap">
                <h1 class="text-xl font-semibold text-foreground">Payout Approvals</h1>
                @php $pendingCount = collect($payouts)->where('status','Pending')->count(); @endphp
                @if ($pendingCount > 0)
                    <span class="kt-badge kt-badge-warning kt-badge-sm">
                        {{ $pendingCount }} pending
                    </span>
                @endif
            </div>
            <button id="btn-export-payouts" class="kt-btn kt-btn-ghost">
                <i data-lucide="download" class="w-4 h-4 mr-1"></i>Export
            </button>
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('payments.payouts') }}" class="card border border-border rounded-lg p-3 mb-5">
            <div class="flex flex-wrap gap-2 items-end">
                <div class="min-w-[130px]">
                    <label class="block text-xs text-muted-foreground mb-1">Status</label>
                    <select name="status" class="kt-input w-full">
                        <option value="">All</option>
                        <option value="Pending" @selected(($status ?? '') === 'Pending')>Pending</option>
                        <option value="Approved" @selected(($status ?? '') === 'Approved')>Approved</option>
                        <option value="Rejected" @selected(($status ?? '') === 'Rejected')>Rejected</option>
                    </select>
                </div>
                <div class="min-w-[160px]">
                    <label class="block text-xs text-muted-foreground mb-1">Owner</label>
                    <select name="owner" class="kt-input w-full">
                        <option value="">All owners</option>
                        @foreach ($owners ?? [] as $o)
                            <option value="{{ $o }}" @selected(($owner ?? '') === $o)>{{ $o }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="kt-btn kt-btn-mono self-end">Apply</button>
                <a href="{{ route('payments.payouts') }}" class="kt-btn kt-btn-ghost self-end">Reset</a>
            </div>
        </form>

        {{-- Table + QV --}}
        <div class="grid grid-cols-1 xl:grid-cols-[1fr,380px] gap-5">

            <div class="card border border-border rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1000px] text-sm">
                        <thead class="bg-muted/40 sticky top-0 z-10">
                            <tr>
                                @foreach (['#', 'Request', 'Vendor', 'Amount', 'Destination', 'Deal', 'Handover docs', 'Notes', 'Requested by', 'Status', 'Actions'] as $col)
                                    <th
                                        class="p-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wide
                                       {{ $col === 'Actions' ? 'w-44' : '' }}">
                                        {{ $col }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border bg-background">
                            @forelse ($payouts as $p)
                                @php
                                    $pCls = match ($p['status'] ?? '') {
                                        'Approved' => 'kt-badge-success',
                                        'Rejected' => 'kt-badge-destructive',
                                        'Pending' => 'kt-badge-warning',
                                        default => 'kt-badge-outline',
                                    };
                                    $docsOk = $p['handover_docs_complete'] ?? false;
                                @endphp
                                <tr class="hover:bg-muted/30 transition-colors" data-payout-id="{{ $p['id'] }}">
                                    <td class="p-3 text-xs text-muted-foreground">{{ $loop->iteration }}</td>
                                    <td class="p-3">
                                        <button data-action="preview-payout" data-id="{{ $p['id'] }}"
                                            class="font-medium text-foreground hover:text-primary text-left text-sm">
                                            {{ $p['ref'] ?? 'PAY-' . $p['id'] }}
                                        </button>
                                        <div class="text-xs text-muted-foreground">{{ $p['requested_at'] ?? '—' }}</div>
                                    </td>
                                    <td class="p-3 text-xs">{{ $p['vendor_name'] ?? '—' }}</td>
                                    <td class="p-3 font-semibold text-sm">£{{ number_format($p['amount'] ?? 0) }}</td>
                                    <td class="p-3 text-xs">{{ $p['destination'] ?? '—' }}</td>
                                    <td class="p-3 font-mono text-xs">{{ $p['deal_ref'] ?? '—' }}</td>
                                    <td class="p-3">
                                        @if ($docsOk)
                                            <span class="kt-badge kt-badge-success kt-badge-sm">Complete ✔</span>
                                        @else
                                            <span class="kt-badge kt-badge-destructive kt-badge-sm">Incomplete</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-xs max-w-[140px]">
                                        <span class="truncate block" title="{{ $p['note'] ?? '' }}">
                                            {{ $p['note'] ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-xs">{{ $p['requested_by'] ?? '—' }}</td>
                                    <td class="p-3">
                                        <span
                                            class="kt-badge {{ $pCls }} kt-badge-sm">{{ $p['status'] ?? '—' }}</span>
                                    </td>
                                    <td class="p-3">
                                        <div class="flex items-center gap-1.5">
                                            <button data-action="preview-payout" data-id="{{ $p['id'] }}"
                                                class="kt-btn kt-btn-ghost kt-btn-sm">Open</button>
                                            @if (($p['status'] ?? '') === 'Pending')
                                                <button data-action="approve-payout" data-id="{{ $p['id'] }}"
                                                    data-amount="{{ $p['amount'] ?? 0 }}" @disabled(!$docsOk)
                                                    class="kt-btn kt-btn-mono kt-btn-sm
                                                       {{ !$docsOk ? 'opacity-50 cursor-not-allowed' : '' }}"
                                                    title="{{ !$docsOk ? 'Handover documents incomplete' : 'Approve payout' }}">
                                                    Approve
                                                </button>
                                                <button data-action="reject-payout" data-id="{{ $p['id'] }}"
                                                    class="kt-btn kt-btn-outline kt-btn-sm text-destructive">
                                                    Reject
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="p-12 text-center text-muted-foreground text-sm">
                                        <i data-lucide="check-circle" class="w-8 h-8 mx-auto mb-2 opacity-30"></i>
                                        No payout requests.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div
                    class="px-4 py-3 border-t border-border flex items-center justify-between
                    text-xs text-muted-foreground bg-muted/10">
                    <span>{{ count($payouts) }} of {{ $total ?? count($payouts) }}</span>
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

            {{-- Preview Modal --}}
            <div id="modal-payout-preview"
                class="modal-overlay fixed inset-0 z-[100] hidden items-center justify-center p-4">

                <div class="modal-backdrop fixed inset-0 bg-black/60 backdrop-blur-sm"></div>

                <div
                    class="modal-box relative w-full max-w-5xl max-h-[90vh] overflow-hidden
               card border border-border rounded-2xl shadow-2xl bg-background">

                    {{-- Header --}}
                    <div class="px-6 py-4 border-b border-border flex items-center justify-between bg-muted/20">
                        <div>
                            <h2 id="pv-title" class="text-lg font-semibold">Payout Request</h2>
                            <div id="pv-meta" class="text-xs text-muted-foreground mt-1"></div>
                        </div>

                        <button class="modal-close kt-btn kt-btn-icon kt-btn-ghost">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>

                    {{-- Tabs --}}
                    <div class="border-b border-border px-6 pt-3">
                        <div class="flex gap-2 flex-wrap">
                            <button data-preview-tab="request" class="preview-tab kt-btn kt-btn-sm kt-btn-mono">
                                Request
                            </button>

                            <button data-preview-tab="deal" class="preview-tab kt-btn kt-btn-sm kt-btn-ghost">
                                Deal
                            </button>

                            <button data-preview-tab="documents" class="preview-tab kt-btn kt-btn-sm kt-btn-ghost">
                                Documents
                            </button>

                            <button data-preview-tab="log" class="preview-tab kt-btn kt-btn-sm kt-btn-ghost">
                                Log
                            </button>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="overflow-y-auto max-h-[calc(90vh-180px)] p-6">

                        <div id="preview-tab-request" class="preview-content"></div>

                        <div id="preview-tab-deal" class="preview-content hidden"></div>

                        <div id="preview-tab-documents" class="preview-content hidden"></div>

                        <div id="preview-tab-log" class="preview-content hidden"></div>

                    </div>

                    {{-- Footer --}}
                    <div id="pv-footer" class="border-t border-border px-6 py-4 bg-muted/10 flex justify-end">

                        <div id="pv-footer-actions" class="flex gap-2 flex-wrap">
                        </div>

                    </div>

                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════════════════ --}}
        {{-- MODALS                                                                  --}}
        {{-- ═══════════════════════════════════════════════════════════════════════ --}}

        {{-- Approve --}}
        <div id="modal-approve-payout" class="modal-overlay fixed inset-0 z-[200] hidden items-center justify-center p-4">
            <div class="modal-backdrop fixed inset-0 bg-black/50 backdrop-blur-sm"></div>
            <div
                class="modal-box relative w-full max-w-md mx-auto card border border-border rounded-2xl shadow-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-border flex items-center justify-between bg-muted/20">
                    <h2 class="text-base font-semibold">Approve payout</h2>
                    <button class="modal-close kt-btn kt-btn-icon kt-btn-ghost"><i data-lucide="x"
                            class="w-4 h-4"></i></button>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <div class="rounded-xl border border-border p-4 bg-muted/30">
                        <div class="text-xs text-muted-foreground">Amount to release</div>
                        <div id="approve-amount-display" class="text-2xl font-bold mt-1">—</div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1">Approval note (optional)</label>
                        <textarea id="approve-note" class="kt-input w-full" rows="2" placeholder="Any context for the audit log…"></textarea>
                    </div>
                    <input type="hidden" id="approve-payout-id" />
                    <div class="flex gap-2 justify-end pt-2 border-t border-border">
                        <button class="modal-close kt-btn kt-btn-ghost">Cancel</button>
                        <button id="btn-confirm-approve" class="kt-btn kt-btn-mono">Confirm approval</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Reject --}}
        <div id="modal-reject-payout" class="modal-overlay fixed inset-0 z-[200] hidden items-center justify-center p-4">
            <div class="modal-backdrop fixed inset-0 bg-black/50 backdrop-blur-sm"></div>
            <div
                class="modal-box relative w-full max-w-md mx-auto card border border-border rounded-2xl shadow-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-border flex items-center justify-between bg-muted/20">
                    <h2 class="text-base font-semibold text-destructive">Reject payout</h2>
                    <button class="modal-close kt-btn kt-btn-icon kt-btn-ghost"><i data-lucide="x"
                            class="w-4 h-4"></i></button>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <input type="hidden" id="reject-payout-id" />
                    <div>
                        <label class="block text-xs font-medium mb-1">Reason for rejection <span
                                class="text-destructive">*</span></label>
                        <textarea id="reject-reason" class="kt-input w-full" rows="3"
                            placeholder="Explain why this payout is being rejected…"></textarea>
                    </div>
                    <div class="flex gap-2 justify-end pt-2 border-t border-border">
                        <button class="modal-close kt-btn kt-btn-ghost">Cancel</button>
                        <button id="btn-confirm-reject" class="kt-btn kt-btn-destructive">Confirm rejection</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="toast-container" class="fixed bottom-5 right-5 z-[200] flex flex-col gap-2 pointer-events-none"></div>
        @include('partials._phase4_js')
    </div>

    <script>
        function switchPreviewTab(tab) {

            document.querySelectorAll('.preview-content')
                .forEach(el => el.classList.add('hidden'));

            document.querySelectorAll('.preview-tab')
                .forEach(btn => {
                    btn.classList.remove('kt-btn-mono');
                    btn.classList.add('kt-btn-ghost');
                });

            document
                .getElementById('preview-tab-' + tab)
                ?.classList.remove('hidden');

            document
                .querySelector(`[data-preview-tab="${tab}"]`)
                ?.classList.remove('kt-btn-ghost');

            document
                .querySelector(`[data-preview-tab="${tab}"]`)
                ?.classList.add('kt-btn-mono');
        }

        document.addEventListener('click', e => {
            const tabBtn = e.target.closest('[data-preview-tab]');
            if (!tabBtn) return;

            switchPreviewTab(tabBtn.dataset.previewTab);
        });
        (function() {
            const fmtCurrency = amount =>
                new Intl.NumberFormat('en-GB', {
                    style: 'currency',
                    currency: 'GBP'
                }).format(Number(amount || 0));
            const {
                toast,
                openModal,
                closeModal,
                auditEvent,
                fmt
            } = window.CS4;
            const PAYOUTS = @json(array_values($payouts));

            function renderQV(id) {
                const p = PAYOUTS.find(x => String(x.id) === String(id));
                if (!p) return;
                const qvTitle = document.getElementById('pv-title');
                const qvMeta = document.getElementById('pv-meta');
                if (qvTitle) qvTitle.textContent = p.ref ?? ('PAY-' + p.id);
                if (qvMeta) qvMeta.textContent = p.vendor_name + ' · ' + p.status;

                // Request tab
                const reqTab = document.getElementById('preview-tab-request');
                if (reqTab) reqTab.innerHTML =
                    `
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div><span class="text-muted-foreground">Amount</span><br><strong>${fmtCurrency(p.amount ?? 0)}</strong></div>
                        <div><span class="text-muted-foreground">Destination</span><br><strong>${p.destination ?? '—'}</strong></div>
                        <div><span class="text-muted-foreground">Deal</span><br><strong class="font-mono">${p.deal_ref ?? '—'}</strong></div>
                        <div><span class="text-muted-foreground">Requested by</span><br><strong>${p.requested_by ?? '—'}</strong></div>
                        <div><span class="text-muted-foreground">Requested at</span><br><strong>${p.requested_at ?? '—'}</strong></div>
                        <div><span class="text-muted-foreground">Status</span><br><strong>${p.status ?? '—'}</strong></div>
                    </div>
                    ${p.note ? `<div class="mt-3 p-2 rounded-lg bg-muted/30 border border-border text-xs">${p.note}</div>` : ''}`;

                // Documents tab
                const docsTab = document.getElementById('preview-tab-documents');
                if (docsTab) {
                    const docs = p.documents ?? [];
                    docsTab.innerHTML = docs.length ?
                        `<div class="space-y-2">${docs.map(d => `
                                            <div class="flex items-center gap-2 px-3 py-2 rounded-lg border border-border bg-muted/10 text-xs">
                                                <i data-lucide="file-text" class="w-3.5 h-3.5 text-muted-foreground"></i>
                                                <span class="flex-1">${d.name}</span>
                                                <span class="kt-badge ${d.present ? 'kt-badge-success' : 'kt-badge-destructive'} kt-badge-sm">
                                                    ${d.present ? '✔' : 'Missing'}
                                                </span>
                                            </div>`).join('')}</div>` :
                        '<p class="text-xs text-muted-foreground">No document records.</p>';
                }

                // Log tab
                const logTab = document.getElementById('preview-tab-log');
                if (logTab) {
                    const log = p.approval_log ?? [];
                    logTab.innerHTML = log.length ?
                        log.map(l => `<div class="flex gap-2 text-xs">
                    <div class="w-2 h-2 rounded-full bg-primary mt-1.5 shrink-0"></div>
                    <div><strong>${l.action}</strong> by ${l.by} <span class="text-muted-foreground">— ${l.at}</span>
                    ${l.note ? `<br><span class="text-muted-foreground">${l.note}</span>` : ''}</div>
                </div>`).join('') :
                        '<p class="text-xs text-muted-foreground">No log entries.</p>';
                }

                const dealTab = document.getElementById('preview-tab-deal');

                if (dealTab) {
                    dealTab.innerHTML = `
        <div class="grid grid-cols-2 gap-3 text-xs">
            <div>
                <span class="text-muted-foreground">Deal Ref</span><br>
                <strong>${p.deal_ref ?? '—'}</strong>
            </div>

            <div>
                <span class="text-muted-foreground">Vendor</span><br>
                <strong>${p.vendor_name ?? '—'}</strong>
            </div>

            <div>
                <span class="text-muted-foreground">Destination</span><br>
                <strong>${p.destination ?? '—'}</strong>
            </div>

            <div>
                <span class="text-muted-foreground">Amount</span><br>
                <strong>${fmtCurrency(p.amount ?? 0)}</strong>
            </div>
        </div>
    `;
                }

                // Footer
                const footer = document.getElementById('pv-footer');
                const footerActions = document.getElementById('pv-footer-actions');
                if (footer && footerActions) {
                    footer.classList.remove('hidden');
                    footerActions.innerHTML = p.status === 'Pending' ?
                        `<button data-action="approve-payout" data-id="${p.id}" data-amount="${p.amount}"
                           class="kt-btn kt-btn-mono kt-btn-sm ${p.handover_docs_complete ? '' : 'opacity-50 cursor-not-allowed'}"
                           ${p.handover_docs_complete ? '' : 'disabled'}>Approve</button>
                   <button data-action="reject-payout" data-id="${p.id}"
                           class="kt-btn kt-btn-outline kt-btn-sm text-destructive">Reject</button>` :
                        `<span class="text-xs text-muted-foreground">${p.status} · ${p.resolved_at ?? ''}</span>`;
                }


                if (typeof lucide !== 'undefined') lucide.createIcons();
                switchPreviewTab('request');
                openModal('modal-payout-preview');

                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }

            document.addEventListener('click', e => {
                const btn = e.target.closest('[data-action]');
                if (!btn) return;
                const action = btn.dataset.action,
                    id = btn.dataset.id;

                if (action === 'preview-payout') renderQV(id);
                if (action === 'approve-payout') {
                    document.getElementById('approve-payout-id').value = id;
                    document.getElementById('approve-amount-display').textContent = fmtCurrency(btn.dataset
                        .amount ?? 0);
                    openModal('modal-approve-payout');
                }
                if (action === 'reject-payout') {
                    document.getElementById('reject-payout-id').value = id;
                    openModal('modal-reject-payout');
                }
            });

            document.getElementById('btn-confirm-approve')?.addEventListener('click', () => {
                const id = document.getElementById('approve-payout-id')?.value;
                const note = document.getElementById('approve-note')?.value?.trim();
                auditEvent('payout_approved', {
                    id,
                    note
                });
                toast('Payout approved.', 'success');
                closeModal('modal-approve-payout');
            });

            document.getElementById('btn-confirm-reject')?.addEventListener('click', () => {
                const id = document.getElementById('reject-payout-id')?.value;
                const reason = document.getElementById('reject-reason')?.value?.trim();
                if (!reason) {
                    toast('Reason required.', 'warning');
                    return;
                }
                auditEvent('payout_rejected', {
                    id,
                    reason
                });
                toast('Payout rejected.', 'success');
                closeModal('modal-reject-payout');
            });

            document.getElementById('btn-export-payouts')?.addEventListener('click', () =>
                toast('Export queued — check your email.', 'info')
            );
        })();
    </script>

@endsection
