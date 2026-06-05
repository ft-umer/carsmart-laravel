{{-- resources/views/disputes/partials/_financials_tab.blade.php --}}
{{-- Phase 4 — S2: Dispute Case — Financials tab --}}
<div class="space-y-5">

    {{-- Current deal financials --}}
    <div class="card border border-border rounded-xl p-4">
        <h4 class="font-semibold text-sm mb-3">Deal financials</h4>
        <dl class="grid grid-cols-2 gap-x-6 gap-y-2 text-sm">
            <div><dt class="text-muted-foreground">Agreed price</dt><dd class="font-medium">£14,000</dd></div>
            <div><dt class="text-muted-foreground">Platform fee</dt><dd>£350</dd></div>
            <div><dt class="text-muted-foreground">Seller payout (expected)</dt><dd>£13,650</dd></div>
            <div><dt class="text-muted-foreground">Buyer card hold</dt><dd>£14,000</dd></div>
            <div><dt class="text-muted-foreground">Payout status</dt><dd><span class="kt-badge kt-badge-warning kt-badge-sm">Held — dispute open</span></dd></div>
        </dl>
    </div>

    {{-- Decide outcome --}}
    <div class="card border border-border rounded-xl p-5">
        <div class="flex items-center justify-between mb-4">
            <h4 class="font-semibold text-sm">Decide outcome</h4>
            <span class="kt-badge kt-badge-warning kt-badge-sm">Decision pending</span>
        </div>

        <div class="space-y-3 mb-5">
            @foreach ([
                ['price_adjustment','Price adjustment','Adjust the agreed price up or down; buyer or seller absorbs the difference.'],
                ['cancel_rerun','Cancel & re-run','Cancel this deal and re-list the vehicle for auction.'],
                ['vendor_charge','Vendor charge','Charge the buyer vendor for a specific amount (e.g. damage, transport cost).'],
                ['partial_refund','Partial refund','Refund a portion of the buyer\'s payment.'],
                ['note_only','Note only','Record a decision note without financial action.'],
            ] as [$val, $label, $desc])
                <label class="flex items-start gap-3 p-3 rounded-lg border border-border cursor-pointer hover:bg-muted/20 transition-colors outcome-option"
                    onclick="selectOutcome('{{ $val }}')">
                    <input type="radio" name="outcome_type" value="{{ $val }}" class="kt-radio mt-0.5">
                    <div>
                        <p class="font-medium text-sm">{{ $label }}</p>
                        <p class="text-xs text-muted-foreground mt-0.5">{{ $desc }}</p>
                    </div>
                </label>
            @endforeach
        </div>

        {{-- Price adjustment fields --}}
        <div id="outcome-price_adjustment" class="outcome-fields hidden rounded-lg bg-muted/30 p-4 space-y-3 mb-4">
            <h5 class="font-medium text-sm">Price adjustment</h5>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <label class="form-label">Adjusted price (£)</label>
                    <input type="number" value="13500" class="kt-input w-full">
                </div>
                <div>
                    <label class="form-label">Direction</label>
                    <select class="kt-select w-full">
                        <option>Buyer pays less (price down)</option>
                        <option>Buyer pays more (price up)</option>
                    </select>
                </div>
            </div>
            <div class="rounded-lg bg-card border border-border p-3 text-sm">
                <p class="text-muted-foreground text-xs mb-1">Delta preview</p>
                <p>Agreed price: £14,000 → Adjusted: £13,500 <span class="text-destructive">(-£500)</span></p>
                <p class="text-xs text-muted-foreground mt-1">Seller payout will be reduced by £500. Platform fee unchanged.</p>
            </div>
        </div>

        {{-- Cancel & re-run fields --}}
        <div id="outcome-cancel_rerun" class="outcome-fields hidden rounded-lg bg-muted/30 p-4 space-y-3 mb-4">
            <h5 class="font-medium text-sm">Cancel & re-run</h5>
            <div>
                <label class="form-label">Reason for cancellation</label>
                <textarea rows="3" class="kt-textarea w-full text-sm" placeholder="Describe why the deal is being cancelled…"></textarea>
            </div>
            <div class="rounded-lg bg-warning/10 border border-warning/20 p-3 text-sm">
                <i data-lucide="alert-circle" class="w-4 h-4 inline text-warning mr-1"></i>
                The deal will be cancelled and the vehicle re-listed. The buyer hold will be released.
            </div>
        </div>

        {{-- Vendor charge fields --}}
        <div id="outcome-vendor_charge" class="outcome-fields hidden rounded-lg bg-muted/30 p-4 space-y-3 mb-4">
            <h5 class="font-medium text-sm">Vendor charge</h5>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Amount (£)</label>
                    <input type="number" placeholder="0.00" class="kt-input w-full">
                </div>
                <div>
                    <label class="form-label">Charge to</label>
                    <select class="kt-select w-full"><option>Buyer (Fast Cars Ltd)</option><option>Seller</option></select>
                </div>
            </div>
            <div><label class="form-label">Description</label>
                <input type="text" class="kt-input w-full" placeholder="e.g. Damage repair cost">
            </div>
        </div>

        {{-- Partial refund fields --}}
        <div id="outcome-partial_refund" class="outcome-fields hidden rounded-lg bg-muted/30 p-4 space-y-3 mb-4">
            <h5 class="font-medium text-sm">Partial refund</h5>
            <div>
                <label class="form-label">Refund amount (£)</label>
                <input type="number" placeholder="0.00" class="kt-input w-full">
            </div>
            <div><label class="form-label">Reason</label>
                <textarea rows="2" class="kt-textarea w-full text-sm" placeholder="Reason for partial refund…"></textarea>
            </div>
        </div>

        {{-- Note only fields --}}
        <div id="outcome-note_only" class="outcome-fields hidden rounded-lg bg-muted/30 p-4 space-y-3 mb-4">
            <h5 class="font-medium text-sm">Decision note</h5>
            <div>
                <label class="form-label">Note *</label>
                <textarea rows="4" class="kt-textarea w-full text-sm" placeholder="Record the decision rationale…"></textarea>
            </div>
        </div>

        <div>
            <label class="form-label">Internal decision note (all outcomes)</label>
            <textarea rows="3" class="kt-textarea w-full text-sm mb-3"
                placeholder="Summarise the decision for the audit log…"></textarea>
        </div>

        <div class="flex gap-2 pt-2 border-t border-border">
            <button class="kt-btn kt-btn-outline">Save draft decision</button>
            <button class="kt-btn kt-btn-primary ml-auto" onclick="openModal('modal-confirm-outcome')">
                Apply outcome & post to financials
            </button>
        </div>
    </div>

    {{-- Approval log --}}
    <div class="card border border-border rounded-xl p-4">
        <h4 class="font-semibold text-sm mb-3">Outcome approval log</h4>
        <div class="text-sm text-muted-foreground">No outcome applied yet.</div>
    </div>

