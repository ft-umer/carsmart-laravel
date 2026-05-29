{{--
    resources/views/auctions/partials/lot-detail-modal.blade.php
    A6 — Lot Detail & Bid Feed
    Tabs: Overview | Bid feed | Participants | Rules | Notes | Activity | History
--}}

<div id="lot-detail-modal"
     class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-md" data-modal-backdrop></div>

    <div role="dialog"
         class="relative w-full max-w-3xl mx-auto card rounded-xl overflow-hidden border border-border
                shadow-2xl bg-background flex flex-col max-h-[90vh] opacity-0 scale-95 transition-all">

        {{-- Header --}}
        <div class="p-4 border-b border-border flex items-start justify-between shrink-0">
            <div>
                <h2 id="lot-detail-title" class="text-base font-semibold">Lot</h2>
                <div id="lot-detail-sub" class="text-xs text-muted-foreground mt-1">Lot # · Vehicle · State</div>
            </div>
            <div class="flex gap-2 items-center flex-wrap">
                <button id="lot-btn-extend"   class="kt-btn kt-btn-outline kt-btn-sm">
                    Extend + <input id="lot-extend-min" type="number" value="2" min="1"
                                   class="w-9 text-center bg-transparent border-b border-border outline-none mx-1" /> min
                </button>
                <button id="lot-btn-withdraw" class="kt-btn kt-btn-ghost kt-btn-sm">Withdraw</button>
                <button id="lot-btn-rerun"    class="kt-btn kt-btn-outline kt-btn-sm">Re-run</button>
                <button id="lot-btn-announce" class="kt-btn kt-btn-ghost kt-btn-sm">Announce</button>
                <button data-modal-close class="kt-btn kt-btn-icon kt-btn-ghost">✕</button>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="border-b border-border px-4 overflow-x-auto shrink-0">
            <div class="flex gap-1 min-w-max">
                @foreach(['overview','bid-feed','participants','rules','notes','activity','history'] as $tab)
                <button data-tab="{{ $tab }}"
                        class="lot-tab-btn px-3 py-2 text-sm border-b-2 whitespace-nowrap
                               {{ $tab === 'overview' ? 'border-primary font-medium text-foreground' : 'border-transparent text-muted-foreground hover:text-foreground' }}">
                    {{ ucfirst(str_replace('-', ' ', $tab)) }}
                </button>
                @endforeach
            </div>
        </div>

        {{-- Tab panels --}}
        <div class="p-4 overflow-auto flex-1">

            {{-- Overview --}}
            <div data-panel="overview" class="lot-panel space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div id="lot-vehicle-summary" class="card border border-border rounded p-3 text-sm space-y-1">—</div>
                    <div id="lot-pricing-summary"  class="card border border-border rounded p-3 text-sm space-y-1">—</div>
                </div>
                <div id="lot-photos-strip" class="flex gap-2 overflow-x-auto">
                    {{-- photo thumbnails injected by JS --}}
                </div>
                <div id="lot-reserve-status-card"
                     class="card border border-border rounded p-3 text-sm">—</div>
            </div>

            {{-- Bid feed --}}
            <div data-panel="bid-feed" class="lot-panel hidden">
                <div class="flex items-center justify-between mb-3 flex-wrap gap-2">
                    <h3 class="text-sm font-medium">Bid feed</h3>
                    <div class="flex items-center gap-3 text-xs">
                        <label class="flex items-center gap-1 cursor-pointer">
                            <input type="checkbox" id="lot-feed-my-vendor" class="form-checkbox" /> My vendor only
                        </label>
                        <label class="flex items-center gap-1 cursor-pointer">
                            <input type="checkbox" id="lot-feed-proxy-only" class="form-checkbox" /> Proxy only
                        </label>
                        <label class="flex items-center gap-1 cursor-pointer">
                            <input type="checkbox" id="lot-feed-extensions" class="form-checkbox" /> Extensions
                        </label>
                    </div>
                </div>
                <div id="lot-bid-feed"
                     class="border border-border rounded overflow-auto max-h-72 bg-background divide-y divide-border text-xs font-mono">
                    <div class="p-3 text-muted-foreground text-center">No bids yet</div>
                </div>
            </div>

            {{-- Participants --}}
            <div data-panel="participants" class="lot-panel hidden">
                <div id="lot-participants-list" class="space-y-2 text-sm">—</div>
            </div>

            {{-- Rules --}}
            <div data-panel="rules" class="lot-panel hidden">
                <div id="lot-rules-content" class="space-y-3 text-sm">
                    {{-- Shows lot-level overrides vs auction defaults --}}
                </div>
            </div>

            {{-- Notes --}}
            <div data-panel="notes" class="lot-panel hidden">
                <textarea id="lot-notes-area" rows="6" class="kt-input w-full"
                          placeholder="Internal notes for this lot…"></textarea>
                <div class="mt-2 flex justify-end">
                    <button id="lot-btn-save-notes" class="kt-btn kt-btn-mono kt-btn-sm">Save notes</button>
                </div>
            </div>

            {{-- Activity --}}
            <div data-panel="activity" class="lot-panel hidden">
                <div id="lot-activity-feed" class="space-y-2 text-sm">—</div>
            </div>

            {{-- History --}}
            <div data-panel="history" class="lot-panel hidden">
                <div id="lot-history-log" class="space-y-1 text-xs font-mono">—</div>
            </div>

        </div>

        <div class="p-3 border-t border-border flex justify-end bg-muted/20 shrink-0">
            <button data-modal-close class="kt-btn kt-btn-ghost">Close</button>
        </div>

    </div>
</div>