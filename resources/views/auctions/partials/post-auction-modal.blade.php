{{--
    resources/views/auctions/partials/post-auction-modal.blade.php
    A8 — Re-runs & Post-auction actions
    Outcomes: Deal pending | Re-run | Offer highest bidder | Switch to BIN/Offer
--}}

<div id="post-auction-modal"
     class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-md" data-modal-backdrop></div>

    <div role="dialog"
         class="relative w-full max-w-3xl mx-auto card rounded-xl overflow-hidden border border-border
                shadow-2xl bg-background flex flex-col max-h-[88vh] opacity-0 scale-95 transition-all">

        <div class="p-4 border-b border-border flex items-center justify-between shrink-0">
            <div>
                <h3 class="text-base font-semibold">Post-auction actions</h3>
                <p class="text-xs text-muted-foreground mt-0.5">
                    Resolve unsold or disputed lots. Winning lots create Deal Pending records.
                </p>
            </div>
            <button data-modal-close class="kt-btn kt-btn-icon kt-btn-ghost">✕</button>
        </div>

        <div class="p-4 overflow-auto flex-1 space-y-4">

            {{-- Retention / audit notice --}}
            <div class="flex items-center gap-2 text-xs text-muted-foreground bg-muted/30 rounded p-2 border border-border">
                <i class="ki-outline ki-information text-base"></i>
                Data retention banner: all post-auction actions are fully audited.
                <label class="ml-auto flex items-center gap-1 cursor-pointer">
                    <input type="checkbox" class="form-checkbox" /> Include archived
                </label>
            </div>

            {{-- Ended lots table --}}
            <div class="overflow-auto border border-border rounded">
                <table class="w-full text-sm">
                    <thead class="bg-muted/40">
                        <tr>
                            <th class="p-2 text-left">Lot</th>
                            <th class="p-2 text-left">Vehicle</th>
                            <th class="p-2 text-right">Highest bid</th>
                            <th class="p-2 text-left">Reserve met</th>
                            <th class="p-2 text-left">Outcome</th>
                            <th class="p-2 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="post-auction-tbody" class="bg-background divide-y divide-border">
                        {{-- Injected by JS --}}
                        <tr>
                            <td colspan="6" class="p-4 text-center text-xs text-muted-foreground">
                                No ended lots yet
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Deal pending info --}}
            <div class="rounded border border-border bg-muted/20 p-3 text-xs text-muted-foreground space-y-1">
                <div class="font-medium text-foreground text-sm">Workflow notes</div>
                <div>• <strong>Reserve met</strong> → auto-accept; creates Deal Pending with 7-day seller objection window.</div>
                <div>• <strong>Unsold</strong> → Re-run (clone), Offer highest bidder (with expiry), or Switch to BIN/Offer on listing.</div>
                <div>• <strong>Objection window</strong> begins on auction close. Deal proceeds to Handover once acknowledged.</div>
            </div>

        </div>

        <div class="p-3 border-t border-border flex justify-end shrink-0">
            <button data-modal-close class="kt-btn kt-btn-ghost">Close</button>
        </div>
    </div>
</div>