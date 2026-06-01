{{-- resources/views/logistics/jobs/index.blade.php --}}
{{-- Phase 4 — L2: Logistics → Jobs (Scheduler) --}}
@extends('layouts.app')
@section('title', 'Transport Jobs — Logistics')

@section('content')

    @include('partials._retention_banner')

    <div class="kt-container-fixed">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
            <div class="flex items-center gap-3 flex-wrap">
                <h1 class="text-xl font-semibold text-foreground">Transport Jobs</h1>
                <span class="text-sm text-muted-foreground">{{ count($jobs) }} job{{ count($jobs) !== 1 ? 's' : '' }}</span>
                @php $inTransit = collect($jobs)->where('status','In transit')->count(); @endphp
                @if ($inTransit > 0)
                    <span class="kt-badge kt-badge-warning kt-badge-sm">{{ $inTransit }} in transit</span>
                @endif
            </div>
            <div class="flex gap-2">
                {{-- View toggle --}}
                <div class="flex items-center border border-border rounded overflow-hidden">
                    <a href="{{ route('logistics.jobs.index', array_merge(request()->all(), ['view' => 'list'])) }}"
                        class="px-3 py-1.5 text-xs font-medium transition-colors
                      {{ ($view ?? 'list') === 'list' ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:bg-muted' }}">
                        List
                    </a>
                    <a href="{{ route('logistics.jobs.index', array_merge(request()->all(), ['view' => 'calendar'])) }}"
                        class="px-3 py-1.5 text-xs font-medium transition-colors
                      {{ ($view ?? '') === 'calendar' ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:bg-muted' }}">
                        Calendar
                    </a>
                </div>
                <a href="{{ route('logistics.jobs.create') }}" class="kt-btn kt-btn-mono">
                    + Create job
                </a>
            </div>
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('logistics.jobs.index') }}"
            class="card border border-border rounded-lg p-3 mb-5">
            <input type="hidden" name="view" value="{{ $view ?? 'list' }}">
            <div class="flex flex-wrap gap-2 items-end">
                <div class="flex-1 min-w-[180px]">
                    <label class="block text-xs text-muted-foreground mb-1">Search</label>
                    <input name="search" value="{{ $search ?? '' }}" type="search" class="kt-input w-full"
                        placeholder="Job / deal / VRM / provider…" />
                </div>
                <div class="min-w-[130px]">
                    <label class="block text-xs text-muted-foreground mb-1">Status</label>
                    <select name="status" class="kt-input w-full">
                        <option value="">All</option>
                        @foreach (['Scheduled', 'In transit', 'Delivered', 'Issue'] as $s)
                            <option value="{{ $s }}" @selected(($jobStatus ?? '') === $s)>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-[160px]">
                    <label class="block text-xs text-muted-foreground mb-1">Provider</label>
                    <select name="provider" class="kt-input w-full">
                        <option value="">All providers</option>
                        @foreach ($providers ?? [] as $p)
                            <option value="{{ $p }}" @selected(($provider ?? '') === $p)>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-[130px]">
                    <label class="block text-xs text-muted-foreground mb-1">Owner</label>
                    <select name="owner" class="kt-input w-full">
                        <option value="">All owners</option>
                        @foreach ($owners ?? [] as $o)
                            <option value="{{ $o }}" @selected(($owner ?? '') === $o)>{{ $o }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="kt-btn kt-btn-mono self-end">Apply</button>
                <a href="{{ route('logistics.jobs.index') }}" class="kt-btn kt-btn-ghost self-end">Reset</a>
            </div>
        </form>

        @if (($view ?? 'list') === 'calendar')
            {{-- Calendar view (simplified weekly) --}}
            @include('logistics.partials._jobs_calendar', ['jobs' => $jobs])
        @else
            {{-- List view + QV --}}
            <div class="grid grid-cols-1 xl:grid-cols-[1fr,380px] gap-5">

                <div class="card border border-border rounded-xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[960px] text-sm">
                            <thead class="bg-muted/40 sticky top-0 z-10">
                                <tr>
                                    @foreach (['#', 'Job', 'Deal', 'Vehicle', 'Pickup → Drop', 'Slot', 'Provider', 'Status', 'Actions'] as $col)
                                        <th
                                            class="p-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wide
                                           {{ $col === 'Actions' ? 'w-56' : '' }}">
                                            {{ $col }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border bg-background">
                                @forelse ($jobs as $job)
                                    @php
                                        $jCls = match ($job['status'] ?? '') {
                                            'Scheduled' => 'kt-badge-info',
                                            'In transit' => 'kt-badge-warning',
                                            'Delivered' => 'kt-badge-success',
                                            'Issue' => 'kt-badge-destructive',
                                            default => 'kt-badge-outline',
                                        };
                                    @endphp
                                    <tr class="hover:bg-muted/30 transition-colors cursor-pointer" data-action="preview-job"
                                        data-id="{{ $job['id'] }}">
                                        <td class="p-3 text-xs text-muted-foreground">{{ $loop->iteration }}</td>
                                       <td class="p-3" onclick="event.stopPropagation()">
                                            <button data-action="preview-job" data-id="{{ $job['id'] }}"
                                                class="font-medium text-foreground hover:text-primary text-left">
                                                {{ $job['ref'] ?? 'JOB-' . $job['id'] }}
                                            </button>
                                        </td>
                                        <td class="p-3 font-mono text-xs">{{ $job['deal_ref'] ?? '—' }}</td>
                                        <td class="p-3">
                                            <div class="text-xs font-medium">{{ $job['vehicle_title'] ?? '—' }}</div>
                                            @if ($job['vrm'] ?? null)
                                                <span
                                                    class="font-mono text-xs bg-muted px-1.5 py-0.5 rounded">{{ $job['vrm'] }}</span>
                                            @endif
                                        </td>
                                        <td class="p-3 text-xs">
                                            <div>{{ $job['pickup_address'] ?? '—' }}</div>
                                            <div class="text-muted-foreground">→ {{ $job['drop_address'] ?? '—' }}</div>
                                        </td>
                                        <td class="p-3 text-xs">{{ $job['slot'] ?? '—' }}</td>
                                        <td class="p-3 text-xs">{{ $job['provider'] ?? '—' }}</td>
                                        <td class="p-3">
                                            <span
                                                class="kt-badge {{ $jCls }} kt-badge-sm">{{ $job['status'] ?? '—' }}</span>
                                        </td>
                                        <td class="p-3">
                                            <div class="flex items-center gap-1.5">
                                                <button data-action="preview-job" data-id="{{ $job['id'] }}"
                                                    class="kt-btn kt-btn-ghost kt-btn-sm">Preview</button>
                                                <a href="{{ route('logistics.jobs.show', $job['id']) }}"
                                                    class="kt-btn kt-btn-outline kt-btn-sm">Open</a>
                                                <div class="relative" x-data="{ open: false }">
                                                    <button @click="open=!open" class="kt-btn kt-btn-ghost kt-btn-sm px-2">
                                                        <i data-lucide="more-horizontal" class="w-4 h-4"></i>
                                                    </button>
                                                    <div x-show="open" @click.outside="open=false"
                                                        class="absolute right-0 mt-1 w-52 bg-background border border-border
                                                        rounded-lg shadow-lg z-20 py-1 text-sm">
                                                        <button data-action="message-provider"
                                                            data-id="{{ $job['id'] }}"
                                                            class="w-full text-left flex items-center gap-2 px-3 py-2 hover:bg-muted">
                                                            <i data-lucide="message-circle"
                                                                class="w-3.5 h-3.5 opacity-60"></i>
                                                            Message provider
                                                        </button>
                                                        @if (in_array($job['status'] ?? '', ['Scheduled']))
                                                            <button data-action="mark-in-transit"
                                                                data-id="{{ $job['id'] }}"
                                                                class="w-full text-left flex items-center gap-2 px-3 py-2 hover:bg-muted">
                                                                <i data-lucide="truck" class="w-3.5 h-3.5 opacity-60"></i>
                                                                Mark in transit
                                                            </button>
                                                        @endif
                                                        @if (in_array($job['status'] ?? '', ['Scheduled', 'In transit']))
                                                            <button data-action="mark-delivered"
                                                                data-id="{{ $job['id'] }}"
                                                                class="w-full text-left flex items-center gap-2 px-3 py-2 hover:bg-muted text-green-600">
                                                                <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                                                                Mark delivered
                                                            </button>
                                                        @endif
                                                        <button data-action="upload-proof" data-id="{{ $job['id'] }}"
                                                            class="w-full text-left flex items-center gap-2 px-3 py-2 hover:bg-muted">
                                                            <i data-lucide="upload" class="w-3.5 h-3.5 opacity-60"></i>
                                                            Upload proof
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="p-12 text-center text-muted-foreground text-sm">
                                            <i data-lucide="truck" class="w-8 h-8 mx-auto mb-2 opacity-30"></i>
                                            No transport jobs.
                                            <a href="{{ route('logistics.jobs.create') }}"
                                                class="text-primary hover:underline ml-1">Create one</a>.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div
                        class="px-4 py-3 border-t border-border flex items-center justify-between
                        text-xs text-muted-foreground bg-muted/10">
                        <span>{{ count($jobs) }} of {{ $total ?? count($jobs) }}</span>
                        <div class="flex gap-2">
                            <button class="kt-btn kt-btn-ghost kt-btn-sm" disabled>
                                <i data-lucide="chevron-left" class="w-3.5 h-3.5 mr-1"></i>Prev
                            </button>
                            <button class="kt-btn kt-btn-ghost kt-btn-sm" disabled>
                                Next<i data-lucide="chevron-right" class="w-3.5 h-3.5 ml-1"></i>
                            </button>
                        </div>
                    </div>
                </div>

               
        @endif

        <div id="toast-container" class="fixed bottom-5 right-5 z-[200] flex flex-col gap-2 pointer-events-none"></div>
        @include('partials._phase4_js')

    </div>
    <script>
        (function() {
            const {
                toast,
                auditEvent,
                fmt
            } = window.CS4;
            const JOBS = @json($jobs->values());

            function renderQV(id) {
                const job = JOBS.find(j => String(j.id) === String(id));
                if (!job) return;
                const qvTitle = document.getElementById('qv-title');
                const qvMeta = document.getElementById('qv-meta');
                const link = document.getElementById('qv-open-link');
                const qvBody = document.getElementById('qv-body');
                if (qvTitle) qvTitle.textContent = job.ref ?? ('JOB-' + job.id);
                if (qvMeta) qvMeta.textContent = job.status + ' · ' + (job.provider ?? 'No provider');
                if (link) {
                    link.href = '/logistics/jobs/' + id;
                    link.classList.remove('hidden');
                }
                if (!qvBody) return;

                qvBody.innerHTML = `
            <div class="space-y-3 text-xs">
                <div class="grid grid-cols-2 gap-2">
                    <div><span class="text-muted-foreground">Deal</span><br><strong class="font-mono">${job.deal_ref ?? '—'}</strong></div>
                    <div><span class="text-muted-foreground">Provider</span><br><strong>${job.provider ?? '—'}</strong></div>
                    <div><span class="text-muted-foreground">Slot</span><br><strong>${job.slot ?? '—'}</strong></div>
                    <div><span class="text-muted-foreground">Status</span><br><strong>${job.status ?? '—'}</strong></div>
                </div>
                <div class="p-3 rounded-lg border border-border bg-muted/10">
                    <div class="font-semibold mb-1 flex items-center gap-1">
                        <i data-lucide="map-pin" class="w-3.5 h-3.5 opacity-60"></i> Route
                    </div>
                    <div>${job.pickup_address ?? '—'}</div>
                    <div class="text-muted-foreground text-xs mt-0.5">↓</div>
                    <div>${job.drop_address ?? '—'}</div>
                </div>
                <div>
                    <div class="font-semibold mb-1">Vehicle</div>
                    <div>${job.vehicle_title ?? '—'}</div>
                    ${job.vrm ? `<span class="font-mono bg-muted px-1.5 py-0.5 rounded mt-1 inline-block">${job.vrm}</span>` : ''}
                </div>
                ${job.tracking_ref ? `<div class="p-2 rounded-lg bg-primary/5 border border-primary/20 text-xs">
                        Tracking: <strong class="font-mono">${job.tracking_ref}</strong></div>` : ''}
            </div>`;

                const footer = document.getElementById('qv-footer');
                const footerActions = document.getElementById('qv-footer-actions');
                if (footer && footerActions) {
                    footer.classList.remove('hidden');
                    footerActions.innerHTML = `
                <a href="/logistics/jobs/${id}#chat" class="kt-btn kt-btn-outline kt-btn-sm">
                    <i data-lucide="message-circle" class="w-3.5 h-3.5 mr-1"></i>Chat
                </a>
                <a href="/logistics/jobs/${id}" class="kt-btn kt-btn-mono kt-btn-sm">Open</a>`;
                }
                if (typeof lucide !== 'undefined') lucide.createIcons();
                window.CS4.openModal('job-preview-modal');
            }

            document.addEventListener('click', e => {
                const btn = e.target.closest('[data-action]');
                if (!btn) return;
                const action = btn.dataset.action,
                    id = btn.dataset.id;
                if (action === 'preview-job') renderQV(id);
                if (action === 'mark-in-transit') {
                    auditEvent('job_in_transit', {
                        id
                    });
                    toast('Job marked in transit.', 'success');
                }
                if (action === 'mark-delivered') {
                    auditEvent('job_delivered', {
                        id
                    });
                    toast('Job marked delivered. Handover checklist unlocked.', 'success');
                }
                if (action === 'message-provider') window.location = '/logistics/jobs/' + id + '#chat';
                if (action === 'upload-proof') toast('Upload proof of collection/delivery from the job detail.',
                    'info');
            });
        })();
    </script>

@endsection
