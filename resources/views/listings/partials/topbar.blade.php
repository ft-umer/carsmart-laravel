<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-4">

    <!-- LEFT SIDE -->
    <div class="flex flex-col gap-2">

        <!-- VIEW SWITCH -->
        <div class="flex items-center gap-2">

            <button
                data-view="listings"
                class="kt-btn kt-btn-mono active-view">
                Listings
            </button>

            <button
                data-view="valuations"
                class="kt-btn kt-btn-ghost">
                Valuations
            </button>

        </div>

        <!-- TITLE -->
        <div>
            <h1 class="text-lg font-semibold text-foreground">
                Listings
            </h1>

            <div class="text-xs text-muted-foreground">
                Browse · Pull valuations · Apply to pricing
            </div>
        </div>

    </div>

    <!-- RIGHT SIDE ACTIONS -->
    <div class="flex flex-wrap items-center gap-2">

        <button
            id="btn-create-listing"
            class="kt-btn kt-btn-mono">
            + Create listing
        </button>

        <button class="kt-btn kt-btn-outline">
            Bulk actions
        </button>

        <button class="kt-btn kt-btn-ghost">
            Refresh
        </button>

    </div>

</div>