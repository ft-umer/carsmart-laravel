{{-- resources/views/payments/methods.blade.php --}}
{{-- Phase 4 — P3: Payments → Methods (Cards on file) --}}
@extends('layouts.app')
@section('title', 'Payment Methods — Payments')

@section('content')

@include('partials._retention_banner')
<div class="kt-container-fixed">


<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
    <div class="flex items-center gap-3 flex-wrap">
        <h1 class="text-xl font-semibold text-foreground">Payment Methods</h1>
        <span class="text-sm text-muted-foreground">Cards on file</span>
    </div>
    <button id="btn-send-setup" class="kt-btn kt-btn-mono">
        <i data-lucide="send" class="w-4 h-4 mr-1"></i>Send setup link
    </button>
</div>

{{-- MIT info banner --}}
<div class="flex items-start gap-3 px-4 py-3 mb-5 rounded-lg
            bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700
            text-blue-800 dark:text-blue-300 text-xs">
    <i data-lucide="shield-check" class="w-4 h-4 shrink-0 mt-0.5"></i>
    <p>
        All cards are stored under <strong>merchant-initiated transaction (MIT)</strong> mandates.
        Only brand, last four digits, and expiry are visible. No full card data is stored.
        The mandate text is displayed to vendors during setup.
    </p>
</div>

{{-- Table + QV --}}
<div class="grid grid-cols-1 xl:grid-cols-[1fr,360px] gap-5">

    <div class="card border border-border rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[700px] text-sm">
                <thead class="bg-muted/40 sticky top-0 z-10">
                    <tr>
                        @foreach(['Vendor','Card','Expiry','Status','Added by','Added','Actions'] as $col)
                            <th class="p-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wide
                                       {{ $col === 'Actions' ? 'w-40' : '' }}">
                                {{ $col }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-border bg-background">
                    @forelse ($methods as $pm)
                        @php
                            $pmCls = match ($pm['status'] ?? '') {
                                'Verified' => 'kt-badge-success',
                                'Pending'  => 'kt-badge-warning',
                                'Expired'  => 'kt-badge-destructive',
                                'Removed'  => 'kt-badge-outline',
                                default    => 'kt-badge-outline',
                            };
                        @endphp
                        <tr class="hover:bg-muted/30 transition-colors" data-pm-id="{{ $pm['id'] }}">
                            <td class="p-3">
                                <div class="font-medium text-sm">{{ $pm['vendor_name'] ?? '—' }}</div>
                                <div class="text-xs text-muted-foreground font-mono">{{ $pm['vendor_id'] ?? '' }}</div>
                            </td>
                            <td class="p-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-9 h-6 rounded bg-muted flex items-center justify-center
                                                text-xs font-bold text-muted-foreground shrink-0">
                                        {{ strtoupper(substr($pm['brand'] ?? 'CARD', 0, 4)) }}
                                    </div>
                                    <span class="font-mono text-sm">•••• {{ $pm['last4'] ?? '****' }}</span>
                                </div>
                            </td>
                            <td class="p-3 text-sm">
                                <span class="{{ \Carbon\Carbon::createFromFormat('m/Y', $pm['expiry'] ?? '01/2099')->isPast() ? 'text-destructive font-semibold' : '' }}">
                                    {{ $pm['expiry'] ?? '—' }}
                                </span>
                            </td>
                            <td class="p-3">
                                <span class="kt-badge {{ $pmCls }} kt-badge-sm">{{ $pm['status'] ?? '—' }}</span>
                            </td>
                            <td class="p-3 text-xs text-muted-foreground">{{ $pm['added_by'] ?? '—' }}</td>
                            <td class="p-3 text-xs text-muted-foreground">{{ $pm['added_at'] ?? '—' }}</td>
                            <td class="p-3">
                                <div class="flex items-center gap-1.5">
                                    <button data-action="preview-method" data-id="{{ $pm['id'] }}"
                                            class="kt-btn kt-btn-ghost kt-btn-sm">View</button>
                                    <button data-action="replace-method" data-id="{{ $pm['id'] }}"
                                            data-vendor="{{ $pm['vendor_name'] ?? '' }}"
                                            class="kt-btn kt-btn-outline kt-btn-sm">Replace</button>
                                    <button data-action="remove-method" data-id="{{ $pm['id'] }}"
                                            class="kt-btn kt-btn-ghost kt-btn-sm text-destructive">Remove</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-12 text-center text-muted-foreground text-sm">
                                <i data-lucide="credit-card" class="w-8 h-8 mx-auto mb-2 opacity-30"></i>
                                No payment methods on file.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-border flex items-center justify-between
                    text-xs text-muted-foreground bg-muted/10">
            <span>{{ count($methods) }} of {{ $total ?? count($methods) }}</span>
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
        <div class="px-4 py-3 border-b border-border bg-muted/20 shrink-0">
            <div id="qv-title" class="text-sm font-semibold">Select a method</div>
            <div id="qv-meta"  class="text-xs text-muted-foreground mt-0.5">Details will appear here</div>
        </div>
        <div class="flex-1 overflow-auto p-4" id="qv-body">
            <p class="text-xs text-muted-foreground">Select a row to view card details.</p>
        </div>
    </aside>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════ --}}
{{-- MODALS                                                                  --}}
{{-- ═══════════════════════════════════════════════════════════════════════ --}}

