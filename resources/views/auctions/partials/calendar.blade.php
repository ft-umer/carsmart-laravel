{{--
    resources/views/auctions/partials/calendar.blade.php
    A1 — Auctions Calendar (month/week/day views, drag to create)
--}}

<div id="auctions-calendar" class="space-y-4">

    {{-- Calendar toolbar --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
            <button id="cal-prev" class="kt-btn kt-btn-ghost kt-btn-icon">
                <i class="ki-outline ki-left text-base"></i>
            </button>
            <h2 id="cal-title" class="text-lg font-semibold text-foreground min-w-[160px] text-center">
                October 2025
            </h2>
            <button id="cal-next" class="kt-btn kt-btn-ghost kt-btn-icon">
                <i class="ki-outline ki-right text-base"></i>
            </button>
            <button id="cal-today" class="kt-btn kt-btn-outline kt-btn-sm">Today</button>
        </div>
        <div class="flex items-center gap-1">
            <button data-cal-view="month" class="cal-view-btn kt-btn kt-btn-mono kt-btn-sm">Month</button>
            <button data-cal-view="week"  class="cal-view-btn kt-btn kt-btn-ghost kt-btn-sm">Week</button>
            <button data-cal-view="day"   class="cal-view-btn kt-btn kt-btn-ghost kt-btn-sm">Day</button>
        </div>
    </div>

    {{-- Legend --}}
    <div class="flex items-center gap-4 text-xs text-muted-foreground">
        <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-blue-500 inline-block"></span> Planned</span>
        <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-green-500 inline-block"></span> Published</span>
        <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-yellow-500 inline-block"></span> Live</span>
        <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-muted-foreground inline-block"></span> Ended</span>
    </div>

    {{-- Calendar grid --}}
    <div class="card border border-border rounded-lg overflow-hidden">

        {{-- Day-of-week headers --}}
        <div class="grid grid-cols-7 border-b border-border bg-muted/40">
            @foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $d)
            <div class="p-2 text-xs font-medium text-muted-foreground text-center">{{ $d }}</div>
            @endforeach
        </div>

        {{-- Calendar body (populated by JS) --}}
        <div id="cal-grid" class="grid grid-cols-7 divide-x divide-y divide-border min-h-[480px]">
            {{-- cells injected by auctions.js --}}
        </div>
    </div>

    {{-- Clash / density warning --}}
    <div id="cal-clash-warning"
         class="hidden rounded border border-yellow-200 bg-yellow-50 p-3 text-xs text-yellow-800">
        ⚠ Multiple auctions overlap on the selected day. Check for scheduling clashes.
    </div>

</div>