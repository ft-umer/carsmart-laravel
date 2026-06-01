{{-- resources/views/payments/wallets/index.blade.php --}}
{{-- Phase 4 — P2: Payments → Wallets --}}
@extends('layouts.app')
@section('title', 'Wallets — Payments')

@section('content')

@include('partials._retention_banner')

<div class="kt-container-fixed">
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
    <h1 class="text-xl font-semibold text-foreground">Vendor Wallets</h1>
    <div class="flex gap-2">
        <button id="btn-add-adjustment" class="kt-btn kt-btn-outline">
            <i data-lucide="plus-circle" class="w-4 h-4 mr-1"></i>Add adjustment
        </button>
    </div>
</div>

<div class="card border border-border rounded-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full min-w-[700px] text-sm">
            <thead class="bg-muted/40 sticky top-0 z-10">
                <tr>
                    @foreach (['Vendor','Balance','Holds','Last payout','Status','Actions'] as $col)
                        <th class="p-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wide">{{ $col }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-border bg-background">
                @forelse ($wallets as $w)
                    @php
                        $wStatusCls = match ($w['status'] ?? '') {
                            'Clear'    => 'kt-badge-success',
                            'Flagged'  => 'kt-badge-warning',
                            'Frozen'   => 'kt-badge-destructive',
                            default    => 'kt-badge-outline',
                        };
                    @endphp
                    <tr class="hover:bg-muted/30 transition-colors">
                        <td class="p-3">
                            <div class="font-medium">{{ $w['vendor_name'] }}</div>
                            <div class="text-xs text-muted-foreground font-mono">{{ $w['vendor_id'] }}</div>
                        </td>
                        <td class="p-3 font-semibold text-sm">£{{ number_format($w['balance'] ?? 0) }}</td>
                        <td class="p-3">
                            @if ($w['holds'] ?? 0)
                                <span class="text-sm font-medium text-amber-600 dark:text-amber-400">
                                    £{{ number_format($w['holds']) }}
                                </span>
                            @else
                                <span class="text-xs text-muted-foreground">—</span>
                            @endif
                        </td>
                        <td class="p-3 text-xs text-muted-foreground">{{ $w['last_payout_date'] ?? '—' }}</td>
                        <td class="p-3"><span class="kt-badge {{ $wStatusCls }} kt-badge-sm">{{ $w['status'] ?? 'Clear' }}</span></td>
                        <td class="p-3">
                            <a href="{{ route('payments.wallets.show', $w['id']) }}"
                               class="kt-btn kt-btn-outline kt-btn-sm">Open</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-12 text-center text-muted-foreground text-sm">
                            <i data-lucide="wallet" class="w-8 h-8 mx-auto mb-2 opacity-30"></i>
                            No wallets found.
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
document.getElementById('btn-add-adjustment')?.addEventListener('click', () => window.CS4.toast('Open a wallet to add an adjustment.', 'info'));
</script>

@endsection
