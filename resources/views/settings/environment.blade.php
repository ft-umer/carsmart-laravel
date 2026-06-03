{{-- resources/views/settings/environment.blade.php --}}
{{-- Phase 5 — S9: Settings → Environment (Staging/Sandbox) --}}
@extends('layouts.app')
@section('title', 'Environment — Settings')

@section('content')

@include('partials._retention_banner')

<div class="kt-container-fixed">
 <nav class="flex items-center gap-1.5 text-xs text-muted-foreground mb-4">
        <a href="{{ route('dashboard') }}" class="hover:text-foreground transition-colors">Home</a>
        <i data-lucide="chevron-right" class="w-3 h-3 shrink-0"></i>
         <a href="{{ route('settings.index') }}" class="hover:text-foreground transition-colors">Settings</a>
        <i data-lucide="chevron-right" class="w-3 h-3 shrink-0"></i>
        <span class="text-foreground font-medium">Environment</span>
    </nav>
    <div class="flex items-center gap-3 mb-4">
        <a href="{{ route('settings.index') }}" class="text-muted-foreground hover:text-foreground">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </a>
        <div>
            <h1 class="text-xl font-semibold text-foreground">Environment</h1>
            <p class="text-sm text-muted-foreground mt-0.5">Staging / sandbox keys, seed tools, and safe toggles</p>
        </div>
    </div>

    {{-- Environment badge --}}
    <div class="rounded-lg border {{ app()->environment('production') ? 'border-destructive/40 bg-destructive/5' : 'border-warning/40 bg-warning/5' }} p-4 mb-6 flex items-center gap-3">
        <i data-lucide="{{ app()->environment('production') ? 'alert-octagon' : 'terminal' }}" class="w-5 h-5 {{ app()->environment('production') ? 'text-destructive' : 'text-warning' }} shrink-0"></i>
        <div>
            <p class="text-sm font-semibold {{ app()->environment('production') ? 'text-destructive' : 'text-warning' }}">
                {{ strtoupper(app()->environment()) }} environment
            </p>
            <p class="text-xs text-muted-foreground">
                {{ app()->environment('production')
                    ? 'You are on PRODUCTION. Destructive actions are restricted.'
                    : 'Sandbox mode active. Outbound sends and payments use test credentials.' }}
            </p>
        </div>
    </div>

    {{-- Sandbox provider keys --}}
    <div class="card border border-border rounded-xl p-6 mb-4">
        <h2 class="text-sm font-semibold text-foreground mb-4">Sandbox provider keys</h2>
        <div class="space-y-4">
            @foreach([
                ['email_test_key',    'Email test API key',    'SG.test_…'],
                ['sms_test_key',      'SMS test API key',      'SK.test_…'],
                ['whatsapp_test_key', 'WhatsApp test token',   'EAAtest…'],
                ['psp_test_key',      'PSP test secret key',   'sk_test_…'],
            ] as [$name, $label, $placeholder])
                <div>
                    <label class="block text-xs text-muted-foreground mb-1 font-medium">{{ $label }}</label>
                    <div class="relative max-w-sm">
                        <input type="password" name="{{ $name }}" id="{{ $name }}" class="kt-input w-full pr-10"
                               value="{{ $envKeys[$name] ?? '' }}" placeholder="{{ $placeholder }}"
                               autocomplete="new-password" />
                        <button type="button" class="absolute right-2 top-1/2 -translate-y-1/2 text-muted-foreground toggle-secret" data-target="{{ $name }}">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="flex justify-end mt-4">
            <button class="kt-btn kt-btn-mono kt-btn-sm">Save sandbox keys</button>
        </div>
    </div>

    {{-- Safe toggles --}}
    <div class="card border border-border rounded-xl p-6 mb-4">
        <h2 class="text-sm font-semibold text-foreground mb-4">Safe toggles</h2>
        <div class="space-y-3">
            @foreach([
                ['disable_outbound', 'Disable outbound sends', 'All Email/SMS/WhatsApp sends are silently swallowed', true],
                ['fake_payments',    'Fake payments',           'Payment intents return success immediately',          true],
                ['sample_logistics', 'Use sample logistics quotes', 'Logistics quotes return mock data',               true],
                ['force_2fa_off',    'Disable two-factor auth (staging only)', 'Skips 2FA for easier testing',         false],
            ] as [$key, $label, $hint, $default])
                <label class="flex items-start gap-3 p-3 rounded-lg border border-border hover:bg-muted/20 transition-colors cursor-pointer">
                    <div class="mt-0.5">
                        <input type="checkbox" name="flags[{{ $key }}]" class="kt-checkbox"
                               {{ ($envFlags[$key] ?? $default) ? 'checked' : '' }} />
                    </div>
                    <div class="flex-1">
                        <div class="text-sm font-medium text-foreground">{{ $label }}</div>
                        <div class="text-xs text-muted-foreground mt-0.5">{{ $hint }}</div>
                    </div>
                </label>
            @endforeach
        </div>
        <div class="flex justify-end mt-4">
            <button class="kt-btn kt-btn-mono kt-btn-sm">Save toggles</button>
        </div>
    </div>

    {{-- Seed / Reset tools --}}
    <div class="card border border-border rounded-xl p-6 mb-4">
        <h2 class="text-sm font-semibold text-foreground mb-4">Demo data</h2>
        <div class="flex gap-3 flex-wrap">
            <button type="button" class="kt-btn kt-btn-outline" id="btn-seed"
                    @if(app()->environment('production')) disabled title="Not available in production" @endif>
                <i data-lucide="database" class="w-4 h-4 mr-1"></i> Seed demo data
            </button>
            <button type="button" class="kt-btn kt-btn-outline text-warning" id="btn-reset"
                    @if(app()->environment('production')) disabled title="Not available in production" @endif>
                <i data-lucide="refresh-cw" class="w-4 h-4 mr-1"></i> Reset demo data
            </button>
        </div>
        <p class="text-xs text-muted-foreground mt-2">Seeds listings, auctions, vendors, leads, and sample valuations with realistic data.</p>
    </div>

    {{-- Danger zone --}}
    @cannot('production-only')
    <div class="card border border-destructive/40 rounded-xl p-6">
        <div class="flex items-center gap-2 mb-4">
            <i data-lucide="skull" class="w-5 h-5 text-destructive"></i>
            <h2 class="text-sm font-semibold text-destructive">Danger zone</h2>
            <span class="kt-badge kt-badge-destructive kt-badge-xs">Super Admin only</span>
        </div>
        <div class="space-y-3">
            <div class="flex items-center justify-between p-3 rounded-lg border border-destructive/30 bg-destructive/5">
                <div>
                    <div class="text-sm font-medium text-foreground">Flush all queued jobs</div>
                    <div class="text-xs text-muted-foreground">Clears the automation queue — actions in progress will not complete</div>
                </div>
                <button class="kt-btn kt-btn-destructive kt-btn-sm" id="btn-flush-jobs">Flush jobs</button>
            </div>
            <div class="flex items-center justify-between p-3 rounded-lg border border-destructive/30 bg-destructive/5">
                <div>
                    <div class="text-sm font-medium text-foreground">Purge all staging data</div>
                    <div class="text-xs text-muted-foreground">Permanently deletes all records in this environment</div>
                </div>
                <button class="kt-btn kt-btn-destructive kt-btn-sm" id="btn-purge-all">Purge all</button>
            </div>
        </div>
    </div>
    @endcannot

