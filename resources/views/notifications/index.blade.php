{{-- resources/views/notifications/index.blade.php --}}
{{-- Phase 5 — N0: Notifications Centre --}}
@extends('layouts.app')
@section('title', 'Notifications — Carsmart')

@section('content')

@include('partials._retention_banner')

<div class="kt-container-fixed">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
        <div class="flex items-center gap-3">
            <h1 class="text-xl font-semibold text-foreground">Notifications</h1>
            @if(($unreadCount ?? 0) > 0)
                <span class="kt-badge kt-badge-primary kt-badge-sm">{{ $unreadCount }} unread</span>
            @endif
        </div>
        <div class="flex gap-2">
            <button class="kt-btn kt-btn-ghost kt-btn-sm" id="btn-mark-all-read">
                <i data-lucide="check-check" class="w-4 h-4 mr-1"></i> Mark all read
            </button>
            <a href="{{ route('notifications.preferences') }}" class="kt-btn kt-btn-outline kt-btn-sm">
                <i data-lucide="settings" class="w-4 h-4 mr-1"></i> Preferences
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card border border-border rounded-lg p-3 mb-5">
        <div class="flex flex-wrap gap-2 items-center">
            <span class="text-xs text-muted-foreground font-medium mr-1">Type:</span>
            @foreach(['All','Alerts','Assignments','Automations','Finance','Logistics','Valuations'] as $type)
                <button class="kt-btn kt-btn-{{ ($activeType ?? 'All') === $type ? 'mono' : 'outline' }} kt-btn-xs notif-type-btn"
                        data-type="{{ $type }}">
                    {{ $type }}
                </button>
            @endforeach
            <div class="ml-auto flex items-center gap-2">
                <label class="flex items-center gap-1.5 text-xs cursor-pointer text-muted-foreground hover:text-foreground">
                    <input type="checkbox" class="kt-checkbox" id="unread-only-toggle"
                           {{ ($unreadOnly ?? false) ? 'checked' : '' }} />
                    Unread only
                </label>
            </div>
        </div>
    </div>

    {{-- Bulk valuation job summaries (Phase 5 optional) --}}
    @foreach($valuationSummaries ?? [] as $summary)
        <div class="mb-3 rounded-lg border border-primary/20 bg-primary/5 p-4 flex items-start gap-3">
            <i data-lucide="database" class="w-4 h-4 text-primary shrink-0 mt-0.5"></i>
            <div class="flex-1">
                <p class="text-sm font-medium text-foreground">Valuation fetch completed</p>
                <p class="text-xs text-muted-foreground mt-0.5">
                    {{ $summary['succeeded'] }} succeeded,
                    <span class="{{ $summary['failed'] > 0 ? 'text-destructive' : '' }}">{{ $summary['failed'] }} failed</span>
                    — {{ \Carbon\Carbon::parse($summary['completed_at'])->diffForHumans() }}
                </p>
            </div>
            @if($summary['failed'] > 0)
                <a href="{{ route('automations.runs', ['status' => 'Failed', 'journey_id' => $summary['journey_id']]) }}"
                   class="kt-btn kt-btn-ghost kt-btn-xs">View failures</a>
            @endif
            <button class="text-muted-foreground hover:text-foreground p-1" onclick="this.closest('.mb-3').remove()">
                <i data-lucide="x" class="w-3.5 h-3.5"></i>
            </button>
        </div>
    @endforeach

    {{-- Notifications list --}}
    <div class="card border border-border rounded-xl overflow-hidden">
        <div class="divide-y divide-border" id="notif-list">
            @forelse($notifications ?? [] as $notif)
                <div class="flex items-start gap-3 p-4 hover:bg-muted/20 transition-colors notif-item
                            {{ !$notif['read'] ? 'bg-primary/3' : '' }}"
                     data-type="{{ $notif['type'] }}" data-read="{{ $notif['read'] ? '1' : '0' }}"
                     data-id="{{ $notif['id'] }}">

                    {{-- Type icon --}}
                    <span class="flex items-center justify-center size-9 rounded-lg shrink-0
                                 {{ match($notif['type']) {
                                     'Alerts'      => 'bg-destructive/10',
                                     'Assignments' => 'bg-primary/10',
                                     'Automations' => 'bg-info/10',
                                     'Finance'     => 'bg-success/10',
                                     'Logistics'   => 'bg-warning/10',
                                     'Valuations'  => 'bg-primary/10',
                                     default       => 'bg-muted',
                                 } }}">
                        <i data-lucide="{{ match($notif['type']) {
                            'Alerts'      => 'alert-circle',
                            'Assignments' => 'user-check',
                            'Automations' => 'zap',
                            'Finance'     => 'dollar-sign',
                            'Logistics'   => 'truck',
                            'Valuations'  => 'database',
                            default       => 'bell',
                        } }}" class="w-4 h-4 {{ match($notif['type']) {
                            'Alerts'      => 'text-destructive',
                            'Assignments' => 'text-primary',
                            'Automations' => 'text-info',
                            'Finance'     => 'text-success',
                            'Logistics'   => 'text-warning',
                            'Valuations'  => 'text-primary',
                            default       => 'text-muted-foreground',
                        } }}"></i>
                    </span>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="text-sm font-medium text-foreground {{ !$notif['read'] ? 'font-semibold' : '' }}">
                                    {{ $notif['title'] }}
                                    @if(!$notif['read'])
                                        <span class="inline-block w-2 h-2 rounded-full bg-primary ml-1.5 align-middle"></span>
                                    @endif
                                </p>
                                <p class="text-xs text-muted-foreground mt-0.5">{{ $notif['summary'] }}</p>
                                @if($notif['object'] ?? false)
                                    <p class="text-xs text-muted-foreground mt-0.5">
                                        <span class="kt-badge kt-badge-outline kt-badge-xs">{{ $notif['object'] }}</span>
                                    </p>
                                @endif
                            </div>
                            <span class="text-xs text-muted-foreground whitespace-nowrap shrink-0">
                                {{ \Carbon\Carbon::parse($notif['time'])->diffForHumans() }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2 mt-2">
                            @if($notif['action_url'] ?? false)
                                <a href="{{ $notif['action_url'] }}" class="kt-btn kt-btn-ghost kt-btn-xs">Open</a>
                            @endif
                            @if(!$notif['read'])
                                <button class="kt-btn kt-btn-ghost kt-btn-xs notif-read-btn" data-id="{{ $notif['id'] }}">
                                    Mark read
                                </button>
                            @endif
                            <button class="kt-btn kt-btn-ghost kt-btn-xs notif-mute-btn text-muted-foreground" data-id="{{ $notif['id'] }}">
                                Mute
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-16 text-center">
                    <i data-lucide="bell-off" class="w-10 h-10 mx-auto mb-3 text-muted-foreground/30"></i>
                    <p class="text-sm font-medium text-foreground">All caught up!</p>
                    <p class="text-xs text-muted-foreground mt-1">No notifications match your current filter.</p>
                </div>
            @endforelse
        </div>
    </div>

    @if(isset($notifications) && method_exists($notifications, 'links'))
        <div class="mt-4">{{ $notifications->links() }}</div>
    @endif

</div>

@push('scripts')
<script>
// Type filter
document.querySelectorAll('.notif-type-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const type = this.dataset.type;
        document.querySelectorAll('.notif-type-btn').forEach(b => {
            b.className = 'kt-btn kt-btn-outline kt-btn-xs notif-type-btn';
        });
        this.className = 'kt-btn kt-btn-mono kt-btn-xs notif-type-btn';

        document.querySelectorAll('.notif-item').forEach(item => {
            item.style.display = type === 'All' || item.dataset.type === type ? '' : 'none';
        });
    });
});

// Unread only toggle
document.getElementById('unread-only-toggle')?.addEventListener('change', function() {
    document.querySelectorAll('.notif-item').forEach(item => {
        if(this.checked) {
            item.style.display = item.dataset.read === '0' ? '' : 'none';
        } else {
            item.style.display = '';
        }
    });
});

// Mark read
document.querySelectorAll('.notif-read-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const item = btn.closest('.notif-item');
        item.dataset.read = '1';
        item.classList.remove('bg-primary/3');
        const dot = item.querySelector('.bg-primary.rounded-full');
        dot?.remove();
        btn.closest('.flex').querySelector('.notif-read-btn')?.remove();
    });
});

// Mark all read
document.getElementById('btn-mark-all-read')?.addEventListener('click', () => {
    document.querySelectorAll('.notif-item[data-read="0"]').forEach(item => {
        item.dataset.read = '1';
        item.classList.remove('bg-primary/3');
        item.querySelectorAll('.bg-primary.rounded-full').forEach(d => d.remove());
        item.querySelector('.notif-read-btn')?.remove();
    });
    document.querySelector('.kt-badge-primary')?.remove();
});
</script>
@endpush

@endsection
