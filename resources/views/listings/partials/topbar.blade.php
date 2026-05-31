{{-- L0 Topbar — module switcher includes all Phase 1 views --}}
<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-4">

    {{-- LEFT: view switcher + title --}}
    <div class="flex flex-col gap-3">

        {{-- Module tab switcher --}}
        <div class="flex items-center gap-1 flex-wrap">
            @foreach([
                ['listings','Listings'],
                ['qa','QA Queue'],
                ['publication','Publication Queue'],
                ['valuations','Valuations'],
                ['exchange','Exchange Proposals'],
                ['lifecycle','Lifecycle'],
            ] as [$view,$label])
                <button data-view="{{ $view }}"
                    class="kt-btn kt-btn-sm {{ $view === 'listings' ? 'kt-btn-mono active-view' : 'kt-btn-ghost' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <div id="listings-title">
            <h1 class="text-xl font-semibold">Listings</h1>
            <div class="text-sm text-muted-foreground">Browse · Search · QA · Valuations · Publication Queue · Exchange · Lifecycle</div>
        </div>

    </div>

    {{-- RIGHT: actions (hidden on non-listings views) --}}
    <div id="listings-actions" class="flex flex-wrap items-center gap-2">

        {{-- CREATE --}}
        <button id="btn-create-listing" class="kt-btn kt-btn-mono">
            <i class="ki-filled ki-plus"></i> Create Listing
        </button>

        {{-- BULK ACTIONS --}}
        <div class="kt-menu" data-kt-menu="true">
            <button class="kt-btn kt-btn-outline">Bulk Actions <i class="ki-filled ki-down"></i></button>
            <div class="kt-menu-dropdown w-72">
                <div class="kt-menu-item"><button class="kt-menu-link" data-bulk="assign-owner">Assign Owner</button></div>
                <div class="kt-menu-item"><button class="kt-menu-link" data-bulk="mark-qa">Mark for Quality Assurance</button></div>
                <div class="kt-menu-item"><button class="kt-menu-link" data-bulk="enable-bin-offer">Enable Buy It Now / Offer</button></div>
                <div class="kt-menu-item"><button class="kt-menu-link" data-bulk="publication-queue">Move to Publication Queue</button></div>
                <div class="kt-menu-item"><button class="kt-menu-link" data-bulk="create-auction">Create Auction</button></div>
                <div class="kt-menu-item"><button class="kt-menu-link" data-bulk="pull-valuations">Pull Valuations</button></div>
                <div class="kt-menu-separator"></div>
                <div class="kt-menu-item"><button class="kt-menu-link text-danger" data-bulk="archive">Archive</button></div>
            </div>
        </div>

        {{-- SAVED VIEWS --}}
        <div class="kt-menu" data-kt-menu="true">
            <button class="kt-btn kt-btn-outline">Saved Views <i class="ki-filled ki-down"></i></button>
            <div class="kt-menu-dropdown w-56">
                <div class="kt-menu-item"><button class="kt-menu-link">My Listings</button></div>
                <div class="kt-menu-item"><button class="kt-menu-link">QA Queue</button></div>
                <div class="kt-menu-item"><button class="kt-menu-link">Ready for Publication</button></div>
                <div class="kt-menu-item"><button class="kt-menu-link">Auction Listings</button></div>
                <div class="kt-menu-separator"></div>
                <div class="kt-menu-item"><button class="kt-menu-link">Save Current View</button></div>
            </div>
        </div>

        {{-- EXPORT --}}
        <div class="kt-menu" data-kt-menu="true">
            <button class="kt-btn kt-btn-outline">Export <i class="ki-filled ki-down"></i></button>
            <div class="kt-menu-dropdown w-48">
                <div class="kt-menu-item"><button class="kt-menu-link">Export CSV</button></div>
            </div>
        </div>

        {{-- COLUMNS --}}
        <div class="kt-menu" data-kt-menu="true">
            <button class="kt-btn kt-btn-outline">Columns <i class="ki-filled ki-down"></i></button>
            <div class="kt-menu-dropdown w-56">
                @foreach(['Vehicle','Valuation','Reserve','BIN / Offer','QA','Auction','State','Owner'] as $col)
                    <label class="kt-menu-item flex items-center gap-2">
                        <input type="checkbox" checked class="kt-checkbox"> {{ $col }}
                    </label>
                @endforeach
            </div>
        </div>

        {{-- L10: Include Archived (Governance) --}}
        <label class="flex items-center gap-2 px-2">
            <input type="checkbox" class="kt-checkbox">
            <span class="text-sm">Include Archived</span>
        </label>

        <button class="kt-btn kt-btn-ghost">
            <i class="ki-filled ki-arrows-circle"></i> Refresh
        </button>

    </div>

</div>

<script>
document.addEventListener('click', function (e) {
    const button = e.target.closest('[data-view]');
    if (!button) return;

    const view = button.dataset.view;
    const allViews = ['listings','qa','publication','valuations','exchange','lifecycle'];

    allViews.forEach(v => {
        document.getElementById(`view-${v}`)?.classList.add('hidden');
    });
    document.getElementById(`view-${view}`)?.classList.remove('hidden');

    document.querySelectorAll('[data-view]').forEach(btn => {
        btn.classList.remove('kt-btn-mono');
        btn.classList.add('kt-btn-ghost');
    });
    button.classList.remove('kt-btn-ghost');
    button.classList.add('kt-btn-mono');

    // Hide/show listings-specific UI
    const isListings = view === 'listings';
    document.getElementById('listings-title')?.classList.toggle('hidden', !isListings);
    document.getElementById('listings-actions')?.classList.toggle('hidden', !isListings);
    document.getElementById('listings-filters')?.classList.toggle('hidden', !isListings);
});
</script>
