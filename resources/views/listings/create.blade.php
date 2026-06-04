@extends('layouts.app')

@section('title', 'Create Listing')

@section('content')

<section class="kt-container-fixed grow px-4 lg:px-6 py-6">

    {{-- Page header --}}
    <div class="flex items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-semibold">Create Listing</h1>
            <div class="text-sm text-muted-foreground mt-0.5">
                Complete all steps to submit the listing for Quality Assurance.
            </div>
        </div>
        <a href="{{ route('listings.index') }}" class="kt-btn kt-btn-outline">
            <i class="ki-filled ki-arrow-left"></i> Back to Listings
        </a>
    </div>

    {{-- Wizard card --}}
    <div class="card border border-border rounded-2xl overflow-hidden">

        {{-- Stepper --}}
        <div class="border-b border-border px-6 py-4 overflow-x-auto">
            <div class="flex items-center gap-0 min-w-max">

                @php
                    $steps = [
                        ['num' => 1, 'label' => 'Vehicle'],
                        ['num' => 2, 'label' => 'Seller'],
                        ['num' => 3, 'label' => 'Media'],
                        ['num' => 4, 'label' => 'Documents'],
                        ['num' => 5, 'label' => 'Pricing'],
                        ['num' => 6, 'label' => 'Compliance'],
                        ['num' => 7, 'label' => 'Summary'],
                    ];
                @endphp

                @foreach($steps as $step)
                    <div class="flex items-center">

                        {{-- Step bubble + label --}}
                        <div class="flex flex-col items-center gap-1 min-w-[64px]">
                            <div
                                id="stepper-bubble-{{ $step['num'] }}"
                                class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-semibold transition-colors
                                    {{ $step['num'] === 1 ? 'bg-primary text-primary-foreground' : 'bg-muted text-muted-foreground' }}">
                                {{ $step['num'] }}
                            </div>
                            <span
                                id="stepper-label-{{ $step['num'] }}"
                                class="text-xs font-medium transition-colors
                                    {{ $step['num'] === 1 ? 'text-foreground' : 'text-muted-foreground' }}">
                                {{ $step['label'] }}
                            </span>
                        </div>

                        {{-- Connector line (not after last) --}}
                        @if (!$loop->last)
                            <div
                                id="stepper-line-{{ $step['num'] }}"
                                class="h-px w-8 mx-1 transition-colors
                                    {{ $step['num'] < 1 ? 'bg-primary' : 'bg-border' }}">
                            </div>
                        @endif

                    </div>
                @endforeach

            </div>
        </div>

        {{-- Wizard form --}}
        <form id="create-listing-form" action="{{ route('listings.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="p-6">

                {{-- Step 1: Vehicle --}}
                <div id="wizard-step-1">
                    @include('listings.partials.wizard.step1')
                </div>

                {{-- Step 2: Seller --}}
                <div id="wizard-step-2" class="hidden">
                    @include('listings.partials.wizard.step2')
                </div>

                {{-- Step 3: Media --}}
                <div id="wizard-step-3" class="hidden">
                    @include('listings.partials.wizard.step3')
                </div>

                {{-- Step 4: Documents --}}
                <div id="wizard-step-4" class="hidden">
                    @include('listings.partials.wizard.step4')
                </div>

                {{-- Step 5: Pricing --}}
                <div id="wizard-step-5" class="hidden">
                    @include('listings.partials.wizard.step5')
                </div>

                {{-- Step 6: Compliance --}}
                <div id="wizard-step-6" class="hidden">
                    @include('listings.partials.wizard.step6')
                </div>

                {{-- Step 7: Summary --}}
                <div id="wizard-step-7" class="hidden">
                    @include('listings.partials.wizard.step7')
                </div>

            </div>

            {{-- Footer nav --}}
            <div class="border-t border-border px-6 py-4 flex items-center justify-between gap-3">
                <button
                    type="button"
                    id="wizard-back"
                    class="kt-btn kt-btn-outline"
                    style="display:none">
                    <i class="ki-filled ki-arrow-left"></i> Back
                </button>

                <div class="flex items-center gap-2 ml-auto">
                    <button type="button" id="wizard-save-draft" class="kt-btn kt-btn-ghost">
                        Save Draft
                    </button>
                    <button type="button" id="wizard-next" class="kt-btn kt-btn-primary">
                        Next <i class="ki-filled ki-arrow-right"></i>
                    </button>
                </div>
            </div>

        </form>

    </div>

</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    const totalSteps = 7;
    let currentStep = 1;

    const showStep = (step) => {
        for (let i = 1; i <= totalSteps; i++) {
            document.getElementById(`wizard-step-${i}`)?.classList.add('hidden');

            // Stepper bubble
            const bubble = document.getElementById(`stepper-bubble-${i}`);
            const label  = document.getElementById(`stepper-label-${i}`);
            const line   = document.getElementById(`stepper-line-${i}`);

            if (bubble) {
                bubble.classList.toggle('bg-primary', i <= step);
                bubble.classList.toggle('text-primary-foreground', i <= step);
                bubble.classList.toggle('bg-muted', i > step);
                bubble.classList.toggle('text-muted-foreground', i > step);

                if (i < step) {
                    bubble.innerHTML = '<i class="ki-filled ki-check text-xs"></i>';
                } else {
                    bubble.textContent = i;
                }
            }

            if (label) {
                label.classList.toggle('text-foreground', i <= step);
                label.classList.toggle('text-muted-foreground', i > step);
            }

            if (line) {
                line.classList.toggle('bg-primary', i < step);
                line.classList.toggle('bg-border', i >= step);
            }
        }

        document.getElementById(`wizard-step-${step}`)?.classList.remove('hidden');

        const backBtn = document.getElementById('wizard-back');
        const nextBtn = document.getElementById('wizard-next');

        if (backBtn) backBtn.style.display = step === 1 ? 'none' : 'inline-flex';

        if (nextBtn) {
            if (step === totalSteps) {
                nextBtn.innerHTML = '<i class="ki-filled ki-send"></i> Submit for QA';
                nextBtn.type = 'submit';
                nextBtn.name = 'submit_for_qa';
                nextBtn.value = '1';
            } else {
                nextBtn.innerHTML = 'Next <i class="ki-filled ki-arrow-right"></i>';
                nextBtn.type = 'button';
                nextBtn.removeAttribute('name');
            }
        }
    };

    showStep(currentStep);

    document.getElementById('wizard-next')?.addEventListener('click', () => {
        if (currentStep < totalSteps) {
            currentStep++;
            showStep(currentStep);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });

    document.getElementById('wizard-back')?.addEventListener('click', () => {
        if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });

    document.getElementById('wizard-save-draft')?.addEventListener('click', () => {
        const form = document.getElementById('create-listing-form');
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'save_draft';
        input.value = '1';
        form.appendChild(input);
        form.submit();
    });

});
</script>
@endpush

@endsection