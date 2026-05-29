{{-- ───────────────────────────────────────────────────────────────────── --}}
{{-- DELETE LEAD MODAL                                                    --}}
{{-- ───────────────────────────────────────────────────────────────────── --}}

<div
    id="modal-delete"
    class="fixed inset-0 z-[10000] hidden">

    {{-- Backdrop --}}
   <div
    class="absolute inset-0 bg-black/40 backdrop-blur-sm"
    data-close-modal="modal-delete">
</div>

    {{-- Dialog --}}
    <div
        class="absolute inset-0 flex items-center justify-center p-4">

        <div
            class="card w-full max-w-md
                   border border-border
                   rounded-xl overflow-hidden">

            <div class="p-5">

                <div
                    class="w-12 h-12 rounded-full
                           bg-red-100 text-red-600
                           flex items-center justify-center
                           mx-auto mb-4">

                    <i
                        data-lucide="trash-2"
                        class="w-5 h-5">
                    </i>

                </div>

                <h3 class="text-center font-semibold text-lg">
                    Delete Lead
                </h3>

                <p
                    class="text-sm text-muted-foreground
                           text-center mt-2">

                    This action cannot be undone.
                </p>

                <p
                    class="text-sm text-muted-foreground
                           text-center">

                    Are you sure you want to delete this lead?
                </p>

            </div>

            <form
                id="form-delete-lead"
                method="POST">

                @csrf
                @method('DELETE')

                <div
                    class="px-5 py-4 border-t border-border
                           flex justify-end gap-2">

                    <button
                        type="button"
                        class="kt-btn kt-btn-outline"
                        data-close-modal="modal-delete">

                        Cancel

                    </button>

                    <button
                        type="submit"
                        class="kt-btn kt-btn-destructive">

                        Delete Lead

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>