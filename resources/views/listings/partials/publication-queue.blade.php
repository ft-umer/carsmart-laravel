{{--
    L5 — Publication Queue
    Final pre-publish checks and scheduling.
--}}
<div class="space-y-4">

    {{-- Table --}}
    <div class="card border border-border overflow-auto">
        <table class="w-full text-sm min-w-[1100px]">
            <thead class="bg-muted/40">
                <tr>
                    <th class="p-3 text-left">Listing</th>
                    <th class="p-3 text-left">Vehicle</th>
                    <th class="p-3 text-center">QA</th>
                    <th class="p-3 text-center">KYC</th>
                    <th class="p-3 text-center">Photos</th>
                    <th class="p-3 text-center">Docs</th>
                    <th class="p-3 text-center">Pricing</th>
                    <th class="p-3 text-left">Channel</th>
                    <th class="p-3 text-left">Scheduled</th>
                    <th class="p-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @php
                $pubRows = [
                    ['id'=>'LST-1024','vehicle'=>'Audi A6 Avant 2017','qa'=>true,'kyc'=>true,'photos'=>true,'docs'=>true,'pricing'=>true,'channel'=>'BIN/Offer','scheduled'=>'2026-06-01 10:00','blocked'=>false],
                    ['id'=>'LST-1028','vehicle'=>'Porsche 911 Carrera 2018','qa'=>true,'kyc'=>false,'photos'=>true,'docs'=>false,'pricing'=>true,'channel'=>'Auction','scheduled'=>'—','blocked'=>true],
                ];
                @endphp
                @foreach($pubRows as $row)
                    @php $blocked = $row['blocked']; @endphp
                    <tr class="border-t border-border {{ $blocked ? 'opacity-60' : '' }}">
                        <td class="p-3 font-medium">{{ $row['id'] }}</td>
                        <td class="p-3">{{ $row['vehicle'] }}</td>
                        @foreach(['qa','kyc','photos','docs','pricing'] as $gate)
                            <td class="p-3 text-center">
                                @if($row[$gate])
                                    <span class="text-green-600">✔</span>
                                @else
                                    <span class="text-red-500">✖</span>
                                @endif
                            </td>
                        @endforeach
                        <td class="p-3">{{ $row['channel'] }}</td>
                        <td class="p-3 text-muted-foreground">{{ $row['scheduled'] }}</td>
                        <td class="p-3 text-right">
                            <div class="flex justify-end gap-1">
                                <button class="kt-btn kt-btn-xs kt-btn-outline">Preview</button>
                                @if(!$blocked)
                                    <button class="kt-btn kt-btn-xs kt-btn-mono">Publish Now</button>
                                    <button class="kt-btn kt-btn-xs kt-btn-outline">Schedule</button>
                                    <button class="kt-btn kt-btn-xs kt-btn-outline">Create Auction</button>
                                @else
                                    <span class="kt-badge kt-badge-danger text-xs">Blocked</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Right panel --}}
    <div class="card border border-border p-4">
        <div class="font-semibold mb-3">Pre-Publish Checklist</div>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3 text-sm">
            @foreach(['QA Pass','KYC Verified','Required Photos','Required Docs','Pricing Set'] as $gate)
                <div class="card border border-border p-2 text-center">
                    <div class="text-green-600 text-lg">✔</div>
                    <div class="text-xs mt-1">{{ $gate }}</div>
                </div>
            @endforeach
        </div>
        <div class="mt-4 text-xs text-muted-foreground">
            Publish is gated — all five checks must pass. Editions listings may require additional content fields and pro photography confirmation.
        </div>
    </div>

</div>
