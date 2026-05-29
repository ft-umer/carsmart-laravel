{{--
    resources/views/auctions/partials/publish-confirm-modal.blade.php
    Publish confirmation + A10 Notifications & Automations modal
--}}

{{-- ── Publish confirm ─────────────────────────────────────────── --}}
<div id="publish-confirm-modal"
     class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-md" data-modal-backdrop></div>

    <div role="dialog"
         class="relative w-full max-w-md mx-auto card rounded-xl overflow-hidden border border-border
                shadow-2xl bg-background p-6 opacity-0 scale-95 transition-all">

        <h3 class="text-base font-semibold">Publish auction?</h3>
        <p class="text-sm text-muted-foreground mt-2">
            Once published, the auction will be visible to participants and scheduled notifications may be sent.
        </p>
        <div id="publish-checklist" class="mt-3 space-y-1.5 text-sm">
            {{-- readiness checklist populated by JS --}}
        </div>
        <div class="mt-5 flex justify-end gap-3">
            <button data-modal-close class="kt-btn kt-btn-ghost">Cancel</button>
            <button id="confirm-publish-btn" class="kt-btn kt-btn-mono">Publish</button>
        </div>

    </div>
</div>

{{-- ── Notifications & Automations (A10) ─────────────────────── --}}
<div id="notifications-modal"
     class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-md" data-modal-backdrop></div>

    <div role="dialog"
         class="relative w-full max-w-2xl mx-auto card rounded-xl overflow-hidden border border-border
                shadow-2xl bg-background flex flex-col max-h-[88vh] opacity-0 scale-95 transition-all">

        <div class="p-4 border-b border-border flex items-center justify-between shrink-0">
            <div>
                <h3 class="text-base font-semibold">Notifications &amp; Automations</h3>
                <p class="text-xs text-muted-foreground mt-0.5">
                    A10 — Configure triggers, channels, and quiet-hour rules
                </p>
            </div>
            <button data-modal-close class="kt-btn kt-btn-icon kt-btn-ghost">✕</button>
        </div>

        <div class="p-4 overflow-auto flex-1 space-y-5">

            {{-- Triggers --}}
            <div>
                <div class="text-xs font-semibold text-muted-foreground uppercase tracking-wide mb-3">
                    Triggers
                </div>
                <div class="space-y-2 text-sm">
                    @foreach([
                        ['auction_published',         'Auction published',             true],
                        ['auction_starts_t24',        'Auction starts in T-24h',        true],
                        ['lot_closing_t10',           'Lot closing in T-10 minutes',    true],
                        ['outbid',                    'Outbid notification',            true],
                        ['reserve_met',               'Reserve met',                    true],
                        ['auction_ended',             'Auction ended',                  true],
                        ['rerun_scheduled',           'Re-run scheduled',               false],
                    ] as [$key, $label, $default])
                    <label class="flex items-center justify-between cursor-pointer border border-border rounded p-2">
                        <span>{{ $label }}</span>
                        <input type="checkbox" name="trigger_{{ $key }}" class="form-checkbox"
                               {{ $default ? 'checked' : '' }} />
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Channels --}}
            <div>
                <div class="text-xs font-semibold text-muted-foreground uppercase tracking-wide mb-3">
                    Channels
                </div>
                <div class="flex gap-4">
                    @foreach(['Email','SMS','WhatsApp'] as $ch)
                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                        <input type="checkbox" name="channel_{{ strtolower($ch) }}" class="form-checkbox" checked />
                        {{ $ch }}
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Quiet hours --}}
            <div>
                <div class="text-xs font-semibold text-muted-foreground uppercase tracking-wide mb-3">
                    Quiet hours
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                    <div>
                        <label class="block text-xs font-medium mb-1">From</label>
                        <input type="time" name="quiet_from" value="22:00" class="kt-input" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1">To</label>
                        <input type="time" name="quiet_to" value="08:00" class="kt-input" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1">Max per day</label>
                        <input type="number" name="max_per_day" value="5" min="1" class="kt-input" />
                    </div>
                </div>
                <p class="text-xs text-muted-foreground mt-2">
                    Per-auction overrides are allowed. Cap applies globally unless overridden.
                </p>
            </div>

            {{-- Automation log --}}
            <div>
                <div class="text-xs font-semibold text-muted-foreground uppercase tracking-wide mb-2">
                    Recent automation events
                </div>
                <div id="automation-log"
                     class="border border-border rounded bg-background divide-y divide-border max-h-48 overflow-auto text-xs font-mono">
                    <div class="p-2 text-muted-foreground text-center">No automation events yet</div>
                </div>
            </div>

        </div>

        <div class="p-3 border-t border-border flex justify-end gap-2 shrink-0">
            <button data-modal-close class="kt-btn kt-btn-ghost">Cancel</button>
            <button id="btn-save-notifications" class="kt-btn kt-btn-mono">Save settings</button>
        </div>
    </div>
</div>