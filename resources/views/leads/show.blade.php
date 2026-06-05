{{-- resources/views/leads/show.blade.php --}}
{{-- Phase 3 — C3: Lead Detail (full workspace) --}}
@extends('layouts.app')
@section('title', ($lead['id'] ?? 'Lead') . ' — Carsmart')

@section('content')

@php
    $stageCls = match ($lead['stage'] ?? '') {
        'New'                  => 'kt-badge-outline',
        'Qualified'            => 'kt-badge-info',
        'Pricing sent'         => 'kt-badge-primary',
        'Awaiting seller docs' => 'kt-badge-warning',
        'Ready'                => 'kt-badge-success',
        default                => 'kt-badge-outline',
    };
    $latestVal = !empty($lead['valuations']) ? end($lead['valuations']) : null;
    $hasVrm    = !empty($lead['vrm']) || !empty($lead['vin']);
    $hasListing = !empty($lead['linked_listing_id']);
@endphp

<div class="kt-container-fixed">

{{-- Breadcrumb --}}
<nav class="flex items-center gap-1.5 text-xs text-muted-foreground mb-4">
    <a href="{{ route('leads.index') }}" class="hover:text-foreground">Leads</a>
    <i data-lucide="chevron-right" class="w-3 h-3"></i>
    <span class="text-foreground font-medium">{{ $lead['id'] }}</span>
</nav>

{{-- Header card --}}
<div class="card border border-border rounded-xl px-5 py-4 mb-5">
    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">

        {{-- Identity --}}
        <div>
            <div class="flex items-center gap-3 flex-wrap mb-2">
                <h1 class="text-xl font-semibold text-foreground">{{ $lead['id'] }} — {{ $lead['name'] }}</h1>
                <span class="kt-badge {{ $stageCls }}">{{ $lead['stage'] }}</span>
                @if ($lead['dnc'] ?? false)
                    <span class="kt-badge kt-badge-destructive">Do Not Contact</span>
                @endif
                @if (($lead['priority'] ?? '') === 'High')
                    <span class="kt-badge kt-badge-warning kt-badge-sm">High Priority</span>
                @endif
            </div>
            <div class="flex items-center gap-4 text-sm text-muted-foreground flex-wrap">
                <span><i data-lucide="user" class="w-3 h-3 inline mr-1"></i>Owner: {{ $lead['owner'] ?: 'Unassigned' }}</span>
                <span><i data-lucide="calendar" class="w-3 h-3 inline mr-1"></i>Added: {{ $lead['date_added'] }}</span>
                @if (!empty($lead['sla_due']))
                    <span class="{{ now()->toDateString() >= $lead['sla_due'] ? 'text-destructive font-medium' : '' }}">
                        <i data-lucide="clock" class="w-3 h-3 inline mr-1"></i>SLA due: {{ $lead['sla_due'] }}
                    </span>
                @endif
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex flex-wrap gap-2">
            @if (!$hasListing)
                <button type="button" onclick="openModal('modal-convert-listing')"
                    class="kt-btn kt-btn-primary kt-btn-sm">
                    <i data-lucide="arrow-right-circle" class="w-4 h-4 mr-1"></i> Convert to listing
                </button>
            @else
                <a href="{{ route('listings.show', $lead['linked_listing_id']) }}"
                    class="kt-btn kt-btn-outline kt-btn-sm">
                    <i data-lucide="link" class="w-4 h-4 mr-1"></i> {{ $lead['linked_listing_id'] }}
                </a>
            @endif
            <button type="button" class="kt-btn kt-btn-outline kt-btn-sm" onclick="openModal('modal-send-message')">
                <i data-lucide="message-square" class="w-4 h-4 mr-1"></i> Send message
            </button>
            <button type="button" class="kt-btn kt-btn-outline kt-btn-sm" onclick="openModal('modal-add-task')">
                <i data-lucide="check-square" class="w-4 h-4 mr-1"></i> Add task
            </button>
            <div class="relative" x-data="{ open: false }">
                <button @click="open=!open" class="kt-btn kt-btn-ghost kt-btn-sm">
                    More <i data-lucide="chevron-down" class="w-3 h-3 ml-1"></i>
                </button>
                <div x-show="open" @click.away="open=false"
                    class="absolute right-0 mt-1 w-48 card border border-border rounded-lg shadow-lg z-50 py-1">
                    <button onclick="openModal('modal-move-stage')" class="w-full text-left px-4 py-2 text-sm hover:bg-muted">Move stage</button>
                    <button onclick="openModal('modal-assign')" class="w-full text-left px-4 py-2 text-sm hover:bg-muted">Assign owner</button>
                    <button onclick="openModal('modal-convert-customer')" class="w-full text-left px-4 py-2 text-sm hover:bg-muted">Convert to customer</button>
                    <button onclick="openModal('modal-merge')" class="w-full text-left px-4 py-2 text-sm hover:bg-muted">Merge duplicate</button>
                    <hr class="border-border my-1">
                    <button onclick="openModal('modal-dnc')" class="w-full text-left px-4 py-2 text-sm text-destructive hover:bg-muted">Mark Do-Not-Contact</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tab navigation --}}
