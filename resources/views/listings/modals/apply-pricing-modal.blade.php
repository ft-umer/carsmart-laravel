{{--
    L4 — Apply Pricing Modal
    Delta preview + BIN conflict warning + confirm.
--}}
<div id="apply-pricing-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">

    <div class="fixed inset-0 bg-black/50" onclick="document.getElementById('apply-pricing-modal').classList.add('hidden');document.getElementById('apply-pricing-modal').classList.remove('flex');"></div>

    <div class="relative w-full max-w-md card rounded-xl border border-border bg-background shadow-xl">

        <div class="p-4 border-b border-border flex items-center justify-between">
            <h3 class="text-lg font-semibold">Apply Valuation to Pricing</h3>
            <button class="kt-btn kt-btn-sm kt-btn-ghost" onclick="document.getElementById('apply-pricing-modal').classList.add('hidden');document.getElementById('apply-pricing-modal').classList.remove('flex');">✕</button>
        </div>

        <div class="p-4 space-y-4">

            {{-- Valuation being applied --}}
            <div class="rounded-xl bg-muted/20 border border-border p-3 text-sm">
                <div class="text-xs text-muted-foreground mb-1">Valuation to apply</div>
                <div class="font-semibold text-lg">£14,200</div>
                <div class="text-xs text-muted-foreground">Carsmart · 2 hours ago</div>
            </div>

            {{-- Choose where to apply --}}
            <div class="space-y-3">
                <div class="text-xs font-medium">Apply to</div>

                <label class="flex items-start gap-3 card border border-border p-3 rounded-xl cursor-pointer hover:bg-muted/10">
                    <input type="checkbox" id="confirm-apply-guide" class="kt-checkbox mt-0.5" checked>
                    <div>
                        <div class="font-medium text-sm">Guide Price</div>
                        <div class="text-xs text-muted-foreground mt-0.5">
                            Current: £14,250 → New: £14,200
                            <span class="ml-1 text-red-500">−£50 (−0.4%)</span>
                        </div>
                    </div>
                </label>

                <label class="flex items-start gap-3 card border border-border p-3 rounded-xl cursor-pointer hover:bg-muted/10">
                    <input type="checkbox" id="confirm-apply-reserve" class="kt-checkbox mt-0.5">
                    <div>
                        <div class="font-medium text-sm">Reserve Price</div>
                        <div class="text-xs text-muted-foreground mt-0.5">
                            Current: £14,000 → New: £14,200
                            <span class="ml-1 text-green-600">+£200 (+1.4%)</span>
                        </div>
                    </div>
                </label>
            </div>

            {{-- BIN conflict warning (shown when reserve applied and BIN active) --}}
            <div id="apply-bin-warning" class="hidden rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                Warning: BIN is active (£15,495). Applying this valuation as Reserve is permitted (Reserve £14,200 &lt; BIN £15,495). Verify this is correct before confirming.
            </div>

            {{-- Confirm note --}}
            <div class="text-xs text-muted-foreground">
                This action will update the listing's Pricing tab and create an audit entry with before → after delta.
                Event: <span class="font-mono">valuation_applied</span>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" class="kt-btn kt-btn-ghost"
                    onclick="document.getElementById('apply-pricing-modal').classList.add('hidden');document.getElementById('apply-pricing-modal').classList.remove('flex');">
                    Cancel
                </button>
                <button type="button" class="kt-btn kt-btn-mono" id="confirm-apply-btn">Confirm & Apply</button>
            </div>

        </div>

    </div>

</div>

<script>
(function () {
    const reserveCheck = document.getElementById('confirm-apply-reserve');
    const binWarning = document.getElementById('apply-bin-warning');
    const binActive = true; // TODO: derive from listing data

    reserveCheck?.addEventListener('change', () => {
        if (binActive && reserveCheck.checked) {
            binWarning?.classList.remove('hidden');
        } else {
            binWarning?.classList.add('hidden');
        }
    });

    document.getElementById('confirm-apply-btn')?.addEventListener('click', () => {
        // TODO: POST /listings/{id}/valuations/apply
        // Events: valuation_applied
        document.getElementById('apply-pricing-modal')?.classList.add('hidden');
        document.getElementById('apply-pricing-modal')?.classList.remove('flex');
    });
})();
</script>
