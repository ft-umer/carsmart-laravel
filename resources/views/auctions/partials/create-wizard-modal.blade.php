{{--
    resources/views/auctions/partials/create-wizard-modal.blade.php
    A2 — Create Auction Wizard
    Steps: 1 Basics → 2 Schedule → 3 Rules → 4 Participants → 5 Lots defaults → 6 Assets → 7 Summary
--}}

<div id="create-auction-modal"
     class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-md" data-modal-backdrop></div>

    <div role="dialog"
         class="relative w-full max-w-3xl mx-auto card rounded-xl overflow-hidden border border-border
                shadow-2xl bg-background flex flex-col max-h-[92vh] opacity-0 scale-95 transition-all">

        {{-- Header --}}
        <div class="p-4 border-b border-border flex items-center justify-between shrink-0">
            <div>
                <h3 class="text-lg font-semibold">Create Auction</h3>
                <p class="text-xs text-muted-foreground mt-0.5">Configure schedule, rules, participants and lots</p>
            </div>
            <button data-modal-close class="kt-btn kt-btn-icon kt-btn-ghost">✕</button>
        </div>

        {{-- Step progress --}}
        <div class="px-5 py-3 border-b border-border bg-muted/20 shrink-0">
            <div class="flex items-center justify-between gap-2 flex-wrap">
                <div id="wizard-step-pills" class="flex gap-1 flex-wrap">
                    @foreach(['Basics','Schedule','Rules','Participants','Lot defaults','Assets','Summary'] as $i => $label)
                    <button data-pill="{{ $i + 1 }}"
                            class="wizard-pill text-xs px-2.5 py-1 rounded border
                                   {{ $i === 0 ? 'border-primary bg-primary/10 text-primary font-medium' : 'border-border text-muted-foreground' }}">
                        {{ $i + 1 }}. {{ $label }}
                    </button>
                    @endforeach
                </div>
                <div class="flex items-center gap-2 text-xs text-muted-foreground shrink-0">
                    <span id="wizard-step-label">Step 1 of 7</span>
                    <div class="w-24 h-1.5 rounded-full bg-border overflow-hidden">
                        <div id="wizard-progress-bar" class="h-full bg-primary rounded-full transition-all"
                             style="width:14%"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Scrollable form --}}
        <div class="flex-1 overflow-hidden flex flex-col">
            <form id="create-auction-form" class="flex-1 overflow-y-auto" novalidate>

                {{-- ── Step 1: Basics ────────────────────────────────── --}}
                <div class="wizard-step p-5 space-y-4" data-step="1">
                    <h4 class="text-sm font-semibold">Basics</h4>
                    <div>
                        <label class="block text-xs font-medium mb-1">Auction name <span class="text-red-500">*</span></label>
                        <input name="name" class="kt-input" placeholder="e.g. October Prime Sale" required />
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1">Description</label>
                        <textarea name="description" rows="3" class="kt-input w-full"
                                  placeholder="Short description visible to participants"></textarea>
                    </div>
                    <div class="flex items-center gap-6">
                        <div>
                            <label class="block text-xs font-medium mb-2">Visibility</label>
                            <div class="flex items-center gap-4">
                                <label class="flex items-center gap-2 text-sm cursor-pointer">
                                    <input type="radio" name="visibility" value="Public" class="form-radio" checked />
                                    Public
                                </label>
                                <label class="flex items-center gap-2 text-sm cursor-pointer">
                                    <input type="radio" name="visibility" value="Private" class="form-radio" />
                                    Private (invite-only)
                                </label>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1">Cohort tag <span class="text-muted-foreground">(optional)</span></label>
                        <input name="cohort_tag" class="kt-input" placeholder="e.g. Prestige, Trade-only" />
                    </div>
                </div>

                {{-- ── Step 2: Schedule ──────────────────────────────── --}}
                <div class="wizard-step hidden p-5 space-y-4" data-step="2">
                    <h4 class="text-sm font-semibold">Schedule</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium mb-1">Start date & time <span class="text-red-500">*</span></label>
                            <input type="datetime-local" name="start" class="kt-input" required />
                        </div>
                        <div>
                            <label class="block text-xs font-medium mb-1">End date & time <span class="text-red-500">*</span></label>
                            <input type="datetime-local" name="end" class="kt-input" required />
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-2">Closing style</label>
                        <div class="flex items-center gap-6">
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="radio" name="closing_style" value="single" class="form-radio" checked />
                                Single end time
                            </label>
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="radio" name="closing_style" value="staggered" class="form-radio" />
                                Staggered lots
                            </label>
                        </div>
                        <div id="staggered-interval-wrap" class="hidden mt-3">
                            <label class="block text-xs font-medium mb-1">Interval between lots (seconds)</label>
                            <input type="number" name="stagger_interval" value="60" min="10"
                                   class="kt-input w-36" />
                        </div>
                    </div>
                    <div id="schedule-clash-warning"
                         class="hidden rounded border border-yellow-200 bg-yellow-50 p-2 text-xs text-yellow-800">
                        ⚠ Another auction overlaps this time window. Please check the Calendar.
                    </div>
                </div>

                {{-- ── Step 3: Rules ─────────────────────────────────── --}}
                <div class="wizard-step hidden p-5 space-y-5" data-step="3">
                    <h4 class="text-sm font-semibold">Rules</h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="border border-border rounded-lg p-3 space-y-3">
                            <div class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Bidding</div>
                            <label class="flex items-center justify-between text-sm cursor-pointer">
                                Proxy bidding
                                <input type="checkbox" name="proxy_bidding" class="form-checkbox" checked />
                            </label>
                            <label class="flex items-center justify-between text-sm cursor-pointer">
                                Sniper protection
                                <input type="checkbox" name="sniper_protection" id="sniper-toggle" class="form-checkbox" checked />
                            </label>
                            <div id="sniper-minutes-wrap" class="pl-4">
                                <label class="block text-xs font-medium mb-1">Extend by (minutes)</label>
                                <input type="number" name="sniper_minutes" value="2" min="1" max="30"
                                       class="kt-input w-24" />
                                <p class="text-xs text-muted-foreground mt-1">
                                    Each bid in the last N minutes extends closing by N minutes.
                                </p>
                            </div>
                        </div>

                        <div class="border border-border rounded-lg p-3 space-y-3">
                            <div class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Reserve & Pricing</div>
                            <label class="flex items-center justify-between text-sm cursor-pointer">
                                Auto-accept at ≥ Reserve
                                <input type="checkbox" name="auto_accept" class="form-checkbox" checked />
                            </label>
                            <div>
                                <label class="block text-xs font-medium mb-1">Start price</label>
                                <select name="start_price_mode" class="kt-input">
                                    <option value="zero">£0 (open)</option>
                                    <option value="guide">Guide price</option>
                                    <option value="custom">Custom £___</option>
                                </select>
                            </div>
                            <div id="start-price-custom-wrap" class="hidden">
                                <label class="block text-xs font-medium mb-1">Custom start price (£)</label>
                                <input type="number" name="start_price_custom" min="0" class="kt-input" />
                            </div>
                        </div>
                    </div>

                    <div class="border border-border rounded-lg p-3 space-y-2">
                        <div class="text-xs font-medium text-muted-foreground uppercase tracking-wide">BIN / Offer Precedence</div>
                        <label class="flex items-center justify-between text-sm cursor-pointer">
                            BIN ends lot until first valid bid
                            <input type="checkbox" name="bin_precedence" class="form-checkbox" checked />
                        </label>
                        <p class="text-xs text-muted-foreground">
                            Once a valid bid is placed, BIN is automatically disabled on that lot.
                        </p>
                    </div>

                    <div class="border border-border rounded-lg p-3 space-y-2">
                        <div class="text-xs font-medium text-muted-foreground uppercase tracking-wide">Bid Increment Schema</div>
                        <select name="increment_schema" class="kt-input">
                            <option value="standard">Standard (£250 → £500 → £1,000 bands)</option>
                            <option value="premium">Premium (£500 → £1,000 → £2,500 bands)</option>
                            <option value="custom">Custom (configure below)</option>
                        </select>
                        <div id="increment-schema-view"
                             class="text-xs text-muted-foreground mt-1 bg-muted/30 rounded p-2">
                            Band 0–£10k: £250 · £10k–£25k: £500 · £25k+: £1,000
                        </div>
                    </div>
                </div>

                {{-- ── Step 4: Participants ──────────────────────────── --}}
                <div class="wizard-step hidden p-5 space-y-4" data-step="4">
                    <h4 class="text-sm font-semibold">Participants</h4>

                    <div class="border border-border rounded-lg p-3 space-y-3">
                        <div class="text-xs font-medium text-muted-foreground uppercase tracking-wide">
                            Eligibility gates
                        </div>
                        <label class="flex items-center justify-between text-sm cursor-pointer">
                            Vendor KYC/KYB Verified required
                            <input type="checkbox" name="gate_kyc" class="form-checkbox" checked />
                        </label>
                        <label class="flex items-center justify-between text-sm cursor-pointer">
                            Card on file required
                            <input type="checkbox" name="gate_card" class="form-checkbox" checked />
                        </label>
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-2">Participant mode</label>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="radio" name="participant_mode" value="all" class="form-radio" checked />
                                All eligible vendors
                            </label>
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="radio" name="participant_mode" value="invite" class="form-radio" />
                                Invite specific vendors
                            </label>
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="radio" name="participant_mode" value="set" class="form-radio" />
                                Use saved participant set
                            </label>
                        </div>
                        <div id="participant-set-select" class="hidden mt-3">
                            <select name="participant_set" class="kt-input">
                                <option>Prestige Set A</option>
                                <option>Trade Network B</option>
                            </select>
                        </div>
                    </div>

                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                        <input type="checkbox" name="send_invites_now" class="form-checkbox" />
                        Send invites immediately on creation
                    </label>
                </div>

                {{-- ── Step 5: Lot defaults ──────────────────────────── --}}
                <div class="wizard-step hidden p-5 space-y-4" data-step="5">
                    <h4 class="text-sm font-semibold">Lot defaults</h4>
                    <p class="text-xs text-muted-foreground">
                        These values are inherited by each lot unless overridden at lot level.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium mb-1">Default start price</label>
                            <select name="lot_default_start_price" class="kt-input">
                                <option value="zero">£0 (same as auction rule)</option>
                                <option value="guide">Guide price</option>
                                <option value="custom">Custom</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium mb-1">Default increment schema</label>
                            <select name="lot_default_increment" class="kt-input">
                                <option>Inherit from auction</option>
                                <option>Standard</option>
                                <option>Premium</option>
                            </select>
                        </div>
                    </div>

                    <div class="border border-border rounded-lg p-3 space-y-2">
                        <label class="flex items-center justify-between text-sm cursor-pointer">
                            Allow vendor↔vendor exchange proposals pre-end
                            <input type="checkbox" name="allow_exchange" class="form-checkbox" checked />
                        </label>
                        <p class="text-xs text-muted-foreground">
                            Limited to 1 active proposal per listing at any time.
                        </p>
                    </div>
                </div>

                {{-- ── Step 6: Assets ────────────────────────────────── --}}
                <div class="wizard-step hidden p-5 space-y-4" data-step="6">
                    <h4 class="text-sm font-semibold">Assets <span class="text-muted-foreground text-xs font-normal">(public page)</span></h4>

                    <div>
                        <label class="block text-xs font-medium mb-1">Banner image</label>
                        <input type="file" name="banner_image" accept="image/*" class="kt-input" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1">Hero copy / headline</label>
                        <input name="hero_copy" class="kt-input"
                               placeholder="e.g. Exceptional vehicles, exceptional prices" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1">Terms snippet (visible to participants)</label>
                        <textarea name="terms_snippet" rows="4" class="kt-input w-full"
                                  placeholder="Summary of terms for this auction…"></textarea>
                    </div>
                </div>

                {{-- ── Step 7: Summary ───────────────────────────────── --}}
                <div class="wizard-step hidden p-5 space-y-4" data-step="7">
                    <h4 class="text-sm font-semibold">Summary &amp; Create</h4>
                    <p class="text-sm text-muted-foreground">
                        Review your configuration before creating the auction.
                    </p>

                    <div id="auction-wizard-summary"
                         class="bg-muted/30 rounded border border-border p-4 text-sm space-y-2 max-h-64 overflow-y-auto">
                        {{-- populated by JS --}}
                    </div>

                    <div id="wizard-validation-flags"
                         class="hidden rounded border border-red-200 bg-red-50 p-3 text-xs text-red-700 space-y-1">
                        {{-- validation issues injected by JS --}}
                    </div>
                </div>

            </form>
        </div>

        {{-- Footer --}}
        <div class="p-4 border-t border-border flex items-center justify-between bg-muted/10 shrink-0">
            <button id="wizard-back" type="button" class="kt-btn kt-btn-ghost" disabled>← Back</button>
            <div class="flex gap-2">
                <button id="wizard-save-draft" type="button" class="kt-btn kt-btn-outline">Save draft</button>
                <button id="wizard-next" type="button" class="kt-btn kt-btn-mono">Next →</button>
                <button id="wizard-create" type="button" class="kt-btn kt-btn-mono hidden">Create Auction</button>
            </div>
        </div>

    </div>
</div>