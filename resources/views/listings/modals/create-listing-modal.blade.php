{{--
    L1 — Create Listing Wizard (7 steps)
    Vehicle → Seller → Media → Documents → Pricing → Compliance → Summary
--}}
<div id="add-listing-wizard"
    class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/50 backdrop-blur-sm">

    <div class="relative w-full max-w-7xl card rounded-xl border border-border bg-background shadow-xl overflow-hidden flex flex-col"
        style="max-height: calc(100vh - 32px);">

        {{-- Header --}}
        <div class="px-6 py-5 border-b border-border flex items-center justify-between shrink-0">
            <div>
                <h3 class="text-xl font-semibold">Create New Listing</h3>
                <p class="text-sm text-muted-foreground mt-1">Complete all steps before submitting for QA.</p>
            </div>
            <button class="close-listing-wizard kt-btn kt-btn-icon kt-btn-ghost">
                <i class="ki-filled ki-cross text-lg"></i>
            </button>
        </div>

        {{-- Stepper nav --}}
        <div class="px-6 py-4 border-b border-border shrink-0 overflow-x-auto">
            <div class="flex items-center gap-2 min-w-max">
                @php
                $steps = ['Vehicle','Seller','Media','Documents','Pricing','Compliance','Summary'];
                @endphp
                @foreach($steps as $i => $label)
                    <div class="wizard-step @if($i === 0) active @endif" data-step-nav="{{ $i + 1 }}">
                        <div class="wizard-step-number">{{ $i + 1 }}</div>
                        <div class="wizard-step-label">{{ $label }}</div>
                    </div>
                    @if(!$loop->last)
                        <div class="wizard-connector h-px w-6 bg-border"></div>
                    @endif
                @endforeach
            </div>
        </div>

        {{-- Body --}}
        <div class="flex-1 overflow-y-auto p-6">
            <div id="wizard-step-1">@include('listings.partials.wizard.step1')</div>
            <div id="wizard-step-2" class="hidden">@include('listings.partials.wizard.step2')</div>
            <div id="wizard-step-3" class="hidden">@include('listings.partials.wizard.step3')</div>
            <div id="wizard-step-4" class="hidden">@include('listings.partials.wizard.step4')</div>
            <div id="wizard-step-5" class="hidden">@include('listings.partials.wizard.step5')</div>
            <div id="wizard-step-6" class="hidden">@include('listings.partials.wizard.step6')</div>
            <div id="wizard-step-7" class="hidden">@include('listings.partials.wizard.step7')</div>
        </div>

        {{-- Footer --}}
        <div class="px-6 py-4 border-t border-border flex items-center justify-between bg-background shrink-0">
            <button id="wizard-back" class="kt-btn kt-btn-outline" style="display:none;">Back</button>
            <div class="flex items-center gap-3 ml-auto">
                <button type="button" class="kt-btn kt-btn-outline">Save Draft</button>
                <button id="wizard-next" class="kt-btn kt-btn-primary">Next</button>
            </div>
        </div>

    </div>

</div>

<style>
.wizard-step {
    display: flex; align-items: center; gap: 10px;
    padding: 8px 14px; border-radius: 12px;
    border: 1px solid hsl(var(--border));
    background: hsl(var(--background));
    transition: all .2s ease; cursor: default;
}
.wizard-step-number {
    width: 26px; height: 26px; border-radius: 999px;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 700;
    background: hsl(var(--muted)); color: hsl(var(--muted-foreground));
}
.wizard-step-label { font-size: 13px; font-weight: 600; color: hsl(var(--foreground)); }
.wizard-step.active { border-color: hsl(var(--primary)); background: hsl(var(--primary) / 0.10); }
.wizard-step.active .wizard-step-number { background: hsl(var(--primary)); color: white; }
.wizard-step.active .wizard-step-label { color: hsl(var(--primary)); }
.wizard-step.done .wizard-step-number { background: hsl(var(--success, 142 76% 36%)); color: white; }
.wizard-connector { flex-shrink: 0; }
</style>

<script>
(function () {
    const TOTAL = 7;
    let current = 1;

    const modal   = document.getElementById('add-listing-wizard');
    const nextBtn = document.getElementById('wizard-next');
    const backBtn = document.getElementById('wizard-back');

    function showStep(step) {
        for (let i = 1; i <= TOTAL; i++) {
            document.getElementById(`wizard-step-${i}`)?.classList.toggle('hidden', i !== step);
        }
        // stepper nav
        document.querySelectorAll('.wizard-step').forEach((el, idx) => {
            const n = idx + 1;
            el.classList.toggle('active', n === step);
            el.classList.toggle('done', n < step);
        });
        backBtn.style.display = step === 1 ? 'none' : '';
        nextBtn.textContent = step === TOTAL ? 'Create Listing' : 'Next';
    }

    nextBtn?.addEventListener('click', () => {
        if (current < TOTAL) { current++; showStep(current); }
        else { /* submit */ closeWizard(); }
    });
    backBtn?.addEventListener('click', () => {
        if (current > 1) { current--; showStep(current); }
    });

    function closeWizard() {
        modal?.classList.add('hidden');
        modal?.classList.remove('flex');
        current = 1;
        showStep(1);
    }

    document.querySelector('.close-listing-wizard')?.addEventListener('click', closeWizard);
    modal?.addEventListener('click', (e) => { if (e.target === modal) closeWizard(); });

    // Open wizard
    document.getElementById('btn-create-listing')?.addEventListener('click', () => {
        modal?.classList.remove('hidden');
        modal?.classList.add('flex');
        showStep(current);
    });
    
     document.getElementById('btn-create-listings')?.addEventListener('click', () => {
        modal?.classList.remove('hidden');
        modal?.classList.add('flex');
        showStep(current);
    });

    showStep(current);
})();
</script>