</div>

{{-- Confirm outcome modal --}}
<div id="modal-confirm-outcome" class="fixed inset-0 z-[10001] hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('modal-confirm-outcome')"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="card w-full max-w-md border border-border rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-border flex items-center justify-between">
                <h3 class="font-semibold">Confirm outcome</h3>
                <button onclick="closeModal('modal-confirm-outcome')" class="kt-btn kt-btn-ghost kt-btn-sm">✕</button>
            </div>
            <div class="p-5 text-sm space-y-3">
                <p>Applying this outcome will post entries to the wallet/charges ledger and update the deal. This action is logged and cannot be undone without a new adjustment.</p>
                <div class="rounded-lg bg-muted/40 p-3">
                    <p class="font-medium mb-1">Outcome: Price adjustment</p>
                    <p class="text-muted-foreground">£14,000 → £13,500 · Seller absorbs -£500</p>
                </div>
            </div>
            <div class="px-5 py-4 border-t border-border flex justify-end gap-2">
                <button onclick="closeModal('modal-confirm-outcome')" class="kt-btn kt-btn-outline">Cancel</button>
                <button class="kt-btn kt-btn-primary">Confirm & apply</button>
            </div>
        </div>
    </div>
</div>

<script>
function selectOutcome(val) {
    document.querySelectorAll('.outcome-fields').forEach(el => el.classList.add('hidden'));
    document.getElementById('outcome-' + val)?.classList.remove('hidden');
}
function openModal(id) { document.getElementById(id)?.classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id)?.classList.add('hidden'); }
</script>
