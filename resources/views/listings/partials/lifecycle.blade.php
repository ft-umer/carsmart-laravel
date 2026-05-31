{{--
    L8 — Lifecycle: States and Transitions
    Visual state machine for a listing's journey.
--}}
<div class="space-y-4">

    <div>
        <h3 class="font-semibold">Listing Lifecycle</h3>
        <div class="text-xs text-muted-foreground">Canonical states and permitted transitions. Automations fire on state change.</div>
    </div>

    {{-- State diagram (simplified visual) --}}
    <div class="card border border-border p-4 overflow-x-auto">
        <div class="flex items-center gap-2 flex-wrap text-xs font-medium min-w-max">
            @php
            $states = [
                ['Draft', 'bg-muted text-foreground', false],
                ['QA', 'bg-yellow-100 text-yellow-800', false],
                ['Ready', 'bg-blue-100 text-blue-800', false],
                ['Published', 'bg-green-100 text-green-800', false],
                ['Live', 'bg-green-500 text-white', true],
                ['Ended', 'bg-muted text-foreground', false],
                ['Deal Pending', 'bg-orange-100 text-orange-700', false],
                ['Handover', 'bg-purple-100 text-purple-700', false],
                ['Closed', 'bg-gray-200 text-gray-600', false],
            ];
            @endphp
            @foreach($states as [$state,$cls,$active])
                <div class="flex items-center gap-1">
                    <span class="px-3 py-1.5 rounded-full {{ $cls }} {{ $active ? 'ring-2 ring-offset-1 ring-green-500' : '' }}">
                        {{ $state }}
                    </span>
                    @if(!$loop->last)
                        <span class="text-muted-foreground">→</span>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Side paths --}}
        <div class="flex gap-2 mt-3 flex-wrap text-xs">
            <span class="px-3 py-1 rounded-full bg-red-100 text-red-700">Failed QA</span>
            <span class="px-3 py-1 rounded-full bg-muted text-foreground">Duplicate</span>
            <span class="px-3 py-1 rounded-full bg-muted text-foreground">Archived</span>
            <span class="px-3 py-1 rounded-full bg-blue-50 text-blue-700">Assigned to Auction (from Ready)</span>
        </div>
    </div>

    {{-- Transition table --}}
    <div class="card border border-border overflow-auto">
        <table class="w-full text-sm">
            <thead class="bg-muted/40">
                <tr>
                    <th class="p-3 text-left">From</th>
                    <th class="p-3 text-left">To</th>
                    <th class="p-3 text-left">Trigger / Gate</th>
                    <th class="p-3 text-left">Events</th>
                </tr>
            </thead>
            <tbody>
                @php
                $transitions = [
                    ['Draft','QA','Submit action by CRM/Admin','listing_state_changed, listing_submitted_for_qa'],
                    ['QA','Ready','Pass checklist (all required items present)','qa_passed'],
                    ['QA','Failed QA','Fail with reasons','qa_failed'],
                    ['Ready','Published','Publish action — QA Pass + KYC Verified + Photos + Docs + Pricing','listing_published'],
                    ['Ready','Assigned to Auction','Create/assign auction','auction_assigned'],
                    ['Published','Live','Auction goes live / BIN listing active','listing_state_changed'],
                    ['Live','Ended','Auction ends (sniper protection may extend)','listing_state_changed'],
                    ['Ended','Deal Pending','Reserve met or BIN/Offer accepted','listing_state_changed'],
                    ['Deal Pending','Handover','Collection booked + seller-objection window acknowledged','listing_state_changed'],
                    ['Handover','Closed','Payout approved and completed','listing_state_changed'],
                    ['Any','Archived','Archive action (role-gated for financial listings)','listing_bulk_updated'],
                ];
                @endphp
                @foreach($transitions as [$from,$to,$gate,$events])
                    <tr class="border-t border-border">
                        <td class="p-3 font-medium">{{ $from }}</td>
                        <td class="p-3">{{ $to }}</td>
                        <td class="p-3 text-muted-foreground">{{ $gate }}</td>
                        <td class="p-3 text-xs font-mono text-muted-foreground">{{ $events }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Automations --}}
    <div class="card border border-border p-4">
        <div class="font-semibold mb-2 text-sm">Automations</div>
        <div class="space-y-1 text-sm text-muted-foreground">
            <div>• Missing photos/docs/pricing → reminder triggered to owner</div>
            <div>• Reserve not set → prompt before scheduling publication</div>
            <div>• KYC pending → compliance task created and publication blocked</div>
            <div>• Valuation outdated (>7 days) → nudge to Pull Latest Valuation</div>
        </div>
    </div>

</div>
