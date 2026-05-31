{{-- Step 7: Summary & Submission --}}
<div class="space-y-6">

    <div class="flex items-center justify-between gap-3 flex-wrap">
        <div>
            <h3 class="text-lg font-semibold">Summary & Submission</h3>
            <p class="text-sm text-gray-500 mt-1">Review all information before submitting to Quality Assurance.</p>
        </div>
        <span class="kt-badge kt-badge-warning">Draft</span>
    </div>

    {{-- Validation checklist --}}
    <div class="border border-gray-200 rounded-2xl p-5 bg-gray-50">
        <h4 class="text-sm font-semibold text-gray-900 mb-4">Validation Checklist</h4>
        <div class="space-y-3 text-sm">
            <div class="flex items-center justify-between border-b border-gray-200 pb-2">
                <span>Vehicle details</span><span class="text-green-600 font-medium">Complete</span>
            </div>
            <div class="flex items-center justify-between border-b border-gray-200 pb-2">
                <span>Seller information</span><span class="text-green-600 font-medium">Complete</span>
            </div>
            <div class="flex items-center justify-between border-b border-gray-200 pb-2">
                <span>Required media (6 photos)</span><span class="text-red-600 font-medium">Missing 2 photos</span>
            </div>
            <div class="flex items-center justify-between border-b border-gray-200 pb-2">
                <span>Documents (V5C, MOT)</span><span class="text-amber-600 font-medium">Partial</span>
            </div>
            <div class="flex items-center justify-between border-b border-gray-200 pb-2">
                <span>Pricing rules validated</span><span class="text-green-600 font-medium">Valid</span>
            </div>
            <div class="flex items-center justify-between border-b border-gray-200 pb-2">
                <span>Compliance confirmed</span><span class="text-green-600 font-medium">Done</span>
            </div>
            <div class="flex items-center justify-between">
                <span>KYC / KYB Verification</span><span class="text-amber-600 font-medium">Pending</span>
            </div>
        </div>
    </div>

    {{-- Rules reminder --}}
    <div class="rounded-2xl border border-blue-200 bg-blue-50 px-4 py-4 text-sm text-blue-700 space-y-1">
        <div>• Listings with missing required items remain in Draft state.</div>
        <div>• KYC must be Verified before the listing can move to Publication Queue.</div>
        <div>• BIN requires Reserve to be blank.</div>
        <div>• Either Reserve or Buy-It-Now price is required to publish.</div>
    </div>

    {{-- Actions --}}
    <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
        <button type="button" class="kt-btn kt-btn-danger">Discard Draft</button>
        <div class="flex items-center gap-2">
            <button type="button" class="kt-btn kt-btn-outline">Save Draft</button>
            <button type="button" class="kt-btn kt-btn-outline">Run Valuations Again</button>
            <button type="submit" class="kt-btn kt-btn-primary">Submit for QA</button>
        </div>
    </div>

</div>
