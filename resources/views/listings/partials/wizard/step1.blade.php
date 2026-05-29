<div class="space-y-6 max">

    {{-- Header --}}
    <div class="flex items-center justify-between gap-3 flex-wrap">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">
                Vehicle Information
            </h3>
            <p class="text-sm text-gray-500 mt-1">
                Enter vehicle details and run VRM lookup to prefill data.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" class="kt-btn kt-btn-outline">
                Save Draft
            </button>

            <button
                type="button"
                id="run-vrm-btn"
                class="kt-btn kt-btn-primary"
                disabled>
                Run VRM
            </button>
        </div>
    </div>

    {{-- Vehicle Details --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">

        <div>
            <label class="block text-xs font-medium mb-1">
                VRM <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                id="vrm-input"
                class="kt-input w-full"
                placeholder="AB12 CDE">
        </div>

        <div>
            <label class="block text-xs font-medium mb-1">
                VIN
            </label>
            <input type="text" class="kt-input w-full">
        </div>

        <div>
            <label class="block text-xs font-medium mb-1">
                Make
            </label>
            <input type="text" class="kt-input w-full">
        </div>

        <div>
            <label class="block text-xs font-medium mb-1">
                Model
            </label>
            <input type="text" class="kt-input w-full">
        </div>

        <div>
            <label class="block text-xs font-medium mb-1">
                Derivative
            </label>
            <input type="text" class="kt-input w-full">
        </div>

        <div>
            <label class="block text-xs font-medium mb-1">
                Year
            </label>
            <input type="number" class="kt-input w-full">
        </div>

        <div>
            <label class="block text-xs font-medium mb-1">
                Mileage
            </label>
            <input type="number" class="kt-input w-full">
        </div>

        <div>
            <label class="block text-xs font-medium mb-1">
                Colour
            </label>
            <input type="text" class="kt-input w-full">
        </div>

        <div>
            <label class="block text-xs font-medium mb-1">
                Body Type
            </label>
            <select class="kt-input w-full">
                <option>Select</option>
                <option>Saloon</option>
                <option>SUV</option>
                <option>Hatchback</option>
                <option>Coupe</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-medium mb-1">
                Fuel Type
            </label>
            <select class="kt-input w-full">
                <option>Select</option>
                <option>Petrol</option>
                <option>Diesel</option>
                <option>Hybrid</option>
                <option>Electric</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-medium mb-1">
                Transmission
            </label>
            <select class="kt-input w-full">
                <option>Select</option>
                <option>Automatic</option>
                <option>Manual</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-medium mb-1">
                ULEZ
            </label>
            <select class="kt-input w-full">
                <option>Yes</option>
                <option>No</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-medium mb-1">
                Doors
            </label>
            <input type="number" class="kt-input w-full">
        </div>

        <div>
            <label class="block text-xs font-medium mb-1">
                Seats
            </label>
            <input type="number" class="kt-input w-full">
        </div>

        <div>
            <label class="block text-xs font-medium mb-1">
                MOT Expiry
            </label>
            <input type="date" class="kt-input w-full">
        </div>

        <div>
            <label class="block text-xs font-medium mb-1">
                Number of Keys
            </label>
            <input type="number" class="kt-input w-full">
        </div>

    </div>

    {{-- Service / Options --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        <div>
            <label class="block text-xs font-medium mb-1">
                Service History
            </label>

            <select class="kt-input w-full">
                <option>Select</option>
                <option>Full</option>
                <option>Partial</option>
                <option>None</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-medium mb-1">
                Options / Features
            </label>

            <select class="kt-input w-full" multiple>
                <option>Panoramic Roof</option>
                <option>Heated Seats</option>
                <option>Apple CarPlay</option>
                <option>360 Camera</option>
            </select>
        </div>

    </div>

    {{-- Notes --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        <div>
            <label class="block text-xs font-medium mb-1">
                Condition Notes
            </label>

            <textarea rows="5" class="kt-input w-full"></textarea>
        </div>

        <div>
            <label class="block text-xs font-medium mb-1">
                Known Faults
            </label>

            <textarea rows="5" class="kt-input w-full"></textarea>
        </div>

    </div>

  
{{-- Valuation / Pricing --}}
<div class="rounded-2xl border border-border bg-muted/30 p-5">

    {{-- Header --}}
    <div class="flex items-center justify-between gap-3 flex-wrap mb-5">

        <div>
            <h4 class="font-semibold text-sm text-foreground">
                Pricing & Valuation
            </h4>

            <p class="text-xs text-muted-foreground mt-1">
                Latest valuation:
                <span class="font-medium text-foreground">
                    £18,450
                </span>
                (CAP HPI · 2 hours ago)
            </p>
        </div>

        <button type="button" class="kt-btn kt-btn-outline">
            <i class="ki-filled ki-chart-line-up"></i>
            Get Valuations
        </button>

    </div>

    {{-- Pricing Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">

        {{-- Valuation --}}
        <div>
            <label class="block text-xs font-medium text-foreground mb-1">
                Valuation (£)
            </label>

            <input
                type="number"
                class="kt-input w-full"
                placeholder="18450">
        </div>

        {{-- Reserve --}}
        <div>
            <label class="block text-xs font-medium text-foreground mb-1">
                Reserve (£)
            </label>

            <input
                type="number"
                class="kt-input w-full"
                placeholder="17000">
        </div>

        {{-- BIN Toggle --}}
        <div class="flex items-center gap-3 pt-6">

            <label class="kt-switch">
                <input type="checkbox" id="bin-toggle">
                <span></span>
            </label>

            <label
                for="bin-toggle"
                class="text-sm font-medium text-foreground">
                Buy It Now
            </label>

        </div>

        {{-- BIN Price --}}
        <div>
            <label class="block text-xs font-medium text-foreground mb-1">
                BIN Price (£)
            </label>

            <input
                type="number"
                class="kt-input w-full"
                placeholder="18995">
        </div>

    </div>

    {{-- Offer Settings --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">

        {{-- Toggle --}}
        <div class="flex items-center gap-3 pt-6">

            <label class="kt-switch">
                <input type="checkbox" checked>
                <span></span>
            </label>

            <label class="text-sm font-medium text-foreground">
                Enable Make Offer
            </label>

        </div>

        {{-- Auto Accept --}}
        <div>
            <label class="block text-xs font-medium text-foreground mb-1">
                Auto Accept ≥
            </label>

            <div class="relative">

                <input
                    type="text"
                    value="98"
                    class="kt-input w-full pe-10">

                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-muted-foreground">
                    %
                </span>

            </div>
        </div>

        {{-- Auto Decline --}}
        <div>
            <label class="block text-xs font-medium text-foreground mb-1">
                Auto Decline <
            </label>

            <div class="relative">

                <input
                    type="text"
                    value="90"
                    class="kt-input w-full pe-10">

                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-muted-foreground">
                    %
                </span>

            </div>
        </div>

    </div>

    {{-- Validation Note --}}
    <div class="mt-6 rounded-xl border border-warning/20 bg-warning/10 px-4 py-4">

        <div class="flex items-start gap-3">

            <i class="ki-filled ki-information-4 text-warning text-lg mt-0.5"></i>

            <div class="space-y-1 text-sm text-muted-foreground">

                <div>
                    Either Reserve or Buy-It-Now price is required.
                </div>

                <div>
                    If BIN is enabled, Reserve Price must remain blank.
                </div>

                <div>
                    Make Offer works independently and can run alongside BIN.
                </div>

            </div>

        </div>

    </div>

</div>


</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const vrmInput = document.getElementById('vrm-input');
        const runBtn = document.getElementById('run-vrm-btn');

        if (vrmInput && runBtn) {
            vrmInput.addEventListener('input', function () {
                runBtn.disabled = this.value.trim().length < 2;
            });
        }
    });
</script>