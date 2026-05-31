{{--
    L4 — Add Valuation Modal
    Manual valuation entry: source, amount, notes, valuer, comps, apply to guide/reserve.
--}}
<div id="add-valuation-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">

    <div class="fixed inset-0 bg-black/50" onclick="document.getElementById('add-valuation-modal').classList.add('hidden');document.getElementById('add-valuation-modal').classList.remove('flex');"></div>

    <div class="relative w-full max-w-lg card rounded-xl border border-border bg-background shadow-xl">

        <div class="p-4 border-b border-border flex items-center justify-between">
            <h3 class="text-lg font-semibold">Add Valuation</h3>
            <button class="kt-btn kt-btn-sm kt-btn-ghost" onclick="document.getElementById('add-valuation-modal').classList.add('hidden');document.getElementById('add-valuation-modal').classList.remove('flex');">✕</button>
        </div>

        <form class="p-4 space-y-4" id="add-valuation-form">

            {{-- Source type --}}
            <div>
                <label class="block text-xs font-medium mb-1">Source Type <span class="text-red-500">*</span></label>
                <select class="kt-input w-full" id="valuation-source-type">
                    <option value="">Select source type</option>
                    <option value="Internal">Internal</option>
                    <option value="External">External</option>
                </select>
            </div>

            {{-- External provider name (shown when External selected) --}}
            <div id="valuation-provider-wrap" class="hidden">
                <label class="block text-xs font-medium mb-1">Provider Name</label>
                <input type="text" class="kt-input w-full" placeholder="e.g. HPI, Autotrader, Motorway, CAP…">
            </div>

            {{-- Amount --}}
            <div>
                <label class="block text-xs font-medium mb-1">Amount (£) <span class="text-red-500">*</span></label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground">£</span>
                    <input type="number" class="kt-input w-full pl-7" placeholder="14250" id="valuation-amount">
                </div>
            </div>

            {{-- Valuer --}}
            <div>
                <label class="block text-xs font-medium mb-1">Valuer</label>
                <input type="text" class="kt-input w-full" placeholder="Name or system">
            </div>

            {{-- Comps --}}
            <div>
                <label class="block text-xs font-medium mb-1">Comparable Listings (URLs or IDs)</label>
                <textarea class="kt-input w-full" rows="2" placeholder="https://... or LST-XXXX, one per line"></textarea>
            </div>

            {{-- Notes --}}
            <div>
                <label class="block text-xs font-medium mb-1">Notes</label>
                <textarea class="kt-input w-full" rows="3" placeholder="Reasoning, market context…"></textarea>
            </div>

            {{-- Apply to Guide / Reserve --}}
            <div class="rounded-xl border border-border p-3 space-y-2">
                <div class="text-xs font-medium mb-1">Apply to Pricing</div>
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" id="apply-guide" class="kt-checkbox"> Apply as Guide Price
                </label>
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" id="apply-reserve" class="kt-checkbox"> Apply as Reserve Price
                </label>
                {{-- Delta preview --}}
                <div id="valuation-delta-preview" class="hidden mt-2 text-xs text-muted-foreground rounded-lg bg-muted/30 px-3 py-2">
                    Delta preview: Guide £14,250 → <span id="preview-guide">—</span> &nbsp;|&nbsp; Reserve £14,000 → <span id="preview-reserve">—</span>
                </div>
                {{-- BIN conflict warning --}}
                <div id="bin-conflict-warning" class="hidden mt-2 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs text-red-700">
                    Warning: Reserve cannot exceed BIN price (£15,495). Please adjust amount or disable BIN first.
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" class="kt-btn kt-btn-ghost"
                    onclick="document.getElementById('add-valuation-modal').classList.add('hidden');document.getElementById('add-valuation-modal').classList.remove('flex');">
                    Cancel
                </button>
                <button type="submit" class="kt-btn kt-btn-mono">Save Valuation</button>
            </div>

        </form>

    </div>

</div>

<script>
(function () {
    const sourceType = document.getElementById('valuation-source-type');
    const providerWrap = document.getElementById('valuation-provider-wrap');
    const amountInput = document.getElementById('valuation-amount');
    const applyGuide = document.getElementById('apply-guide');
    const applyReserve = document.getElementById('apply-reserve');
    const deltaPreview = document.getElementById('valuation-delta-preview');
    const previewGuide = document.getElementById('preview-guide');
    const previewReserve = document.getElementById('preview-reserve');
    const binWarning = document.getElementById('bin-conflict-warning');

    const currentGuide = 14250;
    const currentReserve = 14000;
    const binPrice = 15495;

    sourceType?.addEventListener('change', () => {
        providerWrap?.classList.toggle('hidden', sourceType.value !== 'External');
    });

    function updatePreview() {
        const amount = parseFloat(amountInput?.value) || 0;
        const showGuide = applyGuide?.checked;
        const showReserve = applyReserve?.checked;

        if ((showGuide || showReserve) && amount > 0) {
            deltaPreview?.classList.remove('hidden');
            if (previewGuide) previewGuide.textContent = showGuide ? '£' + amount.toLocaleString() : '—';
            if (previewReserve) previewReserve.textContent = showReserve ? '£' + amount.toLocaleString() : '—';
            // BIN conflict check
            if (showReserve && amount > binPrice) {
                binWarning?.classList.remove('hidden');
            } else {
                binWarning?.classList.add('hidden');
            }
        } else {
            deltaPreview?.classList.add('hidden');
        }
    }

    [amountInput, applyGuide, applyReserve].forEach(el => el?.addEventListener('input', updatePreview));
    [applyGuide, applyReserve].forEach(el => el?.addEventListener('change', updatePreview));

    document.getElementById('add-valuation-form')?.addEventListener('submit', (e) => {
        e.preventDefault();
        // TODO: POST to /listings/{id}/valuations
        // Events: valuation_added, valuation_applied (if apply checked)
        document.getElementById('add-valuation-modal')?.classList.add('hidden');
        document.getElementById('add-valuation-modal')?.classList.remove('flex');
    });
})();
</script>
