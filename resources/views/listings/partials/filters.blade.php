<div class="card rounded-lg border border-border p-3 mb-4">

    <form id="listing-filters" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-3 items-end">

        <!-- SEARCH -->
        <input name="search" class="kt-input w-full min-w-0" placeholder="Search listing, vehicle, VRM..." />

        <!-- STATUS -->
        <select name="state" class="kt-input w-full min-w-0">
            <option value="">Any status</option>
            <option value="Draft">Draft</option>
            <option value="Ready">Ready</option>
        </select>

        <!-- OWNER -->
        <select name="owner" class="kt-input w-full min-w-0">
            <option value="">Any owner</option>
            <option value="JR">JR</option>
            <option value="AM">AM</option>
        </select>

        <!-- QA -->
        <select name="qa" class="kt-input w-full min-w-0">
            <option value="">QA status</option>
            <option value="Pass">Pass</option>
            <option value="Needs">Needs</option>
        </select>

        <!-- RESERVE -->
        <select name="reserve" class="kt-input w-full min-w-0">
            <option value="">Reserve</option>
            <option value="1">Has Reserve</option>
            <option value="0">No Reserve</option>
        </select>

        <!-- ACTIONS -->
        <div class="flex gap-2 w-full">

            <button type="button" id="reset-filters" class="kt-btn kt-btn-ghost">
                Reset
            </button>

            <button type="submit" class="kt-btn kt-btn-mono">
                Apply
            </button>

        </div>

    </form>

</div>
