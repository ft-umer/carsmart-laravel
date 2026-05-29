{{-- ─── Toolbar ─────────────────────────────────────────────────────────── --}}

<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">

    {{-- Left: title + count --}}
    <div class="flex items-center gap-3">
        <h1 class="text-xl font-semibold text-foreground">Leads</h1>

        <span id="leads-count" class="text-sm text-muted-foreground">
            {{ count($leads) }} lead{{ count($leads) !== 1 ? 's' : '' }}
        </span>

        @php
            $overdue = collect($leads)
                ->where('sla_due', '<', now()->toDateString())
                ->count();
        @endphp

        @if ($overdue > 0)
            <span class="kt-badge kt-badge-destructive kt-badge-sm">
                {{ $overdue }} SLA breach{{ $overdue > 1 ? 'es' : '' }}
            </span>
        @endif
    </div>

    {{-- Right: actions --}}
    <div class="flex flex-wrap gap-2 items-center">

        {{-- View toggle --}}
        <div class="flex items-center border border-border rounded overflow-hidden">

            <a href="{{ route('leads.index', array_merge(request()->all(), ['view' => 'table'])) }}"
                class="px-3 py-1.5 text-xs font-medium
                {{ $view === 'table'
                    ? 'bg-primary text-primary-foreground'
                    : 'text-muted-foreground hover:bg-muted' }}">
                Table
            </a>

            <a href="{{ route('leads.index', array_merge(request()->all(), ['view' => 'board'])) }}"
                class="px-3 py-1.5 text-xs font-medium
                {{ $view === 'board'
                    ? 'bg-primary text-primary-foreground'
                    : 'text-muted-foreground hover:bg-muted' }}">
                Board
            </a>

        </div>

        {{-- Bulk actions dropdown --}}
        <div class="relative" x-data="{ open:false }">

            <button
                type="button"
                id="bulk-actions-btn"
                @click.stop="open=!open"
                class="kt-btn kt-btn-outline">

                Bulk actions

                <span
                    id="bulk-count-badge"
                    class="ml-1 text-muted-foreground text-xs">
                </span>

                ▾
            </button>

            {{-- FIXED DROPDOWN --}}
            <div
                x-cloak
                x-show="open"
                x-transition
                @click.outside="open=false"
                class="absolute left-0 mt-1 w-56 card border border-border rounded-lg shadow-xl z-[9999] py-1 bg-background">

                <button
                    data-bulk="assign-owner"
                    class="bulk-action-btn w-full text-left flex items-center gap-2 px-4 py-2 text-sm hover:bg-muted">

                    <span class="i-lucide-user-check w-4 h-4"></span>
                    Assign owner
                </button>

                <button
                    data-bulk="move-stage"
                    class="bulk-action-btn w-full text-left flex items-center gap-2 px-4 py-2 text-sm hover:bg-muted">

                    <span class="i-lucide-arrow-right w-4 h-4"></span>
                    Move stage
                </button>

                <button
                    data-bulk="send-message"
                    class="bulk-action-btn w-full text-left flex items-center gap-2 px-4 py-2 text-sm hover:bg-muted">

                    <span class="i-lucide-message-square w-4 h-4"></span>
                    Send message
                </button>

                <button
                    data-bulk="add-task"
                    class="bulk-action-btn w-full text-left flex items-center gap-2 px-4 py-2 text-sm hover:bg-muted">

                    <span class="i-lucide-check-square w-4 h-4"></span>
                    Add task
                </button>

                <button
                    data-bulk="merge"
                    class="bulk-action-btn w-full text-left flex items-center gap-2 px-4 py-2 text-sm hover:bg-muted">

                    <span class="i-lucide-git-merge w-4 h-4"></span>
                    Merge
                </button>

                <div class="border-t border-border my-1"></div>

                <button
                    id="bulk-pull-valuations-btn"
                    data-bulk="pull-valuations"
                    class="bulk-action-btn w-full text-left flex items-center gap-2 px-4 py-2 text-sm text-primary font-medium hover:bg-primary/10">

                    <span class="i-lucide-refresh-cw w-4 h-4"></span>

                    Pull valuations

                    <span class="ml-auto text-xs text-muted-foreground">
                        VRM/VIN only
                    </span>
                </button>

            </div>
        </div>

        {{-- Quick add --}}
        <button
            type="button"
            id="btn-quick-add-lead"
            class="kt-btn kt-btn-mono">

            + Add Lead
        </button>

    </div>

</div>