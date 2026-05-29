
<div id="add-listing-wizard"
    class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/50 backdrop-blur-sm">

    {{-- Modal --}}
    <div class="relative w-full max-w-7xl card rounded-xl border border-border bg-background shadow-xl overflow-hidden">

        {{-- Header --}}
        <div class="px-6 py-5 border-b border-border flex items-center justify-between">

            <div>
                <h3 class="text-xl font-semibold text-foreground">
                    Create New Listing
                </h3>

                <p class="text-sm text-muted-foreground mt-1">
                    Complete the listing workflow before submitting for QA.
                </p>
            </div>

            <button class="close-modal kt-btn kt-btn-icon kt-btn-ghost">
                <i class="ki-filled ki-cross text-lg"></i>
            </button>

        </div>

      

        {{-- Body --}}
        <div class="p-6 overflow-y-auto"
            style="max-height: calc(100vh - 240px);">

            <div id="wizard-step-1">
                @include('listings.partials.wizard.step1')
            </div>

            <div id="wizard-step-2" class="hidden">
                @include('listings.partials.wizard.step2')
            </div>

            <div id="wizard-step-3" class="hidden">
                @include('listings.partials.wizard.step3')
            </div>

            <div id="wizard-step-4" class="hidden">
                @include('listings.partials.wizard.step4')
            </div>

            <div id="wizard-step-5" class="hidden">
                @include('listings.partials.wizard.step5')
            </div>

        </div>

        {{-- Footer --}}
        <div class="px-6 py-4 border-t border-border flex items-center justify-between bg-background">

            <button id="wizard-back"
                class="kt-btn kt-btn-outline">
                Back
            </button>

            <div class="flex items-center gap-3">

                <button type="button"
                    class="kt-btn kt-btn-outline">
                    Save Draft
                </button>

                <button id="wizard-next"
                    class="kt-btn kt-btn-primary">
                    Next
                </button>

            </div>

        </div>

    </div>

</div>

<style>
    .wizard-step {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 16px;
        border-radius: 12px;
        border: 1px solid hsl(var(--border));
        background: hsl(var(--background));
        transition: all .2s ease;
    }

    .wizard-step-number {
        width: 28px;
        height: 28px;
        border-radius: 999px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
        background: hsl(var(--muted));
        color: hsl(var(--muted-foreground));
    }

    .wizard-step-label {
        font-size: 13px;
        font-weight: 600;
        color: hsl(var(--foreground));
    }

    .wizard-step.active {
        border-color: hsl(var(--primary));
        background: hsl(var(--primary) / 0.10);
    }

    .wizard-step.active .wizard-step-number {
        background: hsl(var(--primary));
        color: white;
    }

    .wizard-step.active .wizard-step-label {
        color: hsl(var(--primary));
    }
</style>
```
