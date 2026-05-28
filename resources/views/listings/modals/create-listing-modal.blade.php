<div
id="add-listing-wizard"
class="fixed inset-0 z-50 hidden items-center justify-center p-4">

    <div
    class="fixed inset-0 bg-black/50"></div>

    <div
    class="relative w-full max-w-3xl card rounded-lg border border-border bg-background">

        <div
        class="p-4 border-b border-border flex items-center justify-between">

            <h3 class="text-lg font-medium">
                Create New Listing
            </h3>

            <button
            class="close-modal kt-btn kt-btn-icon kt-btn-ghost">
                ✕
            </button>

        </div>

        <div class="p-4">

            @include('listings.partials.wizard.step1')

        </div>

    </div>

</div>