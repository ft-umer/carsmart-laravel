{{-- ───────────────────────────────────────────────────────────────────── --}}
{{-- BULK MOVE STAGE MODAL                                                --}}
{{-- ───────────────────────────────────────────────────────────────────── --}}

<div
    id="modal-bulk-stage"
    class="fixed inset-0 z-[10000] hidden">

    {{-- Backdrop --}}
   <div
    class="absolute inset-0 bg-black/40 backdrop-blur-sm"
    data-close-modal="modal-bulk-stage">
</div>

    {{-- Dialog --}}
    <div
        class="absolute inset-0 flex items-center justify-center p-4">

        <div
            class="card w-full max-w-md
                   border border-border
                   rounded-xl overflow-hidden">

            {{-- Header --}}
            <div
                class="px-5 py-4 border-b border-border
                       flex items-center justify-between">

                <div>

                    <h3 class="font-semibold">
                        Move Stage
                    </h3>

                    <p class="text-xs text-muted-foreground mt-0.5">
                        Move selected leads to another stage
                    </p>

                </div>

                <button
                    type="button"
                    class="kt-btn kt-btn-ghost kt-btn-sm"
                    data-close-modal="modal-bulk-stage">

                    ✕

                </button>

            </div>

            {{-- Body --}}
            <div class="p-5">

                <label class="form-label">
                    Stage
                </label>

                <select
                    id="bulk-stage-select"
                    class="kt-select w-full">

                    <option value="New">
                        New
                    </option>

                    <option value="Qualified">
                        Qualified
                    </option>

                    <option value="Pricing sent">
                        Pricing sent
                    </option>

                    <option value="Awaiting seller docs">
                        Awaiting seller docs
                    </option>

                    <option value="Ready">
                        Ready
                    </option>

                </select>

            </div>

            {{-- Footer --}}
            <div
                class="px-5 py-4 border-t border-border
                       flex justify-end gap-2">

                <button
                    type="button"
                    class="kt-btn kt-btn-outline"
                    data-close-modal="modal-bulk-stage">

                    Cancel

                </button>

                <button
                    type="button"
                    id="btn-confirm-bulk-stage"
                    class="kt-btn kt-btn-primary">

                    Move Stage

                </button>

            </div>

        </div>

    </div>

</div>