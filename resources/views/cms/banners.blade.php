{{-- resources/views/cms/banners.blade.php --}}
{{-- Phase 5 — CMS2: Banners & Features (incl. Editions) --}}
@extends('layouts.app')
@section('title', 'Banners & Features — CMS')

@section('content')

@include('partials._retention_banner')

<div class="kt-container-fixed">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
        <div>
            <h1 class="text-xl font-semibold text-foreground">Banners & Features</h1>
            <p class="text-sm text-muted-foreground mt-0.5">Schedule hero banners, homepage slots, and Editions features</p>
        </div>
        <div class="flex gap-2">
            <button id="btn-add-feature" class="kt-btn kt-btn-mono">
                <i data-lucide="plus" class="w-4 h-4 mr-1"></i> Add feature
            </button>
        </div>
    </div>

    {{-- View toggle: Calendar / List --}}
    <div class="flex gap-2 mb-5">
        <button id="view-calendar" class="kt-btn kt-btn-mono kt-btn-sm">
            <i data-lucide="calendar" class="w-4 h-4 mr-1"></i> Calendar
        </button>
        <button id="view-list" class="kt-btn kt-btn-outline kt-btn-sm">
            <i data-lucide="list" class="w-4 h-4 mr-1"></i> List
        </button>
    </div>

    {{-- ── Calendar view ── --}}
    <div id="pane-calendar" class="card border border-border rounded-xl overflow-hidden mb-5">
        <div class="flex items-center justify-between p-4 border-b border-border">
            <button id="cal-prev" class="kt-btn kt-btn-ghost kt-btn-sm">
                <i data-lucide="chevron-left" class="w-4 h-4"></i>
            </button>
            <h3 id="cal-heading" class="text-sm font-semibold text-foreground">
                {{ now()->format('F Y') }}
            </h3>
            <button id="cal-next" class="kt-btn kt-btn-ghost kt-btn-sm">
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </button>
        </div>
        <div class="grid grid-cols-7 text-xs font-semibold text-muted-foreground uppercase tracking-wide p-2">
            @foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $day)
                <div class="p-2 text-center">{{ $day }}</div>
            @endforeach
        </div>
        <div id="cal-grid" class="grid grid-cols-7 gap-px bg-border">
            @php
                $start = now()->startOfMonth()->startOfWeek(\Carbon\Carbon::MONDAY);
                $end   = now()->endOfMonth()->endOfWeek(\Carbon\Carbon::SUNDAY);
                $cur   = $start->copy();
            @endphp
            @while($cur <= $end)
                @php $isToday = $cur->isToday(); $isMonth = $cur->month === now()->month; @endphp
                <div class="bg-background min-h-[80px] p-1.5 {{ $isToday ? 'ring-2 ring-inset ring-primary/30' : '' }}">
                    <div class="text-xs {{ $isToday ? 'font-bold text-primary' : ($isMonth ? 'text-foreground' : 'text-muted-foreground/40') }} mb-1">
                        {{ $cur->format('j') }}
                    </div>
                    {{-- feature dots from $features array if available --}}
                    @foreach(collect($features ?? [])->where('date', $cur->format('Y-m-d')) as $feat)
                        <div class="text-[10px] bg-primary/10 text-primary rounded px-1 py-0.5 mb-0.5 truncate cursor-pointer hover:bg-primary/20 feature-item" data-id="{{ $feat['id'] }}">
                            {{ $feat['title'] }}
                        </div>
                    @endforeach
                </div>
                @php $cur->addDay(); @endphp
            @endwhile
        </div>
    </div>

    {{-- ── List view ── --}}
    <div id="pane-list" class="hidden">
        <div class="card border border-border rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-sm">
                    <thead class="bg-muted/40">
                        <tr>
                            @foreach(['Slot','Date/Time','Content','Channels','Status','Actions'] as $col)
                                <th class="p-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wide">
                                    {{ $col }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border bg-background">
                        @forelse($features ?? [] as $feat)
                            <tr class="hover:bg-muted/30 transition-colors">
                                <td class="p-3 font-medium text-foreground">{{ $feat['slot'] ?? 'Hero' }}</td>
                                <td class="p-3 text-sm">{{ \Carbon\Carbon::parse($feat['date'])->format('d M Y H:i') }}</td>
                                <td class="p-3">
                                    <div class="font-medium text-foreground text-sm">{{ $feat['title'] }}</div>
                                    @if(!empty($feat['ref_type']))
                                        <div class="text-xs text-muted-foreground">{{ $feat['ref_type'] }} #{{ $feat['ref_id'] }}</div>
                                    @endif
                                </td>
                                <td class="p-3">
                                    <div class="flex gap-1 flex-wrap">
                                        @foreach($feat['channels'] ?? ['Web'] as $ch)
                                            <span class="kt-badge kt-badge-outline kt-badge-xs">{{ $ch }}</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="p-3">
                                    <span class="kt-badge kt-badge-{{ match($feat['status'] ?? 'Scheduled') {
                                        'Published' => 'success', 'Scheduled' => 'info',
                                        'Draft' => 'secondary', default => 'secondary'
                                    } }} kt-badge-sm">{{ $feat['status'] ?? 'Scheduled' }}</span>
                                </td>
                                <td class="p-3">
                                    <div class="flex gap-1">
                                        <button class="kt-btn kt-btn-ghost kt-btn-xs feature-edit" data-id="{{ $feat['id'] }}">
                                            <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                        </button>
                                        <button class="kt-btn kt-btn-ghost kt-btn-xs" title="Preview">
                                            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                        </button>
                                        <button class="kt-btn kt-btn-ghost kt-btn-xs text-destructive" title="Remove">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-10 text-center text-muted-foreground">
                                    No features scheduled. <button class="text-primary underline" id="btn-add-feature-empty">Add the first one</button>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- Add / Edit Feature Modal --}}
