
@if(isset($retentionWarning) && $retentionWarning)
<div class="mb-4 rounded-lg border border-warning/40 bg-warning/5 px-4 py-3 flex items-start gap-3" role="alert">
    <i data-lucide="clock" class="w-4 h-4 text-warning shrink-0 mt-0.5"></i>
    <div class="flex-1 text-sm">
        <span class="font-semibold text-warning">Retention notice:</span>
        <span class="text-muted-foreground ml-1">{{ $retentionWarning }}</span>
    </div>
    <button class="text-muted-foreground hover:text-foreground shrink-0"
            onclick="this.closest('[role=alert]').remove()" aria-label="Dismiss">
        <i data-lucide="x" class="w-4 h-4"></i>
    </button>
</div>
@endif

@if(config('carsmart.show_retention_banner', false))
<div class="mb-4 rounded-lg border border-info/30 bg-info/5 px-4 py-3 flex items-start gap-3" role="alert" id="retention-policy-banner">
    <i data-lucide="shield" class="w-4 h-4 text-info shrink-0 mt-0.5"></i>
    <div class="flex-1 text-sm text-muted-foreground">
        Records are retained for
        <strong class="text-foreground">{{ config('carsmart.retention_months', 12) }} months</strong>
        per the platform's
        <a href="{{ route('settings.privacy') }}" class="text-primary underline hover:no-underline">data retention policy</a>.
        Archived records are
        <strong class="text-foreground">{{ config('carsmart.include_archived_default', false) ? 'included' : 'excluded' }}</strong>
        by default.
    </div>
    <button class="text-muted-foreground hover:text-foreground shrink-0"
            onclick="this.closest('[role=alert]').remove()" aria-label="Dismiss">
        <i data-lucide="x" class="w-4 h-4"></i>
    </button>
</div>
@endif
