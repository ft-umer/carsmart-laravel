{{-- resources/views/automations/index.blade.php --}}
{{-- Phase 5 — A0: Automations → Overview --}}
@extends('layouts.app')
@section('title', 'Automations — Carsmart')

@section('content')

@include('partials._retention_banner')

<div class="kt-container-fixed">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
        <div>
            <h1 class="text-xl font-semibold text-foreground">Automations</h1>
            <p class="text-sm text-muted-foreground mt-0.5">Build, schedule, and monitor automated journeys</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('automations.create') }}" class="kt-btn kt-btn-mono">
                <i data-lucide="plus" class="w-4 h-4 mr-1"></i> Create journey
            </a>
        </div>
    </div>

    {{-- Summary KPIs --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @foreach([
            ['Active journeys',  $stats['active'] ?? 0,  'zap',         'primary'],
            ['Paused',           $stats['paused'] ?? 0,  'pause-circle','warning'],
            ['Runs today',       $stats['runs_today'] ?? 0, 'activity',  'success'],
            ['Failures today',   $stats['failures'] ?? 0, 'x-circle',   'destructive'],
        ] as [$label, $val, $icon, $colour])
            <div class="kt-card">
                <div class="kt-card-content p-4 flex items-center gap-3">
                    <span class="flex items-center justify-center size-10 rounded-lg bg-{{ $colour }}/10 shrink-0">
                        <i data-lucide="{{ $icon }}" class="w-5 h-5 text-{{ $colour }}"></i>
                    </span>
                    <div>
                        <div class="text-xl font-bold text-mono">{{ $val }}</div>
                        <div class="text-xs text-muted-foreground">{{ $label }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ── Tabs ── --}}
    <div class="flex border-b border-border gap-1 mb-5">
        @foreach([
            ['journeys',     'Journeys',     route('automations.index')],
            ['triggers',     'Triggers',     route('automations.triggers')],
            ['templates',    'Templates',    route('automations.templates')],
            ['runs',         'Runs',         route('automations.runs')],
            ['suppressions', 'Suppressions', route('automations.suppressions')],
        ] as [$key, $label, $href])
            <a href="{{ $href }}"
               class="px-4 py-2.5 text-sm font-medium border-b-2 transition-colors
                      {{ ($activeTab ?? 'journeys') === $key
                          ? 'border-primary text-primary'
                          : 'border-transparent text-muted-foreground hover:text-foreground' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- Journeys table --}}
    <div class="grid grid-cols-1 xl:grid-cols-[1fr,380px] gap-5">
        <div class="card border border-border rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-sm">
                    <thead class="bg-muted/40">
                        <tr>
                            @foreach(['Name','Trigger','Channel(s)','Cadence','Status','Last run','Owner','Actions'] as $col)
                                <th class="p-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wide">{{ $col }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border bg-background">
                        @forelse($journeys ?? [] as $j)
                            <tr class="hover:bg-muted/30 transition-colors cursor-pointer auto-row" data-id="{{ $j['id'] }}">
                                <td class="p-3 font-medium text-foreground">{{ $j['name'] }}</td>
                                <td class="p-3 text-xs">
                                    <span class="kt-badge kt-badge-outline kt-badge-sm">{{ $j['trigger'] }}</span>
                                </td>
                                <td class="p-3">
                                    <div class="flex gap-1 flex-wrap">
                                        @foreach($j['channels'] ?? [] as $ch)
                                            <span class="kt-badge kt-badge-info kt-badge-xs">{{ $ch }}</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="p-3 text-xs text-muted-foreground">{{ $j['cadence'] ?? '—' }}</td>
                                <td class="p-3">
                                    <span class="kt-badge kt-badge-{{ match($j['status'] ?? 'Draft') {
                                        'Active' => 'success', 'Paused' => 'warning',
                                        'Draft'  => 'secondary', 'Archived' => 'destructive', default => 'secondary'
                                    } }} kt-badge-sm">{{ $j['status'] }}</span>
                                </td>
                                <td class="p-3 text-xs text-muted-foreground">
                                    {{ isset($j['last_run']) ? \Carbon\Carbon::parse($j['last_run'])->diffForHumans() : 'Never' }}
                                </td>
                                <td class="p-3 text-sm">{{ $j['owner'] }}</td>
                                <td class="p-3">
                                    <div class="flex gap-1">
                                        <a href="{{ route('automations.edit', $j['id']) }}" class="kt-btn kt-btn-ghost kt-btn-xs" title="Open">
                                            <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                        </a>
                                        <button class="kt-btn kt-btn-ghost kt-btn-xs auto-toggle" data-id="{{ $j['id'] }}"
                                                title="{{ $j['status'] === 'Active' ? 'Pause' : 'Resume' }}">
                                            <i data-lucide="{{ $j['status'] === 'Active' ? 'pause' : 'play' }}" class="w-3.5 h-3.5"></i>
                                        </button>
                                        <button class="kt-btn kt-btn-ghost kt-btn-xs auto-duplicate" data-id="{{ $j['id'] }}" title="Duplicate">
                                            <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-10 text-center text-muted-foreground">
                                    No journeys yet. <a href="{{ route('automations.create') }}" class="text-primary underline">Create the first one</a>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Quick View --}}
        <div id="auto-qv" class="card border border-border rounded-xl p-5 hidden xl:block">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-foreground text-sm">Quick view</h3>
            </div>
            <p class="text-sm text-muted-foreground">Select a journey to see details.</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
const autoQv = document.getElementById('auto-qv');
document.querySelectorAll('.auto-row').forEach(row => {
    row.addEventListener('click', function(e) {
        if(e.target.closest('a,button')) return;
        const id = this.dataset.id;
        const name = this.querySelector('td:first-child')?.innerText;
        const status = this.querySelector('.kt-badge')?.innerText;
        autoQv.innerHTML = `
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-foreground text-sm">Journey #${id}</h3>
            </div>
            <div class="space-y-3">
                <div class="text-sm font-medium text-foreground">${name}</div>
                <div class="text-xs text-muted-foreground">Status: ${status}</div>
                <a href="/automations/${id}/edit" class="kt-btn kt-btn-mono kt-btn-sm w-full justify-center block text-center mt-3">Open builder</a>
                <a href="/automations/${id}/runs" class="kt-btn kt-btn-outline kt-btn-sm w-full justify-center block text-center">View runs</a>
            </div>`;
    });
});
</script>
@endpush

@endsection
