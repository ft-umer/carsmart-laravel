{{--
    resources/views/auctions/partials/live-console-modal.blade.php
    A5 — Live Console (multi-lot real-time operations)
    Proxy bidding display, sniper countdown, extend/pause/end controls
--}}

<div id="live-console-modal"
     class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="fixed inset-0 bg-black/60 backdrop-blur-md" data-modal-backdrop></div>

    <div role="dialog"
         class="relative w-full max-w-[1100px] mx-auto card rounded-xl overflow-hidden border border-border
                shadow-2xl bg-background flex flex-col max-h-[94vh] opacity-0 scale-95 transition-all">

        {{-- Header --}}
        <div class="p-4 border-b border-border flex items-center justify-between shrink-0 bg-muted/10">
            <div class="flex items-center gap-3">
                <span class="flex h-2.5 w-2.5 rounded-full bg-green-500 animate-pulse"></span>
                <div>
                    <h2 id="console-title" class="text-base font-semibold">Live Console</h2>
                    <div id="console-sub" class="text-xs text-muted-foreground">Auction · Schedule</div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button id="console-btn-pause-all"  class="kt-btn kt-btn-outline kt-btn-sm">Pause all</button>
                <button id="console-btn-extend-all" class="kt-btn kt-btn-ghost kt-btn-sm">
                    Extend all + <input id="console-extend-all-min" type="number" value="5" min="1" max="60"
                                       class="w-9 text-center bg-transparent border-b border-border outline-none mx-1" /> min
                </button>
                <button data-modal-close class="kt-btn kt-btn-icon kt-btn-ghost">✕</button>
            </div>
        </div>

        {{-- Console body --}}
        <div class="flex flex-col md:flex-row flex-1 overflow-hidden">

            {{-- Left: Lots list ──────────────────────────────────── --}}
            <div class="w-full md:w-72 border-b md:border-b-0 md:border-r border-border overflow-y-auto shrink-0 bg-muted/10">
                <div class="p-3 border-b border-border text-xs font-semibold text-muted-foreground uppercase tracking-wide">
                    Lots
                </div>
                <div id="console-lots-list" class="divide-y divide-border">
                    {{-- Lot rows injected by JS --}}
                    <div class="p-3 text-xs text-muted-foreground">No lots</div>
                </div>
            </div>

            {{-- Right: Followed lot detail ──────────────────────── --}}
            <div class="flex-1 flex flex-col overflow-hidden">

                {{-- Followed lot header --}}
                <div id="console-lot-header"
                     class="p-4 border-b border-border bg-muted/5 shrink-0">
                    <div class="flex items-center justify-between flex-wrap gap-3">
                        <div>
                            <h3 id="console-lot-title" class="font-semibold">Select a lot to follow</h3>
                            <div id="console-lot-meta" class="text-xs text-muted-foreground mt-0.5">—</div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-muted-foreground">Countdown</div>
                            <div id="console-countdown"
                                 class="text-2xl font-bold font-mono tabular-nums text-foreground">
                                —
                            </div>
                        </div>
                    </div>

                    {{-- Bid stats --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-3">
                        <div class="bg-background border border-border rounded p-2 text-center">
                            <div class="text-xs text-muted-foreground">Current bid</div>
                            <div id="console-current-bid" class="font-semibold text-sm mt-0.5">—</div>
                        </div>
                        <div class="bg-background border border-border rounded p-2 text-center">
                            <div class="text-xs text-muted-foreground">Next minimum</div>
                            <div id="console-next-min" class="font-semibold text-sm mt-0.5">—</div>
                        </div>
                        <div class="bg-background border border-border rounded p-2 text-center">
                            <div class="text-xs text-muted-foreground">Reserve</div>
                            <div id="console-reserve-status" class="font-semibold text-sm mt-0.5">—</div>
                        </div>
                        <div class="bg-background border border-border rounded p-2 text-center">
                            <div class="text-xs text-muted-foreground">Bidders</div>
                            <div id="console-bidders" class="font-semibold text-sm mt-0.5">—</div>
                        </div>
                    </div>

                    {{-- Lot controls --}}
                    <div class="flex gap-2 mt-3 flex-wrap">
                        <button id="console-btn-extend" class="kt-btn kt-btn-outline kt-btn-sm">
                            Extend + <input id="console-extend-min" type="number" value="2" min="1" max="30"
                                           class="w-9 text-center bg-transparent border-b border-border outline-none mx-1" /> min
                        </button>
                        <button id="console-btn-pause-lot"  class="kt-btn kt-btn-outline kt-btn-sm">Pause lot</button>
                        <button id="console-btn-end-lot"    class="kt-btn kt-btn-destructive kt-btn-sm">End lot now</button>
                        <button id="console-btn-rerun-lot"  class="kt-btn kt-btn-ghost kt-btn-sm">Re-run</button>
                        <button id="console-btn-announce-lot" class="kt-btn kt-btn-ghost kt-btn-sm">Announce</button>
                    </div>

                    {{-- Sniper protection indicator --}}
                    <div id="sniper-indicator"
                         class="hidden mt-2 flex items-center gap-2 text-xs text-yellow-700 bg-yellow-50 border border-yellow-200 rounded px-2 py-1">
                        <span class="animate-pulse">⚡</span>
                        Sniper protection active — closing extended
                    </div>
                </div>

                {{-- Bid feed stream --}}
                <div class="flex-1 overflow-y-auto">
                    <div class="p-3 border-b border-border text-xs font-semibold text-muted-foreground flex items-center justify-between">
                        <span>Bid feed</span>
                        <label class="flex items-center gap-1 font-normal">
                            <input type="checkbox" id="console-proxy-filter" class="form-checkbox" />
                            Proxy only
                        </label>
                    </div>
                    <div id="console-bid-feed"
                         class="divide-y divide-border text-xs font-mono bg-background">
                        <div class="p-3 text-muted-foreground text-center">No bids yet</div>
                    </div>
                </div>

            </div>{{-- end right panel --}}

        </div>{{-- end body --}}

    </div>
</div>