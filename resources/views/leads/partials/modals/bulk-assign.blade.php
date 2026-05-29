{{-- ───────────────────────────────────────────────────────────────────── --}}
{{-- BULK ASSIGN OWNER MODAL                                              --}}
{{-- ───────────────────────────────────────────────────────────────────── --}}

<div
    id="modal-bulk-assign"
    class="fixed inset-0 z-[10000] hidden">

    {{-- Backdrop --}}
       <div
    class="absolute inset-0 bg-black/40 backdrop-blur-sm"
    data-close-modal="modal-bulk-assign">
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
                        Assign Owner
                    </h3>

                    <p class="text-xs text-muted-foreground mt-0.5">
                        Assign selected leads to a team member
                    </p>

                </div>

                <button
                    type="button"
                    class="kt-btn kt-btn-ghost kt-btn-sm"
                    data-close-modal="modal-bulk-assign">

                    ✕

                </button>

            </div>

            {{-- Body --}}
            <div class="p-5">

                <label class="form-label">
                    Owner
                </label>

                <select
                    id="bulk-assign-owner-select"
                    class="kt-select w-full">

                    <option value="">
                        Select owner
                    </option>

                    <option value="AH">
                        AH
                    </option>

                    <option value="JM">
                        JM
                    </option>

                    <option value="RB">
                        RB
                    </option>

                    <option value="MS">
                        MS
                    </option>

                    <option value="Unassigned">
                        Unassigned
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
                    data-close-modal="modal-bulk-assign">

                    Cancel

                </button>

                <button
                    type="button"
                    id="btn-confirm-bulk-assign"
                    class="kt-btn kt-btn-primary">

                    Assign

                </button>

            </div>

        </div>

    </div>

</div>