<div id="modal-feature" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-background rounded-xl shadow-xl w-full max-w-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-foreground">Add Feature</h2>
            <button class="feature-modal-close text-muted-foreground hover:text-foreground">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="space-y-4">
            <div>
                <label class="block text-xs text-muted-foreground mb-1 font-medium">Slot <span class="text-destructive">*</span></label>
                <select class="kt-input w-full">
                    <option>Hero banner</option>
                    <option>Homepage carousel</option>
                    <option>Sidebar promo</option>
                    <option>Footer banner</option>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">Start date/time</label>
                    <input type="datetime-local" class="kt-input w-full" />
                </div>
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">End date/time</label>
                    <input type="datetime-local" class="kt-input w-full" />
                </div>
            </div>
            <div>
                <label class="block text-xs text-muted-foreground mb-1 font-medium">Content type</label>
                <select class="kt-input w-full" id="feature-content-type">
                    <option value="page">Page / Post</option>
                    <option value="listing">Listing</option>
                    <option value="auction">Auction</option>
                    <option value="edition">Edition</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-muted-foreground mb-1 font-medium">Content reference</label>
                <input type="text" class="kt-input w-full" placeholder="Search by title or ID…" />
            </div>
            <div id="editions-notice" class="hidden rounded-lg bg-warning/10 border border-warning/30 p-3 text-xs text-warning-foreground">
                <i data-lucide="alert-triangle" class="w-3.5 h-3.5 inline mr-1"></i>
                Editions features require pro photography approval before publishing.
            </div>
            <div>
                <label class="block text-xs text-muted-foreground mb-1 font-medium">Channels</label>
                <div class="flex gap-4">
                    @foreach(['Web','Social'] as $ch)
                        <label class="flex items-center gap-1.5 text-sm cursor-pointer">
                            <input type="checkbox" class="kt-checkbox" checked /> {{ $ch }}
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="flex justify-end gap-2 mt-5">
            <button class="feature-modal-close kt-btn kt-btn-ghost">Cancel</button>
            <button class="kt-btn kt-btn-outline">Save draft</button>
            <button class="kt-btn kt-btn-mono">Schedule feature</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
// View toggle
const calPane  = document.getElementById('pane-calendar');
const listPane = document.getElementById('pane-list');
document.getElementById('view-calendar')?.addEventListener('click', () => {
    calPane.classList.remove('hidden'); listPane.classList.add('hidden');
    document.getElementById('view-calendar').className = 'kt-btn kt-btn-mono kt-btn-sm';
    document.getElementById('view-list').className = 'kt-btn kt-btn-outline kt-btn-sm';
});
document.getElementById('view-list')?.addEventListener('click', () => {
    listPane.classList.remove('hidden'); calPane.classList.add('hidden');
    document.getElementById('view-list').className = 'kt-btn kt-btn-mono kt-btn-sm';
    document.getElementById('view-calendar').className = 'kt-btn kt-btn-outline kt-btn-sm';
});

// Add feature modal
function openFeatureModal() {
    document.getElementById('modal-feature').classList.remove('hidden');
    document.getElementById('modal-feature').classList.add('flex');
}
document.getElementById('btn-add-feature')?.addEventListener('click', openFeatureModal);
document.getElementById('btn-add-feature-empty')?.addEventListener('click', openFeatureModal);
document.querySelectorAll('.feature-modal-close').forEach(b => {
    b.addEventListener('click', () => {
        document.getElementById('modal-feature').classList.add('hidden');
        document.getElementById('modal-feature').classList.remove('flex');
    });
});

// Editions notice
document.getElementById('feature-content-type')?.addEventListener('change', function() {
    document.getElementById('editions-notice').classList.toggle('hidden', this.value !== 'edition');
});
</script>
@endpush

@endsection
