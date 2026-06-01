@extends('layouts.app')

@section('title', 'Carsmart — Auctions')

@section('content')

    <div class="w-full min-w-0">
        <section id="auctions-section" class="kt-container-fixed w-full px-4 lg:px-6 py-6 overflow-x-hidden">

            {{-- ── Top toolbar ──────────────────────────────────────────── --}}
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-5">
                <div>
                    <h1 class="text-2xl font-semibold text-foreground">Auctions</h1>
                    <p class="text-sm text-muted-foreground mt-0.5">
                        Plan · Publish · Operate · Resolve
                    </p>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    <button id="btn-view-calendar" class="kt-btn kt-btn-ghost flex items-center gap-2">
                        <i class="ki-outline ki-calendar text-base"></i> Calendar
                    </button>
                    <div class="relative">
                        <button id="bulk-actions-toggle" class="kt-btn kt-btn-outline flex items-center gap-2">
                            <i class="ki-outline ki-layers text-base"></i> Bulk actions ▾
                        </button>
                        <div id="bulk-actions-menu"
                            class="hidden absolute right-0 mt-2 w-52 card p-2 rounded-lg shadow-lg bg-background border border-border z-30">
                            <button data-bulk="publish" class="kt-btn kt-btn-ghost w-full text-left py-2">Publish
                                selected</button>
                            <button data-bulk="pause" class="kt-btn kt-btn-ghost w-full text-left py-2">Pause
                                selected</button>
                            <button data-bulk="duplicate" class="kt-btn kt-btn-ghost w-full text-left py-2">Duplicate
                                selected</button>
                            <hr class="my-1 border-border">
                            <button data-bulk="archive" class="kt-btn kt-btn-destructive w-full text-left py-2">Archive
                                selected</button>
                        </div>
                    </div>
                    <button id="btn-create-auction" class="kt-btn kt-btn-mono flex items-center gap-2">
                        <i class="ki-outline ki-plus text-base"></i> Create auction
                    </button>
                </div>
            </div>

            {{-- ── VIEW SWITCHER ────────────────────────────────────────── --}}
            <div class="flex items-center gap-1 mb-4 border-b border-border">
                <button data-view="list"
                    class="view-tab-btn px-4 py-2 text-sm border-b-2 border-primary font-medium text-foreground">
                    List
                </button>
                <button data-view="calendar"
                    class="view-tab-btn px-4 py-2 text-sm border-b-2 border-transparent text-muted-foreground hover:text-foreground">
                    Calendar
                </button>
            </div>

            {{-- ══════════════════════════════════════════════════════════
         A0 LIST VIEW
         ══════════════════════════════════════════════════════════ --}}
            <div id="view-list">

                {{-- Filters --}}
                <div class="card rounded-lg border border-border p-3 mb-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
                        <input id="filter-search" class="kt-input lg:col-span-2"
                            placeholder="Search auction name / number / owner" />

                        <select id="filter-status" class="kt-input">
                            <option value="">Any status</option>
                            <option>Planned</option>
                            <option>Published</option>
                            <option>Live</option>
                            <option>Paused</option>
                            <option>Ended</option>
                            <option>Archived</option>
                        </select>

                        <select id="filter-visibility" class="kt-input">
                            <option value="">Visibility (any)</option>
                            <option>Public</option>
                            <option>Private</option>
                        </select>

                        <div class="flex gap-2">
                            <button id="btn-reset-filters" class="kt-btn kt-btn-ghost flex-1">Reset</button>
                            <button id="btn-apply-filters" class="kt-btn kt-btn-mono flex-1">Apply</button>
                            <button id="btn-export-csv" class="kt-btn kt-btn-outline flex-shrink-0" title="Export CSV">
                                <i class="ki-outline ki-exit-down text-base"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Table --}}
                <div class="card rounded-lg border border-border w-full overflow-x-auto">
                    <div class="p-3 border-b border-border flex items-center gap-3">
                        <label class="flex items-center gap-2">
                            <input id="select-all" type="checkbox" class="form-checkbox" />
                            <span class="text-sm text-muted-foreground">Select all</span>
                        </label>
                        <div id="bulk-count" class="text-sm text-muted-foreground">0 selected</div>
                    </div>

                    <table id="auctions-table" class="min-w-[1050px] w-full text-sm">
                        <thead class="bg-muted/40">
                            <tr>
                                <th class="p-3 w-10"></th>
                                <th class="p-3 text-left">Auction</th>
                                <th class="p-3 text-left">Schedule (start → end)</th>
                                <th class="p-3 text-right">Lots</th>
                                <th class="p-3 text-right">Live</th>
                                <th class="p-3 text-right">Ended</th>
                                <th class="p-3 text-left">Reserve met</th>
                                <th class="p-3 text-left">Visibility</th>
                                <th class="p-3 text-left">Owner</th>
                                <th class="p-3 text-left w-56">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="auctions-tbody" class="divide-y divide-border bg-background">

                            @foreach ($auctions as $auction)
                                <tr class="hover:bg-muted/20 transition-colors">

                                    <td class="p-3">
                                        <input type="checkbox" class="rounded border-border">
                                    </td>

                                    <td class="p-3">

                                        <div class="flex flex-col">

                                            <span class="font-medium text-foreground">
                                                {{ $auction['title'] }}
                                            </span>

                                            <span class="text-xs text-muted-foreground">
                                                #{{ $auction['id'] }}
                                            </span>

                                        </div>

                                    </td>

                                    <td class="p-3 text-sm text-muted-foreground">

                                        <div class="flex flex-col">
                                            <span>{{ $auction['start_date'] }}</span>
                                            <span>{{ $auction['end_date'] }}</span>
                                        </div>

                                    </td>

                                    <td class="p-3 text-right">
                                        {{ $auction['lots'] }}
                                    </td>

                                    <td class="p-3 text-right text-green-500 font-medium">
                                        {{ $auction['live'] }}
                                    </td>

                                    <td class="p-3 text-right text-muted-foreground">
                                        {{ $auction['ended'] }}
                                    </td>

                                    <td class="p-3">

                                        <span class="kt-badge kt-badge-success kt-badge-outline">
                                            {{ $auction['reserve_met'] }}
                                        </span>

                                    </td>

                                    <td class="p-3">

                                        @if ($auction['visibility'] === 'Public')
                                            <span class="kt-badge kt-badge-primary kt-badge-outline">
                                                Public
                                            </span>
                                        @else
                                            <span class="kt-badge kt-badge-secondary kt-badge-outline">
                                                Private
                                            </span>
                                        @endif

                                    </td>

                                    <td class="p-3 text-sm text-foreground">
                                        {{ $auction['owner'] }}
                                    </td>

                                    <td class="p-3">

                                        <div class="flex items-center gap-2">

                                           <a href="{{ route('auctions.show', $auction['id']) }}"
   class="kt-btn kt-btn-sm kt-btn-outline">
    View
