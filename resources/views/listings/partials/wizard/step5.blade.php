{{-- Step 5: Pricing --}}
<div class="space-y-6">

    <div class="flex items-center justify-between gap-3 flex-wrap">
        <div>
            <h3 class="text-lg font-semibold">Pricing</h3>
            <p class="text-sm text-gray-500 mt-1">Set guide, reserve and BIN/Offer configuration.</p>
        </div>
        <button type="button" class="kt-btn kt-btn-outline">
            <i class="ki-filled ki-chart-line-up"></i> Get Valuations
        </button>
    </div>

    {{-- Latest valuation note --}}
    <div class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-700">
        Latest valuation: <strong>£14,200</strong> (Carsmart · 2 hours ago)
        &nbsp;<a href="#" class="underline text-blue-600">View Valuations →</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">

        <div>
            <label class="block text-xs font-medium mb-1">Valuation (£)</label>
            <input type="number" class="kt-input w-full" placeholder="14200">
        </div>

        <div>
            <label class="block text-xs font-medium mb-1">Reserve (£)</label>
            <input type="number" class="kt-input w-full" placeholder="14000" id="reserve-input">
        </div>

        <div class="flex items-center gap-3 pt-6">
            <label class="kt-switch">
                <input type="checkbox" id="bin-toggle">
                <span></span>
            </label>
            <label for="bin-toggle" class="text-sm font-medium">Buy It Now</label>
        </div>

        <div>
            <label class="block text-xs font-medium mb-1">BIN Price (£)</label>
            <input type="number" class="kt-input w-full" placeholder="15495" id="bin-price-input" disabled>
        </div>

    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        <div class="flex items-center gap-3 pt-6">
            <label class="kt-switch"><input type="checkbox" checked><span></span></label>
            <label class="text-sm font-medium">Enable Make Offer</label>
        </div>

        <div>
            <label class="block text-xs font-medium mb-1">Auto Accept ≥</label>
            <div class="relative">
                <input type="text" value="98" class="kt-input w-full pe-10">
                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-muted-foreground">%</span>
            </div>
        </div>

        <div>
            <label class="block text-xs font-medium mb-1">Auto Decline &lt;</label>
            <div class="relative">
                <input type="text" value="90" class="kt-input w-full pe-10">
                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-muted-foreground">%</span>
            </div>
        </div>

    </div>

    <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700 space-y-1">
        <div>• Either Reserve or Buy-It-Now price is required.</div>
        <div>• If BIN is enabled, Reserve must remain blank.</div>
        <div>• Make Offer works independently and can run alongside BIN.</div>
    </div>

</div>

<script>
(function () {
    const binToggle = document.getElementById('bin-toggle');
    const binPrice  = document.getElementById('bin-price-input');
    const reserve   = document.getElementById('reserve-input');

    binToggle?.addEventListener('change', () => {
        if (binToggle.checked) {
            binPrice.disabled = false;
            reserve.value = '';
            reserve.disabled = true;
        } else {
            binPrice.disabled = true;
            reserve.disabled = false;
        }
    });
})();
</script>
