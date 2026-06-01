{{-- resources/views/deals/partials/_logistics_tab.blade.php --}}
@if ($deal['job'] ?? null)
    @php $job = $deal['job']; @endphp
    <div class="card border border-border rounded-xl p-4 space-y-3">
        <div class="flex items-start justify-between gap-3 flex-wrap">
            <div>
                <h3 class="text-sm font-semibold">Transport job {{ $job['ref'] ?? '' }}</h3>
                <p class="text-xs text-muted-foreground mt-0.5">
                    {{ $job['pickup_address'] ?? '—' }} → {{ $job['drop_address'] ?? '—' }}
                </p>
            </div>
            @php
                $jobStateCls = match ($job['status'] ?? '') {
                    'Scheduled'  => 'kt-badge-info',
                    'In transit' => 'kt-badge-warning',
                    'Delivered'  => 'kt-badge-success',
                    'Issue'      => 'kt-badge-destructive',
                    default      => 'kt-badge-outline',
                };
            @endphp
            <span class="kt-badge {{ $jobStateCls }}">{{ $job['status'] ?? 'Unknown' }}</span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
            <div><span class="text-muted-foreground">Provider</span><br><strong>{{ $job['provider'] ?? '—' }}</strong></div>
            <div><span class="text-muted-foreground">Slot</span><br><strong>{{ $job['slot'] ?? '—' }}</strong></div>
            <div><span class="text-muted-foreground">Tracking ref</span><br><strong class="font-mono">{{ $job['tracking_ref'] ?? '—' }}</strong></div>
            <div><span class="text-muted-foreground">Created</span><br><strong>{{ $job['created_at'] ?? '—' }}</strong></div>
        </div>

        <div class="flex gap-2 flex-wrap pt-2 border-t border-border">
            <a href="{{ route('logistics.jobs.show', $job['id']) }}"
               class="kt-btn kt-btn-outline kt-btn-sm">Open job</a>
            <a href="{{ route('logistics.jobs.show', $job['id']) }}#chat"
               class="kt-btn kt-btn-ghost kt-btn-sm">
                <i data-lucide="message-circle" class="w-3.5 h-3.5 mr-1"></i>Transport chat
            </a>
        </div>
    </div>

    {{-- Handover checklist summary --}}
    <div class="card border border-border rounded-xl p-4">
        <h3 class="text-sm font-semibold mb-3">Handover checklist</h3>
        @include('logistics.partials._handover_checklist_summary', ['deal' => $deal, 'job' => $job])
    </div>
@else
    <div class="card border border-border rounded-xl p-6 text-center text-sm text-muted-foreground">
        <i data-lucide="truck" class="w-8 h-8 mx-auto mb-2 opacity-30"></i>
        No logistics job linked.
        <div class="mt-3">
            <a href="{{ route('logistics.jobs.create', ['deal' => $deal['id']]) }}"
               class="kt-btn kt-btn-mono">Book collection</a>
        </div>
    </div>
@endif