<div class="flex gap-1 mb-5 border-b border-border overflow-x-auto" id="lead-tabs">
    @foreach (['overview','person','company','vehicles','communications','tasks','files','activity','history'] as $tab)
        <button
            onclick="switchTab('{{ $tab }}')"
            id="tab-btn-{{ $tab }}"
            class="tab-btn px-4 py-2.5 text-sm font-medium whitespace-nowrap border-b-2 -mb-px transition-colors
                   {{ $tab === 'overview' ? 'border-primary text-primary' : 'border-transparent text-muted-foreground hover:text-foreground' }}">
            {{ ucfirst($tab) }}
        </button>
    @endforeach
</div>

{{-- Tab content --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

    {{-- Main content (2/3) --}}
    <div class="xl:col-span-2 space-y-5">

        {{-- OVERVIEW TAB --}}
        <div id="tab-overview" class="tab-pane space-y-5">

            <div class="card border border-border rounded-xl p-5">
                <h3 class="font-semibold mb-4">Key details</h3>
                <dl class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                    <div><dt class="text-muted-foreground">Email</dt><dd>{{ $lead['email'] }}</dd></div>
                    <div><dt class="text-muted-foreground">Phone</dt><dd>{{ $lead['phone'] }}</dd></div>
                    <div><dt class="text-muted-foreground">Source</dt><dd>{{ $lead['source'] }}</dd></div>
                    <div><dt class="text-muted-foreground">Tags</dt><dd>
                        @foreach ($lead['tags'] ?? [] as $tag)
                            <span class="kt-badge kt-badge-outline kt-badge-sm mr-1">{{ $tag }}</span>
                        @endforeach
                    </dd></div>
                </dl>
            </div>

            {{-- Consent badges --}}
            <div class="card border border-border rounded-xl p-5">
                <h3 class="font-semibold mb-3">Consent & contact</h3>
                <div class="flex gap-3 flex-wrap">
                    @foreach (['email','sms','whatsapp'] as $ch)
                        <span class="kt-badge {{ ($lead['consent'][$ch] ?? false) ? 'kt-badge-success' : 'kt-badge-destructive' }}">
                            {{ strtoupper($ch) }} {{ ($lead['consent'][$ch] ?? false) ? '✔' : '✖' }}
                        </span>
                    @endforeach
                    @if ($lead['dnc'] ?? false)
                        <span class="kt-badge kt-badge-destructive">Do Not Contact</span>
                    @endif
                </div>
            </div>

            {{-- Next task --}}
            @if (!empty($lead['tasks']))
                <div class="card border border-border rounded-xl p-5">
                    <h3 class="font-semibold mb-3">Next task</h3>
                    @php $nextTask = collect($lead['tasks'])->where('status','pending')->first(); @endphp
                    @if ($nextTask)
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-sm">{{ $nextTask['title'] }}</p>
                                <p class="text-xs text-muted-foreground mt-0.5">Due: {{ $nextTask['due'] }}</p>
                            </div>
                            <span class="kt-badge {{ $nextTask['priority'] === 'High' ? 'kt-badge-destructive' : 'kt-badge-outline' }}">
                                {{ $nextTask['priority'] }}
                            </span>
                        </div>
                    @else
                        <p class="text-sm text-muted-foreground">No pending tasks.</p>
                    @endif
                </div>
            @endif

        </div>

        {{-- PERSON TAB --}}
        <div id="tab-person" class="tab-pane hidden space-y-5">
            <div class="card border border-border rounded-xl p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold">Person profile</h3>
                    <button class="kt-btn kt-btn-outline kt-btn-sm">Edit</button>
                </div>
                <dl class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                    <div><dt class="text-muted-foreground">Full name</dt><dd>{{ $lead['name'] }}</dd></div>
                    <div><dt class="text-muted-foreground">Email</dt><dd>{{ $lead['email'] }}</dd></div>
                    <div><dt class="text-muted-foreground">Phone</dt><dd>{{ $lead['phone'] }}</dd></div>
                    <div><dt class="text-muted-foreground">Preferred channel</dt><dd>Email</dd></div>
                    <div><dt class="text-muted-foreground">Best time to reach</dt><dd>Morning</dd></div>
                    <div><dt class="text-muted-foreground">Language</dt><dd>English</dd></div>
                    <div class="col-span-2"><dt class="text-muted-foreground mb-1">Address</dt>
                        <dd class="text-sm">123 Example Street, London, SW1A 1AA</dd></div>
                </dl>
            </div>
        </div>

        {{-- COMPANY TAB --}}
        <div id="tab-company" class="tab-pane hidden">
            <div class="card border border-border rounded-xl p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold">Company (optional)</h3>
                    <button class="kt-btn kt-btn-outline kt-btn-sm" onclick="openModal('modal-link-company')">Link company</button>
                </div>
                <p class="text-sm text-muted-foreground">No company linked. Link an existing vendor or create a new one.</p>
            </div>
        </div>

        {{-- VEHICLES TAB --}}
        <div id="tab-vehicles" class="tab-pane hidden space-y-5">

            <div class="card border border-border rounded-xl p-5">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="font-semibold">Vehicle</h3>
                        @if ($hasVrm)
                            <p class="text-xs text-muted-foreground mt-0.5">{{ $lead['vrm'] ?: $lead['vin'] }}</p>
                        @endif
                    </div>
                    <button class="kt-btn kt-btn-outline kt-btn-sm">Edit vehicle</button>
                </div>
                <dl class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                    <div><dt class="text-muted-foreground">VRM</dt><dd>{{ $lead['vrm'] ?: '—' }}</dd></div>
                    <div><dt class="text-muted-foreground">VIN</dt><dd>{{ $lead['vin'] ?: '—' }}</dd></div>
                    <div><dt class="text-muted-foreground">Make / Model</dt><dd>BMW 330i</dd></div>
                    <div><dt class="text-muted-foreground">Year</dt><dd>2019</dd></div>
                    <div><dt class="text-muted-foreground">Mileage</dt><dd>42,000 mi</dd></div>
                    <div><dt class="text-muted-foreground">Intent</dt><dd>Sell</dd></div>
                </dl>
            </div>

            {{-- Valuation card --}}
            <div class="card border border-border rounded-xl p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold">Valuation</h3>
                    <div class="flex gap-2">
                        <button
                            id="btn-pull-valuation"
                            onclick="pullValuation('{{ $lead['id'] }}')"
                            @if (!$hasVrm) disabled title="Add VRM or VIN first" @endif
                            class="kt-btn kt-btn-outline kt-btn-sm {{ !$hasVrm ? 'opacity-40 cursor-not-allowed' : '' }}">
                            <i data-lucide="refresh-cw" class="w-3.5 h-3.5 mr-1"></i> Pull latest
                        </button>
                        <button onclick="openModal('modal-add-valuation')"
                            @if (!$hasVrm) disabled title="Add VRM or VIN first" @endif
                            class="kt-btn kt-btn-outline kt-btn-sm {{ !$hasVrm ? 'opacity-40 cursor-not-allowed' : '' }}">
                            <i data-lucide="plus" class="w-3.5 h-3.5 mr-1"></i> Add valuation
                        </button>
                        @if ($hasListing && $latestVal)
                            <button onclick="openModal('modal-apply-pricing')"
                                class="kt-btn kt-btn-primary kt-btn-sm">
                                <i data-lucide="tag" class="w-3.5 h-3.5 mr-1"></i> Apply to pricing
                            </button>
                        @endif
                    </div>
                </div>

                @if (!$hasVrm)
                    <div class="rounded-lg bg-muted/40 p-4 text-sm text-muted-foreground text-center">
                        <i data-lucide="alert-circle" class="w-4 h-4 inline mr-1"></i>
                        Add a VRM or VIN to enable valuations.
                    </div>
                @elseif ($latestVal)
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                        <div class="rounded-lg bg-muted/40 p-3">
                            <p class="text-xs text-muted-foreground mb-1">Latest value</p>
                            <p class="text-lg font-bold text-foreground">£{{ number_format($latestVal['amount']) }}</p>
                        </div>
                        <div class="rounded-lg bg-muted/40 p-3">
                            <p class="text-xs text-muted-foreground mb-1">Source</p>
                            <p class="font-medium text-sm">{{ $latestVal['source'] }}</p>
                        </div>
                        <div class="rounded-lg bg-muted/40 p-3">
                            <p class="text-xs text-muted-foreground mb-1">Time</p>
                            <p class="font-medium text-sm">{{ \Carbon\Carbon::parse($latestVal['date'])->format('d M H:i') }}</p>
                        </div>
                        @if ($hasListing)
                            <div class="rounded-lg bg-muted/40 p-3">
                                <p class="text-xs text-muted-foreground mb-1">Δ vs guide</p>
                                <p class="font-medium text-sm text-success">+£200 (+1.4%)</p>
                            </div>
                        @endif
                    </div>

                    {{-- Valuation fetch status indicator --}}
                    <div id="valuation-fetch-status" class="hidden mb-3">
                        <div id="val-status-inprogress" class="hidden flex items-center gap-2 text-sm text-muted-foreground">
                            <svg class="animate-spin w-4 h-4" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
                            Fetching valuation…
                        </div>
                        <div id="val-status-succeeded" class="hidden flex items-center gap-2 text-sm text-success">
                            <i data-lucide="check-circle" class="w-4 h-4"></i> Valuation updated successfully.
                        </div>
                        <div id="val-status-failed" class="hidden flex items-center gap-2 text-sm text-destructive">
                            <i data-lucide="x-circle" class="w-4 h-4"></i>
                            <span id="val-status-reason">Fetch failed.</span>
                            <button onclick="pullValuation('{{ $lead['id'] }}')" class="underline">Retry</button>
                        </div>
                    </div>

                    {{-- History table --}}
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-border text-muted-foreground text-xs">
                                    <th class="text-left pb-2">Date</th>
                                    <th class="text-left pb-2">Source</th>
                                    <th class="text-left pb-2">Valuer</th>
                                    <th class="text-right pb-2">Amount</th>
                                    <th class="text-left pb-2">Notes</th>
                                    <th class="text-left pb-2">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($lead['valuations'] as $v)
                                    <tr class="border-b border-border/50">
                                        <td class="py-2">{{ \Carbon\Carbon::parse($v['date'])->format('d M H:i') }}</td>
                                        <td class="py-2">{{ $v['source'] }}</td>
                                        <td class="py-2 text-muted-foreground">{{ $v['valuer'] ?: '—' }}</td>
                                        <td class="py-2 text-right font-medium">£{{ number_format($v['amount']) }}</td>
                                        <td class="py-2 text-muted-foreground text-xs">{{ $v['notes'] ?: '—' }}</td>
                                        <td class="py-2">
                                            @if ($hasListing)
                                                <button onclick="openModal('modal-apply-pricing')"
                                                    class="kt-btn kt-btn-ghost kt-btn-xs text-primary">Apply</button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="rounded-lg bg-muted/40 p-4 text-sm text-muted-foreground text-center">
                        No valuations yet. Pull latest or add manually.
                    </div>
                @endif
            </div>
        </div>

        {{-- COMMUNICATIONS TAB --}}
        <div id="tab-communications" class="tab-pane hidden">
            <div class="card border border-border rounded-xl p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold">Communications</h3>
                    <button onclick="openModal('modal-send-message')" class="kt-btn kt-btn-primary kt-btn-sm">
                        <i data-lucide="send" class="w-3.5 h-3.5 mr-1"></i> Compose
                    </button>
                </div>
                <div class="space-y-3">
                    <div class="rounded-lg bg-muted/30 p-3 text-sm">
                        <div class="flex justify-between text-xs text-muted-foreground mb-1">
                            <span>Email · John Smith</span><span>10 Oct 14:32</span>
                        </div>
                        <p>Hi John, thanks for getting in touch regarding your BMW 330i…</p>
                    </div>
                    <div class="rounded-lg bg-primary/10 p-3 text-sm">
                        <div class="flex justify-between text-xs text-muted-foreground mb-1">
                            <span>Email · SR (you)</span><span>11 Oct 09:15</span>
                        </div>
                        <p>We'd love to get a valuation sorted — can you confirm the mileage?</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- TASKS TAB --}}
        <div id="tab-tasks" class="tab-pane hidden">
            <div class="card border border-border rounded-xl p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold">Tasks</h3>
                    <button onclick="openModal('modal-add-task')" class="kt-btn kt-btn-primary kt-btn-sm">
                        <i data-lucide="plus" class="w-3.5 h-3.5 mr-1"></i> Add task
                    </button>
                </div>
                @forelse ($lead['tasks'] ?? [] as $task)
                    <div class="flex items-center justify-between py-3 border-b border-border/50 last:border-0">
                        <div class="flex items-center gap-3">
                            <input type="checkbox" {{ $task['status'] === 'done' ? 'checked' : '' }} class="kt-checkbox">
                            <div>
                                <p class="text-sm font-medium {{ $task['status'] === 'done' ? 'line-through text-muted-foreground' : '' }}">{{ $task['title'] }}</p>
                                <p class="text-xs text-muted-foreground">Due: {{ $task['due'] }}</p>
                            </div>
                        </div>
                        <span class="kt-badge {{ $task['priority'] === 'High' ? 'kt-badge-destructive' : 'kt-badge-outline' }} kt-badge-sm">{{ $task['priority'] }}</span>
                    </div>
                @empty
                    <p class="text-sm text-muted-foreground">No tasks. Add one to stay on track.</p>
                @endforelse
            </div>
        </div>

        {{-- FILES TAB --}}
        <div id="tab-files" class="tab-pane hidden">
            <div class="card border border-border rounded-xl p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold">Files</h3>
                    <button class="kt-btn kt-btn-outline kt-btn-sm">
                        <i data-lucide="upload" class="w-3.5 h-3.5 mr-1"></i> Upload
                    </button>
                </div>
                <div class="border-2 border-dashed border-border rounded-lg p-8 text-center text-sm text-muted-foreground">
                    <i data-lucide="file" class="w-8 h-8 mx-auto mb-2 opacity-40"></i>
                    Drop files here or click Upload. Max 25 MB each.
                </div>
            </div>
        </div>

        {{-- ACTIVITY TAB --}}
        <div id="tab-activity" class="tab-pane hidden">
            <div class="card border border-border rounded-xl p-5">
                <h3 class="font-semibold mb-4">Activity timeline</h3>
                <div class="space-y-4">
                    @foreach (array_reverse($lead['activity'] ?? []) as $event)
                        <div class="flex gap-3 text-sm">
                            <div class="mt-1 w-2 h-2 rounded-full bg-primary shrink-0"></div>
                            <div>
                                <p class="font-medium">{{ str_replace('_', ' ', ucfirst($event['type'])) }}</p>
                                <p class="text-muted-foreground text-xs">{{ $event['description'] }}</p>
                                <p class="text-muted-foreground text-xs mt-0.5">{{ $event['date'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- HISTORY TAB --}}
        <div id="tab-history" class="tab-pane hidden">
            <div class="card border border-border rounded-xl p-5">
                <h3 class="font-semibold mb-4">Field-level audit history</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-border text-muted-foreground text-xs">
                                <th class="text-left pb-2">When</th>
                                <th class="text-left pb-2">Actor</th>
                                <th class="text-left pb-2">Field</th>
                                <th class="text-left pb-2">Before</th>
                                <th class="text-left pb-2">After</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-border/50">
                                <td class="py-2 text-xs text-muted-foreground">11 Oct 09:00</td>
                                <td class="py-2">SR</td>
                                <td class="py-2">stage</td>
                                <td class="py-2 text-muted-foreground">New</td>
                                <td class="py-2 text-foreground">Qualified</td>
                            </tr>
                            <tr class="border-b border-border/50">
                                <td class="py-2 text-xs text-muted-foreground">12 Oct 10:24</td>
                                <td class="py-2">System</td>
                                <td class="py-2">valuation</td>
                                <td class="py-2 text-muted-foreground">—</td>
                                <td class="py-2 text-foreground">£14,200 (AutoProvider)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    {{-- Right panel --}}
    <div class="xl:col-span-1 space-y-4">

        {{-- Summary card --}}
        <div class="card border border-border rounded-xl p-4">
            <h4 class="font-semibold text-sm mb-3">Summary</h4>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-muted-foreground">Stage</dt>
                    <dd><span class="kt-badge {{ $stageCls }} kt-badge-sm">{{ $lead['stage'] }}</span></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-muted-foreground">Owner</dt>
                    <dd>{{ $lead['owner'] ?: 'Unassigned' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-muted-foreground">Source</dt>
                    <dd>{{ $lead['source'] }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-muted-foreground">VRM</dt>
                    <dd>{{ $lead['vrm'] ?: '—' }}</dd>
                </div>
                @if ($latestVal)
                    <div class="flex justify-between">
                        <dt class="text-muted-foreground">Latest valuation</dt>
                        <dd class="font-medium">£{{ number_format($latestVal['amount']) }}</dd>
                    </div>
                @endif
                @if ($hasListing)
                    <div class="flex justify-between">
                        <dt class="text-muted-foreground">Linked listing</dt>
                        <dd><a href="{{ route('listings.show', $lead['linked_listing_id']) }}" class="text-primary hover:underline text-xs">{{ $lead['linked_listing_id'] }}</a></dd>
                    </div>
                @endif
            </dl>
        </div>

        {{-- Quick compose --}}
        <div class="card border border-border rounded-xl p-4">
            <h4 class="font-semibold text-sm mb-3">Quick note</h4>
            <textarea rows="3" placeholder="Add a note…" class="kt-textarea w-full text-sm mb-2"></textarea>
            <button class="kt-btn kt-btn-primary kt-btn-sm w-full">Save note</button>
        </div>

        {{-- Consent panel --}}
        <div class="card border border-border rounded-xl p-4">
            <h4 class="font-semibold text-sm mb-3">Consent</h4>
            <div class="space-y-2">
                @foreach (['email' => 'Email','sms' => 'SMS','whatsapp' => 'WhatsApp'] as $key => $label)
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-muted-foreground">{{ $label }}</span>
                        <span class="kt-badge {{ ($lead['consent'][$key] ?? false) ? 'kt-badge-success' : 'kt-badge-outline' }} kt-badge-sm">
                            {{ ($lead['consent'][$key] ?? false) ? 'Yes' : 'No' }}
                        </span>
                    </div>
                @endforeach
                <div class="flex items-center justify-between text-sm pt-1 border-t border-border mt-1">
                    <span class="text-muted-foreground">Do Not Contact</span>
                    <span class="kt-badge {{ ($lead['dnc'] ?? false) ? 'kt-badge-destructive' : 'kt-badge-outline' }} kt-badge-sm">
                        {{ ($lead['dnc'] ?? false) ? 'On' : 'Off' }}
                    </span>
                </div>
            </div>
            <button onclick="openModal('modal-consent')" class="kt-btn kt-btn-ghost kt-btn-xs mt-3 text-primary">Edit consent</button>
        </div>

    </div>

</div>
</div>

{{-- ── Modals ──────────────────────────────────────────────────────────────── --}}
@include('leads.partials.modals.add-valuation')
@include('leads.partials.modals.apply-pricing')

{{-- Send message modal --}}
<div id="modal-send-message" class="fixed inset-0 z-[10000] hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" data-close-modal="modal-send-message"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="card w-full max-w-lg border border-border rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-border flex items-center justify-between">
                <h3 class="font-semibold">Send message</h3>
                <button class="kt-btn kt-btn-ghost kt-btn-sm" data-close-modal="modal-send-message">✕</button>
            </div>
            <div class="p-5 space-y-4">
                <div>
                    <label class="form-label">Channel</label>
                    <select class="kt-select w-full">
                        <option @if ($lead['consent']['email'] ?? false) value="email" @else disabled @endif>Email {{ ($lead['consent']['email'] ?? false) ? '' : '(no consent)' }}</option>
                        <option @if ($lead['consent']['sms'] ?? false) value="sms" @else disabled @endif>SMS {{ ($lead['consent']['sms'] ?? false) ? '' : '(no consent)' }}</option>
                        <option @if ($lead['consent']['whatsapp'] ?? false) value="whatsapp" @else disabled @endif>WhatsApp {{ ($lead['consent']['whatsapp'] ?? false) ? '' : '(no consent)' }}</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Template</label>
                    <select class="kt-select w-full"><option value="">Select template…</option></select>
                </div>
                <div>
                    <label class="form-label">Message</label>
                    <textarea rows="4" class="kt-textarea w-full" placeholder="Type your message…"></textarea>
                </div>
            </div>
            <div class="px-5 py-4 border-t border-border flex justify-end gap-2">
                <button class="kt-btn kt-btn-outline" data-close-modal="modal-send-message">Cancel</button>
                <button class="kt-btn kt-btn-primary">Send</button>
            </div>
        </div>
    </div>
</div>

{{-- Add task modal --}}
<div id="modal-add-task" class="fixed inset-0 z-[10000] hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" data-close-modal="modal-add-task"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="card w-full max-w-md border border-border rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-border flex items-center justify-between">
                <h3 class="font-semibold">Add task</h3>
                <button class="kt-btn kt-btn-ghost kt-btn-sm" data-close-modal="modal-add-task">✕</button>
            </div>
            <div class="p-5 space-y-4">
                <div><label class="form-label">Title *</label><input type="text" class="kt-input w-full" required></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="form-label">Due date</label><input type="date" class="kt-input w-full"></div>
                    <div><label class="form-label">Priority</label>
                        <select class="kt-select w-full"><option>Normal</option><option>High</option></select>
                    </div>
                </div>
                <div><label class="form-label">Notes</label><textarea rows="2" class="kt-textarea w-full"></textarea></div>
            </div>
            <div class="px-5 py-4 border-t border-border flex justify-end gap-2">
                <button class="kt-btn kt-btn-outline" data-close-modal="modal-add-task">Cancel</button>
                <button class="kt-btn kt-btn-primary">Save task</button>
            </div>
        </div>
    </div>
</div>

{{-- Convert to listing modal --}}
<div id="modal-convert-listing" class="fixed inset-0 z-[10000] hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" data-close-modal="modal-convert-listing"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="card w-full max-w-md border border-border rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-border flex items-center justify-between">
                <div>
                    <h3 class="font-semibold">Convert to listing</h3>
                    <p class="text-xs text-muted-foreground mt-0.5">Lead valuations will be copied to the new listing.</p>
                </div>
                <button class="kt-btn kt-btn-ghost kt-btn-sm" data-close-modal="modal-convert-listing">✕</button>
            </div>
            <div class="p-5 space-y-3 text-sm">
                <p>This will create a new Listing pre-filled with this lead's vehicle and valuation data. The lead will remain open until you close it.</p>
                @if ($latestVal)
                    <div class="rounded-lg bg-muted/40 p-3">
                        <p class="text-xs text-muted-foreground mb-1">Valuation to carry over</p>
                        <p class="font-medium">£{{ number_format($latestVal['amount']) }} — {{ $latestVal['source'] }}</p>
                    </div>
                @endif
            </div>
            <div class="px-5 py-4 border-t border-border flex justify-end gap-2">
                <button class="kt-btn kt-btn-outline" data-close-modal="modal-convert-listing">Cancel</button>
                <form action="{{ route('leads.convert.listing', $lead['id']) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="kt-btn kt-btn-primary">Convert &amp; open listing</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- DNC modal --}}
<div id="modal-dnc" class="fixed inset-0 z-[10000] hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" data-close-modal="modal-dnc"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="card w-full max-w-md border border-border rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-border flex items-center justify-between">
                <h3 class="font-semibold text-destructive">Mark Do-Not-Contact</h3>
                <button class="kt-btn kt-btn-ghost kt-btn-sm" data-close-modal="modal-dnc">✕</button>
            </div>
            <div class="p-5 text-sm">
                <p>This will block all outbound messages to <strong>{{ $lead['name'] }}</strong> across all channels. You must record a reason.</p>
                <div class="mt-4">
                    <label class="form-label">Reason *</label>
                    <textarea rows="3" class="kt-textarea w-full" required></textarea>
                </div>
            </div>
            <div class="px-5 py-4 border-t border-border flex justify-end gap-2">
                <button class="kt-btn kt-btn-outline" data-close-modal="modal-dnc">Cancel</button>
                <button class="kt-btn kt-btn-destructive">Confirm DNC</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function switchTab(name) {
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(b => {
        b.classList.remove('border-primary', 'text-primary');
        b.classList.add('border-transparent', 'text-muted-foreground');
    });
    const pane = document.getElementById('tab-' + name);
    const btn  = document.getElementById('tab-btn-' + name);
    if (pane) pane.classList.remove('hidden');
    if (btn)  { btn.classList.add('border-primary','text-primary'); btn.classList.remove('border-transparent','text-muted-foreground'); }
}

function openModal(id) {
    const m = document.getElementById(id);
    if (m) m.classList.remove('hidden');
}

function closeModal(id) {
    const m = document.getElementById(id);
    if (m) m.classList.add('hidden');
}

document.querySelectorAll('[data-close-modal]').forEach(btn => {
    btn.addEventListener('click', () => closeModal(btn.dataset.closeModal));
});

function pullValuation(leadId) {
    const status = document.getElementById('valuation-fetch-status');
    const inprog = document.getElementById('val-status-inprogress');
    const succ   = document.getElementById('val-status-succeeded');
    const fail   = document.getElementById('val-status-failed');
    if (!status) return;
    status.classList.remove('hidden');
    [inprog, succ, fail].forEach(el => el?.classList.add('hidden'));
    inprog?.classList.remove('hidden');

    // Simulate fetch — replace with real fetch('/leads/' + leadId + '/valuations/pull', {method:'POST'})
    setTimeout(() => {
        inprog?.classList.add('hidden');
        succ?.classList.remove('hidden');
        setTimeout(() => status.classList.add('hidden'), 3000);
    }, 1800);
}
</script>
@endpush

@endsection
