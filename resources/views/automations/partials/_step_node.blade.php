{{-- resources/views/automations/partials/_step_node.blade.php --}}
{{-- Renders a single step node inside the Journey Builder canvas --}}
@php
    $typeConfig = [
        'trigger'   => ['icon' => 'zap',          'colour' => 'primary',     'label' => 'Trigger'],
        'message'   => ['icon' => 'send',          'colour' => 'info',        'label' => 'Send message'],
        'wait'      => ['icon' => 'clock',         'colour' => 'warning',     'label' => 'Wait'],
        'condition' => ['icon' => 'git-branch',    'colour' => 'success',     'label' => 'Condition'],
        'task'      => ['icon' => 'check-square',  'colour' => 'secondary',   'label' => 'Create task'],
        'failover'  => ['icon' => 'shuffle',       'colour' => 'destructive', 'label' => 'Failover'],
    ];
    $cfg    = $typeConfig[$step['type'] ?? 'message'] ?? $typeConfig['message'];
    $colour = $cfg['colour'];
    $icon   = $cfg['icon'];
    $label  = $cfg['label'];
@endphp

<div class="flex flex-col items-center">
    {{-- Connector line from previous node --}}
    @unless($loop->first ?? false)
        <div class="flex flex-col items-center">
            <div class="w-px h-6 bg-border"></div>
            <div class="size-2 rounded-full bg-border"></div>
            <div class="w-px h-6 bg-border"></div>
        </div>
    @endunless

    {{-- Node card --}}
    <div id="step-node-{{ $step['id'] ?? uniqid() }}"
         class="relative w-72 rounded-xl border border-border bg-background shadow-sm p-4
                hover:border-{{ $colour }}/50 hover:shadow-md transition-all cursor-pointer step-canvas-node"
         data-step-id="{{ $step['id'] ?? '' }}"
         data-step-type="{{ $step['type'] ?? 'message' }}"
         onclick="openStepProps('{{ $step['type'] ?? 'message' }}', 'step-node-{{ $step['id'] ?? '' }}')">

        <div class="flex items-center gap-3">
            <span class="flex items-center justify-center size-9 rounded-lg bg-{{ $colour }}/10 shrink-0">
                <i data-lucide="{{ $icon }}" class="w-4 h-4 text-{{ $colour }}"></i>
            </span>
            <div class="flex-1 min-w-0">
                <div class="text-xs font-semibold text-foreground">{{ $label }}</div>
                <div class="text-xs text-muted-foreground truncate mt-0.5">
                    @if($step['type'] === 'trigger')
                        {{ $step['config']['event'] ?? 'Select event…' }}
                    @elseif($step['type'] === 'message')
                        {{ $step['config']['channel'] ?? 'Email' }} — {{ $step['config']['template'] ?? 'Select template…' }}
                    @elseif($step['type'] === 'wait')
                        Wait {{ $step['config']['duration'] ?? '24' }}{{ $step['config']['unit'] ?? 'h' }}
                    @elseif($step['type'] === 'condition')
                        If {{ $step['config']['field'] ?? 'field' }} {{ $step['config']['operator'] ?? '>' }} {{ $step['config']['value'] ?? '…' }}
                    @elseif($step['type'] === 'task')
                        {{ $step['config']['title'] ?? 'Task title…' }}
                    @elseif($step['type'] === 'failover')
                        Failover → {{ $step['config']['fallback_channel'] ?? 'Email' }}
                    @endif
                </div>
            </div>
        </div>

        {{-- Status dot --}}
        @if($step['status'] ?? false)
            <span class="absolute top-2 right-8 size-2 rounded-full
                         {{ match($step['status']) {
                             'ok'      => 'bg-success',
                             'error'   => 'bg-destructive',
                             'skipped' => 'bg-warning',
                             default   => 'bg-muted-foreground',
                         } }}"></span>
        @endif

        {{-- Delete handle --}}
        <button type="button"
                class="absolute top-2 right-2 p-0.5 text-muted-foreground hover:text-destructive opacity-0
                       group-hover:opacity-100 transition-opacity step-delete-btn"
                data-step-id="{{ $step['id'] ?? '' }}"
                onclick="event.stopPropagation(); removeStepNode(this)">
            <i data-lucide="x" class="w-3.5 h-3.5"></i>
        </button>
    </div>

    {{-- Branch labels for condition nodes --}}
    @if(($step['type'] ?? '') === 'condition')
        <div class="flex gap-8 mt-1">
            <div class="flex flex-col items-center">
                <div class="w-px h-4 bg-success/50"></div>
                <span class="text-xs text-success font-medium px-2 py-0.5 rounded bg-success/10">Yes</span>
            </div>
            <div class="flex flex-col items-center">
                <div class="w-px h-4 bg-destructive/50"></div>
                <span class="text-xs text-destructive font-medium px-2 py-0.5 rounded bg-destructive/10">No</span>
            </div>
        </div>
    @endif
</div>
