{{-- ───────────────────────────────────────────────────────────────────── --}}
{{-- BOARD VIEW (KANBAN)                                                  --}}
{{-- ───────────────────────────────────────────────────────────────────── --}}

@php
    $stages = [
        'New',
        'Qualified',
        'Pricing sent',
        'Awaiting seller docs',
        'Ready',
    ];
@endphp

<div class="grid grid-cols-1 xl:grid-cols-5 gap-4">

    @foreach ($stages as $stage)

        @php
            $stageLeads = collect($leads)->where('stage', $stage);
        @endphp

        <div
            class="card border border-border rounded-xl overflow-hidden h-fit">

            {{-- Column Header --}}
            <div
                class="px-4 py-3 border-b border-border bg-muted/20">

                <div class="flex items-center justify-between">

                    <div class="font-medium text-sm">

                        {{ $stage }}

                    </div>

                    <span
                        class="kt-badge kt-badge-outline kt-badge-sm">

                        {{ $stageLeads->count() }}

                    </span>

                </div>

            </div>

            {{-- Cards --}}
            <div class="p-3 space-y-3">

                @forelse($stageLeads as $lead)

                    @php
                        $latestVal = !empty($lead['valuations'])
                            ? end($lead['valuations'])
                            : null;

                        $isOverdue =
                            !empty($lead['sla_due']) &&
                            $lead['sla_due'] < now()->toDateString();
                    @endphp

                    <div
                        class="border border-border rounded-xl p-3 bg-background hover:shadow-sm transition">

                        {{-- Lead Name --}}
                        <div class="flex items-start justify-between gap-2">

                            <div>

                                <a
                                    href="{{ route('leads.show', $lead['id']) }}"
                                    class="font-medium text-sm hover:text-primary">

                                    {{ $lead['name'] }}

                                </a>

                                <div
                                    class="text-xs text-muted-foreground mt-0.5">

                                    {{ $lead['id'] }}

                                </div>

                            </div>

                            @if ($lead['priority'] === 'High')
                                <span
                                    class="kt-badge kt-badge-warning kt-badge-sm">

                                    High

                                </span>
                            @endif

                        </div>

                        {{-- Contact --}}
                        <div
                            class="mt-3 text-xs text-muted-foreground space-y-1">

                            @if (!empty($lead['email']))
                                <div>
                                    {{ $lead['email'] }}
                                </div>
                            @endif

                            @if (!empty($lead['phone']))
                                <div>
                                    {{ $lead['phone'] }}
                                </div>
                            @endif

                        </div>

                        {{-- VRM --}}
                        @if (!empty($lead['vrm']) || !empty($lead['vin']))

                            <div class="mt-3">

                                <span
                                    class="font-mono text-xs bg-muted px-2 py-1 rounded">

                                    {{ $lead['vrm'] ?: $lead['vin'] }}

                                </span>

                            </div>

                        @endif

                        {{-- Latest Valuation --}}
                        @if ($latestVal)

                            <div
                                class="mt-3 text-xs">

                                <div class="text-muted-foreground">

                                    Latest valuation

                                </div>

                                <div class="font-semibold">

                                    £{{ number_format($latestVal['amount']) }}

                                </div>

                            </div>

                        @endif

                        {{-- SLA --}}
                        @if (!empty($lead['sla_due']))

                            <div
                                class="mt-3 text-xs {{ $isOverdue ? 'text-destructive font-medium' : 'text-muted-foreground' }}">

                                {{ $isOverdue ? '⚠ ' : '' }}
                                SLA: {{ $lead['sla_due'] }}

                            </div>

                        @endif

                        {{-- Tags --}}
                        @if (!empty($lead['tags']))

                            <div
                                class="flex flex-wrap gap-1 mt-3">

                                @foreach ($lead['tags'] as $tag)

                                    <span
                                        class="kt-badge kt-badge-outline kt-badge-sm">

                                        {{ $tag }}

                                    </span>

                                @endforeach

                            </div>

                        @endif

                        {{-- Footer Actions --}}
                        <div
                            class="flex items-center gap-2 mt-4 pt-3 border-t border-border">

                            <button
                                data-action="quick-view"
                                data-id="{{ $lead['id'] }}"
                                class="kt-btn kt-btn-ghost kt-btn-sm flex-1">

                                Quick View

                            </button>

                            <a
                                href="{{ route('leads.show', $lead['id']) }}"
                                class="kt-btn kt-btn-outline kt-btn-sm">

                                Open

                            </a>

                        </div>

                    </div>

                @empty

                    <div
                        class="text-center text-xs text-muted-foreground py-8">

                        No leads

                    </div>

                @endforelse

            </div>

        </div>

    @endforeach

</div>