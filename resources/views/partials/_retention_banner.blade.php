{{-- resources/views/partials/_retention_banner.blade.php --}}
{{-- G1: 12-month data-retention notice + Include-archived toggle (appears on all Phase-4 indexes) --}}

@php
    $retentionMonths = config('carsmart.retention_months', 12);
    $includeArchived = request()->boolean('include_archived', false);
@endphp

<div class="kt-container-fixed">
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2
            px-4 py-2.5 mb-4 rounded-lg
            bg-amber-50 dark:bg-amber-900/20
            border border-amber-200 dark:border-amber-700
            text-amber-800 dark:text-amber-300 text-xs">
    <div class="flex items-center gap-2">
        <i data-lucide="shield" class="w-3.5 h-3.5 shrink-0"></i>
        <span>
            Data shown covers the last <strong>{{ $retentionMonths }} months</strong> per retention policy.
            Older records are archived and accessible only to Administrators.
        </span>
    </div>
    <label class="flex items-center gap-2 cursor-pointer shrink-0">
        <input type="checkbox"
               class="form-checkbox rounded"
               name="include_archived"
               value="1"
               {{ $includeArchived ? 'checked' : '' }}
               onchange="
                   const url = new URL(window.location.href);
                   this.checked ? url.searchParams.set('include_archived','1')
                                : url.searchParams.delete('include_archived');
                   window.location.href = url.toString();
               " />
        <span class="font-medium">Include archived</span>
    </label>
</div>
</div>
