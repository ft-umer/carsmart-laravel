{{-- resources/views/automations/edit.blade.php --}}
{{-- Phase 5 — A1: Journey Builder (visual) --}}
@extends('layouts.app')
@section('title', ($journey['name'] ?? 'New Journey') . ' — Journey Builder')

@section('content')

@include('partials._retention_banner')

<div class="flex flex-col h-full">

    {{-- Builder Toolbar --}}
    <div class="flex items-center justify-between gap-3 px-6 py-3 border-b border-border bg-background sticky top-0 z-20">
        <div class="flex items-center gap-3">
            <a href="{{ route('automations.index') }}" class="text-muted-foreground hover:text-foreground">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
            </a>
            <input type="text" class="kt-input text-sm font-semibold bg-transparent border-transparent hover:border-border focus:border-border w-48"
                   value="{{ $journey['name'] ?? 'Untitled journey' }}" id="journey-name" />
            <span class="kt-badge kt-badge-{{ match($journey['status'] ?? 'Draft') {
                'Active' => 'success', 'Paused' => 'warning', 'Draft' => 'secondary', default => 'secondary'
            } }} kt-badge-sm">{{ $journey['status'] ?? 'Draft' }}</span>
        </div>
        <div class="flex gap-2">
            <button class="kt-btn kt-btn-ghost kt-btn-sm">
                <i data-lucide="settings" class="w-4 h-4 mr-1"></i> Properties
            </button>
            <button class="kt-btn kt-btn-outline kt-btn-sm" id="btn-test-journey">
                <i data-lucide="play" class="w-4 h-4 mr-1"></i> Test
            </button>
            <button class="kt-btn kt-btn-mono kt-btn-sm" id="btn-publish-journey">
                Publish journey
            </button>
        </div>
    </div>

    <div class="flex flex-1 overflow-hidden">

        {{-- ── Step Palette (left) ── --}}
        <div class="w-56 border-r border-border bg-muted/20 p-3 overflow-y-auto shrink-0">
            <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wide mb-3">Add step</p>
            <div class="space-y-1.5">
                @foreach([
                    ['trigger',   'Trigger',        'zap',         'primary'],
                    ['message',   'Send message',   'send',        'info'],
                    ['wait',      'Wait',            'clock',       'warning'],
                    ['condition', 'Condition',       'git-branch',  'success'],
                    ['task',      'Create task',     'check-square','secondary'],
                    ['failover',  'Failover',        'shuffle',     'destructive'],
                ] as [$type, $label, $icon, $colour])
                    <div class="flex items-center gap-2 p-2.5 rounded-lg bg-background border border-border
                                cursor-grab hover:border-primary/40 hover:bg-primary/5 transition-colors
                                step-palette-item" data-type="{{ $type }}">
                        <span class="flex items-center justify-center size-7 rounded-md bg-{{ $colour }}/10 shrink-0">
                            <i data-lucide="{{ $icon }}" class="w-3.5 h-3.5 text-{{ $colour }}"></i>
                        </span>
                        <span class="text-xs font-medium text-foreground">{{ $label }}</span>
                    </div>
                @endforeach
            </div>

            <div class="border-t border-border mt-4 pt-4">
                <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wide mb-3">Triggers</p>
                <div class="space-y-1 text-xs text-muted-foreground">
                    @foreach([
                        'missing_photos','missing_docs','pricing_not_set',
                        'kyc_pending','reserve_not_set',
                        'valuation_fetched','valuation_applied','valuation_failed',
                        'auction_published','auction_starts','auction_closing',
                        'outbid','reserve_met','auction_ended',
                        'deal_created','payout_requested',
                    ] as $trig)
                        <div class="px-2 py-1 rounded hover:bg-accent cursor-pointer trigger-chip" data-trigger="{{ $trig }}">
                            {{ str_replace('_', ' ', $trig) }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ── Canvas ── --}}
        <div class="flex-1 overflow-auto bg-[radial-gradient(circle,_theme(colors.border)_1px,_transparent_1px)] bg-[length:20px_20px] relative"
             id="journey-canvas">

            <div class="min-h-full min-w-full p-10" id="canvas-nodes">

                {{-- Default empty canvas or loaded steps --}}
                @if(empty($journey['steps']))
                    <div class="flex flex-col items-center gap-4 pt-10" id="canvas-empty">
                        <div class="step-node border-2 border-dashed border-primary/40 rounded-xl p-4 w-64 text-center bg-background/80">
                            <i data-lucide="zap" class="w-8 h-8 mx-auto mb-2 text-primary/40"></i>
                            <p class="text-sm text-muted-foreground">Drop a <strong class="text-foreground">Trigger</strong> here to start</p>
                        </div>
                    </div>
                @else
                    <div class="flex flex-col items-center gap-0" id="canvas-steps">
                        @foreach($journey['steps'] as $step)
                            @include('automations.partials._step_node', ['step' => $step])
                        @endforeach
                    </div>
                @endif

            </div>
        </div>

        {{-- ── Properties Panel (right) ── --}}
        <div id="props-panel" class="w-72 border-l border-border bg-background overflow-y-auto shrink-0 hidden">
            <div class="flex items-center justify-between p-4 border-b border-border">
                <h3 class="text-sm font-semibold text-foreground" id="props-title">Step properties</h3>
                <button id="props-close" class="text-muted-foreground hover:text-foreground">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <div class="p-4 space-y-4" id="props-content">
                <p class="text-sm text-muted-foreground">Select a step to edit its properties.</p>
            </div>
        </div>

    </div>
