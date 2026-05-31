{{--
    L6 — Exchange Proposals (Vendor ↔ Vendor, Pre-end)
    Allow proposals to swap vehicles with optional cash difference before auction end.
    Limit: 1 active proposal per listing.
--}}
<div class="space-y-4">

    <div class="flex items-center justify-between">
        <div>
            <h3 class="font-semibold">Exchange Proposals</h3>
            <div class="text-xs text-muted-foreground">Maximum 1 active proposal per listing. Acceptance creates a linked deal draft.</div>
        </div>
        <button class="kt-btn kt-btn-sm kt-btn-mono" id="btn-create-proposal">Create Proposal</button>
    </div>

    @php
    $proposals = [
        ['id'=>'EXP-001','listing'=>'LST-1023','offered_by'=>'Vendor A','offered_vehicle'=>'Ford Mustang GT 2020','offered_vrm'=>'FD20 XYZ','offered_guide'=>16500,'cash_diff'=>1500,'expiry'=>'2026-06-03','notes'=>'Good condition, low miles','status'=>'Active'],
        ['id'=>'EXP-002','listing'=>'LST-1024','offered_by'=>'Vendor B','offered_vehicle'=>'Honda Civic Type R 2019','offered_vrm'=>'HN19 ABC','offered_guide'=>19000,'cash_diff'=>0,'expiry'=>'2026-05-28','notes'=>'','status'=>'Expired'],
    ];
    @endphp

    <div class="space-y-3">
        @foreach($proposals as $p)
            <div class="card border border-border p-4 rounded-2xl">
                <div class="flex items-start justify-between flex-wrap gap-3">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-semibold">{{ $p['id'] }}</span>
                            <span class="kt-badge {{ $p['status']==='Active' ? 'kt-badge-warning' : 'kt-badge-outline' }}">{{ $p['status'] }}</span>
                        </div>
                        <div class="text-sm text-muted-foreground mb-2">
                            Offered by {{ $p['offered_by'] }} on {{ $p['listing'] }}
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                            <div><div class="text-xs text-muted-foreground">Offered Vehicle</div><div class="font-medium">{{ $p['offered_vehicle'] }}</div></div>
                            <div><div class="text-xs text-muted-foreground">VRM</div><div class="font-medium">{{ $p['offered_vrm'] }}</div></div>
                            <div><div class="text-xs text-muted-foreground">Guide</div><div class="font-medium">£{{ number_format($p['offered_guide']) }}</div></div>
                            <div><div class="text-xs text-muted-foreground">Cash Difference</div><div class="font-medium {{ $p['cash_diff'] > 0 ? 'text-green-600' : '' }}">{{ $p['cash_diff'] > 0 ? '+£'.number_format($p['cash_diff']) : '—' }}</div></div>
                            <div><div class="text-xs text-muted-foreground">Expiry</div><div class="font-medium">{{ $p['expiry'] }}</div></div>
                            @if($p['notes'])
                                <div class="md:col-span-3"><div class="text-xs text-muted-foreground">Notes</div><div>{{ $p['notes'] }}</div></div>
                            @endif
                        </div>
                    </div>
                    @if($p['status'] === 'Active')
                        <div class="flex gap-2">
                            <button class="kt-btn kt-btn-sm kt-btn-mono">Accept</button>
                            <button class="kt-btn kt-btn-sm kt-btn-outline">Counter</button>
                            <button class="kt-btn kt-btn-sm kt-btn-ghost text-danger">Decline</button>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

</div>
