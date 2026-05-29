{{-- ───────────────────────────────────────────────────────────────────── --}}
{{-- TABLE VIEW                                                          --}}
{{-- ───────────────────────────────────────────────────────────────────── --}}

<div class="card border border-border rounded-xl">

    {{-- Table header toolbar --}}
    <div
        class="px-4 py-3 border-b border-border flex items-center justify-between gap-3 bg-muted/20">

        <label class="flex items-center gap-2 cursor-pointer select-none">

            <input
                id="select-all-cb"
                type="checkbox"
                class="form-checkbox rounded" />

            <span
                id="selected-label"
                class="text-xs text-muted-foreground">
                0 selected
            </span>

        </label>

        <div class="flex items-center gap-2">

            <button
                id="btn-export"
                class="kt-btn kt-btn-outline kt-btn-sm">
                Export
            </button>

            <button
                id="btn-refresh-table"
                class="kt-btn kt-btn-ghost kt-btn-sm"
                title="Refresh">

                <i
                    data-lucide="refresh-cw"
                    class="w-3.5 h-3.5">
                </i>

            </button>

        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto overflow-y-visible">

        <table
            id="leads-table"
            class="w-full min-w-[960px] text-sm">

            <thead class="bg-muted/40">

                <tr>

                    <th class="p-3 w-10"></th>

                    <th class="p-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wide">
                        Lead
                    </th>

                    <th class="p-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wide">
                        Contact
                    </th>

                    <th class="p-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wide">
                        Source
                    </th>

                    <th class="p-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wide">
                        VRM / VIN
                    </th>

                    <th class="p-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wide">
                        Stage
                    </th>

                    <th class="p-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wide">
                        SLA due
                    </th>

                    <th class="p-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wide">
                        Owner
                    </th>

                    <th class="p-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wide w-48">
                        Actions
                    </th>

                </tr>

            </thead>

            <tbody
                id="leads-tbody"
                class="divide-y divide-border bg-background">

                @forelse($leads as $lead)

                    @php
                        $isOverdue = $lead['sla_due'] < now()->toDateString();
                        $isDueToday = $lead['sla_due'] === now()->toDateString();
                        $latestVal = !empty($lead['valuations'])
                            ? end($lead['valuations'])
                            : null;
                    @endphp

                    <tr
                        class="hover:bg-muted/30 transition-colors"
                        data-lead-id="{{ $lead['id'] }}">

                        {{-- Checkbox --}}
                        <td class="p-3">

                            <input
                                type="checkbox"
                                value="{{ $lead['id'] }}"
                                class="row-cb form-checkbox rounded"
                                data-has-vrm="{{ !empty($lead['vrm']) || !empty($lead['vin']) ? '1' : '0' }}" />

                        </td>

                        {{-- Name --}}
                        <td class="p-3">

                            <a
                                href="{{ route('leads.show', $lead['id']) }}"
                                class="font-medium text-foreground hover:text-primary leading-tight">

                                {{ $lead['name'] }}

                            </a>

                            <div class="text-xs text-muted-foreground mt-0.5 flex items-center gap-1">

                                {{ $lead['id'] }}

                                @if ($lead['priority'] === 'High')
                                    <span class="kt-badge kt-badge-warning kt-badge-sm">
                                        High
                                    </span>
                                @endif

                                @if ($lead['dnc'])
                                    <span class="kt-badge kt-badge-destructive kt-badge-sm">
                                        DNC
                                    </span>
                                @endif

                            </div>

                            @if (!empty($lead['tags']))
                                <div class="flex flex-wrap gap-1 mt-1">

                                    @foreach ($lead['tags'] as $tag)
                                        <span class="kt-badge kt-badge-outline kt-badge-sm">
                                            {{ $tag }}
                                        </span>
                                    @endforeach

                                </div>
                            @endif

                            <div
                                class="mt-1 valuation-job-status"
                                data-lead-id="{{ $lead['id'] }}">
                            </div>

                        </td>

                        {{-- Contact --}}
                        <td class="p-3">

                            @if ($lead['email'])
                                <a
                                    href="mailto:{{ $lead['email'] }}"
                                    class="text-xs text-primary hover:underline block">

                                    {{ $lead['email'] }}

                                </a>
                            @endif

                            @if ($lead['phone'])
                                <span class="text-xs text-muted-foreground">
                                    {{ $lead['phone'] }}
                                </span>
                            @endif

                        </td>

                        {{-- Source --}}
                        <td class="p-3">
                            <span class="text-xs">
                                {{ $lead['source'] }}
                            </span>
                        </td>

                        {{-- VRM / VIN --}}
                        <td class="p-3">

                            @if (!empty($lead['vrm']))

                                <span class="font-mono text-xs bg-muted px-2 py-0.5 rounded">
                                    {{ $lead['vrm'] }}
                                </span>

                            @elseif(!empty($lead['vin']))

                                <span class="font-mono text-xs bg-muted px-2 py-0.5 rounded">
                                    {{ $lead['vin'] }}
                                </span>

                            @else

                                <span class="text-xs text-muted-foreground">
                                    —
                                </span>

                            @endif

                            @if ($latestVal)

                                <div class="text-xs text-muted-foreground mt-0.5">
                                    £{{ number_format($latestVal['amount']) }}
                                </div>

                            @endif

                        </td>

                        {{-- Stage --}}
                        <td class="p-3">

                            @php
                                $stageCls = match ($lead['stage']) {
                                    'New' => 'kt-badge-outline',
                                    'Qualified' => 'kt-badge-primary',
                                    'Pricing sent' => 'kt-badge-warning',
                                    'Awaiting seller docs' => 'kt-badge-info',
                                    'Ready' => 'kt-badge-success',
                                    default => 'kt-badge-outline',
                                };
                            @endphp

                            <span class="kt-badge {{ $stageCls }} kt-badge-sm">
                                {{ $lead['stage'] }}
                            </span>

                        </td>

                        {{-- SLA --}}
                        <td class="p-3">

                            <span
                                class="text-xs
                                {{ $isOverdue
                                    ? 'text-destructive font-semibold'
                                    : ($isDueToday
                                        ? 'text-warning font-medium'
                                        : 'text-muted-foreground') }}">

                                {{ $isOverdue ? '⚠ ' : '' }}
                                {{ $lead['sla_due'] }}

                            </span>

                        </td>

                        {{-- Owner --}}
                        <td class="p-3">

                            @if ($lead['owner'])

                                <span
                                    class="inline-flex items-center justify-center
                                           w-7 h-7 rounded-full
                                           bg-primary/10 text-primary
                                           text-xs font-bold">

                                    {{ $lead['owner'] }}

                                </span>

                            @else

                                <span class="text-xs text-muted-foreground">
                                    Unassigned
                                </span>

                            @endif

                        </td>

                        {{-- Actions --}}
                        <td class="p-3">

                            <div class="flex items-center gap-1.5">

                                <button
                                    data-action="quick-view"
                                    data-id="{{ $lead['id'] }}"
                                    class="kt-btn kt-btn-ghost kt-btn-sm">

                                    Quick view
                                </button>

                                <a
                                    href="{{ route('leads.show', $lead['id']) }}"
                                    class="kt-btn kt-btn-outline kt-btn-sm">

                                    Open
                                </a>

                                {{-- FIXED KT DROPDOWN --}}
                                <div class="relative" x-data="{ open:false }">

                                    <button
                                        type="button"
                                        @click.stop="open=!open"
                                        class="kt-btn kt-btn-ghost kt-btn-sm">

                                        ⋯
                                    </button>

                                    <div
                                        x-cloak
                                        x-show="open"
                                        x-transition
                                        @click.outside="open=false"
                                        class="absolute right-0 mt-1 w-44
                                               card border border-border
                                               rounded-lg shadow-xl
                                               z-[9999]
                                               py-1 bg-background text-sm">
                                                                                       <a
                                            href="{{ route('leads.edit', $lead['id']) }}"
                                            class="flex items-center gap-2 px-3 py-2 hover:bg-muted">
                                            Edit
                                        </a>

                                        <button
                                            data-action="move-stage-single"
                                            data-id="{{ $lead['id'] }}"
                                            class="w-full text-left flex items-center gap-2 px-3 py-2 hover:bg-muted">

                                            Move stage
                                        </button>

                                        <button
                                            data-action="assign-single"
                                            data-id="{{ $lead['id'] }}"
                                            class="w-full text-left flex items-center gap-2 px-3 py-2 hover:bg-muted">

                                            Assign owner
                                        </button>

                                        @if (!empty($lead['vrm']) || !empty($lead['vin']))

                                            <button
                                                data-action="pull-val-single"
                                                data-id="{{ $lead['id'] }}"
                                                class="w-full text-left flex items-center gap-2 px-3 py-2 hover:bg-muted text-primary">

                                                Pull valuation
                                            </button>

                                        @endif

                                        <div class="border-t border-border my-1"></div>

                                        <button
                                            data-action="delete-single"
                                            data-id="{{ $lead['id'] }}"
                                            class="w-full text-left flex items-center gap-2 px-3 py-2 hover:bg-muted text-destructive">

                                            Delete
                                        </button>

                                    </div>

                                </div>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="9"
                            class="p-12 text-center text-muted-foreground text-sm">

                            <i
                                data-lucide="inbox"
                                class="w-8 h-8 mx-auto mb-2 opacity-30">
                            </i>

                            No leads found.

                            <a
                                href="{{ route('leads.create') }}"
                                class="text-primary hover:underline">

                                Add your first lead

                            </a>

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    {{-- Pagination --}}
    <div
        class="px-4 py-3 border-t border-border
               flex items-center justify-between
               text-xs text-muted-foreground">

        <span>
            Showing {{ count($leads) }} of {{ count($leads) }}
        </span>

        <div class="flex gap-2">

            <button
                class="kt-btn kt-btn-ghost kt-btn-sm"
                disabled>

                Prev

            </button>

            <button
                class="kt-btn kt-btn-ghost kt-btn-sm"
                disabled>

                Next

            </button>

        </div>

    </div>

</div>