</div>

{{-- Journey Properties Modal --}}
<div id="modal-journey-props" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-background rounded-xl shadow-xl w-full max-w-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-foreground">Journey Properties</h2>
            <button class="journey-props-close text-muted-foreground hover:text-foreground">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">Quiet hours start</label>
                    <input type="time" class="kt-input w-full" value="21:00" />
                </div>
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">Quiet hours end</label>
                    <input type="time" class="kt-input w-full" value="08:00" />
                </div>
            </div>
            <div>
                <label class="block text-xs text-muted-foreground mb-1 font-medium">Max messages per recipient per day</label>
                <input type="number" class="kt-input w-full" value="3" min="1" max="20" />
            </div>
            <div>
                <label class="block text-xs text-muted-foreground mb-1 font-medium">Start date</label>
                <input type="date" class="kt-input w-full" />
            </div>
            <div>
                <label class="block text-xs text-muted-foreground mb-1 font-medium">End date (optional)</label>
                <input type="date" class="kt-input w-full" />
            </div>
            <div>
                <label class="block text-xs text-muted-foreground mb-1 font-medium">Audience exclusions</label>
                <div class="flex gap-3 flex-wrap">
                    @foreach(['DNC list','Channel consent No','Suppression list'] as $excl)
                        <label class="flex items-center gap-1.5 text-sm cursor-pointer">
                            <input type="checkbox" class="kt-checkbox" checked /> {{ $excl }}
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="flex justify-end gap-2 mt-5">
            <button class="journey-props-close kt-btn kt-btn-ghost">Cancel</button>
            <button class="kt-btn kt-btn-mono">Save properties</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Properties panel
document.getElementById('props-close')?.addEventListener('click', () => {
    document.getElementById('props-panel').classList.add('hidden');
});

// Journey props button
document.querySelector('[data-lucide="settings"]')?.closest('button')?.addEventListener('click', () => {
    document.getElementById('modal-journey-props').classList.remove('hidden');
    document.getElementById('modal-journey-props').classList.add('flex');
});
document.querySelectorAll('.journey-props-close').forEach(b => b.addEventListener('click', () => {
    document.getElementById('modal-journey-props').classList.add('hidden');
    document.getElementById('modal-journey-props').classList.remove('flex');
}));

// Dragging step palette items onto canvas
const paletteItems = document.querySelectorAll('.step-palette-item');
const canvas = document.getElementById('canvas-nodes');
const canvasSteps = document.getElementById('canvas-steps') || document.createElement('div');

paletteItems.forEach(item => {
    item.addEventListener('click', () => {
        const type = item.dataset.type;
        addStepNode(type);
    });
});

function addStepNode(type) {
    const empty = document.getElementById('canvas-empty');
    if(empty) empty.remove();

    // Ensure canvas-steps div exists
    let stepsDiv = document.getElementById('canvas-steps');
    if(!stepsDiv) {
        stepsDiv = document.createElement('div');
        stepsDiv.id = 'canvas-steps';
        stepsDiv.className = 'flex flex-col items-center gap-0';
        canvas.appendChild(stepsDiv);
    }

    const id = 'step-' + Date.now();
    const colors = { trigger: 'primary', message: 'info', wait: 'warning',
                     condition: 'success', task: 'secondary', failover: 'destructive' };
    const icons  = { trigger: 'zap', message: 'send', wait: 'clock',
                     condition: 'git-branch', task: 'check-square', failover: 'shuffle' };
    const labels = { trigger: 'Trigger', message: 'Send message', wait: 'Wait',
                     condition: 'Condition', task: 'Create task', failover: 'Failover' };

    const colour = colors[type] || 'secondary';
    const icon   = icons[type] || 'circle';
    const label  = labels[type] || type;

    stepsDiv.innerHTML += `
        <div class="flex flex-col items-center">
            ${stepsDiv.children.length > 0 ? '<div class="w-px h-8 bg-border"></div>' : ''}
            <div id="${id}" class="step-node relative w-64 rounded-xl border border-border bg-background shadow-sm p-4 cursor-pointer hover:border-${colour}/50 transition-colors"
                 onclick="openStepProps('${type}','${id}')">
                <div class="flex items-center gap-3">
                    <span class="flex items-center justify-center size-8 rounded-lg bg-${colour}/10 shrink-0">
                        <i data-lucide="${icon}" class="w-4 h-4 text-${colour}"></i>
                    </span>
                    <div>
                        <div class="text-xs font-semibold text-foreground">${label}</div>
                        <div class="text-xs text-muted-foreground">Click to configure</div>
                    </div>
                </div>
                <button class="absolute top-1 right-1 p-1 text-muted-foreground hover:text-destructive" onclick="event.stopPropagation(); this.closest('.step-node').parentElement.remove()">
                    <i data-lucide="x" class="w-3 h-3"></i>
                </button>
            </div>
        </div>`;
    lucide.createIcons();
}

