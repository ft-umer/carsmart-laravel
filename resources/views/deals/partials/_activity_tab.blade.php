{{-- resources/views/deals/partials/_activity_tab.blade.php --}}
<div class="card border border-border rounded-xl overflow-hidden">
    <div class="px-4 py-3 border-b border-border bg-muted/20">
        <h3 class="text-sm font-semibold">Activity timeline</h3>
    </div>
    <div class="p-4">
        @if (!empty($deal['activity']))
            <div class="relative pl-4">
                <div class="absolute left-0 top-0 bottom-0 w-px bg-border ml-1.5"></div>
                <div class="space-y-4">
                    @foreach (array_reverse($deal['activity']) as $act)
                        <div class="flex gap-3 relative">
                            <div class="w-3 h-3 rounded-full bg-primary border-2 border-background shrink-0 mt-1
                                        relative z-10 -ml-1.5"></div>
                            <div class="flex-1 pb-1">
                                <div class="text-sm font-medium">{{ $act['description'] ?? '—' }}</div>
                                <div class="text-xs text-muted-foreground mt-0.5">
                                    {{ $act['date'] ?? '' }}
                                    @if ($act['by'] ?? null)
                                        · {{ $act['by'] }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <p class="text-sm text-muted-foreground text-center py-6">No activity recorded.</p>
        @endif
    </div>
</div>

{{-- ─────────────────────────────────────────────────────────────────────── --}}
{{-- resources/views/deals/partials/_history_tab.blade.php                  --}}
{{-- (appended here to minimise file count — split if preferred)            --}}
@section('history_tab')
@endsection
