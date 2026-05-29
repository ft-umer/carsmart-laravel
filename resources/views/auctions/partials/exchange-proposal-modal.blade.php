{{--
    resources/views/auctions/partials/exchange-proposal-modal.blade.php
    A9 — Vendor↔Vendor Exchange Proposals
    Limit: 1 active proposal per listing
--}}

<div id="exchange-proposal-modal"
     class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-md" data-modal-backdrop></div>

    <div role="dialog"
         class="relative w-full max-w-lg mx-auto card rounded-xl overflow-hidden border border-border
                shadow-2xl bg-background opacity-0 scale-95 transition-all">

        <div class="p-4 border-b border-border flex items-center justify-between">
            <div>
                <h3 class="text-base font-semibold">Exchange Proposal</h3>
                <p class="text-xs text-muted-foreground mt-0.5">
                    Vendor↔Vendor swap before auction end — 1 active proposal per listing
                </p>
            </div>
            <button data-modal-close class="kt-btn kt-btn-icon kt-btn-ghost">✕</button>
        </div>

        <div class="p-5 space-y-4">

            {{-- Incoming proposal card --}}
            <div id="ep-incoming-card"
                 class="hidden rounded border border-border bg-muted/20 p-4 space-y-3 text-sm">
                <div class="text-xs font-semibold text-muted-foreground uppercase tracking-wide">
                    Active proposal
                </div>
                <div>
                    <span class="text-muted-foreground">Offered by:</span>
                    <strong id="ep-offered-by" class="ml-1">—</strong>
                </div>
                <div>
                    <span class="text-muted-foreground">Offered vehicle:</span>
                    <strong id="ep-offered-vehicle" class="ml-1">—</strong>
                </div>
                <div>
                    <span class="text-muted-foreground">Cash difference:</span>
                    <strong id="ep-cash-diff" class="ml-1">—</strong>
                </div>
                <div>
                    <span class="text-muted-foreground">Expires:</span>
                    <strong id="ep-expiry" class="ml-1">—</strong>
                </div>
                <div>
                    <span class="text-muted-foreground">Notes:</span>
                    <span id="ep-notes" class="ml-1">—</span>
                </div>
                <div class="flex gap-2 pt-1">
                    <button id="ep-btn-accept"  class="kt-btn kt-btn-mono">Accept</button>
                    <button id="ep-btn-decline" class="kt-btn kt-btn-outline">Decline</button>
                    <button id="ep-btn-counter" class="kt-btn kt-btn-ghost">Counter</button>
                </div>
            </div>

            {{-- No active proposal --}}
            <div id="ep-no-proposal" class="text-sm text-muted-foreground text-center py-4">
                No active exchange proposal for this lot.
            </div>

            {{-- Create proposal form --}}
            <div class="border-t border-border pt-4 space-y-3">
                <div class="text-xs font-semibold text-muted-foreground uppercase tracking-wide">
                    Create proposal
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1">Offered vehicle(s)</label>
                    <input name="ep_vehicle" class="kt-input"
                           placeholder="LST-XXXX or vehicle description" />
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium mb-1">Cash difference (£)</label>
                        <input name="ep_cash_diff" type="number" step="0.01" class="kt-input"
                               placeholder="0 if even swap" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1">Expiry date</label>
                        <input name="ep_expiry" type="datetime-local" class="kt-input" />
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1">Notes</label>
                    <textarea name="ep_notes" rows="2" class="kt-input w-full"></textarea>
                </div>
                <div class="flex justify-end">
                    <button id="ep-btn-submit" class="kt-btn kt-btn-mono">Submit proposal</button>
                </div>
            </div>
        </div>

    </div>
</div>