</div>

@push('scripts')
<script>
document.querySelectorAll('.toggle-secret').forEach(btn => {
    btn.addEventListener('click', () => {
        const input = document.getElementById(btn.dataset.target);
        input.type = input.type === 'password' ? 'text' : 'password';
        btn.querySelector('i').setAttribute('data-lucide', input.type === 'password' ? 'eye' : 'eye-off');
        lucide.createIcons();
    });
});

document.getElementById('btn-seed')?.addEventListener('click', () => {
    if(!confirm('This will insert demo data into the database. Continue?')) return;
    const btn = document.getElementById('btn-seed');
    btn.textContent = 'Seeding…'; btn.disabled = true;
    setTimeout(() => { btn.innerHTML = '<i data-lucide="check" class="w-4 h-4 mr-1 inline"></i> Seeded!'; lucide.createIcons(); }, 2000);
});

document.getElementById('btn-reset')?.addEventListener('click', () => {
    if(!confirm('Reset all demo data? This will delete existing records and re-seed.')) return;
    const btn = document.getElementById('btn-reset');
    btn.textContent = 'Resetting…'; btn.disabled = true;
    setTimeout(() => { btn.innerHTML = '<i data-lucide="check" class="w-4 h-4 mr-1 inline"></i> Reset!'; lucide.createIcons(); }, 2500);
});

document.getElementById('btn-flush-jobs')?.addEventListener('click', () => {
    if(!confirm('Flush ALL queued jobs? This cannot be undone.')) return;
    alert('Queue flushed.');
});

document.getElementById('btn-purge-all')?.addEventListener('click', () => {
    const confirm1 = confirm('⚠️ PURGE ALL staging data? This is permanent.');
    if(!confirm1) return;
    const input = prompt('Type PURGE to confirm:');
    if(input === 'PURGE') alert('Purge initiated.');
});
</script>
@endpush

@endsection
