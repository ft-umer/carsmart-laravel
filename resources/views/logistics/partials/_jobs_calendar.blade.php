{{-- resources/views/logistics/partials/_jobs_calendar.blade.php --}}
{{-- Phase 4 — L2: Weekly calendar view for transport jobs --}}

@php
    use Carbon\Carbon;
    $today     = Carbon::now();
    $weekStart = $today->copy()->startOfWeek();         // Monday
    $weekEnd   = $today->copy()->endOfWeek();           // Sunday
    $days      = collect(range(0, 6))->map(fn($i) => $weekStart->copy()->addDays($i));

    // Group jobs by date (expects $jobs[*]['slot_date'] as 'Y-m-d')
    $byDay = collect($jobs)->groupBy(fn($j) => substr($j['slot'] ?? '', 0, 10));

    $slotCls = [
        'Scheduled'  => 'border-blue-300 bg-blue-50 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300',
        'In transit' => 'border-amber-300 bg-amber-50 dark:bg-amber-900/20 text-amber-800 dark:text-amber-300',
        'Delivered'  => 'border-green-300 bg-green-50 dark:bg-green-900/20 text-green-800 dark:text-green-300',
        'Issue'      => 'border-red-300 bg-red-50 dark:bg-red-900/20 text-red-800 dark:text-red-300',
    ];
@endphp

<div class="card border border-border rounded-xl overflow-hidden">

    {{-- Calendar header --}}
    <div class="px-4 py-3 border-b border-border bg-muted/20 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <button id="btn-cal-prev" class="kt-btn kt-btn-ghost kt-btn-sm px-2">
                <i data-lucide="chevron-left" class="w-4 h-4"></i>
            </button>
            <span class="text-sm font-semibold">
                {{ $weekStart->format('d M') }} – {{ $weekEnd->format('d M Y') }}
            </span>
            <button id="btn-cal-next" class="kt-btn kt-btn-ghost kt-btn-sm px-2">
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </button>
            <button id="btn-cal-today" class="kt-btn kt-btn-outline kt-btn-sm">Today</button>
        </div>
        <div class="flex gap-3 text-xs">
            @foreach(['Scheduled' => 'blue', 'In transit' => 'amber', 'Delivered' => 'green', 'Issue' => 'red'] as $lbl => $col)
                <span class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-{{ $col }}-400"></span>
                    {{ $lbl }}
                </span>
            @endforeach
        </div>
    </div>

    {{-- Day columns --}}
    <div class="grid grid-cols-7 divide-x divide-border overflow-x-auto min-w-[700px]">
        {{-- Day headers --}}
        @foreach ($days as $day)
            <div class="px-2 py-2.5 text-center border-b border-border
                        {{ $day->isToday() ? 'bg-primary/5' : 'bg-muted/10' }}">
                <div class="text-xs text-muted-foreground font-medium">{{ $day->format('D') }}</div>
                <div class="text-sm font-semibold mt-0.5
                            {{ $day->isToday()
                                ? 'w-7 h-7 bg-primary text-primary-foreground rounded-full flex items-center justify-center mx-auto'
                                : '' }}">
                    {{ $day->format('d') }}
                </div>
            </div>
        @endforeach

        {{-- Job cells --}}
        @foreach ($days as $day)
            @php $dateKey = $day->format('Y-m-d'); $dayJobs = $byDay->get($dateKey, collect()); @endphp
            <div class="p-1.5 min-h-[160px] space-y-1.5
                        {{ $day->isToday() ? 'bg-primary/5' : '' }}
                        {{ $day->isWeekend() ? 'bg-muted/5' : '' }}">
                @forelse ($dayJobs as $job)
                    @php $cls = $slotCls[$job['status'] ?? ''] ?? 'border-border bg-muted/20 text-foreground'; @endphp
                    <a href="{{ route('logistics.jobs.show', $job['id']) }}"
                       class="block rounded-lg border px-2 py-1.5 text-xs transition-shadow
                              hover:shadow-md {{ $cls }}">
                        <div class="font-semibold truncate">{{ $job['ref'] ?? 'JOB' }}</div>
                        @if ($job['vrm'] ?? null)
                            <div class="font-mono opacity-80">{{ $job['vrm'] }}</div>
                        @endif
                        <div class="opacity-70 truncate">{{ $job['provider'] ?? '—' }}</div>
                    </a>
                @empty
                    {{-- empty slot --}}
                @endforelse
            </div>
        @endforeach
    </div>

    <div class="px-4 py-2 border-t border-border text-xs text-muted-foreground bg-muted/10">
        Showing week of {{ $weekStart->format('d M Y') }}
        · {{ $jobs->count() }} job{{ $jobs->count() !== 1 ? 's' : '' }} total
    </div>
</div>

<script>
/* Calendar week navigation (page-reload based) */
document.getElementById('btn-cal-prev')?.addEventListener('click', () => {
    const url = new URL(window.location.href);
    const offset = parseInt(url.searchParams.get('week_offset') ?? '0') - 1;
    url.searchParams.set('week_offset', offset);
    window.location.href = url.toString();
});
document.getElementById('btn-cal-next')?.addEventListener('click', () => {
    const url = new URL(window.location.href);
    const offset = parseInt(url.searchParams.get('week_offset') ?? '0') + 1;
    url.searchParams.set('week_offset', offset);
    window.location.href = url.toString();
});
document.getElementById('btn-cal-today')?.addEventListener('click', () => {
    const url = new URL(window.location.href);
    url.searchParams.delete('week_offset');
    window.location.href = url.toString();
});
</script>
