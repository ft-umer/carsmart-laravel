<div class="space-y-6">

    <div class="flex items-center justify-between gap-3 flex-wrap">

        <div>
            <h3 class="text-lg font-semibold text-gray-900">
                Summary & Submission
            </h3>

            <p class="text-sm text-gray-500 mt-1">
                Review all information before submitting to Quality Assurance.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <span class="kt-badge kt-badge-warning">
                Draft
            </span>
        </div>

    </div>

    {{-- Validation Summary --}}
    <div class="border border-gray-200 rounded-2xl p-5 bg-gray-50">

        <h4 class="text-sm font-semibold text-gray-900 mb-4">
            Validation Checklist
        </h4>

        <div class="space-y-3 text-sm">

            <div class="flex items-center justify-between border-b border-gray-200 pb-2">
                <span>Vehicle details completed</span>
                <span class="text-green-600 font-medium">Complete</span>
            </div>

            <div class="flex items-center justify-between border-b border-gray-200 pb-2">
                <span>Required media uploaded</span>
                <span class="text-red-600 font-medium">Missing 2 photos</span>
            </div>

            <div class="flex items-center justify-between border-b border-gray-200 pb-2">
                <span>KYC / KYB Verification</span>
                <span class="text-amber-600 font-medium">Pending</span>
            </div>

            <div class="flex items-center justify-between">
                <span>Pricing rules validated</span>
                <span class="text-green-600 font-medium">Valid</span>
            </div>

        </div>

    </div>

    {{-- Validation Rules --}}
    <div class="rounded-2xl border border-blue-200 bg-blue-50 px-4 py-4 text-sm text-blue-700 space-y-2">

        <div>
            • Either Reserve or Buy-It-Now price is required.
        </div>

        <div>
            • Buy-It-Now requires Reserve field to remain blank.
        </div>

        <div>
            • Listings with missing required items remain in Draft state.
        </div>

        <div>
            • Offer system works independently and can be enabled alongside BIN.
        </div>

    </div>

    {{-- Actions --}}
    <div class="flex flex-wrap items-center justify-between gap-3 pt-2">

        <button type="button" class="kt-btn kt-btn-danger">
            Discard Draft
        </button>

        <div class="flex items-center gap-2">

            <button type="button" class="kt-btn kt-btn-outline">
                Save Draft
            </button>

            <button type="button" class="kt-btn kt-btn-outline">
                Run Valuations Again
            </button>

            <button type="submit" class="kt-btn kt-btn-primary">
                Submit for QA
            </button>

        </div>

    </div>

</div>