</a>

                                            <button class="kt-btn kt-btn-sm kt-btn-ghost">
                                                Edit
                                            </button>

                                        </div>

                                    </td>

                                </tr>
                            @endforeach

                        </tbody>
                    </table>

                    <div class="p-3 border-t border-border flex items-center justify-between">
                        <div id="auctions-pagination" class="text-sm text-muted-foreground">Showing 0 auctions</div>
                        <div class="flex gap-2">
                            <button id="list-prev" class="kt-btn kt-btn-ghost">← Prev</button>
                            <button id="list-next" class="kt-btn kt-btn-ghost">Next →</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══════════════════════════════════════════════════════════
         A1 CALENDAR VIEW
         ══════════════════════════════════════════════════════════ --}}
            <div id="view-calendar" class="hidden">
                @include('auctions.partials.calendar')
            </div>

        </section>
    </div>

    {{-- ── Modals ──────────────────────────────────────────────── --}}
    @include('auctions.partials.create-wizard-modal')
    @include('auctions.partials.detail-modal')
    @include('auctions.partials.lot-detail-modal')
    @include('auctions.partials.participants-modal')
    @include('auctions.partials.post-auction-modal')
    @include('auctions.partials.exchange-proposal-modal')
    @include('auctions.partials.live-console-modal')
    @include('auctions.partials.notifications-modal')


    @push('scripts')
        <script src="{{ asset('js/auctions/auctions.js') }}"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const tabs = document.querySelectorAll('.view-tab-btn');

                const listView = document.getElementById('view-list');
                const calendarView = document.getElementById('view-calendar');

                tabs.forEach(tab => {

                    tab.addEventListener('click', function() {

                        const view = this.dataset.view;

                        // reset tabs
                        tabs.forEach(btn => {
                            btn.classList.remove(
                                'border-primary',
                                'font-medium',
                                'text-foreground'
                            );

                            btn.classList.add(
                                'border-transparent',
                                'text-muted-foreground'
                            );
                        });

                        // active tab
                        this.classList.remove(
                            'border-transparent',
                            'text-muted-foreground'
                        );

                        this.classList.add(
                            'border-primary',
                            'font-medium',
                            'text-foreground'
                        );

                        // switch views
                        if (view === 'calendar') {

                            listView.classList.add('hidden');
                            calendarView.classList.remove('hidden');

                        } else {

                            calendarView.classList.add('hidden');
                            listView.classList.remove('hidden');

                        }

                    });

                });


                // ─────────────────────────────
                // Create Auction Modal
                // ─────────────────────────────

                const createAuctionBtn = document.getElementById('btn-create-auction');
                const createAuctionModal = document.getElementById('create-auction-modal');

                if (createAuctionBtn && createAuctionModal) {

                    createAuctionBtn.addEventListener('click', () => {

                        // show wrapper
                        createAuctionModal.classList.remove('hidden');
                        createAuctionModal.classList.add('flex');

                        // animate dialog
                        const dialog = createAuctionModal.querySelector('[role="dialog"]');

                        setTimeout(() => {

                            dialog.classList.remove('opacity-0', 'scale-95');
                            dialog.classList.add('opacity-100', 'scale-100');

                        }, 10);

                    });

                }

                // ─────────────────────────────
                // Close Modal
                // ─────────────────────────────

                const closeModal = () => {

                    const dialog = createAuctionModal.querySelector('[role="dialog"]');

                    dialog.classList.remove('opacity-100', 'scale-100');
                    dialog.classList.add('opacity-0', 'scale-95');

                    setTimeout(() => {

                        createAuctionModal.classList.add('hidden');
                        createAuctionModal.classList.remove('flex');

                    }, 200);

                };

                createAuctionModal.querySelectorAll('[data-modal-close], [data-modal-backdrop]')
                    .forEach(el => {

                        el.addEventListener('click', closeModal);

                    });

                // ─────────────────────────────
                // Auction Wizard
                // ─────────────────────────────

                let currentStep = 1;
                const totalSteps = 7;

                const wizardSteps = document.querySelectorAll('.wizard-step');
                const nextBtn = document.getElementById('wizard-next');
                const backBtn = document.getElementById('wizard-back');
                const createBtn = document.getElementById('wizard-create');

                const progressBar = document.getElementById('wizard-progress-bar');
                const stepLabel = document.getElementById('wizard-step-label');
                const pills = document.querySelectorAll('.wizard-pill');

                function showStep(step) {

                    currentStep = step;

                    // Toggle steps
                    wizardSteps.forEach(el => {

                        const elStep = Number(el.dataset.step);

                        if (elStep === step) {
                            el.classList.remove('hidden');
                        } else {
                            el.classList.add('hidden');
                        }

                    });

                    // Update pills
                    pills.forEach(pill => {

                        const pillStep = Number(pill.dataset.pill);

                        pill.classList.remove(
                            'border-primary',
                            'bg-primary/10',
                            'text-primary',
                            'font-medium'
                        );

                        pill.classList.add(
                            'border-border',
                            'text-muted-foreground'
                        );

                        if (pillStep === step) {

                            pill.classList.remove(
                                'border-border',
                                'text-muted-foreground'
                            );

                            pill.classList.add(
                                'border-primary',
                                'bg-primary/10',
                                'text-primary',
                                'font-medium'
                            );

                        }

                    });

                    // Buttons
                    backBtn.disabled = step === 1;

                    if (step === totalSteps) {

                        nextBtn.classList.add('hidden');
                        createBtn.classList.remove('hidden');

                    } else {

                        nextBtn.classList.remove('hidden');
                        createBtn.classList.add('hidden');

                    }

                    // Progress
                    const progress = (step / totalSteps) * 100;

                    progressBar.style.width = `${progress}%`;

                    stepLabel.innerText = `Step ${step} of ${totalSteps}`;

                }

                // Next
                nextBtn.addEventListener('click', () => {

                    if (currentStep < totalSteps) {

                        showStep(currentStep + 1);

                    }

                });

                // Back
                backBtn.addEventListener('click', () => {

                    if (currentStep > 1) {

                        showStep(currentStep - 1);

                    }

                });

                // Click pills
                pills.forEach(pill => {

                    pill.addEventListener('click', () => {

                        const step = Number(pill.dataset.pill);

                        showStep(step);

                    });

                });

                // Init
                showStep(1);

            });
        </script>
    @endpush
@endsection
