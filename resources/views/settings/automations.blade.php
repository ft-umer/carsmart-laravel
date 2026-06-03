{{-- resources/views/settings/automations.blade.php --}}
{{-- Phase 5 — S6: Settings → Automations Policy --}}
@extends('layouts.app')
@section('title', 'Automations Policy — Settings')

@section('content')

@include('partials._retention_banner')

<div class="kt-container-fixed">
 <nav class="flex items-center gap-1.5 text-xs text-muted-foreground mb-4">
        <a href="{{ route('dashboard') }}" class="hover:text-foreground transition-colors">Home</a>
        <i data-lucide="chevron-right" class="w-3 h-3 shrink-0"></i>
         <a href="{{ route('settings.index') }}" class="hover:text-foreground transition-colors">Settings</a>
        <i data-lucide="chevron-right" class="w-3 h-3 shrink-0"></i>
        <span class="text-foreground font-medium">Automations Policy</span>
    </nav>

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('settings.index') }}" class="text-muted-foreground hover:text-foreground">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </a>
        <div>
            <h1 class="text-xl font-semibold text-foreground">Automations Policy</h1>
            <p class="text-sm text-muted-foreground mt-0.5">Platform-wide quiet hours, caps, approval rules, and valuation fetch limits</p>
        </div>
    </div>

    <form method="POST" action="{{ route('settings.automations.update') }}">
        @csrf @method('PATCH')

        {{-- Quiet hours --}}
        <div class="card border border-border rounded-xl p-6 mb-4">
            <div class="flex items-center gap-3 mb-4">
                <span class="flex items-center justify-center size-9 rounded-lg bg-warning/10">
                    <i data-lucide="moon" class="w-4 h-4 text-warning"></i>
                </span>
                <div>
                    <h2 class="text-sm font-semibold text-foreground">Quiet hours</h2>
                    <p class="text-xs text-muted-foreground">Messages will be queued (not dropped) during this window</p>
                </div>
                <div class="ml-auto">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="quiet_hours_enabled" class="kt-checkbox"
                               id="quiet-hours-toggle"
                               {{ ($policy['quiet_hours_enabled'] ?? true) ? 'checked' : '' }} />
                        <span class="text-sm font-medium">Enabled</span>
                    </label>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-3" id="quiet-hours-fields">
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">Start (local server time)</label>
                    <input type="time" name="quiet_hours_start" class="kt-input w-full"
                           value="{{ $policy['quiet_hours_start'] ?? '21:00' }}" />
                </div>
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">End</label>
                    <input type="time" name="quiet_hours_end" class="kt-input w-full"
                           value="{{ $policy['quiet_hours_end'] ?? '08:00' }}" />
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                @foreach(['Email','SMS','WhatsApp'] as $ch)
                    <label class="flex items-center gap-2 text-sm cursor-pointer p-2 rounded-lg border border-border hover:bg-muted/30">
                        <input type="checkbox" name="quiet_channels[]" value="{{ strtolower($ch) }}" class="kt-checkbox"
                               {{ in_array(strtolower($ch), $policy['quiet_channels'] ?? ['email','sms','whatsapp']) ? 'checked' : '' }} />
                        {{ $ch }}
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Daily caps --}}
        <div class="card border border-border rounded-xl p-6 mb-4">
            <div class="flex items-center gap-3 mb-4">
                <span class="flex items-center justify-center size-9 rounded-lg bg-primary/10">
                    <i data-lucide="gauge" class="w-4 h-4 text-primary"></i>
                </span>
                <h2 class="text-sm font-semibold text-foreground">Daily message caps</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">Max messages per recipient per day</label>
                    <div class="flex items-center gap-2">
                        <input type="number" name="max_per_recipient_day" class="kt-input w-24"
                               value="{{ $policy['max_per_recipient_day'] ?? 3 }}" min="1" max="50" />
                        <span class="text-sm text-muted-foreground">messages</span>
                    </div>
                </div>
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">Max platform sends per hour</label>
                    <div class="flex items-center gap-2">
                        <input type="number" name="max_platform_sends_hour" class="kt-input w-24"
                               value="{{ $policy['max_platform_sends_hour'] ?? 1000 }}" min="1" />
                        <span class="text-sm text-muted-foreground">sends</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Templates requiring approval --}}
        <div class="card border border-border rounded-xl p-6 mb-4">
            <div class="flex items-center gap-3 mb-4">
                <span class="flex items-center justify-center size-9 rounded-lg bg-info/10">
                    <i data-lucide="check-circle" class="w-4 h-4 text-info"></i>
                </span>
                <h2 class="text-sm font-semibold text-foreground">Templates requiring approval</h2>
            </div>
            <div class="space-y-2">
                @foreach([
                    ['broadcast',            'Broadcast / bulk sends'],
                    ['financial',            'Financial messages (fee notices, payment requests)'],
                    ['compliance_sensitive', 'Compliance-sensitive (KYC, legal, disputes)'],
                ] as [$val, $label])
                    <label class="flex items-center gap-2 text-sm cursor-pointer p-2 rounded-lg hover:bg-muted/30">
                        <input type="checkbox" name="approval_required[]" value="{{ $val }}" class="kt-checkbox"
                               {{ in_array($val, $policy['approval_required'] ?? ['broadcast','financial','compliance_sensitive']) ? 'checked' : '' }} />
                        {{ $label }}
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Valuation fetch policy (Phase 5 addition) --}}
        <div class="card border border-border rounded-xl p-6 mb-4 border-primary/20">
            <div class="flex items-center gap-3 mb-4">
                <span class="flex items-center justify-center size-9 rounded-lg bg-primary/10">
                    <i data-lucide="database" class="w-4 h-4 text-primary"></i>
                </span>
                <div>
                    <h2 class="text-sm font-semibold text-foreground">Valuation fetch policy</h2>
                    <p class="text-xs text-muted-foreground">
                        Rate limits stored here; enforcement is backend. These values hint the UI and suppression logs.
                    </p>
                </div>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">
                        Max valuation fetches per user per hour
                    </label>
                    <div class="flex items-center gap-2">
                        <input type="number" name="valuation_fetch_rate_limit" class="kt-input w-24"
                               value="{{ $policy['valuation_fetch_rate_limit'] ?? 10 }}" min="1" max="1000" />
                        <span class="text-sm text-muted-foreground">fetches / hour</span>
                    </div>
                    <p class="text-xs text-muted-foreground mt-1">
                        Backend enforces this. Exceeded fetches are logged as suppressed runs.
                    </p>
                </div>
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">
                        Apply quiet hours to valuation fetch automations
                    </label>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="radio" name="valuation_quiet_hours" value="1" class="kt-radio"
                                   {{ ($policy['valuation_quiet_hours'] ?? false) ? 'checked' : '' }} />
                            Yes — queue fetches during quiet hours
                        </label>
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="radio" name="valuation_quiet_hours" value="0" class="kt-radio"
                                   {{ !($policy['valuation_quiet_hours'] ?? false) ? 'checked' : '' }} />
                            No — fetch any time
                        </label>
                    </div>
                    <p class="text-xs text-muted-foreground mt-1">
                        "Yes" suppresses valuation fetch automations during quiet hours. Suppression reason logged as
                        <code class="font-mono">quiet_hours</code>.
                    </p>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="kt-btn kt-btn-mono">Save automations policy</button>
        </div>
    </form>

</div>

@push('scripts')
<script>
document.getElementById('quiet-hours-toggle')?.addEventListener('change', function() {
    document.getElementById('quiet-hours-fields').style.opacity = this.checked ? '1' : '0.4';
});
</script>
@endpush

@endsection
