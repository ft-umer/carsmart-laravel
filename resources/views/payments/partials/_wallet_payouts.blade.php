{{-- resources/views/payments/partials/_wallet_payouts.blade.php --}}
<div class="card border border-border rounded-xl overflow-hidden">
    <div class="px-4 py-3 border-b border-border bg-muted/20 flex items-center justify-between">
        <h3 class="text-sm font-semibold">Payout requests</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-xs">
            <thead class="bg-muted/40">
                <tr>
                    @foreach(['Requested','Amount','Destination','Deal','Note','Requested by','Status','Actions'] as $col)
                        <th class="p-3 text-left font-semibold text-muted-foreground uppercase tracking-wide">{{ $col }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-border bg-background">
                @forelse ($wallet['payouts'] ?? [] as $p)
                    @php
                        $pCls = match($p['status'] ?? '') {
                            'Approved' => 'kt-badge-success',
                            'Rejected' => 'kt-badge-destructive',
                            'Pending'  => 'kt-badge-warning',
                            default    => 'kt-badge-outline',
                        };
                    @endphp
                    <tr class="hover:bg-muted/20 transition-colors">
                        <td class="p-3 text-muted-foreground whitespace-nowrap">{{ $p['requested_at'] ?? '—' }}</td>
                        <td class="p-3 font-semibold">£{{ number_format($p['amount'] ?? 0) }}</td>
                        <td class="p-3">{{ $p['destination'] ?? '—' }}</td>
                        <td class="p-3 font-mono">{{ $p['deal_ref'] ?? '—' }}</td>
                        <td class="p-3 max-w-[160px] truncate" title="{{ $p['note'] ?? '' }}">
                            {{ $p['note'] ?? '—' }}
                        </td>
                        <td class="p-3">{{ $p['requested_by'] ?? '—' }}</td>
                        <td class="p-3"><span class="kt-badge {{ $pCls }} kt-badge-sm">{{ $p['status'] ?? '—' }}</span></td>
                        <td class="p-3">
                            @if (($p['status'] ?? '') === 'Pending')
                                <div class="flex gap-1.5">
                                    <button data-action="approve-payout" data-id="{{ $p['id'] }}"
                                            class="kt-btn kt-btn-mono kt-btn-sm">Approve</button>
                                    <button data-action="reject-payout" data-id="{{ $p['id'] }}"
                                            class="kt-btn kt-btn-outline kt-btn-sm text-destructive">Reject</button>
                                </div>
                            @else
                                <span class="text-xs text-muted-foreground">{{ $p['resolved_at'] ?? '—' }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="p-8 text-center text-muted-foreground">No payout requests.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