{{-- Send setup link --}}
<div id="modal-send-setup" class="modal-overlay fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="modal-backdrop fixed inset-0 bg-black/50 backdrop-blur-sm"></div>
    <div class="modal-box relative w-full max-w-md mx-auto card border border-border rounded-2xl shadow-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-border flex items-center justify-between bg-muted/20">
            <h2 class="text-base font-semibold">Send card setup link</h2>
            <button class="modal-close kt-btn kt-btn-icon kt-btn-ghost"><i data-lucide="x" class="w-4 h-4"></i></button>
        </div>
        <div class="px-6 py-4 space-y-4">
            <p class="text-sm text-muted-foreground">
                A secure link will be sent to the vendor. It includes the MIT mandate text
                and creates a setup intent. Status changes to <strong>Pending → Verified</strong>
                once the vendor completes setup.
            </p>
            <div>
                <label class="block text-xs font-medium mb-1">Vendor</label>
                <select id="setup-vendor" class="kt-input w-full">
                    <option value="">Select vendor</option>
                    @foreach ($vendors ?? [] as $v)
                        <option value="{{ $v['id'] }}">{{ $v['name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium mb-1">Vendor email</label>
                <input id="setup-email" type="email" class="kt-input w-full"
                       placeholder="vendor@example.com" />
            </div>
            <div class="rounded-lg bg-muted/30 border border-border p-3 text-xs text-muted-foreground">
                <strong>Mandate text preview:</strong><br>
                By adding your card, you authorise Carsmart to charge your card for platform fees,
                deposits, and dispute outcomes under the merchant-initiated transaction agreement.
            </div>
            <div class="flex gap-2 justify-end pt-2 border-t border-border">
                <button class="modal-close kt-btn kt-btn-ghost">Cancel</button>
                <button id="btn-confirm-send-setup" class="kt-btn kt-btn-mono">Send setup link</button>
            </div>
        </div>
    </div>
</div>

{{-- Remove confirmation --}}
<div id="modal-remove-method" class="modal-overlay fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="modal-backdrop fixed inset-0 bg-black/50 backdrop-blur-sm"></div>
    <div class="modal-box relative w-full max-w-sm mx-auto card border border-border rounded-2xl shadow-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-border flex items-center justify-between">
            <h2 class="text-base font-semibold text-destructive">Remove card</h2>
            <button class="modal-close kt-btn kt-btn-icon kt-btn-ghost"><i data-lucide="x" class="w-4 h-4"></i></button>
        </div>
        <div class="px-6 py-4 space-y-4">
            <p class="text-sm text-muted-foreground">
                Remove this card on file? The vendor will need to re-add a card before bidding.
            </p>
            <input type="hidden" id="remove-method-id" />
            <div class="flex gap-2 justify-end">
                <button class="modal-close kt-btn kt-btn-ghost">Cancel</button>
                <button id="btn-confirm-remove" class="kt-btn kt-btn-destructive">Remove</button>
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
    const METHODS = @json(array_values($methods));

    function previewMethod(id) {
        const pm = METHODS.find(m => String(m.id) === String(id));
        if (!pm) return;
        const qvTitle = document.getElementById('qv-title');
        const qvMeta  = document.getElementById('qv-meta');
        const qvBody  = document.getElementById('qv-body');
        if (qvTitle) qvTitle.textContent = pm.vendor_name ?? '—';
        if (qvMeta)  qvMeta.textContent  = pm.status + ' · Added ' + (pm.added_at ?? '');
        if (!qvBody) return;
        qvBody.innerHTML = `
            <div class="space-y-4 text-xs">
                <div class="flex items-center gap-3 p-3 rounded-xl border border-border bg-muted/20">
                    <div class="w-12 h-8 rounded-lg bg-gradient-to-br from-slate-700 to-slate-900
                                flex items-center justify-center text-white text-xs font-bold shrink-0">
                        ${(pm.brand ?? 'CARD').toUpperCase().substring(0,4)}
                    </div>
                    <div>
                        <div class="font-mono text-sm font-semibold">•••• •••• •••• ${pm.last4 ?? '****'}</div>
                        <div class="text-muted-foreground">Expires ${pm.expiry ?? '—'}</div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div><span class="text-muted-foreground">Vendor</span><br><strong>${pm.vendor_name ?? '—'}</strong></div>
                    <div><span class="text-muted-foreground">Status</span><br><strong>${pm.status ?? '—'}</strong></div>
                    <div><span class="text-muted-foreground">Added by</span><br><strong>${pm.added_by ?? '—'}</strong></div>
                    <div><span class="text-muted-foreground">Added</span><br><strong>${pm.added_at ?? '—'}</strong></div>
                    <div><span class="text-muted-foreground">Mandate</span><br>
                        <span class="kt-badge kt-badge-success kt-badge-sm">${pm.mandate_accepted ? 'Accepted' : 'Pending'}</span>
                    </div>
                    <div><span class="text-muted-foreground">Setup initiated by</span><br><strong>${pm.setup_initiated_by ?? '—'}</strong></div>
                </div>
                <div class="flex gap-2 flex-wrap pt-2 border-t border-border">
                    <button onclick="window.CS4.toast('Setup link sent.','success')"
                            class="kt-btn kt-btn-outline kt-btn-sm">Send replace link</button>
                    <button onclick="document.getElementById('remove-method-id').value='${pm.id}';window.CS4.openModal('modal-remove-method')"
                            class="kt-btn kt-btn-ghost kt-btn-sm text-destructive">Remove</button>
                </div>
            </div>`;
    }

    document.addEventListener('click', e => {
        const btn = e.target.closest('[data-action]');
        if (!btn) return;
        const action = btn.dataset.action, id = btn.dataset.id;
        if (action === 'preview-method')  previewMethod(id);
        if (action === 'replace-method')  { auditEvent('payment_method_replace_initiated', { id }); toast('Setup link sent for replacement.', 'success'); }
        if (action === 'remove-method')   { document.getElementById('remove-method-id').value = id; openModal('modal-remove-method'); }
    });

    document.getElementById('btn-send-setup')?.addEventListener('click', () => openModal('modal-send-setup'));
    document.getElementById('btn-confirm-send-setup')?.addEventListener('click', () => {
        const vendor = document.getElementById('setup-vendor')?.value;
        const email  = document.getElementById('setup-email')?.value?.trim();
        if (!vendor || !email) { toast('Vendor and email required.', 'warning'); return; }
        auditEvent('payment_method_setup_sent', { vendor, email });
        toast('Setup link sent to ' + email, 'success');
        closeModal('modal-send-setup');
    });

    document.getElementById('btn-confirm-remove')?.addEventListener('click', () => {
        const id = document.getElementById('remove-method-id')?.value;
        auditEvent('payment_method_removed', { id });
        toast('Card removed.', 'success');
        closeModal('modal-remove-method');
    });
})();
</script>

@endsection
