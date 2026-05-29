{{--
    resources/views/auctions/partials/detail-modal.blade.php
    A3 — Auction Detail (record view)
    Tabs: Overview | Lots | Participants | Rules & Increments | Bid feed | Messages | Assets | Activity | History
--}}

<div id="auction-detail-modal"
     class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-md" data-modal-backdrop></div>

    <div role="dialog"
         class="relative w-full max-w-[1000px] mx-auto card rounded-xl overflow-hidden border border-border
                shadow-2xl bg-background flex flex-col max-h-[92vh] opacity-0 scale-95 transition-all">

        {{-- ── Header ──────────────────────────────────────────── --}}
        <div class="p-4 border-b border-border flex items-start justify-between gap-3 shrink-0">
            <div>
                <h2 id="auc-detail-title" class="text-lg font-semibold">Auction</h2>
                <div id="auc-detail-sub" class="text-xs text-muted-foreground mt-1">
                    Status · Schedule · Owner
                </div>
                <div id="auc-detail-badges" class="flex gap-2 mt-2 flex-wrap"></div>
            </div>
            <div class="flex gap-2 items-start flex-wrap">
                <button id="auc-btn-publish"    class="kt-btn kt-btn-mono kt-btn-sm">Publish</button>
                <button id="auc-btn-start"      class="kt-btn kt-btn-outline kt-btn-sm">Start now</button>
                <button id="auc-btn-pause"      class="kt-btn kt-btn-outline kt-btn-sm">Pause</button>
                <button id="auc-btn-extend"     class="kt-btn kt-btn-outline kt-btn-sm">
                    Extend + <input id="auc-extend-min" type="number" value="5" min="1" max="60"
                                   class="w-10 text-center bg-transparent border-b border-border outline-none mx-1" /> min
                </button>
                <button id="auc-btn-add-lots"   class="kt-btn kt-btn-ghost kt-btn-sm">Add lots</button>
                <button id="auc-btn-invite"     class="kt-btn kt-btn-ghost kt-btn-sm">Invite vendors</button>
                <button id="auc-btn-announce"   class="kt-btn kt-btn-ghost kt-btn-sm">Announce</button>
                <div class="relative">
                    <button id="auc-more-toggle" class="kt-btn kt-btn-ghost kt-btn-sm">More ▾</button>
                    <div id="auc-more-menu"
                         class="hidden absolute right-0 mt-1 w-52 card p-2 rounded-lg shadow bg-background border border-border z-40 text-sm">
                        <button data-auc-action="live-console"  class="kt-btn kt-btn-ghost w-full text-left py-2">Live console</button>
                        <button data-auc-action="duplicate"     class="kt-btn kt-btn-ghost w-full text-left py-2">Duplicate</button>
                        <button data-auc-action="post-auction"  class="kt-btn kt-btn-ghost w-full text-left py-2">Post-auction actions</button>
                        <hr class="my-1 border-border">
                        <button data-auc-action="archive"       class="kt-btn kt-btn-destructive w-full text-left py-2">Archive</button>
                    </div>
                </div>
                <button data-modal-close class="kt-btn kt-btn-icon kt-btn-ghost">✕</button>
            </div>
        </div>

        {{-- ── KPI strip ────────────────────────────────────────── --}}
        <div id="auc-kpi-strip"
             class="grid grid-cols-2 md:grid-cols-5 gap-px bg-border border-b border-border shrink-0">
            @foreach([
                ['auc-kpi-lots',      'Total lots',     '—'],
                ['auc-kpi-live',      'Live lots',      '—'],
                ['auc-kpi-ended',     'Ended lots',     '—'],
                ['auc-kpi-reserve',   'Reserve met',    '—'],
                ['auc-kpi-bidders',   'Active bidders', '—'],
            ] as [$id, $label, $val])
            <div class="bg-background p-3 text-center">
                <div class="text-xs text-muted-foreground">{{ $label }}</div>
                <div id="{{ $id }}" class="text-lg font-semibold mt-0.5">{{ $val }}</div>
            </div>
            @endforeach
        </div>

        {{-- ── Tabs ─────────────────────────────────────────────── --}}
        <div class="border-b border-border px-4 overflow-x-auto shrink-0">
            <div class="flex gap-1 min-w-max">
                @foreach(['overview','lots','participants','rules','bid-feed','messages','assets','activity','history'] as $tab)
                <button data-tab="{{ $tab }}"
                        class="auc-tab-btn px-3 py-2 text-sm border-b-2 whitespace-nowrap
                               {{ $tab === 'overview' ? 'border-primary font-medium text-foreground' : 'border-transparent text-muted-foreground hover:text-foreground' }}">
                    {{ ucfirst(str_replace('-', ' ', $tab)) }}
                </button>
                @endforeach
            </div>
        </div>

        {{-- ── Tab panels ──────────────────────────────────────── --}}
        <div class="p-4 overflow-auto flex-1">

            {{-- Overview --}}
            <div data-panel="overview" class="auc-panel space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2 space-y-3">
                        <div class="card border border-border rounded p-3">
                            <div class="text-xs text-muted-foreground mb-1">Readiness checklist</div>
                            <div id="overview-checklist" class="space-y-1.5 text-sm">—</div>
                        </div>
                        <div class="card border border-border rounded p-3">
                            <div class="text-xs text-muted-foreground mb-1">Alerts</div>
                            <div id="overview-alerts" class="space-y-1 text-sm">—</div>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="card border border-border rounded p-3">
                            <div class="text-xs text-muted-foreground mb-1">Schedule</div>
                            <div id="overview-schedule" class="text-sm font-medium">—</div>
                        </div>
                        <div class="card border border-border rounded p-3">
                            <div class="text-xs text-muted-foreground mb-1">Rules summary</div>
                            <div id="overview-rules" class="text-xs space-y-1">—</div>
                        </div>
                        <div class="card border border-border rounded p-3">
                            <div class="text-xs text-muted-foreground mb-1">Participants</div>
                            <div id="overview-participants" class="text-sm">—</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Lots (A4 inline) --}}
            <div data-panel="lots" class="auc-panel hidden">
                <div class="flex items-center justify-between mb-3 flex-wrap gap-2">
                    <h3 class="text-sm font-medium">Lots Manager</h3>
                    <div class="flex gap-2 flex-wrap">
                        <button id="btn-add-from-listings" class="kt-btn kt-btn-mono kt-btn-sm">
                            + Add from Listings
                        </button>
                        <button id="btn-reorder-lots" class="kt-btn kt-btn-outline kt-btn-sm">Reorder</button>
                        <div class="relative">
                            <button id="lots-bulk-toggle" class="kt-btn kt-btn-ghost kt-btn-sm">Bulk ▾</button>
                            <div id="lots-bulk-menu"
                                 class="hidden absolute right-0 mt-1 w-44 card p-2 rounded shadow bg-background border border-border z-40 text-xs">
                                <button data-lots-bulk="withdraw"   class="kt-btn kt-btn-ghost w-full text-left py-1.5">Withdraw selected</button>
                                <button data-lots-bulk="re-run"     class="kt-btn kt-btn-ghost w-full text-left py-1.5">Re-run selected</button>
                                <button data-lots-bulk="preview"    class="kt-btn kt-btn-ghost w-full text-left py-1.5">Preview selected</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="overflow-auto border border-border rounded">
                    <table class="w-full text-sm">
                        <thead class="bg-muted/40">
                            <tr>
                                <th class="p-2 w-10"></th>
                                <th class="p-2 text-left">Lot #</th>
                                <th class="p-2 text-left">Listing</th>
                                <th class="p-2 text-left">Vehicle</th>
                                <th class="p-2 text-right">Start price</th>
                                <th class="p-2 text-left">Reserve?</th>
                                <th class="p-2 text-left">BIN/Offer</th>
                                <th class="p-2 text-left">State</th>
                                <th class="p-2 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="lots-tbody" class="bg-background divide-y divide-border">
                            <tr>
                                <td colspan="9" class="p-4 text-center text-xs text-muted-foreground">
                                    No lots added yet. Use "Add from Listings" to add lots.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Participants (A7 inline) --}}
            <div data-panel="participants" class="auc-panel hidden">
                <div class="flex items-center justify-between mb-3 flex-wrap gap-2">
                    <h3 class="text-sm font-medium">Participants</h3>
                    <div class="flex gap-2">
                        <select id="participants-set-load" class="kt-input kt-input-sm text-xs">
                            <option value="">Load saved set…</option>
                            <option>Prestige Set A</option>
                            <option>Trade Network B</option>
                        </select>
                        <button id="btn-save-participant-set" class="kt-btn kt-btn-outline kt-btn-sm">Save as set</button>
                        <button id="btn-invite-vendor" class="kt-btn kt-btn-mono kt-btn-sm">Invite vendor</button>
                    </div>
                </div>
                <div class="overflow-auto border border-border rounded">
                    <table class="w-full text-sm">
                        <thead class="bg-muted/40">
                            <tr>
                                <th class="p-2 text-left">Vendor</th>
                                <th class="p-2 text-left">KYB</th>
                                <th class="p-2 text-left">Card on file</th>
                                <th class="p-2 text-left">Status</th>
                                <th class="p-2 text-left">Last seen</th>
                                <th class="p-2 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="participants-tbody" class="bg-background divide-y divide-border">
                            <tr>
                                <td colspan="6" class="p-4 text-center text-xs text-muted-foreground">
                                    No participants invited yet.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Rules & Increments --}}
            <div data-panel="rules" class="auc-panel hidden">
                <div id="panel-rules-content" class="space-y-4 text-sm">
                    {{-- populated by JS --}}
                </div>
            </div>

            {{-- Bid feed (A5/A6 inline) --}}
            <div data-panel="bid-feed" class="auc-panel hidden">
                <div class="flex items-center justify-between mb-3 flex-wrap gap-2">
                    <h3 class="text-sm font-medium">Live Bid Feed</h3>
                    <div class="flex gap-2 flex-wrap">
                        <select id="bid-feed-lot-filter" class="kt-input text-xs">
                            <option value="">All lots</option>
                        </select>
                        <label class="flex items-center gap-1 text-xs">
                            <input type="checkbox" id="bid-feed-proxy-only" class="form-checkbox" />
                            Proxy only
                        </label>
                        <button id="btn-open-live-console" class="kt-btn kt-btn-mono kt-btn-sm">
                            Open Live Console
                        </button>
                    </div>
                </div>
                <div id="bid-feed-stream"
                     class="border border-border rounded overflow-auto max-h-80 bg-background divide-y divide-border text-xs font-mono">
                    <div class="p-3 text-muted-foreground text-center">
                        Bid feed is live during auction. No bids yet.
                    </div>
                </div>
            </div>

            {{-- Messages --}}
            <div data-panel="messages" class="auc-panel hidden">
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium mb-1">Announcement to all participants</label>
                        <textarea id="announcement-text" rows="4" class="kt-input w-full"
                                  placeholder="Type announcement…"></textarea>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button id="btn-send-announcement" class="kt-btn kt-btn-mono kt-btn-sm">
                            Send announcement
                        </button>
                    </div>
                    <div id="messages-log" class="border border-border rounded divide-y divide-border max-h-64 overflow-auto text-sm">
                        <div class="p-3 text-xs text-muted-foreground text-center">No messages sent yet</div>
                    </div>
                </div>
            </div>

            {{-- Assets --}}
            <div data-panel="assets" class="auc-panel hidden">
                <div id="panel-assets-content" class="space-y-3 text-sm max-w-lg">
                    {{-- populated by JS --}}
                </div>
            </div>

            {{-- Activity --}}
            <div data-panel="activity" class="auc-panel hidden">
                <div id="panel-activity" class="space-y-2 text-sm">—</div>
            </div>

            {{-- History --}}
            <div data-panel="history" class="auc-panel hidden">
                <div id="panel-history" class="space-y-1 text-xs font-mono">—</div>
            </div>

        </div>{{-- end tab content --}}

        {{-- Footer --}}
        <div class="p-3 border-t border-border flex justify-end gap-2 bg-muted/20 shrink-0">
            <button data-modal-close class="kt-btn kt-btn-ghost">Close</button>
        </div>

    </div>
</div>