function openStepProps(type, id) {
    const panel = document.getElementById('props-panel');
    const title = document.getElementById('props-title');
    const content = document.getElementById('props-content');
    panel.classList.remove('hidden');

    const configs = {
        trigger: `
            <div><label class="block text-xs text-muted-foreground mb-1">Event</label>
            <select class="kt-input w-full text-xs">
                <optgroup label="Valuations">
                    <option>valuation_fetched</option>
                    <option>valuation_applied</option>
                    <option>valuation_failed</option>
                </optgroup>
                <optgroup label="Listings">
                    <option>missing_photos</option>
                    <option>missing_docs</option>
                    <option>pricing_not_set</option>
                    <option>reserve_not_set</option>
                </optgroup>
                <optgroup label="KYC">
                    <option>kyc_pending</option>
                </optgroup>
                <optgroup label="Auctions">
                    <option>auction_published</option>
                    <option>auction_starts</option>
                    <option>auction_closing</option>
                    <option>outbid</option>
                    <option>reserve_met</option>
                    <option>auction_ended</option>
                </optgroup>
                <optgroup label="Deals">
                    <option>deal_created</option>
                    <option>payout_requested</option>
                </optgroup>
            </select></div>`,
        message: `
            <div><label class="block text-xs text-muted-foreground mb-1">Channel</label>
            <select class="kt-input w-full text-xs"><option>Email</option><option>SMS</option><option>WhatsApp</option></select></div>
            <div><label class="block text-xs text-muted-foreground mb-1">Template</label>
            <select class="kt-input w-full text-xs"><option>Select template…</option></select></div>
            <div><label class="block text-xs text-muted-foreground mb-1">Failover channel</label>
            <select class="kt-input w-full text-xs"><option>None</option><option>Email</option><option>SMS</option></select></div>`,
        wait: `
            <div><label class="block text-xs text-muted-foreground mb-1">Duration</label>
            <div class="flex gap-2"><input type="number" class="kt-input w-20" value="24" />
            <select class="kt-input flex-1 text-xs"><option>Hours</option><option>Days</option><option>Minutes</option></select></div></div>`,
        condition: `
            <div><label class="block text-xs text-muted-foreground mb-1">Field</label>
            <input type="text" class="kt-input w-full text-xs" placeholder="e.g. delta_pennies" /></div>
            <div><label class="block text-xs text-muted-foreground mb-1">Operator</label>
            <select class="kt-input w-full text-xs"><option>&gt;</option><option>&lt;</option><option>=</option><option>is set</option></select></div>
            <div><label class="block text-xs text-muted-foreground mb-1">Value</label>
            <input type="text" class="kt-input w-full text-xs" placeholder="e.g. 50000" /></div>`,
        task: `
            <div><label class="block text-xs text-muted-foreground mb-1">Task title</label>
            <input type="text" class="kt-input w-full text-xs" placeholder="Review valuation delta…" /></div>
            <div><label class="block text-xs text-muted-foreground mb-1">Assign to</label>
            <select class="kt-input w-full text-xs"><option>Journey owner</option><option>Listing owner</option></select></div>
            <div><label class="block text-xs text-muted-foreground mb-1">Priority</label>
            <select class="kt-input w-full text-xs"><option>Normal</option><option>High</option><option>Urgent</option></select></div>`,
        failover: `
            <div><label class="block text-xs text-muted-foreground mb-1">Fallback channel</label>
            <select class="kt-input w-full text-xs"><option>Email</option><option>SMS</option><option>WhatsApp</option></select></div>`,
    };

    title.textContent = { trigger:'Trigger config', message:'Message step', wait:'Wait step',
                           condition:'Condition', task:'Create task', failover:'Failover' }[type] || 'Step config';
    content.innerHTML = (configs[type] || '<p class="text-sm text-muted-foreground">No properties.</p>') +
        `<div class="pt-3"><button class="kt-btn kt-btn-mono kt-btn-sm w-full">Apply</button></div>`;
    lucide.createIcons();
}
</script>
@endpush

@endsection
