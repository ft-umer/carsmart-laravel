<div class="card rounded-lg border border-border p-3 mb-4">

    <form id="listing-filters" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-3 items-end">

        <!-- SEARCH -->
        <input
            name="search"
            class="kt-input w-full min-w-0"
            placeholder="Search reg, lot, listing, auction, person, company, email, telephone..." />

        <!-- STATUS -->
        <select name="status" class="kt-input w-full min-w-0">
            <option value="">Any status</option>
            <option value="Draft">Draft</option>
            <option value="Ready">Ready</option>
            <option value="Live">Live</option>
            <option value="Sold">Sold</option>
        </select>

        <!-- AUCTION -->
        <select name="auction" class="kt-input w-full min-w-0">
            <option value="">Any auction</option>
            <option value="Auction 1">Auction 1</option>
            <option value="Auction 2">Auction 2</option>
        </select>

        <!-- OWNER -->
        <select name="owner" class="kt-input w-full min-w-0">
            <option value="">Any owner</option>
            <option value="JR">JR</option>
            <option value="AM">AM</option>
        </select>

        <!-- SALE TYPE -->
        <select name="sale_type" class="kt-input w-full min-w-0">
            <option value="">Any sale type</option>
            <option value="Auction">Auction</option>
            <option value="BuyNow">Buy Now</option>
            <option value="Tender">Tender</option>
        </select>

        <!-- VLT -->
        <select name="vlt" class="kt-input w-full min-w-0">
            <option value="">Any VLT</option>
            <option value="yes">VLT</option>
            <option value="no">Non-VLT</option>
        </select>

        <!-- RESERVE -->
        <select name="reserve" class="kt-input w-full min-w-0">
            <option value="">Any reserve</option>
            <option value="set">Set</option>
            <option value="not_set">Not set</option>
        </select>

        <!-- KYC -->
        <select name="kyc" class="kt-input w-full min-w-0">
            <option value="">Any KYC</option>
            <option value="required">Required</option>
            <option value="verified">Verified</option>
            <option value="failed">Failed</option>
        </select>

        <!-- QA -->
        <select name="qa" class="kt-input w-full min-w-0">
            <option value="">Any QA</option>
            <option value="Pass">Pass</option>
            <option value="Needs">Needs</option>
        </select>

        <!-- MISSING -->
        <select name="missing" class="kt-input w-full min-w-0">
            <option value="">Missing items</option>
            <option value="photos">Photos</option>
            <option value="id">ID</option>
            <option value="v5c">V5C</option>
            <option value="pricing">Pricing</option>
        </select>

        <!-- DATE TYPE -->
        <select name="date_type" class="kt-input w-full min-w-0">
            <option value="created">Created</option>
            <option value="updated">Updated</option>
        </select>

        <!-- DATE RANGE -->
        <input
            type="date"
            name="date_from"
            class="kt-input w-full min-w-0"
            placeholder="From" />

        <input
            type="date"
            name="date_to"
            class="kt-input w-full min-w-0"
            placeholder="To" />

        <!-- ACTIONS -->
        <div class="flex gap-2 w-full lg:col-span-2">

            <button
                type="button"
                id="reset-filters"
                class="kt-btn kt-btn-ghost flex-1">
                Reset
            </button>

            <button
                type="submit"
                class="kt-btn kt-btn-mono flex-1">
                Apply
            </button>

        </div>

    </form>

</div>