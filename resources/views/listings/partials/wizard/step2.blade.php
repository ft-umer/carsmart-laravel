<div class="space-y-6">

    <div>
        <h3 class="text-lg font-semibold text-gray-900">
            Seller Information
        </h3>

        <p class="text-sm text-gray-500 mt-1">
            Link the seller and verify KYC/KYB compliance.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">

        <div>
            <label class="block text-xs font-medium mb-1">
                Seller Type
            </label>

            <select class="kt-input w-full">
                <option>Person</option>
                <option>Company</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-medium mb-1">
                Person / Company
            </label>

            <input type="text" class="kt-input w-full">
        </div>

        <div>
            <label class="block text-xs font-medium mb-1">
                Sale Type
            </label>

            <select class="kt-input w-full">
                <option>CST1</option>
                <option>CST2</option>
                <option>CST3</option>
                <option>CST4</option>
                <option>CST5</option>
                <option>VLT1</option>
                <option>VLT2</option>
                <option>VLT3</option>
                <option>VLT4</option>
                <option>VLT5</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-medium mb-1">
                Email Address
            </label>
            <input type="email" class="kt-input w-full">
        </div>

        <div>
            <label class="block text-xs font-medium mb-1">
                Phone Number
            </label>
            <input type="text" class="kt-input w-full">
        </div>

        <div>
            <label class="block text-xs font-medium mb-1">
                Preferred Contact Channel
            </label>

            <select class="kt-input w-full">
                <option>Phone</option>
                <option>Email</option>
                <option>SMS</option>
                <option>WhatsApp</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-medium mb-1">
                KYC / KYB Status
            </label>

            <input
                type="text"
                readonly
                value="Pending Verification"
                class="kt-input w-full bg-gray-100 cursor-not-allowed">
        </div>

        <div>
            <label class="block text-xs font-medium mb-1">
                Business Number
            </label>
            <input type="text" class="kt-input w-full">
        </div>

        <div class="md:col-span-2 xl:col-span-1">
            <label class="block text-xs font-medium mb-1">
                Address
            </label>
            <textarea rows="4" class="kt-input w-full"></textarea>
        </div>

    </div>

    <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
        Listings cannot move to the publication queue until seller KYC/KYB status is verified.
    </div>

</div>