{{-- ───────────────────────────────────────────────────────────────────── --}}
{{-- ADD VALUATION MODAL                                                  --}}
{{-- ───────────────────────────────────────────────────────────────────── --}}

<div
    id="modal-add-valuation"
    class="fixed inset-0 z-[10000] hidden">

  <div
    class="absolute inset-0 bg-black/40 backdrop-blur-sm"
    data-close-modal="modal-add-valuation">
</div>
    {{-- Dialog --}}
    <div
        class="absolute inset-0 flex items-center justify-center p-4">

        <div
            class="card w-full max-w-2xl
                   border border-border
                   rounded-xl overflow-hidden">

            {{-- Header --}}
            <div
                class="px-5 py-4 border-b border-border
                       flex items-center justify-between">

                <div>

                    <h3 class="font-semibold text-base">
                        Add Valuation
                    </h3>

                    <p class="text-xs text-muted-foreground mt-0.5">
                        Record a manual vehicle valuation
                    </p>

                </div>

                <button
                    type="button"
                    class="kt-btn kt-btn-ghost kt-btn-sm"
                    data-close-modal="modal-add-valuation">

                    ✕

                </button>

            </div>

            <form id="form-add-valuation">

                <input
                    type="hidden"
                    id="val-lead-id"
                    name="lead_id">

                <div class="p-5 space-y-5">

                    {{-- Source + Valuer --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div>

                            <label class="form-label">
                                Source *
                            </label>

                            <select
                                name="source"
                                required
                                class="kt-select w-full">

                                <option value="">
                                    Select source
                                </option>

                                <option value="CAP">
                                    CAP
                                </option>

                                <option value="Glass's">
                                    Glass's
                                </option>

                                <option value="Manual">
                                    Manual
                                </option>

                                <option value="Trade">
                                    Trade
                                </option>

                                <option value="Dealer">
                                    Dealer
                                </option>

                            </select>

                        </div>

                        <div>

                            <label class="form-label">
                                Valuer
                            </label>

                            <input
                                type="text"
                                name="valuer"
                                class="kt-input w-full">

                        </div>

                    </div>

                    {{-- Amount --}}
                    <div>

                        <label class="form-label">
                            Valuation Amount (£) *
                        </label>

                        <input
                            type="number"
                            name="amount"
                            min="0"
                            step="1"
                            required
                            class="kt-input w-full">

                    </div>

                    {{-- Notes --}}
                    <div>

                        <label class="form-label">
                            Notes
                        </label>

                        <textarea
                            name="notes"
                            rows="4"
                            class="kt-textarea w-full"></textarea>

                    </div>

                    {{-- Comparable vehicles --}}
                    <div>

                        <label class="form-label">
                            Comparable Vehicles
                        </label>

                        <input
                            type="text"
                            name="comps"
                            placeholder="AB12CDE, XY22ZZZ, AA11AAA"
                            class="kt-input w-full">

                        <p class="text-xs text-muted-foreground mt-1">
                            Separate registrations with commas.
                        </p>

                    </div>

                </div>

                {{-- Footer --}}
                <div
                    class="px-5 py-4 border-t border-border
                           flex justify-end gap-2">

                    <button
                        type="button"
                        class="kt-btn kt-btn-outline"
                        data-close-modal="modal-add-valuation">

                        Cancel

                    </button>

                    <button
                        type="submit"
                        class="kt-btn kt-btn-primary">

                        Save Valuation

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>