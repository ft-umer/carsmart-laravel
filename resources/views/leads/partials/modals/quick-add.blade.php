{{-- ───────────────────────────────────────────────────────────────────── --}}
{{-- QUICK ADD LEAD MODAL                                                 --}}
{{-- ───────────────────────────────────────────────────────────────────── --}}

<div
    id="modal-quick-add"
    class="fixed inset-0 z-[10000] hidden">

    {{-- Backdrop --}}
  <div
    class="absolute inset-0 bg-black/80 backdrop-blur-sm"
    data-close-modal="modal-quick-add">
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
                        Add Lead
                    </h3>

                    <p class="text-xs text-muted-foreground mt-0.5">
                        Create a new CRM lead
                    </p>

                </div>

                <button
                    type="button"
                    class="kt-btn kt-btn-ghost kt-btn-sm"
                    data-close-modal="modal-quick-add">

                    ✕

                </button>

            </div>

            {{-- Form --}}
            <form
                action="{{ route('leads.store') }}"
                method="POST">

                @csrf

                <div class="p-5 space-y-5">

                    {{-- Name --}}
                    <div>

                        <label class="form-label">
                            Lead Name *
                        </label>

                        <input
                            type="text"
                            name="name"
                            required
                            class="kt-input w-full">

                    </div>

                    {{-- Contact --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div>

                            <label class="form-label">
                                Email
                            </label>

                            <input
                                type="email"
                                name="email"
                                class="kt-input w-full">

                        </div>

                        <div>

                            <label class="form-label">
                                Phone
                            </label>

                            <input
                                type="text"
                                name="phone"
                                class="kt-input w-full">

                        </div>

                    </div>

                    {{-- Source + Owner --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div>

                            <label class="form-label">
                                Source
                            </label>

                            <select
                                name="source"
                                class="kt-select w-full">

                                <option value="">
                                    Select source
                                </option>

                                <option>Website</option>
                                <option>Phone</option>
                                <option>Referral</option>
                                <option>Facebook</option>
                                <option>Instagram</option>
                                <option>AutoTrader</option>

                            </select>

                        </div>

                        <div>

                            <label class="form-label">
                                Owner
                            </label>

                            <input
                                type="text"
                                name="owner"
                                class="kt-input w-full">

                        </div>

                    </div>

                    {{-- Vehicle --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div>

                            <label class="form-label">
                                VRM
                            </label>

                            <input
                                type="text"
                                name="vrm"
                                class="kt-input w-full uppercase">

                        </div>

                        <div>

                            <label class="form-label">
                                VIN
                            </label>

                            <input
                                type="text"
                                name="vin"
                                class="kt-input w-full">

                        </div>

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

                </div>

                {{-- Footer --}}
                <div
                    class="px-5 py-4 border-t border-border
                           flex justify-end gap-2">

                    <button
                        type="button"
                        class="kt-btn kt-btn-outline"
                        data-close-modal="modal-quick-add">

                        Cancel

                    </button>

                    <button
                        type="submit"
                        class="kt-btn kt-btn-primary">

                        Create Lead

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>