{{-- resources/views/logistics/partials/_handover_checklist_summary.blade.php --}}
{{-- Compact read-only checklist summary shown inside the Deal detail Logistics tab --}}

@php
    $items = [
        'Buyer identity checked'  => $job['chk_buyer_id']  ?? false,
        'Seller identity checked' => $job['chk_seller_id'] ?? false,
        'V5C present'             => $job['chk_v5c']       ?? false,
        'Keys received'           => $job['chk_keys']      ?? false,
        'Photos uploaded'         => !empty($job['photos']),
        'Buyer signed'            => !empty($job['buyer_signature']),
        'Seller signed'           => !empty($job['seller_signature']),
    ];
    $done  = collect($items)->filter()->count();
    $total = count($items);
    $pct   = $total ? round($done / $total * 100) : 0;
@endphp

<div class="space-y-3">
    {{-- Progress bar --}}
    <div>
        <div class="flex justify-between text-xs mb-1">
            <span class="text-muted-foreground">{{ $done }}/{{ $total }} items complete</span>
            <span class="font-semibold {{ $pct === 100 ? 'text-green-600 dark:text-green-400' : 'text-foreground' }}">
                {{ $pct }}%
            </span>
        </div>
        <div class="h-1.5 rounded-full bg-muted overflow-hidden">
            <div class="h-full rounded-full transition-all
                        {{ $pct === 100 ? 'bg-green-500' : 'bg-primary' }}"
                 style="width: {{ $pct }}%"></div>
        </div>
    </div>

    {{-- Item grid --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
        @foreach ($items as $label => $checked)
            <div class="flex items-center gap-2 px-2.5 py-2 rounded-lg text-xs
                        {{ $checked
                            ? 'border border-green-200 bg-green-50 dark:bg-green-900/20 dark:border-green-700'
                            : 'border border-border bg-muted/20' }}">
                <i data-lucide="{{ $checked ? 'check-circle' : 'circle' }}"
                   class="w-3.5 h-3.5 shrink-0
                          {{ $checked ? 'text-green-500' : 'text-muted-foreground' }}"></i>
                <span class="{{ $checked
                    ? 'text-green-800 dark:text-green-300'
                    : 'text-muted-foreground' }}">
                    {{ $label }}
                </span>
            </div>
        @endforeach
    </div>

    {{-- Key count --}}
    @if ($job['chk_keys'] ?? false)
        <div class="text-xs text-muted-foreground">
            Keys handed over: <strong class="text-foreground">{{ $job['key_count'] ?? '—' }}</strong>
        </div>
    @endif

    {{-- Condition notes --}}
    @if ($job['condition_notes'] ?? null)
        <div class="text-xs bg-muted/30 border border-border rounded-lg px-3 py-2">
            <span class="font-medium">Condition notes: </span>{{ $job['condition_notes'] }}
        </div>
    @endif

    {{-- CTA --}}
    <a href="{{ route('logistics.jobs.show', $job['id']) }}?tab=checklist"
       class="kt-btn kt-btn-outline kt-btn-sm w-full mt-1 text-center">
        {{ $pct < 100 ? 'Complete checklist' : 'View checklist' }}
    </a>
</div>
