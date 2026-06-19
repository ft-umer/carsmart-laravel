{{-- resources/views/tasks/index.blade.php --}}
{{-- Phase 5 — T0: Global Tasks Hub --}}
@extends('layouts.app')
@section('title', 'Tasks — Carsmart')

@section('content')

@include('partials._retention_banner')

<div class="kt-container-fixed">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
        <div>
            <h1 class="text-xl font-semibold text-foreground">Tasks</h1>
            <p class="text-sm text-muted-foreground mt-0.5">Cross-module task hub — shared operational heartbeat</p>
        </div>
        <button class="kt-btn kt-btn-mono" id="btn-new-task">
            <i data-lucide="plus" class="w-4 h-4 mr-1"></i> New task
        </button>
    </div>

    {{-- KPI strip --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @foreach([
            ['Due today',    $stats['due_today']    ?? 0, 'calendar',     'destructive'],
            ['Overdue',      $stats['overdue']      ?? 0, 'alert-circle', 'destructive'],
            ['In progress',  $stats['in_progress']  ?? 0, 'loader',       'warning'],
            ['Completed today', $stats['done_today'] ?? 0, 'check-circle','success'],
        ] as [$label, $val, $icon, $colour])
            <div class="kt-card">
                <div class="kt-card-content p-4 flex items-center gap-3">
                    <span class="flex items-center justify-center size-10 rounded-lg bg-{{ $colour }}/10 shrink-0">
                        <i data-lucide="{{ $icon }}" class="w-5 h-5 text-{{ $colour }}"></i>
                    </span>
                    <div>
                        <div class="text-2xl font-bold text-mono">{{ $val }}</div>
                        <div class="text-xs text-muted-foreground">{{ $label }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Tabs: My tasks | Team | Queues --}}
    <div class="flex border-b border-border gap-1 mb-5">
        @foreach([
            ['my',     'My tasks',    $stats['my_count']    ?? 0],
            ['team',   'Team',        $stats['team_count']  ?? 0],
            ['queues', 'Queues',      $stats['queue_count'] ?? 0],
        ] as [$key, $label, $count])
            <button class="tasks-tab-btn px-4 py-2.5 text-sm font-medium border-b-2 transition-colors
                           {{ ($activeTab ?? 'my') === $key
                               ? 'border-primary text-primary'
                               : 'border-transparent text-muted-foreground hover:text-foreground' }}"
                    data-tab="{{ $key }}">
                {{ $label }}
                @if($count > 0)
                    <span class="ml-1.5 kt-badge kt-badge-secondary kt-badge-xs">{{ $count }}</span>
                @endif
            </button>
        @endforeach
    </div>

    {{-- Filters --}}
    <form id="tasks-filter-form" class="card border border-border rounded-lg p-3 mb-5">
        <div class="flex flex-wrap gap-2 items-end">
            <div class="flex-1 min-w-[180px]">
                <label class="block text-xs text-muted-foreground mb-1">Search</label>
                <input type="search" name="search" value="{{ $search ?? '' }}"
                       class="kt-input w-full" placeholder="Task title or object…" id="task-search" />
            </div>
            <div class="min-w-[140px]">
                <label class="block text-xs text-muted-foreground mb-1">Type</label>
                <select name="type" class="kt-input w-full" id="task-type">
                    <option value="">All types</option>
                    @foreach(['Listing','Auction','Lead','Deal','Payout','Dispute','Logistics','Valuation'] as $t)
                        <option value="{{ $t }}" @selected(($type ?? '') === $t)>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[130px]">
                <label class="block text-xs text-muted-foreground mb-1">Priority</label>
                <select name="priority" class="kt-input w-full">
                    <option value="">All priorities</option>
                    <option value="Urgent"  @selected(($priority ?? '') === 'Urgent')>🔴 Urgent</option>
                    <option value="High"    @selected(($priority ?? '') === 'High')>🟠 High</option>
                    <option value="Normal"  @selected(($priority ?? '') === 'Normal')>🟡 Normal</option>
                    <option value="Low"     @selected(($priority ?? '') === 'Low')>🟢 Low</option>
                </select>
            </div>
            <div class="min-w-[130px]">
                <label class="block text-xs text-muted-foreground mb-1">Due</label>
                <select name="due" class="kt-input w-full">
                    <option value="">Any time</option>
                    <option value="today"     @selected(($due ?? '') === 'today')>Today</option>
                    <option value="this_week" @selected(($due ?? '') === 'this_week')>This week</option>
                    <option value="overdue"   @selected(($due ?? '') === 'overdue')>Overdue</option>
                </select>
            </div>
            <div class="min-w-[140px]">
                <label class="block text-xs text-muted-foreground mb-1">Owner</label>
                <select name="owner" class="kt-input w-full">
                    <option value="">All owners</option>
                    @foreach($owners ?? [] as $o)
                        <option value="{{ $o['id'] }}" @selected(($owner ?? '') == $o['id'])>{{ $o['name'] }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="kt-btn kt-btn-mono self-end">Apply</button>
            <a href="{{ route('tasks.index') }}" class="kt-btn kt-btn-ghost self-end">Reset</a>
        </div>
    </form>

    {{-- Tasks table + Quick View --}}
    <div class="grid grid-cols-1 xl:grid-cols-[1fr,380px] gap-5">

        {{-- Table --}}
        <div class="card border border-border rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-sm">
                    <thead class="bg-muted/40 sticky top-0 z-10">
                        <tr>
                            <th class="p-3 w-8">
                                <input type="checkbox" class="kt-checkbox" id="select-all-tasks" />
                            </th>
                            @foreach(['Item','Type','Due','Priority','Owner','Source','Actions'] as $col)
                                <th class="p-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wide {{ $col === 'Actions' ? 'w-44' : '' }}">
                                    {{ $col }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border bg-background" id="tasks-tbody">
                        @forelse($tasks ?? [] as $task)
                            <tr class="hover:bg-muted/30 transition-colors task-row cursor-pointer"
                                data-id="{{ $task['id'] }}"
                                data-type="{{ $task['type'] }}">
                                <td class="p-3">
                                    <input type="checkbox" class="kt-checkbox task-checkbox" value="{{ $task['id'] }}" />
                                </td>
                                <td class="p-3">
                                    <div class="font-medium text-foreground">{{ $task['title'] }}</div>
                                    @if($task['object_ref'] ?? false)
                                        <div class="text-xs text-muted-foreground mt-0.5">{{ $task['object_ref'] }}</div>
                                    @endif
                                </td>
                                <td class="p-3">
                                    <span class="kt-badge kt-badge-outline kt-badge-xs">{{ $task['type'] }}</span>
                                </td>
                                <td class="p-3">
                                    @php
                                        $dueDate = \Carbon\Carbon::parse($task['due_at']);
                                        $isOverdue = $dueDate->isPast() && !($task['completed'] ?? false);
                                        $isDueToday = $dueDate->isToday();
                                    @endphp
                                    <span class="text-xs {{ $isOverdue ? 'text-destructive font-semibold' : ($isDueToday ? 'text-warning font-medium' : 'text-muted-foreground') }}">
                                        @if($isOverdue)
                                            <i data-lucide="alert-circle" class="w-3 h-3 inline mr-0.5"></i>
                                        @elseif($isDueToday)
                                            <i data-lucide="clock" class="w-3 h-3 inline mr-0.5"></i>
                                        @endif
                                        {{ $dueDate->format('d M') }}
                                        @if($isOverdue) (overdue) @elseif($isDueToday) (today) @endif
                                    </span>
                                </td>
                                <td class="p-3">
                                    @php
                                        $prioColour = match($task['priority'] ?? 'Normal') {
                                            'Urgent' => 'destructive',
                                            'High'   => 'warning',
                                            'Normal' => 'secondary',
                                            'Low'    => 'success',
                                            default  => 'secondary',
                                        };
                                        $prioDot = match($task['priority'] ?? 'Normal') {
                                            'Urgent' => '🔴', 'High' => '🟠', 'Normal' => '🟡', 'Low' => '🟢', default => '⚪',
                                        };
                                    @endphp
                                    <span class="kt-badge kt-badge-{{ $prioColour }} kt-badge-xs">
                                        {{ $prioDot }} {{ $task['priority'] ?? 'Normal' }}
                                    </span>
                                </td>
                                <td class="p-3">
                                    <div class="flex items-center gap-1.5">
                                        <span class="flex items-center justify-center size-6 rounded-full bg-primary/10 text-primary text-[10px] font-bold">
                                            {{ strtoupper(substr($task['owner'] ?? '?', 0, 2)) }}
                                        </span>
                                        <span class="text-xs text-foreground">{{ $task['owner'] ?? '—' }}</span>
                                    </div>
                                </td>
                                <td class="p-3">
                                    <span class="text-xs text-muted-foreground">{{ $task['source_module'] ?? '—' }}</span>
                                </td>
                                <td class="p-3">
                                    <div class="flex items-center gap-1">
                                        <a href="{{ route('tasks.show', $task['id']) }}"
                                           class="kt-btn kt-btn-ghost kt-btn-xs" title="Open">
                                           Open
                                        </a>
                                        @unless($task['completed'] ?? false)
                                            <button class="kt-btn kt-btn-ghost kt-btn-xs text-success task-complete-btn"
                                                    data-id="{{ $task['id'] }}" title="Mark complete">
                                                Mark Complete
                                            </button>
                                            <button class="kt-btn kt-btn-ghost kt-btn-xs task-snooze-btn"
                                                    data-id="{{ $task['id'] }}" title="Snooze">
                                                Snooze
                                            </button>
                                        @endunless
                                        <button class="kt-btn kt-btn-ghost kt-btn-xs task-assign-btn"
                                                data-id="{{ $task['id'] }}" title="Assign">
                                           Assign
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-14 text-center text-muted-foreground">
                                    <i data-lucide="check-circle" class="w-10 h-10 mx-auto mb-3 opacity-20"></i>
                                    <p class="text-sm font-medium">No tasks here.</p>
                                    <button class="mt-2 kt-btn kt-btn-outline kt-btn-sm" id="btn-new-task-empty">
                                        Create a task
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Bulk actions bar --}}
            <div id="bulk-bar" class="hidden items-center gap-3 px-4 py-2.5 border-t border-border bg-muted/30">
                <span class="text-xs text-muted-foreground" id="bulk-count">0 selected</span>
                <button class="kt-btn kt-btn-outline kt-btn-xs" id="bulk-complete">Complete</button>
                <button class="kt-btn kt-btn-outline kt-btn-xs" id="bulk-assign">Assign</button>
                <button class="kt-btn kt-btn-ghost kt-btn-xs text-destructive" id="bulk-delete">Delete</button>
            </div>

            @if(isset($tasks) && method_exists($tasks, 'links'))
                <div class="p-4 border-t border-border">{{ $tasks->links() }}</div>
            @endif
        </div>

        {{-- Quick View panel --}}
        <div id="task-qv" class="card border border-border rounded-xl p-5 hidden xl:flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-foreground text-sm">Quick view</h3>
            </div>
            <p class="text-sm text-muted-foreground flex-1">Select a task row to see details here.</p>
        </div>

    </div>
</div>

{{-- New Task Modal --}}
<div id="modal-new-task" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-background rounded-xl shadow-xl w-full max-w-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-foreground">New task</h2>
            <button class="new-task-close text-muted-foreground hover:text-foreground">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="space-y-3">
            <div>
                <label class="block text-xs text-muted-foreground mb-1 font-medium">Title <span class="text-destructive">*</span></label>
                <input type="text" class="kt-input w-full" placeholder="What needs to be done?" />
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">Type</label>
                    <select class="kt-input w-full">
                        @foreach(['Listing','Auction','Lead','Deal','Payout','Dispute','Logistics','Valuation','General'] as $t)
                            <option>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">Priority</label>
                    <select class="kt-input w-full">
                        <option value="Normal" selected>🟡 Normal</option>
                        <option value="High">🟠 High</option>
                        <option value="Urgent">🔴 Urgent</option>
                        <option value="Low">🟢 Low</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">Due date</label>
                    <input type="datetime-local" class="kt-input w-full" />
                </div>
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">Assign to</label>
                    <select class="kt-input w-full">
                        <option value="">Unassigned</option>
                        @foreach($owners ?? [] as $o)
                            <option value="{{ $o['id'] }}">{{ $o['name'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs text-muted-foreground mb-1 font-medium">Linked object (optional)</label>
                <input type="text" class="kt-input w-full" placeholder="Listing ID, auction ID, deal ID…" />
            </div>
            <div>
                <label class="block text-xs text-muted-foreground mb-1 font-medium">Notes</label>
                <textarea class="kt-input w-full" rows="3" placeholder="Additional context…"></textarea>
            </div>
        </div>
        <div class="flex justify-end gap-2 mt-5">
            <button class="new-task-close kt-btn kt-btn-ghost">Cancel</button>
            <button class="kt-btn kt-btn-mono">Create task</button>
        </div>
    </div>
</div>

{{-- Snooze Modal --}}
<div id="modal-snooze" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-background rounded-xl shadow-xl w-full max-w-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-foreground">Snooze task</h2>
            <button class="snooze-close text-muted-foreground hover:text-foreground">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="space-y-2">
            @foreach([
                ['1h',    '1 hour'],
                ['3h',    '3 hours'],
                ['tomorrow', 'Tomorrow morning'],
                ['next_week','Next Monday'],
                ['custom',   'Custom date/time…'],
            ] as [$val, $label])
                <label class="flex items-center gap-2 p-2.5 rounded-lg border border-border hover:bg-muted/30 cursor-pointer">
                    <input type="radio" name="snooze_until" value="{{ $val }}" class="kt-radio" />
                    <span class="text-sm text-foreground">{{ $label }}</span>
                </label>
            @endforeach
        </div>
        <div id="snooze-custom" class="mt-3 hidden">
            <input type="datetime-local" class="kt-input w-full" />
        </div>
        <div class="flex justify-end gap-2 mt-4">
            <button class="snooze-close kt-btn kt-btn-ghost">Cancel</button>
            <button class="kt-btn kt-btn-mono">Snooze</button>
        </div>
    </div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    // ── Tab switching (fixed safe toggle) ────────────────────────────────
    document.querySelectorAll('.tasks-tab-btn').forEach(btn => {
        btn.addEventListener('click', function () {

            document.querySelectorAll('.tasks-tab-btn').forEach(b => {
                b.classList.remove('border-primary', 'text-primary');
                b.classList.add('border-transparent', 'text-muted-foreground');
            });

            this.classList.remove('border-transparent', 'text-muted-foreground');
            this.classList.add('border-primary', 'text-primary');

            // future: AJAX / query param
        });
    });

    // ── Modal helpers ────────────────────────────────────────────────────
    const modalNewTask = document.getElementById('modal-new-task');
    const modalSnooze  = document.getElementById('modal-snooze');

    function openModal(modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal(modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // ── New Task Modal ────────────────────────────────────────────────────
    function openNewTask() {
        openModal(modalNewTask);
    }

    document.getElementById('btn-new-task')?.addEventListener('click', openNewTask);
    document.getElementById('btn-new-task-empty')?.addEventListener('click', openNewTask);

    document.querySelectorAll('.new-task-close').forEach(btn => {
        btn.addEventListener('click', () => closeModal(modalNewTask));
    });

    // ── Quick View (safer extraction) ─────────────────────────────────────
    const qv = document.getElementById('task-qv');

    document.querySelectorAll('.task-row').forEach(row => {
        row.addEventListener('click', function (e) {
            if (e.target.closest('a,button,input')) return;

            const cells = this.querySelectorAll('td');

            const id    = this.dataset.id;
            const title = this.querySelector('.font-medium')?.innerText || '—';
            const type  = this.querySelector('.kt-badge')?.innerText || '—';
            const due   = cells[3]?.innerText?.trim() || '—';
            const prio  = cells[4]?.innerText?.trim() || '—';
            const owner = cells[5]?.innerText?.trim() || '—';
            const src   = cells[6]?.innerText?.trim() || '—';

            qv.innerHTML = `
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-foreground text-sm">Task #${id}</h3>
                    <span class="kt-badge kt-badge-outline kt-badge-xs">${type}</span>
                </div>

                <div class="space-y-3 flex-1">
                    <p class="text-sm font-medium text-foreground">${title}</p>

                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="bg-muted/40 rounded-lg p-2">
                            <div class="text-muted-foreground mb-0.5">Due</div>
                            <div class="font-medium">${due}</div>
                        </div>

                        <div class="bg-muted/40 rounded-lg p-2">
                            <div class="text-muted-foreground mb-0.5">Priority</div>
                            <div class="font-medium">${prio}</div>
                        </div>

                        <div class="bg-muted/40 rounded-lg p-2">
                            <div class="text-muted-foreground mb-0.5">Owner</div>
                            <div class="font-medium">${owner}</div>
                        </div>

                        <div class="bg-muted/40 rounded-lg p-2">
                            <div class="text-muted-foreground mb-0.5">Source</div>
                            <div class="font-medium">${src}</div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-2 mt-4">
                    <a href="/tasks/${id}" class="kt-btn kt-btn-mono kt-btn-sm w-full justify-center">Open task</a>
                    <div class="flex gap-2">
                        <button class="kt-btn kt-btn-outline kt-btn-sm flex-1">Complete</button>
                        <button class="kt-btn kt-btn-outline kt-btn-sm flex-1">Assign</button>
                    </div>
                </div>
            `;
        });
    });

    // ── Snooze Modal ─────────────────────────────────────────────────────
    document.querySelectorAll('.task-snooze-btn').forEach(btn => {
        btn.addEventListener('click', e => {
            e.stopPropagation();
            openModal(modalSnooze);
        });
    });

    document.querySelectorAll('.snooze-close').forEach(btn => {
        btn.addEventListener('click', () => closeModal(modalSnooze));
    });

    document.querySelectorAll('[name="snooze_until"]').forEach(radio => {
        radio.addEventListener('change', () => {
            const custom = document.getElementById('snooze-custom');
            if (!custom) return;
            custom.classList.toggle('hidden', radio.value !== 'custom');
        });
    });

    // ── Complete task ────────────────────────────────────────────────────
    document.querySelectorAll('.task-complete-btn').forEach(btn => {
        btn.addEventListener('click', e => {
            e.stopPropagation();

            const row = btn.closest('tr');
            if (!row) return;

            row.classList.add('opacity-40');

            const title = row.querySelector('.font-medium');
            if (title) title.classList.add('line-through');

            btn.disabled = true;
        });
    });

    // ── Bulk selection (safe guards added) ───────────────────────────────
    const bulkBar   = document.getElementById('bulk-bar');
    const bulkCount = document.getElementById('bulk-count');

    const selectAll = document.getElementById('select-all-tasks');

    function updateBulkBar() {
        const selected = document.querySelectorAll('.task-checkbox:checked').length;

        if (!bulkBar || !bulkCount) return;

        if (selected > 0) {
            bulkBar.classList.remove('hidden');
            bulkBar.classList.add('flex');
            bulkCount.textContent = `${selected} selected`;
        } else {
            bulkBar.classList.add('hidden');
            bulkBar.classList.remove('flex');
        }
    }

    selectAll?.addEventListener('change', function () {
        document.querySelectorAll('.task-checkbox').forEach(cb => {
            cb.checked = this.checked;
        });
        updateBulkBar();
    });

    document.querySelectorAll('.task-checkbox').forEach(cb => {
        cb.addEventListener('change', updateBulkBar);
    });

});
</script>
@endpush
@endsection
