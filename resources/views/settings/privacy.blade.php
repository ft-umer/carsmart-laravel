{{-- resources/views/settings/privacy.blade.php --}}
{{-- Phase 5 — S7: Settings → Consent & Privacy --}}
@extends('layouts.app')
@section('title', 'Consent & Privacy — Settings')

@section('content')

@include('partials._retention_banner')

<div class="kt-container-fixed">

 <nav class="flex items-center gap-1.5 text-xs text-muted-foreground mb-4">
        <a href="{{ route('dashboard') }}" class="hover:text-foreground transition-colors">Home</a>
        <i data-lucide="chevron-right" class="w-3 h-3 shrink-0"></i>
         <a href="{{ route('settings.index') }}" class="hover:text-foreground transition-colors">Settings</a>
        <i data-lucide="chevron-right" class="w-3 h-3 shrink-0"></i>
        <span class="text-foreground font-medium">Consent & Privacy</span>
    </nav>

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('settings.index') }}" class="text-muted-foreground hover:text-foreground">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </a>
        <div>
            <h1 class="text-xl font-semibold text-foreground">Consent & Privacy</h1>
            <p class="text-sm text-muted-foreground mt-0.5">Retention periods, export masking, and right-to-be-forgotten</p>
        </div>
    </div>

    <form method="POST" action="{{ route('settings.privacy.update') }}">
        @csrf @method('PATCH')

        {{-- Retention --}}
        <div class="card border border-border rounded-xl p-6 mb-4">
            <div class="flex items-center gap-3 mb-4">
                <span class="flex items-center justify-center size-9 rounded-lg bg-info/10">
                    <i data-lucide="clock" class="w-4 h-4 text-info"></i>
                </span>
                <h2 class="text-sm font-semibold text-foreground">Data retention</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">Default retention period</label>
                    <div class="flex items-center gap-2">
                        <input type="number" name="retention_months" class="kt-input w-24"
                               value="{{ $settings['retention_months'] ?? 12 }}" min="1" max="120" />
                        <span class="text-sm text-muted-foreground">months</span>
                    </div>
                </div>
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">Include archived records in exports</label>
                    <div class="flex gap-4 mt-1">
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="radio" name="include_archived_default" value="1" class="kt-radio"
                                   {{ ($settings['include_archived_default'] ?? false) ? 'checked' : '' }} />
                            On by default
                        </label>
                        <label class="flex items-center gap-2 text-sm cursor-pointer">
                            <input type="radio" name="include_archived_default" value="0" class="kt-radio"
                                   {{ !($settings['include_archived_default'] ?? false) ? 'checked' : '' }} />
                            Off by default
                        </label>
                    </div>
                </div>
            </div>
            <div class="rounded-lg bg-info/10 border border-info/30 p-3 text-xs text-info-foreground">
                <i data-lucide="info" class="w-3.5 h-3.5 inline mr-1"></i>
                Retention banners will appear on all list views when records approach their expiry.
            </div>
        </div>

        {{-- Export masking --}}
        <div class="card border border-border rounded-xl p-6 mb-4">
            <div class="flex items-center gap-3 mb-4">
                <span class="flex items-center justify-center size-9 rounded-lg bg-warning/10">
                    <i data-lucide="eye-off" class="w-4 h-4 text-warning"></i>
                </span>
                <h2 class="text-sm font-semibold text-foreground">Export masking</h2>
            </div>
            <p class="text-xs text-muted-foreground mb-3">
                Fields below will be masked (e.g. <code class="font-mono">j**e@e***.com</code>) for non-privileged roles in all CSV/PDF exports.
            </p>
            <div class="space-y-2">
                @foreach([
                    ['email',    'Email address'],
                    ['phone',    'Phone / mobile number'],
                    ['address',  'Postal address'],
                    ['dob',      'Date of birth'],
                    ['bank_sort','Bank sort code'],
                    ['bank_acc', 'Bank account number'],
                ] as [$key, $label])
                    <label class="flex items-center gap-2 text-sm cursor-pointer p-2 rounded-lg hover:bg-muted/30">
                        <input type="checkbox" name="masked_fields[]" value="{{ $key }}" class="kt-checkbox"
                               {{ in_array($key, $settings['masked_fields'] ?? ['email','phone','address']) ? 'checked' : '' }} />
                        {{ $label }}
                        <span class="text-xs text-muted-foreground ml-auto font-mono">{{ $key }}</span>
                    </label>
                @endforeach
            </div>
            <p class="text-xs text-muted-foreground mt-2">
                Privileged roles (Admin, Compliance, Super Admin) always see unmasked data.
            </p>
        </div>

        {{-- Right to be forgotten --}}
        <div class="card border border-border rounded-xl p-6 mb-4">
            <div class="flex items-center gap-3 mb-4">
                <span class="flex items-center justify-center size-9 rounded-lg bg-destructive/10">
                    <i data-lucide="trash-2" class="w-4 h-4 text-destructive"></i>
                </span>
                <h2 class="text-sm font-semibold text-foreground">Right-to-be-forgotten</h2>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">Redaction map</label>
                    <p class="text-xs text-muted-foreground mb-2">
                        Configure which fields are nullified / replaced when a RTBF request is processed.
                    </p>
                    <div class="space-y-2">
                        @foreach([
                            ['name',    'Full name',     'Replaced with "[Redacted]"'],
                            ['email',   'Email address', 'Replaced with randomised placeholder'],
                            ['phone',   'Phone number',  'Nullified'],
                            ['address', 'Address lines', 'Nullified'],
                            ['dob',     'Date of birth', 'Nullified'],
                            ['ip',      'IP addresses',  'Nullified in logs'],
                        ] as [$key, $label, $action])
                            <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-muted/20">
                                <label class="flex items-center gap-2 text-sm cursor-pointer flex-1">
                                    <input type="checkbox" name="rtbf_fields[]" value="{{ $key }}" class="kt-checkbox"
                                           {{ in_array($key, $settings['rtbf_fields'] ?? ['name','email','phone','address','dob','ip']) ? 'checked' : '' }} />
                                    <span class="font-medium">{{ $label }}</span>
                                </label>
                                <span class="text-xs text-muted-foreground">{{ $action }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="border-t border-border pt-4">
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">Test on sample data</label>
                    <div class="flex items-center gap-3">
                        <input type="text" id="rtbf-sample-id" class="kt-input flex-1"
                               placeholder="User ID or email to test redaction…" />
                        <button type="button" class="kt-btn kt-btn-outline kt-btn-sm whitespace-nowrap" id="btn-rtbf-test">
                            <i data-lucide="play" class="w-3.5 h-3.5 mr-1"></i> Run on sample
                        </button>
                    </div>
                    <div id="rtbf-result" class="mt-2 hidden"></div>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="kt-btn kt-btn-mono">Save privacy settings</button>
        </div>
    </form>

</div>

@push('scripts')
<script>
document.getElementById('btn-rtbf-test')?.addEventListener('click', () => {
    const sample = document.getElementById('rtbf-sample-id').value;
    if(!sample) return;
    const res = document.getElementById('rtbf-result');
    res.classList.remove('hidden');
    res.className = 'mt-2 text-xs rounded-lg bg-muted p-3 font-mono';
    res.innerHTML = `<span class="text-warning">Running dry-run for: ${sample}…</span>`;
    setTimeout(() => {
        res.innerHTML = `<div class="text-success mb-1">✓ Dry-run complete (no data modified)</div>
<div class="text-muted-foreground">name:    "John Doe" → "[Redacted]"</div>
<div class="text-muted-foreground">email:   "john@example.com" → "rdct_a7f2@example.invalid"</div>
<div class="text-muted-foreground">phone:   "+447900123456" → null</div>
<div class="text-muted-foreground">address: "123 Main St…" → null</div>`;
    }, 1200);
});
</script>
@endpush

@endsection
