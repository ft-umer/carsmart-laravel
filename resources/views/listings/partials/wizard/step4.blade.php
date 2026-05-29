<div class="space-y-6">

    <div>
        <h3 class="text-lg font-semibold text-gray-900">
            Documents & Compliance
        </h3>

        <p class="text-sm text-gray-500 mt-1">
            Upload ownership and compliance documents.
        </p>
    </div>

    {{-- Document Uploads --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        <div class="border rounded-2xl border-gray-200 p-5 bg-gray-50">
            <label class="block text-sm font-semibold mb-3">
                V5C Front Image
            </label>
            <input type="file" class="kt-input w-full">
        </div>

        <div class="border rounded-2xl border-gray-200 p-5 bg-gray-50">
            <label class="block text-sm font-semibold mb-3">
                V5C Back Image
            </label>
            <input type="file" class="kt-input w-full">
        </div>

        <div class="border rounded-2xl border-gray-200 p-5 bg-gray-50">
            <label class="block text-sm font-semibold mb-3">
                MOT Certificate
            </label>
            <input type="file" class="kt-input w-full">
        </div>

        <div class="border rounded-2xl border-gray-200 p-5 bg-gray-50">
            <label class="block text-sm font-semibold mb-3">
                Service Receipts
            </label>
            <input type="file" class="kt-input w-full">
        </div>

        <div class="border rounded-2xl border-gray-200 p-5 bg-gray-50 md:col-span-2">
            <label class="block text-sm font-semibold mb-3">
                Other Proofs / Supporting Documents
            </label>

            <input type="file" multiple class="kt-input w-full">

            <div class="text-xs text-gray-500 mt-2">
                Maximum upload size: 25 MB per file.
            </div>
        </div>

    </div>

    {{-- Compliance --}}
    <div class="border border-gray-200 rounded-2xl p-5">

        <h4 class="text-sm font-semibold text-gray-900 mb-4">
            Compliance Confirmation
        </h4>

        <div class="space-y-4">

            <label class="flex items-start gap-3">
                <input type="checkbox" class="mt-1">
                <span class="text-sm text-gray-700">
                    Seller consent obtained for vehicle listing and publication.
                </span>
            </label>

            <label class="flex items-start gap-3">
                <input type="checkbox" class="mt-1">
                <span class="text-sm text-gray-700">
                    Data and privacy notices accepted and internally recorded.
                </span>
            </label>

            <label class="flex items-start gap-3">
                <input type="checkbox" class="mt-1">
                <span class="text-sm text-gray-700">
                    Vehicle information verified before submission.
                </span>
            </label>

        </div>

    </div>

</div>