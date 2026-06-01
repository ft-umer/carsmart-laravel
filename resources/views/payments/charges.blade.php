{{-- resources/views/payments/charges.blade.php --}}
{{-- Phase 4 — P1: Payments → Charges & Fees --}}
@extends('layouts.app')
@section('title', 'Charges & Fees — Payments')

@section('content')

@include('partials._retention_banner')

<div class="kt-container-fixed">
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
    <div class="flex items-center gap-3 flex-wrap">
        <h1 class="text-xl font-semibold text-foreground">Charges &amp; Fees</h1>
        <span class="text-sm text-muted-foreground">{{ count($invoices) }} invoice{{ count($invoices) !== 1 ? 's' : '' }}</span>
    </div>
    <div class="flex gap-2 flex-wrap">
        <button id="btn-gen-invoice" class="kt-btn kt-btn-outline">
            <i data-lucide="file-plus" class="w-4 h-4 mr-1"></i>Generate invoice
        </button>
        <button id="btn-export-invoices" class="kt-btn kt-btn-ghost">
            <i data-lucide="download" class="w-4 h-4 mr-1"></i>Export
        </button>
    </div>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('payments.charges') }}"
      class="card border border-border rounded-lg p-3 mb-5">
    <div class="flex flex-wrap gap-2 items-end">
        <div class="min-w-[160px]">
            <label class="block text-xs text-muted-foreground mb-1">Vendor</label>
            <select name="vendor" class="kt-input w-full">
                <option value="">All vendors</option>
                @foreach ($vendors ?? [] as $v)
                    <option value="{{ $v['id'] }}" @selected(($vendor ?? '') === $v['id'])>{{ $v['name'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="min-w-[140px]">
            <label class="block text-xs text-muted-foreground mb-1">Period</label>
            <select name="period" class="kt-input w-full">
                <option value="this_month" @selected(($period ?? '') === 'this_month')>This month</option>
                <option value="last_month"  @selected(($period ?? '') === 'last_month')>Last month</option>
                <option value="this_year"   @selected(($period ?? '') === 'this_year')>This year</option>
                <option value="all"         @selected(($period ?? '') === 'all')>All time</option>
            </select>
        </div>
        <div class="min-w-[130px]">
            <label class="block text-xs text-muted-foreground mb-1">Type</label>
            <select name="type" class="kt-input w-full">
                <option value="">All types</option>
                <option value="monthly"     @selected(($type ?? '') === 'monthly')>Monthly</option>
                <option value="transaction" @selected(($type ?? '') === 'transaction')>Per-transaction</option>
            </select>
        </div>
        <div class="min-w-[120px]">
            <label class="block text-xs text-muted-foreground mb-1">Status</label>
            <select name="status" class="kt-input w-full">
                <option value="">All</option>
                <option value="Draft"  @selected(($status ?? '') === 'Draft')>Draft</option>
                <option value="Issued" @selected(($status ?? '') === 'Issued')>Issued</option>
                <option value="Paid"   @selected(($status ?? '') === 'Paid')>Paid</option>
            </select>
        </div>
        <button type="submit" class="kt-btn kt-btn-mono self-end">Apply</button>
        <a href="{{ route('payments.charges') }}" class="kt-btn kt-btn-ghost self-end">Reset</a>
    </div>
</form>

{{-- Table + QV --}}
<div class="grid grid-cols-1 xl:grid-cols-[1fr,360px] gap-5">

    <div class="card border border-border rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] text-sm">
                <thead class="bg-muted/40 sticky top-0 z-10">
                    <tr>
                        @foreach (['#','Invoice','Vendor','Period','Items','Subtotal','Tax','Total','Status','Actions'] as $col)
                            <th class="p-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wide
                                       {{ $col === 'Actions' ? 'w-44' : '' }}">
                                {{ $col }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-border bg-background">
                    @forelse ($invoices as $inv)
                        @php
                            $statusCls = match ($inv['status']) {
                                'Paid'   => 'kt-badge-success',
                                'Issued' => 'kt-badge-info',
                                'Draft'  => 'kt-badge-outline',
                                default  => 'kt-badge-outline',
                            };
                        @endphp
                        <tr class="hover:bg-muted/30 transition-colors" data-inv-id="{{ $inv['id'] }}">
                            <td class="p-3 text-xs text-muted-foreground">{{ $loop->iteration }}</td>
                            <td class="p-3">
                                <button data-action="preview-invoice" data-id="{{ $inv['id'] }}"
                                        class="font-medium text-foreground hover:text-primary text-left">
                                    {{ $inv['ref'] ?? 'INV-' . $inv['id'] }}
                                </button>
                            </td>
                            <td class="p-3 text-xs">{{ $inv['vendor_name'] ?? '—' }}</td>
                            <td class="p-3 text-xs text-muted-foreground">{{ $inv['period'] ?? '—' }}</td>
                            <td class="p-3 text-xs">{{ count($inv['line_items'] ?? []) }} item{{ count($inv['line_items'] ?? []) !== 1 ? 's' : '' }}</td>
                            <td class="p-3 text-sm font-medium">£{{ number_format($inv['subtotal'] ?? 0) }}</td>
                            <td class="p-3 text-xs">£{{ number_format($inv['tax'] ?? 0) }}</td>
                            <td class="p-3 text-sm font-semibold">£{{ number_format($inv['total'] ?? 0) }}</td>
                            <td class="p-3"><span class="kt-badge {{ $statusCls }} kt-badge-sm">{{ $inv['status'] }}</span></td>
                            <td class="p-3">
                                <div class="flex items-center gap-1.5">
                                    <button data-action="preview-invoice" data-id="{{ $inv['id'] }}"
                                            class="kt-btn kt-btn-ghost kt-btn-sm">Open</button>
                                    @if ($inv['status'] === 'Draft')
                                        <button data-action="send-invoice" data-id="{{ $inv['id'] }}"
                                                class="kt-btn kt-btn-outline kt-btn-sm">Send</button>
                                    @elseif ($inv['status'] === 'Issued')
                                        <button data-action="mark-paid" data-id="{{ $inv['id'] }}"
                                                class="kt-btn kt-btn-mono kt-btn-sm">Mark paid</button>
                                    @endif
                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open=!open" class="kt-btn kt-btn-ghost kt-btn-sm px-2">
                                            <i data-lucide="more-horizontal" class="w-4 h-4"></i>
                                        </button>
                                        <div x-show="open" @click.outside="open=false"
                                             class="absolute right-0 mt-1 w-44 bg-background border border-border
                                                    rounded-lg shadow-lg z-20 py-1 text-sm">
                                            <button data-action="export-pdf" data-id="{{ $inv['id'] }}"
                                                    class="w-full text-left flex items-center gap-2 px-3 py-2 hover:bg-muted">
                                                <i data-lucide="file-text" class="w-3.5 h-3.5 opacity-60"></i> Export PDF
                                            </button>
                                            <button data-action="credit-note" data-id="{{ $inv['id'] }}"
                                                    class="w-full text-left flex items-center gap-2 px-3 py-2 hover:bg-muted">
                                                <i data-lucide="file-minus" class="w-3.5 h-3.5 opacity-60"></i> Credit note
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="p-12 text-center text-muted-foreground text-sm">
                                <i data-lucide="receipt" class="w-8 h-8 mx-auto mb-2 opacity-30"></i>
                                No invoices found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-border flex items-center justify-between text-xs text-muted-foreground bg-muted/10">
            <span>{{ count($invoices) }} of {{ $total ?? count($invoices) }}</span>
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
    <aside id="invoice-qv"
           class="card border border-border rounded-xl overflow-hidden
                  sticky top-[86px] h-[calc(100vh-120px)] flex flex-col">
        <div class="px-4 py-3 border-b border-border bg-muted/20 shrink-0">
            <div id="qv-title" class="text-sm font-semibold">Select an invoice</div>
            <div id="qv-meta" class="text-xs text-muted-foreground mt-0.5">Preview will appear here</div>
        </div>
        <div class="flex-1 overflow-auto p-4 text-sm" id="qv-body">
            <p class="text-xs text-muted-foreground">Select a row to preview line items.</p>
        </div>
    </aside>
</div>

<div id="toast-container" class="fixed bottom-5 right-5 z-[200] flex flex-col gap-2 pointer-events-none"></div>
@include('partials._phase4_js')

</div>
<script>
(function () {
    const { toast, auditEvent } = window.CS4;
    const INVOICES = @json(array_values($invoices));

    function previewInvoice(id) {
        const inv = INVOICES.find(i => i.id === id || String(i.id) === String(id));
        if (!inv) return;
        const qvTitle = document.getElementById('qv-title');
        const qvMeta  = document.getElementById('qv-meta');
        const qvBody  = document.getElementById('qv-body');
        if (qvTitle) qvTitle.textContent = inv.ref ?? ('INV-' + inv.id);
        if (qvMeta)  qvMeta.textContent  = inv.vendor_name + ' · ' + inv.period + ' · ' + inv.status;
        if (!qvBody) return;
        const lines = (inv.line_items ?? []).map(l => `
            <tr class="border-b border-border">
                <td class="p-2">${l.description ?? '—'}</td>
                <td class="p-2 text-muted-foreground">${l.reference ?? '—'}</td>
                <td class="p-2 text-right font-medium">£${Number(l.amount ?? 0).toLocaleString()}</td>
            </tr>`).join('');
        qvBody.innerHTML = `
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div><span class="text-muted-foreground">Vendor</span><br><strong>${inv.vendor_name ?? '—'}</strong></div>
                    <div><span class="text-muted-foreground">Period</span><br><strong>${inv.period ?? '—'}</strong></div>
                    <div><span class="text-muted-foreground">Status</span><br><strong>${inv.status}</strong></div>
                    <div><span class="text-muted-foreground">Total</span><br><strong>£${Number(inv.total ?? 0).toLocaleString()}</strong></div>
                </div>
                <div>
                    <h4 class="text-xs font-semibold text-muted-foreground uppercase tracking-wide mb-2">Line items</h4>
                    <div class="border border-border rounded-lg overflow-hidden">
                        <table class="w-full text-xs">
                            <thead class="bg-muted/40"><tr>
                                <th class="p-2 text-left font-medium">Description</th>
                                <th class="p-2 text-left font-medium">Ref</th>
                                <th class="p-2 text-right font-medium">Amount</th>
                            </tr></thead>
                            <tbody class="bg-background">${lines || '<tr><td colspan="3" class="p-3 text-center text-muted-foreground">No line items.</td></tr>'}</tbody>
                        </table>
                    </div>
                </div>
                <div class="flex gap-2 flex-wrap">
                    ${inv.status === 'Draft' ? `<button onclick="window.CS4.toast('Invoice sent.','success')" class="kt-btn kt-btn-outline kt-btn-sm">Send to vendor</button>` : ''}
                    ${inv.status === 'Issued' ? `<button onclick="window.CS4.toast('Marked as paid.','success')" class="kt-btn kt-btn-mono kt-btn-sm">Mark paid</button>` : ''}
                    <button onclick="window.CS4.toast('PDF queued.','info')" class="kt-btn kt-btn-ghost kt-btn-sm">Export PDF</button>
                </div>
            </div>`;
    }

    document.addEventListener('click', e => {
        const btn = e.target.closest('[data-action]');
        if (!btn) return;
        const action = btn.dataset.action, id = btn.dataset.id;
        if (action === 'preview-invoice')  previewInvoice(id);
        if (action === 'send-invoice')     { auditEvent('invoice_sent', { id }); toast('Invoice sent to vendor.', 'success'); }
        if (action === 'mark-paid')        { auditEvent('invoice_paid', { id }); toast('Invoice marked as paid.', 'success'); }
        if (action === 'export-pdf')       toast('PDF export queued.', 'info');
        if (action === 'credit-note')      toast('Credit note created.', 'success');
    });
    document.getElementById('btn-export-invoices')?.addEventListener('click', () => toast('Export queued.', 'info'));
    document.getElementById('btn-gen-invoice')?.addEventListener('click', () => { auditEvent('invoice_generated', {}); toast('Invoice draft created.', 'success'); });
})();
</script>

@endsection
