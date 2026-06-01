{{-- resources/views/deals/partials/_financials_tab.blade.php --}}
<div class="space-y-4">

    {{-- Payout request summary --}}
    <div class="card border border-border rounded-xl p-4 space-y-3">
        <h3 class="text-sm font-semibold">Payout status</h3>
        @if ($deal['payout_request'] ?? null)
            @php $req = $deal['payout_request']; @endphp
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                <div><span class="text-muted-foreground">Amount</span><br><strong>£{{ number_format($req['amount'] ?? 0) }}</strong></div>
                <div><span class="text-muted-foreground">Destination</span><br><strong>{{ $req['destination'] ?? '—' }}</strong></div>
                <div><span class="text-muted-foreground">Requested by</span><br><strong>{{ $req['requested_by'] ?? '—' }}</strong></div>
                <div><span class="text-muted-foreground">Status</span><br>
                    @php
                        $payoutCls = match ($req['status'] ?? '') {
                            'Pending'  => 'kt-badge-warning',
                            'Approved' => 'kt-badge-success',
                            'Rejected' => 'kt-badge-destructive',
                            default    => 'kt-badge-outline',
                        };
                    @endphp
                    <span class="kt-badge {{ $payoutCls }} kt-badge-sm">{{ $req['status'] ?? '—' }}</span>
                </div>
            </div>
            @if ($req['note'] ?? null)
                <div class="text-xs bg-muted/30 border border-border rounded-lg px-3 py-2">
                    <span class="text-muted-foreground">Note: </span>{{ $req['note'] }}
                </div>
            @endif
            {{-- Approval log --}}
            @if (!empty($req['approvals']))
                <div class="border-t border-border pt-3">
                    <h4 class="text-xs font-semibold text-muted-foreground uppercase tracking-wide mb-2">Approval log</h4>
                    <div class="space-y-2">
                        @foreach ($req['approvals'] as $apr)
                            <div class="flex items-center gap-3 text-xs">
                                <span class="kt-badge {{ $apr['action'] === 'Approved' ? 'kt-badge-success' : 'kt-badge-destructive' }} kt-badge-sm">
                                    {{ $apr['action'] }}
                                </span>
                                <span class="font-medium">{{ $apr['by'] ?? '—' }}</span>
                                <span class="text-muted-foreground">{{ $apr['at'] ?? '' }}</span>
                                @if ($apr['note'] ?? null)
                                    <span class="text-muted-foreground">— {{ $apr['note'] }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @else
            <p class="text-sm text-muted-foreground">No payout request yet.</p>
        @endif
    </div>

    {{-- Deposit / holds --}}
    <div class="card border border-border rounded-xl p-4 space-y-3">
        <h3 class="text-sm font-semibold">Deposit hold</h3>
        @if ($deal['deposit_hold'] ?? null)
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
                <div><span class="text-muted-foreground">Amount</span><br><strong>£{{ number_format($deal['deposit_hold']) }}</strong></div>
                <div><span class="text-muted-foreground">Status</span><br>
                    <span class="kt-badge kt-badge-warning kt-badge-sm">Active</span>
                </div>
                <div><span class="text-muted-foreground">Expires</span><br><strong>{{ $deal['deposit_hold_expiry'] ?? '—' }}</strong></div>
            </div>
        @else
            <p class="text-xs text-muted-foreground">No active hold.</p>
        @endif
    </div>
</div>
