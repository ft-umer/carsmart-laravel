<div id="add-listing-wizard"
    class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/50 backdrop-blur-sm">


    <div class="relative w-full max-w-3xl card rounded-lg border border-border bg-background">

        <div class="p-4 border-b border-border flex items-center justify-between">

            <h3 class="text-lg font-medium">
                Create New Listing
            </h3>

            <button class="close-modal kt-btn kt-btn-icon kt-btn-ghost">
                ✕
            </button>

        </div>
        <div class="p-4">

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

        <div class="flex justify-between p-4 border-t border-border">

            <button id="wizard-back" class="kt-btn kt-btn-outline">
                Back
            </button>

            <button id="wizard-next" class="kt-btn kt-btn-primary">
                Next
            </button>

        </div>

    </div>

</div>
