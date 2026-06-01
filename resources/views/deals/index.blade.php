{{-- resources/views/deals/index.blade.php --}}
{{-- Phase 4 — D1: Deals Browse / Pipeline --}}
@extends('layouts.app')
@section('title', 'Deals — Carsmart')

@section('content')

    {{-- ── Retention banner (G1) ────────────────────────────────────────────── --}}
    @include('partials._retention_banner')
    <div class="kt-container-fixed">

        {{-- ── Toolbar ───────────────────────────────────────────────────────────── --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
            <div class="flex items-center gap-3 flex-wrap">
                <h1 class="text-xl font-semibold text-foreground">Deals</h1>
                <span class="text-sm text-muted-foreground">
                    {{ count($deals) }} deal{{ count($deals) !== 1 ? 's' : '' }}
                </span>
                @php $pendingObj = collect($deals)->where('objection_active', true)->count(); @endphp
                @if ($pendingObj > 0)
                    <span class="kt-badge kt-badge-warning kt-badge-sm">
                        {{ $pendingObj }} objection{{ $pendingObj > 1 ? 's' : '' }} active
                    </span>
                @endif
                @php $awaitingPayout = collect($deals)->where('state', 'Awaiting payout')->count(); @endphp
                @if ($awaitingPayout > 0)
                    <span class="kt-badge kt-badge-info kt-badge-sm">{{ $awaitingPayout }} awaiting payout</span>
                @endif
            </div>
            <div class="flex gap-2 flex-wrap items-center">
                <button id="btn-export-deals" class="kt-btn kt-btn-outline">
                    <i data-lucide="download" class="w-4 h-4 mr-1"></i>Export
                </button>
                <a href="{{ route('deals.create') }}" class="kt-btn kt-btn-mono">
                    + New Deal
                </a>
            </div>
        </div>

        {{-- ── Filters ────────────────────────────────────────────────────────────── --}}
        <form method="GET" action="{{ route('deals.index') }}" id="deal-filter-form"
            class="card border border-border rounded-lg p-3 mb-5">
            <input type="hidden" name="include_archived" value="{{ request('include_archived') }}">
            <div class="flex flex-wrap gap-2 items-end">

                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs text-muted-foreground mb-1">Search</label>
                    <input name="search" value="{{ $search ?? '' }}" type="search" class="kt-input w-full"
                        placeholder="Deal / listing / lot / person / VRM…" />
                </div>

                <div class="min-w-[140px]">
                    <label class="block text-xs text-muted-foreground mb-1">State</label>
                    <select name="state" class="kt-input w-full">
                        <option value="">All states</option>
                        @foreach (['Pending', 'Collection scheduled', 'Handover complete', 'Awaiting payout', 'Closed', 'Cancelled'] as $s)
                            <option value="{{ $s }}" @selected(($state ?? '') === $s)>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="min-w-[140px]">
                    <label class="block text-xs text-muted-foreground mb-1">Objection</label>
                    <select name="objection" class="kt-input w-full">
                        <option value="">Any</option>
                        <option value="in_window" @selected(($objection ?? '') === 'in_window')>In window</option>
                        <option value="window_over" @selected(($objection ?? '') === 'window_over')>Window over</option>
                    </select>
                </div>

                <div class="min-w-[140px]">
                    <label class="block text-xs text-muted-foreground mb-1">Financial</label>
                    <select name="financial" class="kt-input w-full">
                        <option value="">Any</option>
                        <option value="holds" @selected(($financial ?? '') === 'holds')>Holds active</option>
                        <option value="awaiting_payout" @selected(($financial ?? '') === 'awaiting_payout')>Awaiting payout</option>
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
                <a href="{{ route('deals.index') }}" class="kt-btn kt-btn-ghost self-end">Reset</a>
            </div>
        </form>

        {{-- ── Flash ─────────────────────────────────────────────────────────────── --}}
        @if (session('success'))
            <div
                class="flex items-center gap-2 px-4 py-3 mb-4 rounded-lg
                bg-green-50 dark:bg-green-900/20 border border-green-200 text-green-800 dark:text-green-300 text-sm">
                <i data-lucide="check-circle" class="w-4 h-4 shrink-0"></i>
                {{ session('success') }}
                <button onclick="this.parentElement.remove()" class="ml-auto">✕</button>
            </div>
        @endif

        {{-- ── Table + Quick View ──────────────────────────────────────────────────── --}}
        <div class="grid grid-cols-1 xl:grid-cols-[1fr,380px] gap-5">

            {{-- Table card --}}
            <div class="card border border-border rounded-xl overflow-hidden">

                <div class="px-4 py-3 border-b border-border flex items-center justify-between bg-muted/20">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input id="select-all-cb" type="checkbox" class="form-checkbox rounded" />
                        <span class="text-xs text-muted-foreground" id="selected-label">0 selected</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open=!open" class="kt-btn kt-btn-outline kt-btn-sm" id="bulk-deals-btn">
                                Bulk actions ▾
                            </button>
                            <div x-show="open" @click.outside="open=false"
                                class="absolute right-0 mt-1 w-48 bg-background border border-border rounded-lg shadow-xl z-30 py-1 text-sm">
                                <button data-bulk="assign-owner"
                                    class="bulk-action-btn w-full text-left flex items-center gap-2 px-4 py-2 hover:bg-muted">
                                    <i data-lucide="user-check" class="w-4 h-4 opacity-60"></i> Assign owner
                                </button>
                                <button data-bulk="move-state"
                                    class="bulk-action-btn w-full text-left flex items-center gap-2 px-4 py-2 hover:bg-muted">
                                    <i data-lucide="arrow-right" class="w-4 h-4 opacity-60"></i> Move state
                                </button>
                            </div>
                        </div>
                        <button id="btn-refresh-deals" class="kt-btn kt-btn-ghost kt-btn-sm" title="Refresh">
                            <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1100px] text-sm" id="deals-table">
                        <thead class="bg-muted/40 sticky top-0 z-10">
                            <tr>
                                <th class="p-3 w-10"></th>
                                @foreach (['Deal', 'Source', 'Vehicle', 'Buyer (Vendor)', 'Seller', 'Price', 'Objection ends', 'State', 'Owner', 'Actions'] as $col)
                                    <th
                                        class="p-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wide
                                       {{ $col === 'Actions' ? 'w-52' : '' }}">
                                        {{ $col }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border bg-background" id="deals-tbody">
                            @forelse ($deals as $deal)
                                @php
                                    $objDays = $deal['objection_days_left'] ?? null;
                                    $isUrgent = $objDays !== null && $objDays <= 2 && $objDays >= 0;
                                    $isOver = $objDays !== null && $objDays < 0;

                                    $stateCls = match ($deal['state']) {
                                        'Pending' => 'kt-badge-outline',
                                        'Collection scheduled' => 'kt-badge-info',
                                        'Handover complete' => 'kt-badge-primary',
                                        'Awaiting payout' => 'kt-badge-warning',
                                        'Closed' => 'kt-badge-success',
                                        'Cancelled' => 'kt-badge-destructive',
                                        default => 'kt-badge-outline',
                                    };
                                @endphp
                                <tr class="hover:bg-muted/30 transition-colors" data-deal-id="{{ $deal['id'] }}">

                                    <td class="p-3">
                                        <input type="checkbox" class="row-cb form-checkbox rounded"
                                            value="{{ $deal['id'] }}" />
                                    </td>

                                    {{-- Deal ref --}}
                                    <td class="p-3">
                                        <a href="{{ route('deals.show', $deal['id']) }}"
                                            class="font-medium text-foreground hover:text-primary">{{ $deal['ref'] }}</a>
                                        <div class="text-xs text-muted-foreground mt-0.5">{{ $deal['id'] }}</div>
                                    </td>

                                    {{-- Source --}}
                                    <td class="p-3">
                                        @php
                                            $srcCls = match ($deal['source']) {
                                                'AUC' => 'kt-badge-primary',
                                                'BIN' => 'kt-badge-info',
                                                'Offer' => 'kt-badge-warning',
                                                default => 'kt-badge-outline',
                                            };
                                        @endphp
                                        <span
                                            class="kt-badge {{ $srcCls }} kt-badge-sm">{{ $deal['source'] }}</span>
                                    </td>

                                    {{-- Vehicle --}}
                                    <td class="p-3">
                                        <div class="font-medium text-xs">{{ $deal['vehicle_title'] }}</div>
                                        @if ($deal['vrm'] ?? null)
                                            <span
                                                class="font-mono text-xs bg-muted px-1.5 py-0.5 rounded">{{ $deal['vrm'] }}</span>
                                        @endif
                                    </td>

                                    {{-- Buyer --}}
                                    <td class="p-3">
                                        <div class="text-xs font-medium">{{ $deal['buyer_name'] }}</div>
                                        <div class="text-xs text-muted-foreground">{{ $deal['buyer_company'] ?? '' }}
                                        </div>
                                    </td>

                                    {{-- Seller --}}
                                    <td class="p-3">
                                        <div class="text-xs">{{ $deal['seller_name'] }}</div>
                                    </td>

                                    {{-- Price --}}
                                    <td class="p-3">
                                        <div class="font-semibold text-sm">£{{ number_format($deal['price']) }}</div>
                                    </td>

                                    {{-- Objection ends --}}
                                    <td class="p-3 whitespace-nowrap">
                                        @if ($objDays === null)
                                            <span class="text-xs text-muted-foreground">—</span>
                                        @elseif ($isOver)
                                            <span class="text-xs text-muted-foreground">Expired</span>
                                        @else
                                            <span
                                                class="text-xs font-medium {{ $isUrgent ? 'text-destructive' : 'text-foreground' }}">
                                                {{ $isUrgent ? '⚠ ' : '' }}{{ $objDays }}d left
                                            </span>
                                        @endif
                                    </td>

                                    {{-- State --}}
                                    <td class="p-3">
                                        <span
                                            class="kt-badge {{ $stateCls }} kt-badge-sm">{{ $deal['state'] }}</span>
                                    </td>

                                    {{-- Owner --}}
                                    <td class="p-3">
                                        @if ($deal['owner'] ?? null)
                                            <span
                                                class="inline-flex items-center justify-center w-7 h-7 rounded-full
                                                 bg-primary/10 text-primary text-xs font-bold"
                                                title="{{ $deal['owner'] }}">
                                                {{ $deal['owner'] }}
                                            </span>
                                        @else
                                            <span class="text-xs text-muted-foreground">Unassigned</span>
                                        @endif
                                    </td>

                                    {{-- Actions --}}
                                    <td class="p-3">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <button data-action="quick-view" data-id="{{ $deal['id'] }}"
                                                class="kt-btn kt-btn-ghost kt-btn-sm">Preview</button>
                                            <a href="{{ route('deals.show', $deal['id']) }}"
                                                class="kt-btn kt-btn-outline kt-btn-sm">Open</a>

                                            <div class="relative" x-data="{ open: false }">
                                                <button @click="open=!open" class="kt-btn kt-btn-ghost kt-btn-sm px-2">
                                                    <i data-lucide="more-horizontal" class="w-4 h-4"></i>
                                                </button>
                                                <div x-show="open" @click.outside="open=false"
                                                    class="absolute right-0 mt-1 w-52 bg-background border border-border
                                                    rounded-lg shadow-lg z-20 py-1 text-sm">
                                                    <button data-action="assign-owner" data-id="{{ $deal['id'] }}"
                                                        class="w-full text-left flex items-center gap-2 px-3 py-2 hover:bg-muted">
                                                        <i data-lucide="user-check" class="w-3.5 h-3.5 opacity-60"></i>
                                                        Assign owner
                                                    </button>
                                                    <button data-action="start-collection" data-id="{{ $deal['id'] }}"
                                                        class="w-full text-left flex items-center gap-2 px-3 py-2 hover:bg-muted">
                                                        <i data-lucide="truck" class="w-3.5 h-3.5 opacity-60"></i>
                                                        Start collection
                                                    </button>
                                                    <button data-action="open-dispute" data-id="{{ $deal['id'] }}"
                                                        class="w-full text-left flex items-center gap-2 px-3 py-2 hover:bg-muted">
                                                        <i data-lucide="alert-octagon" class="w-3.5 h-3.5 opacity-60"></i>
                                                        Open dispute
                                                    </button>
                                                    @if (($deal['state'] ?? '') === 'Awaiting payout')
                                                        <button data-action="approve-payout"
                                                            data-id="{{ $deal['id'] }}"
                                                            class="w-full text-left flex items-center gap-2 px-3 py-2
                                                               hover:bg-muted text-primary font-medium">
                                                            <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                                                            Approve payout
                                                        </button>
                                                    @endif
                                                    <div class="border-t border-border my-1"></div>
                                                    <button data-action="cancel-rerun" data-id="{{ $deal['id'] }}"
                                                        class="w-full text-left flex items-center gap-2 px-3 py-2
                                                           hover:bg-muted text-destructive">
                                                        <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                                                        Cancel &amp; re-run
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="p-12 text-center text-muted-foreground text-sm">
                                        <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 opacity-30"></i>
                                        No deals found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div
                    class="px-4 py-3 border-t border-border flex items-center justify-between
                    text-xs text-muted-foreground bg-muted/10">
                    <span>Showing {{ count($deals) }} of {{ $total ?? count($deals) }}</span>
                    <div class="flex gap-2">
                        <button class="kt-btn kt-btn-ghost kt-btn-sm" {{ ($page ?? 1) <= 1 ? 'disabled' : '' }}>
                            <i data-lucide="chevron-left" class="w-3.5 h-3.5 mr-1"></i>Prev
                        </button>
                        <button class="kt-btn kt-btn-ghost kt-btn-sm" {{ !($hasMore ?? false) ? 'disabled' : '' }}>
                            Next<i data-lucide="chevron-right" class="w-3.5 h-3.5 ml-1"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- ── Quick View Panel ──────────────────────────────────────────────── --}}
            <aside id="quick-view-panel"
                class="card border border-border rounded-xl overflow-hidden
                  sticky top-[86px] h-[calc(100vh-120px)] flex flex-col">

                <div class="px-4 py-3 border-b border-border flex items-start justify-between gap-3 bg-muted/20 shrink-0">
                    <div class="min-w-0">
                        <div id="qv-title" class="text-sm font-semibold text-foreground">Select a deal</div>
                        <div id="qv-meta" class="text-xs text-muted-foreground mt-0.5">Preview will appear here</div>
                    </div>
                    <a id="qv-open-link" href="#" class="kt-btn kt-btn-outline kt-btn-sm hidden shrink-0">Open</a>
                </div>

                <div class="border-b border-border px-4 pt-2 shrink-0 overflow-x-auto">
                    <div class="flex gap-1 min-w-max" id="qv-tab-list">
                        @foreach (['Overview', 'Parties', 'Financials', 'Activity'] as $tab)
                            <button data-qv-tab="{{ Str::slug($tab) }}"
                                class="qv-tab-btn kt-btn kt-btn-sm
                                   {{ $loop->first ? 'kt-btn-mono' : 'kt-btn-ghost' }}">
                                {{ $tab }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="flex-1 overflow-auto p-4 text-sm" id="qv-body">
                    @foreach (['overview', 'parties', 'financials', 'activity'] as $tab)
                        <div id="qv-tab-{{ $tab }}"
                            class="qv-tab-content {{ $loop->first ? '' : 'hidden' }} space-y-3">
                            <p class="text-xs text-muted-foreground">Select a deal to preview.</p>
                        </div>
                    @endforeach
                </div>

                <div id="qv-footer" class="border-t border-border px-4 py-3 bg-muted/10 shrink-0 hidden">
                    <div class="flex gap-2 flex-wrap" id="qv-footer-actions"></div>
                </div>
            </aside>
        </div>


        {{-- ═══════════════════════════════════════════════════════════════════════ --}}
        {{-- MODALS                                                                  --}}
        {{-- ═══════════════════════════════════════════════════════════════════════ --}}

        {{-- Cancel & Re-run --}}
        <div id="modal-cancel-rerun" class="modal-overlay fixed inset-0 z-50 hidden items-center justify-center p-4">
            <div class="modal-backdrop fixed inset-0 bg-black/50 backdrop-blur-sm"></div>
            <div role="dialog" aria-modal="true"
                class="modal-box relative w-full max-w-md mx-auto card border border-border rounded-2xl shadow-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-border flex items-center justify-between bg-muted/20">
                    <h2 class="text-base font-semibold text-destructive flex items-center gap-2">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i> Cancel &amp; Re-run
                    </h2>
                    <button class="modal-close kt-btn kt-btn-icon kt-btn-ghost" aria-label="Close">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <p class="text-sm text-muted-foreground">
                        This will cancel the deal and re-list the vehicle. Provide a reason (required).
                    </p>
                    <input type="hidden" id="cancel-deal-id" value="" />
                    <div>
                        <label class="block text-xs font-medium mb-1">Reason <span
                                class="text-destructive">*</span></label>
                        <textarea id="cancel-reason" class="kt-input w-full" rows="3"
                            placeholder="e.g. Seller withdrew, buyer failed payment…"></textarea>
                    </div>
                    <div class="flex gap-2 justify-end pt-2 border-t border-border">
                        <button class="modal-close kt-btn kt-btn-ghost">Back</button>
                        <button id="btn-confirm-cancel" class="kt-btn kt-btn-destructive">Confirm cancel &amp;
                            re-run</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Assign owner --}}
        <div id="modal-assign-owner" class="modal-overlay fixed inset-0 z-50 hidden items-center justify-center p-4">
            <div class="modal-backdrop fixed inset-0 bg-black/50 backdrop-blur-sm"></div>
            <div
                class="modal-box relative w-full max-w-sm mx-auto card border border-border rounded-2xl shadow-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-border flex items-center justify-between">
                    <h2 class="text-base font-semibold">Assign owner</h2>
                    <button class="modal-close kt-btn kt-btn-icon kt-btn-ghost"><i data-lucide="x"
                            class="w-4 h-4"></i></button>
                </div>
                <div class="px-6 py-4 space-y-3">
                    <input type="hidden" id="assign-deal-id" value="" />
                    <select id="assign-owner-select" class="kt-input w-full">
                        @foreach ($owners ?? [] as $o)
                            <option>{{ $o }}</option>
                        @endforeach
                    </select>
                    <div class="flex gap-2 justify-end">
                        <button class="modal-close kt-btn kt-btn-ghost">Cancel</button>
                        <button id="btn-confirm-assign" class="kt-btn kt-btn-mono">Assign</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Toast container --}}
        <div id="toast-container" class="fixed bottom-5 right-5 z-[200] flex flex-col gap-2 pointer-events-none"></div>

        @include('partials._phase4_js')

    </div>
    <script>
        const fmtCurrency = amount =>
            new Intl.NumberFormat('en-GB', {
                style: 'currency',
                currency: 'GBP'
            }).format(Number(amount || 0));
        (function() {
            'use strict';
            const {
                toast,
                openModal,
                closeModal,
                auditEvent,
                fmt,
                $,
                $$
            } = window.CS4;

            /* ── Embedded data ──────────────────────────────────────────────────── */
            const DEALS = @json(array_values($deals));
            const state = {
                selectedIds: new Set(),
                activeDealId: null
            };

            /* ── Selection ─────────────────────────────────────────────────────── */
            function updateSelectionUI() {
                const n = state.selectedIds.size;
                const allCbs = $$('.row-cb');
                const selectAll = $('#select-all-cb');
                if ($('#selected-label')) $('#selected-label').textContent = n + ' selected';
                if (selectAll) {
                    selectAll.indeterminate = n > 0 && n < allCbs.length;
                    selectAll.checked = n === allCbs.length && allCbs.length > 0;
                }
            }
            $('#select-all-cb')?.addEventListener('change', function() {
                $$('.row-cb').forEach(cb => {
                    cb.checked = this.checked;
                    this.checked ? state.selectedIds.add(cb.value) : state.selectedIds.delete(cb.value);
                });
                updateSelectionUI();
            });
            document.addEventListener('change', e => {
                if (!e.target.classList.contains('row-cb')) return;
                e.target.checked ? state.selectedIds.add(e.target.value) : state.selectedIds.delete(e.target
                    .value);
                updateSelectionUI();
            });

            /* ── Quick view ─────────────────────────────────────────────────────── */
            function renderQV(id) {
                const deal = DEALS.find(d => d.id === id);
                if (!deal) return;
                state.activeDealId = id;
                $('#qv-title').textContent = deal.ref + ' · ' + (deal.vehicle_title ?? '');
                $('#qv-meta').textContent = 'State: ' + deal.state + ' · Owner: ' + (deal.owner ?? 'Unassigned');
                const link = $('#qv-open-link');
                if (link) {
                    link.href = '/deals/' + id;
                    link.classList.remove('hidden');
                }

                // Overview
                $('#qv-tab-overview').innerHTML = `
            <div class="grid grid-cols-2 gap-3 text-xs">
                <div><span class="text-muted-foreground">Deal ID</span><br><strong>${deal.ref}</strong></div>
                <div><span class="text-muted-foreground">Source</span><br><strong>${deal.source}</strong></div>
                <div><span class="text-muted-foreground">Vehicle</span><br><strong>${deal.vehicle_title ?? '—'}</strong></div>
                <div><span class="text-muted-foreground">VRM</span><br><strong class="font-mono">${deal.vrm ?? '—'}</strong></div>
                <div><span class="text-muted-foreground">Price</span><br><strong>${fmtCurrency(deal.price)}</strong></div>
                <div><span class="text-muted-foreground">Objection days</span><br>
                    <strong class="${(deal.objection_days_left ?? 99) <= 2 ? 'text-destructive' : ''}">
                        ${deal.objection_days_left !== null ? deal.objection_days_left + 'd' : '—'}
                    </strong>
                </div>
            </div>
            ${deal.objection_active ? `<div class="mt-2 px-3 py-2 rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 text-amber-800 dark:text-amber-300 text-xs">
                    <i data-lucide="alert-triangle" class="w-3.5 h-3.5 inline mr-1"></i>
                    Seller objection window open — ${deal.objection_days_left}d remaining
                </div>` : ''}
        `;

                // Parties
                $('#qv-tab-parties').innerHTML = `
            <div class="space-y-3 text-xs">
                <div class="p-3 rounded-lg border border-border bg-muted/20">
                    <div class="font-semibold text-foreground mb-1.5 flex items-center gap-2">
                        <i data-lucide="user" class="w-3.5 h-3.5 opacity-60"></i> Seller
                    </div>
                    <div>${deal.seller_name ?? '—'}</div>
                    <div class="text-muted-foreground">${deal.seller_email ?? ''}</div>
                    ${deal.kyc_verified ? '<span class="kt-badge kt-badge-success kt-badge-sm mt-1.5">KYC Verified</span>' : ''}
                </div>
                <div class="p-3 rounded-lg border border-border bg-muted/20">
                    <div class="font-semibold text-foreground mb-1.5 flex items-center gap-2">
                        <i data-lucide="building-2" class="w-3.5 h-3.5 opacity-60"></i> Buyer / Vendor
                    </div>
                    <div>${deal.buyer_name ?? '—'}</div>
                    <div class="text-muted-foreground">${deal.buyer_company ?? ''}</div>
                    ${deal.card_on_file ? '<span class="kt-badge kt-badge-primary kt-badge-sm mt-1.5">Card on file ✔</span>' : '<span class="kt-badge kt-badge-outline kt-badge-sm mt-1.5">No card on file</span>'}
                </div>
            </div>
        `;

                // Financials
                $('#qv-tab-financials').innerHTML = `
            <div class="space-y-2 text-xs">
                <div class="flex justify-between py-1.5 border-b border-border">
                    <span class="text-muted-foreground">Agreed price</span>
                    <strong>${fmtCurrency(deal.price)}</strong>
                </div>
                <div class="flex justify-between py-1.5 border-b border-border">
                    <span class="text-muted-foreground">Platform fee</span>
                    <span>${deal.platform_fee ? fmtCurrency(deal.platform_fee) : '—'}</span>
                </div>
                <div class="flex justify-between py-1.5 border-b border-border">
                    <span class="text-muted-foreground">Deposit hold</span>
                    <span>${deal.deposit_hold ? fmtCurrency(deal.deposit_hold) : '—'}</span>
                </div>
                <div class="flex justify-between py-1.5">
                    <span class="text-muted-foreground">Payout state</span>
                    <span>${deal.payout_state ?? 'Not requested'}</span>
                </div>
            </div>
        `;

                // Activity
                const acts = deal.activity ?? [];
                $('#qv-tab-activity').innerHTML = acts.length ?
                    acts.slice().reverse().map(a => `
                <div class="flex gap-2 text-xs">
                    <div class="w-2 h-2 rounded-full bg-primary mt-1.5 shrink-0"></div>
                    <div><div class="font-medium">${a.description}</div><div class="text-muted-foreground">${a.date}</div></div>
                </div>`).join('') :
                    '<p class="text-xs text-muted-foreground">No activity.</p>';

                // Footer
                $('#qv-footer')?.classList.remove('hidden');
                $('#qv-footer-actions').innerHTML = `
            <button onclick="window.location='/deals/${id}'" class="kt-btn kt-btn-outline kt-btn-sm">
                Open full detail
            </button>
            ${deal.state === 'Awaiting payout' ? `<button data-action="approve-payout" data-id="${id}"
                    class="kt-btn kt-btn-mono kt-btn-sm ml-auto">Approve payout</button>` : ''}
        `;

                window.CS4.switchQvTab('overview');
                if (typeof lucide !== 'undefined') lucide.createIcons();
            }

            /* ── Row action delegation ──────────────────────────────────────────── */
            document.addEventListener('click', e => {
                const btn = e.target.closest('[data-action]');
                if (!btn) return;
                const action = btn.dataset.action;
                const id = btn.dataset.id;

                if (action === 'quick-view') renderQV(id);
                if (action === 'assign-owner') {
                    $('#assign-deal-id').value = id;
                    openModal('modal-assign-owner');
                }
                if (action === 'cancel-rerun') {
                    $('#cancel-deal-id').value = id;
                    openModal('modal-cancel-rerun');
                }
                if (action === 'open-dispute') window.location = '/disputes/create?deal=' + id;
                if (action === 'approve-payout') window.location = '/payments/payouts?deal=' + id;
                if (action === 'start-collection') window.location = '/logistics/jobs/create?deal=' + id;
            });

            /* ── Cancel confirm ─────────────────────────────────────────────────── */
            $('#btn-confirm-cancel')?.addEventListener('click', () => {
                const id = $('#cancel-deal-id').value;
                const reason = $('#cancel-reason').value.trim();
                if (!reason) {
                    toast('Reason is required.', 'warning');
                    return;
                }
                auditEvent('deal_cancelled', {
                    id,
                    reason
                });
                toast('Deal cancelled. Re-run queued.', 'success');
                closeModal('modal-cancel-rerun');
            });

            /* ── Assign confirm ─────────────────────────────────────────────────── */
            $('#btn-confirm-assign')?.addEventListener('click', () => {
                const id = $('#assign-deal-id').value;
                const owner = $('#assign-owner-select').value;
                const deal = DEALS.find(d => d.id === id);
                if (deal) deal.owner = owner;
                auditEvent('deal_owner_changed', {
                    id,
                    owner
                });
                toast('Owner assigned: ' + owner, 'success');
                closeModal('modal-assign-owner');
                renderQV(id);
            });

            /* ── Export ─────────────────────────────────────────────────────────── */
            $('#btn-export-deals')?.addEventListener('click', () => toast('Export queued — check your email.', 'info'));
            $('#btn-refresh-deals')?.addEventListener('click', () => toast('Refreshed.', 'info'));

        })();
    </script>

@endsection
