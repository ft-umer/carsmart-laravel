{{-- ───────────────────────────────────────────────────────────────────── --}}
{{-- APPLY VALUATION TO PRICING MODAL                                     --}}
{{-- ───────────────────────────────────────────────────────────────────── --}}

<div
    id="modal-apply-pricing"
    class="fixed inset-0 z-[10000] hidden">

    {{-- Backdrop --}}
    <div
    class="absolute inset-0 bg-black/40 backdrop-blur-sm"
    data-close-modal="modal-apply-pricing">
</div>

    {{-- Dialog --}}
    <div
        class="absolute inset-0 flex items-center justify-center p-4">

        <div
            class="card w-full max-w-lg
                   border border-border
                   rounded-xl overflow-hidden">

            {{-- Header --}}
            <div
                class="px-5 py-4 border-b border-border
                       flex items-center justify-between">

                <div>

                    <h3 class="font-semibold text-base">
                        Apply Valuation
                    </h3>

                    <p class="text-xs text-muted-foreground mt-0.5">
                        Update listing guide price using this valuation
                    </p>

                </div>

                <button
                    type="button"
                    class="kt-btn kt-btn-ghost kt-btn-sm"
                    data-close-modal="modal-apply-pricing">

                    ✕

                </button>

            </div>

            {{-- Body --}}
            <div class="p-5 space-y-4">

                <div
                    class="rounded-xl border border-border
                           bg-muted/20 p-4">

                    <div class="text-xs text-muted-foreground mb-1">
                        Selected valuation
                    </div>

                    <div
                        id="apply-val-amount"
                        class="text-2xl font-bold">

                        £0

                    </div>

                </div>

                <div
                    id="apply-val-delta"
                    class="text-sm text-muted-foreground">

                    —

                </div>

                <div
                    id="apply-listing-info"
                    class="text-sm text-muted-foreground">

                    —

                </div>

                <div
                    class="rounded-lg border border-amber-200
                           bg-amber-50 dark:bg-amber-900/20
                           px-3 py-2 text-xs text-amber-800
                           dark:text-amber-300">

                    This action will update the listing pricing guide.
                    Ensure the valuation has been reviewed before applying.

                </div>

            </div>

            {{-- Footer --}}
            <div
                class="px-5 py-4 border-t border-border
                       flex justify-end gap-2">

                <button
                    type="button"
                    class="kt-btn kt-btn-outline"
                    data-close-modal="modal-apply-pricing">

                    Cancel

                </button>

                <button
                    type="button"
                    id="btn-confirm-apply-pricing"
                    class="kt-btn kt-btn-primary">

                    Apply to Pricing

                </button>

            </div>

        </div>

    </div>

</div>